
<!DOCTYPE html>
<html lang="en">    
    <head>
        <title>LiveShop Admin</title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="{{asset('public/assets/ncr/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/ncr/css/bootstrap-responsive.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/ncr/css/matrix-login.css')}}" />
        <link href="{{asset('public/assets/ncr/font-awesome/css/font-awesome.css')}}" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
        <style>
           label.error{
               color: red;
               font-size: 14px;
           }
        </style>
    </head>
    <body>
        <div id="loginbox"> 
            
        @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif


        @if(session('message'))
        <div class="alert alert-danger {{session('message.type')}}">
            {{session('message.text')}}
        </div>
        @endif
       
            <form id="login-form" class="form-vertical" action=" {{url('admin-post-login')}}" method="post"> 			 <div class="control-group">                  <div class="controls" >                    <img src="{{asset('public/assets/images/01_logo.png')}}"  style="margin-left: 30%;"alt="">                </div>            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-envelope"></i></span>
                            <input type="email" placeholder="E-mail address" id="admin_user_name" name="admin_user_name" required/>
                        </div>
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span>
                            <input type="password" placeholder="Password" id="admin_password" name="admin_password" required />
                        </div>
                    </div>
                </div>
            <div class="control-group">
                    <div class="controls">
                        <div class="login-button">
                             <span><input type="submit" class="btn btn-success" id="admin_login_submit" value="Login"/> </span>
                        </div>
                    </div>
                </div>
                </form>
       
        </div>        
        <script src="{{asset('public/assets/ncr/js/jquery.min.js')}}"></script>  
        <script src="{{asset('public/assets/ncr/js/matrix.login.js')}}"></script> 
        <script src="{{asset('public/assets/ncr/js/jquery.validate.js')}}"></script>  
        <script src="{{asset('public/assets/Myfile/login_js.js')}}"></script>
    </body>

</html>
