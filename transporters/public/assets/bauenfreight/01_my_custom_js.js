$().ready(function(){
    $("#login_form").validate({
        rules:{
            user_email : "required",
            password : {
                        required :true ,
                       // minlength : 6
                        }
        },
        messages : {
            user_email : "Please enter your email id",
            password : {
                required : "Please enter your password",
               // minlength : "Your password must be at list 6 characters long"
            }
        }
    });
    
    
    $("#signup_form").validate({
        rules:{
            user_email : "required",
            password : {
                        required :true ,
                        minlength : 6
                        },
            retypepassword : {
                        required :true ,
                        equalTo : "#password"
                        },
           first_name : "required",
          
        },
        messages : {
            user_email : "Please enter your email id",
            password : {
                required : "Please enter your password",
                minlength : "Your password must be at list 6 characters long"
            },
            retypepassword : {
                required : "Please repeat your password",
                equalTo : "Your retypepassword must be same as your password"
            },
             first_name : "Please enter your first name",
        }
    });
    
    
    });
    
    $("#submit_login_form").click(function(){
    $("#login_form").submit();
    });
    $("#submit_signup_form").click(function(){
    $("#signup_form").submit();
    });