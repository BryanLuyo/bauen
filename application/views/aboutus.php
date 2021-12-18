<style>
	.pmargin{
		padding-left: 10%;
		padding-right: 10%;
		text-align:center;
		margin-top:15px;
	}
	.color{
		color:rgb(63,63,63);
	}
	.pfz{
		font-size:18pt;
	}
	.pfzx{
		font-size:24pt;
	}
	ul {
		list-style: none;
	}
	
</style>
<div>
  <div class="login-logo" style="margin-top:20px;">
    <a href="http://www.bauenfreight.com"><b>Bauen.</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
	
	<p class="pmargin color pfzx">CONECTANDO CARGAS CON TRANSPORTISTAS HOMOLOGADOS</p>
	<p class="pmargin">
Solo cotiza con nosotros y obtén propuestas serias en minutos. 
</p>
	<div class="row">
	<div class="box-header with-border">
	  <h3 class="box-title color">Contáctanos</h3>
	</div>
	<div class="col-md-6 color pfzx" style="margin-top:20px;">
		<p>Sergio Olcese - CEO & Founder</p>
		<p>Fernando Pazos - CFO & Founder</p>
	</div>
	<div class="col-md-6">
		<form method="post" action="">
			<div class="box-body">
				<?php if(isset($errors) && !empty($errors)){?>
				<div class="callout callout-danger"><?php echo $errors;?></div>
				<?php }?>
				<?php if(isset($success) && !empty($success)){?>
				<div class="callout callout-success"><?php echo $success;?></div>
				<?php }?>
				
				<div class="form-group">
                  <label for="en_name">Nombre</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese su nombre">
                </div>
				<div class="form-group">
                  <label for="en_name">Correo</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa una dirección de correo">
                </div>
				<div class="form-group">
                  <label for="en_name">Teléfono</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Ingresa un número telefónico">
                </div>
				<div class="form-group">
                  <label for="en_name">Mensaje</label>
                  <textarea class="form-control" id="message" name="message" rows="5" placeholder="Escribe un mensaje para nuestros representantes"></textarea>
                </div>
				<div class="form-group">
                  <input type="submit" class="btn btn-primary" value="Enviar">
                </div>
			</div>
		</form>
	</div>
	</div>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
