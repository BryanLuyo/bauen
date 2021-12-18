<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<section class="content-header">
      <h1>
        Request Details
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER.'requests')?>">Requests</a></li>
        <li class="active">Request Details</li>
      </ol>
    </section>
	<section class="invoice">
		<div class="row invoice-info">
			<div class="col-sm-3 invoice-col">
			  <h4>Customer</h4>
			  <address>
				<b>Name: </b><?=ucwords($request['cus_first_name_cir'].' '.$request['cus_last_name_cir'])?><br>
				<b>Email: </b><?=$request['cus_email_cir']?><br>
				<b>Phone no.: </b><?=phoneno_format($request['cus_phone_no_cir'])?><br>
				<!-- <b>Rating: </b> -->
			  </address>
			</div>
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
			  <h4>Transporter</h4>
			  <address>
				<b>Name: </b><?=ucwords($request['trans_first_name'].' '.$request['trans_last_name'])?><br>
				<b>Email: </b><?=$request['trans_email']?><br>
				<b>Phone no.: </b><?=phoneno_format($request['trans_phone_no'])?><br>

				<div title="<?= ( empty($rating) ) ? '' : $rating[0]['user_comment']; ?>">
					<b>Rating: </b> 
					<?php if( empty($rating) ) : ?>
					
					Unrated

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

			  </address>
			</div>
			<div class="col-sm-3 invoice-col">
			  <h4>Driver & Vehicle</h4>
			  <address>
				<b>Driver Name: </b><?=ucwords($request['driver_first_name'].' '.$request['driver_last_name'])?><br>
				<b>Email: </b><?=$request['driver_email']?><br>
				<b>Phone no.: </b><?=phoneno_format($request['driver_phone_no'])?><br>
				<b>Rating: </b> </br>
				<b>Vehicle No.: </b><?=$request['plate_no']?>
			  </address>
			</div>
			
			<!-- /.col -->
			<div class="col-sm-3 invoice-col">
			  <h4>Request</h4>
			  <b>Request ID: </b><?=$request['request_id']?><br>
				<b>Pickup Location: </b><?=ucwords($request['pickup_location'])?><br>
			  <b>Dropoff Location: </b><?=ucwords($request['dropoff_location'])?><br>
			  <b>Load Type: </b><?=ucwords($request['load_name'])?><br>
			  <b>Trailer: </b> <?=ucwords($request['trailer_name'])?><br>
				<b>Size: </b> <?=ucwords($request['size'])?><br>
			  <b>Weight: </b> <?=ucwords($request['weight'])?> Tn<br>
			  <b>Pickup Date: </b> <?=display_date_format($request['pickup_date'])?></br>
			  <b>Time: </b><?=$request['pickup_time']?><br>
			  <!--<b>Amount: </b><?=number_format((($request['granted_amount']>0)?$request['granted_amount']:$request['request_amount']),2)?><br>-->
			  <b>Inc. Amount: </b><?=number_format($request['request_amount'],2)?><br>
			  <b>Grant Amount: </b><?=number_format($request['granted_amount'],2)?><br>
			  <b>Status: </b><?=request_status($request['request_status'])?>
			</div>
		</div>
		<div class="row invoice-info">
			<hr>
			<div class="col-sm-12 invoice-col">
			<b>Instruction: </b><?=$request['description']?>
			</div>
		</div>
	</section>
	
	<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
			<div class="box-header">Request Bids</div>
			<div class="box-body">
				<table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Transporter Details</th>
                  <th>Bid Amount</th>
                  <th>Place Date</th>
				  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($requestbids)){
					$request_id = $request['request_id'];
					foreach($requestbids as $bid){
						$row_id = $bid['bid_id'];
						//$is_blocked = $bid['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($bid['trans_first_name'].' '.$bid['trans_last_name'])?></td>
                  <td><?=number_format($bid['bid_amount'],2)?></td>
                  <td><?php
					echo "Date : ".display_date_format($request['create_date']);
				  ?></td>
                  <td><?php
					if($request['bid_id']==$row_id){
						echo "Won the Bid";
					}
					else{
						if($request['bid_id']>0){
							echo "Lost";
						}
						else{
							if($bid['bid_status']==1){
								echo "Customer Accepted";
							}
							else{
								echo "Pending";
							}
						}
					}
				  ?></td>
                  
                  <td><?php
					/*if($request['bid_id']!=$row_id){
						echo anchor(base_url(BASE_FOLDER.'requests/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'request_bids'));
					}*/
					
					if($bid['bid_status']=='0' ){
						if($bid['is_admin_delete']=='0'){
							echo anchor(base_url(BASE_FOLDER.'requests/deletebid/'.$row_id.'/'.$request_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger btn-sm page_delete','tbl_name'=>'request_bids'));
						}
						else{
							echo "<span class='text-danger'>Deleted</span>";
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
	<script>
		$(document).ready(function(){
			$(".page_delete").click(function(){
				if(confirm("Are You Sure! You want to delete the record?")){
					return true;
				}
				return false;
			});
		});
	</script>