<?php
defined('BASEPATH') OR exit('No direct script access allowed');

	$controllername = $this->uri->segment(2);
	$methodname = $this->uri->segment(3);
	
	$dashboardActive = $trailerActive = $userAction = $industrytypeAction = $requestAction = $documentAction = $documenttypesAction = $loadtypeAction = '';
	
	//trailer
	$trai_list = $trai_add = '';
	// transporter (user)
	$trans_list = $user_list = '';
	// industry type 
	$instype_list = $instype_add = '';
	// load type
	$load_list = $load_add = '';
	// request 
	$req_list='';
	// document types
	$doct_type_list=$doct_type_add='';
	// documents 
	$doct_user = $doct_vehicle = '';
	
	$actClsName = $subActClsName = "active";
	switch(strtolower($controllername)){
		case 'trailers':
			$trailerActive=$actClsName;
			switch($methodname){
				case 'add':
					$trai_add=$subActClsName;
					break;
				default:
					$trai_list=$subActClsName;
					break;
			}
			break;
		case 'users':
			$userAction=$actClsName;
			switch($methodname){
				case 'transporters':
					$trans_list=$subActClsName;
					break;
				case 'drivers':
					$trans_list=$subActClsName;
					break;
				case 'vehicles':
					$trans_list=$subActClsName;
					break;
				default:
					$user_list=$subActClsName;
					break;
			}
			break;
		case 'industrytypes':
			$industrytypeAction=$actClsName;
			switch($methodname){
				case 'add':
					$instype_add=$subActClsName;
					break;
				default:
					$instype_list=$subActClsName;
					break;
			}
			break;
		case 'loadtypes':
			$loadtypeAction=$actClsName;
			switch($methodname){
				case 'add':
					$load_add=$subActClsName;
					break;
				default:
					$load_list=$subActClsName;
					break;
			}
			break;
		case 'requests':
			$requestAction=$actClsName;
			switch($methodname){
				default:
					$req_list=$subActClsName;
					break;
			}
			break;
		case 'documenttypes':
			$documenttypesAction=$actClsName;
			switch($methodname){
				case 'add':
					$doct_type_add=$subActClsName;
					break;
				default:
					$doct_type_list=$subActClsName;
					break;
			}
			break;
		case 'userdocuments':
			$documentAction=$actClsName;
			switch($methodname){
				default:
					$doct_user=$subActClsName;
					break;
			}
			break;
		case 'vehicledocuments':
			$documentAction=$actClsName;
			switch($methodname){
				default:
					$doct_vehicle=$subActClsName;
					break;
			}
			break;
		
		default:
			$dashboardActive=$actClsName;
			break;
	}
?>
  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <?php
			if($this->session->has_userdata(SES_ADMIN_ID) && $this->session->userdata(SES_ADMIN_ID)>0){
				if($this->session->userdata(SES_ADMIN_SUPER)){
					//super admin section 
					?>
					<li class="<?=$dashboardActive?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'dashboard')?>">
					<i class="fa fa-dashboard"></i> <span>Dashboard</span>
				  </a>
				</li>
				
				<li class="<?=$trailerActive?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'trailers')?>">
					<i class="fa fa-truck"></i>
					<span>Trailers</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$trai_list?>"><a href="<?=base_url(BASE_FOLDER.'trailers')?>"><i class="fa fa-circle-o"></i>List</a></li>
					<li class="<?=$trai_add?>"><a href="<?=base_url(BASE_FOLDER.'trailers/add')?>"><i class="fa fa-circle-o"></i>Add</a></li>
				  </ul>
				</li>
				
				<li class="<?=$loadtypeAction?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'loadtypes')?>">
					<i class="fa fa-cubes"></i>
					<span>Load Types</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$load_list?>"><a href="<?=base_url(BASE_FOLDER.'loadtypes')?>"><i class="fa fa-circle-o"></i>List</a></li>
					<li class="<?=$load_add?>"><a href="<?=base_url(BASE_FOLDER.'loadtypes/add')?>"><i class="fa fa-circle-o"></i>Add</a></li>
				  </ul>
				</li>
				
				<li class="<?=$industrytypeAction?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'industrytypes')?>">
					<i class="fa fa-industry"></i>
					<span>Industry Types</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$instype_list?>"><a href="<?=base_url(BASE_FOLDER.'industrytypes')?>"><i class="fa fa-circle-o"></i>List</a></li>
					<li class="<?=$instype_add?>"><a href="<?=base_url(BASE_FOLDER.'industrytypes/add')?>"><i class="fa fa-circle-o"></i>Add</a></li>
				  </ul>
				</li>
				
				<li class="<?=$documenttypesAction?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'documenttypes')?>">
					<i class="fa fa-book"></i>
					<span>Document Types</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$doct_type_list?>"><a href="<?=base_url(BASE_FOLDER.'documenttypes')?>"><i class="fa fa-circle-o"></i>List</a></li>
					<li class="<?=$doct_type_add?>"><a href="<?=base_url(BASE_FOLDER.'documenttypes/add')?>"><i class="fa fa-circle-o"></i>Add</a></li>
				  </ul>
				</li>
				
				<li class="<?=$userAction?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'users')?>">
					<i class="fa fa-users"></i>
					<span>Users</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$user_list?>"><a href="<?=base_url(BASE_FOLDER.'users')?>"><i class="fa fa-circle-o"></i>Customers</a></li>
					<li class="<?=$trans_list?>"><a href="<?=base_url(BASE_FOLDER.'users/transporters')?>"><i class="fa fa-circle-o"></i>Transporters</a></li>
				  </ul>
				</li>
				
				<li class="<?=$documentAction?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'documents')?>">
					<i class="fa fa-book"></i>
					<span>Documents</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$doct_user?>"><a href="<?=base_url(BASE_FOLDER.'userdocuments')?>"><i class="fa fa-circle-o"></i>Users</a></li>
					<li class="<?=$doct_vehicle?>"><a href="<?=base_url(BASE_FOLDER.'vehicledocuments')?>"><i class="fa fa-circle-o"></i>Vehicles</a></li>
				  </ul>
				</li>
				
				
				<li class="<?=$requestAction?> treeview">
				  <a href="<?=base_url(BASE_FOLDER.'requests')?>">
					<i class="fa fa-database"></i>
					<span>Requests</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="<?=$req_list?>"><a href="<?=base_url(BASE_FOLDER.'requests')?>"><i class="fa fa-circle-o"></i>List</a></li>
				  </ul>
				</li>
				
					<?php
				}
				else{
					// other admins  
					if($this->session->userdata(SES_ADMIN_TYPE)){
						?>
						
						<?php
					}
					else{
						// no menu found
						?>
						<?php
					}
				}
			}
		?>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>