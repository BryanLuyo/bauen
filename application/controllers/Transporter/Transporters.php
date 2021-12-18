<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transporters extends MY_Controller{
	public $transporter_id;
	function __construct(){
		/**MODE DEV ERROR INIT*/
		/*ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);*/
		/**END DEV */
		parent::__construct();
	}
	
	public function index(){
		redirect(BASE_FOLDER_TRANS.'login');
	}
	
	public function login(){
		$this->trans_session_on();
		$data=array();
		$this->load->library(array('form_validation'));
		$rules = array(
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email|callback_emailpresent',
				'errors'=>array(
					'emailpresent'=>'No se encontró un usuario con el correo %s.'
				)
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$find_user=array(
				'email'=>$this->input->post('email'),
				'password'=>md5($this->input->post('password')),
				'user_type'=>'1',
				'is_company'=>'1'
			);
			$select_fields=array('user_id','first_name','is_company', 'is_user_verify','user_type','super_parent_id', 'is_blocked');
			$admin = $this->BaseModel->getData($this->tableNameUser,$find_user,$select_fields);
			if(empty($admin)){
				$rules = array(
					array(
						'field'=>'user_not_match',
						'label'=>'',
						'rules'=>'trim|required',
						'errors'=>array(
							'required'=>'El correo y/o la contraseña no coinciden.'
						)
					),
				);
				$this->form_validation->set_rules($rules);
				$this->form_validation->run();
			}
			else{
				$this->trans_session_set($admin);
				redirect(BASE_FOLDER_TRANS.'dashboard');
			}
		}
		
		$this->loadviewtrans('login',$data);
	}

	public function verification() {
		$this->trans_session_unverified();
		$data= array();

		if( $this->input->post('code01') != null ) {
			$code01 = $this->input->post('code01');
			$code02 = $this->input->post('code02');
			$code03 = $this->input->post('code03');
			$code04 = $this->input->post('code04');
			$code05 = $this->input->post('code05');
			$code06 = $this->input->post('code06');

			if(is_numeric($code01) && is_numeric($code02) && is_numeric($code03) && is_numeric($code04) && is_numeric($code05) && is_numeric($code06)) {
				$verification_code = $code01.$code02.$code03.$code04.$code05.$code06;
				$transporter_id = $this->session->userdata(SES_TRANS_ID);
				$find_user=array(
					'user_id'=>$transporter_id,
					'user_type'=>'1',
					'is_company'=>'1'
				);
				$admin = $this->BaseModel->getData($this->tableNameUser,$find_user,array('verification_code'));
				if(empty($admin)) {
					$data['verification_error'] = 'Ocurrió un error inesperado. Por favor, póngase en contacto con el equipo técnico.';
				} else {
					if($admin['verification_code'] == $verification_code) {
						$this->BaseModel->updateDatas($this->tableNameUser,array('is_user_verify' => 1),$find_user);
						$this->trans_session_validate_current_user();
						redirect(BASE_FOLDER_TRANS.'dashboard');
					} else {
						$data['verification_error'] = 'El código ingresado es incorrecto. Por favor, verifique e intente nuevamente.';
					}
				}
			} else {
				$data['verification_error'] = 'Ingrese los 6 dígitos de su código de verificación.';
			}
		}

		$this->loadviewtrans('verification', $data);
	}
	
	public function emailpresent($email=''){
		if(!empty($email)){
			$find_cond = array(
				'email'=>$email
			);
			$tableRow = $this->BaseModel->tableRow($this->tableNameUser,$find_cond);
			if(!$tableRow){
				return false;
			}
		}
		return true;
	}
	
	public function logout(){
		$this->trans_session_destroy();
	}
	
	public function dashboard(){
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);

		$data=array();
		$staticson=array('bids'=>'0','winbids'=>'0','completedbids'=>'0','lostbids'=>'0');
		$total_counts=array(
			'today'=>$staticson,
			'week'=>$staticson,
			'month'=>$staticson,
		);
		$select_section='count(*) total_record';
		$week_no = date("W");
		$month_no = date("n");
		$yr=date("Y");

		
		if($this->session->userdata(SES_TRANS_BLOCKED) == 2) {
			$data['blocked'] = true;
		}


		foreach($total_counts as $key=>$static){
			foreach($static as $stat_for=>$stat_val){
				$table_name='';
				$main_fld='update_date';
				$conditions=array();
				if($stat_for=='bids'){
					$main_fld='create_date';
					$table_name=$this->tableNameRequestBid;
					$conditions['user_id']=$this->transporter_id;
				}
				elseif($stat_for=='winbids'){
					$table_name=$this->tableNameRequestBid;
					$conditions['bid_status']='2';
					$conditions['user_id']=$this->transporter_id;
				}
				elseif($stat_for=='lostbids'){
					$table_name=$this->tableNameRequestBid;
					$conditions['bid_status']='4';
					$conditions['user_id']=$this->transporter_id;
				}
				elseif($stat_for=='completedbids'){
					$table_name=$this->tableNameRequest;
					$conditions['transporter_id']=$this->transporter_id;
					$conditions['request_status']=REQUEST_COMPLED_STATUS;
					$conditions['driver_id >']='0';
					$conditions['vehicle_id >']='0';
				}
				else{
					
				}
				
				// logics 
				if($key=='today'){
					$conditions['DATE('.$main_fld.')']=date("Y-m-d");
				}
				elseif($key=='week'){
					$conditions['WEEK('.$main_fld.')']=$week_no;
					$conditions['YEAR('.$main_fld.')']=$yr;
				}
				elseif($key=='month'){
					$conditions['MONTH('.$main_fld.')']=$month_no;
					$conditions['YEAR('.$main_fld.')']=$yr;
				}
				else{
					
				}
				
				$conditions['is_deleted']='0';
				$conditions['is_blocked']='0';
				if(!empty($table_name)){
					$stat_val = $this->BaseModel->tableRow($table_name,$conditions);
				}
				$total_counts[$key][$stat_for]=$stat_val;
			}
		}
		//$this->pr($total_counts);
		// assing data 
		$data['total_counts']=$total_counts;
		//get dashboard request 
		
		//verifica si hay usuarios transporter filtrados por usuarios
		
		$find_cond=array(
			'id_transporter'=>$this->transporter_id
		);
		
		$tranporterFilter = $this->BaseModel->getDatas($this->tableNameUserTransporter,$find_cond);
		
		$tfc_assos = array();
		
		$find_request_premium  = array();
		
		if(count($tranporterFilter) > 0) {
			
			// $tfc_assos = array ('id_transporter' => $this->transporter_id);
			
			
		} else {
			
			$find_request_premium = array('is_premium' => '0');
			
		}
	
		$find_request = array(
			'request_status <'=>'2',
			'is_deleted' => 0,
		);
		
		$find_request = array_merge($find_request_premium,  $find_request);
		
		
		
		
		//verifica si vencio el tiempo de request
		
		if(isset($_SERVER['APP_ENV'])){
			if($_SERVER['APP_ENV'] == 'development'){
				 $fecha_servidor = ' NOW() ';
			}
		} else {
				 $fecha_servidor = ' DATE_ADD(NOW(), INTERVAL 2 HOUR) ';
		} 	
		
		$find_request['complexCondition'] = ' IF(`trns_requests`.`close_bid_time`IS NULL , '.$fecha_servidor.' , `trns_requests`.`close_bid_time`) >=  '."'".$this->dateformat."'";
		
		$assos_cond=array(
			'trans_bid_cond'=>array(
				'user_id'=>$this->transporter_id
			),
			
			'tfc_assos' => $tfc_assos
		);
		
		$requests = $this->getrequests($find_request,$assos_cond);
		
		$data['requests']=$requests;
		
		
		$this->loadviewtrans('dashboard',$data);
	}
	
	
	public function propuestas(){
		
		$this->trans_session_off();
		$this->transporter_id=$this->session->userdata(SES_TRANS_ID);
		$data=array();

		if($this->session->userdata(SES_TRANS_BLOCKED) == 2) {
			$data['blocked'] = true;
		}  

		//verifica si hay usuarios transporter filtrados por usuarios
		
		$find_cond=array(
			'id_transporter'=>$this->transporter_id
		);
		
		$tranporterFilter = $this->BaseModel->getDatas($this->tableNameUserTransporter,$find_cond);
			
		$tfc_assos = array();
		
		$find_request_premium  = array();
		
		if(count($tranporterFilter) > 0) {
			// $tfc_assos = array ('id_transporter' => $this->transporter_id);	
			
		} else {
			$find_request_premium = array('is_premium' => '0');
		} 

		$find_request = array(
			// 'pickup_date >='=>date("Y-m-d"),
			'request_status >'=>'1',
			'is_deleted' => 0,
		);

		
		$find_request = array_merge($find_request_premium,  $find_request);
		
		
		//verifica si vencio el tiempo de request
		
		/* if(isset($_SERVER['APP_ENV'])) {
			if($_SERVER['APP_ENV'] == 'development'){
				 $fecha_servidor = ' NOW() ';
			}
		} else {
				 $fecha_servidor = ' DATE_ADD(NOW(), INTERVAL 2 HOUR) ';
		} 	*/
		
		
		//$find_request['complexCondition'] = ' IF(`trns_requests`.`close_bid_time`IS NULL , '.$fecha_servidor.' , `trns_requests`.`close_bid_time`) >=  '."'".$this->dateformat."'";
		
		
		$find_request['complexCondition'] = 'trns_request_bids.user_id = '.$this->transporter_id ;
	
		
		$assos_cond=array(
			'trans_bid_cond'=>array(
				'user_id'=>$this->transporter_id
			)
		);
		

		$requests = $this->getrequests($find_request,$assos_cond);
		
		
		$data['requests']=$requests;
		$this->loadviewtrans('propuestas',$data);
	}
	
	
	public function profile(){
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);
		$data=array();
		$ins_tab='active';
		$sub_tab='';
		// form section 
		$this->load->library(array('form_validation'));
		$rules=array();
		if($this->input->post('ins_post')){
			$sub_tab='';
			$rules=array(
				array(
					'field'=>'support_instruction',
					'label'=>'Instruction',
					'rules'=>'trim|required',
					'errors'=>array()
				)
			);
		}
		elseif($this->input->post('sup_post')){
			$ins_tab='';
			$sub_tab='active';
			$rules=array(
				array(
					'field'=>'support_contact',
					'label'=>'Contact',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'support_email',
					'label'=>'Email',
					'rules'=>'trim|required|valid_email',
					'errors'=>array()
				),
				
			);
		}
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data['update_date']=$this->dateformat;
			if($this->input->post('support_instruction')){
				$save_data['support_instruction']=$this->input->post('support_instruction');
			}
			if($this->input->post('support_email')){
				$save_data['support_email']=$this->input->post('support_email');
			}
			if($this->input->post('support_contact')){
				$save_data['support_contact']=$this->input->post('support_contact');
			}
			
			$update_cond=array(
				'user_id'=>$this->transporter_id,
				'user_type'=>'1',
				'is_company'=>'1'
			);
			//$this->pr($update_cond);
			$this->BaseModel->updateDatas($this->tableNameUser,$save_data,$update_cond);
			$this->session->set_flashdata('success','Detalles actualizados.');
			
		}
		
		// select section 
		$find_cond=array(
			'user_id'=>$this->transporter_id
		);
		$select_fields=array('user_id','parent_user_id','user_type','first_name','last_name','email','phone_no','image','is_user_verify','is_phone_no_verify','is_email_verify','is_company','dni_no','ruc_no','industrytype_id','company_name','company_licence_no','support_instruction','support_email','support_contact','is_blocked');
		
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameUserRating,
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id'),
				'select_fields'=>'AVG(rating) rating',
				'oncond'=>array('is_deleted'=>'0')
			),
			array(
				'table_name'=>$this->tableNameVehicle,
				'table_name_alias'=>'vhl',
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>'count(vehicle_id) total_vehicle',
				'oncond'=>array('is_deleted'=>'0')
			),
			array(
				'table_name'=>$this->tableNameIndustryType,
				'table_name_alias'=>'',
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('industrytype_id'=>'industrytype_id'),
				'select_fields'=>array('industrytype_name'),
				'oncond'=>array('is_deleted'=>'0')
			),
		);
		$offset=0;
		$limit=1;
		$complexconditions=array();
		$group_by=array();
		
		$user = $this->BaseModel->getDatas($this->tableNameUser,$find_cond,$select_fields,$order_by,$joins,$offset,$limit,$complexconditions,$group_by);
		
		if(empty($user)){
			redirect(BASE_FOLDER_TRANS.'logout');
		}
		$user = $user[0];
		// now get total nnumber driver 
		$find_driver=array(
			'parent_user_id'=>$this->transporter_id,
			'user_type'=>'1',
			'is_company'=>'0'
		);
		$extra=array('is_count'=>'1');
		$total_driver = $this->getdrivers($find_driver,$extra);
		$user['total_driver']=$total_driver;
		// request section 
		$find_req=array(
			'transporter_id'=>$this->transporter_id,
		);
		$extra=array('count'=>'1','fields'=>array('request_id'));
		$total_request = $this->getrequests($find_req,$extra);
		$user['total_request']=$total_request;
		
		// get documet types 
		$documenttypes = $this->getdocumenttypes(array('document_for'=>'1'));
		$find_document=array(
			'user_id'=>$this->transporter_id
		);
		$documents = $this->getuserdocuments($find_document);
		//
		$data['user']=$user;
		$data['documenttypes']=$documenttypes;
		$data['documents']=$documents;
		$data['ins_tab']=$ins_tab;
		$data['sub_tab']=$sub_tab;
		//$this->pr($data);
		$this->loadviewtrans('profile_view',$data);
	}

	public function subadmins(){
		$this->trans_session_off();
		$this->transporter_id=$this->session->userdata(SES_TRANS_ID);
		$creater_id=$this->session->userdata(SES_CREATOR_ID);
		$data=array();
		//find the subadmins 
		$find_cond=array(
			'super_parent_id'=>$this->transporter_id
		);
		$subadmins = $this->BaseModel->getDatas($this->tableNameUser,$find_cond);
		$data['subadmins']=$subadmins;
		$this->loadviewtrans('subadmin_list',$data);
	}
	
	public function addsubadmin(){
		$this->trans_session_off();
		$this->transporter_id=$this->session->userdata(SES_TRANS_ID);
		$creater_id=$this->session->userdata(SES_CREATOR_ID);
		
		$data=array();
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email|callback_user_unique_email',
				'errors'=>array(
					'user_unique_email'=>'El correo %s ya esta en uso.'
				)
			),
			array(
				'field'=>'phone_no',
				'label'=>'Phone No.',
				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',
				'errors'=>array(
					'valid_phone_no'=>'El campo %s debe contener un número telefónico válido.',
					'user_unique_phone_no'=>'El número %s ya esta en uso.'
				)
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'dni_no',
				'label'=>'DNI No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			
			$verification_code = rand(9999,1000000);
			$password = $this->input->post('password');
			$email_user = $this->input->post('email');
			$phone_number = $this->input->post('phone_no');
			$firstname = $this->input->post('first_name');
			$lastname = $this->input->post('last_name');
			$save_data=array(
				'parent_user_id'=>$creater_id,
				'creater_id'=>$creater_id,
				'user_type'=>'1',
				'is_company'=>'1',
				'super_parent_id'=>$this->transporter_id,
				'verification_code'=>$verification_code,
				'is_phone_no_verify'=>'1',
				'is_user_verify'=>'1',
				'dni_no'=>$this->input->post('dni_no'),
				'phone_no'=>$phone_number,
				'email'=>$email_user,
				'last_name'=>$lastname,
				'first_name'=>$firstname,
				'password'=>md5($password),
				'showpass'=>$password,
				'is_user_verify'=>'1',
				'is_phone_no_verify'=>'1',
				'is_email_verify'=>'1',
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			// image section 
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image = $_FILES['image'];
				$image_name = $this->uploadimage($image,'users');
				if(!empty($image_name)){
					$save_data['image']=$image_name;
				}
			}
			//REGISTRO A FIREBASE ADD CODE

			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL,"http://3.137.91.53/transporter/api/post-firebase");
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "user_email=$email_user&phone_no=$phone_number&password=$password&first_name=$firstname&last_name=$lastname");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$uid = curl_exec($ch);
			curl_close ($ch);
			$error='Error';
			//sleep(10);
			try{
				$driver_id = $this->BaseModel->insertData($this->tableNameUser,$save_data);
				if($driver_id>0){
					
					$this->session->set_flashdata('success','Los datos del sub administrador se han registrado satisfactoriamente');
					redirect(BASE_FOLDER_TRANS.'subadmins');
				}else{
					$error = 'No se pudo guardar los detalles del sub administrador.';
   					throw new Exception($error);
				}
			}catch(Exception $e){	
				$ch = curl_init(); 		
				curl_setopt($ch, CURLOPT_URL,"http://3.137.91.53/transporter/api/delete-firebase");
				curl_setopt($ch, CURLOPT_POST, TRUE);			
				curl_setopt($ch, CURLOPT_POSTFIELDS, "uid=$uid&email=$email_user");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$out_ = curl_exec($ch);
				curl_close ($ch);
				// ELIMINAR DE FIREBASE ADD CODE
				$this->session->set_flashdata('error','No se pudo guardar los detalles del sub administrador.');
			}
		

		}
		$this->loadviewtrans('subadmin_add',$data);
	}
	
	public function editsubadmin($subadmin_id=0){
		$this->trans_session_off();
		$this->transporter_id=$this->session->userdata(SES_TRANS_ID);
		$creater_id=$this->session->userdata(SES_CREATOR_ID);
		
		$data=array();
		if($subadmin_id>0){
			$find_driver=array(
				'user_id'=>$subadmin_id,
				'super_parent_id'=>$this->transporter_id,
				'user_type'=>'1',
				'is_company'=>'1'
			);
			$subadmin = $this->BaseModel->getData($this->tableNameUser,$find_driver);
			if(empty($subadmin)){
				$this->session->set_flashdata('error','No se encontraron los detalles del sub administrador.');
				redirect(BASE_FOLDER_TRANS.'subadmins');
			}
			$data['subadmin']=$subadmin;
		}
		else{
			$this->session->set_flsahdata('error','No se encontró la información del sub administrador.');
			redirect(BASE_FOLDER_TRANS.'subadmins');
		}
		
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email|callback_user_unique_email['.$subadmin_id.']',
				'errors'=>array(
					'user_unique_email'=>'El correo %s ya esta en uso.'
				)
			),
			array(
				'field'=>'phone_no',
				'label'=>'Phone No.',
				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no['.$subadmin_id.']',
				'errors'=>array(
					'valid_phone_no'=>'El campo %s debe contener un número telefónico válido.',
					'user_unique_phone_no'=>'El número %s ya esta en uso.'
				)
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'dni_no',
				'label'=>'DNI No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$old_image = $subadmin['image'];
			$password = $this->input->post('password');
			$save_data=array(
				'dni_no'=>$this->input->post('dni_no'),
				'phone_no'=>$this->input->post('phone_no'),
				'email'=>$this->input->post('email'),
				'last_name'=>$this->input->post('last_name'),
				'first_name'=>$this->input->post('first_name'),
				'password'=>md5($password),
				'showpass'=>$password,
				'update_date'=>$this->dateformat,
			);
			// image section 
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image = $_FILES['image'];
				$image_name = $this->uploadimage($image,'users');
				if(!empty($image_name)){
					$save_data['image']=$image_name;
					//remove old image 
					$this->removeimage($old_image,'users');
				}
			}
			$this->BaseModel->updateDatas($this->tableNameUser,$save_data,$find_driver);
			$this->session->set_flashdata('success','Los detalles del sub administrador han sido actualizados.');
			redirect(BASE_FOLDER_TRANS.'subadmins');
		}
		
		$this->loadviewtrans('subadmin_edit',$data);
	}


	



	
}
?>