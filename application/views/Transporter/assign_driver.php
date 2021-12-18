<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Asignar Conductor y Vehículo</h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'requests')?>">Órdenes</a></li>
        <li class="active">Asignar Conductor y Vehículo</li>
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
              <h3 class="box-title">Detalles del conductor y vehículo</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="driver_id">Conductor</label>
				  <select name="driver_id" id="driver_id" class="form-control" >
					<option value="0">Seleccionar conductor</option>
					<?php
					if(!empty($drivers)){
						$driver_id = set_value('driver_id');
						foreach($drivers as $driver){
							$selected='';
							if($driver_id==$driver['user_id']){
								$selected='selected';
							}
							?>
						<option value="<?=$driver['user_id']?>" <?=$selected?>><?=ucwords($driver['first_name'].' '.$driver['last_name']).'( '.user_status($driver['user_status']).' )'?></option>
							<?php
						}
					}
					?>
				  </select>
				  <?=form_error('driver_id')?>
                </div>
				
				<div class="form-group">
          <label for="plate_no">Vehículo</label>
          <select name="vehicle_id" id="vehicle_id" class="form-control" >
            <option value="0">Seleccionar Vehículo</option>
            <?php
            if(!empty($vehicles)){
              $vehicle_id = set_value('vehicle_id');
              foreach($vehicles as $vehicle){
                $selected='';
                if($vehicle_id==$vehicle['vehicle_id']){
                  $selected='selected';
                }
                ?>
              <option value="<?=$vehicle['vehicle_id']?>" <?=$selected?>><?=$vehicle['plate_no'].'( '.vehicle_status($vehicle['vehicle_status']).' )'?></option>
						<?php } } ?>
				  </select>
				  <?=form_error('vehicle_id')?>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Asignar</button>
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