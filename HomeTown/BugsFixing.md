To check bugs on node stage/Beta/
    errorlog:-
        tail -1f /var/log/hometowncore/error.log | while read LOGLINE; do echo $LOGLINE | python -mjson.tool ; done
        tail -1f /var/log/hometowncore/combined.log | while read LOGLINE; do echo $LOGLINE | python -mjson.tool ; done
    beta logs:-
        tail -1f /beta/logs/nginx/hometown-core | while read LOGLINE; do echo $LOGLINE | python -mjson.tool ; done
    accesslog:-
        tail -f /stage/logs/nginx/hometown-node/access.log
        tail -f /beta/logs/nginx/hometown-core/error.log

    frontend:-
     tail -f /stage/logs/nginx/frontend/frontend-access.log 
    tail -f /stage/logs/nginx/frontend/frontend-error.log 

    alice:-
      tail -100f /stage/logs/hometown-php/alice/exception.log 
        tail -f /stage/logs/nginx/hometown-php/alice.access.log 
        ls -l /stage/logs/hometown-php/alice/exception.log 

Login url for staging/beta
    sudo ssh -i HOMETOWN_STAGING.pem  ubuntu@13.232.175.105
    sudo ssh -i HT-BETA.pem  ubuntu@13.232.183.147

steps to deploy on any one of them
1.  npm run stage-build
    npm restart pm2 process name

Alice php url:-
https://stage-alice.hometown.in/mobapi/main/cache/key/product_TA876BH45ZTQHTFUR


kill -9 $(lsof -t -i:3001)
search redis
get stag_product

get tesla_api_product_listing 
The sku HO340FU73JXYHTFUR is not in solar. Cannot search this product on staging. It is added in the cart of
customer id = 1472602. And its not in redis as well and not in php only on mysql how?

nano 
cntrl+3 plus shift+3 - will give line number
contrl+shift+w - will give txtbox to search







