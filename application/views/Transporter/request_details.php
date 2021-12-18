<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<section class="content-header">
      <h1>
        Detalles de la Carga
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'requests')?>">Ordenes de Servicio</a></li>
        <li class="active">Detalles de la Orden</li>
      </ol>
    </section>
	<section class="invoice">
		<div class="row invoice-info">
			<div class="col-sm-4 invoice-col">
				<h4>Cliente</h4>
			  <address>
				<b>Nombre: </b><?=ucwords($request['cus_first_name_cir'].' '.$request['cus_last_name_cir'])?><br>
				<b>Correo: </b><?=$request['cus_email_cir']?><br>
				<b>Teléfono.: </b><?=phoneno_format($request['cus_phone_no_cir'])?><br>
			  </address>
			</div>
			<div class="col-sm-4 invoice-col">
				<h4>Chofer y Vehículo</h4>
			  <address>
				<b>Conductor: </b><?=ucwords($request['driver_first_name'].' '.$request['driver_last_name'])?><br>
				<b>Cuenta: </b><?=$request['driver_email']?><br>
				<b>Teléfono: </b><?=phoneno_format($request['driver_phone_no'])?><br>

				<div title="<?= ( empty($rating) ) ? '' : $rating[0]['user_comment']; ?>">
					<b>Rating: </b> 
					<?php if( empty($rating) ) : ?>
					
					Sin calificar

					<?php else: ?>

					<?php for($i = 0; $i < $rating[0]['rating']; $i++) : ?>
						<i class="fa fa-star" style="color:#fec40e"></i>
					<?php endfor; ?>
					
					<?php if($rating[0]['rating'] < 5) : for( $i = 0; $i < (5 - $rating[0]['rating']); $i++ ) : ?>
						<i class="fa fa-star" style="color:#d2d6de"></i>
					<?php endfor; endif; ?>

					<?php endif; ?>

					<br>
				</div>

				<b>Placa: </b><?=$request['plate_no']?></br>
				<b>Color del Vehiculo: </b><?=$request['vehicle_color']?><span style="background-color:<?=$request['vehicle_color']?>">&nbsp;&nbsp;</span>
			  </address>
			</div>
			
			<!-- /.col -->
			<div class="col-sm-4 invoice-col">
				<h4>Detalles de la Orden</h4>
			  <b>ID: </b><?=$request['request_id']?><br>
			  <b>Punto de recojo: </b><?=ucwords($request['pickup_location'])?><br>
			  <b>Punto de entrega: </b><?=ucwords($request['dropoff_location'])?><br>
			  <b>Tipo de Carga: </b><?=ucwords($request['load_name'])?><br>
			  <b>Tipo de Vehículo: </b> <?=ucwords($request['trailer_name'])?><br>
			  <b>Medidas: </b> <?=ucwords($request['size'])?><br>
			  <b>Peso: </b> <?=ucwords($request['weight'])?> Tn<br>
			  <b>Fecha: </b> <?=display_date_format($request['pickup_date'])?></br>
			  <b>Hora: </b><?=$request['pickup_time']?><br>
			  <!--<b>Flete: </b><?=number_format((($request['granted_amount']>0)?$request['granted_amount']:$request['request_amount']),2)?><br>-->
			  <b>Monto Inicial: </b><?=number_format($request['request_amount'],2)?><br>
			  <b>Monto Pactado: </b><?=number_format($request['granted_amount'],2)?><br>
			  <b>Estado: </b><?=request_status($request['request_status'])?><br>
			  <b>Adicionales: </b><?=($request['additional'] != NULL) ? ucwords($request['additional']) : 'N/A'?>
			</div>
		</div>
		<div class="row invoice-info">
			<hr>
			<div class="col-sm-12 invoice-col">
			<b>Instrucciones: </b><?=$request['description']?>
			</div>
		</div>
	</section>
	
	<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
			<div class="box-header">Cotizaciones</div>
			<div class="box-body">
				<table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Transportista</th>
                  <th>Monto</th>
                  <th>Fecha de Envío</th>
				  				<th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($requestbids)){
					foreach($requestbids as $bid){
						$row_id = $bid['bid_id'];
						//$is_blocked = $bid['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($bid['trans_first_name'].' '.$bid['trans_last_name'])?></td>
                  <td><?=number_format($bid['bid_amount'],2)?></td>
                  <td><?php
					echo "Fecha : ".display_date_format($request['create_date']);
				  ?></td>
                  <td><?php
					if($request['bid_id']==$row_id){
						echo "Ganaste la propuesta";
					}
					else{
						if($request['bid_id']>0){
							echo "Perdida";
						}
						else{
							if($bid['bid_status']==1){
								echo "Aceptado por el cliente";
							}
							else{
								echo "Pendiente";
							}
						}
					}
				  ?></td>
                </tr>
						<?php
					}
				}
				?>
                </tbody>
              </table>
			</div>
		  </div>
		</div>
	  </div>
	 </section>
	 <!-- data table config loading section --->
	<script src="<?=base_url('assets/js/datatableconfig.js')?>" type="text/javascript"></script>