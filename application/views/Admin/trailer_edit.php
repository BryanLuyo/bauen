<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Trailer Edit
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="<?=base_url(BASE_FOLDER.'trailers')?>">Trailers</a></li>
        <li class="active">Trailer Edit</li>
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
                <!--div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Enter trailer name" value="<?=set_value('name',$trailer['name'])?>">
				  <?=form_error('name')?>
                </div-->
				<?php
				//print_r($trailer_names);
				if(!empty($trailer_names)){
					foreach($trailer_names as $trailer_name){
						$lng_name = $trailer_name['language_name'];
						$sort_name = $trailer_name['sort_name'];
						$language_trailer_id = $trailer_name['language_trailer_id'];
				?>
				<div class="form-group">
                  <label for="<?=$sort_name?>_name">Name(<?=ucfirst($lng_name)?>)</label>
                  <input type="text" class="form-control" id="<?=$sort_name?>_name" name="<?=$sort_name?>_name" placeholder="Enter trailer name" value="<?=set_value($sort_name.'_name',$trailer_name['trailer_name'])?>">
				  <input type="hidden" name="<?=$sort_name?>_language_trailer_id" value="<?=$language_trailer_id?>">
				  <?=form_error($sort_name.'_name')?>
                </div>
				<?php	
					}
				}
				?>
                <div class="form-group">
                  <label for="min_load">Minimum Load (Kg)</label>
                  <input type="text" class="form-control" id="min_load" name="min_load" placeholder="Enter minimum load in kg" value="<?=set_value('min_load',$trailer['min_load'])?>">
				  <?=form_error('min_load')?>
                </div>
				<div class="form-group">
                  <label for="max_load">Maximum Load (Kg)</label>
                  <input type="text" class="form-control" id="max_load" name="max_load" placeholder="Enter maximum load in kg" value="<?=set_value('max_load',$trailer['max_load'])?>">
				  <?=form_error('max_load')?>
                </div>
				<div class="form-group">
                  <label for="max_load">Image</label>
				  <?php
					if(!empty($trailer['image'])){
						?>
						<img src="<?=base_url('uploads/trailers/'.$trailer['image'])?>" width="40" height="40">
						<?php
					}
				  ?>
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