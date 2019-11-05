//sales force insert case and delete case written in php
Target : To change all apis in php in Node.
Sales-force login:-
https://test.salesforce.com
        username - webintegrationuser@praxisretail.in.devbox
        password - india@123
Login-in stage.hometown.in to see help button (my profile -> my orders)
    login - shruti.shah@prxisretail.in
    password - 123456
InsertCase - working
getCase
https://stage-api.hometown.in/api/tesla/users/insertcase
actionLogin- orders in profile - alice/alice/protected/modules/mopapi/controller/customerController

salesForce login:-
url:-
    https://workbench.developerforce.com/ 
usr - 
    webintegrationuser@praxisretail.in.devbox
    india@123

Account search with mobile number.
    then Id is sales force id
    and then this Id will be the accountid of order
Redis keys:-
stage keys:-
stage_sales-force_configuration
PHP trick:-
CustomerFailedOrders is a name of a function search in bob folder. So, all strings are function. 
findFailedOrders -> order.php

1. There are prefix in node as well for stage and beta now only  check that.
2. Where do cancelled orders get saved in mysql? sales_order?
sales_order -> sales_order_item(fk_sales_order)-> sales_order_item_status->fk_customer_address_region


order table query in salesforce (here it fetched only first order)
    case query in sales force for order id
        for each case query, query on Case_Line__c table for each case id
    OrderItem query for order id 
        get filtered non furniture products
sap order url 

website -> bob -> SAP -> salesforce


order table query in salesforce (here it fetched only first order)
    case query in sales force for order id
        for each case query, query on Case_Line__c table for each case id
    OrderItem query for order id 
        get filtered non furniture products
sap order url 

website -> bob -> SAP -> salesforce

window.dataLevel

select cchcc.fkfk_catalog_category 
    from catalog_config_has_catalog_category as cchcc
    join catalog_config cc 
        on cc.id_catalog_config = CCHCC.fk_catalog_config 
    where cc.sku_supplier_config = {product}
    and cc.status = 'active'

    and cchcc.fk_catalog_category IN (nonFurnitureCategory)

Print - 210643548

30th oct, 2019
Order to track - 0211402337
Points:-
1. All non furniture products are stored in SAP using our website.
    So, the products have only three status.
2. All furniture products gets stored in salesforce.- the status are basically for this products.
3. If products are not in salesforce they will be in BOB.
4. Some confusion in SAP will leave it for now.
5. We send order to SAP, SAP update it to salesforce. 
6. One order may contain multiple products, so fareye create on case for each delivery or fitment and
    it will have multiple case lines depends on how many products are there.

1. Difference between ordered stock and commited stock?
    commited stock - Means how many stock is there in a regional warehouse.
Commited stock means the stock that has gone for delivery from warehouse.
Suppose 5 products ordered in which only 3 has gone from regional warehouse. Other extra product will be shipped from other master warehouse. 3 is commited stock and other store is 5 which can be any other state


 // const orderQuery = encodeURIComponent(
  //   'SELECT Taxes2__c,Sales_TRX_Number__c,Id,TotalAmount,SAP_Created_Date__c,Delivery_Status__c,' +
  //     'ShippingStreet,ShippingCity, ShippingPostalCode,ShippingState,Delivery_Name__c,Billing_Name__c,' +
  //     'BillingStreet,BillingCity,BillingPostalCode,BillingState ' +
  //     `FROM Order WHERE Sales_TRX_Number__c = ` +
  //     " '" +
  //     `${sapOrderID} ` +
  //     "' " +
  //     ` OR Website_Order_Number__c = ` +
  //     "'" +
  //     `${bobOrderID} ` +
  //     "'" +
  //     ` OR Website_Order_Number__c = ` +
  //     "'" +
  //     `${orderID} ` +
  //     "'" +
  //     `OR Website_Order_Number__c = ` +
  //     "'" +
  //     `${paperFyOrder} ` +
  //     "'" +
  //     `OR Website_Order_Number__c = ` +
  //     "'" +
  //     `${amazonOrder}` +
  //     "'",
  // );

Response when u cannot track order before the sap track order. Give this response in track order footer.
  {"error":"You can not tack order before 2019-11-01"}









