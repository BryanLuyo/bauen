<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<section class="content-header">
      <h1>Perfil de Empresa</h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Mi Perfil</li>
      </ol>
    </section>
	<section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <?php if(!empty($user['image'])): ?>
                <img class="profile-user-img img-responsive img-circle" src="<?=base_url('uploads/users/'.$user['image'])?>" alt="Foto de perfil de usuario">
              <?php else: ?>
                <img class="profile-user-img img-responsive img-circle" src="<?=base_url('assets/dist/img/avatar5.png')?>" alt="Foto de perfil de usuario">
              <?php endif; ?>
              <h3 class="profile-username text-center"><?=ucwords($user['first_name'].' '.$user['last_name'])?></h3>

              <p class="text-muted text-center"><?=ucwords($user['company_name'])?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Conductores</b> <a class="pull-right"><?=number_format($user['total_driver'],0,'',',')?></a>
                </li>
                <li class="list-group-item">
                  <b>Vehiculos</b> <a class="pull-right"><?=number_format($user['total_vehicle'],0,'',',')?></a>
                </li>
                <li class="list-group-item">
                  <b>Ordenes</b> <a class="pull-right"><?=number_format($user['total_request'],0,'',',')?></a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sobre nosotros</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <p class="text-muted">
                <?php
                echo "Correo electrónico: ".$user['email']."<br>";
                echo "Teléfono: ".phoneno_format($user['phone_no'])."<br>";
                echo "DNI: ".$user['dni_no']."<br>";
                echo "RUC: ".$user['ruc_no']."<br>";
                echo "Rating: ".$user['rating']."<br>";
                ?>
              </p>

              <hr>
              <strong><i class="fa fa-industry margin-r-5"></i> Empresa</strong>
              <p class="text-muted">
                <?php
                echo "Nombre: ".$user['company_name']."<br>";
                echo "Tipo de Industria: ".$user['industrytype_name']."<br>";
                ?>
              </p>
              <hr>
              <strong><i class="fa fa-book margin-r-5"></i> Documentos</strong>
              <p>
                <?php
                foreach($documenttypes as $documenttype){
                  $documenttype_id = $documenttype['documenttype_id'];
                  $document_title = $documenttype['document_title'];
                  $document_status = -1;
                  $document_index = array_search($documenttype_id,array_column($documents,'documenttype_id'));
                  if($document_index!==false){
                    $document_status = $documents[$document_index]['document_status'];
                  }
                  $lavel_cls='warning';
                  if($document_status==1){
                    $lavel_cls='success';
                  }
                  elseif($document_status==2){
                    $lavel_cls='danger';
                  }
                  elseif($document_status==0){
                    $lavel_cls='info';
                  }
                ?>
                <span class="label label-<?=$lavel_cls?>"><?=ucwords($document_title)?></span>
                <?php
                }
                ?>
              </p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="<?=$ins_tab?>"><a href="#activity" data-toggle="tab">Instrucciones</a></li>
              <li class="<?=$sub_tab?>"><a href="#settings" data-toggle="tab">Soporte</a></li>
            </ul>
            <div class="tab-content">
              <div class="<?=$ins_tab?> tab-pane" id="activity">
                <!-- Post -->
                <div class="post">
                  <div class="box-body pad">
                    <form method="post">
                      <textarea class="textarea" placeholder="" 
                      style="width: 100%; height: 400px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="support_instruction" id="support_instruction"><?=set_value('support_instruction',$user['support_instruction'])?></textarea>
                      <?=form_error('support_instruction')?>
                      <button type="submit" name="ins_post" value="ins_post" class="pull-right btn btn-primary mt-5">Enviar</button>
                    </form>
                  </div>
                </div>
                <!-- /.post -->
              </div>
              <!-- /.tab-pane -->

              <div class="<?=$sub_tab?> tab-pane" id="settings">
                <form class="form-horizontal" method="post">
                  <div class="form-group">
                    <label for="support_email" class="col-sm-2 control-label">Correo Electrónico</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="support_email" name="support_email" placeholder="Corroe de contacto" value="<?=set_value('support_email',$user['support_email'])?>">
					            <?=form_error('support_email')?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="support_contact" class="col-sm-2 control-label">Número de contacto</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="support_contact" id="support_contact" placeholder="Número de contacto" value="<?=set_value('support_contact',$user['support_contact'])?>">
					            <?=form_error('support_contact')?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" name="sup_post" value="sup_post" class="btn btn-primary">Enviar</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>