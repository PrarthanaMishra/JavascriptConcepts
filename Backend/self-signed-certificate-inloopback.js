Date : 15th May, 2019

Self signed certificate in loopback.
That means we call api with https://localhost/api

Step 1 : Generate local signed certificates. You can name them anything.
	Check if openssl is installed by typing openssl
openssl req -nodes -new -x509 -keyout server.key -out server.cert

Step 2 : Add the two keys in ssl-config.js 
	var path = require('path'),
fs = require("fs");
exports.privateKey = fs.readFileSync(path.join(__dirname, '../privatekey.pem')).toString();
exports.certificate = fs.readFileSync(path.join(__dirname, '../certificate.pem')).toString();

Step 3 : In server.js 
	var  app = module.exports = loopback() // creates server for loopback
Now we need to create new server with https protocol and then assign these certificates in 
options when creating the server.
var http = require(‘http’);
var https = require(‘https’);
var sslConfig = require(‘./ssl-config’);

Pass httponly paramter in 
if (httpOnly === undefined) {
 httpOnly = process.env.HTTP; 
} 
var server = null; 
if (!httpOnly) 
{ 
var options = { 
key: sslConfig.privateKey,
 cert: sslConfig.certificate
 };
 server = https.createServer(options, app);
 } else { server = http.createServer(app);
 }
Step 3 : Now we will listen server using server not app. Now we know that server
takes port as a first parameter so pass that in server.listen()
return server.listen(app.get('port'), function () { 
//add port first and than callback
Reference : Stack overflow link
https://stackoverflow.com/questions/47957538/preparing-loopback-to-use-ssl
Hackernoon:-
https://hackernoon.com/the-definitive-guide-to-express-the-node-js-web-application-framework-649352e2ae87




