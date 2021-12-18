<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Editar detalles del Conductor
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'drivers')?>">Conductores</a></li>
        <li class="active">Editar detalles del Conductor</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="box box-body">
        <!-- left column -->
        <div class="col-md-8">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Datos del Conductor</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="first_name">Nombre del Conductor</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Ingrese el nombre del conductor" value="<?=set_value('first_name',$driver['first_name'])?>">
				  <?=form_error('first_name')?>
                </div>
				<div class="form-group">
                  <label for="last_name">Apellido del Conductor</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Ingrese el Apellido" value="<?=set_value('last_name',$driver['last_name'])?>">
                </div>
                <div class="form-group">
                  <label for="email">Correo electrónico</label>
                  <input type="text" class="form-control" id="email" name="email" placeholder="Correo electrónico de Cuenta de Usuario de Conductor" value="<?=set_value('email',$driver['email'])?>">
				  <?=form_error('email')?>
                </div>
				<div class="form-group">
                  <label for="phone_no">Número de teléfono</label>
                  <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Ingrese el número de teléfono" value="<?=set_value('phone_no',$driver['phone_no'])?>">
				  <?=form_error('phone_no')?>
                </div>
				<div class="form-group">
                  <label for="password">Contraseña</label>
                  <input type="text" class="form-control" id="password" name="password" placeholder="Ingresar contraseña" value="<?=set_value('password',$driver['showpass'])?>">
				  <?=form_error('password')?>
                </div>
				<div class="form-group">
                  <label for="dni_no">Número de DNI</label>
                  <input type="text" class="form-control" id="dni_no" name="dni_no" placeholder="Enter DNI no." value="<?=set_value('dni_no',$driver['dni_no'])?>">
				  <?=form_error('dni_no')?>
                </div>
				
				<div class="form-group">
                  <label for="max_load">Imagen</label>
				  <?php
					if(!empty($driver['image'])){
						?>
					<img src="<?=base_url('uploads/users/'.$driver['image'])?>" width="60" height="60" >
						<?php
					}
				  ?>
                  <input type="file" class="form-control" id="image" name="image">
                </div>
				
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Editar</button>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
		</div>
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->