Data flow 
1. The whole future group data get stored in SAP.
2. Whenever there is a new data they push the data in a queue and a cron runs that and  store the data from SAP to logs in mongo db (written in php)
3. Now since the logged data was not in proper format a cron runs that makes it in proper fromat and store in mysql db (written in php)
4. Again a cron runs that store the data in redis and solar 
    Solar is used for indexing
5. Salesforce - All the customer activity gets stored in salesforce
    It can be manual as well as from code
6. Alice - It is a php website for all environment of hometown websites
7. If data is seen on any of them that means there is a code issue otherwise data issue.