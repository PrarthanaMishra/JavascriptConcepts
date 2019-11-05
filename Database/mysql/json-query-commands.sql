update vendor_settings set value =  jsonb_set(value::jsonb, '{cmssettings}', '{"email" : {
                                    "parent_email_to_teacher" : {
                                        "cc" : [],
                                        "bcc" : []
                                    },
                                    "feedback_email" : {
                                        "to" : [],
                                        "cc" : [],
                                        "bcc" : []
                                    }
                            },
                            "donation" : ""
                        }' :: jsonb) where vendor_id=1;




SELECT
    jsonb_set(
        '{"name": "Mary",
          "contact":
              {"phone": "555-5555",
               "fax": "111-1111"}}'::jsonb,
        '{contact,phone}',
        '"000-8888"'::jsonb,
        false);




update vendor_settings set value = '{
	"is_tuition_fees_pay_now_button_enabled":false,
	"is_activity_log_password_enabled":false,
	"is_activity_log_notification_instant":false,
	"is_qr_code_used_for_attendance":true,
 	"can_admin_reset_teacher_password": true,
	"is_dropoff_pickup_required_for_activity": true,
	"multi_location":{
		"is_enabled":false,
		"location_page_mobile_display":false
		},
	"display_newsletter_on_mobile":true,
	"display_meal_on_mobile":true
}' where vendor_id=1;


update vendor_settings 
{"email": "[object Object]", "donation": "kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk", "multi_location": {"is_enabled": false, "location_page_mobile_display": false}, "display_meal_on_mobile": false, "display_newsletter_on_mobile": false, "is_qr_code_used_for_attendance": true, "can_admin_reset_teacher_password": true, "is_activity_log_password_enabled": true, "is_activity_log_notification_instant": true, "is_tuition_fees_pay_now_button_enabled": true, "is_dropoff_pickup_required_for_activity": true}



update vendor_settings set value =  jsonb_set(jsonb_set(value::jsonb, '{email}', '{
                                    "parent_email_to_teacher" : {
                                        "cc" : [],
                                        "bcc" : []
                                    },
                                    "feedback_email" : {
                                        "to" : [],
                                        "cc" : [],
                                        "bcc" : []
                                    }
                            }':: jsonb) ::jsonb, '{donation}',
                                                            '"www.linkedin.com"' :: jsonb) where vendor_id = 1;


// We added settings feature because it was giving error when it checks if a feature is there.
insert into feature_permission_mapping(feature_id, permission_id, created_at)values(39, 3, now());


//user.js

module.exports = {
autoCreatedAt : 'createdAt',
autoUpdatedAt : 'updatedAt',
tableName : 'user'
attributes : {
	user_id :{
		type : 'integer',
		primaryKey : true
	}
	name : {
	type : 'string'
	}
	account : {
	type : 'user'
	}
	
}
}

insert into vendor_feature_mapping(vendor_id, feature_id, created_at)values(1, 39, now());119

insert into feature_permission_role_mapping(feature_permission_id, created_at, vendor_role_id)values(119, now(), 2);

Steps :

1. You need to create a feature name settings in feature table
2. Then create record in feature_permission_mapping with feature_id and permission_id
3. you need to create record feature_permission_role_mapping where id of feature_permission_mapping and vendor_role_id
4. Mention feature name in constant


 "data": {
        "url": "http://192.168.1.23:8080/#!/dashboard/donation",
        "vendor_id": 1
    }



// Donation
    'get /mobile/donation': {
        controller: routeConstants.DONATION.controller,
        action: 'getAllDonationDetailsForMobile',
        userType: userConstants.roles.parent
    },
    'get /donation': {
        controller: routeConstants.DONATION.controller,
        action: 'get',
        featureName: routeConstants.DONATION.featureName
    },
    'put /donation': {
        controller: routeConstants.DONATION.controller,
        action: 'update',
        featureName: routeConstants.DONATION.featureName
    },



-- START OF SETTINGS --
update vendor_settings set value = jsonb_set(value :: jsonb, '{can_parent_send_email_to_teachers}', ('true') :: jsonb);

update vendor_settings vs set value = jsonb_set(value :: jsonb, '{donation}', ((d.url):: text)::jsonb)
from (select url, vendor_id from donation)d where vs.vendor_id = d.vendor_id;
-- END OF SETTINGS --

update vendor_settings vs
set value = jsonb_set(value::jsonb, '{is_qr_code_used_for_attendance}',  ('false')::jsonb );   



//settings

new property script :  update vendor_settings set value = jsonb_set(value :: jsonb, '{can_parent_send_email_to_teachers}', ('true') :: jsonb);
donation migration script :  update vendor_settings vs set value = jsonb_set(value :: jsonb, '{donation}', ((d.url):: text)::jsonb)
from (select url, vendor_id from donation)d where vs.vendor_id = d.vendor_id;  















