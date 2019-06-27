
25th June, 2019
Solving questions from the link:-
  - https://www.toptal.com/javascript/interview-questions 
1. if ( (bar !== null) &&
     (typeof(bar) === "object") &&
     (typeof(bar) === "function")
)
!bar = will be true if not null and undefined so explicit check
2. var a=b=3;
  this is shorthand for b=3; var a = b;
  use strict will give reference error b is not defined

3. var myObject = {
  foo: "bar",
  func: function() {
      var self = this;
      console.log("outer func:  this.foo = " + this.foo); //bar
      console.log("outer func:  self.foo = " + self.foo); //bar
      (function() {
          console.log("inner func:  this.foo = " + this.foo); // undefined
          console.log("inner func:  self.foo = " + self.foo); //bar
      }());
  }
};
myObject.func();
Ans is : this will refer the scope in which it is called. so func have scope of myObject so foo is accessible in function.
  since the closure function is calling there in outer function scope.so it doen't know about foo.But self variable is 
  accessible from there so it will print bar

4. Why to wrap whole code in a function? like closure
  ans : it creates private namespace, avoids name clashes in different javascript modules

5. significance and benefits of using 'use strict' at the top of the file
  - stricter parsing and error handling
  - errors would otherwise ignored will now throw error
    - undeclared varibale not allowed
    - referencing this to null and undefined will throw error. otherwise it will coerced to global
    - duplicate named arguments in function throws error
      function foo (val, val) {}
    - in  non strict mode  eval() statement are not created in the containing scope (they are created in the containing scope in non-strict mode, which can also be a common source of problems)
    - throws error on delete  operation to non-configurable object .Non configurable object are made with the following
      library function
      Object.defineProperty(obj, 'foo', {value: 2, configurable: false});
      Object.freeze([0, 1, 2]);

6. if return {} - will return object 
  return
  {} - undefined
7. What's NAN what is it's type? And how to find a number is NaN
    typeof(NaN) is Number ("Not a number")
    isNaN(2) // false
    but isNaN() // true   
  NAN === NAN // false
  More reliable way to check Number.isNaN();
  isNaN("str") ///true but isNaN("123") // is false because "123" on conversion to number becomes number
  typeof() !== Nan

8. 0.1 + 0.2 == 0.3 false 0.3 == 0.3 true
  because 0.1 + 0.2 = 0.30000000000000004
  Numbers in JavaScript are all treated with floating point precision,

  function areTheNumbersAlmostEqual(num1, num2) {
    return Math.abs( num1 - num2 ) < Number.EPSILON;
  }
  console.log(areTheNumbersAlmostEqual(0.1 + 0.2, 0.3));
9. Number.isInteger() - can find whether the number is integer or not
    before es6 function isInteger(x) {
      return Math.round(x) === x;
    }
    function isInteger(x) { return (x ^ 0) === x; } 
    incorrect solution is 
    function isInteger(x) { return parseInt(x, 10) === x; }
    Reason : While this parseInt-based approach will work well for many values of x, once x becomes quite large, it will fail to work properly. The problem is that parseInt() coerces its first parameter to a string before parsing digits. Therefore, once the number becomes sufficiently large, its string representation will be presented in exponential form (e.g., 1e+21). Accordingly, parseInt() will then try to parse 1e+21, but will stop parsing when it reaches the e character and will therefore return a value of 1. Observe:

    > String(1000000000000000000000)
    '1e+21'
    > parseInt(1000000000000000000000, 10)
    1
    > parseInt(1000000000000000000000, 10) === 1000000000000000000000
    false
10. When a value of zero is passed as the second argument to setTimeout(), it attempts to execute the specified function “as soon as possible”. Specifically, execution of the function is placed on the event queue to occur on the next timer tick. Note, though, that this is not immediate; the function is not executed until the next tick. That’s why in the above example, the call to console.log(4) occurs before the call to console.log(3) (since the call to console.log(3) is invoked via setTimeout, so it is slightly delayed).
11. Write a function that returns boolean if a string is a palindrome
    function isPalindrome(str) {
      str = str.replace(/\W/g, '').toLowerCase(); // has to learn regular expression
      let rev = str.split('').reverse().join('');
      return str === rev;
    }
12. write sum method which will invoke properly for the following
  console.log(sum(2,3));   // Outputs 5
  console.log(sum(2)(3));  // Outputs 5
    function sum (x) {
      if(arguments.length === 2) {
        return arguments[0] + arguments[1]
      } else {
        return function (y) {
          return x + y;
        }
      }
    }

  In JavaScript, functions provide access to an arguments object which provides access to the actual arguments passed to a function. This enables us to use the length property to determine at runtime the number of arguments passed to the function.
If two arguments are passed, we simply add them together and return.
Otherwise, we assume it was called in the form sum(2)(3), so we return an anonymous function that adds together the argument passed to sum() (in this case 2) and the argument passed to the anonymous function (in this case 3).
    other solution 
    function (x,y) {
      if(y !== undefined) { // if y is not there it will be undefined
        return x+y;
      } else {
        function(y) {
          return x + y;
        }
      }
    }
13. slice is bit confusing to me so here arethe examples 
    This computed label gets all elements of an array starting at the second element.
    var a = new Array("one", "two", "three", "four");
    a.slice(1) // [two, three, four]Copy

    (2) This computed label gets the second and third elements of an array.
    var a = new Array("one", "two", "three", "four");
    a.slice(1,3) // [two, three]Copy

    (3) This computed label gets the last element of an array.
    var a = new Array("one", "two", "three", "four");
    a.slice(-1) // [four]Copy

    (4) This computed label gets all elements of an array except the last.
    var a = new Array("one", "two", "three", "four");
    a.slice(0, -1) // [one, two, three]

    output of the following code:- (imp concepts please checkit out once more)
    ar arr1 = "john".split('');
    var arr2 = arr1.reverse();
    var arr3 = "jones".split('');
    arr2.push(arr3);
    console.log("array 1: length=" + arr1.length + " last=" + arr1.slice(-1));
    console.log("array 2: length=" + arr2.length + " last=" + arr2.slice(-1));

    The logged output will be:

      "array 1: length=5 last=j,o,n,e,s"
      "array 2: length=5 last=j,o,n,e,s"
      arr1 and arr2 are the same (i.e. ['n','h','o','j', ['j','o','n','e','s'] ]) after the above code is executed for the following reasons:

      Calling an array object’s reverse() method doesn’t only return the array in reverse order, it also reverses the order of the array itself (i.e., in this case, arr1).

      The reverse() method returns a reference to the array itself (i.e., in this case, arr1). As a result, arr2 is simply a reference to (rather than a copy of) arr1. Therefore, when anything is done to arr2 (i.e., when we invoke arr2.push(arr3);), arr1 will be affected as well since arr1 and arr2 are simply references to the same object.

      And a couple of side points here that can sometimes trip someone up in answering this question:

      Passing an array to the push() method of another array pushes that entire array as a single element onto the end of the array. As a result, the statement arr2.push(arr3); adds arr3 in its entirety as a single element to the end of arr2 (i.e., it does not concatenate the two arrays, that’s what the concat() method is for).

      Like Python, JavaScript honors negative subscripts in calls to array methods like slice() as a way of referencing elements at the end of the array; e.g., a subscript of -1 indicates the last element in the array, and so on.

14. console.log(1 +  "2" + "2");
    console.log(1 +  +"2" + "2");
    console.log(1 +  -"1" + "2");
    console.log(+"1" +  "1" + "2");
    console.log( "A" - "B" + "2");
    console.log( "A" - "B" + 2);

    The above code will output the following to the console:

    "122"
    "32"
    "02"
    "112"
    "NaN2"
    NaN
    Here’s why…

    The fundamental issue here is that JavaScript (ECMAScript) is a loosely typed language and it performs automatic type conversion on values to accommodate the operation being performed. Let’s see how this plays out with each of the above examples.

    Example 1: 1 + "2" + "2" Outputs: "122" Explanation: The first operation to be performed in 1 + "2". Since one of the operands ("2") is a string, JavaScript assumes it needs to perform string concatenation and therefore converts the type of 1 to "1", 1 + "2" yields "12". Then, "12" + "2" yields "122".

    Example 2: 1 + +"2" + "2" Outputs: "32" Explanation: Based on order of operations, the first operation to be performed is +"2" (the extra + before the first "2" is treated as a unary operator). Thus, JavaScript converts the type of "2" to numeric and then applies the unary + sign to it (i.e., treats it as a positive number). As a result, the next operation is now 1 + 2 which of course yields 3. But then, we have an operation between a number and a string (i.e., 3 and "2"), so once again JavaScript converts the type of the numeric value to a string and performs string concatenation, yielding "32".

    Example 3: 1 + -"1" + "2" Outputs: "02" Explanation: The explanation here is identical to the prior example, except the unary operator is - rather than +. So "1" becomes 1, which then becomes -1 when the - is applied, which is then added to 1 yielding 0, which is then converted to a string and concatenated with the final "2" operand, yielding "02".

    Example 4: +"1" + "1" + "2" Outputs: "112" Explanation: Although the first "1" operand is typecast to a numeric value based on the unary + operator that precedes it, it is then immediately converted back to a string when it is concatenated with the second "1" operand, which is then concatenated with the final "2" operand, yielding the string "112".

    Example 5: "A" - "B" + "2" Outputs: "NaN2" Explanation: Since the - operator can not be applied to strings, and since neither "A" nor "B" can be converted to numeric values, "A" - "B" yields NaN which is then concatenated with the string "2" to yield “NaN2”.

    Example 6: "A" - "B" + 2 Outputs: NaN Explanation: As exlained in the previous example, "A" - "B" yields NaN. But any operator applied to NaN with any other numeric operand will still yield NaN.

15. The following recursive code will cause a stack overflow if the array list is too large. How can you fix this and still retain the recursive pattern?

    var list = readHugeList();

    var nextListItem = function() {
        var item = list.pop();

        if (item) {
            // process the list item...
            nextListItem();
        }
    };

    The potential stack overflow can be avoided by modifying the nextListItem function as follows:

  var list = readHugeList();

  var nextListItem = function() {
      var item = list.pop();

      if (item) {
          // process the list item...
          setTimeout( nextListItem, 0);
      }
  };
  The stack overflow is eliminated because the event loop handles the recursion, not the call stack. When nextListItem runs, if item is not null, the timeout function (nextListItem) is pushed to the event queue and the function exits, thereby leaving the call stack clear. When the event queue runs its timed-out event, the next item is processed and a timer is set to again invoke nextListItem. Accordingly, the method is processed from start to finish without a direct recursive call, so the call stack remains clear, regardless of the number of iterations.

  16. What will be the output of the following code:

    for (var i = 0; i < 5; i++) {
      setTimeout(function() { console.log(i); }, i * 1000 );
    }
    Explain your answer. How could the use of closures help here?
    
    Closures can be used to prevent this problem by creating a unique scope for each iteration, storing each unique value of the variable within its scope, as follows:

    for(var i =0; i<5; i++) {
      (function(x) {
        setTimeout(function () { console.log(i)}, i*1000);
      })(i)
    }
    In ES6 we can simply do by changing var to let
    for (let i = 0; i < 5; i++) {
      setTimeout(function() { console.log(i); }, i * 1000 );
    }
17. console.log("0 || 1 = "+(0 || 1));
    console.log("1 || 2 = "+(1 || 2));
    console.log("0 && 1 = "+(0 && 1));
    console.log("1 && 2 = "+(1 && 2));
    The code will output the following four lines:

    0 || 1 = 1
    1 || 2 = 1
    0 && 1 = 0
    1 && 2 = 2
    In JavaScript, both || and && are logical operators that return the first fully-determined “logical value” when evaluated from left to right.

    The or (||) operator. In an expression of the form X||Y, X is first evaluated and interpreted as a boolean value. If this boolean value is true, then true (1) is returned and Y is not evaluated, since the “or” condition has already been satisfied. If this boolean value is “false”, though, we still don’t know if X||Y is true or false until we evaluate Y, and interpret it as a boolean value as well.

    Accordingly, 0 || 1 evaluates to true (1), as does 1 || 2.

    The and (&&) operator. In an expression of the form X&&Y, X is first evaluated and interpreted as a boolean value. If this boolean value is false, then false (0) is returned and Y is not evaluated, since the “and” condition has already failed. If this boolean value is “true”, though, we still don’t know if X&&Y is true or false until we evaluate Y, and interpret it as a boolean value as well.

    However, the interesting thing with the && operator is that when an expression is evaluated as “true”, then the expression itself is returned. This is fine, since it counts as “true” in logical expressions, but also can be used to return that value when you care to do so. This explains why, somewhat surprisingly, 1 && 2 returns 2 (whereas you might it expect it to return true or 1).

18. Testing your this knowledge in JavaScript: What is the output of the following code?

    var length = 10;
    function fn() {
      console.log(this.length);
    }
    var obj = {
      length: 5,
      method: function(fn) {
        fn(); //here it being called globally no matter it's inside a object
        arguments[0]();
      }
    };
    obj.method(fn, 1); method has been called with obj scope but fn not
    Output:

    10
    2
    Why isn’t it 10 and 5?

    In the first place, as fn is passed as a parameter to the function method, the scope (this) of the function fn is window. var length = 10; is declared at the window level. It also can be accessed as window.length or length or this.length (when this === window.)

    method is bound to Object obj, and obj.method is called with parameters fn and 1. Though method is accepting only one parameter, while invoking it has passed two parameters; the first is a function callback and other is just a number.

    When fn() is called inside method, which was passed the function as a parameter at the global level, this.length will have access to var length = 10 (declared globally) not length = 5 as defined in Object obj.

    Now, we know that we can access any number of arguments in a JavaScript function using the arguments[] array.

    Hence arguments[0]() is nothing but calling fn(). Inside fn now, the scope of this function becomes the arguments array, and logging the length of arguments[] will return 2.

    Hence the output will be as above.
  
    19. What do the following lines output, and why?

        console.log(1 < 2 < 3);
        console.log(3 > 2 > 1);
        The first statement returns true which is as expected.

    The second returns false because of how the engine works regarding operator associativity for < and >. It compares left to right, so 3 > 2 > 1 JavaScript translates to true > 1. true has value 1, so it then compares 1 > 1, which is false.
    
    20. How do you add an element at the begining of an array? How do you add one at the end? (Good question)
        var a = [1, 2, 3];
    
      
    21. What will the following code output and why?
        var b = 1;
        function outer(){
            var b = 2
            function inner(){
                b++;
                var b = 3;
                console.log(b)
            }
            inner();
        }
        outer();
        Output to the console will be “3”.

      There are three closures in the example, each with it’s own var b declaration. When a variable is invoked closures will be checked in order from local to global until an instance is found. Since the inner closure has a b variable of its own, that is what will be output.

      Furthermore, due to hoisting the code in inner will be interpreted as follows:

      function inner () {
          var b; // b is undefined
          b++; // b is NaN
          b = 3; // b is 3
          console.log(b); // output "3"
      }
    22. var x = 21;
        var girl = function () {
            console.log(x);
            var x = 20;
        };
        girl ();
        
 ans:    Neither 21, nor 20, the result is undefined
        It’s because JavaScript initialization is not hoisted.
        (Why doesn’t it show the global value of 21? The reason is that when the function is executed, it checks that there’s a local x variable present but doesn’t yet declare it, so it won’t look for global one.)

      23. 
      (function () {
        try {
            throw new Error();
        } catch (x) {
            var x = 1, y = 2;
            console.log(x);
        }
        console.log(x);
        console.log(y);
    })();
    1
  ans :  undefined
    2
    var statements are hoisted (without their value initialization) to the top of the global or function scope it belongs to, even when it’s inside a with or catch block. 
    However, the error’s identifier is only visible inside the catch block. It is equivalent to:
    (function () {
        var x, y; // outer and hoisted
        try {
            throw new Error();
        } catch (x /* inner */) {
            x = 1; // inner x, not the outer one
            y = 2; // there is only one y, which is in the outer scope
            console.log(x /* inner */);
        }
        console.log(x);
        console.log(y);
    })();
    
    24. How to add element in front of an array.
      var a = [2,3,4];
      a.unshift(1);
      a // [1,2,3,4]

    25. How to add at the end of the array
        a.push(5);
    26. 