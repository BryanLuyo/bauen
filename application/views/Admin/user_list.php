<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Customers
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Customers</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
			  <?php 
				//echo anchor(base_url(BASE_FOLDER.'users/add'),'<i class="fa fa-plus"></i> Customer',array('class'=>'pull-right btn btn-primary'));
			  ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Image</th>
                  <th>Contact Details</th>
				  <th>Reg. IDs</th>
                  <th>Customer Type</th>
				  <th>Company</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($users)){
					
					foreach($users as $user){
						$row_id = $user['user_id'];
						$is_blocked = $user['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($user['first_name'].' '.$user['last_name'])?></td>
                  <td><?php
					if(!empty($user['image'])){
						?>
					<img src="<?=base_url('uploads/users/'.$user['image'])?>" width="60" height="60" >
						<?php
					}
				  ?></td>
				  <td><?php
					if($user['is_phone_no_verify']){
						echo "Phone : ".phoneno_format($user['phone_no'])."&nbsp;<i class='fa fa-check bg-green'></i><br/>";
					}
					else{
						echo "Phone : ".phoneno_format($user['phone_no'])."&nbsp;<i class='fa fa-close bg-red'></i><br/>";
					}
					
					if(!empty($user['email'])){
						if($user['is_email_verify']){
							echo "Email : ".$user['email']."&nbsp;<i class='fa fa-check bg-green'></i>";
						}
						else{
							echo "Email : ".$user['email']."&nbsp;<i class='fa fa-close bg-red'></i>";
						}
					}
					
				  ?></td>
				  <td><?php
					echo "DNI : ".$user['dni_no']."<br/>";
					echo "RUC : ".$user['ruc_no'];
				  ?></td>
				  <td><?php
					if($user['is_company']){
						echo "Company";
					}
					else{
						echo "Individual";
					}
				  ?></td>
				  <td><?php
					if($user['is_company']){
						echo "Name : ".ucwords($user['company_name'])."<br/>";
						echo "Industry Type :".ucwords($user['industrytype_name'])." <br/>";
						echo "Licence No. : ".$user['company_licence_no']."<br/>";
					}
					else{
						echo "N/A";
					}
				  ?></td>
                  <td style="width:250px;"><?php
					
					if($is_blocked){
						echo anchor(base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Unblock",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'users','url'=>base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id)));
					}
					else{
						echo anchor(base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Block",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'users','url'=>base_url(BASE_FOLDER.'users/blockestatuschange/'.$row_id)));
					}
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER.'users/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'users'));
					
				  ?>
					<form method="post" action="<?=base_url(BASE_FOLDER.'requests')?>" style="display: inline;">
						<input type="hidden" name="user_id" value="<?=$row_id?>">
						<input type="submit" class="btn btn-success btn-sm" value="Requests">
					</form>
					<form method="post" action="<?=base_url(BASE_FOLDER.'userdocuments')?>" class="mt-5">
						<input type="hidden" name="user_id" value="<?=$row_id?>">
						<input type="submit" class="btn btn-primary btn-sm" value="Documents">
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
