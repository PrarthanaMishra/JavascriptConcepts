Link : http://www.thatjsdude.com/interview/js1.html
Link : https://blog.usejournal.com/commonly-asked-javascript-puzzles-95be856af90a
https://medium.com/@nishantk/essential-javascript-interview-question-answer-8421cbe2eff8
Link : https://github.com/ganqqwerty/123-Essential-JavaScript-Interview-Questions/blob/master/README.md
Link :  https://www.codementor.io/nihantanu/21-essential-javascript-tech-interview-practice-questions-answers-du107p62z
https://medium.com/front-end-weekly/tic-tac-toe-javascript-game-b0cd6e98edd9

1. Write a JavaScript program to display the current day and time in the following format.
    Sample Output : Today is : Tuesday. 
    Current time is : 10 PM : 30 : 38

Ans : let date = new Date();
  console.log('Today is : ' + date.getDay());
  console.log('Current time is :' + new Date('hh:mm a'));

2. Verify a prime number?
  function isPrime (input) {
    let divisor = 2;
    while(divisor <= input) {
      if(input % divisor === 0) {
        return false;
      } else {
        return true;
      }
      divisor++;
    }
    return false;
  }
3.  How could you find all prime factors of a number?
  function findPrimeFactors (input) {
    let divisor = 2;
    let primeFactors = [];
    while( divisor <= input) {
      if (input % divisor === 0) {
        primeFactors.push(divisor);
        input = input/divisor;
      } 
      divisor++;
    }
    return primeFactors;
  }
4. Fibonacci of input
  function fibonacci (input) {
    if(input === 0 || input === 1) {
      return input;
    } else {
      return fibonacci(input-1) + fibonacci(input-2);
    }
  }
5. Greatest Common Divisor
  function GCD (n1, n2) {
    let dividened, divisor ;
    if(n1 >  n2) {
      if (n1 % n2 === 0) {
        return n2;
      } else {
        dividened = n2;
        divisor = n1 % n2;
        return GCD (dividened, divisor);
      }
    }
  }
6. function removeDuplicate (arr) {
 let outArr = [];
 for(let i= 0 ; i<= arr.length; i++) {
   if(outArr.indexOf(arr[i]) < 0) {
     outArr.push(arr[i]);
   }
 }
 return outArr;
}
7. function swap(n1, n2) {
  n1 = n1 + n2;
  n2 = n1 - n2;
  n1 = n1- n2;
  return n1, n2;
}
Can you make reverse function as string extension?
String.prototype.reverse = function (){
  if(!this || this.length <2) return this;
  
  return this.split('').reverse().join('');
}

> 'abc'.reverse();
  = 'cba'
8. Check palindrome
  function checkPalindrome (input) {
    let length = input.length;
    for(let i=0; i<= length/2; i++) {
      if (input[i] !== input[length-1-i]) {
        return false;
      } 
    }
    return true;
  }
9. Random between 5 to 7
  function rand5() {
    return 1 + Math.random() * 4;
  }
10. Find missing number from array number 1 to 100
  function missingNumber (arr) {
    let n= arr.length + 1;
    let sum = 0, expectedSum = 0;
    let difference;
    suexpectedSumm = n * (n-1)/2;
    for (let i = 0; i< arr.length; i++) {
      sum = sum + arr[i];
    }
    difference = expectedSum - sum;
    return difference;
  }
11. sum of two number will give a sum given

function sumFinder(arr, sum) {
  var differ = {}, 
      len = arr.length,
      substract;
  
  for(var i =0; i<len; i++) {
     substract = sum - arr[i];

     if(differ[substract])
       return true;       
     else
       differ[arr[i]] = true;
  }
  return true;
12. Largest sum
    function largestSum (arr) {
      let max = arr[0];
      let 2ndMax = arr[1];
      if (max < 2ndMax) {
          max = arr[1];
          2ndMax = arr[0];
      } 
      for(let i = 2; i<arr.length; i++) {
        if(arr[i] > max) {
          2ndMax = max;
          max = arr[i];
        } else if( arr[i] > 2ndMax ) {
          2ndMax = arr[i]
        }
      }
      return max + 2ndMax;
    }
13. Count Total number of zeros from 1 upto n?
    function countZero (n) {
      let count = 0;
      while (n > 10) {
        count = count + Math.floor(n/10);
        n = n/10;
      }
      return count;
    }
14.  How can you match substring of a string?
    function matchStringWithSubString () {

    }
    
15. How would you create all permutation of a string?
    function permutationString (str) {
      let arr = str.slice('');
      let arrcopy = arr;
       for(let i = 0; i< arr.length; i++) { 
      }
    }
16.  Write a mul function which behaves like below mul(1)(1)(1)(1)(1)(); return 5 
      function mul (x) {
        return function f (y) {
          if (typeof y  !== "undefined" ) {
            x = x + y;
            return f;
          }
          return x;
        }
      }
17. (delete operator on local variable) - This is called currying
      // // What is the output 
      var output = (function(x) {
        delete x;
        return x;
      })(0);
18. var output = (function(x) {
        delete x;
        return x;
      })(0);
      delete is only effective on an object's properties. It has no effect on variable or function names. 

19. // What is the Output 
      var Employee = {
          company: 'xyz'
      }
      var emp1 = Object.create(Employee);
      delete emp1.company
      console.log(emp1.company); 

  Object.create() is a Javascript function which takes 2 arguments and returns a new object. The first argument is an object which will be the prototype of the newly created object. The second argument is an object which will be the properties of the newly created object.
  Employee.company or we can also delete from emp1 object using __proto__ property delete emp1.__proto__.company.

20. // What is the Output 
      var bar = true;
      console.log(bar + 0); // true0
      console.log(bar + "xyz"); truexyz
      console.log(bar + true); truetrue
      console.log(bar + false); truefalse
      Guidelines
      Number + Number -> Addition
      - Boolean + Number -> Addition
      - Number + String -> Concatenation
      - String + Boolean -> Concatenation
      - String + String -> Concatenation
    

21. How to check if an variable is array or not
      function isArray (arr) {
        Array.isArray(arr);
      }
22. var trees = ["xyz", "xxxx", "test", "ryan", "apple"];
    delete trees[3];
    console.log(trees.length);
23. var z = 1, y = z = typeof y;
    console.log(y);
    Right to left associative
24. var foo = function bar() { return 12; };
      typeof bar();
25. What is the difference between declaring a function in below format?
      var foo = function() {
        // Some code
      };
      function bar() {
        // Some code
      };
      The main difference is function foo is defined at run-time whereas function bar is defined at parse time. For understanding It in better way let see below code :
26. what is function hoisting in JavaScript?
      ans : foo(); // Here foo is still undefined
            var foo = function foo() {
              return 12;
            };
            var foo = undefined;
            foo(); // Here foo is undefined
            foo = function foo() {
              // Some code stuff
            }
27. What is the instanceof operator in JavaScript? what would be the output of the following code?
    ans : function foo() {
          return foo;
        }
        new foo() instanceof foo;
        instanceof operator checks the current object and return true if the object is of the specified type.
28. bind and call
    let obj = {
      "name" : "Prarthana",
      getMsg = function(b) {
        console.log(b);
      }
    }
    let getName = function(name) {
      console.log("name" + this.name);
    }
    var bound = getName.bind(obj);
29. Difference between this in normal function in JavaScript and arrow functions
    Normal function - The context in which function is called. 
    Arrow function - The context in which function is defined.
    The takeaway: Function expressions are best for object methods. Arrow functions are best for callbacks or methods like map, reduce, or forEach.
    - Arrow functions are incapable for binding.
    - call apply bind doesn't have any effect on them.
  -  But arrow functions do not have a prototype property and they cannot be used with new.
            