
Await only works on promises not on callbacks. It needs promise object.

Eg: Throwing error while using await on callbacks
ConsumerProduct.fetchPlanQuestions = async function(data, cb) {
  let c = 2;
  let aegisResponse = await aegis.post({ // await on callback
    url: externalApiParams.aegis.versionURL + '/CustomPlan/fetchPlanQuestions',
    json: data
  }, function(error, response, body) {
    if (error) {
      log.error('Error in fetchPlanQuestions');
      log.error(error);
      return cb(null, {
        success: false,
        msg: 'Something went wrong',
        data: []
      });
    } else {
      //log.info('body', body);
      return cb(null, body);
    }
  });
  console.log(res.body);
  console.log("cccccccccccccccc");
  console.log(c);
} // Error callback is already called

This worked!
ConsumerProduct.fetchPlanQuestions = async function(data) {
  let c = 2;
  let planResponse = await getplanQuestions(data) // await on promise
  return planResponse; 
  console.log(res.body);
  console.log("cccccccccccccccc");
  console.log(c);
}

function getplanQuestions(data) {
  return new Promise((resolve, reject) => {
    aegis.post({
      url: externalApiParams.aegis.versionURL + '/CustomPlan/fetchPlanQuestions',
      json: data
    }, function(error, response, body) {
      if (error) {
        log.error('Error in fetchPlanQuestions');
        log.error(error);
        return reject({
          success: false,
          msg: 'Something went wrong',
          data: []
        });
      } else {
        return resolve(body);
      }
    })
  })  
}
QUES ASKED : 
async function f() {
  let d= 90;
    await new Promise((resolve, reject) => {
      setTimeout(() => {
        console.log("I m resolved");
        resolve("done!")}, 10000)
    });
    console.log("************", d);
  
  }
It was assume await statement won't block it will move to next statement, which was not the case. It will happen in normal
callback function if await is not there. The main aim of await is to block this statement until promise gets resolved.  

