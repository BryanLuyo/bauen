<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <section class="content-header">
      <h1>
        Conductores registrados
        <small><?php
			if(!empty($transporter)){
				echo "Of ".ucwords($transporter['first_name'].' '.$transporter['last_name']).'&nbsp;( '.phoneno_format($transporter['phone_no']).' )';
			}
		?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i>Listado</a></li>
        <li class="active">Conductores</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			<?php 
				echo anchor(base_url(BASE_FOLDER_TRANS.'drivers/add'),'<i class="fa fa-plus"></i> Conductor',array('class'=>'pull-right btn btn-primary'));
			?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="trnstable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Foto</th>
                  <th>Rating</th>
                  <th>Cuentas de Conductores</th>
									<th>DNI</th>
									<th>Estado</th>
                  <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php 
				if(!empty($drivers)){
					foreach($drivers as $user){
						/*echo '<pre>';
						print_r($user);
						echo '</pre>';*/
						$row_id = $user['user_id'];
						$is_blocked = $user['is_blocked'];
						?>
				<tr>
                  <td><?=$row_id?></td>
                  <td><?=ucwords($user['first_name'].' '.$user['last_name'])?></td>
                  <td><?php
					if(!empty($user['image'])){
						?>
					<img src="<?=$user['image']?>" width="60" height="60" >
						<?php
					}
				  ?></td>
				  <td>
				  <?php
					if(!empty($user['rating'])){
						echo number_format($user['rating'],2);
					}
					else{
						echo "0.0";
					}
				  ?>
				  </td>
				  <td><?php
					//echo "Phone : ".phoneno_format($user['phone_no'])."&nbsp;<i class='fa fa-check bg-green'></i><br/>";
					
					echo "Email : ".$user['email']."&nbsp;<i class='fa fa-check bg-green'></i><br/>";
					echo "Contraseña : ".$user['showpass']."&nbsp;<i class='fa fa-check bg-green'></i>";
					
					/*if($user['is_phone_no_verify']){
						echo "Phone : ".phoneno_format($user['phone_no'])."&nbsp;<i class='fa fa-check bg-green'></i><br/>";
					}
					else{
						echo "Phone : ".phoneno_format($user['phone_no'])."&nbsp;<i class='fa fa-close bg-red'></i><br/>";
					}
					
					if(!empty($user['email'])){
						if($user['is_email_verify']){
							echo "Email : ".$user['email']."&nbsp;<i class='fa fa-check bg-green'></i>";
						}
						else{
							echo "Email : ".$user['email']."&nbsp;<i class='fa fa-close bg-red'></i>";
						}
					}*/
					
				  ?></td>
				  <td><?php
					echo $user['dni_no'];
					// echo "<br/>";
					// echo "RUC : ".$user['ruc_no'];
				  ?></td>
				  <td><?php
					//echo ($is_blocked)?"Blocked":"Active";
					echo user_status($user['user_status']);
				  ?></td>
                  <td style="white-space:nowrap"><?php
					echo anchor(base_url(BASE_FOLDER_TRANS.'drivers/edit/'.$row_id),"<i class='fa fa-edit'></i> Editar",array('class'=>'btn btn-info btn-sm'));
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER_TRANS.'drivers/deletetblrecord/'.$row_id),"<i class='fa fa-trash'></i> Eliminar",array('class'=>'btn btn-danger mr-deleterow btn-sm','tbl_name'=>'users'));
					echo str_repeat('&nbsp;',1);
					echo anchor(base_url(BASE_FOLDER_TRANS.'drivers/documents/'.$row_id),"<i class='fa fa-book'></i> Documentos",array('class'=>'btn btn-info btn-sm'));
				  ?>
					<form method="post" action="<?=base_url(BASE_FOLDER_TRANS.'requests')?>" style="display:inline-block">
						<input type="hidden" name="driver_id" value="<?=$row_id?>">
						<input type="submit" class="btn btn-success btn-sm" value="Órdenes">
					</form>
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
