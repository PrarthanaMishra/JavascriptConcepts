Save for later feature
Add wishlist api and getwish list api

Add saveForLater column in an enum(0, 1, 2)
0 -wishlist 
1- save for later
2- both 

Add wish list api comes with saveForLater 1 or 0 
getwishList api comes with saveForLater 1 or 0 

Incase of quantity if quantity comes then quantity quantity/1 in add quantity

First work:-
Add an enum in wishlist table 
Will adding save for latter first we need to remove cart from cart table 
This both can happen parallely 
If getting from wishlist if we will get the api we will make that cart or product boolean value 2 

1. Add enum in customer_wishlist table 
    ALTER TABLE customer_wishlist ADD save_for_later TINYINT(4) DEFAULT 0  NOT NULL;
2. Add records for saveForLater =1 in add wishlist api
3. Add to quantity can have quantity we can send that but what about getWishlist api. 
    How we would give quanitty on that.
4. In meta - group attribures quantity field is going there but what's its usecase.
5. GetWishlist api testing done 
    For save_for_later 0 it should show 0 and 2 
    For save_for_later 1 it should show 1 and 2
6. while fetching the wishlist we need to add condition for save for later = 0
7. If something on save for later has option to add to the cart it should be removed from saveforlatter.
8. Get wishlist call is working fine.
9. Check once more post call. giving error data not found in cache.
    ALTER TABLE customer_wishlist ADD quantity smallint(6);

2nd october- changes made in already existing api - buylater
1. customer_buylater table
2. response format is same as wishlist
3. params means value in url and queryparams in get and api in body
    DB migrations:-

    CREATE TABLE `customer_buylater` (
  `id_customer_buylater` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_customer` int(10) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `configurable_sku` varchar(255) NOT NULL,
  `simple_sku` varchar(255) DEFAULT NULL,
  `qty` smallint(6) NOT NULL,
  `shipping_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_customer_buylater`),
  KEY `fk_customer` (`fk_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 


tail -1f /var/log/hometowncore/error.log | while read LOGLINE; do echo $LOGLINE | python -mjson.tool ; done








