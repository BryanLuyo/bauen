<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link rel="stylesheet" href="<?=base_url('assets/bootstrap/css/bootstrap-datepicker.min.css')?>" />
    <section class="content-header">
      <h1>
        Requests
        <small><?php
		if(!empty($user)){
			echo "Of ".ucwords($user['first_name'].' '.$user['last_name']).' ('.phoneno_format($user['phone_no']).' )';
		}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Requests</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
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
						<option value="0" selected>Select Trailer</option>
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
						<option value="0" selected>Select Load Type</option>
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
						<option value="0" selected>Select Status</option>
						<?php
						if(!empty($request_status)){
							foreach($request_status as $key=>$status){
								$selected='';
								if(($key+1)==$request_status_key){
									$selected='selected';
								}
								?>
							<option value="<?=($key+1)?>" <?=$selected?>><?=ucwords($status)?></option>
								<?php
							}
						}
						?>
					  </select>
					</div>
					<div class="form-group col-xs-3">
						<input type="text" class="form-control" name="email_phone_no" id="email_phone_no" placeholder="Enter user email/phone no." value="<?=$email_phone_no?>" >
					</div>
					
					<div class="form-group col-xs-3">
						<input type="text" class="form-control datepicker" name="pickup_date_from" id="pickup_date_from" placeholder="Enter date from" value="<?=$pickup_date_from?>" >
					</div>
					
					<div class="form-group col-xs-3">
						<input type="text" class="form-control datepicker" name="pickup_date_to" id="pickup_date_to" placeholder="Enter date to" value="<?=$pickup_date_to?>" >
					</div>
					
				  </div>
				  <!-- /.box-body -->
				  <div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right">Filter</button>
					<?php 
						//echo anchor(base_url(BASE_FOLDER.'users/add'),'<i class="fa fa-plus"></i> Worker',array('class'=>'pull-right btn btn-primary'));
					?>
				  </div>
				</form>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Pickup Address</th>
                  <th>Drop off Address</th>
									<th>Pickup Date & Time</th>
                  <th>Description</th>
                  <th>Customer</th>
									<th>Transporter Company</th>
                  <th>Load Details</th>
                  <th>Amount</th>
                  <th>Trailer</th>
                  <!-- <th>Total Bids</th> -->
				  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($requests)){
					foreach($requests as $request){
						// if($request['request_id'] == 192) {
						// 	echo '<pre>';
						// 	print_r($request);
						// 	echo '</pre>';
						// }

						$row_id = $request['request_id'];
						$is_blocked = $request['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=$request['pickup_location']?></td>
                  <td><?=$request['dropoff_location']?></td>
                  <td>
										<?php
										echo "Date : ".display_date_format($request['pickup_date'])."<br/>";
										echo "Time : ".$request['pickup_time'];
										?>
									</td>
									<td><?=$request['description']?></td>
									<td><?=$request['cus_first_name_cir']?> <?=$request['cus_last_name_cir']?></td>
									<td><?=$request['cus_company_name']?></td>
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
                  <td><?php
					$status_txt=request_status($request['request_status']);
					/*switch($request['request_status']){
						case 1:
							$status_txt='Bid Accepted By Customer';
							break;
						case 2:
							$status_txt='Transporter Accepted';
							break;
						case 3:
							$status_txt='Driver & Vehicle Assigned';
							break;
						case 4:
							$status_txt='In transit';
							break;
						case 5:
							$status_txt='Completed';
							break;
						case 6:
							$status_txt='Expired';
							break;
						default:
							$status_txt="Placed";
							break;
					}*/
					echo $status_txt;
				  ?></td>
                  
                  <td style="width:220px;"><?php
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
				  ?></td>
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
	<script src="<?=base_url('assets/bootstrap/js/bootstrap-datepicker.min.js')?>" type="text/javascript"></script>
	<script>
		$(".datepicker").datepicker({
			autoclose:true,
			format:'mm/dd/yyyy',
		});
	</script>
