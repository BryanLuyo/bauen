<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<section class="content-header">
      <h1>Cotizar</h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'requests')?>">Órdenes</a></li>
        <li class="active">Cotizar</li>
      </ol>
    </section>
	<section class="invoice">
		<div class="row invoice-info">
			<div class="col-sm-4 invoice-col">
			  Datos del Cliente
			  <address>
				<b>Nombre: </b><?=ucwords($request['cus_first_name_cir'].' '.$request['cus_last_name_cir'])?><br>
				<b>Correo Electrónico: </b><?=$request['cus_email_cir']?><br>
				<b>Teléfono: </b><?=phoneno_format($request['cus_phone_no_cir'])?><br>
				<b>Rating: </b>
			  </address>
			</div>
			<!-- /.col -->
			<div class="col-sm-4 invoice-col">
			  Info. de la Orden<br>
			  <b>Orden ID: </b><?=$request['request_id']?><br>
			  <b>Fecha de recojo: </b> <?=display_date_format($request['pickup_date'])?></br>
			  <b>Hora de recojo: </b><?=$request['pickup_time']?><br>
			  <b>Monto propuesto: </b><?=number_format($request['request_amount'],2)?><br>
			  <b>Total de propuestas: </b><?=$request['total_bids']?>
			</div>
			<div class="col-sm-4 invoice-col">
			  Detalles de la Carga
			  <address>
				<b>Vehículo: </b> <?=ucwords($request['trailer_name'])?><br>
				<b>Tipo de Carga: </b><?=ucwords($request['load_name'])?><br>
				<b>Peso: </b><?=$request['weight']." Kg"?><br>
				<b>Dimensiones (WxHxL): </b><?=$request['size']?><br>
			  </address>
			</div>
			<!-- /.col -->
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
			<div class="box-header">
			<?php
			if($request['request_status']>1){
				echo "<h3><center>No puede colocar/actualizar esta propuesta</center></h3>";
			}
			else{
				if(!empty($request['trans_bid_amount'])){
					echo "<h3>Su empresa ya cotizó este servicio de transporte. El monto de su propuesta es de: ".number_format($request['trans_bid_amount'],2)."</br></h3>";
				}
				else{
					echo "<h3>El monto de su propuesta es: 0.00</br></h3>";
				}
			?>
			<label>Ingrese un nuevo monto y presione "Cotizar" para actualizar el monto de la propuesta</label>
			<form role="form" method="post">
			<input type="hidden" name="bid_id" id="bid_id" value="<?=$request['trans_bid_id']?>">
			<div class="box-body">
				<div class="form-group col-xs-3">
					<input type="text" class="form-control" name="bid_amount" id="bid_amount" placeholder="Ingresar flete sin igv" value="<?=set_value('bid_amount')?>">
					<?=form_error('bid_amount')?>
				</div>
				<button type="submit" class="btn btn-primary ">Cotizar</button>
			</div>
			<!-- /.box-body -->
			<div class="box-footer"></div>
			</form>
			<?php
			}
			?>
			Propuestas de la Orden
			</div>
			<div class="box-body">
				
				<table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Detalles del transportista</th>
                  <th>Flete sin IGV</th>
                  <th>Enviada el:</th>
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
						echo "Ganaste la carga";
					}
					else{
						if($request['bid_id']>0){
							echo "Asignada a otro transportista";
						}
						else{
							if($bid['bid_status']==1){
								echo "Asignada a ti";
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