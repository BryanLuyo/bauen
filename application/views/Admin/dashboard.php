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
        Dashboard de Transportes
        <small></small>
      </h1>
      <ol class="breadcrumb" style="margin-bottom:0">
        <li><i class="fa fa-dashboard"></i> Bienvenido a Bauen Freight Web - Para usar las funciones de Tracking, deberá iniciar sesión desde su telefono con Bauen Transporter App</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Today</a></li>
              <li><a href="#timeline" data-toggle="tab">This Week</a></li>
              <li><a href="#settings" data-toggle="tab">This Month</a></li>
            </ul>
            <div class="tab-content">
              <div class=" active tab-pane" id="activity">
                <div class="row">
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['customers']?></h3>
						  <p>Total No. of Customers</p>
						</div>
						<div class="icon">
						  <i class="ion ion-person-stalker"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'users')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['transporters']?></h3>
						  <p>Total No. of Transporters</p>
						</div>
						<div class="icon">
						  <i class="ion ion-person-stalker"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'users/transporters')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['today']['requests']?></h3>

						  <p>Total No. of Requests</p>
						</div>
						<div class="icon">
						  <i class="ion ion-help-buoy"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'requests')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
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
						  <h3><?=$total_counts['week']['customers']?></h3>
						  <p>Total No. of Customers</p>
						</div>
						<div class="icon">
						  <i class="ion ion-person-stalker"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'users')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['week']['transporters']?></h3>
						  <p>Total No. of Transporters</p>
						</div>
						<div class="icon">
						  <i class="ion ion-person-stalker"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'users/transporters')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['week']['requests']?></h3>

						  <p>Total No. of Requests</p>
						</div>
						<div class="icon">
						  <i class="ion ion-help-buoy"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'requests')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
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
						  <h3><?=$total_counts['month']['customers']?></h3>
						  <p>Total No. of Customers</p>
						</div>
						<div class="icon">
						  <i class="ion ion-person-stalker"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'users')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['month']['transporters']?></h3>
						  <p>Total No. of Transporters</p>
						</div>
						<div class="icon">
						  <i class="ion ion-person-stalker"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'users/transporters')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
					  </div>
					</div>
					<!-- ./col -->
					<div class="col-lg-3 col-xs-6">
					  <!-- small box -->
					  <div class="small-box bg-secondary">
						<div class="inner">
						  <h3><?=$total_counts['month']['requests']?></h3>

						  <p>Total No. of Requests</p>
						</div>
						<div class="icon">
						  <i class="ion ion-help-buoy"></i>
						</div>
						<a href="<?=base_url(BASE_FOLDER.'requests')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
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
				<div class="box-header">All New Requests
				<?php 
				echo anchor(base_url(BASE_FOLDER.'dashboard'),'<i class="fa fa-refresh"></i> Refresh',array('class'=>'pull-right btn btn-primary'));
			  ?>
				</div>
				<div class="box-body" style="width:100%;">
				
					<table id="trnstable" class="table table-bordered table-striped"  style="width:100%;">
                <thead style="width:100%">
                <tr style="width:100%">
                  <th>ID</th>
                  <th>Pickup Address</th>
                  <th>Drop off Address</th>
                  <th>Pickup Date & Time</th>
                  <th>Load Details</th>
                  <th>Amount</th>

                  <th>Trailer</th>									
                  <th>Total Bids</th>
				  				<th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody style="width:100%">
                <?php 
				if(!empty($requests)){
					foreach($requests as $request){
									$row_id = $request['request_id'];
									$is_blocked = $request['is_blocked'];
									?>
									<tr style="width:100%">
										<td><?=$row_id?></td>
										<td><?=$request['pickup_location']?></td>
										<td><?=$request['dropoff_location']?></td>
										<td>
											<?php
											echo "Date : ".display_date_format($request['pickup_date'])."<br/>";
											echo "Time : ".$request['pickup_time'];
											?>
										</td>
										<td>
										<?php
											echo "Type : ".ucwords($request['load_name'])."<br/>";
											echo "Weight : ".$request['weight']."<br/>";
											echo "Size(WxHxL) : ".$request['size'];
										?>
										</td>
										<td>
										<?php
										echo "Initial : ".number_format($request['request_amount'],2)."<br/>";
										echo "Granted : ".number_format($request['granted_amount'],2);
										?>
										</td>
										<td><?=ucwords($request['trailer_name'])?></td>
										<td><?=$request['total_bids']?></td>
										<td>
										<?php
											$status_txt=request_status($request['request_status']);
											echo $status_txt;
											?>
										</td>                  
										<td style="width:220px;">
										<?php
										echo anchor(base_url(BASE_FOLDER.'requests/details/'.$row_id),"<i class='fa fa-info'></i> Details",array('class'=>'btn btn-info btn-sm'));
										echo str_repeat('&nbsp;',1);
										
										if(in_array($request['request_status'],array('0','1'))){
											if($is_blocked){
												echo anchor(base_url(BASE_FOLDER.'requests/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Unblock",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'requests','url'=>base_url(BASE_FOLDER.'requests/blockestatuschange/'.$row_id)));
											}
											else{
												echo anchor(base_url(BASE_FOLDER.'requests/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Block",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'requests','url'=>base_url(BASE_FOLDER.'requests/blockestatuschange/'.$row_id)));
											}
											echo str_repeat('&nbsp;',1);
											echo anchor(base_url(BASE_FOLDER.'requests/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'requests'));
										}
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
			</div>
		</div>
	</div>
	</section>
	<style>
	#trnstable{
		width:100%;
	}
	</style>
	<!-- data table config loading section --->
	<script src="<?=base_url('assets/js/datatableconfig.js')?>" type="text/javascript"></script>