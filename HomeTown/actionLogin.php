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