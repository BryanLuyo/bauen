$().ready(function(){
    $("#login-form").validate({
        rules:{
            email : "required",
            password : {
                        required :true ,
                        minlength : 6
                        }
        },
        messages : {
            email : "Please enter your email id",
            password : {
                required : "Please enter your password",
                minlength : "Your password must be at list 6 characters long"
            }
        }
    });
});
//$("#admin_login_submit").click(function(){
//    alert("hi");
//});

