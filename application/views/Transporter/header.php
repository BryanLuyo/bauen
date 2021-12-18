<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Bauen Transportista | Dashboard | Bauen Freight</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?=base_url('assets/bootstrap/css/bootstrap.min.css')?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url('assets/dist/css/AdminLTE.min.css?v=1.0.1')?>">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?=base_url('assets/dist/css/skins/_all-skins.min.css?v=3.0.1')?>">
  
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=base_url('assets/plugins/datatables/dataTables.bootstrap.css')?>">
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <!-- jQuery 2.2.3 -->
  <script src="<?=base_url('assets/plugins/jQuery/jquery-2.2.3.min.js')?>"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script type="text/javascript">
	var JS_BASE_URL="<?=base_url(BASE_FOLDER_TRANS)?>";
   $.widget.bridge('uibutton', $.ui.button);
  </script>
  <!-- Bootstrap 3.3.6 -->
  <script src="<?=base_url('assets/bootstrap/js/bootstrap.min.js')?>"></script>
  <!-- DataTables -->
  <script src="<?=base_url('assets/plugins/datatables/jquery.dataTables.min.js')?>"></script>
  <script src="<?=base_url('assets/plugins/datatables/dataTables.bootstrap.min.js')?>"></script>
  <style>
	.mt-5{
		margin-top:5px!important;
	}
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="javascript:void(0)" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <!-- <span class="logo-mini"><b>B</b>E</span> -->
      <span class="logo-mini"><img src="http://bauenfreigth.azurewebsites.net/transporters/public/assets/bauenfreight/images/logo.png" alt="Bauen"/></span>
      <!-- logo for regular state and mobile devices -->
      <!-- <span class="logo-lg"><b>BAUEN</b>&nbsp;FREIGHT</span> -->
      <span class="logo-lg"><img src="http://bauenfreigth.azurewebsites.net/transporters/public/assets/bauenfreight/images/logo.png" style="height:40px" alt="Bauen"/></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="javascript:void(0)" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><?=$this->session->userdata(SES_TRANS_NAME)?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
					<a href="<?=base_url(BASE_FOLDER_TRANS.'profile')?>" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="pull-right">
                  <a href="<?=base_url(BASE_FOLDER_TRANS.'logout')?>" class="btn btn-default btn-flat">Cerrar sesión</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  
  <!-- Left side column. contains the logo and sidebar -->
  <?php $this->load->view(BASE_FOLDER_TRANS.'leftpanel')?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	<div class="row">
		<div class="col-md-12 mr-alert">
	<?php
		if($this->session->flashdata('error')){
			?>
			<div class="box-body">
			  <div class="alert alert-danger alert-dismissible">
				<i class="icon fa fa-ban"></i><?=$this->session->flashdata('error')?>
			  </div>
			</div>
			<?php
		}
		elseif($this->session->flashdata('success')){
			?>
			<div class="box-body">
			  <div class="alert alert-success alert-dismissible">
				<i class="icon fa fa-check"></i><?=$this->session->flashdata('success')?>
			  </div>
			</div>
			<?php
		}
		else{
			
		}
	?>
		</div>
	</div>
