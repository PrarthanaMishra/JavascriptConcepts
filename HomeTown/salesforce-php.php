

Skip to content
Using Hometown Mail with screen readers
Enable desktop notifications for Hometown Mail.
   OK  No thanks

SFDC File

Mohd Intsar
Attachments
12:36 PM (1 hour ago)
to me

PFA
Attachments area


<?php

/**
 * <p>
 * Customer Controller
 * INDFUR-1419
 * </p>
 * @author Hrishikesh Mishra<hrishikesh.mishra@fabfurnish.com>
 * @package mobApi
 *
 */
class CustomerController extends MapiController
{

    /**
     * @var User
     */
    protected $_user;
    public $errorCode;
    /**
     * Customer
     *
     * @var Transfer_Customer
     */
    public $customer;

    /*
     * Ideabook
     * @var Ideabook per page list count
     */
    const IDEABOOK_PERPAGELISTCOUNT = 10;

    /**
     * <p>
     * Imports the components and models needed
     * Initializes the class var needed for user and generate JSON message
     * </p>
     * @see MobapiController::init()
     */
    public function init()
    {
        parent::init();
        Yii::import('customer.components.*');
        $this->_user = Yii::app()->user;
        if (!$this->_user->isGuest) {
            $this->customer = $this->_user->getCurrentUser();
        }
    }

    /**
     * <p>
     * Get Bob adapter
     * </p>
     * @param void
     * @return void
     */
    protected function _getBobAdapter()
    {
        return GenericModel::getAdapterByName('BobAdapter');
    }

    /**
     * <p>
     * Returns not logged in
     * service message
     * </p>
     * @param void
     * @return void
     * @access public
     */
    protected function _notLoggedIn()
    {
        $this->apiReturn->setSuccess(false);
        $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_NOT_LOGGED_IN));
        $return = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($return);
    }


    private function SFDCAuthentication()
    {
        $data = array('grant_type' => 'password',
            'client_id' => SFDC_CLIENT_ID,
            'client_secret' => SFDC_CLIENT_SECRET,
            'username' => SFDC_USERNAME,
            'password' => SFDC_PASSWORD);
        $authentication = $this->runQuery(SFDC_URL, http_build_query($data));
        return json_decode($authentication, true);
    }


    private function runQuery($url = null, $data = null, $accessToken = null)
    {
        if (!empty($url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!empty($accessToken)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:Bearer " . $accessToken, "Content-Type: application/json"));
            }

            $server_output = curl_exec($ch);
            if ($server_output === false) {
                echo 'Curl error: ' . curl_error($ch);
                die;
            }
            curl_close($ch);
            return $server_output;
        }
    }


    /**
     * <p>
     * Customer login
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public function actionLogin()
    {
        try {
            $bobAdapter = $this->_getBobAdapter();
            $local = GenericModel::getLocalStorageAdapter();
            $sfdcConfigArr = $local->get('sales-force_configuration', false);
            $customerID = !empty($this->getParam("custid")) ? $this->getParam("custid") : 0;
            $from = !empty($this->getParam("from")) ? $this->getParam("from") : "";
            $to = !empty($this->getParam("to")) ? $this->getParam("to") : "";
            $mobile = !empty($this->getParam('mobile')) ? $this->getParam('mobile') : '';
            $limit = !empty($this->getParam('limit')) ? $this->getParam('limit') : (int) $sfdcConfigArr['salesforce_my_order_limit'];
            $finalOrders = array();
            $regions = $local->get('regions', false);
            $regionData = array();
            if (!empty($regions)) {
                foreach ($regions as $region) {
                    if (!empty($region['customer_hub_mapping_id'])) {
                        $regionData[$region['customer_hub_mapping_id']] = array('name' => $region['name']);
                    }
                }
            }
            $orderProcessingConfigArr = $local->get('orderprocessing_configuration', false);
            $trackEnabledDate = $orderProcessingConfigArr['sap_track_order_enable_date'];

            if (!empty($customerID)) {
                $result = $bobAdapter->get('CustomerFailedOrders', false, false, array("customer_id" => $customerID, "from" => $from, "to" => $to, "limit" => $limit));
                $responseSummary = $result->getResultData();
                if (!empty($responseSummary)) {
                    foreach ($responseSummary as $response) {
                        $showTrackOrder = strtotime($trackEnabledDate) < strtotime($response['created_at']) ? true : false;

                        $shippingState = !empty($regionData[$response['s_region']]) ? $regionData[$response['s_region']]['name'] : '';
                        $billingState = !empty($regionData[$response['b_region']]) ? $regionData[$response['b_region']]['name'] : '';
                        $orderItems = array();
                        $orders = array("b_customer_first_name" => $response['b_first_name'],
                            "b_customer_last_name" => $response['b_last_name'],
                            "s_customer_first_name" => $response['s_first_name'],
                            "s_customer_last_name" => $response['s_last_name'],
                            "order_number" => $response['order_nr'],
                            "time" => strtotime($response['created_at']),
                            "order_date" => date("d, M, Y", strtotime($response['created_at'])),
                            "grand_total" => $response['grand_total'],
                            "s_address_1" => !empty($response['s_address2']) ? $response['s_address1'] . ", " . $response['s_address2'] : $response['s_address1'],
                            "s_address_2" => '',
                            "s_city" => $response['s_city'],
                            "s_pincode" => $response['s_postcode'],
                            "s_country" => 'India',
                            "s_region" => $shippingState,
                            "b_address_1" => $response['b_address1'],
                            "b_address_2" => $response['b_address2'],
                            "b_city" => $response['b_city'],
                            "b_pincode" => $response['b_postcode'],
                            "b_country" => "India",
                            "b_region" => $billingState,
                            "sfaccount_id" => "",
                            "sforder_id" => "",
                            "bob_order" => 0,
                            "status" => $response['order_item_status_display_name'],
                            "show_track_order" => $showTrackOrder
                        );
                        foreach ($responseSummary as $resp) {
                            if ($resp['order_nr'] == $response['order_nr']) {
                                $orderItems[] = array("delivery_date_text" => $resp["delivery_date_text"],
                                    "product_name" => $resp["product_name"],
                                    "image" => !empty($resp["image"]) ? ALICE_IMAGE_HOST . "/product/" . $resp["image"] : "",
                                    "order_item_id" => $resp["id_sales_order_item"],
                                    "article_code" => $resp['sku'],
                                    "quantity" => 1,
                                    'bill_of_landing' => '',
                                    'transport_id' => '',
                                    'status' => array(),
                                    'bob_order_item' => 1);

                            }
                        }
                        // print_r($orderItems); die;
                        // $orders['order_items'] = $this->filterOrderItem($orderItems);
                        $orders['order_items'] = $orderItems;
                        $finalOrders[$response['order_nr']] = $orders;
                    }
                    //  $finalOrders[$response['order_nr']] = $orders;
                    $finalOrders = $this->filterFinalOrder($finalOrders);
                }
            }

            $auth = $this->SFDCAuthentication();
            $accessToken = $auth['access_token'];
            $instance = $auth['instance_url'];
            $instanceURL = $instance . "/services/data/v42.0/";
            $where = "";
            ########## Get Images From REDIS #############
            $productImages = $local->get('productsupplierid', false);



            if (!empty($mobile)) {
                if (!empty($accessToken) && !empty($instanceURL)) {
                    if (!empty($from)) {
                        $where .= " AND DAY_ONLY(SAP_Created_Date__c) >= " . $from;
                    }
                    if (!empty($to)) {
                        $where .= " AND DAY_ONLY(SAP_Created_Date__c) <= " . $to;
                    }
                    //$query = $instanceURL . "query?q=" . urlencode("SELECT Total_Price__c,Taxes2__c,AccountId,Website_Order_Number__c,Sales_TRX_Number__c,Id,TotalAmount,SAP_Created_Date__c,Delivery_Status__c,ShippingStreet,ShippingCity,ShippingPostalCode,ShippingState,Delivery_Name__c,Billing_Name__c,BillingStreet,BillingCity,BillingPostalCode,BillingState FROM Order WHERE AccountId IN (SELECT Id FROM Account WHERE PersonMobilePhone='$mobile') " . $where . " ORDER BY SAP_Created_Date__c DESC LIMIT " . $limit);
                    $query = $instanceURL . "query?q=" . urlencode("SELECT Taxes2__c,AccountId,Website_Order_Number__c,Sales_TRX_Number__c,Id,TotalAmount,SAP_Created_Date__c,Delivery_Status__c,ShippingStreet,ShippingCity,ShippingPostalCode,ShippingState,Delivery_Name__c,Billing_Name__c,BillingStreet,BillingCity,BillingPostalCode,BillingState FROM Order WHERE AccountId IN (SELECT Id FROM Account WHERE PersonMobilePhone='$mobile') " . $where . " ORDER BY SAP_Created_Date__c DESC LIMIT " . $limit);
                    $resultData = $this->runQuery($query, '', $accessToken);
                    $resultData = json_decode($resultData, true);
                    if (!empty($resultData['records'])) {
                        foreach ($resultData['records'] as $single) {
                            $showTrackOrder = strtotime($trackEnabledDate) < strtotime($single['SAP_Created_Date__c']) ? true : false;

                            $shippingState = !empty($regionData[$single['ShippingState']]) ? $regionData[$single['ShippingState']]['name'] : '';
                            $billingState = !empty($regionData[$single['BillingState']]) ? $regionData[$single['BillingState']]['name'] : '';
                            $bname = explode(" ", $single['Billing_Name__c'], 2);
                            $sname = explode(" ", $single['Delivery_Name__c'], 2);
                            $orderNumber = !empty($single['Website_Order_Number__c']) ? current(explode("-", $single['Website_Order_Number__c'])) : $single['Sales_TRX_Number__c'];
                            $finalOrders[$orderNumber] = array("b_customer_first_name" => current($bname),
                                "b_customer_last_name" => !empty($bname[1]) ? $bname[1] : '',
                                "s_customer_first_name" => current($sname),
                                "s_customer_last_name" => !empty($sname[1]) ? $sname[1] : '',
                                "order_number" => $orderNumber,
                                "order_date" => date("d, M, Y", strtotime($single['SAP_Created_Date__c'])),
                                "time" => strtotime($single['SAP_Created_Date__c']),
                                "grand_total" => $single['Taxes2__c'],
                               // "grand_total" => $single['Total_Price__c'],
                                "s_address_1" => $single["ShippingStreet"],
                                "s_address_2" => "",
                                "s_city" => $single['ShippingCity'],
                                "s_pincode" => $single['ShippingPostalCode'],
                                "s_country" => "India",
                                "s_region" => $shippingState,
                                "b_address_1" => $single["BillingStreet"],
                                "b_address_2" => "",
                                "b_city" => $single['BillingCity'],
                                "b_pincode" => $single['BillingPostalCode'],
                                "b_country" => "India",
                                "b_region" => $billingState,
                                "sfaccount_id" => $single['AccountId'],
                                "sforder_id" => $single['Id'],
                                "bob_order" => 0,
                                "status" => "",
                                "show_track_order" => $showTrackOrder
                            );
                        }
                        $sforders = array_map(function ($value) {
                            return $value['sforder_id'];
                        }, $finalOrders);
                        $orderitems = implode("','", $sforders);
                        $query = $instanceURL . "query?q=" . urlencode("select OrderId, Id, First_Date__c,Product2Id, Product2.Name,Product2.SAP_Product_Code__c,Product2.Id,Quantity,ListPrice FROM OrderItem where OrderId IN ('$orderitems')");
                        $itemData = $this->runQuery($query, '', $accessToken);
                        $itemData = json_decode($itemData, true);
                        foreach ($finalOrders as $orderKey => $orderValue) {

                            if (!empty($itemData['records'])) {
                                $orderProcessingConfigArr = $local->get('orderprocessing_configuration', false);
                                $removableArticleArr = array();
                                if(!empty($orderProcessingConfigArr['sap_removable_article'])){
                                    $removableArticleArr = explode(", ", $orderProcessingConfigArr['sap_removable_article']);
                                }
                                foreach ($itemData['records'] as $singleItem) {
                                    if (!in_array($singleItem['Product2']['SAP_Product_Code__c'], $removableArticleArr)) {
                                        if ($orderValue['sforder_id'] == $singleItem['OrderId']) {
                                            $image = '';
                                            $mainImage = $local->get('productsupplierid_' . $singleItem['Product2']['SAP_Product_Code__c'], false);
                                            if (!empty($mainImage['main_image'])) {
                                                $image = $mainImage['main_image'] . '-catalog_360.jpg';
                                            }
                                            $finalOrders[$orderKey]['order_items'][] = array("delivery_date_text" => !empty($singleItem['First_Date__c']) ? 'Delivered by ' . date("d M Y", strtotime($singleItem['First_Date__c'])) : '',
                                                "product_name" => !empty($singleItem['Product2']) ? $singleItem['Product2']['Name'] : '',
                                                "image" => $image,
                                                "quantity" => $singleItem['Quantity'],
                                                "order_item_id" => $singleItem['Id'],
                                                "article_code" => $singleItem['Product2Id'],
                                                'bill_of_landing' => '',
                                                'transport_id' => '',
                                                'status' => '',
                                                'bob_order_item' => 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $returnOrders = array();
            if (!empty($finalOrders)) {
                $finalOrders = $this->arrangeByOrderDate($finalOrders);
                $length = count($finalOrders);
                if ($length > $limit) {
                    foreach ($finalOrders as $key => $value) {
                        if ($key < $limit) {
                            $returnOrders[] = $value;
                        } else {
                            break;
                        }
                    }
                } else {
                    $returnOrders = $finalOrders;
                }
            }
            $this->jsonPrinter->printJSON($returnOrders);
        } catch (Exception $e) {
            $this->jsonPrinter->printJSON(array());
        }
    }


    // Sort the Order By Order Date
    public function arrangeByOrderDate($orders)
    {
        $finalOrders = array();
        foreach ($orders as $singleOrder) {
            $order[$singleOrder['order_number']] = $singleOrder['time'];
        }
        arsort($order);  // Associative Reverse Sort By Value
        foreach ($order as $key => $value) {
            $finalOrders[] = $orders[$key];
        }
        return $finalOrders;
    }


    public function actionInsertcase()
    {
        try {
            $auth = $this->SFDCAuthentication();
            $accessToken = $auth['access_token'];
            $instance = $auth['instance_url'];
            $instanceURL = $instance . "/services/data/v42.0/";
            $status = "Open";
            if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
                $post = file_get_contents("php://input");
                $data = CJSON::decode($post, true);
                $origin = 'Web';
                $subject = !empty($data['subject']) ? $data['subject'] : '';
                $desc = !empty($data['desc']) ? $data['desc'] : '';
                $type = !empty($data['type']) ? $data['type'] : '';
                $category_c = !empty($data['category']) ? trim($data['category']) : '';
                $sub_category__c = !empty($data['sub_category']) ? trim($data['sub_category']) : '';
                $accountId = !empty($data['account_id']) ? $data['account_id'] : '';
                $orderId = !empty($data['order_id']) ? $data['order_id'] : '';
                $code = !empty($data("code")) ? $data("code") : '';
                $itemID = !empty($data("item_id")) ? $data("item_id") : '';
            } else {
                $origin = 'Web';
                $subject = !empty($this->getParam("subject")) ? $this->getParam("subject") : '';
                $desc = !empty($this->getParam("desc")) ? $this->getParam("desc") : '';
                $type = !empty($this->getParam("type")) ? $this->getParam("type") : '';
                $category_c = !empty($this->getParam("category")) ? trim($this->getParam("category")) : '';
                $sub_category__c = !empty($this->getParam("sub_category")) ? trim($this->getParam("sub_category")) : '';
                $accountId = !empty($this->getParam("account_id")) ? $this->getParam("account_id") : '';
                $orderId = !empty($this->getParam("order_id")) ? $this->getParam("order_id") : '';
                $code = !empty($this->getParam("code")) ? $this->getParam("code") : '';
                $itemID = !empty($this->getParam("item_id")) ? $this->getParam("item_id") : '';

            }

            $errors = array();
            if (!empty($accessToken) && !empty($auth['instance_url'])) {
                if (empty($origin)) {
                    $errors[] = "Origin can't be empty";
                }
                if (empty($subject)) {
                    $errors[] = "Subject can't be empty";
                }
                if (empty($accountId)) {
                    $errors[] = "Account can't be empty";
                }
                if (empty($orderId)) {
                    $errors[] = "Order Id can't be empty";
                }
                if(empty($itemID)) {
                    $errors[] = "Item Id can't be empty";
                }

                if (empty($errors)) {
                    $insertData = array("Subject" => $subject,
                        "Description" => $desc,
                        "Status" => $status,
                        "Origin" => $origin,
                        "Type" => $type,
                        "Category__c" => $category_c,
                        "Sub_Category__c" => $sub_category__c,
                        "AccountId" =>$accountId,
                        "Sales_TRX__c"=>$orderId);

                    $result = $this->runQuery($instanceURL . "sobjects/Case/", json_encode($insertData), $accessToken);
                    $decodedResult = json_decode($result, true);
                    if (!empty($decodedResult["success"])) {
                        $resultCaseLine = $this->runQuery($instanceURL . "sobjects/Case_Line__c/", json_encode(array('Case__c' => $decodedResult["id"], 'Product__c' => $code, 'Line_Number__c' => $itemID)), $accessToken);
                        $decodedResultCaseLine = json_decode($resultCaseLine, true);
                        if (!empty($decodedResultCaseLine['success'])) {
                            $this->jsonPrinter->printJSON(array("id" => $decodedResult["id"], "success" => true, "errors" => array()));
                        } else {
                            $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => $decodedResult[0]['message']));
                        }
                    } else {
                        $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => $decodedResult[0]['message']));
                    }
                } else {
                    $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => $errors));
                }
            } else {
                $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => array("SFDC Authentication is failed")));
            }
        } catch (Exception $e) {
            $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => array($e->getMessage())));
        }
    }

    public function actionGetcases()
    {
        try {
            $local = GenericModel::getLocalStorageAdapter();
            $sfdcConfigArr = $local->get('sales-force_configuration', false);

            $from = !empty($this->getParam("from")) ? $this->getParam("from") : "";
            $to = !empty($this->getParam("to")) ? $this->getParam("to") : "";
            $limit = !empty($this->getParam('limit')) ? $this->getParam('limit') :  (int) $sfdcConfigArr['salesforce_my_case_limit'];
            $status = !empty($this->getParam('status')) ? $this->getParam('status') : '';
            $customerSFAccountID = !empty($this->getParam('customer_sfid')) ? $this->getParam('customer_sfid') : '';
            $mobile = !empty($this->getParam('mobile')) ? $this->getParam('mobile') : '';
            $auth = $this->SFDCAuthentication();
            $accessToken = $auth['access_token'];
            $instance = $auth['instance_url'];
            $instanceURL = $instance . "/services/data/v42.0";

            if(strtolower($customerSFAccountID) == 'null'){
                $customerSFAccountID = '';
            }

            if (empty($customerSFAccountID)) {
                $query = $instanceURL . "/query?q=" . urlencode("SELECT Id FROM Account WHERE PersonMobilePhone='{$mobile}'");
                $resultData = $this->runQuery($query, '', $accessToken);
                $resultData = json_decode($resultData, true);
                $customerSFAccountID = !empty($resultData['records']) ? $resultData['records'][0]['Id'] : '';
            }

            $cases = array();
            if (!empty($customerSFAccountID)) {

                $where = "";
                if (!empty($accessToken) && !empty($instanceURL)) {
                    if (!empty($from)) {
                        $where .= " AND CreatedDate >= " . $from . "T00:00:00.000Z";
                    }
                    if (!empty($to)) {
                        $where .= " AND CreatedDate <= " . $to . "T00:00:00.000Z";
                    }
                    if (!empty($status)) {
                        $where .= " AND Status = '" . $status . "'";
                    }

                    $where .= " AND Type IN ('Complaint','Request')";
                    $query = "SELECT CaseNumber,Subject,Description,Status,Origin,Type,Category__c,Sub_Category__c,CreatedDate FROM case WHERE AccountId='$customerSFAccountID' " . $where . " LIMIT " . $limit;
                    $query = $instanceURL . "/query?q=" . urlencode($query);
                    $data = $this->runQuery($query, '', $accessToken);
                    $data = json_decode($data, true);
                    if (!empty($data['records'])) {
                        foreach ($data['records'] as $single) {
                            $cases[] = array("subject" => strtolower($single['Origin']) == 'web' ? $single['Subject'] : $single['Category__c'],
                                "description" => $single['Description'],
                                "status" => $single['Status'],
                                "type" => $single['Type'],
                                "origin" => $single['Origin'],
                                "category" => $single['Category__c'],
                                "subcategory" => $single['Sub_Category__c'],
                                "caseNumber" => $single["CaseNumber"],
                                "CreatedDate" => date("d-m-Y", strtotime($single["CreatedDate"]))
                            );
                        }
                    }
                }
            }
            $this->jsonPrinter->printJSON($cases);
        } catch (Exception $e) {
            $this->jsonPrinter->printJSON(array());
        }
    }


    public function actionTrack()
    {
        try {
            $order = array();
            $orderID = !empty($this->getParam("order")) ? $this->getParam("order") : '';
            if (!empty($orderID)) {
                $auth = $this->SFDCAuthentication();
                $accessToken = $auth['access_token'];
                $instance = $auth['instance_url'];
                $instanceURL = $instance . "/services/data/v42.0";
                $sapOrderID = str_pad($orderID, 10, 0, STR_PAD_LEFT);
                $bobOrderID = current(explode("-", $orderID)) . '-6069';  // online number
                $paperFyOrder = 'PF-'.$orderID;
                $amazonOrder = 'AM'.$orderID;

                //$query = $instanceURL . "/query?q=" . urlencode("SELECT Taxes2__c,Sales_TRX_Number__c,Id,TotalAmount,Name,SAP_Created_Date__c,Delivery_Status__c,ShippingStreet,ShippingCity,ShippingPostalCode,ShippingState,Delivery_Name__c,Billing_Name__c,BillingStreet,BillingCity,BillingPostalCode,BillingState FROM Order WHERE Sales_TRX_Number__c = '" . $sapOrderID . "' OR Website_Order_Number__c = '" . $bobOrderID . "' OR Website_Order_Number__c = '" . $orderID . "'");
                $query = $instanceURL . "/query?q=" . urlencode("SELECT Taxes2__c,Sales_TRX_Number__c,Id,TotalAmount,SAP_Created_Date__c,Delivery_Status__c,ShippingStreet,ShippingCity,ShippingPostalCode,ShippingState,Delivery_Name__c,Billing_Name__c,BillingStreet,BillingCity,BillingPostalCode,BillingState FROM Order WHERE Sales_TRX_Number__c = '" . $sapOrderID . "' OR Website_Order_Number__c = '" . $bobOrderID . "' OR Website_Order_Number__c = '" . $orderID . "'  OR Website_Order_Number__c = '" . $paperFyOrder . "'  OR Website_Order_Number__c = '" . $amazonOrder . "'");

                $data = $this->runQuery($query, '', $accessToken);

                $data = json_decode($data, true);
                if ($data['totalSize'] == 1) {   // Exists in SFDC
                    $local = GenericModel::getLocalStorageAdapter();

                    $orderTrackMessage = "";
                    $showTrackOrder = false;
                    $orderProcessingConfigArr = $local->get('orderprocessing_configuration', false);
                    if (strtotime($orderProcessingConfigArr['sap_track_order_enable_date']) > strtotime($data['records'][0]['SAP_Created_Date__c'])) {
                        $orderTrackMessage = !empty($orderProcessingConfigArr['sap_track_order_enable_date']) ? str_replace("[[DATE]]", $orderProcessingConfigArr['sap_track_order_enable_date'],$orderProcessingConfigArr['sap_track_order_enable_date_message']) : "Can not track order";
                    }
                    // Handle Error Message
                    if (!empty($orderTrackMessage)) {
                        $this->jsonPrinter->printJSON(array('error' => $orderTrackMessage));
                        return;
                    } else {
                    $regions = $local->get('regions', false);

                    $regionData = array();
                    if (!empty($regions)) {
                        foreach ($regions as $region) {
                            if (!empty($region['customer_hub_mapping_id'])) {
                                $regionData[$region['customer_hub_mapping_id']] = array('name' => $region['name']);
                            }
                        }
                    }
                    $orderItems = array();
                    $single = $data['records'][0];
                    $shippingState = !empty($regionData[$single['ShippingState']]) ? $regionData[$single['ShippingState']]['name'] : '';
                    $billingState = !empty($regionData[$single['BillingState']]) ? $regionData[$single['BillingState']]['name'] : '';

                    $bname = explode(" ", $single['Billing_Name__c'], 2);
                    $sname = explode(" ", $single['Delivery_Name__c'], 2);
                    $orderNumber = !empty($single['Website_Order_Number__c']) ? $single['Website_Order_Number__c'] : $single['Sales_TRX_Number__c'];
                    $sapOrderNumber = $single['Sales_TRX_Number__c'];


                    $order = array("error"=>"",
                        "b_customer_first_name" => current($bname),
                        "b_customer_last_name" => end($bname),
                        "s_customer_first_name" => end($sname),
                        "s_customer_last_name" => end($sname),
                        "order_number" => $orderNumber,
                        "order_date" => date("d, M, Y", strtotime($single['SAP_Created_Date__c'])),
                        "grand_total" => $single['Taxes2__c'],
                        "s_address_1" => $single["ShippingStreet"],
                        "s_address_2" => "",
                        "s_city" => $single['ShippingCity'],
                        "s_pincode" => $single['ShippingPostalCode'],
                        "s_country" => "India",
                        "s_region" => $shippingState,
                        "b_address_1" => $single["BillingStreet"],
                        "b_address_2" => "",
                        "b_city" => $single['BillingCity'],
                        "b_pincode" => $single['BillingPostalCode'],
                        "b_country" => "India",
                        "b_region" => $billingState,
                        //   "sfaccount_id" => $single['AccountId'],
                        "sforder_id" => $single['Id']
                    );
                    $orderItemStatusMapping = json_decode(SFDC_ORDER_STATUS, true);
                    $query = $instanceURL . "/query?q=" . urlencode("select Id,Type,Status FROM Case where Sales_TRX__c = '" . $single['Id'] . "' AND Type IN ('Fitment','Delivery')");
                    $itemData = $this->runQuery($query, '', $accessToken);
                    $itemData = json_decode($itemData, true);
                    $itemCode = array();
                    if (!empty($itemData['records'])) {
                        foreach ($itemData['records'] as $singleItem) {
                            $caseLinequery = $query = $instanceURL . "/query?q=" . urlencode("select product__r.Name,product__r.SAP_Product_Code__c,Quantity__c FROM Case_Line__c where Case__c = '" . $singleItem['Id'] . "'");
                            $caseLineItem = $this->runQuery($caseLinequery, '', $accessToken);
                            $caseLineItemData = json_decode($caseLineItem, true);
                            foreach ($caseLineItemData['records'] as $singleCaseLineItem) {

                                $itemCode[$singleCaseLineItem['Product__r']['SAP_Product_Code__c']][$singleItem['Type']] = array('quantity' => $singleCaseLineItem['Quantity__c'],
                                    'status' => $singleItem['Type'] . '-' . strtolower($singleItem['Status']));
                            }
                        }
                    }


                    $query = $query = $instanceURL . "/query?q=" . urlencode("select Line_Item_Number__c, Id, First_Date__c,Product2.Name,Product2.SAP_Product_Code__c,Quantity, Goods_received_at_regional_warehouse__c, Goods_shipped_to_regional_warehouse__c, Stock_Committed__c FROM OrderItem where OrderId = '" . $single['Id'] . "'");
                    $itemData = $this->runQuery($query, '', $accessToken);
                    $totalItemData = $itemData = json_decode($itemData, true);
                    $productImages = $local->get('productsupplierid', false);
                    $products = array();
                    foreach ($itemData['records'] as $product) {
                        $products[] = $product['Product2']['SAP_Product_Code__c'];
                    }
                    $bobAdapter = $this->_getBobAdapter();
                    $result = $bobAdapter->get('FilteredNonFurnitureProducts', false, false, array("products" => $products));
                    $nonFurnitureProducts = $result->getResultData();

                    // SAP AWB Number Mapping Arr
                    if (APPLICATION_ENV != 'live') {
                        $params = 'username=' . SAP_USERNAME . "&password=" . SAP_PASSWORD . "&grant_type=" . SAP_GRANT_TYPE . "&client_id=" . SAP_CLIENT_ID;
                        $accessTokenResult = json_decode($this->_sendRequest(SAP_ACCESS_TOKEN, array('application/x-www-form-urlencoded'), $params), true);
                    }

                    $statusArr = array();
                    if (APPLICATION_ENV == 'live' || !empty($accessTokenResult['access_token'])) {
                        $orderArr = array("ORDER_NO" => array(array("VBELN" => $sapOrderNumber)));

                        $sapOrderURL = (APPLICATION_ENV != 'live') ? SAP_ORDER_URL . "?apikey=" . SAP_API_KEY . "&access_token=" . $accessTokenResult['access_token'] : SAP_ORDER_URL;

                        $responseResult = json_decode($this->_sendRequest($sapOrderURL, array('Content-type: application/json'), json_encode($orderArr)), true);
                        if (!empty($responseResult['ITEM_DETAILS'])) {
                            foreach ($responseResult['ITEM_DETAILS'] as $single) {
                                $delivery = array();
                                foreach ($responseResult['HEADER_DETAILS'] as $singleDelievery) {
                                    if ((int)$singleDelievery['VBELN'] == (int)$single['VBELN']) {
                                        $delivery = array("bill_of_landing" => $singleDelievery['BOLNR'],   // AWB Number
                                            'transport_id' => $singleDelievery['TRAID']);    // Courier Partner / Fedex
                                        break;
                                    }
                                }
                                $delivery['quantity'] = $single['LFIMG'];
                                $statusArr[$single['POSNR']][] = $delivery;
                            }
                        }
                    }

                    $orderProcessingConfigArr = $local->get('orderprocessing_configuration', false);
                    $removableArticleArr = array();
                    if (!empty($orderProcessingConfigArr['sap_removable_article'])) {
                        $removableArticleArr = explode(", ", $orderProcessingConfigArr['sap_removable_article']);
                    }
                    foreach ($itemData['records'] as $singleItem) {
                        if (!in_array($singleItem['Product2']['SAP_Product_Code__c'], $removableArticleArr)) {
                            $finalStatus = array();

                            $image = '';
                            $mainImage = $local->get('productsupplierid_' . $singleItem['Product2']['SAP_Product_Code__c'], false);
                            if (!empty($mainImage['main_image'])) {
                                $image = $mainImage['main_image'] . '-catalog_360.jpg';
                            }


                            // Default Array
                            $ItemArr = array("delivery_date_text" => !empty($singleItem['First_Date__c']) ? $singleItem['First_Date__c'] : '',
                                "product_name" => !empty($singleItem['Product2']) ? $singleItem['Product2']['Name'] : '',
                                "image" => $image,
                                "order_item_id" => $singleItem['Id'],
                                'bill_of_landing' => '',
                                'transport_id' => '',
                                'type_code' => in_array($singleItem['Product2']['SAP_Product_Code__c'], $nonFurnitureProducts) ? 1 : 2,
                                'status' => array(),
                                'quantity' => 0
                            );

                            // Check For Non Furniture Product
                            $customArr = array();
                            if (in_array($singleItem['Product2']['SAP_Product_Code__c'], $nonFurnitureProducts)) {
                                if (!empty($statusArr)) {
                                    $singleItemArr = $statusArr[$singleItem['Line_Item_Number__c']];
                                }
                                if (!empty($singleItemArr)) {
                                    foreach ($singleItemArr as $singleItemAWB) {
                                        $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1));
                                        $customArr[] = array('status' => 'Shipped', 'display' => 1);

                                        $ItemArr['status'] = array_merge($orderItemStatus, $customArr);
                                        $ItemArr['quantity'] = (int)$singleItemAWB['quantity'];
                                        $ItemArr['bill_of_landing'] = $singleItemAWB['bill_of_landing'];
                                        $ItemArr['transport_id'] = $singleItemAWB['transport_id'];
                                        $ItemArr['type_code'] = 1;
                                        $orderItems[] = $ItemArr;
                                    }
                                } else {
                                    $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1));
                                    $customArr[] = array('status' => 'Shipped', 'display' => 0);
                                    $ItemArr['status'] = array_merge($orderItemStatus, $customArr);
                                    $ItemArr['quantity'] = $singleItem['Quantity'];
                                    $ItemArr['bill_of_landing'] = '';
                                    $ItemArr['transport_id'] = '';
                                    $ItemArr['type_code'] = 1;
                                    $orderItems[] = $ItemArr;
                                }
                                continue;   // Loop back if current item is Homeware
                            }

                            $customArr = array();
                            $stockCommitted = (int)$singleItem['Stock_Committed__c'];
                            $goodsShipped2RegionalWarehouse = (int)$singleItem['Goods_shipped_to_regional_warehouse__c'];
                            $goodsReceived2RegionalWarehouse = (int)$singleItem['Goods_received_at_regional_warehouse__c'];

                            $singleItemQ = $singleItem['Quantity'];
                            if (empty($stockCommitted)) {
                                if (empty($goodsShipped2RegionalWarehouse)) {

                                    $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0));
                                    $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                    $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                    $ItemArr['status'] = $orderItemStatus;
                                    //$ItemArr['status'] = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0, 'Hometown_Delivery' => 0));
                                    $ItemArr['quantity'] = $singleItemQ;
                                    $orderItems[] = $ItemArr;
                                } else {
                                    $remainingQ = $singleItemQ - $goodsShipped2RegionalWarehouse;
                                    $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1));
                                    $customArr[] = array('status' => 'Shipped', 'display' => 1);
                                    $customArr[] = array('status' => 'Reached Nearest Hub', 'display' => 0);
                                    $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                    $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                    $ItemArr['status'] = $orderItemStatus;
                                    $ItemArr['quantity'] = $goodsShipped2RegionalWarehouse;
                                    $orderItems[] = $ItemArr;
                                    if (!empty($remainingQ)) {  // Still Some Quantity Left
                                        $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0));
                                        $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                        $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                        $ItemArr['status'] = $orderItemStatus;
                                        //$ItemArr['status'] = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0, 'Hometown_Delivery' => 0));
                                        $ItemArr['quantity'] = $remainingQ;
                                        $orderItems[] = $ItemArr;
                                    }
                                }
                            } else {
                                if ($singleItemQ != $stockCommitted) {               // Commited Stock is not equal to Ordered Stock
                                    $remainingQ = $singleItemQ - $stockCommitted;    // x-y = z
                                    if (!empty($remainingQ)) {
                                        $remainingShippQ = $goodsShipped2RegionalWarehouse - $goodsReceived2RegionalWarehouse;  // A-B = k
                                        if (empty($remainingShippQ)) {
                                            $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0));
                                            $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                            $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                            $ItemArr['status'] = $orderItemStatus;
                                            // $ItemArr['status'] = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0, 'Hometown_Delivery' => 0));
                                            $ItemArr['quantity'] = $remainingQ;
                                            $orderItems[] = $ItemArr;
                                        } else {
                                            $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1));
                                            $customArr[] = array('status' => 'Shipped', 'display' => 1);
                                            $customArr[] = array('status' => 'Reached Nearest Hub', 'display' => 0);
                                            $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                            $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                            $ItemArr['status'] = $orderItemStatus;
                                            $ItemArr['quantity'] = $remainingShippQ;
                                            $orderItems[] = $ItemArr;

                                            $lastRemainingQ = $remainingQ - $remainingShippQ;    // Z - K
                                            if (!empty($lastRemainingQ)) {
                                                $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0));
                                                $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                                $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                                $ItemArr['status'] = $orderItemStatus;
                                                // $ItemArr['status'] = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0, 'Hometown_Delivery' => 0));
                                                $ItemArr['quantity'] = $lastRemainingQ;
                                                $orderItems[] = $ItemArr;
                                            }
                                        }
                                    }
                                }

                                $customArr = array();
                                $itemStatusArray = array();
                                if (!empty($itemCode[$singleItem['Product2']['SAP_Product_Code__c']])) {
                                    $itemStatusArray = $itemCode[$singleItem['Product2']['SAP_Product_Code__c']];
                                }
                                if (empty($itemStatusArray)) {  // not in fitment and delivery
                                    $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 1));
                                    $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                    $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                    $ItemArr['status'] = $orderItemStatus;
                                    //$ItemArr['status'] = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 1, 'Hometown_Delivery' => 0));
                                    $ItemArr['quantity'] = $stockCommitted;
                                    $orderItems[] = $ItemArr;
                                } else {
                                    krsort($itemStatusArray); // sort as Fitment, Delivery
                                    $quantitySum = 0;
                                    foreach ($itemStatusArray as $itemValue) {
                                        $finalStatus = array();
                                        foreach ($orderItemStatusMapping['Hometown_Init'] as $hometownValue) {
                                            $finalStatus[] = array('status' => $hometownValue, 'display' => 1);
                                        }
                                        foreach ($orderItemStatusMapping['Hometown_FSC'] as $hometownValue) {
                                            $finalStatus[] = array('status' => $hometownValue, 'display' => 1);
                                        }

                                        //  $finalStatus = array();
                                        $explodedType = explode("-", $itemValue['status']);   // open/loaded/deliver
                                        $display = 1;
                                        if (current($explodedType) == 'Delivery') {
                                            if (!empty($orderItemStatusMapping['Hometown_Delivery_Init'][end($explodedType)])) { // Delivery Init Status
                                                $finalStatus[] = array('status' => $orderItemStatusMapping['Hometown_Delivery_Init'][end($explodedType)], 'display' => 1);
                                                foreach ($orderItemStatusMapping['Hometown_Delivery'] as $hometownKey => $hometownValue) {
                                                    $finalStatus[] = array('status' => $hometownValue, 'display' => 0);
                                                }
                                            } else if (!empty($orderItemStatusMapping['Hometown_Delivery'][end($explodedType)])) { // Delivery Main Status
                                                $initDeliveryStatus = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                                $finalStatus[] = array('status' => $initDeliveryStatus['status'], 'display' => 1);  // Add Init Status
                                                $display = 1;
                                                foreach ($orderItemStatusMapping['Hometown_Delivery'] as $hometownKey => $hometownValue) {  // main
                                                    $finalStatus[] = array('status' => $hometownValue, 'display' => $display);
                                                    if ($hometownKey == end($explodedType)) {  // NExt Status Should not be Display
                                                        $display = 0;
                                                    }
                                                }
                                            } else {   // Add Init +  Main
                                                $initDeliveryStatus = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                                $finalStatus[] = array('status' => $initDeliveryStatus['status'], 'display' => 1);     // init
                                                $finalStatus[] = array('status' => current($orderItemStatusMapping['Hometown_Delivery']), 'display' => $display);  // main

                                                foreach ($orderItemStatusMapping['Hometown_Delivery_Sub'] as $key => $singleStatus) {  // sub
                                                    if ($key == end($explodedType)) {
                                                        $finalStatus[] = array('status' => $singleStatus, 'display' => $display);
                                                        break;
                                                    }
                                                }
                                            }
                                        } else {   // Fitment
                                            // Delivery Will Be Default Added in case of fitment
                                            $initDeliveryStatus = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 1))); // only first record
                                            $finalStatus[] = array('status' => $initDeliveryStatus['status'], 'display' => 1);

                                            foreach ($orderItemStatusMapping['Hometown_Delivery'] as $singleStatus) {
                                                $finalStatus[] = array('status' => $singleStatus, 'display' => 1);
                                            }
                                            // Fitment JSON Handle
                                            if (!empty($orderItemStatusMapping['Hometown_Fitment'][end($explodedType)])) { // Fitment Main Status
                                                $finalStatus[] = array('status' => $orderItemStatusMapping['Hometown_Fitment'][end($explodedType)], 'display' => 1);
                                            } else {   // Add Default Fitment PENDING
                                                $finalStatus[] = array('status' => current($orderItemStatusMapping['Hometown_Fitment']), 'display' => 1);
                                                foreach ($orderItemStatusMapping['Hometown_Fitment_Sub'] as $key => $singleStatus) {
                                                    if ($key == end($explodedType)) {
                                                        $finalStatus[] = array('status' => $singleStatus, 'display' => $display);
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        $ItemArr['status'] = $finalStatus;
                                        $ItemArr['quantity'] = $itemValue['quantity'];
                                        $orderItems[] = $ItemArr;
                                        $quantitySum += $itemValue['quantity'];
                                        if ($quantitySum == $stockCommitted) {  // Committed Quantity Equal To (Fitment+Delivery) Quantity
                                            break;
                                        }
                                    }

                                    if ($quantitySum != $stockCommitted && $stockCommitted > $quantitySum) {

                                        $orderItemStatus = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 1));
                                        $customArr[] = current($this->getConfigOrderStatuses(array('Hometown_Delivery_Init' => 0))); // only first record
                                        $orderItemStatus = array_merge($orderItemStatus, $customArr, $this->getConfigOrderStatuses(array('Hometown_Delivery' => 0)));
                                        $ItemArr['status'] = $orderItemStatus;
                                        //$ItemArr['status'] = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 1, 'Hometown_Delivery' => 0));
                                        $ItemArr['quantity'] = $stockCommitted - $quantitySum;
                                        $orderItems[] = $ItemArr;
                                    }
                                }
                                //}
                            }
                        }
                    }
                    $order['order_items'] = $orderItems;
                }
                } else {  // BOB Order
                    $bobAdapter = $this->_getBobAdapter();
                    $result = $bobAdapter->get('OrderDetails', false, false, array("order_id" => $orderID));
                    $summary = $result->getResultData();
                    if (!empty($summary)) {
                        $local = GenericModel::getLocalStorageAdapter();
                        $orderTrackMessage = "";
                        $orderProcessingConfigArr = $local->get('orderprocessing_configuration', false);
                        if (strtotime($orderProcessingConfigArr['sap_track_order_enable_date']) > strtotime($summary[0]['created_at'])) {
                            $orderTrackMessage = !empty($orderProcessingConfigArr['sap_track_order_enable_date']) ? str_replace("[[DATE]]", $orderProcessingConfigArr['sap_track_order_enable_date'],$orderProcessingConfigArr['sap_track_order_enable_date_message']) : "Can not track order";
                        }
                        $order['error'] = $orderTrackMessage;
                        if(empty($orderTrackMessage)) {
                            $order['status'] = $summary[0]['order_item_status_display_name'];  // cancelled, exported, exportable
                            if ($summary[0]['fk_sales_order_item_status'] == 9) { // cancelled
                                $orderItems = array();
                            } else {  // exportable , exported
                                foreach ($summary as $singleItem) {
                                    $status = $this->getConfigOrderStatuses(array('Hometown_Init' => 1, 'Hometown_FSC' => 0, 'Hometown_Delivery' => 0));
                                    $orderItems[] = array("delivery_date_text" => !empty($singleItem['delivery_date_text']) ? $singleItem['delivery_date_text'] : '',
                                        "product_name" => !empty($singleItem['name']) ? $singleItem['name'] : '',
                                        "image" => !empty($singleItem["image"]) ? ALICE_IMAGE_HOST . "/product/" . $singleItem["image"] : "",
                                        "order_item_id" => $singleItem['id_sales_order_item'],
                                        'bill_of_landing' => '',
                                        'transport_id' => '',
                                        'type_code' => '',
                                        'status' => $status,
                                        'quantity' => $singleItem['quantity']
                                    );
                                }
                            }
                            $order['order_items'] = $orderItems;
                        }

                    }
                }
            }
            $this->jsonPrinter->printJSON($order);
        } catch (Exception $e) {
            $this->jsonPrinter->printJSON(array());
        }
    }

    public
    function actionContactus()
    {
        if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
            $post = file_get_contents("php://input");
            $data = CJSON::decode($post, true);
            $origin = 'Web';
            $subject = !empty($data['subject']) ? $data['subject'] : '';
            $desc = !empty($data['desc']) ? $data['desc'] : '';
            $type = !empty($data['type']) ? $data['type'] : '';
            $category_c = !empty($data['category']) ? $data['category'] : '';
            $sub_category__c = !empty($data['sub_category']) ? $data['sub_category'] : '';
            $mobile = !empty($data["mobile"]) ? $data["mobile"] : '';
            $email = !empty($data["email"]) ? $data["email"] : '';
            $insertedArray = array('origin' => $origin,
                'subject' => $subject,
                'desc' => $desc,
                'type' => $type,
                'category' => $category_c,
                'sub_category' => $sub_category__c,
                'mobile' => $mobile,
                'email' => $email);

            if (empty($subject)) {
                $errors[] = "Subject can't be empty";
            }
            if (empty($mobile)) {
                $errors[] = "Mobile can't be empty";
            }
            if (empty($email)) {
                $errors[] = "Email can't be empty";
            }

            if (empty($errors)) {
                $bobAdapter = $this->_getBobAdapter();
                $result = $bobAdapter->set('ContactUs', false, $insertedArray);
                $responseSummary = $result->getResultData();
                if (!empty($responseSummary))
                    $this->jsonPrinter->printJSON(array("id" => "", "success" => true, "errors" => array()));
            } else {
                $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => $errors));
            }

        } else {
            $this->jsonPrinter->printJSON(array("id" => "", "success" => false, "errors" => array("Invalid JSON Format")));
        }
    }


    public
    function actionExternalLogin()
    {
        $response = array("success" => FALSE);
        if (Session::isRegistered("OSCCustomerData")) {
            Session::remove("OSCCustomerData");
        }
        if (!Yii::app()->user->isGuest) {
            $response["success"] = TRUE;
            echo json_encode($response);
            exit;
        }
        $data ['email'] = $this->getParam('email');
        $data ['first_name'] = $this->getParam('first_name');
        $data ['last_name'] = $this->getParam('last_name');
        $data['reg_type'] = $this->getParam('reg_type');
        if (array_key_exists('email', $data)) {
            $login = Yii::app()->user->externalUserRegistratonLogin($data);
            if ($login) {
                $response["success"] = TRUE;
            }
        }
        echo json_encode($response);
    }

    /**
     * <p>
     * Get Current User Details
     * - Personal Account Details
     * - Addresses Details
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public
    function actionGetDetails()
    {
        if ($this->_user->isGuest) {
            return $this->_notLoggedIn();
        } else {
            $this->apiReturn->setSuccess(true);
            $currentUser = $this->_user->getCurrentUser()->toArray();
            $address = ($this->_user->getOtherAddresses()) ? $this->_user->getOtherAddresses()->toArray() : null;
            $otherAddress = null;
            if (!empty($address)) {
                $count = 0;
                $otherAddress = array();
                foreach ($address as $key => $val) {
                    $otherAddress[$count] = $val;
                    $count++;
                }
            }
            $currentUser['shipping'] = ($this->_user->getShippingAddress()) ? $this->_user->getShippingAddress()->toArray() : null;
            $currentUser['billing'] = ($this->_user->getBillingAddress()) ? $this->_user->getBillingAddress()->toArray() : null;
            $currentUser['fastlane'] = ($this->_user->getFastlaneSettings()) ? $this->_user->getFastlaneSettings()->toArray() : null;
            $currentUser['other'] = $otherAddress;
            $this->apiReturn->setResultData(array(
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                'user' => $currentUser,
            ));
            $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
            $this->jsonPrinter->printJSON($response);
        }
    }

    /**
     * creates a new user For New Website
     */
    public
    function actionCreatenew()
    {
        $error = '';
        $form = new RegistrationForm();
        $values = $this->getParam('RegistrationForm');
        if ($values) {
            if (!isset($values['password'])) {
                $values['password'] = $this->generatePassword(10);
            }
            $values['first_name'] = 'Customer';
            $values['last_name'] = '';
            $values['is_password_mail'] = true;
            $contact = isset($values['contact_number']) ? $values['contact_number'] : null;
            $validParams = Array('email', 'contact_number', 'password');
            $form->setAttributes($values, false);
            if ($form->validate($validParams)) {
                if ($form->is_newsletter_subscribed == 1) {
                    $this->newslettersubscription();
                }
                $bobResult = $this->_user->save($form->attributes, false);
                if ($bobResult->isSuccess()) {
                    $customer = $bobResult->getResultData();
                    $this->_user->setCurrentUser($customer);
                    if (!empty($contact)) {
                        $contactObj = new Transfer_Customer_ContactNumber();
                        $contactObj->setContactNumber($contact);
                        $contactObj->setFkCustomer($customer->getIdCustomer());
                        $contactObj->setContactType(0);
                        $this->_user->updateCustomerContactNumber($contactObj);
                    }
                    unset($this->_user);
                    TrackingService::store('existingCustomer', false);
                } else {
                    $bobErrors = $bobResult->getValidationMessages();
                    if (isset($bobErrors['email']['recordFound']) && $bobErrors['email']['recordFound']) {
                        $error = 'This email id is already registered with us';
                    } else if (isset($bobErrors['email']['emailAddressInvalidHostname']) && $bobErrors['email']['emailAddressInvalidHostname']) {
                        $error = 'This is not a valid email id';
                    }
                }
            }
        }
        echo json_encode(array('errorMessage' => $error, 'success' => empty($error) ? 'yes' : 'no'));
    }

    /**
     * <p>
     *  Create New customer
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public
    function actionCreate()
    {

        if (!$this->_user->isGuest) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED, static::SR_CUSTOMER_ALREADY_LOGGED_IN));
        } else if (Yii::app()->request->getIsPostRequest()) {

            //@todo : need work for is_confirm = 0

            $form = new RegistrationForm();
            $values = $this->getParam('RegistrationForm');

            $this->apiReturn->setSuccess(false);

            if ($values != null) {
                $form->setAttributes($values, false);

                if ($form->validate()) {
                    $userReturn = $this->_user->save($form->attributes);
                    if ($userReturn instanceof Service_Response && $userReturn->isSuccess()) {
                        $this->_user->setCurrentUser(new Transfer_Customer());
                        $rawData = $userReturn->getResultData()->getRawData();
                        $currentUser = $userReturn->getResultData()->toArray();
                        //INDFUR-1526 save logintoken in db
                        $unique = array();  //declare the array
                        $genrateId = uniqid(rand(), true); //genrate the unique id
                        $uniqueId = $genrateId . ':' . $this->serverKey; //adding config unique id to maintain surity between unique id
                        $currentUser['login_token'] = $uniqueId;

                        $bob = GenericModel::getAdapterByName('BobAdapter');
                        $tokenResponse = $bob->get('autoLoginToken', false, false, $currentUser);
                        $apiToken = MobileApiHelper::_processTokenResponse($tokenResponse);
                        $rawData['login_token'] = $apiToken . ':' . $this->serverKey;
                        $customer = $userReturn->getResultData();
                        $this->_user->setCurrentUser($customer);
                        $identity = new UserIdentity($form->attributes['email'], $form->attributes['password']);
                        $identity->authenticate();
                        $duration = $form->rememberme ? 3600 * 24 * 30 : 0;
                        $this->_user->login($identity, $duration);

                        $this->apiReturn->setSuccess(true);
                        $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_SUCCESS));
                        $this->apiReturn->setResultData($rawData);
                    } else if ($userReturn instanceof Service_Response) {
                        /*$validationMessage = $userReturn->getValidationMessages();
                        if (!empty($validationMessage['email']) && !empty($validationMessage['email']['recordFound'])) {
                            $this->apiReturn->setValidationMessages(array('email' => 'Email already exists.'));
                        }*/
                        $this->apiReturn->setSuccess(false);
                        $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED_EXISTS));
                    } else {
                        $this->apiReturn->setSuccess(false);
                        $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED_EXISTS));
                    }
                } else {
                    $formError = $form->getErrors();
                    $error = array();
                    if (isset($formError['day'])) {
                        $error['day'] = array_unique($formError['day']);
                    }
                    if (isset($formError['month'])) {
                        $error['month'] = array_unique($formError['month']);
                    }
                    if (isset($formError['year'])) {
                        $error['year'] = array_unique($formError['year']);
                    }
                    $formError = array_merge($formError, $error);
                    $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                    $this->apiReturn->setValidationMessages($formError);
                }
            } else {
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED));
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED));
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     *  Register New customer
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public
    function actionRegister()
    {

        if (!$this->_user->isGuest) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED, static::SR_CUSTOMER_ALREADY_LOGGED_IN));
        } else if (Yii::app()->request->getIsPostRequest()) {

            //@todo : need work for is_confirm = 0

            $form = new RegistrationForm();
            $values = $this->getParam('RegistrationForm');

            $this->apiReturn->setSuccess(false);

            if ($values != null) {
                // a user cannot be created without a password, so we generate a random one by default.
                if (!isset($values['password'])) {
                    $values['password'] = $this->generatePassword(10);
                }
                //workaround for last name
                $values['first_name'] = 'Customer';
                $values['last_name'] = '';
                $values['is_password_mail'] = true;
                unset($values['day']);
                unset($values['month']);
                unset($values['year']);
                unset($values['password2']);

                $contact = isset($values['contact_number']) ? $values['contact_number'] : null;
                $validParams = Array('email', 'gender');


                $form->setAttributes($values, false);

                if ($form->validate($validParams)) {
                    $userReturn = $this->_user->save($form->attributes);

                    if ($userReturn instanceof Service_Response && $userReturn->isSuccess()) {
                        $this->_user->setCurrentUser(new Transfer_Customer());
                        $customer = $userReturn->getResultData();

                        $this->_user->setCurrentUser($customer);
                        if (!empty($contact)) {
                            $contactObj = new Transfer_Customer_ContactNumber();
                            $contactObj->setContactNumber($contact);
                            $contactObj->setFkCustomer($customer->getIdCustomer());
                            $platform = Session::get('IS_MOBILE_APP_PLATFORM');
                            $contact_type = ($platform == 'ios') ? 3 : 2;
                            $contactObj->setContactType($contact_type);
                            $this->_user->updateCustomerContactNumber($contactObj);
                        }
                        $identity = new UserIdentity($form->attributes['email'], $form->attributes['password']);
                        $identity->authenticate();
                        $form->rememberme = 1;
                        $duration = $form->rememberme ? 3600 * 24 * 30 : 0;
                        $this->_user->login($identity, $duration);
                        $currentUser = $this->_user->getCurrentUser()->toArray();
                        $this->apiReturn->setSuccess(true);
                        $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_SUCCESS));
                        $this->apiReturn->setResultData(array(
                            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                            'user' => $currentUser,
                        ));
                    } else if ($userReturn instanceof Service_Response) {
                        $this->apiReturn->setSuccess(false);
                        $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED_EXISTS));
                    } else {
                        $this->apiReturn->setSuccess(false);
                        $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED_EXISTS));
                    }
                } else {
                    $formError = $form->getErrors();
                    $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                    $this->apiReturn->setValidationMessages($formError);
                }
            } else {
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED));
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_REGISTRATION_FAILED));
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Customer Logout
     * </p>
     */
    public
    function actionLogout()
    {
        if (!$this->_user->isGuest) {
            $currentUser = $this->_user->getCurrentUser()->toArray();
            if (!empty($currentUser['id_customer'])) {
                $status = $this->_user->delLoginToken($currentUser['id_customer']);
            }
            $this->_user->logout();
            $this->cart->clear();
            $this->apiReturn->setSuccess(true);
            $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_LOGOUT_SUCCESS));
        } else {
            $this->apiReturn->setSuccess(true);
            $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGOUT_FAILED, static::SR_CUSTOMER_NOT_LOGGED_IN));
            $this->apiReturn->setValidationMessages(array(I18n::t('No logged in user.')));
        }

        $service = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($service);
    }

    /**
     * <p>
     *  Track order
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public
    function actionTrackorder()
    {

        $trackOrderForm = new TrackOrderForm();
        $trackOrderFormData = $this->getParam('TrackOrderForm');

        if (Yii::app()->request->isPostRequest && $trackOrderFormData) {
            $trackOrderForm->attributes = $trackOrderFormData;
            if ($trackOrderForm->validate()) {
                $orderModel = new Order();

                if (strlen($trackOrderForm->ordernumber) <= 60) {
                    $orderNumber = explode(',', $trackOrderForm->ordernumber);
                    $orderNumber = array_filter($orderNumber);

                    if (is_array($orderNumber) && count($orderNumber) > 0) {

                        $orderNumber = implode(',', $orderNumber);
                        $order = $orderModel->getOrdersDetailsByOrderNumbers($orderNumber);

                        //for display error
                        $orderSearch = explode(",", $trackOrderForm->ordernumber);
                        $orderNumberNotFound = array();
                        foreach ($orderSearch as $key => $ordernum):
                            if ((int)$ordernum && ($order !== false)):
                                if (!array_key_exists((int)$ordernum, $order)):
                                    $orderNumberNotFound[] = $ordernum;
                                endif;
                            else:
                                $orderNumberNotFound[] = $ordernum;
                            endif;
                        endforeach;

                        if (isset($orderNumberNotFound[0])) {
                            $orderNumberNotFound[0] = static::SR_INVALID_ORDER_NUMBER . $orderNumberNotFound[0];
                        }

                        if ($order) {
                            $this->apiReturn->setSuccess(true);
                            $this->apiReturn->setResultData($order);
                            if (!empty($orderNumberNotFound) && count($orderNumberNotFound)) {
                                $this->apiReturn->setErrorMessages(array(wordwrap(implode(",", $orderNumberNotFound), 120, "\n", true)));
                            }
                        } else {
                            $this->apiReturn->setSuccess(false);
                            $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                            $this->apiReturn->setErrorMessages(array(static::SR_TRACK_NO_ORDER_FOUND));
                            if (!empty($orderNumberNotFound) && count($orderNumberNotFound)) {
                                $this->apiReturn->setErrorMessages(array(wordwrap(implode(",", $orderNumberNotFound), 120, "\n", true)));
                            }
                        }
                    } else {
                        $this->apiReturn->setSuccess(false);
                        $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                        $this->apiReturn->setValidationMessages(array(static::SR_TRACK_NO_ORDER_NUMBER_PROVIDED));
                    }
                } else {
                    $this->apiReturn->setSuccess(false);
                    $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                    $this->apiReturn->setValidationMessages(array(static::SR_TRACK_ORDER_TOO_MANY_ORDERS));
                }
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                $this->apiReturn->setValidationMessages($trackOrderForm->getErrors());
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
            $this->apiReturn->setValidationMessages(array(static::SR_TRACK_NO_ORDER_NUMBER_PROVIDED));
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * edit user password
     * @author shailesh ojha
     */
    public
    function actionChangepass()
    {
        if ($this->_user->isGuest) {
            return $this->_notLoggedIn();
        }
        $form = new PasswordForm();
        // validate password form if it was sent
        if ($values = $this->getParam('PasswordForm')) {
            $form->attributes = $values;

            if ($form->validate()) {
                $userData = array(
                    'new_password' => $form->password,
                    'id_customer' => $this->_user->getCurrentUser()->getIdCustomer()
                );
                $bobResult = $this->_user->save($userData);
                if ($bobResult->isSuccess()) {
                    $this->_user->setCurrentUser($bobResult->getResultData());
                }
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setSuccessMessages(array(I18n::t('Edit password successful')));
            }
            $formError = $form->getErrors();
            if (!empty($formError)) {
                //$this->apiReturn->setResultData($formError);
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                $this->apiReturn->setValidationMessages($formError);
            }
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Forgot Password form handling
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public
    function actionForgotPassword()
    {
        $form = new ForgotPasswordForm();
        $formValues = $this->getParam('ForgotPasswordForm');

        if ($formValues) {
            $form->attributes = $formValues;
            //@todo : need work for is_confirm = 0

            if ($form->validate()) {
                $email = $formValues['email'];
                $restorePasswordKey = md5(uniqid(mt_rand(), true));
                $data = array(
                    'email' => $email,
                    'restore_password_key' => $restorePasswordKey,
                    'url' => $this->createAbsoluteUrl('/customer/account/restorepass/key')
                );
                $data['url'] = $data['url'] . '/';

                $this->apiReturn = $this->_user->setRestorePasswordData($data);
                if ($this->apiReturn->isFailure()) {
                    // structure of bob service response is not compatible with the one the native app expects
                    $this->_rebuildMessagesStructure();
                }
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                $this->apiReturn->setValidationMessages(array(I18n::t('The email address provided is not correct.')));
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_FORM_EMPTY));
        }
        $message = $this->apiReturn->getsuccessMessages();
        if (isset($message)) {
            if (isset($message[0]['message']) && $message[0]['message'] == "CUSTOMER_SET_PASSWORD_RESTORE_DATA_SUCCESS") {
                $this->apiReturn->setSuccessMessages(array(static::CUSTOMER_SET_PASSWORD_RESTORE_DATA_SUCCESS));
            }
        }
        $this->jsonPrinter->printJSON($this->mobileAPIService->getApiServiceResponse($this->apiReturn));
    }

    /**
     * <p>
     *  Rebuild message structure
     * </p>
     * @param void
     * @return void
     * @access protected
     */
    protected
    function _rebuildMessagesStructure()
    {
        $errorMessages = $this->apiReturn->getErrorMessages();
        $validationMessages = $this->apiReturn->getValidationMessages();

        $rebuiltErrorMessages = array();
        $rebuiltValidationMessages = array();
        foreach ($errorMessages as $message) {
            $rebuiltErrorMessages[] = $message['message'];
        }
        foreach ($validationMessages as $messages) {
            foreach ($messages as $message) {
                $rebuiltValidationMessages[] = $message;
            }
        }
        if (isset($rebuiltErrorMessages[0]) && $rebuiltErrorMessages[0] == 'CUSTOMER_SET_PASSWORD_RESTORE_DATA_FAILURE') {
            $rebuiltErrorMessages[0] = static::CUSTOMER_SET_PASSWORD_RESTORE_DATA_FAILURE;
        }
        $this->apiReturn->setErrorMessages($rebuiltErrorMessages);
        $this->apiReturn->setValidationMessages($rebuiltValidationMessages);
    }

    /**
     * <p>
     *  Rebuild Success message structure
     * </p>
     * @param void
     * @return void
     * @access protected
     * @author shailesh ojha
     */
    protected
    function _rebuildSuccessMessagesStructure()
    {
        $successMessages = $this->apiReturn->getSuccessMessages();
        $rebuiltsuccessMessages = array();
        foreach ($successMessages as $message) {
            $value = '';
            if (!empty($message['values'])) {
                $value = $message['values'][0];
            }
            $rebuiltsuccessMessages[] = I18n::t($message['message'], $value);
        }
        $this->apiReturn->setSuccessMessages($rebuiltsuccessMessages);
    }

    /**
     * <p>
     *  Get myorder list for logged-in customer
     * </p>
     * @param void
     * @return void
     * @access public
     */
    public
    function actionGetmyorderlist()
    {
        if (Yii::app()->user->isGuest) {
            return $this->_notLoggedIn();
        } else {
            $model = new ApiCustomer();
            $orders = $model->getCustomerOrders();


            if ($orders) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setResultData($orders);
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_ORDER_NOT_FOUND));
            }
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Get customer order detail for
     * logged-in customer based on order id
     * </p>
     * @param void
     * @return void
     */
    public
    function actionGetorderdetail()
    {
        if (Yii::app()->user->isGuest) {
            return $this->_notLoggedIn();
        } else {
            $orderId = (int)$this->getParam('order_id', 0);
            $model = new ApiCustomer();
            $order = $model->getOrderDetailByOrderId($orderId, true);

            if ($order && is_array($order)) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setResultData($order);
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_ORDER_NOT_FOUND));
            }
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Get last success order detail
     * </p>
     * @param void
     * @param void
     * @access public
     */
    public
    function actionLastsuccessorderdetail()
    {

        if (Session::isRegistered('LAST_SUCCESS_ORDER_ID') &&
            Session::get('LAST_SUCCESS_ORDER_ID')
        ) {

            $orderId = Session::get('LAST_SUCCESS_ORDER_ID');
            Session::remove('LAST_SUCCESS_ORDER_ID');

            $model = new ApiCustomer();
            $order = $model->getOrderDetailByOrderId($orderId);

            if ($order && is_array($order)) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setResultData($order);
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_ORDER_LAST_SUCCESS_ORDER_NOT_FOUND));
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_ORDER_LAST_SUCCESS_ORDER_NOT_FOUND));
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Get last failed order number
     * </p>
     * @param void
     * @param void
     * @access public
     */
    public
    function actionLastfailedorder()
    {

        if (Session::isRegistered('LAST_FAILED_ORDER_NR') &&
            Session::get('LAST_FAILED_ORDER_NR')
        ) {
            $orderNumber = Session::get('LAST_FAILED_ORDER_NR');

            $this->apiReturn->setSuccess(true);
            $this->apiReturn->setResultData($orderNumber);
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_ORDER_LAST_FAILED_ORDER_NOT_FOUND));
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Get last success order detail dummy for testing purpose can remove later
     * </p>
     * @param void
     * @param void
     * @access public
     */
    public
    function actionLastsuccessorderdetaildummy()
    {
        if (true || Session::isRegistered('LAST_SUCCESS_ORDER_ID') &&
            Session::get('LAST_SUCCESS_ORDER_ID')
        ) {

            $orderId = Session::get('LAST_SUCCESS_ORDER_ID');
            Session::remove('LAST_SUCCESS_ORDER_ID');

            if ($_SERVER['HTTP_HOST'] == 'fabfurnish.loc') {
                $orderId = 440681;
            } else {
                $orderId = 472151;
            }
            $model = new ApiCustomer();
            $order1 = $model->getOrderDetailByOrderId($orderId);
            if (isset($order1['order_charges'])) {
                unset($order1['order_charges']);
            }
            $orderDetail = array('order_charges' => array
            (array(
                    'title' => 'Shipping Charges',
                    'value' => "100",
                    'color' => '#333333'
                ),
                array(
                    'title' => 'Processing Charges',
                    'value' => "90",
                    'color' => '#333333'
                ),
                array(
                    'title' => 'Tax Collected',
                    'value' => "50",
                    'color' => '#333333'
                ),
                array(
                    'title' => 'Discount / Coupon Value',
                    'value' => "-200",
                    'color' => '#c13100'
                ),
                array(
                    'title' => 'Offer Discount',
                    'value' => "-220",
                    'color' => '#c13100'
                ),
                array(
                    'title' => 'Cash Back',
                    'value' => "-80",
                    'color' => '#c13100'
                ),
                array(
                    'title' => 'Loan Amount',
                    'value' => "400",
                    'color' => '#333333'
                ),
                array(
                    'title' => 'EMI Interest',
                    'value' => "320",
                    'color' => '#333333'
                )
            ));
            $order = array_merge($orderDetail, $order1);
            if ($order && is_array($order)) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setResultData($order);
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_ORDER_LAST_SUCCESS_ORDER_NOT_FOUND));
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_ORDER_LAST_SUCCESS_ORDER_NOT_FOUND));
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Customer auto login
     * </p>
     * @param void
     * @return void
     * @access public
     * @author Anuj Dhingra <anuj.dhingra@fabfurnish.com>
     */
    public
    function actionCheckAutoLogin()
    {

        $customerModel = new ApiCustomer();
        if (!$this->_user->isGuest) {
            $isGuest = false;

        } else {
            $isGuest = true;
        }
        if ($this->_user->isGuest) {
            $result = $customerModel->getAutoLogin($isGuest, $_SERVER['HTTP_LOGINTOKEN']);
            if (!empty($result) && ($result['updatedToken'] && $result['userType'])) {
                $currentUser = $this->_user->getCurrentUser()->toArray();
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_LOGIN_SUCCESS));
                $unique['token'] = $result['updatedToken'];
                $currentUser = array_merge($currentUser, $unique);
                $this->apiReturn->setResultData(
                    array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                        'user' => $currentUser,
                    )
                );
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGIN_FAILED, static::SR_CUSTOMER_LOGIN_CHECK));
                $this->apiReturn->setValidationMessages(array(I18n::t('invalid token')));
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_ALREADY_LOGGED_IN));
            $this->apiReturn->setValidationMessages(array(I18n::t('Already logged in.')));

        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Get Reward and credit point Details of User
     * </p>
     * @param void
     * @return void
     * @access public
     * @Authour Amit Chaudhary
     */
    public
    function actionGetPointDetails()
    {
        if ($this->_user->isGuest) {
            return $this->_notLoggedIn();
        } else {
            $pointArray = array();
            $this->apiReturn->setSuccess(true);
            $loyaltyPointsData = $this->_user->getLoyaltyPointsCollectionByUserId()->toArray();
            $voucherModel = new Voucher();
            $voucherData = $voucherModel->getVoucherCollectionByUserId($this->_user->id)->toArray();
            $totalPoints = 0;
            $waitingPoints = 0;
            if (isset($loyaltyPointsData['customer_sales_order_item_loyalty_collection'])) {
                foreach ($loyaltyPointsData['customer_sales_order_item_loyalty_collection'] as $loualtyPoints) {
                    if ($loualtyPoints['status'] == 'CONSUME' && $loualtyPoints['reflected'] == 2) {
                        $totalPoints += $loualtyPoints['points'] - $loualtyPoints['used_points'];
                    } elseif ($loualtyPoints['status'] != 'EXPIRED' && $loualtyPoints['reflected'] < 2) {
                        $waitingPoints += $loualtyPoints['points'] - $loualtyPoints['used_points'];
                    }
                }
            }
            $pointArray['RewardPoints'] = array('totalPoints' => $totalPoints, 'waitingPoints' => $waitingPoints);
            $availableLoyaltyPoints = 0;
            $currentDate = date_parse(date('Y-m-d'));
            if (isset($loyaltyPointsData['customer_sales_order_item_loyalty_collection']) && count($loyaltyPointsData['customer_sales_order_item_loyalty_collection'])) {
                $historySet = array();
                foreach ($loyaltyPointsData['customer_sales_order_item_loyalty_collection'] as $loualtyPoints) {
                    if ($loualtyPoints['status'] != 'EXPIRED') {
                        $availablePoints = 0;
                        $activation_on = (isset($loualtyPoints['activated_on'])) ? date_parse($loualtyPoints['activated_on']) : '';
                        if (($activation_on != '' && $activation_on <= $currentDate) && $loualtyPoints['status'] != 'EXPIRED') {
                            $availableLoyaltyPoints += $loualtyPoints['points'] - $loualtyPoints['used_points'];
                            $availablePoints = $loualtyPoints['points'] - $loualtyPoints['used_points'];
                        }
                        $lpStatus = ($loualtyPoints['status'] == 'CONSUME' && $loualtyPoints['reflected'] == 2) ? 'Available' : 'Waiting';
                        $loualtyPoints['activated_on'] = (isset($loualtyPoints['activated_on'])) ? date('jS M, y', strtotime($loualtyPoints['activated_on'])) : 'NA';
                        $loualtyPoints['expired_on'] = (isset($loualtyPoints['expired_on'])) ? date('jS M, y', strtotime($loualtyPoints['expired_on'])) : 'NA';
                        $historySet[] = array(
                            'activated_on' => $loualtyPoints['activated_on'],
                            'status' => $lpStatus,
                            'total_points' => $loualtyPoints['points'],
                            'available_point' => $availablePoints,
                            'expired_on' => $loualtyPoints['expired_on']
                        );
                    }
                }
            } else {
                $historySet[] = array();
            }
            if (isset($loyaltyPointsData['customer_sales_order_item_loyalty_history_collection']) && count($loyaltyPointsData['customer_sales_order_item_loyalty_history_collection'])) {
                $consumed = array();
                foreach ($loyaltyPointsData['customer_sales_order_item_loyalty_history_collection'] as $loualtyPointsDebit) {
                    $remarks = (isset($loualtyPointsDebit['remarks'])) ? $loualtyPointsDebit['remarks'] : '';
                    if ($loualtyPointsDebit['transaction_type'] == 'Dr' && !(strstr($remarks, 'Points Expired')) && $loualtyPointsDebit['points'] > 0) {
                        $consumed[] = array(
                            'total_points' => $loualtyPointsDebit['points'],
                            'available_point' => $remarks,

                        );
                    }
                }
            } else {
                $consumed[] = array();
            }
            if (isset($loyaltyPointsData['customer_sales_order_item_loyalty_collection']) && count($loyaltyPointsData['customer_sales_order_item_loyalty_collection'])) {
                $expiredBalance = array();
                foreach ($loyaltyPointsData['customer_sales_order_item_loyalty_collection'] as $loualtyPoints) {
                    if (isset($loualtyPoints['status']) && $loualtyPoints['status'] == 'EXPIRED') {
                        $loualtyPoints['expired_on'] = (isset($loualtyPoints['expired_on'])) ? date('jS M, y', strtotime($loualtyPoints['expired_on'])) : '';
                        $expiredBalance[] = array(
                            'expired_on' => $loualtyPoints['expired_on'],
                            'points' => $loualtyPoints['points'] - $loualtyPoints['used_points'],
                            'status' => ucfirst(strtolower($loualtyPoints['status']))
                        );
                    }
                }
            } else {
                $expiredBalance = array();
            }
            //$pointArray['AvailableBalance'] = $historySet;
            //$pointArray['Consumed'] = $consumed;
            //$pointArray['ExpiredBalance'] = $expiredBalance;
            $totalVoucherBalance = 0;
            $pointcreditArray = array();
            $historyCreditSet = array();
            if (isset($voucherData) && count($voucherData)) {
                foreach ($voucherData as $voucher) {
                    if ($voucher['is_active'] && strtotime($voucher['to_date']) > time()) {
                        $voucherBalance = $voucher['discount_amount'] - $voucher['used_discount_amount'];
                        $totalVoucherBalance += $voucherBalance;
                        $historyCreditSet[] = array(
                            'activated_on' => date('jS M, y', strtotime($voucher['from_date'])),
                            'type' => 'Credit',
                            'discount_amount' => $this->price($voucher['discount_amount']),
                            'balance' => $this->price($voucherBalance),
                            'expired_on' => date('jS M, y', strtotime($voucher['to_date']))
                        );
                    }
                }
            }
            $consumedHistory = array();
            foreach ($voucherData as $voucher) {
                if (isset($voucher['credit_voucher_transaction'])) {
                    foreach ($voucher['credit_voucher_transaction'] as $translationData) {
                        if ($translationData['transaction_type'] == 'Void') {
                            $transactionStatus = 'Refunded';
                        } else {
                            $transactionStatus = 'Consumed';
                        }
                        $consumedHistory[] = array(
                            'activated_on' => date('jS M, y', strtotime($voucher['from_date'])),
                            'type' => 'Credit',
                            'order_nr' => $translationData['order_nr'],
                            'amount' => $this->price($translationData['amount']),
                            'status' => $transactionStatus
                        );
                    }
                }
            }
            $expiredCreditBalance = array();
            foreach ($voucherData as $voucher) {
                $voucherBalance = $voucher['discount_amount'] - $voucher['used_discount_amount'];
                if (($voucher['is_active'] != 1 || strtotime($voucher['to_date']) < time()) && $voucherBalance > 0) {
                    if (strtotime($voucher['to_date']) < time()) {
                        $status = 'Expired';
                    } else {
                        $status = 'Consumed';
                    }
                    $expiredCreditBalance[] = array(
                        'activated_on' => date('jS M, y', strtotime($voucher['to_date'])),
                        'type' => 'Credit',
                        'amount' => $voucherBalance,
                        'status' => $status
                    );
                }
            }
            $pointcreditArray['CreditPoints'] = array('totalPoints' => $totalVoucherBalance);
            $pointcreditArray['AvailableBalance'] = $historyCreditSet;
            //$pointcreditArray['Consumed'] = $consumedHistory;
            //$pointcreditArray['ExpiredBalance'] = $expiredCreditBalance;
            $pointArrsetData['FabRewardPoint'] = $pointArray;
            $pointArrsetData['FabCreditPoint'] = $pointcreditArray;
            $this->apiReturn->setResultData($pointArrsetData);
            $return = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
            $this->jsonPrinter->printJSON($return);
        }
    }

    /**
     * Sets the current customer without authentification
     *
     * Attention: only used in checkout!!
     */
    public
    function set(Transfer_Customer $customer)
    {
        Yii::app()->session->add('customer', $customer);
        Yii::app()->session->add('customerId', $customer->getIdCustomer());
        Yii::app()->session->add('key', md5($_SERVER['REMOTE_ADDR']));
        $this->errorCode = 0;
    }

    /**
     * Load form to manage newsletter registration
     * @author shailesh ojha
     */
    public
    function actionNewsletter()
    {
        if (Yii::app()->user->isGuest) {
            return $this->_notLoggedIn();
        }
        $newsletter = new Newsletter();
        $actualFormValues = array();
        foreach ($this->_user->currentUser->getNewsletterCategories() as $category) {
            $actualFormValues[] = $category->getIdNewsletterCategory();
        }
        $form = new NewsletterForm();
        $form->newsletter_categories_subscribed = $actualFormValues;
        $formValues = $this->getParam('NewsletterForm');

        $categoryData = $newsletter->getCategories();
        if (!empty($formValues)) {
            $form->newsletter_categories_subscribed = array();
            if (!empty($formValues['newsletter_categories_subscribed']) && is_array($formValues['newsletter_categories_subscribed'])) {
                $form->newsletter_categories_subscribed = array_filter($formValues['newsletter_categories_subscribed']);
            }
            if ($form->validate()) {
                $this->apiReturn = $this->_user->setNewsletterStatus($form->attributes);
                if ($this->apiReturn->isSuccess()) {
                    $customer = $this->_user->currentUser;
                    $data = array();
                    foreach ($categoryData as $category) {
                        if (in_array($category['id_newsletter_category'], $form->newsletter_categories_subscribed)) {
                            $data[] = $category;
                        }
                    }
                    $customer->setNewsletterCategories(new Transfer_Newsletter_CategoryCollection($data));
                    $this->_user->setCurrentUser($customer);
                    $this->_rebuildSuccessMessagesStructure();
                }
                if ($this->apiReturn->isFailure()) {
                    // structure of bob service response is not compatible with the one the native app expects
                    $this->_rebuildMessagesStructure();
                }
            }
        }
        $categories = array();
        $subCategories = array();
        if (!empty($categoryData)) {
            foreach ($categoryData as $key => $value) {
                if ($value['show_in_myaccount']):
                    if (!isset($value['parent_id'])) {
                        $categories[$value['id_newsletter_category']] = $value['name'];
                    } else {
                        $subCategories[$value['parent_id']][$value['id_newsletter_category']] = $value['name'];
                    }
                endif;
            }
        }

        $data = array(
            'model' => $form,
            'categories' => $categories,
            'subCategories' => $subCategories,
        );
        $this->apiReturn->setResultData($data);
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);

    }

    /**
     * check pincode based city and state
     * @author shailesh ojha
     */

    public
    function actionPincode()
    {

        $pincode = '';
        if ($this->getParam('find')) {
            $pincode = $this->getParam('find');
            $model = new ApiCustomer();
            $pincodeDetail = $model->getPincodeCityandState($pincode);
            if (empty($pincodeDetail)) {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(I18n::t('Pincode does not exist.')));
            } else {
                $this->apiReturn->setSuccess(true);
                $return = array('data' => array('city' => $pincodeDetail['city'], 'state' => ucwords(strtolower($pincodeDetail['state']))));
                $this->apiReturn->setResultData($return);
            }

        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(I18n::t('Pincode does not exist.')));
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * Address managment add and edit
     * for edit use address_id
     * @author shailesh ojha
     */
    public
    function actionAddress()
    {
        if (Yii::app()->user->isGuest) {
            return $this->_notLoggedIn();
        }
        $form = new AddressForm();
        if ($this->getParam('address_id')) {
            $address_id = (int)$this->getParam('address_id');
            $address = $this->_user->getAddressById($address_id);
            $form->setAttributes($address->toArray(), false);
        } else {//case create
            $billingaddress = $this->_user->getBillingAddress();
            if (null !== $billingaddress) {
                $form->first_name = $billingaddress->getFirstName();
                $form->last_name = $billingaddress->getLastName();
            }
        }
        //save address
        if ($values = $this->getParam('AddressForm')) {
            $form->setAttributes($values, false);
            if ($form->validate()) {
                $this->apiReturn = $this->_user->saveAddress($form);
                if ($this->apiReturn->isSuccess()) {
                    $this->_rebuildSuccessMessagesStructure();
                }
                if ($this->apiReturn->isFailure()) {
                    $this->_rebuildMessagesStructure();
                }
            }
            $formError = $form->getErrors();
            if (!empty($formError)) {
                //$this->apiReturn->setResultData($formError);
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                $this->apiReturn->setValidationMessages($formError);
            }
        }
        $data = array('model' => $form);
        $this->apiReturn->setResultData($data);
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);

    }

    /**
     * Listing of indian states
     * @author shailesh ojha
     *
     */
    public
    function actionRegionList()
    {
        $model = new AddressForm();
        $stateList = $model->getRegionList(true);
        if (empty($stateList)) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(I18n::t('State does not exist.')));
        } else {
            $this->apiReturn->setSuccess(true);
            $states = array('states' => $stateList);
            $this->apiReturn->setResultData($states);
        }

        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * Address managment add default shipping
     * use address_id as request param
     * @author shailesh ojha
     */
    public
    function actionMakeDefaultShipping()
    {
        if (Yii::app()->user->isGuest) {
            return $this->_notLoggedIn();
        }

        if ($this->getParam('address_id')) {
            $address_id = (int)$this->getParam('address_id');
            $address = $this->_user->getAddressById($address_id);

            if (null === $address) {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(I18n::t('Address not found')));
            } else {
                $address->setValue('is_default_shipping', 1);

                $this->apiReturn = $this->_user->saveAddress($address);
                if ($this->apiReturn->isSuccess()) {
                    $this->_rebuildSuccessMessagesStructure();
                }
                if ($this->apiReturn->isFailure()) {
                    $this->_rebuildMessagesStructure();
                }
            }
        }
    }

    /**
     * <p>Google and facebook login Response </p>
     * @Author shailesh ojha
     */
    public
    function actionSocialLogin()
    {
        if (!Yii::app()->user->isGuest) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_ALREADY_LOGGED_IN));
            $this->apiReturn->setValidationMessages(array(I18n::t('Already logged in.')));
        } else {

            $userDetail = $this->getParam('RegistrationForm');
            if ($userDetail && is_array($userDetail) && array_key_exists('email', $userDetail)) {
                //facebook and google method
                if ($userDetail['reg_type'] == 'f') {
                    $this->_FbLogin($userDetail);
                } elseif ($userDetail['reg_type'] == 'g') {
                    $this->_Glogin($userDetail);
                }
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGIN_FAILED, static::SR_CUSTOMER_LOGIN_CHECK));
                $this->apiReturn->setValidationMessages(array(I18n::t('invalid token')));
            }
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    public
    function generatePassword($length)
    {
        $alphaNum = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $newString = str_shuffle(str_repeat($alphaNum, rand(1, $length)));
        return substr($newString, rand(0, strlen($newString) - $length), $length);
    }

    /**
     * <p>Action: Facebook Login Response </p>
     */
    public
    function _FbLogin($userDetail)
    {
        $validator = new CEmailValidator;
        if (!$validator->validateValue($userDetail['email'])) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGIN_FAILED, static::SR_CUSTOMER_LOGIN_CHECK));
            $this->apiReturn->setValidationMessages(array(I18n::t('Invalid email address')));
        } else {
            $data ['email'] = $userDetail['email'];
            $data ['first_name'] = array_key_exists('first_name', $userDetail) ? $userDetail['first_name'] : 'Customer';
            $data ['last_name'] = array_key_exists('last_name', $userDetail) ? $userDetail['last_name'] : '';
            if (array_key_exists('gender', $userDetail) &&
                in_array($userDetail['gender'], array('male', 'female'))
            ) {
                $data ['gender'] = $userDetail['gender'];
            }
            if (array_key_exists('birthday', $userDetail)) {
                $date = str_replace('/', '-', $userDetail['birthday']);
                $data ['birthday'] = Yii::app()->dateFormatter->format('y-MM-dd', strtotime($date));
            }
            if (array_key_exists('ip', $userDetail)) {
                $data ['ip'] = $userDetail['ip'];
            }

            $data['reg_type'] = 'facebook';
            $customer = Yii::app()->user->externalUserRegistratonLogin($data);
            if (!empty($customer)) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_LOGIN_SUCCESS));
                $currentUser = $this->_user->getCurrentUser()->toArray();

                //for autologin
                $bob = GenericModel::getAdapterByName('BobAdapter');
                $tokenResponse = $bob->get('autoLoginToken', false, false, $currentUser);
                $apiToken = MobileApiHelper::_processTokenResponse($tokenResponse);
                $currentUser['login_token'] = $apiToken . ':' . $this->serverKey;


                $this->apiReturn->setResultData(
                    array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                        'user' => $currentUser,
                    )
                );
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGIN_FAILED, static::SR_CUSTOMER_LOGIN_CHECK));
                $this->apiReturn->setValidationMessages(array(I18n::t('invalid token')));
            }
        }
    }

    /**
     * <p>Google login Response </p>
     */
    public
    function _Glogin($userDetail)
    {
        $validator = new CEmailValidator;
        if (!$validator->validateValue($userDetail['email'])) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGIN_FAILED, static::SR_CUSTOMER_LOGIN_CHECK));
            $this->apiReturn->setValidationMessages(array(I18n::t('Invalid email address')));
        } else {
            $data ['email'] = $userDetail['email'];
            $data ['first_name'] = array_key_exists('first_name', $userDetail) ? $userDetail['first_name'] : 'Customer';
            $data ['last_name'] = array_key_exists('last_name', $userDetail) ? $userDetail['last_name'] : '';
            if (array_key_exists('gender', $userDetail) &&
                in_array($userDetail['gender'], array('male', 'female'))
            ) {
                $data ['gender'] = $userDetail['gender'];
            }
            if (array_key_exists('birthday', $userDetail)) {
                $date = str_replace('/', '-', $userDetail['birthday']);
                $data ['birthday'] = Yii::app()->dateFormatter->format('y-MM-dd', strtotime($date));
            }
            if (array_key_exists('ip', $userDetail)) {
                $data ['ip'] = $userDetail['ip'];
            }

            $data['reg_type'] = 'google';
            $customer = Yii::app()->user->externalUserRegistratonLogin($data);
            if (!empty($customer)) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_LOGIN_SUCCESS));
                $currentUser = $this->_user->getCurrentUser()->toArray();

                //for autologin
                $bob = GenericModel::getAdapterByName('BobAdapter');
                $tokenResponse = $bob->get('autoLoginToken', false, false, $currentUser);
                $apiToken = MobileApiHelper::_processTokenResponse($tokenResponse);
                $currentUser['login_token'] = $apiToken . ':' . $this->serverKey;

                $this->apiReturn->setResultData(
                    array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                        'user' => $currentUser,
                    )
                );
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_CUSTOMER_LOGIN_FAILED, static::SR_CUSTOMER_LOGIN_CHECK));
                $this->apiReturn->setValidationMessages(array(I18n::t('invalid token')));
            }
        }
    }

    /**
     * <p>
     * Make Appointment for design & inspirations
     * </p>
     * @param void
     * @return void
     * @access public
     * @author <anuj.dhingra@fabfurnish.com> Anuj Dhingra
     */
    public
    function actionMakeappointment()
    {
        Yii::app()->getModule('design');
        $form = new AppointmentForm();
        // validate appointment form if it was sent
        if ($values = $this->getParam('AppointmentForm')) {
            $form->setAttributes($values, false);
            if ($form->validate()) {
                $customerObj = new Customer();
                $appointment = $customerObj->setCustomerAppointment($form);
            }
            if (!empty($appointment)) {
                $this->apiReturn->setSuccess(true);
                $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_APPOINTMENT_SUCCESS));
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setSuccessMessages(array(static::SR_CUSTOMER_APPOINTMENT_FAILED));
            }
        } else {

            $formError = $form->getErrors();
            $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
            $this->apiReturn->setValidationMessages($formError);
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }

    /**
     * <p>
     * Ideabook for design & inspirations
     * </p>
     * @param void
     * @return void
     * @access public
     * @author <anuj.dhingra@fabfurnish.com> Anuj Dhingra
     */
    public
    function actionIdeabook()
    {
        $wishlistModel = new Wishlist();
        $wishlistItemCollection = $wishlistModel->getCustomerIdeabook();

        $page = (int)($this->getParam('page', 1) - 1);

        $totalActiveSkuInWislistCollection = count($wishlistItemCollection);
        $wishlistItemCollection = $wishlistItemCollection->toArray();

        krsort($wishlistItemCollection);

        $lowerLimit = $page * self::IDEABOOK_PERPAGELISTCOUNT;
        $wishlistItemCollection = array_slice($wishlistItemCollection, $lowerLimit, self::IDEABOOK_PERPAGELISTCOUNT);
        $list = array();
        if ($totalActiveSkuInWislistCollection) {
            foreach ($wishlistItemCollection as $item) {
                $link = $this->createUrl('/catalog/detail', array('name' => $item['name'], 'id' => $item['fk_catalog_config']));
                $imgPath = isset($item['image']) ? $item['image'] . '.jpg' : '';
                $ideaList['name'] = $item['name'];
                $ideaList['image'] = $imgPath;
                $ideaList['link'] = ALICE_MOBILE_API_URL . 'design-inspiration/' . $link;
                $ideaList['shareUrl'] = ALICE_MOBILE_API_SHARE_URL . '/design-inspiration' . $link;
                $ideaList['removeIdeabookItem'] = ALICE_MOBILE_API_URL . 'customer/remove/?id_customer_wishlist=' . $item['id_customer_wishlist'] . '&type=ideabook';
                $ideaList['shopUrl'] = ALICE_MOBILE_API_URL . 'catalog/shopthelook?sku=' . $item['configurable_sku'];

                $list[] = $ideaList;
            }
            $md5 = md5(CJSON::encode($list));
            $this->apiReturn->setSuccess(true);
            $data = $this->_setData($list, null, true, $md5);
            $data['ideabook_count'] = $totalActiveSkuInWislistCollection;
            $this->apiReturn->setResultData($data);
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array(static::SR_NO_DATA_FOUND));
            $this->apiReturn->setResultData(array());
        }
        $return = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($return);
    }

    /**
     * <p>
     * add products to Ideabook for design & inspirations
     * </p>
     * @param void
     * @return void
     * @access public
     * @author <anuj.dhingra@fabfurnish.com> Anuj Dhingra
     */

    public
    function actionAdd()
    {
        $configSku = $this->getParam('p');
        $simpleSku = $this->getParam('sku');
        $type = $this->getParam('type');

        //echo json_encode(array('countWishList'=>$wishlistModel->getItemCountWishlist(),'msg'=>  implode("<br>", $msgArr),'configSku'=>$configSku));
        if ($this->_user->isGuest) {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array("Please login to add product to idea book"));
        } else {
            if ($configSku) {
                $catalog = new Catalog();
                $product = $catalog->getProduct($configSku);
                if (CatalogHelper::isNotBuyable($product)) {
                    $this->apiReturn->setSuccess(false);
                    $this->apiReturn->setErrorMessages(array("You can't add this in your Ideabook"));
                }
                //Yii::app()->getModule('customer');
                $wishlistModel = new Wishlist();
                $response = $wishlistModel->addItemToWishlist($configSku, $simpleSku, $type);
                if (false === $response) {
                    $this->apiReturn->setSuccess(false);
                    $this->apiReturn->setErrorMessages(array("Idea already exist in your ideabook"));
                } else {
                    if ($response->isSuccess()) {
                        $this->apiReturn->setSuccess(true);
                        $this->apiReturn->setSuccessMessages(array("Idea added in your ideabook"));
                        $wishlistModel = new Wishlist();
                        $wishlistItemCollection = $wishlistModel->getCustomerIdeabook();

                        $wishlistskuArr = array();
                        if (isset($wishlistItemCollection)) {
                            foreach ($wishlistItemCollection as $item) {
                                $wishlistskuArr[$item->getIdCustomerWishlist()] = $item->getConfigurableSku();
                            }
                        }
                        $wishlistId = array_search($configSku, $wishlistskuArr);
                        $data = array('removeIdeabookUrl' => ALICE_MOBILE_API_URL . 'customer/remove/?id_customer_wishlist=' . $wishlistId . '&type=ideabook');
                        $this->apiReturn->setResultData($data);
                    }
                }
            } else {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array("Idea already exist in your ideabook"));
            }

        }
        $return = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($return);

    }

    /**
     * <p>
     * remove products to Ideabook for design & inspirations
     * </p>
     * @param void
     * @return void
     * @access public
     * @author <anuj.dhingra@fabfurnish.com> Anuj Dhingra
     */
    public
    function actionRemove()
    {
        $idCustomerWishlist = $this->getParam('id_customer_wishlist');
        $type = $this->getParam('type');

        if (is_numeric($idCustomerWishlist)) {
            $wishlistModel = new Wishlist();
            if ($type == 'ideabook') {
                $response = $wishlistModel->removeItemFromWishlist($idCustomerWishlist);
            }
            if (false === $response) {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array("Cannot delete idea from your ideabook"));
            } else {
                if ($response->isSuccess()) {
                    $this->apiReturn->setSuccess(true);
                    $this->apiReturn->setSuccessMessages(array("Idea successfully removed from your ideabook."));
                }
            }
        } else {
            $this->apiReturn->setSuccess(false);
            $this->apiReturn->setErrorMessages(array("No item selected"));
        }
        $return = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($return);
    }

    /**
     * Book Service Form
     *
     * @author Anuj Dhingra <anuj.dhingra@fabfurnish.com>
     */
    public function actionBookService()
    {
        $form = new BookServiceForm();
        $model = new BookService();
        if ($values = $this->getParam('BookServiceForm')) {
            $form->attributes = $values;
            if ($form->validate()) {
                if (!empty($values['email']) && !empty($values['type'])) {
                    $platform = Session::get('IS_MOBILE_APP_PLATFORM');//Yii::app()->request->getServerParam('HTTP_XFABMOBAPIPLATFORM');
                    $values['platform'] = $platform;
                    $response = $model->setBookService($values);
                    if (!empty($response['referenceId'])) {
                        $this->apiReturn->setSuccess(true);
                        $this->apiReturn->setSuccessMessages(array(I18n::t('Your Service has been booked successfully. For future communications please <br /> save your reference ID ')));
                        $data = array('referenceId' => $response['referenceId']);
                        $this->apiReturn->setResultData($data);

                    } else {
                        $this->apiReturn->setSuccess(false);
                        $this->apiReturn->setErrorMessages(array(I18n::t('Somthing  went wrong please fill the form again')));
                    }
                }

            }
            $formError = $form->getErrors();
            if (!empty($formError)) {
                $this->apiReturn->setSuccess(false);
                $this->apiReturn->setErrorMessages(array(static::SR_FORM_VALIDATION_FAILED));
                $this->apiReturn->setValidationMessages($formError);
            }
        }
        $response = $this->mobileAPIService->getApiServiceResponse($this->apiReturn);
        $this->jsonPrinter->printJSON($response);
    }


    public function _sendRequest($url, $headers, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            curl_close($ch);
            $response = json_encode(array('error' => $error));
        } else {
            curl_close($ch);
        }
        return $response;
    }


    public function getConfigOrderStatuses($configKeys = array())
    {
        $orderItemStatusMapping = json_decode(SFDC_ORDER_STATUS, true);
        $statusArray = array();
        if (!empty($configKeys)) {
            foreach ($configKeys as $key => $value) {
                foreach ($orderItemStatusMapping[$key] as $val) {
                    $statusArray[] = array('status' => $val, 'display' => $value);
                }
            }
        }
        return $statusArray;
    }

    public function filterFinalOrder($orders)
    {
        if (!empty($orders)) {
            foreach ($orders as $key => $order) {
                if (!empty($order['order_items'])) {
                    $orderItems = $order['order_items'];
                    $itemsArr = array();
                    foreach ($orderItems as $item) {
                        $itemsArr[$item['article_code']] = array('delivery_date_text' => $item['delivery_date_text'],
                            'product_name' => $item['product_name'],
                            'image' => $item['image'],
                            'article_code' => $item['article_code'],
                            'order_item_id' => $item['order_item_id'],
                            'quantity' => !empty($itemsArr[$item['article_code']]['quantity']) ? $itemsArr[$item['article_code']]['quantity'] + 1 : 1,
                            'bill_of_landing' => $item['bill_of_landing'],
                            'transport_id' => $item['transport_id'],
                            'status' => $item['status']);
                    }
                    unset($order['order_items']);
                    $order['order_items'] = array_values($itemsArr);
                    $orders[$key] = $order;
                }
            }
        }
        return $orders;
    }

}
CustomerController.php
Displaying CustomerController.php.