
if (empty($stockCommitted)) {
    if (empty($goodsShipped2RegionalWarehouse)) {
    } else {
        if (!empty($remainingQ)) {  // Still Some Quantity Left
        }
    }
} else {
    if ($singleItemQ != $stockCommitted) {               // Commited Stock is not equal to Ordered Stock
        $remainingQ = $singleItemQ - $stockCommitted;    // x-y = z
        if (!empty($remainingQ)) {
            $remainingShippQ = $goodsShipped2RegionalWarehouse - $goodsReceived2RegionalWarehouse;  // A-B = k
            if (empty($remainingShippQ)) {
            } else {
                $lastRemainingQ = $remainingQ - $remainingShippQ;    // Z - K
                if (!empty($lastRemainingQ)) {
                }
            }
        }
    }

    $customArr = array();
    $itemStatusArray = array();
    if (!empty($itemCode[$singleItem['Product2']['SAP_Product_Code__c']])) {;
        $itemStatusArray = $itemCode[$singleItem['Product2']['SAP_Product_Code__c']];
    }
    if (empty($itemStatusArray)) {  // not in fitment and delivery
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
                } else if (!empty($orderItemStatusMapping['Hometown_Delivery'][end($explodedType)])) { // Delivery Main Status
                } else {   // Add Init +  Main
                }
            } else {   // Fitment
                // Delivery Will Be Default Added in case of fitment
                // Fitment JSON Handle
                if (!empty($orderItemStatusMapping['Hometown_Fitment'][end($explodedType)])) { // Fitment Main Status
                    $finalStatus[] = array('status' => $orderItemStatusMapping['Hometown_Fitment'][end($explodedType)], 'display' => 1);
                } else {   // Add Default Fitment PENDING
                }
            }
            if ($quantitySum == $stockCommitted) {  // Committed Quantity Equal To (Fitment+Delivery) Quantity
                break;
            }
        }

        if ($quantitySum != $stockCommitted && $stockCommitted > $quantitySum) {
        }
    }
}