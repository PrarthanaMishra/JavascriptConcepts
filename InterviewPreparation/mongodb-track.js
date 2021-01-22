Practise question link : https://www.w3resource.com/mongodb-exercises/#PracticeOnline
26th june, 2019
Question:-
resturant collections
{
  "address": {
     "building": "1007",
     "coord": [ -73.856077, 40.848447 ],
     "street": "Morris Park Ave",
     "zipcode": "10462"
  },
  "borough": "Bronx",
  "cuisine": "Bakery",
  "grades": [
     { "date": { "$date": 1393804800000 }, "grade": "A", "score": 2 },
     { "date": { "$date": 1378857600000 }, "grade": "A", "score": 6 },
     { "date": { "$date": 1358985600000 }, "grade": "A", "score": 10 },
     { "date": { "$date": 1322006400000 }, "grade": "A", "score": 9 },
     { "date": { "$date": 1299715200000 }, "grade": "B", "score": 14 }
  ],
  "name": "Morris Park Bake Shop",
  "restaurant_id": "30075445"
}
The above json is mongo db json structure
1. Write a MongoDB query to display all the documents in the collection restaurants?
    Ans : db.restaurants.find();

2. Write a MongoDB query to display the fields restaurant_id, name, borough and cuisine for all the documents in the collection restaurant.
  Ans : db.resturants.find({}, {restraunt_id : true, name : true, borough : true, cuisine : true});

3. Write a MongoDB query to display the fields restaurant_id, name, borough and cuisine, but exclude the field _id for all the documents in the collection restaurant
  Ans : db.restaurants.find({}, {restraunt_id : true, name : true, borough : true, cusisine : true}, {field_id : false});

4. Write a MongoDB query to display the fields restaurant_id, name, borough and zip code, but exclude the field _id for all the documents in the collection restaurant
  Ans : db.restaurants.find({}, {restaurant_id : true, name : true, borough : true, address.zipcode : true},  {field_id : 0});

5. Write a MongoDB query to display all the restaurant which is in the borough Bronx.
  Ans : db.resturants.find({"borough" : "Brounx"});

6. Write a MongoDB query to display the first 5 restaurant which is in the borough Bronx.?
  Ans : db.restaurants.find({"borough" : "Bronux"}).limit(5);

7. Write a MongoDB query to display the next 5 restaurants after skipping first 5 which are in the borough Bronx
  Ans : db.restaurants.find({"borough" : "Bronux"}).skip(5).limit(5);

8. Write a MongoDB query to find the restaurants who achieved a score more than 90
  Ans : db.restaurants.find({grades :{$elemMatch : { score : {$gt : 90}} });

9. Write a MongoDB query to find the restaurants that achieved a score, more than 80 but less than 100
  Ans : db.resturants.find({grades : $elemMatch : {score : {$gt : 80 , $lt : 100}}})

10. Write a MongoDB query to find the restaurants which locate in latitude value less than -95.754168
  Ans : db.restaurants.find({address.coord : {$lt : -95.754168}});

11. Write a MongoDB query to find the restaurants that do not prepare any cuisine of 'American' and their grade score more than 70 and latitude less than -65.754168.
  Ans :
  db.restaurants.find({
    $and : [
      {
        cuisine : {$ne : 'American'},
        grades :{ $elemMatch : {$gt : 70} },
        address.cord : {$lt : -65.754168}
      }
    ]
  })

12. Write a MongoDB query to find the restaurants which do not prepare any cuisine of 'American ' and achieved a grade point 'A' not belongs to the borough Brooklyn. The document must be displayed according to the cuisine in descending order.
  Ans : db.resturants.find({
    cuisine : { $ne : 'American'},
    grades : {
      $elemMatch : {grade : "A" 
    },
    borough : {$ne : 'Brooklyn'}
  }).sort({cusisine : -1});

13.  Write a MongoDB query to find the restaurant Id, name, borough and cuisine for those restaurants which contain 'Wil' as first three letters for its name.
 Ans : db.restaurants.find({name : /^WIL/}, { 'restaurant_id' : true, 'name' : true, 'borough' : 1, 'cuisine' : 1 });

14. Write a MongoDB query to find the restaurant Id, name, borough and cuisine for those restaurants which contain 'ces' as last three letters for its name.
  Ans : db.restaurants.find({name : /ces$/}, {'restaurant_id' : 1, name : 1, borough : 1, cuisine : 1})

15. Write a MongoDB query to find the restaurant Id, name, borough and cuisine for those restaurants which contain 'Reg' as three letters somewhere in its name
  Ans : db.restaurants.find({name :/.*Reg.*/ }, {'restaurant_id' : 1, name : 1, borough : 1, cuisine : 1})

16.  Write a MongoDB query to find the restaurants which belong to the borough Bronx and prepared either American or Chinese dish.
  Ans : db.restaurants.find({borough : "Bronx",
                    $or : [
                      {cuisine : "American"},
                      {cusisine : "Chinese"}
                    ]
                  });
17.  Write a MongoDB query to find the restaurant Id, name, borough and cuisine for those restaurants which belong to the borough Staten Island or Queens or Bronxor Brooklyn.
    Ans : db.restaurants.find({
      borough : { $in : [Staten Island, , Queens, Bronxor Brooklyn]}
    },
      { restaurant_id : 1, name : 1, borough : 1,  cuisine : 1 });

18. Write a MongoDB query to find the restaurant Id, name, borough and cuisine for those restaurants which achieved a score which is not more than 10
    db.restaurants.find({
      grades : {
        $elemMatch : {
          score : { $lt : 10}
        }
      }
    }, {
      restaurant_id : 1, name : 1, borough : 1, cuisine : 1});
    })
19. Write a MongoDB query to find the restaurant Id, name, borough and cuisine for those restaurants which prepared dish except 'American' and 'Chinees' or restaurant's name begins with letter 'Wil'
    db.restaurants.find(
      {
      $or: [
        name : /^WIL/,
        {
          $and : [
            { cuisine : {$ne : "American"}},
            {cuisine : {$ne : "Chineese"}}
          ]
        }
      ]
    }, {
      restaurant_id : 1, name : 1, borough : 1, cuisine : 1
    })

20. Write a MongoDB query to find the restaurant Id, name, and grades for those restaurants which achieved a grade of "A" and scored 11 on an ISODate "2014-08-11T00:00:00Z" among many of survey dates.
   ans : 
    db.restaurants.find({
      grades.grade : 'A',
      grades.score : 11,
      grades.date : ISODate("2014-08-11T00:00:00Z")

    }, {restaurant_id : 1, name : 1, grades : 1})

21. Write a MongoDB query to find the restaurant Id, name, address and geographical location for those restaurants where 2nd element of coord array contains a value which is more than 42 and upto 52.
    ans : db.restaurants.find({
      address.coord.1 : {$gt : 42, $lte : 52}
    },
    {restaurant_id :  1, name : 1, address :1, geographical : 1})

22. Write a MongoDB query to arrange the name of the restaurants in ascending order along with all the columns.
    ans : db.restaurants.find().sort({"name" : -1});

23. Write a MongoDB query to arranged the name of the cuisine in ascending order and for that same cuisine borough should be in descending order
    ans : db.restaurants.find().sort({cuisine : 1 , borough : -1})

24. Write a MongoDB query to know whether all the addresses contains the street or not.
    ans : db.restaurants.find({ 
      address.street : {
        $exists : true
      }
    });
25. Write a MongoDB query which will select all documents in the restaurants collection where the coord field value is Double
    ans : db.restaurants.find(
      {"address.coord" : 
         {$type : 1}
      }
     );
26. Write a MongoDB query which will select the restaurant Id, name and grades for those restaurants which returns 0 as a remainder after dividing the score by 7
     ans : db.restaurants.find({
      grades.score : {
        $mod : [7, 0]
      }
     },{restaurant_id : 1, name :1, grades : 1})

27. Write a MongoDB query to find the restaurant name, borough, longitude and attitude and cuisine for those restaurants which contains 'mon' as three letters somewhere in its name.
     ans : db.restaurants.find({
       name : {
         $regex : "mon.*", $options : "i"
       }
     },
    {
      name: 1, borough :1, address.coord : 1, cuisine : 1
    })

28. Write a MongoDB query to find the restaurant name, borough, longitude and latitude and cuisine for those restaurants which contain 'Mad' as first three letters of its name
    ans : db.restaurants.find({
        name : {
         $regex :  /^mad/i
        }
    },{restaurant_id : 1, name : 1, address.coord : 1, cuisine : 1})