
INSERT INTO `sms_templates` (`template_name`, `sms_content`,`status`)
 VALUES ("CampaignCoupon", "#CelebrateYourWay with HomeTown. Here's the GV of Rs.500 valid on Homeware purchase - #COUPONCODE. Valid till 14th Nov. Visit our nearest store now. TnC - http://bit.ly/HT-TnC","Active");
 
update sms_templates set sms_content = "#CelebrateYourWay with HomeTown. Here's the GV of Rs.500 valid on Homeware purchase - #COUPONCODE. Valid till 14th Nov. Visit our nearest store now. TnC - http://bit.ly/HT-TnC" where id_sms_templates=21;

ALTER TABLE customer_cart DROP COLUMN salesforce;
 
SHOW CREATE TABLE sales_force_product_interest;
    
ALTER TABLE customer_cart ADD salesforce TINYINT(4) DEFAULT 0  NOT NULL AFTER shipping_charges;


ALTER TABLE customer_cart ADD fk_sales_force_customer_opportunity INT(11) DEFAULT 0 AFTER shipping_charges;

ALTER TABLE customer_cart DROP COLUMN salesforce;

ALTER TABLE customer ADD customer_city VARCHAR(255) AFTER update_email;

ALTER TABLE customer DROP COLUMN customer_city;




