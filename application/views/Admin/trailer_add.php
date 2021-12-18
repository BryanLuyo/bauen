<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Trailer Add
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER.'trailers')?>">Trailers</a></li>
        <li class="active">Trailer Add</li>
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
              <h3 class="box-title">Trailer details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="en_name">Name(English)</label>
                  <input type="text" class="form-control" id="en_name" name="en_name" placeholder="Enter trailer name" value="<?=set_value('en_name')?>">
				  <?=form_error('en_name')?>
                </div>
				<div class="form-group">
                  <label for="es_name">Name(Spanish)</label>
                  <input type="text" class="form-control" id="es_name" name="es_name" placeholder="Enter trailer name" value="<?=set_value('es_name')?>">
				  <?=form_error('es_name')?>
                </div>
				<!--div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Enter trailer name" value="<?=set_value('name')?>">
				  <?=form_error('name')?>
                </div-->
				
                <div class="form-group">
                  <label for="min_load">Minimum Load (Kg)</label>
                  <input type="text" class="form-control" id="min_load" name="min_load" placeholder="Enter minimum load in kg" value="<?=set_value('min_load')?>">
				  <?=form_error('min_load')?>
                </div>
				<div class="form-group">
                  <label for="max_load">Maximum Load (Kg)</label>
                  <input type="text" class="form-control" id="max_load" name="max_load" placeholder="Enter maximum load in kg" value="<?=set_value('max_load')?>">
				  <?=form_error('max_load')?>
                </div>
				<div class="form-group">
                  <label for="max_load">Image</label>
                  <input type="file" class="form-control" id="image" name="image">
                  <input type="hidden" id="imagereq" name="imagereq">
				  <?=form_error('imagereq')?>
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