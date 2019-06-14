1. student_tuition_fees_mapping -> student_tuition_fees_id

2. account_tuition_fees_transaction

3. vendor_payment_method_mapping

4. payment_method

crunchbase

//notes

A PayPal Sandbox account is a test account which is an exact copy of a "live" PayPal account
You can use the sandbox account to test the various PayPal features and settings before you use the actual "live" PayPal account.


When this api gets called - updatePaymentTransactionStatus

updatePaymentTransactionStatus - is the api where we are interacting with ipn

IPN - instant payment notification


There is only one function, verify, which is used to verify any IPN messages you receive:

ipn.verify(ipn_params, [settings], callback);

ipn_params is the dictionary of POST values sent to your IPN script by PayPal. Don't modify the dict in any way, just pass it directly to ipn.verify to check if the IPN message is valid.

1. create an ipn listener page.
2. add url of this page to paypal account profile
3. PayPal then sends notifications of all transaction-related events to that URL
4. When customers pay for goods or services, PayPal sends a secure FORM POST containing payment information (IPN messages) to the URL
5. The IPN listener detects and processes IPN messages using the merchant backend processes.
6. The IPN listener page contains a custom script or program that waits for the messages
7. messages validates them with PayPal
8. 

How to make recurring payments

url :https://developer.paypal.com/docs/classic/paypal-payments-pro/integration-guide/recurring-payments/

api CreateRecurringPaymentsProfile

// student_tuition_fees_mapping - This record comes from cms. Tution fees and penalty all are filled through cms and this record got created.
// account_tuition_fees_transaction - There is a button in app which when get clicked we created a transaction id and redirects that to payapl and redirects that user to success page.
	We create a record on this table as it presses button. This record contains which account id is creating payment. And we keep status pending. Now we send transaction id
	to paypal to check transaction status. If it's completed we will update status to complete and it will show in app. Otherwise it will show last transaction got failed and table will get updated
	failed.
//vendor_payment_method_mapping,payment_method are static table

1.addPaymentTransaction : just add all details coming from cms and then redirect payment.ejs give all details to fill the form
2. updatePaymentTransactionStatus : This is the url that we put in ipn to send transaction notification status. It needs business account to set the details. So this route ie url is called by ipn itself
		to update transaction status. As soons as we get response fron ipn we send a ok response to ipn so that it get the acknowledment and stop sending it again.
IPn url will be same for every client. Difference will be added by details information that is merchant and email

url that is in database is used to send request which is used by payment.ejs to redirect user to paypal website after submitting the form.

 if(moment(dueDate).format("YYYY-MM-DD") > moment(new Date()).format("YYYY-MM-DD")) 


Some Of the information :-

1. There is a term called ipn forwarder which ipn url to particular location.
2. Issue is : The route that ipn use to send notification is set in business account manually. We cannot say client to add this url by going in settings in business account.
3. PayPal allows us to dynamically set the IPN listener URL by passing a hidden field with each outbound transaction. 

notify_url
https://codeseekah.com/2012/02/11/how-to-setup-multiple-ipn-receivers-in-paypal/
https://stackoverflow.com/questions/43203213/is-it-possible-to-dynamically-set-notify-url-for-recurring-payments-in-paypal


we can do using notify_url <input type="hidden" name="notify_url" value="http://www.mydomain.co.uk/paypal-completed.php"> 
https://stackoverflow.com/questions/13415964/paypal-notify-url-and-return-url-receiving-variables-without-ipn-using-php

//nice tutorial

https://www.evoluted.net/thinktank/web-development/paypal-php-integration
One issue resolved = we can give url in notify_url
Payment has to enable from account only. No such facility

PayPal Instant Payment Notification (IPN),
PayPal Payment Data Transfer (PDT)- No, it's alternative to ipn. It's weakness is it sends payment notification only once
https://shopplugin.net/kb/how-do-i-setup-paypal-ipn-and-pdt/

New integrations outside the UK must use PayPal Payments Pro. It supports recurring payments

https://saijogeorge.com/dummy-credit-card-generator/
https://developer.paypal.com/demo/checkout/#/pattern/confirm
https://developer.paypal.com/demo/checkout/#/pattern/vertical
https://developer.paypal.com/developer/accounts/

<input type="hidden" name="notify_url" value="http://montessori.infojiniconsulting.com/api/tuitionfees/payment/transaction"> 

377350896683111

//Payment gateway
theprarthana-facilitator@gmail.com
credit card number - 4311195383182445
VISA
05/2023

theprarthana-buyer@gmail.com
4311191593499927
VISA
05/2023
9999.00 USD

Login : elvis.c@infojiniconsulting.com
Paswrd : Infojinipayments@001

elvis.c-buyer@infojiniconsulting.com
VISA
4032032961448757
3019604066
alaska 99501

//Production


locale: "en_US" solved country problem in paypal credit card information
https://stackoverflow.com/questions/43276570/trigger-paypal-checkout-button-click
https://developer.paypal.com/developer/applications/edit/QVphZVNDZ3poU1JxVnZydDJYeGZ1RTVKYzJ6NnJXMVdUMlVFTXJIWEQtUWJNV3dCcmRmLTJyWkRMRXppaEhDSlZxSVo2aUp3cXNQLWZEdXA=
https://www.paypal-community.com/t5/Express-Checkout/Default-Credit-Card-Country-using-Client-Side-Express-Checkout/td-p/1411052

elvis live client id : AUrJ5q9OuTvioKtEG5BmB3mMd9zbWKTLsYevD-aKnoGXmx62zxK6jhnuT372hlsfA7Adi-g0PkKMbi_y

how to get client id : click on app name in paypal developer you will get the client id. you can get both for sandbox and live
client app id - bec how paypal will get to know where it will have to deposit money

you can have a account in paypal ie business account.
you can go to developer account and get your sandbox buyer and facilator credit card information.
From there you can get live details as well

https://stackoverflow.com/questions/26903299/paypal-billing-agreement-no-approval-url-or-redirect-url-after-creation?rq=1

current_client id in test ='ZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R'

client id in databse is of elvis - AXZH1nb035oLcVhL-zfwpndNmkXHFhZKyrsptUtjcU_5ILdQYP9TkFKUBkIzSbB9wtZP0oMb3ciJYHvB


For Indian business account sandbox is not working.
Indain bussiness account client id 


https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/
Ipn messages is sent even though ipn is not enabled when we do it by notify_url. This link gives the information

REST APIs uses multiple standards like HTTP, JSON, URL, and XML while SOAP APIs is largely based on HTTP and XML

update account_tuition_fees_transaction set transaction_status='pending' where account_tuition_fees_transaction_id=43;

update vendor_payment_method_mapping set details  ='{
"merchant_id" : "AUrJ5q9OuTvioKtEG5BmB3mMd9zbWKTLsYevD-aKnoGXmx62zxK6jhnuT372hlsfA7Adi-g0PkKMbi_y"
}' where vendor_id=1;

http://montessori.infojiniconsulting.com/#!/dashboard/student/134/detail


09610252-7c2f-4bf8-958a-93785ddd9174

https://stackoverflow.com/questions/6468674/paypal-using-webview
http://www.oodlestechnologies.com/blogs/Generate-Excel-Report-in-Nodejs


Login : elvis.c@infojiniconsulting.com
Paswrd : Infojinipayments@001

306eabcb-eb68-41d6-9c9a-430089f86e76

http://montessori.infojiniconsulting.com/api/tuitionfees/payment/transaction
http://montessori.infojiniconsulting.com/api/tuitionfees/payment/transaction

Advanced html and css tutorial

https://www.sandbox.paypal.com/mep/dashboard

//new complete status sandbox account

card - 4032036895124079
expiry - 07/2023

client id comes from app but it depends with client id - elvis.c-facilitator@infojiniconsulting.com

new sandbox client id - AUYympmJh7UlriMvf3Y4UrOZP0vWv3ky0ClmjT2tC6UZa8IIkcjjo4TSz-sXOF9aa57ramEOO2zvHGOJ

update vendor_payment_method_mapping set details  ='{
"merchant_id" : "AUYympmJh7UlriMvf3Y4UrOZP0vWv3ky0ClmjT2tC6UZa8IIkcjjo4TSz-sXOF9aa57ramEOO2zvHGOJ"
}' where vendor_id=1;

21H50409DS6568334

Note :

Sandbox account has different profile login.
We can get sandbox login details with from paypal devloper account and login in with these credentials.
Get client id from app. That app you can create with the help of bussiness id you have in paypal developer account.

	http://montessori.infojiniconsulting.com/api/tuitionfees/payment/transaction

http://montessori.infojiniconsulting.com/#!/dashboard - 134

zalgo promise value error





