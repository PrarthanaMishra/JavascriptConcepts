16th march, 2019
Javascript concepts:-
(Alepo technologies)
https://github.com/getify/You-Dont-Know-JS/tree/master/es6%20%26%20beyond
https://medium.com/the-node-js-collection/what-you-should-know-to-really-understand-the-node-js-event-loop-and-its-metrics-c4907b19da4c

JavaScript defines seven built-in types:
null
undefined
boolean
number
string
object
symbol -- added in ES6!
4th May, 2019
Difference between let and var
console.log(bar); //undefined   
var bar = 3;
console.log(bar1); // reference error
let bar = 3; 
Let doesn’t hoist entire scope of the block they appear in.They won’t exist until they it’s declared. 
First difference is it doesn’t provide variable hoisting eg : above
Second difference let attaches it to a block in which it is defined. Like var in function
{
var a = 1;
}
console.log(a) // prints 1
While 
{
let  a =1;
}
console.log(a) // throws reference error (same as when var used in a function)
3. var a=1; var a=2; // don’t throw error in the same scope
let a = 1; let a =2 // throws error in the same scope 
Difference between let and const
Const are also attached to a block like let only difference is that once the value assigned to it, it cannot change.
How hoisting works:-
var a = 2
Compiler and engine.
Compiler first checks the declaration. In declarations first function declarations get checked and than engine executes the code. Few examples
a = 2;

var a;

console.log( a );
Can be thought as 
var a;

a = 2;

console.log( a ); //2
2nd example
console.log( a );
var a = 2;
Can be converted as:-
var a;
console.log( a ); // undefined

 a = 2;
Only variable declrations come above the code. Rest things remains at the same place.
Functions decalrations are hoisted but expressions are not.
foo(); // not ReferenceError, but TypeError!

var foo = function bar() {
	// ...
};
So engine will see
Var foo; // foo found but it’s not a function
foo();
Function bar() {
}

Function first:-
foo(); // 1

var foo;

function foo() {
	console.log( 1 );
}

foo = function() {
	console.log( 2 );
};
Output is 1// since for foo first function will get hoisted and than variable so it’s 1 and subsequent declarations of var will get ignored.

foo(); // not ReferenceError, but TypeError!

var foo = function bar() {
	// ...
};

foo(); // TypeError
bar(); // ReferenceError

var foo = function bar() {
	// ...
};

Closures :-
function foo() {
	var a = 2;

	function bar() {
		console.log( a ); // 2
	}

	bar();
}

foo(); // this example seems work because of only lexical scoping. Block scope.

The real example that highlights closure is following:-
function foo() {
	var a = 2;

	function bar() {
		console.log( a );
	}

	return bar;
}

var baz = foo();

baz(); // 2 -- Whoa, closure was just observed, man.

After we execute foo(), we assign the value it returned (our inner bar() function) to a variable called baz, and then we actually invoke baz(), which of course is invoking our inner function bar(), just by a different identifier reference.
bar() is executed, for sure. But in this case, it's executed outside of its declared lexical scope.
After foo() executed, normally we would expect that the entirety of the inner scope of foo() would go away, because we know that the Engine employs a Garbage Collector that comes along and frees up memory once it's no longer in use. Since it would appear that the contents of foo() are no longer in use, it would seem natural that they should be considered gone.
 
bar() still has a reference to that scope, and that reference is called closure.



