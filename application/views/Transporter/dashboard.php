<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
.bg-secondary {
	background: #222d32 !important;
    color: #fff;
}
</style>
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Panel de Transportista
      <small></small>
      </h1>
      <br>
       </section>
            <ol class="breadcrumb" style="margin-bottom:0">
        <li><i class="fa fa-dashboard"></i> Plataforma de Cotizaciones</li>
      </ol>
   

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
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
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Hoy</a></li>
              <li><a href="#timeline" data-toggle="tab">Esta Semana</a></li>
              <li><a href="#settings" data-toggle="tab">Este Mes</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <div class="row">
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['bids']?></h3>
						  <p>Cotizaciones</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0)" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['winbids']?></h3>
						  <p>Cargas ganadas</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['completedbids']?></h3>
						  <p>Servicios completados</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['lostbids']?></h3>
						  <p>Cotizaciones perdidas</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
				</div>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="timeline">
                 <div class="row">
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['week']['bids']?></h3>
						  <p>N??mero de Cotizaciones Totales</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0)" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['week']['winbids']?></h3>
						  <p>N??mero de Servicios Obtenidos</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['week']['completedbids']?></h3>
						  <p>Servicios Completos</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['week']['lostbids']?></h3>
						  <p>N??mero de Cotizaciones perdidas</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
				</div>
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">
                <div class="row">
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['month']['bids']?></h3>
						  <p>N??mero de Cotizaciones Totales</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0)" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['month']['winbids']?></h3>
						  <p>N??mero de Cargas Ganadas</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['month']['completedbids']?></h3>
						  <p>Servicios Completados</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['month']['lostbids']?></h3>
						  <p>N??mero de Cotizaciones perdidas</p>
						</div>
						<div class="icon">
						  <i class="ion ion-soup-can-outline"></i>
						</div>
						<a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
					  </div>
					</div>
				</div>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
	</div>
	<div class="row">
        <div class="col-md-12">
			<div class="box">
				<div class="box-header">
				 ORDENES DE SERVICIO DISPONIBLES PARA COTIZAR 
				<?php 
				echo anchor(base_url(BASE_FOLDER_TRANS.'dashboard'),'<i class="fa fa-refresh"></i> Actualizar',array('class'=>'pull-right btn btn-primary'));
			  ?>
				</div>
				<div class="box-body">
				<?php
					/*echo '<pre>';
						print_r($requests);
					echo '</pre>';*/
					?>
					<table id="trnstable" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>Punto de Recojo</th>
								<th>Punto de Destino</th>
								<th>Fecha y Hora</th>
								<th>??Que cargamos?</th>
								<th>Flete propuesto</th>
								<th>Tipo de Veh??culo</th>
								<th>N??mero de Postores</th>
								<th>Estado</th>
								<th>Opciones</th>
							</tr>
						</thead>
						<tbody>
                <?php 
				if(!empty($requests)){
					foreach($requests as $request){
						$row_id = $request['request_id'];
						$is_blocked = $request['is_blocked'];
						$request_status = $request['request_status'];
						?>
					<tr>
						<td><?=$row_id?></td>
						<td><?=$request['pickup_location']?></td>
						<td><?=$request['dropoff_location']?></td>
						<td><?php
							echo "Fecha: ".display_date_format($request['pickup_date'])."<br/>";
							echo "Hora: ".$request['pickup_time'];
							?></td>
            <td><?php
							echo "Tipo de Carga : ".ucwords($request['load_name'])."<br/>";
							echo "Peso: ".$request['weight']."<br/>";
							echo "Tama??o: ".$request['size'];
							?></td>
            <td><?php
							echo "Precio inicial: ".number_format($request['request_amount'],2)."<br/>";
							echo "Precio aceptado: ".number_format($request['granted_amount'],2);
							?></td>
						<td><?=$request['trailer_name']?></td>
						<td><?=$request['total_bids']?></td>
						<td><?php
							$status_txt=request_status($request['request_status']);
							echo $status_txt;
							?></td>
            <td style="width:230px;"><?php
					echo anchor(base_url(BASE_FOLDER_TRANS.'requests/details/'.$row_id),"<i class='fa fa-info'></i> Detalles",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;','1');
					//echo anchor(base_url(BASE_FOLDER_TRANS.'chats/add/'.$row_id),"<i class='fa fa-commenting-o'></i> Chat",array('class'=>'btn btn-info btn-sm'));
					//echo str_repeat('&nbsp;','1');
					// status wise action button
					if($request['trans_bid_status']==3){
						echo "Cancelado por usted.";
					}
					elseif($request['trans_bid_status']==2){
						// accepted by transporter
						if($request['bid_id']==$request['trans_bid_id']){
							// now depend on the request status 
							if($request_status==3){
								// assing driver and vehicle 
								echo anchor(base_url(BASE_FOLDER_TRANS.'requests/assigndriver/'.$row_id),"<i class='fa fa-plus'></i> Asignar conductor",array('class'=>'btn btn-primary btn-sm'));
							} elseif($request_status==2) {
								echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/2'),"<i class='fa fa-legal'></i> Aceptar propuesta",array('class'=>'btn btn-primary btn-sm'));
								echo str_repeat('&nbsp;','1');
								echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/3'),"<i class='fa fa-legal'></i> Cancelar propuesta",array('class'=>'btn btn-danger btn-sm mt-5'));
							}
						}
					}
					elseif($request['trans_bid_status']==1){
						// customer accept the bid 
						if($request['bid_id']==$request['trans_bid_id']){
							echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/2'),"<i class='fa fa-legal'></i> Aceptar propuesta",array('class'=>'btn btn-primary btn-sm'));
							echo str_repeat('&nbsp;','1');
							echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bidaccept/'.$row_id.'/3'),"<i class='fa fa-legal'></i> Cancelar propuesta",array('class'=>'btn btn-danger btn-sm mt-5'));
						}
					}
					else{
						// only placed or not 
						if($request_status<=1){
							echo anchor(base_url(BASE_FOLDER_TRANS.'requests/bids/'.$row_id),"<i class='fa fa-legal'></i> Cotizar",array('class'=>'btn btn-primary btn-sm'));
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
	<?php endif; ?>
	</section>
	<!-- data table config loading section --->
	<script src="<?=base_url('assets/js/datatableconfig.js')?>" type="text/javascript"></script>