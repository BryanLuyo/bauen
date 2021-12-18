<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="login-box">
  <div class="login-logo">
    <a href="javascript:void(0)"><b>BAUEN</b>FREIGHT</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Ingrese su usuario y contraseña para Iniciar Sesión</p>

    <form action="login" method="post" class="sb-form" is_validation="1" name="login">
      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email" id="email" required='1' value="<?=set_value('email')?>">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
		<?=form_error('email')?>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password" name="password" id="password" required='1' >
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
		<?=form_error('password')?>
		<?=form_error('not_match')?>
      </div>
	  <!-- error section -->
	  <div class="form-group has-feedback">
		<input type="hidden" class="form-control">
	  </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <!--input type="checkbox" name="remember_me" id="remember_me"> Remember Me-->
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat sd-signin">Ingresar</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
	<?php 
		//echo anchor(base_url('Admin/forgotpassword'),'I forgot my password',array('title'=>'forgot password'));
	?>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script type="text/javascript">
	$(document).ready(function(){
		$('html body').bind('keypress',keypresstrack);
	});
	function keypresstrack(e){
		if(e.keyCode==13){
			$(".sd-signin").trigger('click');
		}
	}
</script>