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
actionLogin- orders in profile

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












// const caseQuery = encodeURIComponent(
      //   'SELECT CaseNumber ,Subject,Description,Status,Origin,Type,Category__c,Sub_Category__c,CreatedDate ' +
      //     "FROM case WHERE AccountId='" +
      //     `${salesforceID}` +
      //     "'",
      //   `${where}`,
      // );

