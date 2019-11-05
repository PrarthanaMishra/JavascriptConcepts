https://www.glassdoor.co.in/Interview/Accenture-NodeJs-Interview-Questions-EI_IE4138.0,9_KO10,16.htm#InterviewReview_25778724s

1. Difference between require and import?  
 Ans : Require is more of dynamic analysis and import is more of static analysis
      Require Throws error at runtime and Import throws error while parsing
      Require is Nonlexical and Import is Lexical
      Requires to stay where they have put the file and imports get sorted to the top of the file.
      Import is always run at the very beginning of the file and can’t be run conditionally. On the other hand require can be used inline, conditionally.

2. How does nodejs loads require?he wanted to know about the nodejs algorithm follows at the loading time
ans : https://www.bennadel.com/blog/2169-where-does-node-js-and-require-look-for-modules.htm

3. Some basic question of error handling in nodejs  

let events = require('event');
let eventEmitter = new events.EventEmitter();

let listerner1 = function listener1 () {
  console.log("I am listener 1");
}

let listerner2 = function listener2 () {
  console.log("I am listener 2");
}
eventEmitter.addEventListener('connection', listerner1);
eventEmitter.on('connection', listener1');

eventEmitter.emit('connection');
