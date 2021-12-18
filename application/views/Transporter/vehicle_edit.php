<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- color picker -->
<link rel="stylesheet" href="<?=base_url('assets/plugins/colorpicker/bootstrap-colorpicker.min.css')?>">
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Editar Vehículo
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'vehicles')?>">Vehículos</a></li>
        <li class="active">Editar Vehículo</li>
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
              <h3 class="box-title">Detalles del Vehículo</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="trailer_id">Vehículo</label>
				  <select name="trailer_id" id="trailer_id" class="form-control" >
					<option value="0">Seleccionar vehículo</option>
					<?php
					if(!empty($trailers)){
						$trailer_id = set_value('trailer_id',$vehicle['trailer_id']);
						foreach($trailers as $trailer){
							$selected='';
							if($trailer_id==$trailer['trailer_id']){
								$selected='selected';
							}
							?>
						<option value="<?=$trailer['trailer_id']?>" <?=$selected?>><?=ucwords($trailer['name'])?></option>
							<?php
						}
					}
					?>
				  </select>
				  <?=form_error('trailer_id')?>
                </div>
				<div class="form-group">
                  <label for="truck_brand">Marca</label>
                  <input type="text" class="form-control" id="truck_brand" name="truck_brand" placeholder="Ingrese la marca" value="<?=set_value('truck_brand',$vehicle['truck_brand'])?>">
				  <?=form_error('truck_brand')?>
                </div>
				<div class="form-group">
                  <label for="truck_model">Modelo</label>
                  <input type="text" class="form-control" id="truck_model" name="truck_model" placeholder="Ingrese el modelo" value="<?=set_value('truck_model',$vehicle['truck_model'])?>">
				  <?=form_error('truck_model')?>
                </div>
				<div class="form-group">
                  <label for="plate_no">Número de Placa</label>
                  <input type="text" class="form-control" id="plate_no" name="plate_no" placeholder="Ingrese el número de placa" value="<?=set_value('plate_no',$vehicle['plate_no'])?>">
				  <?=form_error('plate_no')?>
                </div>
                <div class="form-group">
                  <label for="purchase_year">Año de Fabricación</label>
                  <input type="text" class="form-control" id="purchase_year" name="purchase_year" placeholder="Ingrese el año de fabricación" value="<?=set_value('purchase_year',$vehicle['purchase_year'])?>">
				  <?=form_error('purchase_year')?>
                </div>
				<div class="form-group">
                  <label for="vehicle_minload">Carga mínima</label>
                  <input type="text" class="form-control" id="vehicle_minload" name="vehicle_minload" placeholder="Ingrese la carga mínima" value="<?=set_value('vehicle_minload',$vehicle['vehicle_minload'])?>">
				  <?=form_error('vehicle_minload')?>
                </div>
				<div class="form-group">
                  <label for="vehicle_maxload">Carga máxima</label>
                  <input type="text" class="form-control" id="vehicle_maxload" name="vehicle_maxload" placeholder="Ingrese la carga máxima" value="<?=set_value('vehicle_maxload',$vehicle['vehicle_maxload'])?>">
				  <?=form_error('vehicle_maxload')?>
                </div>
				<div class="form-group">
                  <label for="vehicle_color">Color del Vehículo</label>
                  <input type="text" class="form-control" id="vehicle_color" name="vehicle_color" placeholder="Ingrese el color del vehículo" readonly value="<?=set_value('vehicle_color',$vehicle['vehicle_color'])?>">
				  <?=form_error('vehicle_color')?>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Actualizar</button>
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
	<!-- page script -->
	<script src="<?=base_url('assets/plugins/colorpicker/bootstrap-colorpicker.min.js')?>"></script>
	<script>
		$(document).ready(function(){
			$("#vehicle_color").colorpicker();
		});
	</script>