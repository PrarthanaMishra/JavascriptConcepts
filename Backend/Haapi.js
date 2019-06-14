Joi Validation

GUID - Global Unique Identifier


const schema = Joi.date().timestamp('javascript'); // also, for javascript timestamp (milliseconds)
const schema = Joi.date().timestamp('unix'); // for unix timestamp (seconds)
Difference between javascript timestamp and unix time stamp
Link : https://github.com/hapijs/joi/blob/v8.0.5/API.md#datetimestamptype

People coming from PHP and Java have many validation libraries available. But what about JavaScript? There are some options, but none seems more interesting than Joi.
Joi is maintained by Hapi.js project
 Even though hapi.js is a web framework by itself, Joi is independent and can be used in any type of node project.
What makes it even more interesting is that, besides user validation, it can be used to define database schemas as well.
vogels is a DynamoDB data mapper for node.js.
Disable Joi number() convert
schema.validate(value, { convert: false })
Create default fields
updatedAt: Joi.date().default(Date.now)
Show all validation errors
Usually Joi validation dies on the first error, returning on it's message which field failed. This is an issue when an input have many fields with problems. The user will have to do many requests to discover all problematic fields. To fix this issue, the following option can be added on the function call:
const result = schema.validate(value, { abortEarly: false })
With this config, if an error occurs, the result.error will return all validation failures.
//Nice link
Link : http://vawks.com/blog/2014/03/22/the-joi-of-validation/
 Built-in Joi conditional validations

const schema = Joi.object().keys({




a: Joi.any()


   .valid('x')


   .when('b', {


     is: 5,


     then: Joi.valid('y'),


     otherwise: Joi.valid('z')


   }),


 b: Joi.any()
})




In this case, the field a accepts the value x, y, z. But, to accept y as a value, the field b have to be equals 5, otherwise only x and z will be accepted.
Because we used .required() we have to specify the username key, and because we used .with('birthyear') we have also to include the birthyear key whenever we have a username.
The .with constraint is more typically used with non-required values. In this example, it probably makes more sense to use .required() on both username and birthyear, but we’re using a simplified version of the example in the Joi README.


Schema objects also have a .validate() method, and like Joi.validate() it returns either an Error object or null. But instead of taking a schema and a config, it only takes a single value.

If you’re validating an object that has keys which refer to simple data as well as keys that refer to functions, you have two choices.
First, you can explicitly set up rules that those keys should be functions:
var myObject = {
  count: 0,
  increment: function () {
    this.count++
  }
}

var schema = {
  count: Joi.number().integer(),
  increment: Joi.func()
}

Joi.validate(myObject, schema);

Second, you could set either the allowUnknown or the skipFunctions option to true. Using skipFunctions will allow unknown keys only if they’re functions.

var myObject = {
  count: 0,
  increment: function () {
    this.count++
  }
}

var schema = {
  count: Joi.number().integer()
}

Joi.validate(myObject, schema, {skipFunctions: true});

You can call the .allow() method to whitelist a value or array of values, regardless of the other restrictions on the schema. The .valid() method is similar, but it makes the resulting schema match only the values passed into a call to .valid().

Joi.number().allow('a').validate('a');         // null
Joi.number().allow('a').validate(3);           // null

Joi.number().valid('a').validate('a');         // null
Joi.number().valid('a').validate(3);           // Error
Joi.number().valid(3).valid('a').validate(3);  //
We can also use the .invalid() method to blacklist a value or an array of values. If you have multiple calls to valid and invalid, they combine and the later calls win out.
1
2
3
4
5
6
7
8
9
10


Joi.number().invalid(3).validate(3);           // Error

var schema = Joi.string().
                 valid(['a', 'b', 'c']).
                 invalid(['b', 'c']).
                 valid('c');

schema.validate('a');                          // null
schema.validate('b');                          // Error
schema.validate('c');           
We’ve already seen the .required() method which means a key can’t be undefined. We can establish relationships between keys by using the .with(), .without(), .or() and .xor() methods.
 
var morning = {
  orangeJuice: Joi.any().xor('toothPaste'),
  toothPaste:  Joi.any(),
  omelette:    Joi.any().with('breakEggs').without('vegan'),
  breakEggs:   Joi.any(),
  vegan:       Joi.any()
}

Joi.validate({
  orangeJuice: true,
  toothPaste: true
}, morning);
// Error

Joi.validate({
  orangeJuice: true
}, morning);
// null

Joi.validate({
  orangeJuice: true,
  omelette: true
}, morning);
// Error
Joi.object() is also very useful when you need reuse a matcher on different sub-objects:

Joi.validate({
  orangeJuice: true,
  omelette: true,
  breakEggs: true
}, morning);
// null

Joi.validate({
  orangeJuice: true,
  omelette: true,
  breakEggs: true,
  vegan: true
}, morning);
// Error

Joi.object() is also very useful when you need reuse a matcher on different sub-objects:
var nameMatcher = Joi.object({
  firstName: Joi.string().required(),
  lastName: Joi.string().required()
});

var orderSchema = {
  amountCents: Joi.number().integer(),
  billingName: nameMatcher,
  shippingName: nameMatcher
}

Joi.validate({
  amountCents: 10000,
  billingName: {firstName: 'Bob', lastName: 'Dobbs'},
  shippingName: {firstName: 'Sam', lastName: 'Malone'}
}, orderSchema);
// null

Joi.validate({
  amountCents: 10000,
  billingName: {firstName: 'Bob', lastName: 'Dobbs'},
  shippingName: {firstName: 'Madonna', lastName: ''}
}, orderSchema);
// error

Difference between with and required is that with required both are independently required while with means one param cannot occur without other

hour: Joi.number().min(0).max(23).with('minute'),
        minute: Joi.number().min(0).max(59).with('hour')

It provides body, query and URL params validation in Hapi.js



Joi.validate(data to be verified, schema, callback);
Joi.validate({ username: 'abc', birthyear: 1994 }, schema, function (err, value) { });

access_token: [Joi.string(), Joi.number()] - Can be a string or number

https://ciphertrick.com/2017/08/14/request-body-validations-joi-expressjs/

As you know by now, Hapi works on basis of configuration. This means, most Hapi APIs take a configuration object as an input.


// comparision of sail.js and hapi.js
First, how to check a  framework
1. How much it do it yourself
2. Configuration - how easy to configure a framework
3.convention - Is there any convention available to do a task
4. Scaling - how easy to scale an app
5. Testing  - how to test the application
6. Scafffolding - how much developer has to code in comparision to how much generator generates code
7. Connectors - how rich a framework is to connect plugins
8. ORM/ODM - IS there any document mapper exist

Out of this what hapi.js have?
Built in support from input validation, caching, authentication. 
Authentication is done by bcrypt
It doesn’t provide ORM
Get a great amount of control on request handling
Plug-in based architecture- pick up module to scale your application
Configuration-centric:
Rich web server functionality
Good api documentation
Cons:
1.Developer need to find code structure of their own
2. Hapi specific modules such as joi, boom, tv, good are not supported by express
3. End points are created manually
4. Refractoring is manual

Hapi doesn’t buit on top of express.

//Sails
1.sails is built on top of express
2. Sails is rich in scafffolding
3.can create restful api without writing any code
4.It comes with built ORM called waterline
5. It has asset tool called grunt

Pros ;
Provides good code orgnaization and blue prints
Built-in web sockets
Supports various databases
Auto generated code for controllers models and routes
Built-in file uploadlibrary
Modular architecture with hooks and plugins
Cons:
Steep learning curve
Opinated
Sails.js is compatible with express.js middleware



