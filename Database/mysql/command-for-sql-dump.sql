All about database 
Redis tutorial link
https://web.archive.org/web/20120118030804/http://simonwillison.net/static/2010/redis-tutorial/
VPS - Virtual Private Server 


Postgres
How to take dump of test server

pg_dump  -h localhost -p 5432 -U postgres -F c -b -v -f /home/ubuntu/database_dump_8jan2018.backup vdm_school

scp -i ~/Downloads/TESTSERVER2.pem ubuntu@52.205.254.101:/home/ubuntu/database_dump_26july2018.backup ~/Documents

pg_restore -h 192.168.1.17 -p 5432 -U postgres -d vdm_school_master -v ~/Downloads/database_dump_5Oct2017




