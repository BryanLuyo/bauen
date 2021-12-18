<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Documentos
        <small><?php 
		if(!empty($user)){
			echo ucwords($user['first_name'].' '.$user['last_name']).' ('.phoneno_format($user['phone_no']).')';
		}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Documentos</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			  <?php 
				echo anchor(base_url(BASE_FOLDER_TRANS.'documents/add'),'<i class="fa fa-plus"></i> Documento',array('class'=>'pull-right btn btn-primary'));
			  ?>
			  <!-- filter oftion -->
			  <form role="form" method="post">
				  <div class="box-body">
					<div class="form-group col-xs-3">
					  <select class="form-control" name="documenttype_id" id="documenttype_id">
						<option value="0" selected>Seleccionar tipo de documento</option>
						<?php
						if(!empty($documenttypes)){
							foreach($documenttypes as $doctype){
								$selected='';
								if($documenttype_id==$doctype['documenttype_id']){
									$selected='selected';
								}
								?>
							<option value="<?=$doctype['documenttype_id']?>" <?=$selected?>><?=ucwords($doctype['document_title'])?></option>
								<?php
							}
						}
						?>
					  </select>
					</div>
				  </div>
				  <!-- /.box-body -->
				  <div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right">Filtrar</button>
				  </div>
				</form>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Título del Documetno</th>
                  <th>Estado del Documento</th>
                  <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($userdocuments)){
					foreach($userdocuments as $document){
						$row_id = $document['user_document_id'];
						$is_blocked = $document['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($document['document_title'])?></td>
                  <td><?php
					if(!$is_blocked){
						if($document['document_status']==1){
							echo "<span class='bg-green'>Aprobado</span>";
						}
						elseif($document['document_status']==2){
							echo "<span class='bg-red'>Rechazado</span>";
						}
						else{
							echo "<span class='bg-yellow'>Pendiente</span>";
						}
					}
					else{
						echo "Invalidado";
					}
				  ?></td>
                  <td><?php
					echo anchor(base_url(BASE_FOLDER_TRANS.'documents/view/'.$row_id),"<i class='fa fa-eye'></i>  Ver",array('class'=>'btn btn-info btn-sm'));
					
					if($is_blocked || $document['document_status']!=1){
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER.'drivers/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Eliminar",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'user_documents'));
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
