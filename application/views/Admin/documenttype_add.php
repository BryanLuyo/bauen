<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Document Type Add
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER.'documenttypes')?>">Document Types</a></li>
        <li class="active">Document Type Add</li>
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
              <h3 class="box-title">Document type details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="document_title">Title</label>
                  <input type="text" class="form-control" id="document_title" name="document_title" placeholder="Enter document title" value="<?=set_value('document_title')?>">
				  <?=form_error('document_title')?>
                </div>
				<div class="form-group">
                  <label for="document_for">For</label>
                  <select name="document_for" id="document_for" class="form-control" >
					<option value="0" selected>Select Document For</option>
					<?php 
					if(!empty($document_fors)){
						foreach($document_fors as $key=>$document_for){
							$selected='';
							if($key==set_value('document_for')){
								$selected='selected';
							}
							?>
							<option value="<?=$key?>" <?=$selected?>><?=$document_for?></option>
							<?php
						}
					}
					?>
				  </select>
				  <?=form_error('document_for')?>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
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