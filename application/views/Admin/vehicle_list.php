<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Vehicles
        <small><?php
			if(!empty($transporter)){
				echo "Of ".ucwords($transporter['first_name'].' '.$transporter['last_name']).'&nbsp;( '.phoneno_format($transporter['phone_no']).' )';
			}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER.'users/transporters')?>">Transporters</a></li>
        <li class="active">Vehicles</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			  <form role="form" method="post">
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
                  <th>Plate No</th>
                  <th>Purchase Year</th>
                  <th>Trailer</th>
                  <th>Action</th>
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
                  <td><?=ucwords($vehicle['trailer_name'])?></td>
				  <!--td><?php
				   echo ucwords($transporter['first_name'].' '.$transporter['last_name'])."<br/>";
					if($transporter['is_phone_no_verify']){
						echo "Phone : ".phoneno_format($transporter['phone_no'])."&nbsp;<i class='fa fa-check bg-green'></i><br/>";
					}
					else{
						echo "Phone : ".phoneno_format($transporter['phone_no'])."&nbsp;<i class='fa fa-close bg-red'></i><br/>";
					}
					
					if(!empty($transporter['email'])){
						if($transporter['is_email_verify']){
							echo "Email : ".$transporter['email']."&nbsp;<i class='fa fa-check bg-green'></i>";
						}
						else{
							echo "Email : ".$transporter['email']."&nbsp;<i class='fa fa-close bg-red'></i>";
						}
					}
					
				  ?></td-->
                  <td><?php
					if($is_blocked){
						echo anchor(base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Unblock",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'users','url'=>base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id)));
					}
					else{
						echo anchor(base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Block",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'users','url'=>base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id)));
					}
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER.'users/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'users'));
					echo str_repeat('&nbsp;',1);
				  ?>
				  <form method="post" action="<?=base_url(BASE_FOLDER.'vehicledocuments')?>">
						<input type="hidden" name="vehicle_id" value="<?=$row_id?>">
						<input type="submit" class="btn btn-success btn-sm" value="Documents">
					</form>
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