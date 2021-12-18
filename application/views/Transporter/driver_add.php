<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Agregar Conductor
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'drivers')?>">Conductores</a></li>
        <li class="active">Agregar conductor</li>
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
              <h3 class="box-title">Detalles del Conductor</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="first_name">Nombre</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Nombre del Conductor" value="<?=set_value('first_name')?>">
				  <?=form_error('first_name')?>
                </div>
				<div class="form-group">
                  <label for="last_name">Apellido</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Apellido del Conductor" value="<?=set_value('last_name')?>">
                </div>
                <div class="form-group">
                  <label for="email">Correo electrónico</label>
                  <input type="text" class="form-control" id="email" name="email" placeholder="Este servirá de usuario. Debe contener @. " value="<?=set_value('email')?>">
				  <?=form_error('email')?>
                </div>
				<div class="form-group">
                  <label for="phone_no">Número de Teléfono</label>
                  <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Enter phone no." value="<?=set_value('phone_no')?>">
				  <?=form_error('phone_no')?>
                </div>
				<div class="form-group">
                  <label for="password">Contraseña</label>
                  <input type="text" class="form-control" id="password" name="password" placeholder="Ingrese y recuerde la contraseña" value="<?=set_value('password')?>">
				  <?=form_error('password')?>
                </div>
				<div class="form-group">
                  <label for="dni_no">Número de DNI</label>
                  <input type="text" class="form-control" id="dni_no" name="dni_no" placeholder="Ingrese el número de DNI del Conductor" value="<?=set_value('dni_no')?>">
				  <?=form_error('dni_no')?>
                </div>
	        <!--<div class="form-group">
                  <label for="ruc_no">RUC</label>
                  <input type="text" class="form-control" id="ruc_no" name="ruc_no" placeholder="Ingrese el número de RUC" value="<?=set_value('ruc_no')?>">
				  <?=form_error('ruc_no')?>
                </div>-->
				
				<div class="form-group">
                  <label for="max_load">Imagen</label>
                  <input type="file" class="form-control" id="image" name="image">
                </div>
				
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <div class="alert alert-info">Los conductores requerirán de una revisión previa por la administración de Bauen Freight antes de ser aprobados.</div>
                <button type="submit" class="btn btn-primary">Registrar</button>
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