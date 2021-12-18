<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>BAUEN FREIGHT | Reset Password</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?=base_url('assets/')?>bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url('assets/')?>dist/css/AdminLTE.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="javascript:void(0);"><b>BAUEN</b>FREIGHT</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Create Your New Password</p>

    <form action="" method="post">
      <div class="form-group has-feedback">
        <input type="password" name="new_password" class="form-control" placeholder="New Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="con_password" class="form-control" placeholder="Confirm Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-4 pull-right">
          <button type="submit" class="btn btn-primary btn-block btn-flat signin">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
	<?php
		if(!empty($error)){
	?>
	<div style="color:red;"><?=$error?></div>
	<?php
		}
		if(!empty($success)){
	?>
	<div style="color:green;"><?=$success?></div>
	<?php
		}
	?>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!-- jQuery 2.2.3 -->
<script src="<?=base_url('assets/')?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?=base_url('assets/')?>bootstrap/js/bootstrap.min.js"></script>
<script>
	$(document).ready(function(){
		$(".signin").bind('click',function(e){
			/*e.preventDefault();
			var nw_password = $.trim($("[name='new_password']").val());
			var con_password = $.trim($("[name='con_password']").val());
			if(nw_password.length==0){
				alert("Enter your new password");
			}
			else{
				if(con_password.length==0){
					alert("Enter your confirm password");
				}
				else{
					if(con_password!=nw_password){
						alert("Confirm password does not match");
					}
					else{
						$("form").submit();
					}
				}
			}*/
		});
	});
</script>
</body>
</html>
