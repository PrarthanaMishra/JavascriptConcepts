To run frontend in local system 
    -  .eslintrc
    "template-curly-spacing" : "off",
+    "indent" : "off",

pm2.json - "APIHOST": "stage-api.hometown.in/api/",

src/config.js - apiHost: 'localhost:1138/api/

src/helpers/apiClient.js - baseURL: `http://${config.apiHost}`