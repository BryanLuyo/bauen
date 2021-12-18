<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Bauen Transportista</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?=base_url()?>assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url()?>assets/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/iCheck/square/blue.css">

  <script src="<?=base_url()?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script type="text/javascript">
	var siteAdminBaseUrlJS="<?=base_url(BASE_FOLDER_TRANS)?>";
  </script>
  <style type="text/css">
    input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type="number"] {
        -moz-appearance: textfield;
    }
    .verification-container {
        max-width: 460px;
        width: 100%;
        margin: 50px auto;
        text-align: center;
    }
    
    .code-input-container {
        display: flex;
        justify-content: center;
        margin: 0 0 20px;
    }

    .code-input {
        width: 45px;
        height: 50px;
        margin: 0 4px;
        text-align: center;
        font-size: 20px;
    }

    .text-warning {
        color: #B72C43;
    }
</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <div class="login-logo">
    <a href="javascript:void(0)"><b>Bauen.CERTIFIED</a>
  </div>
  
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Bienvenido Sr. Transportista <br> Ingrese su código de verificación.</p>
    <?php if(isset($verification_error)): ?>
      <div class="alert alert-info text-center" style="margin-bottom:1rem"><?= $verification_error; ?></div>
    <?php endif; ?>
    <form action="verification" method="post" class="sb-form" name="verification">
      <div class="code-input-container">
        <input class="code-input form-control" id="code-input" type="number" name="code01" max="9" size="1" required>
        <input disabled class="code-input form-control" type="number" name="code02" max="9" size="1" required>
        <input disabled class="code-input form-control" type="number" name="code03" max="9" size="1" required>
        <input disabled class="code-input form-control" type="number" name="code04" max="9" size="1" required>
        <input disabled class="code-input form-control" type="number" name="code05" max="9" size="1" required>
        <input disabled class="code-input form-control" type="number" name="code06" max="9" size="1" required>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="submit"class="btn btn-primary btn-block btn-flat sd-signin">Verificar</button>
          <a href="<?=base_url(BASE_FOLDER_TRANS.'logout')?>" class="btn btn-block text-center btn-link">Cerrar sesión</button></form>
        </div>
      </div>
    </form>
	<?php 
		//echo anchor(base_url('Admin/forgotpassword'),'I forgot my password',array('title'=>'forgot password'));
	?>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!-- Bootstrap 3.3.6 -->
<script src="<?=base_url()?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?=base_url()?>assets/plugins/iCheck/icheck.min.js"></script>
<!-- developer js -->
<script src="<?=base_url()?>assets/js/dev_script.js" type="text/javascript"></script>
<script type="text/javascript">
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
    <script>
        $(window).ready(function() {

            setTimeout(() => {
                $('.code-input').first().focus()
            }, 1000)

            $('.code-input').keyup(function(e) {
                if( $.isNumeric(String.fromCharCode(e.which-48)) || $.isNumeric(String.fromCharCode(e.which)) ) {
                    console.log('is number', String.fromCharCode(e.which))
                    if($(this).val().length > 1) { // Sólo un número por input
                        let finalVal = $(this).val().substring(0, 1)
                        $(this).val(finalVal)
                    }

                    if($(this).val().length <= 0) return; // Al menos un dígito para continuar con el código
                    
                    let nextElement = $(this).next();
                    if(nextElement.hasClass('code-input')) {
                        nextElement.removeAttr('disabled')
                        nextElement.focus()
                    } else {
                        $('button[type=submit]').focus()
                    }
                } else if(e.which === 8) {
                    console.log('is delete', String.fromCharCode(e.which))
                    let prevElement = $(this).prev();
                    if(prevElement.hasClass('code-input')) {
                        prevElement.focus()
                        $(this).attr('disabled', true)
                    }
                } else {
                    console.log('else', String.fromCharCode(e.which))
                    $(this).val('')
                }
            })
        })

        const resendCode = (e) => {
            e.preventDefault();
            if(confirm('¿Realmente desea que reenviemos el código?')) {
                window.location="{{URL::to('api/user-active-account-resend-code')}}";
            } else {
                console.log('no se reenvia')
            }
        }
    </script>
<!-- preloader view -->
<div class="sitepreloader" style="width:100%; height:100%; background-color:cyan; z-index:9999; left:0px; top:0px; opacity:0.5; position:fixed; display:none;">
	<img src="<?=base_url('assets/Admin/dist/img/')?>avatar.png" style="position:absolute; top:50%; left:50%; margin-left:-108px; margin-top:-108px;" />
</div>
</body>
</html>