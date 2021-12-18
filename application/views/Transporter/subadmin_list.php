<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Sub Adminstradores
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Sub Adminstradores</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			<?php 
				echo anchor(base_url(BASE_FOLDER_TRANS.'addsubadmin'),'<i class="fa fa-plus"></i> Sub Administradores',array('class'=>'pull-right btn btn-primary'));
			?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Imagen</th>
                  <th>Correo electrónico</th>
                  <th>Contactos</th>
				          <th>Número de DNI</th>
                  <th>Acción</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($subadmins)){
					foreach($subadmins as $user){
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
				  <td>
				  <?php
					echo "Correo Electrónico : ".$user['email']."&nbsp;<i class='fa fa-check bg-green'></i>";
				  ?>
				  </td>
				  <td><?php
					echo "Teléfono : ".phoneno_format($user['phone_no'])."&nbsp;<i class='fa fa-check bg-green'></i>";
					
				  ?></td>
				  <td><?php
					echo "DNI : ".$user['dni_no'];
				  ?></td>
                  <td style="width:250px;"><?php
					echo anchor(base_url(BASE_FOLDER_TRANS.'editsubadmin/'.$row_id),"<i class='fa fa-edit'></i> Editar",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;',1);
					if($is_blocked){
						echo anchor(base_url(BASE_FOLDER_TRANS.'transporters/blockestatuschange/'.$row_id),"<i class='fa fa-unlock'></i> Desbloquear",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'users','url'=>base_url(BASE_FOLDER_TRANS.'transporters/blockestatuschange/'.$row_id)));
					}
					else{
						echo anchor(base_url(BASE_FOLDER_TRANS.'transporters/blockestatuschange/'.$row_id.'/1'),"<i class='fa fa-lock'></i> Bloquear",array('class'=>'btn btn-warning btn-sm mr-blockunblock','tbl_name'=>'users','url'=>base_url(BASE_FOLDER_TRANS.'transporters/blockestatuschange/'.$row_id)));
					}
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER_TRANS.'transporters/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Eliminar",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'users'));
				  ?>
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
