Closures:
It  is an abstraction mechanism that separates concerns cleanily.
It is a self invoking function
Inner function knows it’s outer environment from where it’s called.
If we want variable to get initalized once and not  get incremented by outer environment then we can use closure.
let updateClickCount = (function ( ) {
  let counter = 0;
return  function () {
		++counter;
}
});
// 30th June 2018
What is ‘use strict’ meaning?
This makes debugging easier. Code that allowed some errors unnoticed will now give error.
Why ‘use string’ is string?
It is string so that all browser support. If it has been a keyword older browser will throw error as it won’t understand it but in case of string it will ignore it. On the other hand
