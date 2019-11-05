9th sep, 2019
About the product:-
    It has a frontend and backend.
    The same frontend use a technology called PWA. 
    By doing some configurations it has used the same frontend to built 
        - mobile site (redirect to different server when request is coming from mobile)
            It can be known from request Headers. 
        - Android App (The same app has been built in container and then deployed)
        - Website (basically a desktop application)
        - WebApp (its a app)
    They have managed two servers for mobile site and desktop site 
Project setup 
    - Installed git 
    - Installed node using nvm 
        https://gist.github.com/tomsihap/e703b9b063ecc101f5a4fc0b01a514c9
    - Installed node using v6.0
    - Created .env File
    - Build Bundle npm run build 
    - npm run watch 
    - Run redis on a different terminal
        redis cannot get connected from local. That's why we connect with the help of tunnel.
        Need to forward request from local to aws. 
        sudo ssh -i HOMETOWN_STAGING.pem  -f -N -L6381:172.31.24.165:6000 ubuntu@13.232.175.105
        sudo ssh -i HT-BETA.pem -f -N -L6382:172.31.24.165:6000 ubuntu@13.232.183.147
    - If we want to connect to redis command line 
        sudo ssh -i HOMETOWN_STAGING.pem   ubuntu@13.232.175.105
        redis-cli  
    - Install workbench 
    - Install robomongo 

10th sep, 2019
  Installed robo3T and mysql workbench from ubuntu software Center 
  Configure the local and staging databases

11th sep, 2019
   Going through bob. It's the url where they add things in the inventory and do all sort of 
   things for their website, mobile app etc

12th sep, 2019


14th sep, 2019

1. sudo chmod 777 -Rf ../frontend/
2. mv penv.tar.gz ../
3. tar -xvzf penv.tar.gz 
4. sudo killall -9 node
5. source nenv/bin/activate
6. tar -xvzf nenv.tar.gz 
7. npm run build-stage
7. pm2 restart pm2.json --env stage
8. npm install npm@6.9.0 -g
9. kill -9 $(sudo lsof -t -i:3001 )
http://localhost:1138/api/tesla/session/110001

Just to change server for a frontend we just need to change 
    baseURL: 'http://localhost:1138/api',
in apiclient.js file
   baseURL: 'http://localhost:1138/api',
    rejectUnauthorized: false,
    params: {
      devicePlatform: 'desktop' // when ios app ready remove it & get the os from native code and pass here
    }
  });
   const instance = axios.create({
    baseURL: 'http://localhost:1138/api',
    rejectUnauthorized: false,
    params: {
      devicePlatform: 'desktop' // when ios app ready remove it & get the os from native code and pass here
    }
  });
  To connect frontend to the required backend
  1.Run your backend with npm run dev-build and npm run watch 
  2. And then run frontend with the following commands 
  3. Activate npm first 
  4. source nenv/bin/activate 
  5. Then do pm2 list 
  6. Then make build of frontend using npm run build-stage 
  7. Then create execute using  pm2 restart pm2.json --env stage
  8. Then do pm2 logs

  We can send folders as well on an api url 
  For that just use mv command 
  1. First make tar file of env file suppose  
  2. Then   mv penv.tar.gz ../ to the required url that url would be path of the current direcotory
  3 So, if path of the current working direcotry is a api that we can fetch we download the tar file
  4. tar -xvzf nenv.tar.gz  this will decompress the tar file 

  Script file for redis 
   1. nano Script file ie .sh file 
   2. sh redis-tunnel.sh 
   3. sudo su 
   4. chmod +x ./redis-tunnel.sh 
   3. start the file with this ./redis-tunnel.sh
https://www.youtube.com/watch?v=LfA2XDmgVbo  

15th sep, 2019
1. Two changes are required to setup frontend without hard coding.
    In pm2.json - "APIHOST": "localhost:1138/api/"
    And in just apiClient.js remove s from http baseURL: `http://${config.apiHost}`
    Everything else is okay
TODO : To set up on local

https://beta-api.hometown.in/api/tesla/wishlist/800005?devicePlatform=desktop

25th sep, 2019
    deliveryDetails wishlist task:-
        main price = cut price 
        price = special price 
         didnt'getdata =>
        url 
        share_url
        combo_offer
        money_back_offer
        clour_group_count
        colour_group_producs
        is_supplier_display
        soldout

    testCase task
        import supertest from 'supertest';

        const server = supertest.agent('http://localhost:1138/api/tesla');
        describe('get session', () => {
        it('respond with session', done => {
            server
            .get('/session/110011')
            .expect('Content-Type', /json/)
            .expect(200, done);
        });
        });
    npm test should call session api and then response should come 
    npm run test-new will run session api. if it will execute it will run will give output success else stop            
    setupTestFrameworkScriptFile: './jest.setup.js',

    userRegistration task
     // const newsLetterSubscriptionOption = {
      //   fk_customer: 123,
      //   email,
      //   unsubscription_key: '',
      //   created_at: moment().format('YYYY-MM-DD HH:mm:ss.SSS'),
      //   status: 'subscribed',
      //   fk_newsletter_category: newsletterCategories.result[key].name,
      //   used_id_for_rr: '',
      };
     13.232.175.105
    export const bulkInsertIntoNewsletterSubscription = dbCallTransaction(
        `INSERT INTO newsletter_subscription(fk_customer, email, unsubscribe_key, created_at, status, fk_newsletter_category,user_id_for_rr) values ?`,
        );
     id for which name is newsletter
    




    






