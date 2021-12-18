<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link rel="stylesheet" href="<?=base_url('assets/bootstrap/css/bootstrap-datepicker.min.css')?>" />
    <section class="content-header">
      <h1>
        Ordenes en Tránsito
        <small><?php
		if(!empty($user)){
			echo "de ".ucwords($user['first_name'].' '.$user['last_name']).' ('.phoneno_format($user['phone_no']).' )';
		}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Ordenes en tránsito</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php if(isset($blocked) && $blocked == true) : ?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-warning">
						Estimado transportista, debe esperar a que un administrador active su cuenta para empezar a usar nuestra app.
					</div>
				</div>
			</div>
		<?php else: ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			  <form role="form" method="post">
				<input type="hidden" name="transporter_id" value="<?=$transporter_id?>">
				<input type="hidden" name="user_id" value="<?=$user_id?>">
				  <div class="box-body">
					<div class="form-group col-xs-3">
					  <select class="form-control" name="trailer_id" id="trailer_id">
						<option value="0" selected>Seleccionar Tipo de Vehículo</option>
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
					  <select class="form-control" name="loadtype_id" id="loadtype_id">
						<option value="0" selected>Seleccionar Tipo de Carga</option>
						<?php
						if(!empty($loadtypes)){
							foreach($loadtypes as $loadtype){
								$selected='';
								if($loadtype_id==$loadtype['loadtype_id']){
									$selected='selected';
								}
								?>
							<option value="<?=$loadtype['loadtype_id']?>" <?=$selected?>><?=ucwords($loadtype['load_name'])?></option>
								<?php
							}
						}
						?>
					  </select>
					</div>
					<div class="form-group col-xs-3">
					  <select class="form-control" name="request_status" id="request_status">
						<option value="0" selected>Seleccionar Estado</option>
						<?php
						if(!empty($request_status)){
							foreach($request_status as $key=>$status){
								$selected='';
								if($key==$request_status_key){
									$selected='selected';
								}
								?>
							<option value="<?=$key; ?>" <?=$selected?>><?=ucwords($status)?></option>
								<?php
							}
						}
						?>
					  </select>
					</div>
					<div class="form-group col-xs-3">
						<input type="text" class="form-control" name="email_phone_no" id="email_phone_no" placeholder="Ingrese correo o teléfono" value="<?=$email_phone_no?>" >
					</div>
					
					<div class="form-group col-xs-3">
						<input type="text" class="form-control datepicker" name="pickup_date_from" id="pickup_date_from" placeholder="Ingresar fecha desde" value="<?=$pickup_date_from?>" >
					</div>
					
					<div class="form-group col-xs-3">
						<input type="text" class="form-control datepicker" name="pickup_date_to" id="pickup_date_to" placeholder="Ingresar fecha hasta" value="<?=$pickup_date_to?>" >
					</div>

					<div class="form-group col-xs-3">
						<input type="text" class="form-control datepicker" name="pickup_date_to" id="pickup_date_to" placeholder="Buscar por coincidencia" value="<?=$pickup_date_to?>" >
					</div>
					
					<div class="form-group col-xs-3">
						<button type="submit" class="btn btn-primary btn-block">Filtrar</button>
					</div>

				  </div>
				  <!-- /.box-body -->
				</form>
				<hr style="margin:0">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped" style="width:100%">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Lugar de Recojo</th>
                  <th>Punto de Destino</th>
                  <th>Cliente</th>
                  <th>Fecha y Hora</th>
                  <th>¿Que transportamos?</th>
                  <th>Flete</th>
                  <th>Tipo de Vehículo</th>
                  <!-- <th>Total propuestas</th> -->
									<th>Estado</th>
                  <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
								<?php 
										// echo '<pre>';
										// print_r($requests);
										// echo '</pre>';
										// echo '<pre>';
										// print_r($find_request);
										// echo '</pre>';
										// echo '<pre>';
										// print_r($assos_cond);
										// echo '</pre>';

									/*echo '<pre>';
									print_r($user['user_id']);
									echo '</pre>';*/
								if(!empty($requests)){
									
									foreach($requests as $request){
										$allowed_status = array(2, 3, 5, 6, 7, 8, 9, 10, 11, 12);

											$row_id = $request['request_id'];
											$is_blocked = $request['is_blocked'];
											$request_status = $request['request_status'];

											if( in_array($request_status, $allowed_status) ) :
											?>
											<tr>
												<td><?=$row_id?></td>
												<td><?=$request['pickup_location']?></td>
												<td><?=$request['dropoff_location']?></td>
												<td><?=$request['cus_first_name'].' '.$request['cus_last_name'] ?></td>
												<td>
												<?php
													echo "Fecha: ".display_date_format($request['pickup_date'])."<br/>";
													echo "Hora: ".$request['pickup_time'];
												?>
												</td>
												
												<td>
												<?php
													echo "Tipo: ".ucwords($request['load_name'])."<br/>";
													echo "Peso: ".$request['weight']."<br/>";
													echo "Tamaño(WxHxL) : ".$request['size'];
												?>
												</td>
												<td>
												<?php
												echo "Initial : ".number_format($request['request_amount'],2)."<br/>";
												echo "Granted : ".number_format($request['granted_amount'],2);
												?>
												</td>
												<td><?=$request['trailer_name']?></td>
												<!-- <td> -->
													<?php //echo $request['total_bids']; ?>
												<!-- </td> -->
												<td>
												<?php
												$status_txt=request_status($request_status);
												echo $status_txt;
												?>
												</td>
												<td>
												<?php
												// echo '[req_status: '.$request_status.']/ bid_status: '.$request['trans_bid_status'].'/ bid_id: '.$request['bid_id'].'/ trans_bid_id: '.$request['trans_bid_id'];
												echo anchor(base_url(BASE_FOLDER_TRANS.'requests/details/'.$row_id),"<i class='fa fa-info'></i> Detalles",array('class'=>'btn btn-warning btn-sm'));
												
												if($request_status != REQUEST_COMPLED_STATUS){
													echo str_repeat('&nbsp;','1');
													// echo anchor(base_url(BASE_FOLDER_TRANS.'chats/add/'.$row_id),"<i class='fa fa-commenting-o'></i> Chat",array('class'=>'btn btn-info btn-sm'));
												}
												
												echo str_repeat('&nbsp;','1');
												// status wise action button
												if($request['trans_bid_status']==3){
													echo "Cancelled By You";
												}
												elseif($request['trans_bid_status']==2){
													// accepted by transporter
													if($request['bid_id']==$request['trans_bid_id']){
														// now depend on the request status 
														if($request_status==3){
															// assing driver and vehicle 
															echo anchor(base_url(BASE_FOLDER_TRANS.'requests/assigndriver/'.$row_id),"<i class='fa fa-plus'></i> Asignar conductor",array('class'=>'btn btn-info btn-sm'));
														} elseif($request_status==2) {
															echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/2'),"<i class='fa fa-legal'></i> Confirmar",array('class'=>'btn btn-primary btn-sm'));
															echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/3'),"<i class='fa fa-legal'></i> Cancelar",array('class'=>'btn btn-danger btn-sm mt-5'));
														}
													}
												}
												elseif($request['trans_bid_status']==1){ // Looks like it's bad formatted
													// customer accept the bid 
													if($request['bid_id']==$request['trans_bid_id']){
														echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/2'),"<i class='fa fa-legal'></i> Confirmar",array('class'=>'btn btn-primary btn-sm'));
														echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/3'),"<i class='fa fa-legal'></i> Cancelar",array('class'=>'btn btn-danger btn-sm mt-5'));
													}
												}
												else{
													// only placed or not 
													if($request_status<=1){
														echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bids/'.$row_id),"<i class='fa fa-legal'></i> Cotizar",array('class'=>'btn btn-primary btn-sm'));
													}
												}
												?>
												</td>
											</tr>
											<?php
																		
								?>								
						<?php
						endif;
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
			<?php endif; ?>
    </section>
    <!-- /.content -->

	<!-- data table config loading section --->
	<script src="<?=base_url('assets/js/datatableconfig.js')?>" type="text/javascript"></script>
	<script src="<?=base_url('assets/bootstrap/js/bootstrap-datepicker.min.js')?>" type="text/javascript"></script>
	<script>
		$(".datepicker").datepicker({
			autoclose:true,
			format:'mm/dd/yyyy',
		});
	</script>
