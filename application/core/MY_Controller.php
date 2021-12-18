<?php 
header("Access-Control-Allow-Origin: *");
defined('BASEPATH') OR exit('No direct script access allowed');

define('SES_ADMIN_ID','tnsp_admin_id');
define('SES_ADMIN_NAME','tnsp_admin_name');
define('SES_ADMIN_SUPER','tnsp_admin_super');
define('SES_ADMIN_TYPE', 'tnsp_admin_type');
define('BASE_FOLDER', 'Admin/');
define('APP_LANG', '2'); // 1=Eng

define('SES_TRANS_ID', 'tnsp_trans_id');
define('SES_TRANS_NAME', 'tnsp_trans_name');
define('SES_TRANS_SUPER', 'tnsp_trans_super');
define('SES_TRANS_TYPE', 'tnsp_trans_type');
define('SES_TRANS_VERIFY', 'tnsp_trans_verify');
define('SES_TRANS_BLOCKED', 'tnsp_trans_blocked');
define('BASE_FOLDER_TRANS', 'Transporter/');
define('SES_CREATOR_ID', 'tnsp_creator_id');

//request status global section
define('REQUEST_COMPLED_STATUS', '13');
define('DEFAULT_TRUCK_COLOR', '#401ad9');

class MY_Controller extends CI_Controller {
	public $logged_user;
	public $dbprefix;
	public $language_id;
	public $dateformat;
	public $tableNameAdmin;
	public $tableNameTrailer;
	public $tableNameUser;
	public $tableNameUserTransporter;
	public $tableNameIndustryType;
	public $tableNameRequest;
	public $tableNameVehicle;
	public $tableNameDocumentType;
	public $tableNameUserDocument;
	public $tableNameVehicleDocument;
	public $tableNameUserRequestKey;
	public $tableNameUserRating;
	public $tableNameLoadType;
	public $tableNameRequestBid;
	public $tableNameChat;
	public $tableNameMessage;
	public $tableNameUserDeleteMessage;
	
	public $tableNameLanguage;
	public $tableNameLanguageTrailer;
	public $tableNameLanguageLoadType;
	public $tableNameVehicleImage;
	public $tableNameRequestDriverLocation;
	public $tableNameRequestNotification;
	public $tableNameUserCount;
	// country calling code 
	public $countryCallingCode;
	
	function __construct(){
		parent::__construct();

		$this->load->model('Base_model','BaseModel');
		$this->dbprefix="trns_";
		$this->dateformat=date("Y-m-d H:i:s");
		$this->language_id=2; // default for english
		//
		$this->tableNameAdmin="admins";
		$this->tableNameTrailer="trailers";
		$this->tableNameUser="users";
		$this->tableNameUserTransporter="user_transporter";
		$this->tableNameIndustryType="industrytypes";
		$this->tableNameRequest="requests";
		$this->tableNameVehicle="vehicles";
		$this->tableNameDocumentType="documenttypes";
		$this->tableNameUserDocument="user_documents";
		$this->tableNameVehicleDocument="vehicle_documents";
		$this->tableNameUserRequestKey="user_request_keys";
		$this->tableNameUserRating="user_ratings";
		$this->tableNameLoadType="loadtypes";
		$this->tableNameRequestBid="request_bids";
		$this->tableNameChat="chats";
		$this->tableNameMessage="messages";
		$this->tableNameUserDeleteMessage="user_delete_messages";
		$this->tableNameLanguageTrailer="language_trailers";
		$this->tableNameLanguageLoadType="language_loadtypes";
		$this->tableNameVehicleImage="vehicle_images";
		$this->tableNameRequestDriverLocation="request_driver_locations";
		$this->tableNameRequestDriverLocationInTime="request_driver_locations_in_time";
		$this->tableNameRequestNotification="request_notifications";
		$this->tableNameLanguage="languages";
		$this->tableNameUserCount="user_counts";
		
		$this->countryCallingCode="+51"; //hard coded
	}

	public function debugcontroller($data){
		$log   = "____________________________________________________________________________________________________";
		$log  .= "\n";
		if(is_array($data)) {
			$log.=json_encode($data).PHP_EOL;
		} else {
			$log.=$data.PHP_EOL;
		}
		file_put_contents('./logs/debugController_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
		return true;
	}
	
	public function pr($data=array()){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
	// admin 
	public function loadview($view_name='',$view_data=array()){
		$view_array=array('login','resetpassword');
		if(in_array($view_name,$view_array)){
			// with out login 
			$this->load->view(BASE_FOLDER.'header_one');
			$this->load->view(BASE_FOLDER.$view_name,$view_data);
			$this->load->view(BASE_FOLDER.'footer_one');
		}
		else{
			// with login
			$this->load->view(BASE_FOLDER.'header');
			$this->load->view(BASE_FOLDER.$view_name,$view_data);
			$this->load->view(BASE_FOLDER.'footer');
		}
	}
	
	//admin session set 
	public function admin_session_set($adminuser=array()){
		if(!empty($adminuser)){
			$admin_id = isset($adminuser['admin_id'])?$adminuser['admin_id']:0;
			$is_superadmin = isset($adminuser['is_super_admin'])?$adminuser['is_super_admin']:0;
			$admin_name = isset($adminuser['name'])?$adminuser['name']:'';
			$admin_type = isset($adminuser['admin_type'])?$adminuser['admin_type']:'0';
			if($admin_id>0){
				$this->session->set_userdata(SES_ADMIN_ID,$admin_id);
				$this->session->set_userdata(SES_ADMIN_SUPER,$is_superadmin);
				$this->session->set_userdata(SES_ADMIN_NAME,$admin_name);
				$this->session->set_userdata(SES_ADMIN_TYPE,$admin_type);
			}
		}
	}
	
	public function admin_session_destroy(){
		if($this->session->has_userdata(SES_ADMIN_ID) && $this->session->userdata(SES_ADMIN_ID)>0){
			$this->session->unset_userdata(SES_ADMIN_ID);
			$this->session->unset_userdata(SES_ADMIN_SUPER);
			$this->session->unset_userdata(SES_ADMIN_NAME);
			$this->session->unset_userdata(SES_ADMIN_TYPE);
		}
		redirect(BASE_FOLDER.'login');
	}
	
	//admin session checked 
	public function admin_session_on(){
		if($this->session->has_userdata(SES_ADMIN_ID) && $this->session->userdata(SES_ADMIN_ID)>0){
			redirect(BASE_FOLDER.'dashboard');
		}
	}
	
	public function admin_session_off(){
		if(!$this->session->has_userdata(SES_ADMIN_ID) || $this->session->userdata(SES_ADMIN_ID)<=0){
			$this->session->set_flashdata('error','Your session has expired. Please login again');
			redirect(BASE_FOLDER.'login');
		}
		else{
			$this->permissioncheck();
		}
	}
	
	// TRANSPORTER SECTION 
	
	// transporter view load 
	public function loadviewtrans($view_name='',$view_data=array()){
		$view_array=array('login','verification','resetpassword');
		if(in_array($view_name,$view_array)){
			// with out login 
			$this->load->view(BASE_FOLDER_TRANS.'header_one');
			$this->load->view(BASE_FOLDER_TRANS.$view_name,$view_data);
			$this->load->view(BASE_FOLDER_TRANS.'footer_one');
		}
		else{
			// with login
			$this->load->view(BASE_FOLDER_TRANS.'header');
			$this->load->view(BASE_FOLDER_TRANS.$view_name,$view_data);
			$this->load->view(BASE_FOLDER_TRANS.'footer');
		}
	}
	
	// transporter section 
	public function trans_session_validate_current_user(){
		$this->session->set_userdata(SES_TRANS_VERIFY, 1);
	}

	public function trans_session_set($adminuser=array()){
		if(!empty($adminuser)){
			$admin_id = isset($adminuser['user_id'])?$adminuser['user_id']:0;
			$super_parent_id = isset($adminuser['super_parent_id'])?$adminuser['super_parent_id']:0;
			$is_superadmin = isset($adminuser['is_company'])?$adminuser['is_company']:0;
			$admin_name = isset($adminuser['first_name'])?$adminuser['first_name']:'';
			$admin_type = isset($adminuser['user_type'])?$adminuser['user_type']:'0';
			$is_verify = isset($adminuser['is_user_verify'])?$adminuser['is_user_verify']:0;
			$is_blocked = isset($adminuser['is_blocked'])?$adminuser['is_blocked']:2;
			if($admin_id>0){
				$creator_id=$admin_id;
				if($super_parent_id>0){
					$admin_id = $super_parent_id;
				}
				$this->session->set_userdata(SES_TRANS_ID,$admin_id);
				$this->session->set_userdata(SES_CREATOR_ID,$creator_id);
				$this->session->set_userdata(SES_TRANS_SUPER,$is_superadmin);
				$this->session->set_userdata(SES_TRANS_VERIFY,$is_verify);
				$this->session->set_userdata(SES_TRANS_NAME,$admin_name);
				$this->session->set_userdata(SES_TRANS_TYPE,$admin_type);
				$this->session->set_userdata(SES_TRANS_BLOCKED,$is_blocked);
			}
		}
	}
	
	public function trans_session_destroy(){
		if($this->session->has_userdata(SES_TRANS_ID) && $this->session->userdata(SES_TRANS_ID)>0){
			$this->session->unset_userdata(SES_TRANS_ID);
			$this->session->unset_userdata(SES_CREATOR_ID);
			$this->session->unset_userdata(SES_TRANS_SUPER);
			$this->session->unset_userdata(SES_TRANS_NAME);
			$this->session->unset_userdata(SES_TRANS_TYPE);
		}
		redirect(BASE_FOLDER_TRANS.'login');
	}
	
	// transporter session checked 
	public function trans_session_unverified(){
		if(
			!$this->session->has_userdata(SES_TRANS_ID) || 
			$this->session->userdata(SES_TRANS_ID)<=0 || 
			!$this->session->has_userdata(SES_TRANS_VERIFY) || 
			$this->session->userdata(SES_TRANS_VERIFY) != 0
		){
			redirect(BASE_FOLDER_TRANS.'dashboard?verified=true');
		}
	}

	public function trans_session_on(){
		if($this->session->has_userdata(SES_TRANS_ID) && $this->session->userdata(SES_TRANS_ID)>0){
			if($this->session->userdata(SES_TRANS_VERIFY) == 0) {
				redirect(BASE_FOLDER_TRANS.'verification');
			} else { // ESTE ELSE NO DEBERIA IR
				redirect(BASE_FOLDER_TRANS.'dashboard?verified=true');
			}
		}
	}
	
	public function trans_session_off(){
		if(!$this->session->has_userdata(SES_TRANS_ID) || $this->session->userdata(SES_TRANS_ID)<=0){
			$this->session->set_flashdata('error','Your session has expired. Please login again');
			redirect(BASE_FOLDER_TRANS.'login');
		}
		elseif($this->session->userdata(SES_TRANS_VERIFY) == 0) {
			redirect(BASE_FOLDER_TRANS.'verification');
		} else {
			$this->trans_permissioncheck();
		}
	}
	
	// PERMISSION CHECKED SECTION 
	
	public function admin_roles(){
		$admin_roles=array(
			'1'=>'Data Entry'
		);
		return $admin_roles;
	}
	// ADMIN PANEL
	public function permissioncheck(){
		$is_ajaxcall = $this->input->post('is_ajax');
		$controller = strtolower($this->uri->segment(2));
		$action = strtolower($this->uri->segment(3));
		$default=BASE_FOLDER.'dashboard';
		$cpt_admin_type = $this->session->userdata(SES_ADMIN_TYPE);
		$permissiondata = $this->permissiondata();




		//$this->pr($permissiondata);
		if(isset($permissiondata[$controller][$cpt_admin_type])){
			$permissions = $permissiondata[$controller][$cpt_admin_type];
			//$this->pr($permissions);
			if(is_array($permissions)){
				$action = (empty($action))?'index':$action;
				if(!in_array($action,$permissions)){
					if($is_ajaxcall){
						die(json_encode(array('status'=>'0','message'=>'Permission denied ! (292)')));
					}
					$this->session->set_flashdata('error','Permission denied ! (292)');
					redirect($default);
				}
			}
			else{
				if(!$permissions){
					if($is_ajaxcall){
						die(json_encode(array('status'=>'0','message'=>'Permission denied ! (301)')));
					}
					$this->session->set_flashdata('error','Permission denied ! (301)');
					redirect($default);
				}
			}
		}
		else{
			if($is_ajaxcall){
				die(json_encode(array('status'=>'0','message'=>'Permission denied ! (310)')));
			}
			$this->session->set_flashdata('error','Permission denied ! (310)');
			redirect($default);
		}
	}
	
	// TRANSPORTER SECTION 
	public function trans_permissioncheck(){
		$is_ajaxcall = $this->input->post('is_ajax');
		$controller = strtolower($this->uri->segment(2));
		$action = strtolower($this->uri->segment(3));
		$default=BASE_FOLDER_TRANS.'dashboard';
		$cpt_admin_type = $this->session->userdata(SES_TRANS_TYPE);
		$permissiondata = $this->permissiondata();
		
		if(isset($permissiondata[$controller][$cpt_admin_type])){
			$permissions = $permissiondata[$controller][$cpt_admin_type];
			if(is_array($permissions)){
				$action = (empty($action))?'index':$action;
				if(!in_array($action,$permissions)){
					if($is_ajaxcall){
						die(json_encode(array('status'=>'0','message'=>'Permission denied ! (332)')));
					}
					$this->session->set_flashdata('error','Permission denied ! (332)');
					redirect($default);
				}
			}
			else{
				if(!$permissions){
					if($is_ajaxcall){
						die(json_encode(array('status'=>'0','message'=>'Permission denied ! (341)')));
					}
					$this->session->set_flashdata('error','Permission denied ! (341)');
					redirect($default);
				}
			}
		}
		else{
			if($is_ajaxcall){
				die(json_encode(array('status'=>'0','message'=>'Permission denied ! (350)')));
			}
			$this->session->set_flashdata('error','Permission denied ! (350)');
			redirect($default);
		}
	}
	
	public function permissiondata(){
		// 0=super admin, 1=data entry user
		$permissions=array(
			'admins'=>array(
				'0'=>array('logout','dashboard'),
				'1'=>array('logout','dashboard')
			),
			'trailers'=>array(
				'0'=>array('index','add','edit','blockestatuschange','blockunblockdelete','deletetblrecord'),
				'1'=>array()
			),
			'users'=>array(
				'0'=>array('index','transporters','drivers','vehicles','blockestatuschange','blockunblockdelete','deletetblrecord'),
				'1'=>array('blockestatuschange','deletetblrecord','blockunblockdelete')
			),
			'industrytypes'=>array(
				'0'=>array('index','add','edit','blockestatuschange','blockunblockdelete','deletetblrecord'),
				'1'=>array()
			),
			'requests'=>array(
				'0'=>array('index','details','deletebid','blockestatuschange','deletetblrecord','blockunblockdelete','completed'),
				'1'=>array('index','details','bids','bidaccept','assigndriver','completed')
			),
			'documenttypes'=>array(
				'0'=>array('index','add','edit','blockestatuschange','blockunblockdelete','deletetblrecord'),
				'1'=>array()
			),
			'userdocuments'=>array(
				'0'=>array('index','view','blockestatuschange','deletetblrecord'),
				'1'=>array()
			),
			'vehicledocuments'=>array(
				'0'=>array('index','view','blockestatuschange','deletetblrecord'),
				'1'=>array()
			),
			'vehicle_documents'=>array(
				'0'=>array('blockunblockdelete'),
				'1'=>array('blockunblockdelete')
			),
			'user_documents'=>array(
				'0'=>array('blockunblockdelete'),
				'1'=>array()
			),
			'transporters'=>array(
				'0'=>array('blockestatuschange','deletetblrecord','blockunblockdelete'),
				'1'=>array('blockestatuschange','deletetblrecord','blockunblockdelete'),
			),
			'drivers'=>array(
				'0'=>array(),
				'1'=>array('index','add','edit','documents','adddocument','viewdocument'),
			),
			'vehicles'=>array(
				'0'=>array(),
				'1'=>array('index','add','edit','documents','adddocument','viewdocument','deletetblrecord'),
			),
			'documents'=>array(
				'0'=>array(),
				'1'=>array('index','add','view')
			),
			'loadtypes'=>array(
				'0'=>array('index','add','edit','blockestatuschange','deletetblrecord','blockunblockdelete'),
				'1'=>array(),
			),
			'chats'=>array(
				'0'=>array(),
				'1'=>array('index','add','messages','addmessage'),
			),
			'dashboard'=>array('0'=>'1','1'=>'1'),
			'propuestas'=>array('0'=>'1','1'=>'1'),
			'logout'=>array('0'=>'1','1'=>'1'),
			'profile'=>array('0'=>'0','1'=>'1'),
			'addsubadmin'=>array('0'=>'0','1'=>'1'),
			'subadmins'=>array('0'=>'0','1'=>'1'),
			'editsubadmin'=>array('0'=>'0','1'=>'1'),
		);
		return $permissions;
	}
	
	//row delete and blocked un blocked
	
	// all table blocke status change
	public function blockestatuschange($table_id=0,$is_blocked=0){
		$is_ajaxpost=0;
		if($this->input->server('REQUEST_METHOD')=='POST'){			
			echo '<pre>';
			print_r(array('Hola','Como','estas'));
			echo '</pre>';			
			$is_ajaxpost=$this->input->post('is_ajax');
			$tbl_name=$this->input->post('tbl_name');
			if($table_id>0){
				if(!$this->action_permission_checked($tbl_name,$table_id,$is_delete=0)){
					die(json_encode(array('status'=>'0','message'=>'Table Row Status changed Permission denied !')));
				}else{					
					//do the operation
					$updatedata=array(
						'create_date'=>$this->dateformat,
						'is_blocked'=>$is_blocked
					);
					$primary_key=$this->primary_key($tbl_name);
					$conditions = array(
						$primary_key=>$table_id
					);
					
					$this->BaseModel->updateDatas($tbl_name,$updatedata,$conditions);
					//return type
					$is_blocked = ($is_blocked)?0:1;
					die(json_encode(array('status'=>'1','message'=>'Table Row Publish status changed successfully','is_blocked'=>$is_blocked)));
				}
			}
			else{
				die(json_encode(array('status'=>'0','message'=>'Table Row information not set')));
			}
		}
		else{
			$this->session->set_flashdata('error','Unauthenticated access');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function deletetblrecord($table_id=0){
		$is_ajaxpost=0;
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$is_ajaxpost=$this->input->post('is_ajax');
			$tbl_name=$this->input->post('tbl_name');
			if($table_id>0){
				if(!$this->action_permission_checked($tbl_name,$table_id,$is_delete=1)){
					// false 
					die(json_encode(array('status'=>'0','message'=>'Table Row deleted Permission denied !')));
				}
				else{
					//do the operation 
					$updatedata=array(
						'deleted_date'=>$this->dateformat,
						'is_deleted'=>'1'
					);
					$primary_key=$this->primary_key($tbl_name);
					$conditions = array(
						$primary_key=>$table_id
					);
					
					$this->BaseModel->updateDatas($tbl_name,$updatedata,$conditions);
					//return type
					die(json_encode(array('status'=>'1','message'=>'Table Row deleted successfully')));
				}
			}
			else{
				die(json_encode(array('status'=>'0','message'=>'Table Row information not set')));
			}
		}
		else{
			$this->session->set_flashdata('error','Unauthenticated access');
		}
		// redirect to the referrer url 
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function action_permission_checked($tbl_name='',$table_id=0,$is_delete=0){

		if($this->session->has_userdata(SES_ADMIN_SUPER)){



			//checked module permission 
			if(permissionchecked($tbl_name,'blockunblockdelete')){
				
				$primary_key=$this->primary_key($tbl_name);
				if(empty($primary_key)){
					return false;
				}
				$conditions = array(
					$primary_key=>$table_id
				);
				
				$row_details  = $this->BaseModel->getData($tbl_name,$conditions,$from_fields=array('is_blocked'));
				if(!empty($row_details)){
					if($this->session->userdata(SES_ADMIN_SUPER)==1){
						return true;
					}
					else{
						if($is_delete){
							if($row_details['is_blocked']){
								return true;
							}
						}
					}
				}
			}
		}
		elseif($this->session->has_userdata(SES_TRANS_SUPER)){

			//checked module permission		
			if(permissionchecked($tbl_name,'blockunblockdelete')){
				$primary_key=$this->primary_key($tbl_name);
				
				if(empty($primary_key)){
					return false;
				}
				$conditions = array(
					$primary_key=>$table_id
				);
				
				$row_details  = $this->BaseModel->getData($tbl_name,$conditions,$from_fields=array('is_blocked'));
				if(!empty($row_details)){
					if($this->session->userdata(SES_TRANS_SUPER)==1){
						return true;
					}
					else{
						if($is_delete){
							if($row_details['is_blocked']){
								return true;
							}
						}
					}
				}
			}
			else{
				//die(json_encode(array('status'=>'0','message'=>'Delete permission does not available')));
			}
		}
		return false;
	}
	
	public function primary_key($table_name=''){
		$tables=array(
			'trailers'=>'trailer_id',
			'industrytypes'=>'industrytype_id',
			'requests'=>'request_id',
			'documenttypes'=>'documenttype_id',
			'user_documents'=>'user_document_id',
			'vehicle_documents'=>'vehicle_document_id',
			'users'=>'user_id',
			'loadtypes'=>'loadtype_id'
		);
		return (isset($tables[$table_name]))?$tables[$table_name]:'';
	}
	
	
	public function user_unique_email($email='',$id=0){
		if(!empty($email)){
			$find_cond=array(
				'email'=>$email,
			);
			if(!empty($id)){
				$find_cond['user_id !=']=$id;
			}
			$row = $this->BaseModel->tableRow($this->tableNameUser,$find_cond);
			if($row){
				return false;
			}
		}
		return true;
	}
	
	public function valid_phone_no($phone_no=''){
		if(!empty($phone_no)){
			$len = strlen($phone_no);
			//if(in_array($len,array('9','10','11','12','13'))){
			if(in_array($len,array('9'))){
				if(is_numeric($phone_no)){
					if(strpos($phone_no,'-')!==false){
						return false;
					}
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		return true;
	}
	
	public function user_unique_phone_no($phone_no='',$id=0){
		if(!empty($phone_no)){
			$find_cond = array(
				'phone_no'=>$phone_no
			);
			if($id>0){
				$find_cond['user_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameUser,$find_cond);
			if(!empty($tableRow)){
				return false;
			}
		}
		return true;
	}
	
	
	// common section 
	public function getdocument_for(){
		$getdocument_for=array(
			'1'=>'User',
			'2'=>'Vehicle'
		);
		return $getdocument_for;
	}
	
	public function getdocumenttypes($find_cond=array()){
		$select_flds=array('document_title','documenttype_id');
		$documenttypes = $this->BaseModel->getDatas($this->tableNameDocumentType,$find_cond,$select_flds);
		return $documenttypes;
	}
	
	public function getindustrytypes($find_cond=array()){
		$select_fields=array('industrytype_id','industrytype_name');
		$industrytypes = $this->BaseModel->getDatas($this->tableNameIndustryType,$find_cond,$select_fields);
		return $industrytypes;
	}

	public function getloadtypes($find_cond=array(),$select_fields=array()){
		if(empty($select_fields)){
			$select_fields=array('loadtype_id','load_name');
		}
		$joins=array(
			array(
				'table_name'=>$this->tableNameLanguageLoadType,
				'join_with'=>$this->tableNameLoadType,
				'join_with_alias'=>'',
				'join_type'=>'left',
				'join_on'=>array('loadtype_id'=>'loadtype_id'),
				'oncond'=>array('is_blocked'=>'0','is_deleted'=>'0','language_id'=>$this->language_id),
				'select_fields'=>'IFNULL(loadtype_name,load_name) load_name'
			)
		);
		
		$orderBy = array('load_name' => 'ASC');
		
		$loadtypes = $this->BaseModel->getDatas($this->tableNameLoadType,$find_cond,$select_fields,$orderBy,$joins);
		return $loadtypes;
	}
	
	
	public function getrequests($find_cond=array(),$assos_cond=array(),$offset=0,$limit=0){
		$select_flds=array();
		$order_by=array('pickup_date,pickup_time'=>'DESC');
		$is_count=false;
		// driver lat long last position 
		$DR=$this->dbprefix.$this->tableNameRequestDriverLocation;
		//$DR=$this->tableNameRequestDriverLocation;
		$RQ=$this->dbprefix.$this->tableNameRequest;
		$TU=$this->dbprefix.$this->tableNameUser;
		$BD=$this->dbprefix.$this->tableNameRequestBid;
		
		
		$request_id = (isset($find_cond['request_id']))?$find_cond['request_id']:0;
		$transporter_id = (isset($find_cond['transporter_id']))?$find_cond['transporter_id']:0;
		$tranporterFilter = (isset($find_cond['tranporterFilter']))?$find_cond['tranporterFilter']:array();
		
		if($request_id>0){
			$select_flds['special_select_field']="IFNULL((SELECT concat(`$DR`.`latitude`,',',`$DR`.`longitude`) FROM $DR WHERE $DR.request_id=$request_id ORDER BY driver_location_id DESC limit 0,1),'') as driver_location,
			                                      IFNULL((SELECT COUNT($BD.request_id) FROM $BD , $TU 
														  WHERE $BD.user_id=$TU.user_id 
														  AND $BD.is_deleted='0' 
														  AND $TU.is_deleted='0'
														  AND $BD.request_id = $request_id), 0) AS total_bids";
		}
		else{
			$select_flds['special_select_field']="IFNULL((SELECT concat(`$DR`.`latitude`,',',`$DR`.`longitude`) FROM $DR WHERE $DR.request_id=$RQ.request_id ORDER BY driver_location_id DESC limit 0,1),'') as driver_location,
			                                      IFNULL((SELECT COUNT($BD.request_id) FROM $BD , $TU 
														  WHERE $BD.user_id=$TU.user_id 
														  AND $BD.is_deleted='0' 
														  AND $TU.is_deleted='0'
														  AND $BD.request_id = $RQ.request_id), 0) AS total_bids";
		}
		
		//AND $DR.user_id=driver_id
		/*$complexCondition=array(
			'DATE('.$this->dbprefix.$this->tableNameRequest.'.create_date)'=>date('Y-m-d')
		);*/
		
		$complexCondition=array();
		
		$trans_bid_cond=array();
		
		if(isset($find_cond['complexCondition'])){
			$complexCondition=$find_cond['complexCondition'];
			unset($find_cond['complexCondition']);
		}
		
		// request status section 
		if(!empty($find_cond['request_status'])){
			$request_status = $find_cond['request_status'];
			if(strpos($request_status,'*')>=0){
				/*$request_status = explode("*",$request_status);
				$find_cond['request_status'] = $request_status;*/
				$find_cond['request_status'] = explode("*",$request_status);
			}
		}
		
		// associate conditions 
		//custome select fields 
		if(isset($assos_cond['fields']) && !empty($assos_cond['fields'])){
			$select_flds = $assos_cond['fields'];
		}
		// custome order order_by 
		if(isset($assos_cond['order_by']) && !empty($assos_cond['order_by'])){
			$order_by = $assos_cond['order_by'];
		}
		//only count return 
		if(isset($assos_cond['count']) && !empty($assos_cond['count'])){
			$is_count = $assos_cond['count'];
		}
		
		// transporter bid details with request 
		$trans_bid_assos=array();
		$bid_assos=array();
		$bid_join_type="left";
		
		if(isset($assos_cond['trans_bid_cond']) && !empty($assos_cond['trans_bid_cond'])){
			$trans_bid_cond = $assos_cond['trans_bid_cond'];
		}
		if(isset($assos_cond['trans_bid_assos']) && !empty($assos_cond['trans_bid_assos'])){
			$trans_bid_assos = $assos_cond['trans_bid_assos'];
		}
		if(isset($assos_cond['bid_assos']) && !empty($assos_cond['bid_assos'])){
			$bid_assos = $assos_cond['bid_assos'];
			$bid_join_type="inner";
		}
		
		// transporter filter for customer
		
		$tfc_assos=array();
		$tfc_join_type="left";
		
		if(isset($assos_cond['tfc_assos']) && !empty($assos_cond['tfc_assos'])){
			$tfc_assos = $assos_cond['tfc_assos'];
			$tfc_join_type="inner";
		}
		
		
		
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>array('first_name cus_first_name','last_name cus_last_name','email cus_email','phone_no cus_phone_no','image cus_image', 'firebase_id cus_firebase_id', 'company_name cus_company_name'),
				//hlm 17/06/20 condicion para usuario bloqueado
				//'conditions'=>array('is_blocked' => 2)
			),
			
			//hlm 03/08/2020   -- Se crea para filtrar usuarios transporter
			
		    array(
				'table_name'=>$this->tableNameUserTransporter,
				'join_with'=>$this->tableNameRequest,
				'join_type'=>$tfc_join_type,
				'join_on'=>array('user_id'=>'id_customer'),
				'select_fields'=>array('id_transporter'),
				'conditions'=>$tfc_assos
			),
			
            //hlm 31/03/2020   -- Se crea para tener el usuario creador del request 

			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'cir',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('creater_id'=>'user_id'),
				'select_fields'=>array('first_name cus_first_name_cir','last_name cus_last_name_cir','email cus_email_cir','phone_no cus_phone_no_cir','image cus_image_cir', 'firebase_id cus_firebase_id_cir', 'company_name cus_company_name_cir')
			),

			array(
				'table_name'=>$this->tableNameTrailer,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'inner',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'select_fields'=>array('min_load trailer_min_load','max_load trailer_max_load','image trailer_image'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameLanguageTrailer,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameTrailer,
				'join_type'=>'left',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'oncond'=>array('language_id'=>$this->language_id),
				'select_fields'=>'IFNULL(trailer_name,name) trailer_name',
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameLoadType,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'inner',
				'join_on'=>array('loadtype_id'=>'loadtype_id'),
				'select_fields'=>array('load_name'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'trans',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('transporter_id'=>'user_id'),
				'select_fields'=>array('first_name trans_first_name','last_name trans_last_name','email trans_email','phone_no trans_phone_no','company_name trans_company_name')
			),
			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'driver',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('driver_id'=>'user_id'),
				'select_fields'=>array('first_name driver_first_name','last_name driver_last_name','email driver_email','phone_no driver_phone_no','image driver_image')
			),
			array(
				'table_name'=>$this->tableNameVehicle,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('vehicle_id'=>'vehicle_id'),
				'select_fields'=>array('plate_no','vehicle_color'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameRequestBid,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>$bid_join_type,
				'join_on'=>array('bid_id'=>'bid_id'),
				'select_fields'=>array('bid_id','bid_amount','bid_status','bid_comment','cancel_comment','create_date bid_create_date'),
				'conditions'=>$bid_assos
			),
			// array(
			// 	'table_name'=>$this->tableNameRequestBid,
			// 	'table_name_alias'=>'totBid',
			// 	'join_with'=>$this->tableNameRequest,
			// 	'join_type'=>'left',
			// 	'join_on'=>array('request_id'=>'request_id'),
			// 	'select_fields'=>'IFNULL(count(totBid.request_id),0) total_bids',
			// 	'conditions'=>array()
			// ),

			/* array(
				'table_name'=>" (SELECT RB.request_id,RB.is_admin_delete FROM $this->dbprefix$this->tableNameRequestBid AS RB, $this->dbprefix$this->tableNameUser AS TU WHERE RB.user_id=TU.user_id AND RB.is_deleted='0' AND TU.is_deleted='0') ",
				'is_custom'=>'1',
				'table_name_alias'=>'totBid',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('request_id'=>'request_id'),
				//'oncond'=>array('is_admin_delete'=>'0'),
				// 'select_fields'=>'IFNULL(count(totBid.request_id),0) total_bids',
				'select_fields'=>'IFNULL(count(totBid.request_id),0) total_bids',
				'conditions'=>array()
			), */

			// rating section
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'cusrating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(cusrating.rating),2),0) cus_rating',
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'transrating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('transporter_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(transrating.rating),2),0) trans_rating',
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'driverrating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('driver_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(driverrating.rating),2),0) driver_rating',
				'conditions'=>array()
			),
			//rating of the request
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'requsertating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id','request_id'=>'request_id'),
				'oncond'=>array('is_deleted'=>'0'),
				'select_fields'=>array('rating_id cus_req_rating_id','rating cus_req_rating','communication_rating cus_req_communication_rating','trust_rating cus_req_trust_rating','quality_rating cus_req_quality_rating','user_comment cus_req_user_comment'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'reqtranstating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('transporter_id'=>'receiver_user_id','request_id'=>'request_id'),
				'oncond'=>array('is_deleted'=>'0'),
				'select_fields'=>array('rating_id trans_req_rating_id','rating trans_req_rating','communication_rating trans_req_communication_rating','trust_rating trans_req_trust_rating','quality_rating trans_req_quality_rating','user_comment trans_req_user_comment'),
				'conditions'=>array()
			),
		);
		
		
		if(!empty($trans_bid_cond)){
			$bid_join_type="left";
			if(!empty($trans_bid_assos)){
				$bid_join_type="inner";
				$trans_bid_cond = array_merge($trans_bid_cond,$trans_bid_assos);
			}
			
			$joins[]=array(
				'table_name'=>$this->tableNameRequestBid,
				'table_name_alias'=>'trans_bid',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>$bid_join_type,
				'join_on'=>array('request_id'=>'request_id'),
				'oncond'=>$trans_bid_cond,
				'select_fields'=>array('bid_id trans_bid_id','bid_amount trans_bid_amount','bid_status trans_bid_status','bid_comment trans_bid_comment','cancel_comment trans_cancel_comment','create_date trans_bid_create_date'),
				'conditions'=>array(),
			);
		}

		$this->debugcontroller($joins);
		
		$group_by=array('request_id');
		$services = $this->BaseModel->getDatas($this->tableNameRequest,$find_cond,$select_flds,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);

/*$services = $this->BaseModel->getDatas($this->tableNameRequest,$find_cond,$select_flds,$order_by,null,$offset,$limit,$complexCondition,null,$is_count);*/

		if(!$is_count && is_array($services) && !empty($services)){
			$trialer_image_path=base_url('uploads/trailers/');
			foreach($services as $key=>$service){
				if(!empty($service['trailer_image'])){
					$service['trailer_image']=$trialer_image_path.$service['trailer_image'];
				}
				if(!empty($service['cus_image'])){
					$service['cus_image']=base_url('uploads/users/'.$service['cus_image']);
				}
				if(!empty($service['driver_image'])){
					$service['driver_image']=base_url('uploads/users/'.$service['driver_image']);
				}
				if(!empty($service['request_image'])){
					$service['request_image']=base_url('uploads/requests/'.$service['request_image']);
				}
				
				if(empty($service['completed_date'])){
					$service['completed_date']='';
				}
				if(empty($service['deleted_date'])){
					$service['deleted_date']='';
				}
				$driver_location=array();
				if(!empty($service['driver_location'])){
					$driver_location = explode(",",$service['driver_location']);
				}
				$service['driver_latitude']=(isset($driver_location[0]))?trim($driver_location[0]):'';
				$service['driver_longitude']=(isset($driver_location[1]))?trim($driver_location[1]):'';
				unset($service['driver_location']);
				
				$services[$key]=$service;
			}
		}

		    //echo '<pre>';
			//print_r($this->db->last_query());
			//echo '</pre>';
			//exit(); 
			
		return $services;
	}
	
	
	
	public function getrequestsV2($find_cond=array(),$assos_cond=array(),$offset=0,$limit=0){
		$select_flds=array();
		$order_by=array('pickup_date,pickup_time'=>'DESC');
		$is_count=false;
		// driver lat long last position 
		$DR=$this->dbprefix.$this->tableNameRequestDriverLocation;
		//$DR=$this->tableNameRequestDriverLocation;
		$RQ=$this->dbprefix.$this->tableNameRequest;
		$TU=$this->dbprefix.$this->tableNameUser;
		$BD=$this->dbprefix.$this->tableNameRequestBid;
		
		
		$request_id = (isset($find_cond['request_id']))?$find_cond['request_id']:0;
		$transporter_id = (isset($find_cond['transporter_id']))?$find_cond['transporter_id']:0;
		$tranporterFilter = (isset($find_cond['tranporterFilter']))?$find_cond['tranporterFilter']:array();
		
		/* if($request_id>0){
			$select_flds['special_select_field']="IFNULL((SELECT COUNT($BD.request_id) FROM $BD , $TU 
														  WHERE $BD.user_id=$TU.user_id 
														  AND $BD.is_deleted='0' 
														  AND $TU.is_deleted='0'
														  AND $BD.request_id = $request_id), 0) AS total_bids";
		}
		else{
			$select_flds['special_select_field']="IFNULL((SELECT COUNT($BD.request_id) FROM $BD , $TU 
														  WHERE $BD.user_id=$TU.user_id 
														  AND $BD.is_deleted='0' 
														  AND $TU.is_deleted='0'
														  AND $BD.request_id = $RQ.request_id), 0) AS total_bids";
		} */
		
		//AND $DR.user_id=driver_id
		/*$complexCondition=array(
			'DATE('.$this->dbprefix.$this->tableNameRequest.'.create_date)'=>date('Y-m-d')
		);*/
		
		$complexCondition=array();
		
		$trans_bid_cond=array();
		
		if(isset($find_cond['complexCondition'])){
			$complexCondition=$find_cond['complexCondition'];
			unset($find_cond['complexCondition']);
		}
		
		// request status section 
		if(!empty($find_cond['request_status'])){
			$request_status = $find_cond['request_status'];
			if(strpos($request_status,'*')>=0){
				/*$request_status = explode("*",$request_status);
				$find_cond['request_status'] = $request_status;*/
				$find_cond['request_status'] = explode("*",$request_status);
			}
		}
		
		// associate conditions 
		//custome select fields 
		if(isset($assos_cond['fields']) && !empty($assos_cond['fields'])){
			$select_flds = $assos_cond['fields'];
		}
		// custome order order_by 
		if(isset($assos_cond['order_by']) && !empty($assos_cond['order_by'])){
			$order_by = $assos_cond['order_by'];
		}
		//only count return 
		if(isset($assos_cond['count']) && !empty($assos_cond['count'])){
			$is_count = $assos_cond['count'];
		}
		
		// transporter bid details with request 
		$trans_bid_assos=array();
		$bid_assos=array();
		$bid_join_type="left";
		
		if(isset($assos_cond['trans_bid_cond']) && !empty($assos_cond['trans_bid_cond'])){
			$trans_bid_cond = $assos_cond['trans_bid_cond'];
		}
		if(isset($assos_cond['trans_bid_assos']) && !empty($assos_cond['trans_bid_assos'])){
			$trans_bid_assos = $assos_cond['trans_bid_assos'];
		}
		if(isset($assos_cond['bid_assos']) && !empty($assos_cond['bid_assos'])){
			$bid_assos = $assos_cond['bid_assos'];
			$bid_join_type="inner";
		}
		
		// transporter filter for customer
		
		$tfc_assos=array();
		$tfc_join_type="left";
		
		if(isset($assos_cond['tfc_assos']) && !empty($assos_cond['tfc_assos'])){
			$tfc_assos = $assos_cond['tfc_assos'];
			$tfc_join_type="inner";
		}
		
		
		
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>array('first_name cus_first_name','last_name cus_last_name','email cus_email','phone_no cus_phone_no','image cus_image', 'firebase_id cus_firebase_id', 'company_name cus_company_name'),
				//hlm 17/06/20 condicion para usuario bloqueado
				//'conditions'=>array('is_blocked' => 2)
			),
			
			//hlm 03/08/2020   -- Se crea para filtrar usuarios transporter
			
		    array(
				'table_name'=>$this->tableNameUserTransporter,
				'join_with'=>$this->tableNameRequest,
				'join_type'=>$tfc_join_type,
				'join_on'=>array('user_id'=>'id_customer'),
				'select_fields'=>array('id_transporter'),
				'conditions'=>$tfc_assos
			),
			
            //hlm 31/03/2020   -- Se crea para tener el usuario creador del request 

			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'cir',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('creater_id'=>'user_id'),
				'select_fields'=>array('first_name cus_first_name_cir','last_name cus_last_name_cir','email cus_email_cir','phone_no cus_phone_no_cir','image cus_image_cir', 'firebase_id cus_firebase_id_cir', 'company_name cus_company_name_cir')
			),

			array(
				'table_name'=>$this->tableNameTrailer,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'inner',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'select_fields'=>array('min_load trailer_min_load','max_load trailer_max_load','image trailer_image'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameLanguageTrailer,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameTrailer,
				'join_type'=>'left',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'oncond'=>array('language_id'=>$this->language_id),
				'select_fields'=>'IFNULL(trailer_name,name) trailer_name',
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameLoadType,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'inner',
				'join_on'=>array('loadtype_id'=>'loadtype_id'),
				'select_fields'=>array('load_name'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'trans',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('transporter_id'=>'user_id'),
				'select_fields'=>array('first_name trans_first_name','last_name trans_last_name','email trans_email','phone_no trans_phone_no','company_name trans_company_name')
			),
			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'driver',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('driver_id'=>'user_id'),
				'select_fields'=>array('first_name driver_first_name','last_name driver_last_name','email driver_email','phone_no driver_phone_no','image driver_image')
			),
			array(
				'table_name'=>$this->tableNameVehicle,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('vehicle_id'=>'vehicle_id'),
				'select_fields'=>array('plate_no','vehicle_color'),
				'conditions'=>array()
			),
			/* array(
				'table_name'=>$this->tableNameRequestBid,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>$bid_join_type,
				'join_on'=>array('bid_id'=>'bid_id'),
				'select_fields'=>array('bid_id','bid_amount','bid_status','bid_comment','cancel_comment','create_date bid_create_date'),
				'conditions'=>$bid_assos
			), */
			// array(
			// 	'table_name'=>$this->tableNameRequestBid,
			// 	'table_name_alias'=>'totBid',
			// 	'join_with'=>$this->tableNameRequest,
			// 	'join_type'=>'left',
			// 	'join_on'=>array('request_id'=>'request_id'),
			// 	'select_fields'=>'IFNULL(count(totBid.request_id),0) total_bids',
			// 	'conditions'=>array()
			// ),

			/* array(
				'table_name'=>" (SELECT RB.request_id,RB.is_admin_delete FROM $this->dbprefix$this->tableNameRequestBid AS RB, $this->dbprefix$this->tableNameUser AS TU WHERE RB.user_id=TU.user_id AND RB.is_deleted='0' AND TU.is_deleted='0') ",
				'is_custom'=>'1',
				'table_name_alias'=>'totBid',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('request_id'=>'request_id'),
				//'oncond'=>array('is_admin_delete'=>'0'),
				// 'select_fields'=>'IFNULL(count(totBid.request_id),0) total_bids',
				'select_fields'=>'IFNULL(count(totBid.request_id),0) total_bids',
				'conditions'=>array()
			), */

			// rating section
			/* array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'cusrating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(cusrating.rating),2),0) cus_rating',
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'transrating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('transporter_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(transrating.rating),2),0) trans_rating',
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'driverrating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('driver_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(driverrating.rating),2),0) driver_rating',
				'conditions'=>array()
			),
			//rating of the request
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'requsertating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id','request_id'=>'request_id'),
				'oncond'=>array('is_deleted'=>'0'),
				'select_fields'=>array('rating_id cus_req_rating_id','rating cus_req_rating','communication_rating cus_req_communication_rating','trust_rating cus_req_trust_rating','quality_rating cus_req_quality_rating','user_comment cus_req_user_comment'),
				'conditions'=>array()
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'table_name_alias'=>'reqtranstating',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>'left',
				'join_on'=>array('transporter_id'=>'receiver_user_id','request_id'=>'request_id'),
				'oncond'=>array('is_deleted'=>'0'),
				'select_fields'=>array('rating_id trans_req_rating_id','rating trans_req_rating','communication_rating trans_req_communication_rating','trust_rating trans_req_trust_rating','quality_rating trans_req_quality_rating','user_comment trans_req_user_comment'),
				'conditions'=>array()
			), */
		);
		
		
		if(!empty($trans_bid_cond)){
			$bid_join_type="left";
			if(!empty($trans_bid_assos)){
				$bid_join_type="inner";
				$trans_bid_cond = array_merge($trans_bid_cond,$trans_bid_assos);
			}
			
			$joins[]=array(
				'table_name'=>$this->tableNameRequestBid,
				'table_name_alias'=>'trans_bid',
				'join_with'=>$this->tableNameRequest,
				'join_type'=>$bid_join_type,
				'join_on'=>array('request_id'=>'request_id'),
				'oncond'=>$trans_bid_cond,
				'select_fields'=>array('bid_id trans_bid_id','bid_amount trans_bid_amount','bid_status trans_bid_status','bid_comment trans_bid_comment','cancel_comment trans_cancel_comment','create_date trans_bid_create_date'),
				'conditions'=>array(),
			);
		}

		$this->debugcontroller($joins);
		
		$group_by=array('request_id');
		$services = $this->BaseModel->getDatas($this->tableNameRequest,$find_cond,$select_flds,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);

/*$services = $this->BaseModel->getDatas($this->tableNameRequest,$find_cond,$select_flds,$order_by,null,$offset,$limit,$complexCondition,null,$is_count);*/

		if(!$is_count && is_array($services) && !empty($services)){
			$trialer_image_path=base_url('uploads/trailers/');
			foreach($services as $key=>$service){
				if(!empty($service['trailer_image'])){
					$service['trailer_image']=$trialer_image_path.$service['trailer_image'];
				}
				if(!empty($service['cus_image'])){
					$service['cus_image']=base_url('uploads/users/'.$service['cus_image']);
				}
				if(!empty($service['driver_image'])){
					$service['driver_image']=base_url('uploads/users/'.$service['driver_image']);
				}
				if(!empty($service['request_image'])){
					$service['request_image']=base_url('uploads/requests/'.$service['request_image']);
				}
				
				if(empty($service['completed_date'])){
					$service['completed_date']='';
				}
				if(empty($service['deleted_date'])){
					$service['deleted_date']='';
				}
				$driver_location=array();
				if(!empty($service['driver_location'])){
					$driver_location = explode(",",$service['driver_location']);
				}
				$service['driver_latitude']=(isset($driver_location[0]))?trim($driver_location[0]):'';
				$service['driver_longitude']=(isset($driver_location[1]))?trim($driver_location[1]):'';
				unset($service['driver_location']);
				
				$services[$key]=$service;
			}
		}

		   /*  echo '<pre>';
			print_r($this->db->last_query());
			echo '</pre>';
			exit();  */
			
		return $services;
	}
	
	
	
	public function querySQL($query=''){
		$requests = $this->BaseModel->queryResult($query);
		return $requests;
	}


	public function getrequestbids($find_cond=array(),$extra=array()){
		$select_fields=array('bid_id','bid_amount','bid_status','user_id','bid_comment','cancel_comment','create_date','is_admin_delete');
		
		$select_fields=(isset($extra['select_fields']))?$extra['select_fields']:$select_fields;
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$search_text=(isset($extra['search_text']))?$extra['search_text']:'';
		$complexCondition=array();
		$conditions=array();
		if(!empty($search_text)){
			if(filter_var($search_text,FILTER_VALIDATE_EMAIL)){
				$conditions=array(
					'like'=>array('email'=>$search_text)
				);
				$complexCondition="email like '".$search_text."%'";
			}
			else{
				$conditions=array(
					'like'=>array('first_name'=>$search_text)
				);
				
				//$complexCondition="first_name like '".$first_name."%' OR last_name LIKE '%".$last_name."'";
				$complexCondition="concat(first_name,' ',last_name) LIKE '%".$search_text."%'";
			}
			$conditions=array();
		}
		$group_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'table_name_alias'=>'trans',
				'join_with'=>$this->tableNameRequestBid,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'oncond'=>array('is_deleted'=>'0'),
				'select_fields'=>array('company_name trans_first_name','last_name trans_last_name','email trans_email','phone_no trans_phone_no','image trans_image','company_name trans_company_name'),
				'conditions'=>$conditions
			),
			array(
				'table_name'=>"(SELECT R.transporter_id,count(R.transporter_id) total_rec FROM $this->dbprefix$this->tableNameRequest AS R WHERE R.request_status='".REQUEST_COMPLED_STATUS."'  GROUP BY R.transporter_id)",
				'table_name_alias'=>'totcomp',
				'is_custom'=>'1',
				'join_with'=>$this->tableNameRequestBid,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'transporter_id'),
				'select_fields'=>'IFNULL((totcomp.total_rec),0) completed_request',
				'conditions'=>array()
			),
			array(
				'table_name'=>"(SELECT BP.user_id p_user_id,count(BP.user_id) total_place_rec FROM $this->dbprefix$this->tableNameRequestBid AS BP WHERE BP.is_deleted='0' GROUP BY BP.user_id) ",
				'table_name_alias'=>'totalreq',
				'is_custom'=>'1',
				'join_with'=>$this->tableNameRequestBid,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'p_user_id'),
				'select_fields'=>'IFNULL(totalreq.total_place_rec,0) total_request',
				'conditions'=>array()
			)
		);
		
		if(!$is_count){
			$joins[]=array(
				'table_name'=>"( SELECT TRUNCATE(AVG(rating),2) rating,RT.receiver_user_id FROM $this->dbprefix$this->tableNameUserRating AS RT WHERE RT.is_deleted='0' AND RT.is_blocked='0' GROUP BY RT.receiver_user_id ) ",
				'table_name_alias'=>'URT',
				'is_custom'=>'1',
				'join_with'=>$this->tableNameRequestBid,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(URT.rating,0) rating',
			);
		}
		// remove the bid from the list
		//$find_cond['is_admin_delete']='0';
		
		$bids = $this->BaseModel->getDatas($this->tableNameRequestBid,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		if(!$is_count && is_array($bids) && !empty($bids)){
			foreach($bids as $key=>$bid){
				if(!empty($bid['trans_image'])){
					$bid['trans_image']=base_url('uploads/users/'.$bid['trans_image']);
				}
				if(!empty($bid['trans_last_name'])){
					$bid['trans_last_name']='';
				}
				$bids[$key]=$bid;
			}
		}
		return $bids;
	}
	
	public function gettrailers($find_conds=array()){
		$select_fields=array('trailer_id','name','image','min_load','max_load');
		
		$order_by = array('name'=>'ASC');
		
		$joins=array(
			array(
				'table_name'=>$this->tableNameLanguageTrailer,
				'join_with'=>$this->tableNameTrailer,
				'join_with_alias'=>'',
				'join_type'=>'left',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'oncond'=>array('is_blocked'=>'0','is_deleted'=>'0','language_id'=>$this->language_id),
				'select_fields'=>'IFNULL(trailer_name,name) name'
			)
		);
		$trailers = $this->BaseModel->getDatas($this->tableNameTrailer,$find_conds,$select_fields,$order_by,$joins);
		if(!empty($trailers)){
			foreach($trailers as $key=>$trailer){
				if(!empty($trailer['image'])){
					$trailer['image'] = base_url('uploads/trailers/'.$trailer['image']);
				}
				$trailers[$key]=$trailer;
			}
		}
		return $trailers;
	}
	
	public function getrequeststatus(){
		$request_status = array(
			'0'=>'New',
			'1'=>'Bid Placed',
			'2'=>'Bid Accepted By Customer',
			'3'=>'Transporter Accepted',
			'4'=>'Bid Cancelled',
			'5'=>'Driver & Vehicle Assigned',
			'6'=>'In transit',
			'13'=>'Completed',
			'14'=>'Expired'
		);
		return $request_status;
	}
	
	public function getdriverchangestatus(){
		$request_status=array(
			'6'=>'Arriving',
			'7'=>'Loading',
			'8'=>'Loaded',
			'9'=>'Trip start',
			'10'=>'Reached',
			'11'=>'Unloading',
			'12'=>'Unloaded',
			'13'=>'Completed',
		);
		return $request_status;
	}
	
	public function getuserstatus(){
		$user_status=array(
			'1'=>'Available',
			'2'=>'Assigned',
			'3'=>'In Transit'
		);
		if($this->language_id==2){ //spanish section
			$user_status=array(
				'1'=>'Disponible',
				'2'=>'Asignado',
				'3'=>'En tránsito'
			);
		}
		return $user_status;
	}
	
	public function getvehiclestatus(){
		$user_status=array(
			'1'=>'Available',
			'2'=>'Assigned',
			'3'=>'In Transit'
		);
		if($this->language_id==2){
			$user_status=array(
				'1'=>'Disponible',
				'2'=>'Asignado',
				'3'=>'En tránsito'
			);
		}
		return $user_status;
	}
	
	public function getbidstatus(){
		$bid_status=array(
			'0'=>'Placed',
			'1'=>'Customer Accepted',
			'2'=>'Transporter Accepted',
			'3'=>'Transporter Cancelled',
			'4'=>'Transporter Lost'
		);
	}
	
	public function getvehicles($find_cond=array(),$extra=array()){
		$select_fields=array('vehicle_id','trailer_id','plate_no','purchase_year','is_blocked','vehicle_status','vehicle_maxload','vehicle_minload','vehicle_color');
		$complexCondition=array();
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		// search section 
		$search_text=(isset($extra['search_text']))?$extra['search_text']:'';
		if(!empty($search_text)){
			$complexCondition = "plate_no LIKE '%".$search_text."%'";
		}
		$group_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameTrailer,
				'join_with'=>$this->tableNameVehicle,
				'join_type'=>'inner',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'select_fields'=>array('name trailer_name','max_load','min_load')
			)
		);
		$vehicles = $this->BaseModel->getDatas($this->tableNameVehicle,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		if(!$is_count && !empty($vehicles) ){
			foreach($vehicles as $key=>$vehicle){
				$vehicles[$key]['vehicle_status_text']=vehicle_status($vehicle['vehicle_status']);
			}
		}
		return $vehicles;
	}
	
	public function getdrivers($find_cond=array(),$extra=array()){
		$select_fields=array('user_id','showpass','first_name','last_name','phone_no','email','dni_no','ruc_no','company_licence_no','licence_no','image','is_blocked','user_status');
		$complexCondition=array();
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$search_text=(isset($extra['search_text']))?$extra['search_text']:'';
		if(!empty($search_text)){
			$complexCondition = "(concat(first_name,' ',last_name) LIKE '%".$search_text."%' OR phone_no LIKE '%".$search_text."%')";
			if(filter_var($search_text,FILTER_VALIDATE_EMAIL)){
				$complexCondition = "email LIKE '%".$search_text."%'";
			}
		}
		$group_by=array('user_id');
		$joins=array(
			array(
				'table_name'=>$this->tableNameUserRating,
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(rating),2),0) rating',
				'oncond'=>array('is_deleted'=>'0')
			)
		);
		$drivers = $this->BaseModel->getDatas($this->tableNameUser,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		//for loap for image section 
		if(!$is_count && !empty($drivers)){
			foreach($drivers as $key=>$driver){
				if(!empty($driver['image'])){
					$driver['image']=base_url('uploads/users/'.$driver['image']);
					$drivers[$key]=$driver;
				}
				$drivers[$key]['user_status_text']=user_status($driver['user_status']);
				//phone no format
			}
		}
		return $drivers;
	}
	
	public function getuserdocuments($find_cond=array(),$extra=array()){
		$select_fields=array('user_document_id','user_id','documenttype_id','file_name','document_status','is_blocked');
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$complexCondition=array();
		$group_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameDocumentType,
				'join_with'=>$this->tableNameUserDocument,
				'join_type'=>'inner',
				'join_on'=>array('documenttype_id'=>'documenttype_id'),
				'select_fields'=>array('document_title')
			)
		);
		$documents = $this->BaseModel->getDatas($this->tableNameUserDocument,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		//for loap for image section 
		if(!$is_count && !empty($documents)){
			foreach($documents as $key=>$document){
				if(!empty($document['file_name'])){
					$document['file_name']=base_url('uploads/documents/'.$document['file_name']);
					$documents[$key]=$document;
				}
				//phone no format
			}
		}
		return $documents;
	}
	
	public function getvehicledocuments($find_cond=array(),$extra=array()){
		$select_fields=array('vehicle_document_id','user_id','vehicle_id','documenttype_id','file_name','document_status','is_blocked');
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$complexCondition=array();
		$group_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameDocumentType,
				'join_with'=>$this->tableNameVehicleDocument,
				'join_type'=>'inner',
				'join_on'=>array('documenttype_id'=>'documenttype_id'),
				'select_fields'=>array('document_title')
			)
		);
		$documents = $this->BaseModel->getDatas($this->tableNameVehicleDocument,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		//for loap for image section 
		if(!$is_count && !empty($documents)){
			foreach($documents as $key=>$document){
				if(!empty($document['file_name'])){
					$document['file_name']=base_url('uploads/documents/'.$document['file_name']);
					$documents[$key]=$document;
				}
				//phone no format
			}
		}
		return $documents;
	}
	
	public function getchats($find_cond=array(),$extra=array()){
		$select_fields=array('chat_id','user_id','transporter_id','request_id','create_date');
		$user_type=(isset($extra['user_type']))?$extra['user_type']:0;
		
		$select_fields=(isset($extra['select_fields']))?$extra['select_fields']:$select_fields;
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$complexCondition=array();
		$group_by=array();
		
		$joins=array(
			array(
				'table_name'=>$this->tableNameRequest,
				'join_with'=>$this->tableNameChat,
				'join_type'=>'inner',
				'join_on'=>array('request_id'=>'request_id'),
				'select_fields'=>array('pickup_location','pickup_date','pickup_time','request_amount')
			),
			array(
				'table_name'=>$this->tableNameMessage,
				'join_with'=>$this->tableNameChat,
				'join_type'=>'left',
				'join_on'=>array('chat_id'=>'chat_id'),
				'oncond'=>" message_id = (SELECT message_id FROM $this->dbprefix$this->tableNameMessage WHERE chat_id=$this->dbprefix$this->tableNameChat.chat_id AND is_blocked='0' AND is_deleted='0' ORDER BY message_id DESC LIMIT 1) ",
				'select_fields'=>array('user_id message_creater_id','message_type','message_data','create_date message_create_date')
			)
		);
		
		if($user_type==1){ // transporter want to see user detail
			$joins[]=array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameChat,
				'table_name_alias'=>'',
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>array('user_id','first_name','last_name','email','phone_no','image')
			);
		}
		else{ // user section want to see the transporter
			$joins[]=array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameChat,
				'table_name_alias'=>'',
				'join_type'=>'inner',
				'join_on'=>array('transporter_id'=>'user_id'),
				'select_fields'=>array('user_id','first_name','last_name','email','phone_no','image')
			);
		}
		
		$chats = $this->BaseModel->getDatas($this->tableNameChat,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		//for loap for image section 
		if(!$is_count && !empty($chats)){
			foreach($chats as $key=>$chat){
				if(!empty($chat['image'])){
					$chat['image']=base_url('uploads/users/'.$chat['image']);
					$chats[$key]=$chat;
				}
			}
		}
		return $chats;
	}
	
	public function getmessages($find_cond=array(),$extra=array()){
		$select_fields=array('message_id','chat_id','user_id','message_type','message_data','create_date');
		$user_id=(isset($extra['user_id']))?$extra['user_id']:0;
		$super_parent_id=(isset($extra['super_parent_id']))?$extra['super_parent_id']:0;
		
		$select_fields=(isset($extra['select_fields']))?$extra['select_fields']:$select_fields;
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$complexCondition=array();
		$complexCondition='user_deleted_id IS NULL';
		
		$group_by=array();
		
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameMessage,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>array('first_name','last_name','image','super_parent_id')
			),
			array(
				'table_name'=>$this->tableNameUserDeleteMessage,
				'join_with'=>$this->tableNameMessage,
				'join_type'=>'left',
				'join_on'=>array('message_id'=>'message_id'),
				'oncond'=>array('user_id'=>$user_id),
				//'select_fields'=>array('user_deleted_id','create_date')
			),
		);
		
		$messages = $this->BaseModel->getDatas($this->tableNameMessage,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		
		//for loap for image section 
		if(!$is_count && !empty($messages)){
			foreach($messages as $key=>$message){
				$is_mine=0;
				if(!empty($message['image'])){
					$message['image']=base_url('uploads/users/'.$message['image']);
				}
				if($message['message_type']==1 && !empty($message['message_data'])){
					$message['message_data']=base_url('uploads/chats/'.$message['message_data']);
				}
				if(($message['user_id']==$user_id) || ($message['user_id']==$super_parent_id) || ($message['super_parent_id']==$user_id)){
					$is_mine=1;
				}
				
				$message['is_mine']=$is_mine;
				$messages[$key]=$message;
			}
			// now update the chat count 
			$this->update_user_count($user_id,0,2,1);
		}
		return $messages;
	}
	
	/*
	@param $user_id integer target user 
	@param $is_increase integer 
	@param $count_for integer 1=notification, 2=chat, 3=all
	@param $is_default integer set the value 0
	*/
	public function update_user_count($user_id='0',$is_increase='0',$count_for='0',$is_default='0'){
		if($user_id > '0' && $count_for > '0' ){
			// now validate the section 
			$field_name = "";
			switch($count_for){
				case 1:
					if($is_default){
						$field_name=" `unread_notification`='0' ";
					}
					else{
						if($is_increase){
							$field_name=" `unread_notification`=`unread_notification`+'1' ";
						}
						else{
							$field_name=" `unread_notification`=IF(`unread_notification`>0,(`unread_notification`-'1'),'0') ";
						}
					}
					break;
				case 2:
					if($is_default){
						$field_name=" `unread_chat`='0' ";
					}
					else{
						if($is_increase){
							$field_name=" unread_chat=`unread_chat`+'1' ";
						}
						else{
							$field_name=" unread_chat=IF(`unread_chat`>0,(`unread_chat`-'1'),'0') ";
						}
					}
					break;
				case 3:
					if($is_default){
						$field_name=" `unread_notification`='0', `unread_chat`='0' ";
					}
					else{
						if($is_increase){
							$field_name=" `unread_notification`=`unread_notification`+'1', unread_chat=`unread_chat`+'1' ";
						}
						else{
							$field_name=" `unread_notification`=IF(`unread_notification`>0,(`unread_notification`-'1'),'0'), unread_chat=IF(`unread_chat`>0,(`unread_chat`-'1'),'0') ";
						}
					}
					break;
				default:
					break;
			}
			
			if(!empty($field_name)){
				// do the actual section 
				$sql = "UPDATE $this->dbprefix$this->tableNameUserCount SET $field_name WHERE user_id='$user_id' AND is_blocked='0' AND is_deleted='0'";
				$this->BaseModel->customQuery($sql);
			}
		}
	}
	
	
	public function getratings($find_cond=array(),$extra=array()){
		$select_fields=array('rating_id','giver_user_id','rating','user_comment','create_date');
		$select_fields=(isset($extra['select_fields']))?$extra['select_fields']:$select_fields;
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$complexCondition=array();
		$group_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameUserRating,
				'join_type'=>'inner',
				'join_on'=>array('giver_user_id'=>'user_id'),
				'select_fields'=>array('first_name','last_name','image')
			)
		);
		
		$ratings = $this->BaseModel->getDatas($this->tableNameUserRating,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		
		//for loap for image section 
		if(!$is_count && !empty($ratings)){
			foreach($ratings as $key=>$rating){
				$is_mine=0;
				if(!empty($rating['image'])){
					$rating['image']=base_url('uploads/users/'.$rating['image']);
				}
				if(!empty($rating['create_date'])){
					$rating['create_date']=date("d F Y",strtotime($rating['create_date']));
				}
				$ratings[$key]=$rating;
			}
		}
		return $ratings;
	}

	public function debug($prinf=''){
		if(isset($prinf)){
		    $log  = "____________________________________________________________________________________________________";
		    $log  .= "\n";
    		$log  .= "Data: ";
			$log.=json_encode($prinf).PHP_EOL;
			file_put_contents('./logs/debug_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
		}
	}
	
	public function notificaciones___($prinf=array()){		
		    $log  = "***********************************";
		    $log  .= "\n";
    		$log  .= "Data: ";
			$log.=json_encode($prinf).PHP_EOL;
			file_put_contents('./logs/notificaciones_'.date("j.n.Y").'.txt', $log, FILE_APPEND);		
	}

	public function getrequest_summery($find_cond=array()){
		$select_fields=array();
		$user_id = (!empty($find_cond['user_id']))?$find_cond['user_id']:0;
		$user_type = (!empty($find_cond['user_type']))?$find_cond['user_type']:0;
		$is_company = (!empty($find_cond['is_company']))?$find_cond['is_company']:0;
		$total_request=0;
		$completed_request=0;
		$expired_request=0;
		$in_transit_request=0;
		$panding_request=0;
		
		$request_summery=array(
			'total_request'=>$total_request,
			'completed_request'=>$completed_request,
			'expired_request'=>$expired_request,
			'in_transit_request'=>$in_transit_request,
			'panding_request'=>$panding_request,
		);
		// main section getting if user details found 
		if($user_id>0){
			$find_cond=array(
				'is_blocked'=>'0'
			);
			$tb = $this->dbprefix.$this->tableNameRequest;
			$sql = "SELECT IFNULL(count($tb.request_id),0) total_request, IFNULL(SUM(IF($tb.request_status='".REQUEST_COMPLED_STATUS."',1,0)),0) completed_request, IFNULL(SUM(IF($tb.request_status='14',1,0)),0) expired_request, IFNULL(SUM(IF(($tb.request_status > '5' && $tb.request_status < '13'),1,0)),0) in_transit_request";
			$from = " FROM $tb";
			$where = " WHERE $tb.is_blocked='0' AND $tb.is_deleted='0' ";
			if($user_type==1){
				// transport section
				$sql.=", IFNULL(SUM(IF(($tb.request_status = '1' || $tb.request_status = '4'),1,0)),0) panding_request";
				if($is_company){
					$where.=" AND transporter_id='$user_id'";
				}
				else{
					$where.=" AND driver_id='$user_id'";
				}
			}
			else{
				$sql.=", IFNULL(SUM(IF(($tb.request_status = '2'),1,0)),0) panding_request";
				$where.=" AND user_id='$user_id'";
			}
			$query = $sql.$from.$where;
			$request_summery_res = $this->BaseModel->customSelect($query);
			
			if(!empty($request_summery_res)){
				$request_summery = $request_summery_res[0];
			}
		}
		return $request_summery;
	}
	
	public function getnotifications($find_cond=array(),$extra=array()){
		$select_fields=array('notification_id','request_id','user_id','receiver_user_id','notification_type','notification_text','is_read','create_date');
		
		$select_fields=(isset($extra['select_fields']))?$extra['select_fields']:$select_fields;
		$order_by=(isset($extra['order_by']))?$extra['order_by']:array();
		$is_count=(isset($extra['is_count']))?$extra['is_count']:0;
		$offset=(isset($extra['offset']))?$extra['offset']:0;
		$limit=(isset($extra['limit']))?$extra['limit']:0;
		$complexCondition=array();
		
		$group_by=array();
		
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameRequestNotification,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>array('first_name','last_name','image')
			),
			array(
				'table_name'=>$this->tableNameRequest,
				'join_with'=>$this->tableNameRequestNotification,
				'join_type'=>'inner',
				'join_on'=>array('request_id'=>'request_id'),
				'oncond'=>array('is_deleted'=>'0'),
				'select_fields'=>array()
			),
		);
		
		$notifications = $this->BaseModel->getDatas($this->tableNameRequestNotification,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexCondition,$group_by,$is_count);
		
		//for loap for image section 
		if(!$is_count && !empty($notifications)){
			$notification_data=array(
				'request_id'=>'0',
				'user_id'=>'0',
			);
			foreach($notifications as $key=>$notification){
				if(!empty($notification['image'])){
					$notification['image']=base_url('uploads/users/'.$notification['image']);
				}
				$notification_data['request_id']=$notification['request_id'];
				$notification_data['notification_type']=$notification['notification_type'];
				
				$notify_text = $this->getnotificationtypes($notification_data);
				if(!empty($notify_text)){
					$notification['notification_text']=$notify_text;
				}
				$notifications[$key]=$notification;
			}
		}
		return $notifications;
	}
	
	public function getnotificationtypes($data=array()){
		$request_id=(isset($data['request_id']))?$data['request_id']:0;
		$notification_type=(isset($data['notification_type']))?$data['notification_type']:0;
		$amount=number_format(((isset($data['amount']))?$data['amount']:'0'),2);
		$sender_name = (isset($data['sender_name']))?$data['sender_name']:'';
		$conductor = (isset($data['conductor']))?$data['conductor']:'';
		
		$noty_type=array(
			'1'=>array( // place bid
				'1'=>"Placed a new bid in the request #{$request_id}",// english
				'2'=>"Ha recibido una propuesta en la Orden #{$request_id}",// spanish
			),
			'2'=>array( // customer accept bid
				'1'=>"You have won a bid #{$request_id}",
				'2'=>"¡Felicitaciones! Ha ganado la carga #{$request_id}. Vaya a asignar conductor y vehículo aquí."
			),
			'3'=>array( // trans approved
				'1'=>"Approved your awarded bid of the request #{$request_id}",
				'2'=>"El transportista ha confirmado la orden #{$request_id}"
			),
			'4'=>array( // trans cancelled bid
				'1'=>"Cancelled your awarded bid of the request #{$request_id}",
				'2'=>"El transportista ha cancelado #{$request_id}"
			),
			'5'=>array( // driver and vehicle assigned
				'1'=>"Driver and vehicle assigned in your request #{$request_id}",
				'2'=>"Conductor y vehículo asignado en su orden #{$request_id}"
			),
			'6'=>array( //driver get :: driver assing into a request  
				'1'=>"Assigned you in a new request #{$request_id} for transport",
				//'2'=>"Assigned you in a new request #{$request_id} for transport",
				//'2'=>"Ya puede iniciar el trackeo para su orden #{$request_id}"
				'2'=>"Se le ha asignado la carga #{$request_id}, aceptado por {$conductor}"
			),
			'7'=>array( // driver arriving
				'1'=>"Arriving for pick up of request #{$request_id}",
				'2'=>"Llegando para recoger la orden #{$request_id}",
			),
			'8'=>array( // driver arrived
				'1'=>"Arrived at pick up at location for request #{$request_id}",
				'2'=>"Llegó al punto de origen para el pedido #{$request_id}",
			),
			'9'=>array( // loading goods
				'1'=>"#{$request_id} loading",
				'2'=>"#{$request_id} cargando",
			),
			'10'=>array( // loading finished
				'1'=>"#{$request_id} has loaded.",
				'2'=>"#{$request_id} ha terminado de cargar."
			),
			'11'=>array( // trip started
				'1'=>"Leaving the pick up location for deliver for request #{$request_id}",
				'2'=>"Saliendo del punto de origen para la entrega N°#{$request_id} ",
			),
			'12'=>array( //arrive at deliver place
				'1'=>"Arrived at the drop off location for request #{$request_id}",
				'2'=>"El pedido #{$request_id} se encuentra en el Punto de Destino",
			),
			'13'=>array( // unloading
				'1'=>"#{$request_id} Unloading",
				'2'=>"#{$request_id} Descargando",
			),
			'14'=>array( // unloading done 
				'1'=>"#{$request_id} Unloading finished",
				'2'=>"#{$request_id} Descarga finalizada",
			),
			'15'=>array( // completed
				'1'=>"#{$request_id} has been completed",
				'2'=>"#{$request_id} ha terminado el servicio.",
			),
			'16'=>array( // new chat message
				'1'=>"You have a new message from {$sender_name}",
				'2'=>"Has recibido un nuevo mensaje de {$sender_name}",
			),
			'17'=>array( // update placed bid amount
				'1'=>"Update the placed bid in the request #{$request_id}",
				'2'=>"Se ha actualizado la cotización del pedido N°#{$request_id}",
			),
		);
		if($notification_type){
			return isset($noty_type[$notification_type][$this->language_id])?$noty_type[$notification_type][$this->language_id]:'BAUEN FREIGHT Notification';
		}
		else{
			return $noty_type;
		}
	}
	
	//file upload section 
	public function uploadimage($image=array(),$folder=''){
		$file_name='';
		if(isset($image['name']) && !empty($image['name'])){
			$file_name = time().'_'.$this->strreplace($image['name']);
			//$mime_type = $image_file['myme_type'];
			// file uploaded code pending
			$destination_path='uploads/';
			if(!empty($folder)){
				$destination_path.=$folder."/";
			}
			if(!move_uploaded_file($image['tmp_name'],$destination_path.$file_name)){
				$file_name='';
			}
		}
		return $file_name;
	}
	
	private function strreplace($strname=''){
		$replace_array=array('@','#',' ','(',')','^','&','%','$','!','+','`','~','=');
		$strname = str_replace($replace_array ,"", $strname);
		return $strname;
	}
	
	public function removeimage($image_name='',$folder=''){
		if(!empty($image_name)){
			$destination_path='uploads/';
			if(!empty($folder)){
				$destination_path.=$folder.'/';
			}
			$image_path = $destination_path.$image_name;
			if(file_exists($image_path)){
				unlink($image_path);
			}
		}
	}
	
	// email section 
	public function sendemail($email_for=0,$email='',$email_data=array()){
		//pending section
		if(filter_var($email,FILTER_VALIDATE_EMAIL)){
			$iemal_seed_to_sent=true;
			$subjects='';
			$messages='';
			switch($email_for){
				case 1:// signup confirm section
					$subjects="You successfully completed your registration";
					$messages = "Thank you";
					$messages.=json_encode($email_data);
					break;
				case 2://forgot password
					$subjects="Password reset request";
					$messages = "Por favor, copie el siguiente link en su buscador para continuar:\n";
					$messages.=$email_data['changelink'];
					break;
				case 3://password change successfully
					$subjects="Haz cambiado tu contraseña con éxito";
					$messages = "Gracias por actualizar su contraseña. Nos ayuda a mantener su cuenta a salvo";
					break;
				case 4: // user create and send password and login details 
					$subjects="Your login details for track your request";
					$messages="Using below details you can track your request status. Details are :\n";
					$messages.=json_encode($email_data);
					break;
				case 5: // email validation link send
					$v_code = $email_data['verification_code'];
					$email_link = $email_data['email_link'];
					$subjects="Correo de Verificación";
					$messages="Haga click en el siguiente link para verificar su correo electrónico:\n";
					$messages.="Código de verificación : $v_code \n\n";
					$messages.="Copie  el siguiente link para verificar su cuenta: $email_link \n\n";
					//$messages.=json_encode($email_data);
					
					break;
				case 6://contact us
					$subjects="One user trying to contact with you";
					$messages=$email_data['messages'];
					break;
				case 7://send message customer and transporter
				    $request_id = $email_data['request_id'];
					$hora_recojo = $email_data['hora_recojo'];
					$hora_recojo_2 = $email_data['hora_recojo_2'];
					$conductor = $email_data['conductor'];
					$conductor_dni = $email_data['conductor_dni'];
					$licencia = $email_data['licencia'];
					$placa_tracto = $email_data['placa_tracto'];
					$placa_trailer = $email_data['placa_trailer'];
					$name_customer = $email_data['name_customer'];
					$tipo_trailer = $email_data['tipo_trailer'];
					$tipo_carga = $email_data['tipo_carga'];
					$cel_transport = $email_data['cel_transport'];
					$amount = $email_data['amount'];
					$company_name = $email_data['company_name'];
					$descripcion = $email_data['description'];

					$subjects="Conductor y vehículo asignado en su orden # $request_id";
		
					$messages="Constancia de Confirmación de Carga\n\n";
					$messages.="El recojo queda programado el: $hora_recojo a las $hora_recojo_2 \n";
					//$messages.="Favor, volver a confirmar la hora \n\n";
					//$messages.="La unidad es la siguiente: \n\n";
					$messages.="Transportista Asignado: $conductor \n";
					$messages.="Empresa: $company_name \n";
					$messages.="Teléfono: $cel_transport \n";
					$messages.="N° de Licencia de Conducir: $licencia \n";
					$messages.="DNI: $conductor_dni \n";
					$messages.="Placa Tracto: $placa_tracto\n";
					$messages.="Placa Trailer: $placa_trailer\n";
					$messages.="Tipo de trailer: $tipo_trailer\n";
					$messages.="Tipo de carga: $tipo_carga\n";
					$messages.="Precio: S/ $amount + IGV \n";
					$messages.="Descripción: $descripcion \n\n";
					$messages.="Bauen Freight SAC 2020\n\n";
					
					break;

				case 8://send message customer and transporter
				    $request_id = $email_data['request_id'];
					$hora_recojo = $email_data['hora_recojo'];
					$hora_recojo_2 = $email_data['hora_recojo_2'];
					$conductor = $email_data['conductor'];
					$conductor_dni = $email_data['conductor_dni'];
					$licencia = $email_data['licencia'];
					$placa_tracto = $email_data['placa_tracto'];
					$placa_trailer = $email_data['placa_trailer'];
					$name_customer = $email_data['name_customer'];
					$tipo_trailer = $email_data['tipo_trailer'];
					$tipo_carga = $email_data['tipo_carga'];
					$cel_transport = $email_data['cel_transport'];
					$amount = $email_data['amount'];
					$company_name = $email_data['company_name'];
					$descripcion = $email_data['description'];

					$subjects="Conductor y vehículo asignado en su orden # $request_id";
		
					$messages="Constancia de Confirmación de Carga\n\n";
					$messages.="El recojo queda programado el: $hora_recojo a las $hora_recojo_2 \n";
					//$messages.="Favor, volver a confirmar la hora \n\n";
					//$messages.="La unidad es la siguiente: \n\n";
					$messages.="Transportista Asignado: $conductor \n";
					$messages.="Empresa: $company_name \n";
					$messages.="Teléfono: $cel_transport \n";
					$messages.="N° de Licencia de Conducir: $licencia \n";
					$messages.="DNI: $conductor_dni \n";
					$messages.="Placa Tracto: $placa_tracto\n";
					$messages.="Placa Trailer: $placa_trailer\n";
					$messages.="Tipo de trailer: $tipo_trailer\n";
					$messages.="Tipo de carga: $tipo_carga\n";
					$messages.="Precio: S/ $amount + IGV \n";
					$messages.="Descripción: $descripcion \n\n";
					$messages.="Bauen Freight SAC 2020\n\n";
					
					break;
						
						
				default:
					// no email send
					$iemal_seed_to_sent=false;
					break;
			}
			
			if($iemal_seed_to_sent){
				$this->load->library('email');
				$config = Array(
					'protocol' => 'mail',
				);
				
				$this->email->initialize($config);
				$this->email->set_newline("\r\n");
				$this->email->mailtype="both";
				
				$this->email->from('no-reply@bauenfreight.com','Bauen.');
				$this->email->to($email);
                
                if($email_for == 8) {
                	$this->email->cc('sergio@bauenfreight.com');	
                }
				
				$this->email->subject($subjects);
				$this->email->message($messages);
				$this->email->set_alt_message('BAUEN Default Message');
				$this->email->send();
			}
		}
	}
	
	public function dateformatvalidate($date=''){
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)){
			return true;
		} else {
			return false;
		}
	}
	
	public function requestmapimage($locations=array(), $request_id = 0,$is_saved=0){
		$image_name='';
		$is_image_generat=true;
		if(empty($locations)){
			// need to get the location objects of the request 
			if(!empty($request_id)){
				$find_request = array(
					'request_id'=>$request_id
				);
				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
				if(!empty($request)){
					$locations['markers'][]=array(
						'lat'=>$request['pickup_latitude'],
						'long'=>$request['pickup_longitude'],
						'place'=>'pickup',
					);
					$locations['markers'][]=array(
						'lat'=>$request['dropoff_latitude'],
						'long'=>$request['dropoff_longitude'],
						'place'=>'dropoff',
					);
				}
				else{
					$is_image_generat=false;
				}
			}
			else{
				$is_image_generat=false;
			}
		}
		// map image create section 
		$markers = "";
		$patha = "";
		if($is_image_generat && !empty($locations)){
			// live 
			$api_key="AIzaSyCAeL_PSmeFP-hNntq8_v6d5wLf6hBdlhY";
			$map_url = "https://maps.googleapis.com/maps/api/staticmap?key=$api_key&size=320x180&format=JPEG";
			$marker_points = isset($locations['markers'])?$locations['markers']:array();
			$path_points = isset($locations['path'])?$locations['path']:array();
			$start_point= array();
			$end_point = array();
			if(!empty($marker_points)){
				// $color="green";
				$color="yellow";
				foreach($marker_points as $marker){
					$icon = $marker['icon'];
					$place = $marker['place'];
					$lat = $marker['lat'];
					$long = $marker['long'];

					if(!empty($icon)) {
						$markers.="&markers=icon:$icon%7C$lat,$long";
					} else {
						$markers.="&markers=color:$color%7Clabel:$place%7C$lat,$long";
						if(!empty($color)){
							$color="yellow";
						}
						if(empty($start_point)){
							$start_point[]=array(
								'lat'=>$lat,
								'long'=>$long
							);
						}
						else{
							if(empty($end_point)){
								$end_point[]=array(
									'lat'=>$lat,
									'long'=>$long
								);
							}
							
						}
					}
				}
			}

			$this->debugcontroller($markers);
			
			if(!empty($path_points)){
				// need to find all the path of the request
				// merge all the lat long
				$path_points = array_merge($start_point,$path_points,$end_point);
				$patha="&path=color:red|weight:2";
				foreach($path_points as $path_point){
					$patha.="|".$path_point['lat'].",".$path_point['long'];
				}
				
			}
			
			$map_url.=$markers.$patha;
			
			$image_name = time()."request_".$request_id.".jpg";
			copy($map_url,'uploads/requests/'.$image_name);
			
			/*
			$imageString = file_get_contents($map_url);
			file_put_contents('uploads/requests/'.$image_name,$imageString);
			*/
			
			if($is_saved=='1' && $request_id>0){
				$update_cond=array(
					'request_id'=>$request_id
				);
				$update_data=array(
					'request_image'=>$image_name
				);
				$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$update_cond);
			}
			//$image_name=$map_url;
		}
		return $image_name;
	}
	
	//PUSH NOTIFICATION SECTION 
	public function add_notification($add_data=array(),$is_return=0,$is_saved=1){
		
		
		/* if(isset($_SERVER['APP_ENV'])){
			if($_SERVER['APP_ENV'] == 'development'){
				return;
			}
		} */
		
		$response_data=array();
		$this->debug($add_data);
		$this->load->helper(array('array'));
		$saved_fields=array('request_id','user_id','receiver_user_id','notification_type');
		$saved_data=elements($saved_fields,$add_data);
		// get request details
		$request_id = isset($add_data['request_id'])?$add_data['request_id']:'0';
		$receiver_user_id = isset($add_data['receiver_user_id'])?$add_data['receiver_user_id']:'0';
		$notification_type = isset($add_data['notification_type'])?$add_data['notification_type']:'0';
		$chat_id = isset($add_data['chat_id'])?$add_data['chat_id']:'0';
		$request=array();
		//if($chat_id=='0' && $request_id>0){
		if($request_id>0){
			$find_cond=array(
				'request_id'=>$request_id
			);
			$requests = $this->getrequests($find_cond);
			if(!empty($requests)){
				$request=$requests[0];
			}
		}
		// if chat info get the sender information 
		if($chat_id>0){
			$sender_data = array(
				'sender_first_name'=>$this->logged_user['first_name'],
				'sender_last_name'=>$this->logged_user['last_name'],
				'sender_image'=>'',
				'sender_phone_no'=>$this->logged_user['phone_no'],
			);
			
			if(!empty($this->logged_user['image'])){
				$sender_data['sender_image'] = base_url('uploads/users/'.$this->logged_user['image']);
			}
			if(!empty($request)){
				$request = array_merge($request,$sender_data);
			}
		}
		else{
			$sender_data = array(
				'sender_first_name'=>'',
				'sender_last_name'=>'',
				'sender_image'=>'',
				'sender_phone_no'=>'',
			);
		}
		
		$data['bauen'] = array(
			'notification_type'=>$notification_type,
			'request'=>(object)$request,
			'chat_id'=>$chat_id,
		);
		// marge data 
		if(!empty($sender_data)){
			$data['bauen']=array_merge($data['bauen'],$sender_data);
		}
		
		$response_data['data']=$data;
		$response_data['saved_data']=$saved_data;
		$notification_text = $this->getnotificationtypes($add_data);
		
		if(!empty($notification_text)){
			$saved_data['notification_text']=$notification_text;
			$saved_data['create_date']=$this->dateformat;
			$saved_data['update_date']=$saved_data['create_date'];
			if($is_saved){
				$notification_id = $this->BaseModel->insertData($this->tableNameRequestNotification,$saved_data);
				// update the user notification table 
				$this->update_user_count($receiver_user_id,1,1);
			}
			else{
				$notification_id='1';
			}
			
			if($notification_id>0){
				$saved_data['data']=$data;
				$this->debug(array('sendpushnotification' => '2001', $saved_data));
				$result = $this->sendpushnotification($saved_data);

				// $result = $this->notificationpush2('Bauen', array('e6buSisLBVU:APA91bGPKvtaaEaDdDW_WPvwTP9sD5glQfgqiX1syHhmLVcibNEc-Vgl96NKSAIYHEuUlYsAF3_m99-PxEEVfFl3LZXqhZMkLjOwRGPFASPmvgjJnZ2Ndn_WDCqvOWIS4FfnXAu834rk'), $saved_data['notification_text'], $saved_data['receiver_user_id'], 1, array());
	
				$response_data['push_response']=$result;
				$this->debug(array($result));
			}
		}
		$response_data['notification_text']=$notification_text;
		return $response_data;
	}

	/** test */

	public function notificationpush2($title='Bauen',$device_push_key=array(),$message='',$user_id=0,$screen,$adicional){
		$result=array();
		$url = 'https://fcm.googleapis.com/fcm/send';

		$fields = array (
				'registration_ids' => $device_push_key,
				'notification' => array(
						'title' => $title,
						'body' => $message
				),
				'notification_type' =>  $screen,
				'data' => array (
						'message' => $message,
						// 'type_notification' => $screen,
						'notification_type' => $screen,
						'user_id' => $user_id,				
						'vibrate'	=> 1,
						'sound'		=> 1,
						'data_chat'	=> $adicional
				)
		);
		
		$headers = array(
				// 'Authorization: key=' . $this->fbkey,
				'Authorization: key=AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz',
				'Content-Type: application/json'
		);	


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		if ($result === FALSE) {
				$result = curl_error($ch);
		}
		
		curl_close($ch);
		return $result;
}
/** Test */
	
	public function sendpushnotification($push_data=array()){
		$user_id = isset($push_data['receiver_user_id'])?$push_data['receiver_user_id']:0;
		$result=array();
		if($user_id>0){
			$find_user_push_keys=array(
				'user_id'=>$user_id,
				'is_blocked'=>'0',
				'is_deleted'=>'0',
				'device_push_key !='=>'',
			);
			$select_fields='device_type,GROUP_CONCAT(device_push_key) device_push_key';
			$joins=array(
				array(
					'table_name'=>$this->tableNameUser,
					'join_type'=>'inner',
					'join_with'=>$this->tableNameUserRequestKey,
					'join_on'=>array('user_id'=>'user_id'),
					'oncond'=>array('is_blocked'=>'0','is_deleted'=>'0'),
					'select_fields'=>array('user_type')
				)
			);
			$group_by=array('device_type');
			$tb = $this->dbprefix.$this->tableNameUserRequestKey;
			$cudate=$this->dateformat;
			$complexcond=" DATEDIFF('$cudate',$tb.create_date) <=2 ";
			// $users = $this->BaseModel->getDatas($this->tableNameUserRequestKey,$find_user_push_keys,$select_fields,array(),$joins,0,0,$complexcond,$group_by);

			$conditions = array(
				'user_id' => $user_id,
				'is_deleted' => 0,
				'is_blocked' => 0
			);
			$selectFields = array('device_push_key', 'user_type', 'device_type');
			$orderBy = array('update_date' => 'DESC');

			$users = $this->BaseModel->getDatas($this->tableNameUserRequestKey, $conditions);

			// $this->debug(array('sendpushnotification' => '2089', $users));
			// $this->debug(array('sendpushnotification' => '2089', 'findKey' => $findKey));
			
			if(!empty($users)){
				foreach($users as $user){
					$user_type = 0;
					$device_type = $user['device_type'];
					$device_push_key = $user['device_push_key'];
					$device_push_keys = explode(",",$device_push_key);
					
					$messages=array(
						'title'=>'Bauen',
						'body'=>ucfirst($push_data['notification_text']),
						'data'=>$push_data['data'],
					);
					$user_type=$user_type+1;

					
					/* if($device_type!=null){
						if($device_type=='1'){ // android
							$result['andro_d']=$device_push_keys;
							$result['andro'][]= $this->android_push($messages,$device_push_keys,$user_type);
							$this->debug(array('sendpushnotification' => '2110', 'messges' => $messages,'device_push_keys' => $device_push_keys,'user_type' => $user_type));
						}
						elseif($device_type=='2'){ // ios
							if(array_key_exists("request_id", $user)) {
								$result['ios_d']=$device_push_keys;
								$result['ios'][] = $this->ios_push2($messages,$device_push_keys,'2' , $user['request_id']);
							}
						}
					} */

					$result['andro_d']=$device_push_keys;
							$result['andro'][]= $this->android_push($messages,$device_push_keys,$user_type);
							$this->debug(array('sendpushnotification' => '2110', 'messges' => $messages,'device_push_keys' => $device_push_keys,'user_type' => $user_type));

					if(array_key_exists("request_id", $user)) {
								$result['ios_d']=$device_push_keys;
								$result['ios'][] = $this->ios_push2($messages,$device_push_keys,'2' , $user['request_id']);
							}		
					
				}
			}
		}
		return $result;
	}

public function sendpushnotificationSimple($push_data=array(), $message, $request_id = '', $notification_type = 0){
	
	/* if(isset($_SERVER['APP_ENV'])){
			if($_SERVER['APP_ENV'] == 'development'){
				return;
			}
	} */
	
	$user_id = isset($push_data['receiver_user_id']) ? $push_data['receiver_user_id'] : 0;
	$result=array();

	if($user_id>0) {
		$find_user_push_keys=array(
			'user_id'=>$user_id,
			'is_blocked'=>'0',
			'is_deleted'=>'0',
			'device_push_key !='=>'',
		);

		$select_fields='device_type,GROUP_CONCAT(device_push_key) device_push_key';
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_type'=>'inner',
				'join_with'=>$this->tableNameUserRequestKey,
				'join_on'=>array('user_id'=>'user_id'),
				'oncond'=>array('is_blocked'=>'0','is_deleted'=>'0'),
				// 'select_fields'=>array('user_type', 'device_push_key', 'device_type', 'request_id') //Solo estaba user_type
				'select_fields'=>array('user_type') //Solo estaba user_type
			)
		);
		$group_by=array('device_type');
		$tb = $this->dbprefix.$this->tableNameUserRequestKey;
		$cudate=$this->dateformat;
		$users = $this->BaseModel->getDatas($this->tableNameUserRequestKey,$find_user_push_keys,$select_fields,array(),$joins,0,0,array(),$group_by);
	
		
		if(!empty($users)){
			foreach($users as $key => $user){
				$user_type = $user['user_type'];
				$device_type = $user['device_type'];
				$device_push_key = $user['device_push_key'];
				// $request_id = $user['request_id']; //Agregado
				$device_push_keys = explode(",",$device_push_key);
				
				$messages=array(
					'title'=>ucfirst($push_data['notification_text']),
					'body'=>ucfirst($push_data['notification_text']),
					'data'=>$push_data['data'],
				);
				$user_type=$user_type+1;

				if($device_push_key!=null){
					if($device_type=='1'){ // android
						$result['andro_d']=$device_push_key;
						$result['andro'][]= $this->android_push($message,$device_push_keys,$user_type,$request_id, $notification_type);
					}
					elseif($device_type=='2'){ // ios
						$result['ios_d']=$device_push_key;
						$result['ios'][] = $this->ios_push2($message,$device_push_keys, '0', $request_id);
					}
				}
				
			}
		} 
	}
	return $result;
}

public function customer_push($user_id, $message, $screen=0, $request_id=0) {

	$result=array();

		$find_user_push_keys=array(
			// 'user_id'=>525,
			'user_id'=> $user_id,
		);
		$select_fields='device_type,firebase_id,GROUP_CONCAT(device_push_key) device_push_key';
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_type'=>'inner',
				'join_with'=>$this->tableNameUserRequestKey,
				'join_on'=>array('user_id'=>'user_id'),
				'oncond'=>array('is_blocked'=>'0','is_deleted'=>'0'),
				'select_fields'=>array('user_type')
			)
		);
		$group_by=array('device_type');
		$tb = $this->dbprefix.$this->tableNameUserRequestKey;
		$cudate=$this->dateformat;
		$users = $this->BaseModel->getDatas($this->tableNameUserRequestKey,$find_user_push_keys,$select_fields,array(),$joins,0,0,array(),$group_by);
		if(!empty($users)){
			foreach($users as $key => $user){
				$user_type = $user['user_type'];
				$device_type = $user['device_type'];
				$device_push_key = $user['device_push_key'];
				//device_push_id
				$device_push_keys = explode(",",$device_push_key);
				$user_type=$user_type+1;

				if($device_type=='1'){ // android
					$result['andro_d']=$device_push_keys;					
					$result['andro'][]= $this->push($message,$device_push_keys, $screen);
					
				}
				elseif($device_type=='2'){ // ios
					$result['ios_d']=$device_push_keys;
					$result['ios'][] = $this->ios_push2($message,$device_push_keys, $screen, $request_id);
				}
			}
		}

	return $result;
}


public function ios_push2($messages=array(),$device_push_key=array(), $screen='', $request_id='') {
	if($screen == '3'){
		return false;
	}
	$result=array();
	$andro_app_id='AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz'; // customer app 
	$url = 'https://fcm.googleapis.com/fcm/send';
	
	$this->notificaciones___(array('IOS PUSH'));
	
	if(empty($messages["data"])){	
		$fields = array (
			'registration_ids' => $device_push_key,
			'notification' => array(
				'title' => 'Bauen',
				'body' => $messages
			),
			'data' => array (
				'message' => $messages,
				'type_notification' => $screen,
				'request_id' => $request_id,				
				'vibrate'	=> 1,
				'sound'		=> 1
			)
		);	
	}else{
			$data_response["registration_ids"] = $device_push_key;
			$data_response["notification"] = array(
						'title' => 'Bauen',
						'body' => $messages["body"]
					);
		
			$datatemp = $messages['data']['bauen']['request'];
			$data_response["data"]["request_id"] = $datatemp->request_id;
			$data_response["data"]["user_id"] = $datatemp->user_id;
			$data_response["data"]["message"] = $messages["body"];
			$data_response["data"]["type_notification"] = $screen;
			$data_response["data"]["vibrate"] = 1;
			$data_response["data"]["sound"] = 1;
			$fields = $data_response;
	}

	$this->notificaciones___($fields);
	$this->notificaciones___(array('END IOS PUSH'));
	$headers = array(
		'Authorization: key=' . $andro_app_id,
		'Content-Type: application/json'
	);	

	
	$ch = curl_init();
	// Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Disabling SSL Certificate support temporarly
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	// Execute post
	$result = curl_exec($ch);
	if ($result === FALSE) {
		$result = curl_error($ch);
	}
	// Close connection
	$this->debug($result);
	curl_close($ch);
	return $result;
}

	public function push($message='',$device_push_key=array(), $screen = ''){	
		if($screen == '3'){
			return false;
		}
		$result=array();
		$andro_app_id='AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz'; // customer app 
		// Set POST variables
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array (
			'registration_ids' => $device_push_key,
			'notification' => array(
				'title' => 'Bauen Freight',
				'message'  => $message
			)
		);

		$this->debug(array('Android push'));
		$this->debug($fields);

		$headers = array('Authorization: key=' .$andro_app_id,'Content-Type: application/json'); 
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS,  $data);	
		$result = curl_exec($ch );
		curl_close($ch);
		return $result;
}
	public function android_push($messages=array(),$device_push_key=array(),$push_for=0,$request_id='', $notification_type=0){
		//$this->debug(array('Android'));
		//$this->debug($device_push_key);
		$fichero = 'gente.txt';
		$actual = file_get_contents($fichero);
		$actual .= "John Smith\n";
		//file_put_contents($fichero, $results);
		file_put_contents($fichero, $actual);
		
		$result=array();
		if(!empty($device_push_key) && $push_for>0) {
			$andro_app_id='AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz'; // customer app key fcm
			if(!empty($andro_app_id)){
				// Set POST variables
				$this->debug(array('Android'));
				$url = 'https://fcm.googleapis.com/fcm/send';
				// add click event on notification message and sound
				if(isset($messages[0]) && count($messages[0])>0){
					$request_conditions = array(
						'request_id' => $request_id
					);

					$request_data = $this->BaseModel->getData($this->tableNameRequest,$request_conditions);
					$fields = array (
						'registration_ids' => $device_push_key,
						'notification' => array(
							'title' => 'Bauen',
							'message'=> $messages,
							'body'=> $messages
						)
					);
					$fields["data"]["message"] = $messages;
					$fields["data"]["request"] = $request_data;
					$fields["data"]["notification_type"] = $notification_type;
					
					
				}else{
					$data_response["registration_ids"] = $device_push_key;
					$data_response["notification"] = array(
						'title' => 'Bauen',
						'message' => $messages["body"],
						'body' => $messages["body"]
					);

					$data_response["data"]["message"] = $messages["body"];
					$data_response["data"]["notification_type"] = $messages['data']['bauen']['notification_type'];
					$datatemp = $messages['data']['bauen']['request'];
					//$data_response["data"]["request_id"] = $datatemp->request_id;
					$data_response["data"]["request"] = $datatemp;
					$data_response["data"]["user_id"] = $datatemp->user_id;
					$fields = $data_response;
					if($data_response["data"]["notification_type"] == '3'){
						return false;
					}
				}
				
				$headers = array(
					'Authorization: key=' . $andro_app_id,
					'Content-Type: application/json'
				);
				
				
				$this->debug(array('Array Android'));
				$this->debug($fields);
				
				
				$ch = curl_init();
				// Set the url, number of POST vars, POST data
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// Disabling SSL Certificate support temporarly
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				// Execute post
				$result = curl_exec($ch);
				if ($result === FALSE) {
					$result = curl_error($ch);
				}

				$this->debug($result);
				$this->debug(array('end Android'));
				curl_close($ch);
			}
		}
		return $result;
	}
	
	public function ios_push($messages=array(),$device_push_keys=array(),$push_for=0){
		// $push_for , 1: customer, 2 : transportet/driver
		$result=array();
		if(!empty($device_push_keys) && $push_for>0){
			$production=true;
			$passphrase="";
			$pem_dir="assets/iospemcert/";
			$pem_cert_name = "";
			
			if($push_for=='1'){
				// customer app key fcm
				if ($production) {
					$gateway = 'ssl://gateway.push.apple.com:2195';
					$passphrase="";
					//$pem_cert_name = "BauenFreight_Customer_PEM.pem";
					$pem_cert_name = "Bauen_Cust_PEM.pem";
				} else { 
					$gateway = 'ssl://gateway.sandbox.push.apple.com:2195';
					$passphrase="";
					//$pem_cert_name = "BauenFreight_Customer_PEM.pem";
					$pem_cert_name = "Bauen_Cust_PEM.pem";
				}
			}
			elseif($push_for=='2'){
				// trans/driver app key fcm
				if ($production) {
					$gateway = 'ssl://gateway.push.apple.com:2195';
					$passphrase="";
					//$pem_cert_name = "BauenFreight_Production.pem";
					$pem_cert_name = "Bauen_Transporter_PEM.pem";
				} else { 
					$gateway = 'ssl://gateway.sandbox.push.apple.com:2195';
					$passphrase="";
					//$pem_cert_name = "BauenFreight_Production.pem";
					$pem_cert_name = "Bauen_Transporter_PEM.pem";
				}
			}
			
			if(!empty($pem_cert_name)){
				$pem_file_path = $pem_dir.$pem_cert_name;
				if(file_exists($pem_file_path)){
					// push mesage section 
					// Create the payload body
					$data = array();
					if(isset($messages['data'])){
						$data = $messages['data'];
						unset($messages['data']);
					}
					$body['aps'] = array(
						'alert' =>$messages['title'],
						'sound' =>'1',
						'content-available' => '1',
						'others'=>$messages['body'],
						'data'=>$data,
					);
					$this->notificaciones___($body);
					foreach($device_push_keys as $deviceToken){
						$ctx = stream_context_create();
						//certificate file
						stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_file_path);
						stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
						// Open a connection to the APNS server
						$fp = stream_socket_client(
							$gateway, $err,
							$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
						if (!$fp){
							$result[] = "Failed to connect: $err $errstr" . PHP_EOL;
							//return $result;
							fclose($fp);
						}
						else{
							// Encode the payload as JSON
							$payload = json_encode($body);
							// Build the binary notification
							$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
							// Send it to the server
							stream_set_blocking($fp, 0);
							$result[] = fwrite($fp, $msg, strlen($msg));
							// Close the connection to the server
							fclose($fp);
						}
					}
				}
				else{
					$result="Pem not found";
				}
			}
		}
		return $result;
	}
	
	// END PUSH NOTIFICATION
	
	//TWILIO SMS
	/*
	@param $sid get from twilio a/c
	@param $token get from twilio a/c
	@param $to receiver phone numner with country calling code
	@param $from client register number with twilio
	@param $body sending sms mesage
	*/
	function twilio_send_sms( $to='', $body='' ) {
		$sid = 'ACcd6a5da0d009b1d2119ba20ec90a469b';
		$token = '220b3f801a413127d0e31a504e936935';
		$from='+18583754121';
		
		$result='';
		$erro='';
		if(empty($to) || empty($body)){
			return array('result'=>$result,'error'=>$erro);;
		}
		$to = $this->countryCallingCode.$to;
		
	   // resource url & authentication
	   $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json';
	   $auth = $sid . ':' . $token;
	   $fields = 
		   '&To=' .  urlencode( $to ) . 
		   '&From=' . urlencode( $from ) . 
		   '&Body=' . urlencode( $body );

	   // start cURL
	   $res = curl_init();
	   // set cURL options
	   curl_setopt( $res, CURLOPT_URL, $uri );
	   curl_setopt( $res, CURLOPT_POST, 3 ); // number of fields
	   curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
	   curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
	   curl_setopt( $res, CURLOPT_RETURNTRANSFER, true ); // don't echo
	   curl_setopt( $res, CURLOPT_SSL_VERIFYHOST, 0);
	   curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, 0);
		
	   // send cURL
	   $result = curl_exec( $res );
	   if($result===false){
		$erro = curl_error($res);	 
	   }
	   else{
		   $erro=array();
	   }
	   curl_close( $res );
	   
	   return array('result'=>$result,'error'=>$erro);
	}
	
	
}
?>