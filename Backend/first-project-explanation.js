JavaScript

            Some questions links:
http://www.thatjsdude.com/interview/js2.html
Go through api.ai
Tensrflow
PacketSender
Socket programming : https://www.youtube.com/watch?v=HyGtI17qAjM
Gitbash as command line which can run standard command line like ls.
Npm(Node package Manager) for package management for node js.
https://nodejs.org/api/net.html - to check node js api’s
https://app.pluralsight.com
14th July, 2017
Mean Stack
Express.js : https://expressjs.com
Create app folder.
Go into that folder.
Do npm init
It will create a package.json file
Install express in that folder. Npm install express --save
It will save the version in package.json
Npm is mostly used to install modules in server side.
For client side we use bower.
Bower is installed using npm. Npm install bower -g
Create a file called .bowerrc
Add two curly braces. And path where you wanna keep front end packages, it should be public
Create a file in root folder called gitignore and add path of folder name you don’t want to keep in github.
Once dependency gets added if we will do npm install bower install
And then do init bower which will create bower.json file
And then to install angular js just do bower install angular js.
When will do bower install angularjs --save, it will add dependency in bower.json file
Create .bowerrc in root and then write path where you wanna install angular js.
And then install angular js, it will install it in that path only.
EJS and Jade template engine is used when we our application is not single page
Task:
How to render html page from express, how to create default routing,
Mongoose
First start mongod in one command and then mongo in other terminal.
17th July,2017
How to create restful api  in node js.
With moongose database and node js.
Create a project called  todoListApis
Go into that folder and run npm init - It will create a file called package.json
Create a folder api. In that folder create three sub folder controller,model and route. In each subfolder create a file.js.
Route,js will contain the route or url to get data from database.
Start server with nodemon. It will automatically server when any changes is done.
In model.js we will define schema for the database and we will create a model.
In controller.js we actually write  various api’s.We will import moongose and the model that we have created in model.js, and then we write the definition of various api’s defined in routing.js
Now in server.js, we will combine everything.
We will create a server that will listen to some port. And then we will import routes, and model created in model.js so will import that file too.
We will import body-parser, which is used to parse response in json format. Since only one route is not there, we will import route file and then route the app.
Make server listen to some port call a callback function and then see the output. Always use the default set up.

18th July,2017
1.ng-app,ng-controller,ng-repeat,ng-click,ng-model,ng-init (built in directives)
2.Filters, $index :tells index of array in ng-repeat
Create directive and appInfo: It can be used as a tag app-info. It will be used as a new html element.
User defined directive: Directives are very important.
Services: Used to communicate with server.
App.factory
Name we mention in connection is database name.
Project name
    Public
        View,controller,directive,service,app.js
Server
    Apis
    Models
    Route
Has to install body parser for every project.
return $http({
            method:'POST',
            url:'',
            data: {}
        })


Public set means that folder is available to server. So we can open file in that folder. Any file can opened in that folder, we don’t need to put pat till public folder
prarthana@uniplump.com
Put and patch

http://webmail.uniplum.com

Ng-dialog: has to pass data in ng-dialog then only it can show data, it has different scope. Read about ng-dialog
