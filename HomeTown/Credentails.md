Website url:-
live : 
    Desktop - hometown.in
    mobile - m.hometown.in
beta : 
    Desktop - beta.hometown.in
    mobile - beta-m.hometown.in

DB credentails :- 
Mysql stage connection
CORE_DB_PORT="3306"
    CORE_DB_DATABASE="hometowndb_stage"
    CORE_DB_USER="hometowndb_stage"
    CORE_DB_PWD="Homet0wNStag3"

MySql Beta connection 
    CORE_DB_HOST="hometowndb-ap-south-1b.c7vqbmwekhge.ap-south-1.rds.amazonaws.com"
    CORE_DB_PORT="3306"
    CORE_DB_DATABASE="hometowndb_beta"
    CORE_DB_USER="hometowndb_beta"
    CORE_DB_PWD="B3t@!23"
    CORE_DB_IDLE_TIMEOUT=1000
    CORE_DB_CONNECTION_TIMEOUT=1000
    CORE_DB_MIN_CONENCTION=10
    CORE_DB_MAX_CONENCTION=100
MySql Prod connection 

Redis Beta && stage connection 
    sudo ssh -i HOMETOWN_STAGING.pem  -f -N -L6381:172.31.24.165:6000 ubuntu@13.232.175.105
    sudo ssh -i HT-BETA.pem -f -N -L6382:172.31.24.165:6000 ubuntu@13.232.183.147
    sudo ssh -i HOMETOWN_STAGING.pem   ubuntu@13.232.175.105
    redis-cli 
    sudo ssh -i HT-BETA.pem ubuntu@13.232.183.147

Mongo stage connection
    MONGO_HOST=13.233.60.65
    MONGO_PORT=27000
    MONGO_USER=hometownmongo_stage
    MONGO_DB=hometownmongo_stage
    MONGO_PASSWORD=Stag3_!23

BOB credentails
    Beta : -
        url: https://beta-bob.hometown.in/
        username: prarthana
        password:123456
    Stage:- 

Server connection credentails :-
    sudo ssh -i HOMETOWN_STAGING.pem  ubuntu@13.232.175.105
    sudo ssh -i HT-BETA.pem ubuntu@13.232.183.147

Mysql server:-
    mysql -u hometowndb_stage -p -h hometowndb.c7vqbmwekhge.ap-south-1.rds.amazonaws.com
    password - 
New:-
   mysql -u hometowndb_stage -p -h replica-cluster.cluster-c7vqbmwekhge.ap-south-1.rds.amazonaws.com
   sudo service nginx restart
kill -9 $(lsof -t -i:3001)

replica-cluster.cluster-c7vqbmwekhge.ap-south-1.rds.amazonaws.com

PHP:-
nano bob/application/configs/application.ini

mysql -u hometowndb_beta -p -h hometowndb-replica-cluster.cluster-c7vqbmwekhge.ap-south-1.rds.amazonaws.com

mysql -u hometowndb_beta -p -h hometowndb-replica-cluster.cluster-c7vqbmwekhge.ap-south-1.rds.amazonaws.com


        






