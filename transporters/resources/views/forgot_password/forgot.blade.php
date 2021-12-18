@extends('../master_layout/web')
@section('custom_js')
<script>
            $().ready(function(){
			
 $("#password_reset_form").validate({			
          rules:{            
            password_1 : {
                        required :true ,
						minlength : 6
                        },
            password_2 : {
                        required :true ,
                        equalTo : "#password_1"
                        },          
        },
        messages : {
           
            password_1 : {
                required : "Please enter your password",
                minlength : "Your password must be at list 6 characters long"
            },
            password_2 : {
                required : "Please repeat your password",
                equalTo : "Your retypepassword must be same as your password"
            }
        }
	});	
 });
 
 
  $("#submit_password_reset_form").click(function(){
    $("#password_reset_form").submit();
    });

        </script>
@endsection
@section('title')
<title>Bauenfreight</title>
@endsection

@section('banner')
@endsection

@section('main_container')
 <div class="container">
       @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
           {{Session::get('message')}}<strong>!</strong>
            {{Session::forget('message')}}
        </div>
        @endif
  <div class="registration"> 
  <h5>Create new account</h5>
  <form id="password_reset_form" action="{{url('reset-user-password')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      <input type="hidden" name="user_id" value="{{ $user }}">   
<div class="form-group required">
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
        <input type="password" class="form-control" name="password_1" id="password_1"  placeholder="Password"/>
    </div>
</div>
<div class="form-group required">
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
        <input type="password" class="form-control" name="password_2"  placeholder="Retype Password"/>
    </div>
</div>

<div class="form-group ">
    <button type="button" id="submit_password_reset_form" class="btn btn-primary btn-block login-button">SIGN UP</button>
</div>
	
  </form>
  
  </div>
  </div>

@endsection