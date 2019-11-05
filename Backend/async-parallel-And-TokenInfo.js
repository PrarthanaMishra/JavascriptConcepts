It’s about something that I understood or got to learn in work : -
29th september, 2018
Async.parallel - It executes functions in an array parallely but the final callback called once all the functions has called it’s callback.  But any of the function inside the array throws error
Ie without null in callback. It will call main callback and stop right there.

11th october, 2018
Session based token doesn’t have persistent storage of session id while token based authentication have persistent storage of token id. So even if we restart system 
session won’t destroy while in case of session it will destroy.
During token based authentication we have to hit database for every request which can do so many database call. But to store session information in local memory for server is not a feasible
Solution as it will use server memory in heavy traffic which will make server inefficient.
Conclusion : Doing a database hit is more efficient than using server storage






