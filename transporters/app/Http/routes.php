<?php
Route::get('/','Buy_Sell\web\homecontroller@index');
Route::get('/about','Buy_Sell\web\homecontroller@about');
Route::get('/how-it-works','Buy_Sell\web\homecontroller@how_it_works');
Route::get('/carriers','Buy_Sell\web\homecontroller@carriers');

Route::get('/signin','Buy_Sell\auth\web_auth_con@web_login');
Route::post('/post-login','Buy_Sell\auth\web_auth_con@web_post_login');
Route::get('/logout','Buy_Sell\auth\web_auth_con@user_logout');
Route::get('api/logout','Buy_Sell\auth\web_auth_con@user_logout');

Route::post('api/user-tabel-list','Buy_Sell\web\homecontroller@user_tabel_list');
Route::post('api/user-delete-row','Buy_Sell\web\homecontroller@user_delete_row');
Route::post('api/user-insert-data','Buy_Sell\web\homecontroller@user_insert_data');

Route::get('/signup','Buy_Sell\auth\web_auth_con@web_registration');
Route::post('/post-signup','Buy_Sell\auth\web_auth_con@web_post_signup');

Route::post('api/post-firebase','Buy_Sell\auth\web_auth_con@insert_firebase_rest');
Route::post('api/delete-firebase','Buy_Sell\auth\web_auth_con@delete_firebase_delete');

Route::post('/post-company-signup','Buy_Sell\auth\web_auth_con@web_company_post_signup');

Route::get('privacy-policy','Buy_Sell\web\homecontroller@privacy_policy'); 
Route::get('terms-and-conditions','Buy_Sell\web\homecontroller@terms_and_conditions'); 

Route::post('api/user-chat-details','Buy_Sell\web\homecontroller@message_details');
Route::post('api/write-message','Buy_Sell\web\homecontroller@write_message');
Route::any('api/request-accept-{id}-{user}-{bid}','Buy_Sell\web\homecontroller@request_accept'); 
Route::get('api/get-trailers','Buy_Sell\web\homecontroller@get_trailers'); 
Route::post('api/post-request','Buy_Sell\web\homecontroller@post_request'); 
Route::post('api/lang',array(
    'before' => 'csrf',
    'as' => 'language-chooser',
    'uses' => 'Buy_Sell\LanguageController@changeLanguage'));

Route::get('/contacts','Buy_Sell\web\homecontroller@contacts');

/*------------------------------------------------------------------------------------------*/
Route::post('user-forgot-password','Buy_Sell\auth\web_auth_con@user_forgot_password');
Route::any('user-update-password-{time}-{email}','Buy_Sell\auth\web_auth_con@user_update_password');
Route::any('user-update-password-process','Buy_Sell\auth\web_auth_con@user_update_password_process');
Route::post('reset-user-password','Buy_Sell\auth\web_auth_con@reset_user_password');

Route::post('send-your-message','Buy_Sell\auth\web_auth_con@send_your_message');  

/*------------------------------------------------------------------------------------------*/
Route::any('user-active-account-{time}-{email}-{uid}','Buy_Sell\auth\web_auth_con@user_active_account');
Route::any('subadmin-active-account-{time}-{email}-{code}-{phone}-{namelastname}','Buy_Sell\web\homecontroller@subadmin_active_account');
/**********************************************************************************************************/

Route::group(['middleware' => ['check_web_login']],  function(){
    Route::get('/shipper','Buy_Sell\web\homecontroller@shipper');
    Route::get('/your-quote','Buy_Sell\web\homecontroller@your_quote');
    Route::get('/request-list','Buy_Sell\web\homecontroller@request_list');
	Route::get('/request-list-details-{id}','Buy_Sell\web\homecontroller@request_list_details'); 
    Route::get('/message','Buy_Sell\web\homecontroller@message');
    Route::get('/profile','Buy_Sell\web\homecontroller@profile');
    Route::post('post-profile','Buy_Sell\web\homecontroller@update_profile');
    Route::get('/transit-requests','Buy_Sell\web\homecontroller@transit_requests');
    Route::get('/completed-requests','Buy_Sell\web\homecontroller@completed_requests');
    
    Route::get('/completed-requests-search','Buy_Sell\web\homecontroller@completed_requests_search');
    
    
    Route::get('/done-requests','Buy_Sell\web\homecontroller@done_requests');
    Route::get('/past-bids','Buy_Sell\web\homecontroller@past_bids');
    
    Route::get('/total-notification','Buy_Sell\web\homecontroller@total_notification');
    Route::post('/upload-user-image','Buy_Sell\web\homecontroller@upload_user_image');
    Route::post('post-your-request','Buy_Sell\web\homecontroller@post_request'); 
    Route::post('update-your-request','Buy_Sell\web\homecontroller@update_request');

    Route::get('/subadmins', 'Buy_Sell\web\homecontroller@subadmins_list');
    Route::post('/subadmins', 'Buy_Sell\web\homecontroller@add_subadmin');
    Route::post('subadmins/delete','Buy_Sell\web\homecontroller@delete_subadmin');

    Route::get('view-notification/{request_id}', function($request_id) {
        return redirect('/request-list-details-'.base64_encode($request_id.'||'.env(" APP_KEY ")));
    });

});
Route::post('api/user-active-account-resend-code','Buy_Sell\auth\web_auth_con@user_active_account_resend_code');
Route::post('api/user-active-account','Buy_Sell\auth\web_auth_con@user_active_account');
Route::get('api/user-active-account-resend-code','Buy_Sell\auth\web_auth_con@user_active_account_resend_code');
Route::get('/verification','Buy_Sell\web\homecontroller@verification_screen');
Route::get('send-mail',function(){
	$to = "priyo.ncr@gmail.com";  
    $subject = "Test mail";  
    $message = "Hello! This is a simple email message.";  
    $from = "garai.priyodas@gmail.com";  
    $headers = "From: $from";  
    mail($to,$subject,$message,$headers);  
    echo "Mail Sent.";
});

Route::get('/request_list_edit/{request_id}','Buy_Sell\web\homecontroller@requestEdit')->name('requestEdit');
Route::get('/request_edit_close_time/{request_id}','Buy_Sell\web\homecontroller@requestEditCloseTime')->name('requestEditCloseTime');
Route::post('/request/delete','Buy_Sell\web\homecontroller@softdelete_requests')->name('deleteRequest');
Route::post('api/post-firebase','Buy_Sell\auth\web_auth_con@insert_firebase_rest');
Route::post('api/delete-firebase','Buy_Sell\auth\web_auth_con@delete_firebase_delete');