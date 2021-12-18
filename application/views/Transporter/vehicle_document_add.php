<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Agregar Documento
        <small><?php 
		if(!empty($vehicle)){
			echo "Plate No.: ".$vehicle['plate_no']." Year : ".$vehicle['purchase_year'];
		}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'vehicles')?>">Vehículos</a></li>
		<li><a href="<?=base_url(BASE_FOLDER_TRANS.'vehicles/documents/'.$vehicle['vehicle_id'])?>">Documentos</a></li>
        <li class="active">Agregar Documento</li>
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
                  <label for="max_load">Archivo de Documento</label>
                  <input type="file" class="form-control" id="image" name="image">
                  <input type="hidden" class="form-control" id="imgreq" name="imgreq">
				  <?=form_error('imgreq')?>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enviar</button>
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