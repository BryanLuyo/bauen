<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Agregar Sub Administrador
        <small>Los sub administradores sirven para tener otra personas ocupándose de gestionar Bauen.</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'subadmins')?>">Agregar Sub Administrador</a></li>
        <li class="active"></li>
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
              <h3 class="box-title">Detalles del Sub Administrador</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="first_name">Nombres</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Ingresar nombres" value="<?=set_value('first_name')?>">
				  <?=form_error('first_name')?>
                </div>
				<div class="form-group">
                  <label for="last_name">Apellidos</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Ingresar apellidos" value="<?=set_value('last_name')?>">
                </div>
                <div class="form-group">
                  <label for="email">Correo Electrónico</label>
                  <input type="text" class="form-control" id="email" name="email" placeholder="Ingresar correo electrónico" value="<?=set_value('email')?>">
				  <?=form_error('email')?>
                </div>
				<div class="form-group">
                  <label for="phone_no">Número de Teléfono</label>
                  <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Ingresar número de teléfono" value="<?=set_value('phone_no')?>">
				  <?=form_error('phone_no')?>
                </div>
				<div class="form-group">
                  <label for="password">Contraseña</label>
                  <input type="text" class="form-control" id="password" name="password" placeholder="Ingresar contraseña" value="<?=set_value('password')?>">
				  <?=form_error('password')?>
                </div>
				
				<div class="form-group">
                  <label for="dni_no">No. de DNI</label>
                  <input type="text" class="form-control" id="dni_no" name="dni_no" placeholder="Ingresar número de DNI" value="<?=set_value('dni_no')?>">
				  <?=form_error('dni_no')?>
                </div>
				
				<div class="form-group">
                  <label for="max_load">Imagen</label>
                  <input type="file" class="form-control" id="image" name="image">
                </div>
				
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enviar</button>
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