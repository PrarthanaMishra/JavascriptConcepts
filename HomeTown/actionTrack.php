<?php
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
                                    $status = $this->getConfigOrderStatuses(
                                        array(
                                            'Hometown_Init' => 1, 
                                            'Hometown_FSC' => 0, 
                                            'Hometown_Delivery' => 0
                                            )
                                    );
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