<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Document Types
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Document Types</li>
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
				echo anchor(base_url(BASE_FOLDER.'documenttypes/add'),'<i class="fa fa-plus"></i> Document Type',array('class'=>'pull-right btn btn-primary'));
			  ?>
			  <!-- filter oftion -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>For</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($documenttypes)){
					foreach($documenttypes as $documenttype){
						$row_id = $documenttype['documenttype_id'];
						$is_blocked = $documenttype['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($documenttype['document_title'])?></td>
                  <td><?php
					if(isset($document_fors[$documenttype['document_for']])){
						echo ucwords($document_fors[$documenttype['document_for']]);
					}
				  ?></td>
                  <td><?php
					echo anchor(base_url(BASE_FOLDER.'documenttypes/edit/'.$row_id),"<i class='fa fa-edit'></i> Edit",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;',1);
					if($is_blocked){
						echo anchor(base_url(BASE_FOLDER.'documenttypes/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Unblock",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'documenttypes','url'=>base_url(BASE_FOLDER.'documenttypes/blockestatuschange/'.$row_id)));
					}
					else{
						echo anchor(base_url(BASE_FOLDER.'documenttypes/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Block",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'documenttypes','url'=>base_url(BASE_FOLDER.'documenttypes/blockestatuschange/'.$row_id)));
					}
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER.'documenttypes/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Delete",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'documenttypes'));
					
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
