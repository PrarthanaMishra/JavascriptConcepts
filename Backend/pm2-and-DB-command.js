Pm2 and other process

Basic directory commands 
    ls -la  : list contents of directory with hidden files
    ls -lt  : list contents of directory sorted by date
    ls -lh  : list contents of directory sorted by size along with displaying size of files 
    du -h   : total size of directory in which you enter this command 

ssh commands 
ssh-keygen -t rsa -b 4096 -C "your_email@example.com" :   genrating ssh key 
ssh-add -K ~/.ssh/id_rsa                              :   Adding key to ssh agent
ssh ubuntu@server-ip  or ssh ubuntu@domainname        :   connecting to server using ssh 

Copy and Move within server 
    cp /path/offile/tobecopied/source /destination/   : copying fle to another directory 
    cp -r /folderpath/source /destination/            : copy folder to another folder
    mv /path/offile/tobecopied/source /destination/   :  move file from one folder to another
    mv -r /folderpath/source /destination/            :  move file from one folder to another
    cp /path/offile/tobecopied/source/* /destination/ : copies contents/files of source folder to destination
    
Git commands 
    git log                                           : check logs of all commit and updates done on current directory on git
    git pull                                          : get latest commit from git server
    git branch                                        : displays current branch set for your directory  
    git checkout branchname                           : change to different branch from current one 
    git remote -v                                     : get git url set for your directory 
    
Pm2 commands 
    pm2 ls                                            : List all the processes hosted in pm2 
    pm2 show processID                                : show details of one process 
    pm2 restart/start/stop processID                  : After making changes to any file of hosted process use restart 
    pm2 logs                                          : print logs of all pm2 processes
    pm2 logs processID                                : print log of specific process 
    pm2 delete processID                              : delete particular process
    pm2 monit                                         : check memory and cpu utilization by one process
    pm2 logs | grep "text"                            : replace "text" with anything you want to search for within logs 
    pm2 restart app --name "new-name" --update-env    : update name of pm2 process
    NODE_ENV=staging pm2 start server/server.js --name "testsetup:9999" --interpreter "/pathtonodeversion/" : when we want a process to run on specific node version and system has multiple node versions installed
    
Copy Content from localsystem to server 
    scp /pathtofile/ ubuntu@server-ip:/destinationfolder : 
    scp -r /folderpath ubuntu@server-ip:/destinationfolder : 
    
Copy file from Server to Localsystem 
    scp ubuntu@server-ip:/sourcefile /destinationfolderlocalserver 
    scp -r ubuntu@server-ip:/sourcefolder /destinationfolderlocalserver
    
mysql commands 
    mysql -u "username" -p 
    mysql>use database;                                     : switch database
    mysql>queries;                                          : to run any query 
    mysql>show tables;                                      : get list of tables
    mysql>show processlist;                                 : list all the queries and connections established realtime 

Mongo Commands 
    mongo                                               : if no authentication is set
    mongo -h hostname -u "username" -p "password" --authenticationDatabase "admin" : if admin credentials are set
    mongo>show dbs;                                         : lists all databases
    mongo>use dbanme;                                       : select database to work on 
    mongo>list collections;                                 : show collections 
    mongo>db.collection.stats();                                       : detailed summary of single collection like size 

System commands 
    df -h   :  Check current space available on server : 
    top     :   Check status of current memory and cpu available on server 
    sudo service mysql/mysqld status
    sudo service mongodb status
    sudo service nginx status
    
Hosting new process on Staging 
    sudo mkdir testsetup
    sudo chown -R ubuntu:ubuntu testsetup/
    cd testsetup
    git init
    git remote add origin gitlaburlforproject
    git fetch
    git pull
    git checkout
    sudo npm install                                                 
    cd server
    cp datasource.json datasource.staging.json
    cp config.json config.staging.json
    cp externalApiparams.json externalApiparams.staging.json
    NODE_ENV=staging pm2 start server/server.js --name "testsetup:9999" :Process name should describe purpose : port should be used as defined in config.json

mysql backup and restore 
    mysqldump -u username -p dbanme > sqloutouutpul.sql - with data and schema but without stored procedure 
    mysqldump -u username -p dbanme --routines > sqloutouutpul.sql - with data and schema and stored procedure 
    mysqldump -u root -p database --ignore-table=database.table1 --ignore-table=database.table2 > dump.sql - skip tables from dump
    
    mysql -u username -p dbname < sqloutput.sql   -restore database 
    
mongo backup and restore 
    mongodump -h localhost -d databasename -o /pathtodump/ -u "username" -p "password" 
forex : mongodump --host staging.servify.in -d WebAppLogsStag --out /home/ubuntu/ --username "username" --password "password"
    
    mongorestore -d databasename /pathtodump/ 
forex : mongorestore -d WebAppLogsStag /home/ubuntu/WebAppLogsdump/ 
    
postgres 
login : sudo -iu postgres
        psql -d database  - choose database to perform actions 
        psql              - login to postgres 
basic : \dt               - list tables 
      : \du               - list users 
      : \l                - list database
create : create databasename                        - create empty database 
       : create user username                          - creating user without any role
       : create user username with SUPERUSER           - creating user with specified ROLE 
       
Dump  : with data - 
        pg_dump -d servify_integration > /tmp/dump.sql
        
        Only Schema - 
        pg_dump -d servify_integration -s > /tmp/dump.sql
        
Restore : 
        psql -d databasename -f dump.sql 

       
       
export csv : ```COPY (SELECT * FROM integration) TO '/tmp/integration.csv' WITH CSV HEADER;``` - after logging into database
    
    

