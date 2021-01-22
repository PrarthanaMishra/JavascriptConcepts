sudo nginx -t
 1373  sudo service nginx status 
 1374  curl localhost
 1375  sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/hometown.loc
 1376  sudo nano /etc/nginx/sites-available/hometown.loc 
 1377  sudo ln -s /etc/nginx/sites-available/hometown.loc /etc/nginx/sites-enabled/
 1378  sudo nginx -t
 1379  sudo nano /etc/nginx/sites-available/default 
 1380  sudo nginx -t
 1381  sudo nano /etc/nginx/sites-available/hometown.loc 
 1382  sudo nginx -t
 1383  mkdir /var/logs/nginx/frontend/
 1384  mkdir /var/log/nginx/frontend
 1385  sudo mkdir /var/log/nginx/frontend
 1386  sudo nano /etc/nginx/sites-available/hometown.loc 
 1387  sudo nginx -t
 1388  sudo service nginx restart 
 1389  sudo nano /etc/nginx/sites-available/hometown.loc 
 1390  sudo nano  /etc/hosts
 1391  cp /etc/nginx/sites-available/hometown.loc /etc/nginx/sites-available/api.hometown.loc 
 1392  sudo cp /etc/nginx/sites-available/hometown.loc /etc/nginx/sites-available/api.hometown.loc 
 1393  sudo ln -s /etc/nginx/sites-available/api.hometown.loc /etc/nginx/sites-enabled/
 1394  sudo nano /etc/nginx/sites-enabled/api.hometown.loc 
 1395  sudo nano  /etc/hosts
 1396  sudo nano /etc/nginx/sites-enabled/api.hometown.loc 
 1397  sudo nginx -t
 1398  sudo mkdir /var/log/nginx/hometown-core
 1399  sudo nginx -t
 1400  sudo service nginx restart 
