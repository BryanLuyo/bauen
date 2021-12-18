$().ready(function(){
    $("#web_login_form").validate({
        rules:{
            user_email : "required",
            user_password : {
                        required :true ,
                        minlength : 6
                        }
        },
        messages : {
            user_email : "Please enter your email id",
            user_password : {
                required : "Please enter your password",
                minlength : "Your password must be at list 6 characters long"
            }
        }
    });
    
    
     $("#web_change_password_form").validate({
        rules:{
            current_password : {
                                required :true
                                },
            new_password : {
                            required :true,
                            minlength : 6
                            },
            repeat_password : {
                            required :true,
                            equalTo : "#new_password"
                            }
        },
        messages : {
            current_password : {
                required : "Please enter your current password"
            },
            new_password : {
                required : "Please enter your new password",
                minlength : "Your password must be at list 6 characters long"
            },
            repeat_password : {
                required : "Please repeat your password",
                equalTo : "Your password must be same as your new password"
            }
        }
    });
    
     $("#user_registration").validate({
        rules:{
            registration_user_name : {
                                required :true
                                },
            registration_user_email : {
                            required :true
                           
                            },
            registration_user_password : {
                            required :true,
                            minlength : 6
                            },
            registration_repeat_password : {
                            required :true,
                            equalTo : "#registration_user_password"
                            }
                            
        },
        messages : {
        registration_user_name : {
                required : "Please enter your user name"
            },
        registration_user_email : {
                required : "Please enter your email"
            },
        registration_user_password : {
                required : "Please enter your password",
                minlength : "Your password must be at list 6 characters long"
            },
        registration_repeat_password : {
                required : "Please repeat your password",
                equalTo : "Your password must be same as your new password"
            }
        }
    });    
});

 $("#user_registration_submit").click(function(){
     $("#user_registration").submit();
   // console.log('please done');
 });
$("#web_change_password_submit").click(function(){
     $("#web_change_password_form").submit();
   // console.log('please done');
 });