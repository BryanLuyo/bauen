<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Trailers
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Trailers</li>
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
				echo anchor(base_url(BASE_FOLDER.'trailers/add'),'<i class="fa fa-plus"></i> Trailer',array('class'=>'pull-right btn btn-primary'));
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
                  <th>Load</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($trailers)){
					//print_r($trailers);
					foreach($trailers as $trailer){
						$row_id = $trailer['trailer_id'];
						$is_blocked = $trailer['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?php if(!empty($trailer['trailer_name'])){
					  echo ucwords($trailer['trailer_name']);
				  }else{
					  echo ucwords($trailer['name']);
				  }?></td>
                  <td><?php
					if(!empty($trailer['image'])){
						?>
					<img src="<?=base_url('uploads/trailers/'.$trailer['image'])?>" width="60" height="60" >
						<?php
					}
				  ?></td>
				  <td><?php
					echo "Min : ".$trailer['min_load']." Kg<br/>";
					echo "Max : ".$trailer['max_load']." Kg";
				  ?></td>
                  <td><?php
					echo anchor(base_url(BASE_FOLDER.'trailers/edit/'.$row_id),"<i class='fa fa-edit'></i> Edit",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;',1);
					if(!$trailer['is_default']){
						if($is_blocked){
							echo anchor(base_url(BASE_FOLDER.'trailers/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Unblock",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'trailers','url'=>base_url(BASE_FOLDER.'trailers/blockestatuschange/'.$row_id)));
						}
						else{
							echo anchor(base_url(BASE_FOLDER.'trailers/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Block",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'trailers','url'=>base_url(BASE_FOLDER.'trailers/blockestatuschange/'.$row_id)));
						}
						echo str_repeat('&nbsp;',1);
						echo anchor(base_url(BASE_FOLDER.'trailers/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'trailers'));
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
