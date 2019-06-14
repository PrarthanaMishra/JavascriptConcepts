update vendor_settings set value = '{                                                   
    "is_tuition_fees_pay_now_button_enabled" : true,
    "multi_location":{                              
        "is_enabled":false,                             
        "location_page_mobile_display":false            
    },
    "is_qr_code_used_for_attendance": false                                      
}' where vendor_id = 1;


RAISE NOTICE '%', v_query;

DROP FUNCTION public.fn_search_student_for_attendance(p_classroom_id integer, p_location_id integer,  p_vendor_id integer, p_role character varying, p_status status, p_search_text character varying);                                      
    


update vendor_settings set value = '{
	"is_tuition_fees_pay_now_button_enabled":false,
	"is_activity_log_password_enabled":false,
	"is_activity_log_notification_instant":false,
	"is_qr_code_used_for_attendance":true,
	"multi_location":{
		"is_enabled":false,
		"location_page_mobile_display":false
		},
	"display_newsletter_on_mobile":true,
	"display_meal_on_mobile":true
}' where vendor_id=1;


http://localhost:1340/attendance-check-in-checkout-report?start_date=2017-04-29&end_date=2018-04-1%20&%20 &JWT=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImFjY291bnRfaWQiOjcsImZpcnN0X25hbWUiOiJhZG1pbiIsImxhc3RfbmFtZSI6IiIsInJvbGVfaWQiOjEsInJvbGUiOiJTdXBlckFkbWluIiwidmVuZG9yX2lkIjoxLCJsb2dvIjoiaW1hZ2VzL3ZkbV9sb2dvIiwic2V0dGluZ3MiOnsiaXNfdHVpdGlvbl9mZWVzX3BheV9ub3dfYnV0dG9uX2VuYWJsZWQiOmZhbHNlLCJpc19hY3Rpdml0eV9sb2dfcGFzc3dvcmRfZW5hYmxlZCI6ZmFsc2UsImlzX2FjdGl2aXR5X2xvZ19ub3RpZmljYXRpb25faW5zdGFudCI6ZmFsc2UsImlzX3FyX2NvZGVfdXNlZF9mb3JfYXR0ZW5kYW5jZSI6dHJ1ZSwibXVsdGlfbG9jYXRpb24iOnsiaXNfZW5hYmxlZCI6ZmFsc2UsImxvY2F0aW9uX3BhZ2VfbW9iaWxlX2Rpc3BsYXkiOmZhbHNlfSwiZGlzcGxheV9uZXdzbGV0dGVyX29uX21vYmlsZSI6dHJ1ZSwiZGlzcGxheV9tZWFsX29uX21vYmlsZSI6dHJ1ZX19LCJpYXQiOjE1MjYwMzg2NTEsImV4cCI6MTU1NzU3NDY1MSwiYXVkIjoici10ZWNoIiwiaXNzIjoici10ZWNoIn0.H8VjBDSbDHMtjemm2tI7dgfBB1rzpkAYArPK1T342LM


http://localhost:1340/attendance-check-in-checkout-report?start_date=2017-04-29&end_date=2018-04-1

