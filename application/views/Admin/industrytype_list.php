<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Industry Types
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Industry Types</li>
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
				echo anchor(base_url(BASE_FOLDER.'industrytypes/add'),'<i class="fa fa-plus"></i> Industry',array('class'=>'pull-right btn btn-primary'));
			  ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($industrytypes)){
					foreach($industrytypes as $industry){
						$row_id = $industry['industrytype_id'];
						$is_blocked = $industry['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($industry['industrytype_name'])?></td>
                  
                  <td><?php
					echo anchor(base_url(BASE_FOLDER.'industrytypes/edit/'.$row_id),"<i class='fa fa-edit'></i> Edit",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;',1);
					if($is_blocked){
						echo anchor(base_url(BASE_FOLDER.'industrytypes/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Unblock",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'industrytypes','url'=>base_url(BASE_FOLDER.'industrytypes/blockestatuschange/'.$row_id)));
					}
					else{
						echo anchor(base_url(BASE_FOLDER.'industrytypes/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Block",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'industrytypes','url'=>base_url(BASE_FOLDER.'industrytypes/blockestatuschange/'.$row_id)));
					}
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER.'industrytypes/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'industrytypes'));
					
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
