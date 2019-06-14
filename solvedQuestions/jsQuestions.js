
1. Write a JavaScript program to display the current day and time in the following format.
    Sample Output : Today is : Tuesday. 
    Current time is : 10 PM : 30 : 38

Ans : let date = new Date();
  console.log('Today is : ' + date.getDay());
  console.log('Current time is :' + new Date('hh:mm a'));