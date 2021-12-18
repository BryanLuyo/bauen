<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Vehiculos
        <small><?php
			if(!empty($transporter)){
				echo "Of ".ucwords($transporter['first_name'].' '.$transporter['last_name']).'&nbsp;( '.phoneno_format($transporter['phone_no']).' )';
			}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Vehiculos</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
				<?php 
					echo anchor(base_url(BASE_FOLDER_TRANS.'vehicles/add'),'<i class="fa fa-plus"></i> Vehiculo',array('class'=>'pull-right btn btn-primary','style'=>'margin-right:5px;'));
				?>
			  <form role="form" method="post">
				  <div class="box-body">
					<div class="form-group col-xs-3">
					  <select class="form-control" name="trailer_id" id="trailer_id">
						<option value="0" selected>Seleccionar Vehículo</option>
						<?php
						if(!empty($trailers)){
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
					</div>
					<div class="form-group col-xs-3">
					  <select class="form-control" name="vehicle_status" id="vehicle_status">
						<option value="0" selected>Seleccionar Estado</option>
						<?php
						if(!empty($vehiclestatus)){
							foreach($vehiclestatus as $key=>$status){
								$selected='';
								if($vehicle_status==$key){
									$selected='selected';
								}
								?>
							<option value="<?=$key?>" <?=$selected?>><?=ucwords($status)?></option>
								<?php
							}
						}
						?>
					  </select>
					</div>
				  </div>
				  <!-- /.box-body -->
				  <div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right">Filtrar</button>
				  </div>
				</form>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Número de Placa</th>
                  <th>Año de Compra</th>
                  <th>Color</th>
                  <th>Tipo de Vehículo</th>
                  <th>Carga máxima</th>
                  <th>Estado</th>
                  <th style="width:230px;">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($vehicles)){
					foreach($vehicles as $vehicle){
						$row_id = $vehicle['vehicle_id'];
						$is_blocked = $vehicle['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=$vehicle['plate_no']?></td>
                  <td><?=$vehicle['purchase_year']?></td>
                  <td><?=$vehicle['vehicle_color']?>
				  <?php if(!empty($vehicle['vehicle_color'])){
					?>
					<br><span style="display:block;border:1px solid red;width:30px;height:10px;background-color:<?=$vehicle['vehicle_color']?>">&nbsp;<span>
					<?php
				  }?></td>
                  <td><?=ucwords($vehicle['trailer_name'])?></td>
                  <td><?php 
					echo "Min: ".$vehicle['vehicle_minload']."<br>";
					echo "Max: ".$vehicle['vehicle_maxload'];
				  ?></td>
				  <td><?php
					//echo ($is_blocked)?"Blocked":"Active";
					echo vehicle_status($vehicle['vehicle_status']);
				  ?></td>
                  <td style="white-space:nowrap"><?php
					echo anchor(base_url(BASE_FOLDER_TRANS.'vehicles/edit/'.$row_id),"<i class='fa fa-edit'></i> Editar",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER_TRANS.'vehicles/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Eliminar",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'users'));
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER_TRANS.'vehicles/documents/'.$row_id),"<i class='fa fa-book'></i> Documentos",array('class'=>'btn btn-info btn-sm'));
				  ?>
				  </td>
                </tr>
						<?php
					}
				}
				?>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
	<!-- data table config loading section --->
	<script src="<?=base_url('assets/js/datatableconfig.js')?>" type="text/javascript"></script>