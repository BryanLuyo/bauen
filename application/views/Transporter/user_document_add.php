<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Agregar Documentos
        <small><?php 
		if(!empty($user)){
			echo ucwords($user['first_name'].' '.$user['last_name']).' ('.phoneno_format($user['phone_no']).')';
		}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<?php
		if(isset($user['is_company'])){
			if($user['is_company']=='0'){
		?>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'drivers')?>">Conductores</a></li>
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'drivers/documents/'.$user['user_id'])?>">Documentos</a></li>
		<?php
			}
		}
		else{
		?>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'documents')?>">Documentos</a></li>
		<?php
		}
		?>
        <li class="active">Agregar Documentos</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="box box-body">
        <!-- left column -->
        <div class="col-md-8">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Detalles del Documento</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="documenttype_id">Tipo de Documento</label>
				  <select class="form-control" id="documenttype_id" name="documenttype_id">
					<option value='0'>Seleccionar tipo de documento</option>
					<?php
						if(!empty($documenttypes)){
							$documenttype_id = set_value('documenttype_id');
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
				  <?=form_error('documenttype_id')?>
                </div>
				
				<div class="form-group">
                  <label for="max_load">Adjuntar Documento</label>
                  <input type="file" class="form-control" id="image" name="image">
                  <input type="hidden" class="form-control" id="imgreq" name="imgreq">
				  <?=form_error('imgreq')?>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">Enviar
                </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
		</div>
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->