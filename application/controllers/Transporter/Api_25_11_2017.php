<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
class Api extends MY_Controller{
	private $response_status;
	private $response_message;
	private $logged_user;
	private $logged_user_id;
	private $dflt_country_id;
	private $limit;
	
	function __construct(){
		parent::__construct();
		$this->response_status=0;
		$this->request_checked();
		// function named 
		$func_name = $this->uri->segment(2);
		$this->response_message = ucwords(str_replace("_"," ",$func_name))." service response";
		$this->logged_user=array();
		$this->logged_user_id=0;
		$this->dflt_country_id=1; // for US
		$this->limit='30';
	}
	
	private function request_checked(){
		if($this->input->server('REQUEST_METHOD')!=strtoupper('post')){
			$this->response_status=-1;
			$this->json_output();
		}
		$this->write_log();
		$this->minimum_param_checked();
	}
	public function write_log(){
		// write to txt log
		$log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
		//"User Server details : ".json_encode($_SERVER).PHP_EOL.
		"Attempt function name : ".$this->uri->segment(2).PHP_EOL.
		"Posted data : ".json_encode($this->input->post()).PHP_EOL.
		"-------------------------\n".PHP_EOL;
		if(isset($_FILES)){
			$log.="FIle Posted data : ".json_encode($_FILES).PHP_EOL.
			"-------------------------\n".PHP_EOL;
		}
		//Save string to log, use FILE_APPEND to append.
		file_put_contents('./logs/log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
	}
	
	private function minimum_param_checked($is_log_user=0){
		$this->logged_user_id = $this->input->post('user_id');
		$user_has_key = $this->input->post('user_request_key');
		$device_type = $this->input->post('device_type');
		$device_unique_code = $this->input->post('device_unique_code');
		$device_push_key = $this->input->post('device_push_id');
		$this->language_id = $this->input->post('language_id');
		if(empty($this->language_id)){
			$this->language_id=1; // default id
		}
		switch($is_log_user){
			case 0:
				// minimum checking 
				if(!in_array($device_type,array('1','2'))){
					$this->response_status=0;
					$this->response_message="Device type missing";
					$this->json_output();
				}
				if(empty($device_unique_code)){
					$this->response_status=0;
					$this->response_message="Device key missing";
					$this->json_output();
				}
				break;
			case 1:
				// valid user checked section
				if(empty($this->logged_user_id)){
					$this->response_status=0;
					$this->response_message="User details missing";
					$this->json_output();
				}
				if(empty($user_has_key)){
					$this->response_status=0;
					$this->response_message="Token key missing";
					$this->json_output();
				}
				// validate user key is valied or not 
				$find_has=array(
					'user_id'=>$this->logged_user_id,
					'request_key'=>$user_has_key,
					'device_type'=>$device_type,
					'device_unique_id'=>$device_unique_code,
					'is_deleted'=>'0'
				);
				$joins=array(
					array(
						'table_name'=>$this->tableNameUser,
						'join_with'=>$this->tableNameUserRequestKey,
						'join_type'=>'inner',
						'join_on'=>array('user_id'=>'user_id'),
						'conditions'=>array(
							'is_deleted'=>'0'
						),
						'select_fields'=>array('first_name','last_name','user_type','image','phone_no','is_phone_no_verify','is_company','user_status')
					)
				);
				
				$fld_arr=array('user_id');
				$userdata = $this->BaseModel->getData($this->tableNameUserRequestKey,$find_has,$fld_arr,array(),$joins);
				if(empty($userdata)){
					$this->response_message="Authentication faild";
					$this->response_status=-3;
					$this->json_output();
				}
				else{
					$this->logged_user = $userdata;
					//$this->pr($userdata);
				}
				break;
			default:
				$this->response_status=-2;
				$this->response_message="Invalid request param set";
				$this->json_output();
				break;
		}
	}
	
	private function json_output($response_data=array()){
		switch($this->response_status){
			case -1:
				$this->response_message="Your request not accepted";
				$this->response_status=0;
				break;
			case -2:
				$this->response_message="Invalid request param set";
				$this->response_status=0;
				break;
			case -3:
				$this->response_message="Authentication Error";
				$this->response_status=0;
				break;
			default:
				break;
		}
		$response = array(
			'status'=>$this->response_status,
			'message'=>$this->response_message,
		);
		if(is_array($response_data)){
			//$response = array_merge($response,$response_data);
		}
		$response = array_merge($response,$response_data);
		die(json_encode($response));
	}
	
	public function user_request_key($user_id=0,$is_new=0){
		$has_key = md5(time().$user_id);
		
		$device_type = $this->input->post('device_type');
		$device_unique_code = $this->input->post('device_unique_code');
		
		if(!$is_new){
			// old user update has key
			$find_data=array(
				'device_type'=>$device_type,
				'device_unique_code'=>$device_unique_code,
			);
			
			$find_data['user_id']=$user_id;
			$find_data['is_deleted']='0';
			// remove old instance 
			$this->BaseModel->removeDatas($this->tableNameUserHaxKey,$find_data);
		}
		
		// save the new data 
		$save_data=array(
			'user_id'=>$user_id,
			'haxkey'=>$has_key,
			'device_push_key'=>$device_unique_code, // trying todo with 
			'device_type'=>$device_type,
			'device_unique_code'=>$device_unique_code,
			'create_date'=>$this->dateformat,
			'update_date'=>$this->dateformat,
		);
		
		$this->BaseModel->insertData($this->tableNameUserHaxKey,$save_data);
		return $has_key;
	}
	
	protected function userdetails($user_id=0){
		if(empty($user_id)){
			return array();
		}
		// get the users basic details 
		$find_cond=array(
			'user_id'=>$user_id,
			'is_blocked'=>'0',
			//'is_deleted'=>array('1','0')
		);
		$select_flds=array('user_id','first_name','last_name','email','phone_no','user_type','image','dni_no','is_company','company_name','company_licence_no','ruc_no','is_user_verify','verification_code','about_us','address');
		//,'latitude','longitude'
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameIndustryType,
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('industrytype_id'=>'industrytype_id'),
				'select_fields'=>array('industrytype_name')
			),
			array(
				'table_name'=>$this->tableNameUserRating,
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('user_id'=>'receiver_user_id'),
				'select_fields'=>'IFNULL(TRUNCATE(AVG(rating),2),0) rating',
				'oncond'=>array('is_deleted'=>'0')
			)
		);
		
		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond,$select_flds,$order_by,$joins);
		if(!empty($user) && $user['user_id']==$user_id){
			$user_type= $user['user_type'];
			$is_company= $user['is_company'];
			
			if($user_type==1){ //transsporter or driver
			}
			else{
			}
			// user image link change 
			if(!empty($user['image'])){
				$user['image'] = base_url('uploads/users/'.$user['image']);
			}
			// user ratings most recent 3 
			$find_rattings=array(
				'receiver_user_id'=>$user_id,
				'is_blocked'=>'0'
			);
			$extra=array(
				'limit'=>'3',
				'offset'=>'0',
				'is_count'=>'0',
				'order_by'=>array('rating_id'=>'DESC'),
			);
			$ratings = $this->getratings($find_rattings,$extra);
			// adding into the user object
			$user['ratings']=$ratings;
			// user request summery section
			$find_req_summery=array(
				'user_id'=>$user_id,
				'user_type'=>$user_type,
				'is_company'=>$is_company
			);
			$request_summery = $this->getrequest_summery($find_req_summery);
			if(is_array($request_summery)){
				$user = array_merge($user,$request_summery);
			}
		}
		else{
			$user=array();
		}
		return $user;
	}
	
	// api main service function bellow here 
	public function basicdata(){
		$response_data=array();
		// get industry types list 
		$find_industrytype=array(
			'is_blocked'=>'0'
		);
		$industrytypes = $this->getindustrytypes($find_industrytype);
		$find_trailer=array(
			'is_blocked'=>'0',
			'is_default'=>'0'
		);
		$trailers = $this->gettrailers($find_trailer);
		// find another trailer 
		$find_another_trailer=array(
			'is_blocked'=>'0',
			'is_default'=>'1'
		);
		$another_trailer = $this->gettrailers($find_another_trailer);
		if(!empty($another_trailer)){
			$another_trailer = $another_trailer[0];
		}
		// user doc type
		$find_user_doc_type=array(
			'is_blocked'=>'0',
			'document_for'=>'1'
		);
		$user_document_types = $this->getdocumenttypes($find_user_doc_type);
		$find_vehicle_doc_type=array(
			'is_blocked'=>'0',
			'document_for'=>'2'
		);
		$vehicle_document_types = $this->getdocumenttypes($find_vehicle_doc_type);
		// load types 
		$find_loadtype=array(
			'is_blocked'=>'0'
		);
		$loadtypes = $this->getloadtypes($find_loadtype);
		
		$response_data['industrytypes']=$industrytypes;
		$response_data['trailers']=$trailers;
		$response_data['user_document_types']=$user_document_types;
		$response_data['vehicle_document_types']=$vehicle_document_types;
		$response_data['loadtypes']=$loadtypes;
		$response_data['another_trailer']=$another_trailer;
		
		$response_data['termscondition']= base_url('users/termsconditions'); //"https://google.com";
		$response_data['suport_email']="support@bauenfreight.com";
		$response_data['suport_phone']="7777777777";
		$response_data['term_of_service']=base_url('users/termsconditions'); //"http://google.com";
		// base url 
		$site_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'];
		$response_data['site_url']=$site_url;
		
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function industrytype(){
		$response_data=array();
		$find_industrytype=array(
			'is_blocked'=>'0'
		);
		$industrytypes = $this->getindustrytypes($find_industrytype);
		
		$this->response_status=1;
		$response_data['industrytypes']=$industrytypes;
		$this->json_output($response_data);
	}
	
	public function trailers(){
		$response_data=array();
		$find_trailer=array(
			'is_blocked'=>'0',
			'is_default'=>'0'
		);
		$trailers = $this->gettrailers($find_trailer);
		$this->response_status=1;
		$response_data['trailers']=$trailers;
		$this->json_output($response_data);
	}
	
	public function user_document_types(){
		$response_data=array();
		$find_user_doc_type=array(
			'is_blocked'=>'0',
			'document_for'=>'1'
		);
		$document_types = $this->getdocumenttypes($find_user_doc_type);
		$response_data['user_document_types']=$document_types;
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function vehicle_document_types(){
		$response_data=array();
		$find_vehicle_doc_type=array(
			'is_blocked'=>'0',
			'document_for'=>'2'
		);
		$vehicle_document_types = $this->getdocumenttypes($find_vehicle_doc_type);
		$response_data['vehicle_document_types']=$vehicle_document_types;
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function registration(){
		$response_data = array();
		$this->load->library(array('form_validation'));
		$this->load->helper(array('array'));
		
		$is_company = $this->input->post('is_company');
		// rule form validation 
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
					'user_unique_email'=>'This %s value already exists.'
				)
			),
			array(
				'field'=>'phone_no',
				'label'=>'Phone No.',
				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',
				'errors'=>array(
					'user_unique_phone_no'=>'This %s value already exists.',
					'valid_phone_no'=>'Please enter valid %s.',
				)
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			/*array(
				'field'=>'dni_no',
				'label'=>'DNI No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),*/
		);
		// if the user registration as company then 
		if($is_company){
			$com_rules=array(
				array(
					'field'=>'company_name',
					'label'=>'Company Name',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'industrytype_id',
					'label'=>'Industry Type',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				/*array(
					'field'=>'ruc_no',
					'label'=>'RUC No.',
					'rules'=>'trim|required',
					'errors'=>array()
				),*/
			);
			// merge the array 
			$rules = array_merge($rules,$com_rules);
		}
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('','');
		if($this->form_validation->run()===true){
			$first_name = $this->input->post('first_name');
			$last_name = $this->input->post('last_name');
			$email = $this->input->post('email');
			$phone_no = $this->input->post('phone_no');
			$password = $this->input->post('password');
			$dni_no = $this->input->post('dni_no');
			$user_type = $this->input->post('user_type');
			$ruc_no = $this->input->post('ruc_no');
			$company_name = $this->input->post('company_name');
			$company_licence_no = $this->input->post('company_licence_no');
			$industrytype_id = $this->input->post('industrytype_id');
			$country_code = $this->input->post('country_code');
			
			$about_us = $this->input->post('about_us');
			$address = $this->input->post('address');
			$latitude = $this->input->post('latitude');
			$longitude = $this->input->post('longitude');
			//
			if(empty($last_name)){
				$last_name='';
			}
			if(empty($company_name)){
				$company_name='';
			}
			if(empty($company_licence_no)){
				$company_licence_no='';
			}
			if(empty($country_code)){
				$country_code='';
			}
			
			if(empty($about_us)){
				$about_us='';
			}
			
			if(empty($address)){
				$address='';
			}
			if(empty($latitude)){
				$latitude='';
			}
			if(empty($longitude)){
				$longitude='';
			}
			
			// generate the verification code 
			$verify_code = $this->verify_code(); // must send on mobile for mobile number validation
			// email verification token send 
			$email_verify_token = $this->email_verify_token();
			
			// save the user 
			$save_data=array(
				'first_name'=>$first_name,
				'last_name'=>$last_name,
				'email'=>$email,
				'phone_no'=>$phone_no,
				'user_type'=>$user_type,
				'password'=>md5($password),
				'showpass'=>$password,
				'dni_no'=>$dni_no,
				'is_company'=>$is_company,
				'company_name'=>$company_name,
				'company_licence_no'=>$company_licence_no,
				'ruc_no'=>$ruc_no,
				'industrytype_id'=>$industrytype_id,
				'country_code'=>$country_code,
				'verification_code'=>$verify_code,
				'email_verify_token'=>$email_verify_token,
				'about_us'=>$about_us,
				'address'=>$address,
				'latitude'=>$latitude,
				'longitude'=>$longitude,
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			$response_data['user']=$save_data;
			$user_id = $this->BaseModel->insertData($this->tableNameUser,$save_data);
			if($user_id>0){
				// create request token or this user 
				$request_key = $this->generate_request_key($user_id,$is_new=1);
				// user info saved successfully
				// send sms with verification code 
				$this->sendsms($phone_no,$verify_code);
				// send the email verify link 
				$this->send_email_verify_link($email,$email_verify_token,$verify_code);
				// get user details
				$response_data=$this->userdetails($user_id);
				$response_data['user_request_key']=$request_key;
				$this->response_status=1;
				$this->response_message="A verification code send to your email address";
				// create user count table 
				$save_count_data=array(
					'user_id'=>$user_id,
					'create_date'=>$this->dateformat,
					'update_date'=>$this->dateformat,
				);
				$this->BaseModel->insertData($this->tableNameUserCount,$save_count_data);
			}
			else{
				$this->response_message="We get some error in processing the data";
			}
		}
		else{
			$error = validation_errors();
			$this->response_message=$error;
		}
		$this->json_output($response_data);
	}
	
	private function verify_code(){
		$verify_code = rand(99999,1000000);
		return $verify_code;
	}
	
	private function email_verify_token(){
		$email_verify_token = time().rand(999,1000000);
		return md5($email_verify_token);
	}
	
	private function send_email_verify_link($email='',$email_verify_token='',$verification_code=''){
		if(filter_var($email,FILTER_VALIDATE_EMAIL)){
			// send email
			$email_link='';
			if(!empty($email_verify_token)){
				$email_link = base_url('users/emailvalidate/'.$email_verify_token);
			}
			
			$email_data=array(
				'email_link'=>$email_link,
				'verification_code'=>$verification_code
			);
			// send mail section
			$this->sendemail(5,$email,$email_data);
		}
	}
	
	private function sendsms($phone_no='',$verify_code=''){
		if(!empty($phone_no) && !empty($verify_code)){
			// sms send api integration pending
		}
	}
	
	public function generate_request_key($user_id=0,$is_new=0){
		$has_key = md5(time().$user_id);
		$device_type = $this->input->post('device_type');
		$device_unique_id = $this->input->post('device_unique_code');
		$device_push_key = $this->input->post('device_push_id');
		
		if(!$is_new){
			// old user update has key
			$find_data=array(
				'device_type'=>$device_type,
				'device_unique_id'=>$device_unique_id,
			);
			//$find_data['user_id']=$user_id;
			$find_data['is_deleted']='0';
			// remove old instance 
			$this->BaseModel->removeDatas($this->tableNameUserRequestKey,$find_data);
		}
		
		// save the new data 
		$save_data=array(
			'user_id'=>$user_id,
			'request_key'=>$has_key,
			'device_push_key'=>$device_push_key, // trying todo with 
			'device_type'=>$device_type,
			'device_unique_id'=>$device_unique_id,
			'create_date'=>$this->dateformat,
			'update_date'=>$this->dateformat,
		);
		
		$this->BaseModel->insertData($this->tableNameUserRequestKey,$save_data);
		return $has_key;
	}
	
	// verification code validation 
	public function verify_code_validate(){
		$response_data=array();
		$verify_code = $this->input->post('verify_code');
		$this->minimum_param_checked(1);
		if(empty($verify_code)){
			$this->response_message="Enter your verification code";
			$this->json_output($response_data);
		}
		$find_cond = array(
			'user_id'=>$this->logged_user_id,
			'verification_code'=>$verify_code,
			'is_blocked'=>'0'
		);
		
		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);
		if(!empty($user)){
			// now 
			if(!$user['is_user_verify']){
				$update_data=array(
					'is_phone_no_verify'=>'1',
					'is_user_verify'=>'1',
					'update_date'=>$this->dateformat
				);
				$this->BaseModel->updateDatas($this->tableNameUser,$update_data,$find_cond);
			}
			else{
				if(!$user['is_phone_no_verify']){
					$update_data=array(
						'is_phone_no_verify'=>'1',
						'update_date'=>$this->dateformat
					);
					$this->BaseModel->updateDatas($this->tableNameUser,$update_data,$find_cond);
				}
			}
			$this->response_status=1;
			$this->response_message="Your account verified successfully";
			$response_data = $this->userdetails($this->logged_user_id);
		}
		else{
			$this->response_message="Verification code is invalid";
		}
		$this->json_output($response_data);
	}
	
	public function resend_verify_code(){
		$response_data=array();
		$phone_no = $this->input->post('phone_no');
		$user_id = $this->input->post('user_id');
		
		if($user_id>0){
			$find_cond=array(
				'is_blocked'=>'0',
				'user_id'=>$user_id
			);
		}
		else{
			if(empty($phone_no)){
				$this->response_message="Enter your phone no.";
				$this->json_output($response_data);
			}
			else{
				// validate format 
				if(!$this->valid_phone_no($phone_no)){
					$this->response_message="Enter valid phone no.";
					$this->json_output($response_data);
				}
				else{
					$find_cond=array(
						'is_blocked'=>'0',
						'phone_no'=>$phone_no
					);
				}
			}
		}
		
		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);
		if(!empty($user)){
			if($user['is_user_verify']){
				$this->response_message="This account already validated.";
				$this->json_output($response_data);
			}
			elseif($user['is_phone_no_verify']){
				$this->response_message="This phone no. already validated";
				$this->json_output($response_data);
			}
			else{
				if(empty($user_id)){
					$find_cond['user_id']=$user['user_id'];
				}
				
				if(empty($phone_no)){
					$phone_no = $user['phone_no'];
				}
				
				$varify_code = $this->verify_code();
				$update_data=array(
					'verification_code'=>$varify_code,
					'update_date'=>$this->dateformat,
				);
				$this->BaseModel->updateDatas($this->tableNameUser,$update_data,$find_cond);
				// send sms 
				$this->sendsms($phone_no,$varify_code);
				$email = $user['email'];
				$email_link='';
				$this->send_email_verify_link($email,$email_link,$varify_code);
				$this->response_message="A verification code sent to your email address.";
				$this->response_status=1;
			}
		}
		else{
			$this->response_message="No record found";
		}
		$this->json_output($response_data);
	}
	
	
	public function forgotpassword(){
		$email = $this->input->post('email');
		if(empty($email)){
			$this->response_message="Please provide your registered email address";
			$this->json_output();
		}
		if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
			$this->response_message="Please provide email address in valid format";
			$this->json_output();
		}
		// find section 
		$find_cond=array(
			'email'=>$email,
			'is_blocked'=>'0'
		);
		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);
		if(empty($user)){
			$this->response_message="This email does not registered with us";
			$this->json_output();
		}
		// now create the password reset link or send the new password 
		$phrash = time();
		$change_pass_token = md5($phrash);
		$changelink = base_url('users/resetpassword/'.$change_pass_token);
		// update the users 
		$update_data=array(
			'change_pass_token'=>$change_pass_token,
			'update_date'=>$this->dateformat
		);
		$update_cond=array(
			'user_id'=>$user['user_id']
		);
		$this->BaseModel->updateDatas($this->tableNameUser,$update_data,$update_cond);
		$this->response_message="Password reset link send to your email address";
		$this->response_status=1;
		//send email section 
		$email_data=array(
			'changelink'=>$changelink
		);
		$this->sendemail(2,$email,$email_data);
		$this->json_output();
	}
	
	public function login(){
		$response_data=array();
		$email = $this->input->post('email');
		//$password = $this->input->post('password');
		$user_type = $this->input->post('user_type');
		$is_company = $this->input->post('is_company');
		// validation 
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email',
				'errors'=>array()
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
		);
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('','');
		if($this->form_validation->run()==true){
			$find_cond=array(
				'email'=>$email,
				'password'=>md5($this->input->post('password')),
				'is_blocked'=>'0',
			);
			
			$select_fields=array('user_id','user_type');
			$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);
			if(!empty($user)){
				$user_id = $user['user_id'];
				// validate the user type 
				/*if($user_type==1){//transporter section
					if($is_company!=$user['is_company']){
						$this->response_message="Invalid user details";
						$this->json_output($response_data);
					}
				}*/
				
				if($user_type!=$user['user_type']){
					$this->response_message="Invalid user details";
					$this->json_output($response_data);
				}
				else{
					if($user_type==1){//transporter section
						if($is_company!=$user['is_company']){
							$this->response_message="Invalid user details";
							$this->json_output($response_data);
						}
					}
				}
				// update the hax key 
				$hax_key = $this->generate_request_key($user_id,$is_new=0);
				$response_data=$this->userdetails($user_id);
				$response_data['user_request_key']=$hax_key;
				$this->response_status='1';
			}
			else{
				$this->response_message="Email or password does not matched.";
			}
		}
		else{
			$erros = validation_errors();
			$this->response_message=$erros;
		}
		$this->json_output($response_data);
	}
	
	public function user_profile(){
		$response_data=array();
		$user_id = $this->input->post('user_id');
		$other_user_id = $this->input->post('other_user_id');
		$hax_key = $this->input->post('request_key');
		$this->minimum_param_checked(1);
		if($other_user_id>0){
			$response_data=$this->userdetails($other_user_id);
		}
		else{
			$response_data=$this->userdetails($user_id);
			// update the request key 
			$hax_key = $this->generate_request_key($user_id,$is_new=0);
			$response_data['user_request_key']=$hax_key;
		}
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	
	public function edit_profile(){
		$response_data=array();
		$user_id = $this->input->post('user_id');
		$this->minimum_param_checked(1);
		$this->load->library(array('form_validation'));
		$image=array();
		$old_image = $this->logged_user['image'];
		$old_phone_no = $this->logged_user['phone_no'];
		$user_type = $this->logged_user['user_type'];
		$is_company = $this->logged_user['is_company'];
		
		// image section 
		if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
			$image = $_FILES['image'];
		}
		$rules=array(
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'phone_no',
				'label'=>'Phone',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'address',
				'label'=>'Address',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			
			/*array(
				'field'=>'dni_no',
				'label'=>'DNI no.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'ruc_no',
				'label'=>'RUC no.',
				'rules'=>'trim|required',
				'errors'=>array()
			),*/
		);
		if($is_company){
			if($user_type==0){
				//user
				$rules[]=array(
					'field'=>'dni_no',
					'label'=>'DNI no.',
					'rules'=>'trim|required',
					'errors'=>array()
				);
				$rules[]=array(
					'field'=>'ruc_no',
					'label'=>'RUC no.',
					'rules'=>'trim|required',
					'errors'=>array()
				);
			}
		}
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('','');
		if($this->form_validation->run()===true){
			$phone_no = $this->input->post('phone_no');
			$is_phone_no_verify=1;
			$verification_code='';
			$update_data=array(
				'first_name'=>$this->input->post('first_name'),
				'last_name'=>$this->input->post('last_name'),
				'phone_no'=>$phone_no,
				'about_us'=>$this->input->post('about_us'),
				'address'=>$this->input->post('address'),
				'update_date'=>$this->dateformat,
				//'dni_no'=>$this->input->post('dni_no'),
				//'ruc_no'=>$this->input->post('ruc_no'),
			);
			if($is_company){
				if($user_type==0){
					$update_data['dni_no']=$this->input->post('dni_no');
					$update_data['ruc_no']=$this->input->post('ruc_no');
				}
			}
			
			if($old_phone_no!=$phone_no){
				$verification_code = $this->verify_code();
				$is_phone_no_verify=0;
				$update_data['is_phone_no_verify']=$is_phone_no_verify;
				$update_data['verification_code']=$verification_code;
				$update_data['old_phone_no']=$old_phone_no;
			}
			
			// image section 
			if(!empty($image)){
				$image_name = $this->uploadimage($image,'users');
				if(!empty($image_name)){
					$update_data['image']=$image_name;
					// remove old i
					$this->removeimage($old_image,'users');
				}
				$old_image = $image_name;
			}
			
			$update_cond=array(
				'user_id'=>$user_id
			);
			$this->BaseModel->updateDatas($this->tableNameUser,$update_data,$update_cond);
			//send sms if 
			if(!$is_phone_no_verify){
				if(!empty($verification_code)){
					$this->sendsms($phone_no,$verification_code);
				}
			}
			
			//$response_data['image']=base_url('uploads/users/'.$old_image);
			//$response_data['is_phone_no_verify']=$is_phone_no_verify;
			
			$response_data = $this->userdetails($user_id);
			$response_data['user_request_key']=$this->input->post('user_request_key');
			$this->response_status=1;
			$this->response_message="Profile details updated successfully";
		}
		else{
			$errors = validation_errors();
			$this->response_message=$errors;
		}
		$this->json_output($response_data);
	}
	
	public function user_image_upload(){
		$response_data=array();
		$user_id = $this->input->post('user_id');
		$this->minimum_param_checked(1);
		$image=array();
		$old_image = $this->logged_user['image'];
		
		// image section 
		if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
			$image = $_FILES['image'];
			$image_name = $this->uploadimage($image,'users');
			if(!empty($image_name)){
				$update_data['image']=$image_name;
				$update_data['update_date']=$this->dateformat;
				// remove old i
				$this->removeimage($old_image,'users');
				$update_cond=array(
					'user_id'=>$user_id
				);
				$this->BaseModel->updateDatas($this->tableNameUser,$update_data,$update_cond);
				$this->response_status=1;
				$response_data['image']=base_url('uploads/users/'.$image_name);
			}
			else{
				$this->response_message="Image uploading faild";
			}
		}
		else{
			$this->response_message="Image file is missing";
		}
		$this->json_output($response_data);
	}
	
	public function email_register_checked(){
		$response_data=array();
		$email = $this->input->post('email');
		// validation 
		if(filter_var($email,FILTER_VALIDATE_EMAIL)){
			$find_cond=array(
				'email'=>$email,
				'user_type !='=>'2',
				'is_blocked'=>'0'
			);
			$row = $this->BaseModel->tableRow($this->tableNameUser,$find_cond);
			if($row){
				$this->response_status=1;
				$this->response_message="Email found";
			}
			else{
				$this->response_message="Email details not found";
			}
		}
		else{
			$this->response_message="Invalid email format";
		}
		$this->json_output($response_data);
	}
	
	public function uploaddoc(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->input->post('user_id');
		$request_id = $this->input->post('request_id');
		$file=array();
		if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
			$file = $_FILES['file'];
		}
		if($this->logged_user['user_type']>0){
			$this->response_message="Invalid request";
			$this->json_output($response_data);
		}
		if(empty($request_id)){
			$this->response_message="Request details missing";
			$this->json_output($response_data);
		}
		if(empty($file)){
			$this->response_message="Upload file is missing";
			$this->json_output($response_data);
		}
		//
		$find_req=array(
			'request_id'=>$request_id,
			'is_blocked'=>'0',
			'status'=>'1',
			'worker_id'=>$user_id
		);
		$request = $this->BaseModel->getData($this->tableNameServiceRequest,$find_req);
		if(empty($request)){
			$this->response_message="Request details not found";
			$this->json_output($response_data);
		}
		// upload the file 
		$attachment_type=0; //1= image 
		$file_type  =(isset($file['type']))?$file['type']:'';
		if(!empty($file_type)){
			if(strpos($file_type,'image')!==false){
				$attachment_type=1;
			}
		}
		$file_name = $this->uploadimage($file);
		// save in database 
		$save_data = array(
			'request_id'=>$request_id,
			'user_id'=>$user_id,
			'attachment_type'=>$attachment_type,
			'attachment_name'=>$file_name,
			'create_date'=>$this->dateformat,
			'update_date'=>$this->dateformat,
		);
		$attachment_id = $this->BaseModel->insertData($this->tableNameServiceAttachment,$save_data);
		if($attachment_id>0){
			//update the count of attachment of the request 
			$old_attatchment = $request['attachment_count'];
			$old_attatchment=($old_attatchment+1);
			$update_data=array(
				'attachment_count'=>$old_attatchment,
				'update_date'=>$this->dateformat
			);
			$this->BaseModel->updateDatas($this->tableNameServiceRequest,$update_data,$find_req);
			///
			$this->response_status=1;
			$response_data=array(
				'attachment_id'=>$attachment_id,
				'attachment_type'=>$attachment_type,
				'attachment_name'=>base_url('uploads/'.$file_name)
			);
		}
		else{
			$this->response_message="Documment upload faild";
		}
		$this->json_output($response_data);
	}
	
	// multifile uploader
	public function multydocupload(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->input->post('user_id');
		$request_id = $this->input->post('request_id');
		$file=array();
		if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
			$file = $_FILES['file'];
		}
		if($this->logged_user['user_type']>0){
			$this->response_message="Invalid request";
			$this->json_output($response_data);
		}
		if(empty($request_id)){
			$this->response_message="Request details missing";
			$this->json_output($response_data);
		}
		if(empty($file)){
			$this->response_message="Upload file is missing";
			$this->json_output($response_data);
		}
		//
		$find_req=array(
			'request_id'=>$request_id,
			'is_blocked'=>'0',
			'status'=>'1',
			'worker_id'=>$user_id
		);
		$request = $this->BaseModel->getData($this->tableNameServiceRequest,$find_req);
		if(empty($request)){
			$this->response_message="Request details not found";
			$this->json_output($response_data);
		}
		//multiple file upload section 
		$file_names = isset($file['name'])?$file['name']:array();
		$file_types = isset($file['type'])?$file['type']:array();
		$file_tmp_names = isset($file['tmp_name'])?$file['tmp_name']:array();
		$file_errors = isset($file['error'])?$file['error']:array();
		$file_sizes = isset($file['size'])?$file['size']:array();
		if(!empty($file_names)){
			if(is_array($file_names)){
				foreach($file_names as $key=>$file_name){
					$file_type  =(isset($file_types[$key]))?$file_types[$key]:'';
					$tmp_name  =(isset($file_tmp_names[$key]))?$file_tmp_names[$key]:'';
					$file_error  =(isset($file_errors[$key]))?$file_errors[$key]:'';
					$size  =(isset($file_sizes[$key]))?$file_sizes[$key]:'';
					$filearray = array(
						'name'=>'test_'.$file_name,
						'type'=>$file_type,
						'tmp_name'=>$tmp_name,
						'error'=>$file_error,
						'size'=>$size,
					);
					// section main
					$attachment_type=0; //1= image
					if(!empty($file_type)){
						if(strpos($file_type,'image')!==false){
							$attachment_type=1;
						}
					}
					$file_name = $this->uploadimage($filearray);
					if(!empty($file_name)){
						// save in database 
						$save_data = array(
							'request_id'=>$request_id,
							'user_id'=>$user_id,
							'attachment_type'=>$attachment_type,
							'attachment_name'=>$file_name,
							'create_date'=>$this->dateformat,
							'update_date'=>$this->dateformat,
						);
						$attachment_id = $this->BaseModel->insertData($this->tableNameServiceAttachment,$save_data);
						if($attachment_id>0){
							$response_data[]=array(
								'attachment_id'=>$attachment_id,
								'attachment_type'=>$attachment_type,
								'attachment_name'=>base_url('uploads/'.$file_name)
							);
						}
					}
				}
			}
			else{
				// one image only
				// section main
				$file_type = $file['type'];
				$attachment_type=0; //1= image
				if(!empty($file_type)){
					if(strpos($file_type,'image')!==false){
						$attachment_type=1;
					}
				}
				$file_name = $this->uploadimage($file);
				if(!empty($file_name)){
					// save in database 
					$save_data = array(
						'request_id'=>$request_id,
						'user_id'=>$user_id,
						'attachment_type'=>$attachment_type,
						'attachment_name'=>$file_name,
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$attachment_id = $this->BaseModel->insertData($this->tableNameServiceAttachment,$save_data);
					if($attachment_id>0){
						$response_data[]=array(
							'attachment_id'=>$attachment_id,
							'attachment_type'=>$attachment_type,
							'attachment_name'=>base_url('uploads/'.$file_name)
						);
					}
				}
			}
			
			//update the attachment count 
			if(!empty($response_data)){
				$total_attachment_nw=count($response_data);
				//update the count of attachment of the request 
				$old_attatchment = $request['attachment_count'];
				$old_attatchment=($old_attatchment+$total_attachment_nw);
				$update_data=array(
					'attachment_count'=>$old_attatchment,
					'update_date'=>$this->dateformat
				);
				$this->BaseModel->updateDatas($this->tableNameServiceRequest,$update_data,$find_req);
				$this->response_status=1;
				$response_datas['attachements']=$response_data;
				$response_data=$response_datas;
			}
			else{
				$this->response_message="Documment upload faild";
			}
		}
		// upload the file
		$this->json_output($response_data);
	}
	
	// transporter section 
	public function vehicles(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			// valid for transporter
			$user_id = $this->logged_user_id;
			$search_text = $this->input->post('search_text');
			$limit = $this->limit;
			$page_no = ($this->input->post('page_no')>1)?$this->input->post('page_no'):1;
			$offset = ($page_no-1)*$limit;
			//$response_data['limit']=$limit;
			//$response_data['offset']=$offset;
			$trailer_id = $this->input->post('trailer_id');
			//truck find section 
			$find_vehicle=array(
				'user_id'=>$user_id,
			);
			if($trailer_id>0){
				$find_vehicle['trailer_id']=$trailer_id;
			}
			$extra_data=array(
				'is_count'=>'1',
				'search_text'=>$search_text,
			);
			// count section 
			$total_row = $this->getvehicles($find_vehicle,$extra_data);
			if($total_row>0){
				$extra_data['is_count']=0;
				$extra_data['limit']=$limit;
				$extra_data['offset']=$offset;
				$vehicles = $this->getvehicles($find_vehicle,$extra_data);
			}
			else{
				$vehicles=array();
			}
			$response_data['total_row']=$total_row;
			$response_data['vehicles']=$vehicles;
			$this->response_status=1;
		}
		
		$this->json_output($response_data);
	}
	
	public function drivers(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			// valid for transporter
			$user_id = $this->logged_user_id;
			$search_text = $this->input->post('search_text');
			$limit = $this->limit;
			$page_no = ($this->input->post('page_no')>1)?$this->input->post('page_no'):1;
			$offset = ($page_no-1)*$limit;
			//$response_data['limit']=$limit;
			//$response_data['offset']=$offset;
			//truck find section 
			$find_driver=array(
				'parent_user_id'=>$user_id,
				'user_type'=>'1',
				'is_company'=>'0'
			);
			$extra_data=array(
				'is_count'=>'1',
				'search_text'=>$search_text,
			);
			// count section 
			$total_row = $this->getdrivers($find_driver,$extra_data);
			if($total_row>0){
				$extra_data['is_count']=0;
				$extra_data['limit']=$limit;
				$extra_data['offset']=$offset;
				$drivers = $this->getdrivers($find_driver,$extra_data);
			}
			else{
				$drivers=array();
			}
			$response_data['total_row']=$total_row;
			$response_data['drivers']=$drivers;
			$this->response_status=1;
		}
		$this->json_output($response_data);
	}
	
	public function add_vehicle(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'trailer_id',
					'label'=>'Trailer',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'plate_no',
					'label'=>'Plate No.',
					'rules'=>'trim|required|callback_unique_plate_no',
					'errors'=>array(
						'unique_plate_no'=>'This %s is already exists.'
					)
				),
				array(
					'field'=>'purchase_year',
					'label'=>'Purchase Year',
					'rules'=>'trim|required|numeric',
					'errors'=>array()
				),
				array(
					'field'=>'vehicle_minload',
					'label'=>'Minimum Load',
					'rules'=>'trim|required|greater_than[-1]',
					'errors'=>array(
						'greater_than'=>'Minimum value is 0'
					)
				),
				array(
					'field'=>'vehicle_maxload',
					'label'=>'Purchase Year',
					'rules'=>'trim|required|greater_than[-1]',
					'errors'=>array(
						'greater_than'=>'Minimum value is 0'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$save_data=array(
					'user_id'=>$user_id,
					'trailer_id'=>$this->input->post('trailer_id'),
					'plate_no'=>$this->input->post('plate_no'),
					'purchase_year'=>$this->input->post('purchase_year'),
					'vehicle_minload'=>$this->input->post('vehicle_minload'),
					'vehicle_maxload'=>$this->input->post('vehicle_maxload'),
					'create_date'=>$this->dateformat,
					'update_date'=>$this->dateformat,
				);
				$vehicle_id = $this->BaseModel->insertData($this->tableNameVehicle,$save_data);
				if($vehicle_id>0){
					// add image section 
					if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
						$image_names = $_FILES['image']['name'];
						$image_tmp_names = $_FILES['image']['tmp_name'];
						if(is_array($image_names) && count($image_names)>0){
							foreach($image_names as $key=>$name){
								$tmp_name = isset($image_tmp_names[$key])?$image_tmp_names[$key]:'';
								if(empty($tmp_name)){
									continue;
								}
								
								$image = array(
									'name'=>$name,
									'tmp_name'=>$tmp_name,
								);
								$file_name = $this->uploadimage($image,'vehicles');
								if(!empty($file_name)){
									$save_data=array(
										'vehicle_id'=>$vehicle_id,
										'image_file'=>$file_name,
										'create_date'=>$this->dateformat,
										'update_date'=>$this->dateformat,
									);
									$vehicle_image_id = $this->BaseModel->insertData($this->tableNameVehicleImage,$save_data);
									if($vehicle_image_id>0){
										/*$response_data=array(
											'vehicle_image_id'=>$vehicle_image_id,
											'image_file'=>base_url('uploads/vehicles/'.$file_name)
										);*/
									}
								}
							}
						}
					}
					
					$this->response_status=1;
					$find_cond=array(
						'vehicle_id'=>$vehicle_id
					);
					$vehicles = $this->getvehicles($find_cond);
					$response_data['vehicle']=$vehicles;
				}
				else{
					$this->response_message="Vehicle saving faild";
				}
			}
			else{
				$errors = validation_errors();
				$this->response_message=$errors;
			}
		}
		$this->json_output($response_data);
	}
	
	public function edit_vehicle(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$this->load->library(array('form_validation'));
			$vehicle_id = $this->input->post('vehicle_id');
			$rules=array(
				array(
					'field'=>'vehicle_id',
					'label'=>'Vehicle',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'trailer_id',
					'label'=>'Trailer',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'plate_no',
					'label'=>'Plate No.',
					'rules'=>'trim|required|callback_unique_plate_no['.$vehicle_id.']',
					'errors'=>array(
						'unique_plate_no'=>'This %s is already exists.'
					)
				),
				array(
					'field'=>'purchase_year',
					'label'=>'Purchase Year',
					'rules'=>'trim|required|numeric',
					'errors'=>array()
				),
				array(
					'field'=>'vehicle_minload',
					'label'=>'Minimum Load',
					'rules'=>'trim|required|greater_than[-1]',
					'errors'=>array(
						'greater_than'=>'Minimum value is 0'
					)
				),
				array(
					'field'=>'vehicle_maxload',
					'label'=>'Purchase Year',
					'rules'=>'trim|required|greater_than[-1]',
					'errors'=>array(
						'greater_than'=>'Minimum value is 0'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				// validate vehicle 
				$find_vehicle=array(
					'vehicle_id'=>$this->input->post('vehicle_id'),
					'user_id'=>$user_id
				);
				$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle);
				if(!empty($vehicle)){
					$save_data=array(
						'trailer_id'=>$this->input->post('trailer_id'),
						'plate_no'=>$this->input->post('plate_no'),
						'purchase_year'=>$this->input->post('purchase_year'),
						'vehicle_minload'=>$this->input->post('vehicle_minload'),
						'vehicle_maxload'=>$this->input->post('vehicle_maxload'),
						'update_date'=>$this->dateformat,
					);
					$this->BaseModel->updateDatas($this->tableNameVehicle,$save_data,$find_vehicle);
					$this->response_status=1;
					$this->response_message="Vehicle details updated successfully.";
				}
				else{
					$this->response_message="Vehicle details not found.";
				}
			}
			else{
				$errors = validation_errors();
				$this->response_message=$errors;
			}
		}
		$this->json_output($response_data);
	}
	
	public function unique_plate_no($plate_no='', $id=0){
		if(!empty($plate_no)){
			$find_cond=array(
				'UPPER(plate_no)'=>strtoupper($plate_no)
			);
			if($id>0){
				$find_cond['vehicle_id !=']=$id;
			}
			$tablerow = $this->BaseModel->tableRow($this->tableNameVehicle,$find_cond);
			if($tablerow){
				return false;
			}
		}
		return true;
	}
	
	public function add_driver(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
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
						'user_unique_email'=>'This %s is already exists.'
					)
				),
				array(
					'field'=>'phone_no',
					'label'=>'Phone No.',
					'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',
					'errors'=>array(
						'valid_phone_no'=>'Enter valid %s',
						'user_unique_phone_no'=>'This %s is already exists.'
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
				array(
					'field'=>'ruc_no',
					'label'=>'RUC No.',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'licence_no',
					'label'=>'Licence No.',
					'rules'=>'trim|required|callback_unique_licence_no',
					'errors'=>array(
						'unique_licence_no'=>'This %s is already exists.'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$verification_code = $this->verify_code();
				$password = $this->input->post('password');
				$save_data=array(
					'parent_user_id'=>$user_id,
					'user_type'=>'1',
					'is_company'=>'0',
					'verification_code'=>$verification_code,
					'is_phone_no_verify'=>'1',
					'is_user_verify'=>'1',
					'ruc_no'=>$this->input->post('ruc_no'),
					'dni_no'=>$this->input->post('dni_no'),
					'licence_no'=>$this->input->post('licence_no'),
					'phone_no'=>$this->input->post('phone_no'),
					'email'=>$this->input->post('email'),
					'last_name'=>$this->input->post('last_name'),
					'first_name'=>$this->input->post('first_name'),
					'password'=>md5($password),
					'showpass'=>$password,
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
				$driver_id = $this->BaseModel->insertData($this->tableNameUser,$save_data);
				if($driver_id>0){
					$this->response_status=1;
					$find_driver=array(
						'user_id'=>$driver_id
					);
					$drivers = $this->getdrivers($find_driver);
					$response_data['drivers']=$drivers;
				}
				else{
					$this->response_message="Driver details saving faild.";
				}
			}
			else{
				$errors = validation_errors();
				$this->response_message=$errors;
			}
		}
		$this->json_output($response_data);
	}
	
	public function edit_driver(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$driver_id = $this->input->post('driver_id');
			$rules=array(
				array(
					'field'=>'driver_id',
					'label'=>'Driver',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'first_name',
					'label'=>'First Name',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				/*array(
					'field'=>'email',
					'label'=>'Email',
					'rules'=>'trim|required|valid_email|callback_user_unique_email['.$driver_id.']',
					'errors'=>array(
						'user_unique_email'=>'This %s is already exists.'
					)
				),*/
				array(
					'field'=>'phone_no',
					'label'=>'Phone No.',
					'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no['.$driver_id.']',
					'errors'=>array(
						'valid_phone_no'=>'Enter valid %s',
						'user_unique_phone_no'=>'This %s is already exists.'
					)
				),
				array(
					'field'=>'dni_no',
					'label'=>'DNI No.',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'ruc_no',
					'label'=>'RUC No.',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'licence_no',
					'label'=>'Licence No.',
					'rules'=>'trim|required|callback_unique_licence_no['.$driver_id.']',
					'errors'=>array(
						'unique_licence_no'=>'This %s is already exists.'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				// validate the driver 
				$find_driver=array(
					'parent_user_id'=>$user_id,
					'user_id'=>$this->input->post('driver_id'),
					'user_type'=>'1',
					'is_company'=>'0'
				);
				$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver);
				if(!empty($driver)){
					$old_image = $driver['image'];
					$save_data=array(
						'ruc_no'=>$this->input->post('ruc_no'),
						'dni_no'=>$this->input->post('dni_no'),
						'licence_no'=>$this->input->post('licence_no'),
						'phone_no'=>$this->input->post('phone_no'),
						//'email'=>$this->input->post('email'),
						'last_name'=>$this->input->post('last_name'),
						'first_name'=>$this->input->post('first_name'),
						'update_date'=>$this->dateformat,
					);
					// image section 
					if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
						$image = $_FILES['image'];
						$image_name = $this->uploadimage($image,'users');
						if(!empty($image_name)){
							$save_data['image']=$image_name;
							//remove the old image 
							$this->removeimage($old_image);
						}
					}
					$this->BaseModel->updateDatas($this->tableNameUser,$save_data,$find_driver);
					$this->response_status=1;
					$this->response_message="Driver details updated successfully";
				}
				else{
					$this->response_message="Driver details not found";
				}
			}
			else{
				$errors = validation_errors();
				$this->response_message=$errors;
			}
		}
		$this->json_output($response_data);
	}
	
	public function unique_licence_no($licence_no='',$id=0){
		if(!empty($licence_no)){
			$find_cond=array(
				'UPPER(licence_no)'=>strtoupper($licence_no)
			);
			if($id>0){
				$find_cond['user_id !=']=$id;
			}
			$tablerow = $this->BaseModel->tableRow($this->tableNameUser,$find_cond);
			if($tablerow){
				return false;
			}
		}
		return true;
	}
	
	public function delete_vehicle(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$vehicle_id = $this->input->post('vehicle_id');
			if(empty($vehicle_id)){
				$this->response_message="The Vehicle field is required.";
				$this->json_output($response_data);
			}
			$find_vehicle = array(
				'user_id'=>$user_id,
				'vehicle_id'=>$vehicle_id
			);
			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle);
			if(empty($vehicle)){
				$this->response_message="Invalid Vehicle information";
				$this->json_output($response_data);
			}
			// now delete the vehicle 
			$this->BaseModel->removeDatas($this->tableNameVehicle,$find_vehicle);
			$this->response_status='1';
			$this->response_message="Vehicle removed successfully.";
		}
		$this->json_output($response_data);
	}
	public function delete_driver(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$driver_id = $this->input->post('driver_id');
			if(empty($driver_id)){
				$this->response_message="The Driver field is required.";
				$this->json_output($response_data);
			}
			$find_driver = array(
				'parent_user_id'=>$user_id,
				'user_id'=>$driver_id,
				'user_type'=>'1',
				'is_company'=>'0'
			);
			$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver);
			if(empty($driver)){
				$this->response_message="Invalid Driver information.";
				$this->json_output($response_data);
			}
			// delete the driver 
			$this->BaseModel->removeDatas($this->tableNameUser,$find_driver);
			$this->response_message="Driver Details deleted successfully.";
			$this->response_status='1';
		}
		$this->json_output($response_data);
	}
	
	public function add_vehicle_image(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$vehicle_id = $this->input->post('vehicle_id');
			$image = array();
			if(empty($vehicle_id)){
				$this->response_message="The Vehicle field is required.";
				$this->json_output($response_data);
			}
			
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image = $_FILES['image'];
			}
			
			if(!empty($image) && is_array($image)){
				//validate the vehicle 
				$find_vehicle=array(
					'user_id'=>$user_id,
					'vehicle_id'=>$vehicle_id,
				);
				$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle);
				if(empty($vehicle)){
					$this->response_message="Vehicle details not found";
				}
				else{
					$file_name = $this->uploadimage($image,'vehicles');
					if(!empty($file_name)){
						$save_data=array(
							'vehicle_id'=>$vehicle_id,
							'image_file'=>$file_name,
							'create_date'=>$this->dateformat,
							'update_date'=>$this->dateformat,
						);
						$vehicle_image_id = $this->BaseModel->insertData($this->tableNameVehicleImage,$save_data);
						if($vehicle_image_id>0){
							$this->response_message="Image uploading successfully.";
							$this->response_status=1;
							$response_data=array(
								'vehicle_image_id'=>$vehicle_image_id,
								'image_file'=>base_url('uploads/vehicles/'.$file_name)
							);
						}
						else{
							$this->response_message="Image saving faild.";
						}
					}
					else{
						$this->response_message="Image uploading faild.";
					}
				}
			}
			else{
				$this->response_message="Image file required.";
			}
		}
		$this->json_output($response_data);
	}
	
	public function vehicle_documents(){
		$response_data=array();
		$this->minimum_param_checked(1);
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$vehicle_id = $this->input->post('vehicle_id');
			$documenttype_id = $this->input->post('documenttype_id');
			$page_no = $this->input->post('page_no');
			$page_no = ($page_no>1)?$page_no:1;
			$limit = $this->limit;
			$offset = ($page_no-1)*$limit;
			$find_document=array(
				'user_id'=>$user_id
			);
			if($vehicle_id>0){
				$find_document['vehicle_id']=$vehicle_id;
			}
			if($vehicle_id>0){
				$find_document['documenttype_id']=$documenttype_id;
			}
			$extra=array(
				'is_count'=>'1'
			);
			$total_row = $this->getvehicledocuments($find_document,$extra);
			$response_data['total_row']=$total_row;
			if($total_row>0){
				$extra=array(
					'limit'=>$limit,
					'offset'=>$offset
				);
				$documents = $this->getvehicledocuments($find_document,$extra);
				$response_data['documents']=$documents;
			}
			else{
				$response_data['documents']=array();
			}
			$this->response_status=1;
		}
		$this->json_output($response_data);
	}
	
	public function upload_vehicle_document(){
		$response_data=array();
		$this->minimum_param_checked(1);
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$vehicle_id = $this->input->post('vehicle_id');
			$documenttype_id = $this->input->post('documenttype_id');
			$image = (isset($_FILES['image']['name']) && !empty($_FILES['image']['name']))?$_FILES['image']:array();
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'vehicle_id',
					'label'=>'Vehicle',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'documenttype_id',
					'label'=>'Document Type',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
			);
			if(empty($image)){
				$rules[]=array(
					'field'=>'file_name',
					'label'=>'Document File',
					'rules'=>'trim|required',
					'errors'=>array()
				);
			}
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				//validate vehicle
				$find_vehicle=array(
					'user_id'=>$user_id,
					'vehicle_id'=>$vehicle_id
				);
				$extra=array();
				$vehicle = $this->getvehicles($find_vehicle);
				if(!empty($vehicle)){
					$file_name = $this->uploadimage($image,'documents');
					if(!empty($file_name)){
						$save_data=array(
							'vehicle_id'=>$vehicle_id,
							'documenttype_id'=>$documenttype_id,
							'user_id'=>$user_id,
							'file_name'=>$file_name,
							'create_date'=>$this->dateformat,
							'update_date'=>$this->dateformat,
						);
						$vehicle_document_id = $this->BaseModel->insertData($this->tableNameVehicleDocument,$save_data);
						if($vehicle_document_id>0){
							$this->response_message="Vehicle Document uploaded successfully";
							$this->response_status=1;
							$find_cond=array(
								'vehicle_document_id'=>$vehicle_document_id,
							);
							$dodument = $this->getvehicledocuments($find_cond);
							if(!empty($dodument)){
								$dodument=$dodument[0];
							}
							$response_data['document']=$dodument;
						}
						else{
							$this->response_message="Vehicle details saving faild.";
						}
					}
					else{
						$this->response_message="Vehicle document uploading faild.";
					}
				}
				else{
					$this->response_message="Vehicle details not found.";
				}
			}
			else{
				$this->response_message=validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function delete_vehicle_document(){
		$response_data=array();
		$this->minimum_param_checked(1);
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$vehicle_id = $this->input->post('vehicle_id');
			$vehicle_document_id = $this->input->post('vehicle_document_id');
			$find_document=array(
				'user_id'=>$user_id,
				'vehicle_id'=>$vehicle_id,
				'vehicle_document_id'=>$vehicle_document_id
			);
			$document = $this->BaseModel->getData($this->tableNameVehicleDocument,$find_document);
			if(!empty($document)){
				$status = $this->BaseModel->removeDatas($this->tableNameVehicleDocument,$find_document);
				if($status){
					$this->response_message="Vehicle Document deleted successfully";
					$this->response_status=1;
				}
				else{
					$this->response_message="Vehicle Document deleting faild.";
				}
			}
			else{
				$this->response_message="Vehicle Document details not found.";
			}
		}
		$this->json_output($response_data);
	}
	
	
	public function placebid_old(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'request_id',
					'label'=>'Request',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'bid_amount',
					'label'=>'Bid Amount',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$request_id = $this->input->post('request_id');
				$bid_amount = $this->input->post('bid_amount');
				$bid_comment = $this->input->post('bid_comment');
				// validate request 
				$find_request=array(
					'request_id'=>$request_id
				);
				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
				if(empty($request)){
					$this->response_message="Request details not found.";
					$this->json_output($response_data);
				}
				$request_status = $request['request_status'];
				if(!in_array($request_status,array('0','1','4'))){
					$this->response_message="You can not able to place a bid.Customer already selected a bid.";
					$this->json_output($response_data);
				}
				if(empty($bid_comment)){
					$bid_comment='';
				}
				$find_bid=array(
					'user_id'=>$user_id,
					'request_id'=>$request_id
				);
				$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_bid);
				if(empty($bid)){
					// create new bid 
					$save_data=array(
						'user_id'=>$user_id,
						'request_id'=>$request_id,
						'bid_amount'=>$bid_amount,
						'bid_comment'=>$bid_comment,
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$bid_id = $this->BaseModel->insertData($this->tableNameRequestBid,$save_data);
					if($bid_id>0){
						$this->response_status=1;
						$this->response_message="Your bid saved successfully";
						//update the status of the bid 
						if($request_status==0){
							$update_data=array(
								'request_status'=>'1',
								'update_date'=>$this->dateformat
							);
							// track the request status 
							$request_status_track = json_decode($request['request_status_track']);
							$request_status_track[]=array(
								'request_status'=>$update_data['request_status'],
								'create_date'=>$update_data['update_date']
							);
							$update_data['request_status_track']=json_encode($request_status_track);
							$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);
						}
						
						// create a notification 
						$notification_data=array(
							'request_id'=>$request_id,
							'user_id'=>$user_id,
							'receiver_user_id'=>$request['user_id'],
							'notification_type'=>'1',
							'amount'=>$bid_amount
						);
						$this->add_notification($notification_data,$is_return=1);
					}
					else{
						$this->response_message="Your bid does not saved";
					}
				}
				else{
					// only bid in status 0
					if($bid['bid_status']==0){
						$save_data=array(
							'bid_amount'=>$bid_amount,
							'bid_comment'=>$bid_comment,
							'update_date'=>$this->dateformat,
						);
						$this->BaseModel->updateDatas($this->tableNameRequestBid,$save_data,$find_bid);
						$this->response_status=1;
						$this->response_message="You bid amount updated successfully";
						// create a notification for update bid amount
						$notification_data=array(
							'request_id'=>$request_id,
							'user_id'=>$user_id,
							'receiver_user_id'=>$request['user_id'],
							'notification_type'=>'17',
							'amount'=>$bid_amount
						);
						$this->add_notification($notification_data,$is_return=1);
					}
					else{
						$this->response_message="You cant able to modify the bid";
					}
				}
			}
			else{
				$this->response_message = validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function placebid(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'request_id',
					'label'=>'Request',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'bid_amount',
					'label'=>'Bid Amount',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$request_id = $this->input->post('request_id');
				$bid_amount = $this->input->post('bid_amount');
				$bid_comment = $this->input->post('bid_comment');
				// validate request 
				$find_request=array(
					'request_id'=>$request_id
				);
				$joins=array(
					array(
						'table_name'=>$this->tableNameRequestBid,
						'join_type'=>'left',
						'join_with'=>$this->tableNameRequest,
						'join_on'=>array('request_id'=>'request_id'),
						'oncond'=>array('user_id'=>$user_id,'is_deleted'=>'0','is_blocked'=>'0'),
						'conditions'=>array(),
						'select_fields'=>array('bid_id','bid_status'),
					)
				);
				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request,array(),array(),$joins);
				if(empty($request)){
					$this->response_message="Request details not found.";
					$this->json_output($response_data);
				}
				$request_status = $request['request_status'];
				if(!in_array($request_status,array('0','1','4'))){
					$this->response_message="You can not able to place a bid.Customer already selected a bid.";
					$this->json_output($response_data);
				}
				if(empty($bid_comment)){
					$bid_comment='';
				}
				
				/*$find_bid=array(
					'user_id'=>$user_id,
					'request_id'=>$request_id
				);
				$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_bid);*/
				
				if(empty($request['bid_id'])){
					// create new bid 
					$save_data=array(
						'user_id'=>$user_id,
						'request_id'=>$request_id,
						'bid_amount'=>$bid_amount,
						'bid_comment'=>$bid_comment,
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$bid_id = $this->BaseModel->insertData($this->tableNameRequestBid,$save_data);
					if($bid_id>0){
						$this->response_status=1;
						$this->response_message="Your bid saved successfully";
						//update the status of the bid 
						if($request_status==0){
							$update_data=array(
								'request_status'=>'1',
								'update_date'=>$this->dateformat
							);
							// track the request status 
							$request_status_track = json_decode($request['request_status_track']);
							$request_status_track[]=array(
								'request_status'=>$update_data['request_status'],
								'create_date'=>$update_data['update_date']
							);
							$update_data['request_status_track']=json_encode($request_status_track);
							$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);
						}
						
						// create a notification 
						$notification_data=array(
							'request_id'=>$request_id,
							'user_id'=>$user_id,
							'receiver_user_id'=>$request['user_id'],
							'notification_type'=>'1',
							'amount'=>$bid_amount
						);
						$this->add_notification($notification_data,$is_return=1);
					}
					else{
						$this->response_message="Your bid does not saved";
					}
				}
				else{
					// only bid in status 0
					if($request['bid_status']==0){
						$find_bid=array(
							'user_id'=>$user_id,
							'request_id'=>$request_id,
							'bid_id'=>$request['bid_id']
						);
						$save_data=array(
							'bid_amount'=>$bid_amount,
							'bid_comment'=>$bid_comment,
							'update_date'=>$this->dateformat,
						);
						$this->BaseModel->updateDatas($this->tableNameRequestBid,$save_data,$find_bid);
						$this->response_status=1;
						$this->response_message="You bid amount updated successfully";
						// create a notification for update bid amount
						$notification_data=array(
							'request_id'=>$request_id,
							'user_id'=>$user_id,
							'receiver_user_id'=>$request['user_id'],
							'notification_type'=>'17',
							'amount'=>$bid_amount
						);
						$this->add_notification($notification_data,$is_return=1);
					}
					else{
						$this->response_message="You cant able to modify the bid";
					}
				}
			}
			else{
				$this->response_message = validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function confirm_cancel_bid(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'request_id',
					'label'=>'Request',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'bid_id',
					'label'=>'Bid',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'bid_status',
					'label'=>'Bid Status',
					'rules'=>'trim|required|greater_than[1]|less_than[4]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.',
						'less_than'=>'The %s field is required.'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$request_id = $this->input->post('request_id');
				$bid_id = $this->input->post('bid_id');
				$bid_status = $this->input->post('bid_status');
				$cancel_comment = $this->input->post('cancel_comment');
				if(empty($cancel_comment)){
					$cancel_comment='';
				}
				
				//validate request 
				$find_request = array(
					'request_id'=>$request_id,
					'bid_id'=>$bid_id,
					'request_status'=>'2', // accept by customer
				);
				
				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
				if(empty($request)){
					$this->response_message="Request details not found.";
					$this->json_output($response_data);
				}
				// track the request status 
				$request_status_track = json_decode($request['request_status_track']);
				
				$find_bid=array(
					'request_id'=>$request_id,
					'bid_id'=>$bid_id,
					'user_id'=>$user_id,
					'bid_status'=>'1', // customer confirm
				);
				$select_fields=array();
				$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_bid,$select_fields);
				if(!empty($bid)){
					$update_data=array(
						'update_date'=>$this->dateformat,
						'bid_status'=>$bid_status,
						'cancel_comment'=>$cancel_comment,
					);
					$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$find_bid);
					if($bid_status==2){
						// tranporti accept the bid
						// bid confirmation on request 
						$trans_bid_amount=$bid['bid_amount'];
						$update_req=array(
							'transporter_id'=>$user_id,
							'request_status'=>'3',// accept by transporter
							'granted_amount'=>$trans_bid_amount,
							'update_date'=>$this->dateformat,
						);
						// lost all other bid of this request 
						$update_bids=array(
							'request_id'=>$request_id,
							'bid_status'=>'0',
							'bid_id !='=>$bid_id
						);
						$update_data=array(
							'bid_status'=>'4', //loast
							'update_date'=>$this->dateformat,
						);
						$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$update_bids);
						$this->response_message="You successfully confired the bid.";
						$notification_type='3';
					}
					else{
						// transporti reject the bid
						$update_req=array(
							'request_status'=>'4',// cancel by transporter
							'update_date'=>$this->dateformat,
						);
						$this->response_message="You successfully cancelled the bid.";
						$notification_type='4';
					}
					
					// update the request acording the bid action 
					$request_status_track[]=array(
						'request_status'=>$update_req['request_status'],
						'create_date'=>$update_req['update_date']
					);
					$update_req['request_status_track']=json_encode($request_status_track);
					
					$this->BaseModel->updateDatas($this->tableNameRequest,$update_req,$find_request);
					$this->response_status=1;
					// create a notification 
					$notification_data=array(
						'request_id'=>$request_id,
						'user_id'=>$user_id,
						'receiver_user_id'=>$request['user_id'],
						'notification_type'=>$notification_type,
					);
					$this->add_notification($notification_data,$is_return=1);
				}
				else{
					$this->response_message="Invalid bid information.";
				}
			}
			else{
				$this->response_message = validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function assingdriver(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'request_id',
					'label'=>'Request',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'driver_id',
					'label'=>'Driver',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'vehicle_id',
					'label'=>'Vehicle',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$request_id = $this->input->post('request_id');
				$driver_id = $this->input->post('driver_id');
				$vehicle_id = $this->input->post('vehicle_id');
				// validate request details
				$find_request=array(
					'request_id'=>$request_id,
					'transporter_id'=>$user_id,
					'request_status'=>'3',
					'bid_id >'=>'0'
				);
				$select_fields=array();
				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request,$select_fields);
				if(!empty($request)){
					if(empty($request['driver_id']) && empty($request['vehicle_id'])){
						// need toassign :: validate driver
						$find_driver=array(
							'parent_user_id'=>$user_id,
							'user_id'=>$driver_id,
							'user_type'=>'1',
							'is_company'=>'0'
						);
						$select_fields=array();
						$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver,$select_fields);
						if(!empty($driver)){
							if(!$driver['is_blocked']){
								// validate driver not in transit mode 
								if($driver['user_status']==3){
									$this->response_message="Selected Driver is in Transit Of another request";
									$this->json_output($response_data);
								}
								// now validate the vehicles
								$find_vehicle=array(
									'user_id'=>$user_id,
									'vehicle_id'=>$vehicle_id,
								);
								$select_fields=array();
								$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle,$select_fields);
								if(!empty($vehicle)){
									if(!$vehicle['is_blocked']){
										// validate vehicle not in transit mode 
										if($vehicle['vehicle_status']==3){
											$this->response_message="Selected Vehicle is in Transit Of another request";
											$this->json_output($response_data);
										}
										
										// now update the request
										$update_data=array(
											'driver_id'=>$driver_id,
											'vehicle_id'=>$vehicle_id,
											'request_status'=>'5', // driver and vehicle assigned
											'update_date'=>$this->dateformat
										);
										// track the request status 
										$request_status_track = json_decode($request['request_status_track']);
										$request_status_track[]=array(
											'request_status'=>$update_data['request_status'],
											'create_date'=>$update_data['update_date']
										);
										$update_data['request_status_track']=json_encode($request_status_track);
										
										$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);
										$this->response_status=1;
										$this->response_message="Driver & Vehicle assigned successfully.";
										// notification section
										// create a notification for customer
										$notification_data=array(
											'request_id'=>$request_id,
											'user_id'=>$user_id,
											'receiver_user_id'=>$request['user_id'],
											'notification_type'=>'5'
										);
										$this->add_notification($notification_data,$is_return=1);
										// create a notification for driver
										$notification_data=array(
											'request_id'=>$request_id,
											'user_id'=>$user_id,
											'receiver_user_id'=>$driver_id,
											'notification_type'=>'6'
										);
										$this->add_notification($notification_data,$is_return=1);
									}
									else{
										$this->response_message="This Vehicle is blocked By Admin";
									}
								}
								else{
									$this->response_message="vehicle details not found";
								}
							}
							else{
								$this->response_message="This Driver is blocked By Admin";
							}
						}
						else{
							$this->response_message="Driver details not found";
						}
					}
					else{
						$this->response_message="Already Driver & Vehicle assign into this request.";
					}
				}
				else{
					$this->response_message="Request information invalid";
				}
			}
			else{
				$this->response_message = validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function my_bids(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$bid_status = $this->input->post('bid_status');
			$page_no = $this->input->post('page_no');
			$page_no = ($page_no>1)?$page_no:1;
			$limit = $this->limit;
			$offset = ($page_no-1)*$limit;
			$trans_bid_cond=array(
				'user_id'=>$user_id
			);
			// request filter section 
			$loadtype_id = $this->input->post('loadtype_id');
			$trailer_id = $this->input->post('trailer_id');
			$request_from = $this->input->post('request_from');
			$request_to = $this->input->post('request_to');
			$request_weight = $this->input->post('request_weight');
			
			// bid status
			$bid_status = ($bid_status>=0)?($bid_status-1):'-1';
			$trans_bid_assos=array(
				'is_blocked'=>'0',
				'is_deleted'=>'0'
			);
			$find_request=array(
				'is_blocked'=>'0'
			);
			// filter ssection 
			// extra filter 
			if($trailer_id>0){
				$find_request['trailer_id']=$trailer_id;
			}
			if($loadtype_id>0){
				$find_request['loadtype_id']=$loadtype_id;
			}
			// text filter
			if(!empty($request_from)){
				$find_request['like']['pickup_location']=$request_from;
			}
			if(!empty($request_to)){
				$find_request['like']['dropoff_location']=$request_to;
			}
			if($request_weight>0){
				$find_request['weight']=$request_weight;
			}
			
			if($bid_status>='0'){
				if($bid_status=='5'){
					$trans_bid_assos['bid_status']=array('0','3'); //
				}
				elseif($bid_status=='13' || $bid_status=='14'){
					$trans_bid_assos['bid_status']='2'; // transporter accepted
					$find_request['request_status']=$bid_status;
					$find_request['transporter_id']=$user_id;
				}
				elseif($bid_status=='6'){
					$trans_bid_assos['bid_status']=array('1','2');
					$find_request['request_status <']='5';
				}
				elseif($bid_status=='7'){ // only confirmed and after driver assing
					$trans_bid_assos['bid_status']='2';
					$find_request['request_status >=']='5';
				}
				else{
					$trans_bid_assos['bid_status']=$bid_status;
				}
			}
			$assos_cond=array(
				'count'=>'1',
				'fields'=>array('request_id'),
				'bid_assos'=>array(),
				'trans_bid_cond'=>$trans_bid_cond,
				'trans_bid_assos'=>$trans_bid_assos,
			);
			
			$total_row = $this->getrequests($find_request,$assos_cond,$offset,$limit);
			if($total_row>0){
				$assos_cond=array(
					'count'=>'0',
					'bid_assos'=>array(),
					'trans_bid_cond'=>$trans_bid_cond,
					'trans_bid_assos'=>$trans_bid_assos,
				);
				$requests = $this->getrequests($find_request,$assos_cond,$offset,$limit);
			}
			else{
				$requests=array();
			}
			
			$response_data['total_row']=$total_row;
			$response_data['requests']=$requests;
			$this->response_status=1;
		}
		$this->json_output($response_data);
	}
	
	public function delete_bid(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for driver
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$bid_id = $this->input->post("bid_id");
			if(empty($bid_id)){
				$this->response_message="The Bid field is required.";
				$this->json_output($response_data);
			}
			$find_bid=array(
				'bid_id'=>$bid_id,
				'user_id'=>$user_id,
			);
			$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_bid);
			if(empty($bid)){
				$this->response_message="Bid information not found.";
				$this->json_output($response_data);
			}
			if(in_array($bid['bid_status'],array('1','2'))){
				$this->response_message="You cant able to delete. This bid accepted by the customer.";
				$this->json_output($response_data);
			}
			//now remove the bid 
			$this->BaseModel->removeDatas($this->tableNameRequestBid,$find_bid);
			$this->response_message="Bid deleted successfully.";
			$this->response_status='1';
		}
		$this->json_output($response_data);
	}
	
	// driver section 
	public function driver_request_status(){
		$response_data=array();
		$response_data['request_status']=$this->getdriverchangestatus();
		$this->json_output($response_data);
		$this->minimum_param_checked(1);
		// validate the request only for driver
		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==1){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$response_data['request_status']=$this->getdriverchangestatus();
			$this->response_message="Driver allowed request status";
			$this->response_status=1;
		}
		$this->json_output($response_data);
	}
	
	public function update_request_status(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for driver
		if($this->logged_user['user_type'] !=1){ //|| $this->logged_user['is_company']==1
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$request_id = $this->input->post('request_id');
			$request_status = $this->input->post('request_status');
			// validte the data 
			if(empty($request_id)){
				$this->response_message="The Request field is required.";
				$this->json_output($response_data);
			}
			//validate the driver valied request status 
			$driver_allowed = $this->getdriverchangestatus();
			if(!array_key_exists($request_status,$driver_allowed)){
				$this->response_message="Invalid request status received";
				$this->json_output($response_data);
			}
			// request details validate 
			$find_request=array(
				'request_id'=>$request_id,
				'request_status <'=>$request_status,
			);
			
			if($this->logged_user['is_company']){
				$find_request['transporter_id']=$user_id;
			}
			else{
				$find_request['driver_id']=$user_id;
			}
			
			$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
			if(empty($request)){
				$this->response_message="Request details not found.";
				$this->json_output($response_data);
			}
			// get details from the request
			$vehicle_id = $request['vehicle_id'];
			//update the request status
			$update_data = array(
				'update_date'=>$this->dateformat,
				'request_status'=>$request_status
			);
			
			if($request_status==REQUEST_COMPLED_STATUS){ // completed the request
				$update_data['completed_date']=$this->dateformat;
				// need to creaate the image
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
				$request_image = $this->requestmapimage($locations,$request_id,1);
				if(!empty($request_image)){
					$update_data['request_image']=$request_image;
				}
			}
			// track the request status 
			$request_status_track = json_decode($request['request_status_track']);
			$request_status_track[]=array(
				'request_status'=>$update_data['request_status'],
				'create_date'=>$update_data['update_date']
			);
			$update_data['request_status_track']=json_encode($request_status_track);
			
			$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);
			
			//now update the driver status 
			$update_driver_cond=array(
				'user_id'=>$user_id,
				'user_type'=>'1',
				'is_company'=>'0'
			);
			$update_vehicle_cond=array(
				'vehicle_id'=>$vehicle_id
			);
			
			if($request_status==REQUEST_COMPLED_STATUS){ //completed the delivery now set as available
				
				$update_user=array(
					'user_status'=>'1',
					'update_date'=>$this->dateformat
				);
				$this->BaseModel->updateDatas($this->tableNameUser,$update_user,$update_driver_cond);
				// availabe the selected vehicle 
				$update_vehicle=array(
					'vehicle_status'=>'1',
					'update_date'=>$this->dateformat
				);
				$this->BaseModel->updateDatas($this->tableNameVehicle,$update_vehicle,$update_vehicle_cond);
			}
			else{
				// checked if not in in-transit mode then update into transit state
				if($this->logged_user['user_status']!=3){ // in transit mode
					$update_user=array(
						'user_status'=>'3',
						'update_date'=>$this->dateformat
					);
					$this->BaseModel->updateDatas($this->tableNameUser,$update_user,$update_driver_cond);
					// un-availabe the selected vehicle 
					$update_vehicle=array(
						'vehicle_status'=>'3',
						'update_date'=>$this->dateformat
					);
					$this->BaseModel->updateDatas($this->tableNameVehicle,$update_vehicle,$update_vehicle_cond);
				}
			}
			$this->response_message="Request status updated successfully";
			$this->response_status=1;
			// create a notification
			$notification_type='';
			if($request_status=='7'){ //loading
				$notification_type='9';
			}
			elseif($request_status=='8'){ // loaded
				$notification_type='10';
			}
			elseif($request_status=='9'){ // trip start
				$notification_type='11';
			}
			elseif($request_status=='10'){ // reached
				$notification_type='12';
			}
			elseif($request_status=='11'){ // unloading
				$notification_type='13';
			}
			elseif($request_status=='12'){ // unloaded
				$notification_type='14';
			}
			elseif($request_status=='13'){ // completed
				$notification_type='15';
			}
			else{
				$notification_type='7'; // arriving
			}
			// customer section 
			$notification_data=array(
				'request_id'=>$request_id,
				'user_id'=>$user_id,
				'receiver_user_id'=>$request['user_id'],
				'notification_type'=>$notification_type,
			);
			$this->add_notification($notification_data,$is_return=1);
			// transporter section 
			$notification_data=array(
				'request_id'=>$request_id,
				'user_id'=>$user_id,
				'receiver_user_id'=>$request['transporter_id'],
				'notification_type'=>$notification_type,
			);
			$this->add_notification($notification_data,$is_return=1);
		}
		$this->json_output($response_data);
	}
	
	public function driver_location_update(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$this->logged_user['user_type']='1';
		$this->logged_user['is_company']='0';
		if($this->logged_user['user_type'] == 0 || $this->logged_user['is_company']==1){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$rules=array(
				/*array(
					'field'=>'request_id',
					'label'=>'Request',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),*/
				array(
					'field'=>'latitude',
					'label'=>'Latitude',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'longitude',
					'label'=>'Longitude',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$request_id = $this->input->post('request_id');
				$latitude = $this->input->post('latitude');
				$longitude = $this->input->post('longitude');
				// validate the request 
				if($request_id>0){
					$find_request=array(
						'request_id'=>$request_id,
						'driver_id'=>$user_id,
						'request_status >'=>'5',
						'request_status <'=>REQUEST_COMPLED_STATUS,
					);
					$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
					if(empty($request)){
						$this->response_message="Request details not found.";
						$this->json_output($response_data);
					}
				}
				else{
					$request_id='0';
				}
				$save_data=array(
					'request_id'=>$request_id,
					'user_id'=>$user_id,
					'latitude'=>$latitude,
					'longitude'=>$longitude,
					'place_id'=>'',
					'place_address'=>'',
					'create_date'=>$this->dateformat,
					'update_date'=>$this->dateformat,
				);
				$driver_location_id = $this->BaseModel->insertData($this->tableNameRequestDriverLocation,$save_data);
				if($driver_location_id>0){
					$this->response_message="Location updated";
					$this->response_status='1';
				}
				else{
					$this->response_message="Location updated faild";
				}
			}
			else{
				$this->response_message=validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function multiple_location_update(){
		$response_data=array();
		$this->minimum_param_checked(1);
		if($this->logged_user['user_type'] == 0 || $this->logged_user['is_company']==1){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$request_id = $this->input->post('request_id');
			$location_datas = $this->input->post('location_datas'); // its a json string 
			/*
				$location_datas=[{"latitude":"","longitude":""}]
			*/
			if(empty($request_id) || $request_id<0){
				$this->response_message="The Request field is required.";
				$this->json_output($response_data);
			}
			if(empty($location_datas)){
				$this->response_message="The Location Datas field is required.";
				$this->json_output($response_data);
			}
			$location_datas = json_decode($location_datas,true);
			
			// validate request and driver 
			$find_request=array(
				'request_id'=>$request_id,
				'driver_id'=>$user_id,
				'request_status >'=>'5',
				'request_status <'=>REQUEST_COMPLED_STATUS,
			);
			$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
			if(empty($request)){
				$this->response_message="Request details not found.";
				$this->json_output($response_data);
			}
			if(is_array($location_datas) && count($location_datas)>0){
				$this->response_message="Location uploaded";
				foreach($location_datas as $location_data){
					$save_data=array(
						'request_id'=>$request_id,
						'user_id'=>$user_id,
						'latitude'=>$location_data["latitude"],
						'longitude'=>$location_data["longitude"],
						'place_id'=>'',
						'place_address'=>'',
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$this->BaseModel->insertData($this->tableNameRequestDriverLocation,$save_data);
				}
			}
			else{
				$this->response_message="Location not uploaded";
			}
			$this->response_status=1;
		}
		$this->json_output($response_data);
	}
	
	// customer section 
	public function place_request(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for user
		if($this->logged_user['user_type'] !=0 ){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$transporter_id = $this->input->post('transporter_id'); // for create private request
			$this->load->library(array('form_validation'));
			$this->load->helper(array('array'));
			$rules = array(
				array(
					'field'=>'pickup_location',
					'label'=>'Pickup Location',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'pickup_latitude',
					'label'=>'Pickup Coordinate',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'pickup_longitude',
					'label'=>'Pickup Coordinate',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'dropoff_location',
					'label'=>'Dropoff Location',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'dropoff_latitude',
					'label'=>'Dropoff Coordinate',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'dropoff_longitude',
					'label'=>'Dropoff Coordinate',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'pickup_date',
					'label'=>'Pickup Date',
					'rules'=>'trim|required|callback_valid_date_format|callback_valid_date',
					'errors'=>array(
						'valid_date_format'=>'The Pickup Date field format should be yyyy-mm-dd.',
						'valid_date'=>'The Pickup Date is invalid.',
					)
				),
				array(
					'field'=>'pickup_time',
					'label'=>'Pickup Time',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				array(
					'field'=>'trailer_id',
					'label'=>'Trailer',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'loadtype_id',
					'label'=>'Load Type',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'weight',
					'label'=>'Weight',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'size',
					'label'=>'Load Size',
					'rules'=>'trim|required',
					'errors'=>array()
				),
				/*array(
					'field'=>'request_amount',
					'label'=>'Amount',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),*/
				array(
					'field'=>'description',
					'label'=>'Description',
					'rules'=>'trim|required',
					'errors'=>array()
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$save_data=array('user_id','pickup_location','pickup_latitude','pickup_longitude','pickup_place_id','dropoff_location','dropoff_latitude','dropoff_longitude','dropoff_place_id','pickup_date','pickup_time','trailer_id','weight','size','request_amount','description','loadtype_id');
				$save_data = elements($save_data,$this->input->post());
				$save_data['create_date']=$this->dateformat;
				$save_data['update_date']=$this->dateformat;
				// another trailer type section 
				$other_trailer_txt = $this->input->post('other_trailer_txt');
				if(!empty($other_trailer_txt)){
					// find the name is already preasent or name
					if($this->trailer_uniquename($other_trailer_txt)){
						$save_data['other_trailer_txt']=$other_trailer_txt;
					}
					else{
						$this->response_message="This trailer name already present";
						$this->json_output(array());
					}
				}
				
				//calculate the co-ordinate distance among the souce and destination location 
				$distance = $this->distance_calculate($save_data['pickup_latitude'],$save_data['pickup_longitude'],$save_data['dropoff_latitude'],$save_data['dropoff_longitude']);
				$save_data['route_distance']=$distance;// in meter
				// track the request status 
				$request_status_track[]=array(
					'request_status'=>'0',
					'create_date'=>$save_data['create_date']
				);
				$save_data['request_status_track']=json_encode($request_status_track);
				// in private mode request 
				if($transporter_id>0){
					$save_data['is_private']=$transporter_id;
				}
				else{
					$save_data['is_private']=0;
				}
				// validate date format 
				$request_id = $this->BaseModel->insertData($this->tableNameRequest,$save_data);
				if($request_id>0){
					$this->response_message="Your request placed successfully";
					$this->response_status=1;
				}
				else{
					$this->response_message="Your request does not placed successfully";
				}
			}
			else{
				$this->response_message=validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function trailer_uniquename($name='', $id=0){
		if(!empty($name)){
			$find_cond=array(
				'UPPER(trailer_name)'=>strtoupper($name)
			);
			if($id>0){
				$find_cond['trailer_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameLanguageTrailer,$find_cond);
			if(empty($tableRow)){
				return true;
			}
		}
		return false;
	}
	
	public function valid_date($date_str){
		if(!empty($date_str)){
			if($date_str < date('Y-m-d')){
				return false;
			}
		}
		return true;
	}
	
	public function valid_date_format($date_str=''){
		if(!empty($date_str)){
			return $this->dateformatvalidate($date_str);
		}
		return true;
	}
	
	public function distance_calculate($latitude_one=null,$longitude_one=null,$latitude_two=null,$longitude_two=null,$unit=0,$method=1){
		$distance=0; //meter 
		if($latitude_one==null || $longitude_one==null || $latitude_two==null || $longitude_two==null){
			return $distance;
		}
		else{
			// calculation the distance in meter unit
			$latFrom = deg2rad($latitude_one);
			$lonFrom = deg2rad($longitude_one);
			$latTo = deg2rad($latitude_two);
			$lonTo = deg2rad($longitude_two);
			$latDelta = $latTo - $latFrom;
			$lonDelta = $lonTo - $lonFrom;
			if($unit=='1'){ // mile
				$earthRadius=3959;
			}
			elseif($unit=='2'){
				$earthRadius=6371; // Km
			}
			else{
				$earthRadius = 6371000; //meter
			}
			
			switch($method){
				case 1: //Haversine formula 
					$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
					$distance = $angle * $earthRadius;
					break;
				case 2: //Vincenty formula
					$a = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
					$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
					$angle = atan2(sqrt($a), $b);
					
					$distance = $angle * $earthRadius;
					break;
				case 3: //Havercos formula
					$angle = sin($latFrom) * sin($latTo) +  cos($latFrom) * cos($latTo) * cos($lonDelta);
					$angle = acos($angle);
					$distance = $angle * $earthRadius;
					/*
					$degrees = rad2deg($angle);
					if($unit=='1'){ // mile
						$distance = $degrees * 69.05482;
					}
					else{ // miter
						$distance = $degrees * 111.13384 * 1000;
					}*/
					//$distance = $degrees * 111.13384; // 1 degree = 111.13384 km,
					//$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles,
					//$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles
					
				default:
					break;
			}
			return $distance;
		}
	}
	public function point_distance(){
		$latOne = $this->input->post('lat_one');
		$longOne = $this->input->post('long_one');
		$latTwo = $this->input->post('lat_two');
		$longTwo = $this->input->post('long_two');
		$unit = $this->input->post('unit');
		if(!in_array($unit,array('0','1','2'))){
			$unit=0;
		}
		
		for($i=1;$i<4;$i++){
			$distance = $this->distance_calculate($latOne,$longOne,$latTwo,$longTwo,$unit,$i);
			$response_data['distance_'.$i]=$distance;
		}
		
		if($unit=='1'){
			$response_data['unit']='mile';
		}
		elseif($unit=='2'){
			$response_data['unit']='Km';
		}
		else{
			$response_data['unit']='miter';
		}
		$this->response_status='1';
		$this->json_output($response_data);
	}
	public function edit_request(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for user
		if($this->logged_user['user_type'] !=0 ){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'request_id',
					'label'=>'Request',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'The %s field is required.'
					)
				),
				array(
					'field'=>'description',
					'label'=>'Description',
					'rules'=>'trim|required',
					'errors'=>array()
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('','');
			if($this->form_validation->run()===true){
				$request_id = $this->input->post('request_id');
				$description = $this->input->post('description');
				// validate the request section 
				$find_request=array(
					'request_id'=>$request_id,
					'user_id'=>$user_id,
					'request_status <'=>REQUEST_COMPLED_STATUS
				);
				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
				if(!empty($request)){
					$update_data=array(
						'description'=>$description,
						'update_date'=>$this->dateformat
					);
					$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);
					$this->response_message="Request description updated successfully";
					$this->response_status=1;
				}
				else{
					$this->response_message="Request details not found";
				}
			}
			else{
				$this->response_message=validation_errors();
			}
		}
		$this->json_output($response_data);
	}
	
	public function request_bids(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for transporter and user
		if(($this->logged_user['user_type'] !=0) && ($this->logged_user['is_company']==0)){
			$this->response_message="Invalid request";
			$this->json_output($response_data);
		}
		$user_id = $this->logged_user_id;
		// receive params 
		$request_id = $this->input->post('request_id');
		$bid_status = $this->input->post('bid_status');
		$search_text = $this->input->post('search_text');
		
		$page_no = $this->input->post('page_no');
		$page_no = ($page_no>1)?$page_no:1;
		$limit = $this->limit;
		$offset = ($page_no-1)*$limit;
		//validation section 
		if(empty($request_id)){
			$this->response_message="The Request field is required.";
			$this->json_output($response_data);
		}
		$find_request = array(
			'request_id'=>$request_id
		);
		
		if($this->logged_user['user_type']==0){
			$find_request['user_id']=$user_id;
		}
		
		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
		if(empty($request)){
			$this->response_message="Request details not found.";
			$this->json_output($response_data);
		}
		
		$find_bid=array(
			'request_id'=>$request_id,
			'is_admin_delete'=>'0',
		);
		if($bid_status>0){
			$find_bid['bid_status']=$bid_status;
		}
		$extra=array(
			'is_count'=>'1'
		);
		$requestbids=array();
		$total_row = $this->getrequestbids($find_bid,$extra);
		if($total_row>0){
			$extra=array(
				'order_by'=>array(),
				'limit'=>$limit,
				'offset'=>$offset,
				'is_count'=>'0',
				'select_fields'=>array('bid_id','bid_amount','bid_status','bid_comment','cancel_comment','user_id','create_date'),
				'search_text'=>$search_text
			);
			$requestbids = $this->getrequestbids($find_bid,$extra);
		}
		$this->response_status='1';
		$response_data['total_row']=$total_row;
		$response_data['bids']=$requestbids;
		$this->json_output($response_data);
	}
	
	public function bid_accept(){
		$response_data=array();
		$this->minimum_param_checked(1);
		// validate the request only for user
		if($this->logged_user['user_type'] !=0 ){
			$this->response_message="Invalid request";
		}
		else{
			$user_id = $this->logged_user_id;
			// receive the posted data 
			$request_id = $this->input->post('request_id');
			$bid_id = $this->input->post('bid_id');
			// validation section 
			if(empty($request_id)){
				$this->response_message="The Request field is required.";
				$this->json_output($response_data);
			}
			if(empty($bid_id)){
				$this->response_message="The Bid field is required.";
				$this->json_output($response_data);
			}
			$find_request = array(
				'request_id'=>$request_id,
				'user_id'=>$user_id,
				'request_status'=>array('1','4')
			);
			$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
			if(empty($request)){
				$this->response_message="This request is not valid for accepting bid";
				$this->json_output($response_data);
			}
			$find_bid=array(
				'request_id'=>$request_id,
				'bid_id'=>$bid_id,
				'bid_status'=>'0'
			);
			$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_bid);
			if(empty($bid)){
				$this->response_message="Bid details not found.";
				$this->json_output($response_data);
			}
			// now validate if cancelled bid and current bid are same or not 
			if($request['bid_id']==$bid_id){
				$this->response_message="This Bid is cancelled by transporter. Please choose another one";
				$this->json_output($response_data);
			}
			
			// now update the request 
			$update_data=array(
				'request_status'=>'2', // bid accept by cutomer 
				'bid_id'=>$bid_id,
				'update_date'=>$this->dateformat
			);
			// track the request status 
			$request_status_track = json_decode($request['request_status_track']);
			$request_status_track[]=array(
				'request_status'=>$update_data['request_status'],
				'create_date'=>$update_data['update_date']
			);
			$update_data['request_status_track']=json_encode($request_status_track);
			
			$find_request=array(
				'request_id'=>$request_id,
				'user_id'=>$user_id,
			);
			$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);
			// now update the bid 
			$update_bid=array(
				'bid_status'=>'1',
				'update_date'=>$this->dateformat
			);
			$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_bid,$find_bid);
			// notification section 
			$notification_data=array(
				'request_id'=>$request_id,
				'user_id'=>$user_id,
				'receiver_user_id'=>$bid['user_id'],
				'notification_type'=>'2'
			);
			$this->add_notification($notification_data,$is_return=1);
			// response section 
			$this->response_message="You accepted the bid successfully";
			$this->response_status='1';
		}
		$this->json_output($response_data);
	}
	
	// for all type users
	public function request_track(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		// receive the posted data 
		$request_id = $this->input->post('request_id');
		if(empty($request_id)){
			$this->response_message="The Request field is required.";
			$this->json_output($response_data);
		}
		$find_request=array(
			'request_id'=>$request_id
		);
		if($this->logged_user['user_type']==1){
			if($this->logged_user['is_company']==1){
				$find_request['transporter_id']=$user_id;
			}
			else{
				$find_request['driver_id']=$user_id;
			}
		}
		else{
			$find_request['user_id']=$user_id;
		}
		
		$select_fields=array();
		//$request = $this->BaseModel->getData($this->tableNameRequest,$find_request,$select_fields);
		$request = $this->getrequests($find_request);
		if(empty($request)){
			$this->response_message="Request details not found.";
			$this->json_output($response_data);
		}
		else{
			$request = $request[0];
			$response_data=$request;
		}
		$request_status_track = json_decode($request['request_status_track']);
		$response_data['status_track']=$request_status_track;
		$this->response_status=1;
		$this->response_message="Request status traking";
		
		$this->json_output($response_data);
	}
	
	public function request_list(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$user_type = $this->logged_user['user_type'];
		$is_company = $this->logged_user['is_company'];
		// set up the find cond 
		$request_status = $this->input->post('request_status');
		$trailer_id = $this->input->post('trailer_id');
		$loadtype_id = $this->input->post('loadtype_id');
		$driver_id = $this->input->post('driver_id');
		$vehicle_id = $this->input->post('vehicle_id');
		
		$request_from = $this->input->post('request_from');
		$request_to = $this->input->post('request_to');
		$request_weight = $this->input->post('request_weight');
		
		// page section 
		$page_no = $this->input->post('page_no');
		$page_no = ($page_no>1)?$page_no:1;
		$limit = $this->limit;
		$offset = (($page_no-1)*$limit);
		if(empty($request_status)){
			$request_status=0;
		}
		$trans_bid_cond=array();
		$bid_assos=array();
		$find_request=array();
		if($user_type==0){
			// for user section
			$find_request['user_id']=$user_id;
			$find_request['request_status']=$request_status;
		}
		else{
			// transportet and driver section 
			if($is_company==0){
				// driver section
				$find_request['driver_id']=$user_id;
				if($request_status>'5'){
					$find_request['request_status']=$request_status;
				}
				elseif($request_status=='1'){
					$find_request['request_status <']='13';
					$find_request['request_status >=']='5';
				}
				else{
					//$find_request['request_status']=$request_status;
					$find_request['request_status <']='13';
					$find_request['request_status >=']='5';
				}
				$trans_bid_cond=array(
					'user_id'=>'0'
				);
			}
			else{
				// transporter section
				$stape='0';
				if($request_status==='2'){ // customer accept the bid of the tranporti
					$bid_assos=array(
						'user_id'=>$user_id,
						'bid_status'=>'1'
					);
					$stape='1';
				}
				elseif($request_status==='3'){ // transporter accept 
					$bid_assos=array(
						'user_id'=>$user_id,
						'bid_status'=>'2'
					);
					$find_request['transporter_id']=$user_id;
					$stape='2';
				}
				elseif($request_status==='4'){ // cancel by transporter 
					$trans_bid_cond=array(
						'user_id'=>$user_id,
						'bid_status'=>'3'
					);
					$stape='3';
				}
				elseif($request_status>='5'){
					$find_request['transporter_id']=$user_id;
					$find_request['request_status']=$request_status;
					$stape='4';
					$trans_bid_cond=array(
						'user_id'=>$user_id
					);
				}
				elseif($request_status==='1'){
					$trans_bid_cond=array(
						'user_id'=>$user_id
					);
					$stape='5';
				}
				else{
					$find_request['request_status']=$request_status;
					$find_request['transporter_id']=array(0,$user_id);
					$trans_bid_cond=array(
						'user_id'=>$user_id
					);
					$stape='6';
				}
				
				if($driver_id>0){
					$find_request['driver_id']=$driver_id;
				}
				if($vehicle_id>0){
					$find_request['vehicle_id']=$vehicle_id;
				}
				
				// private and normal both type
				$find_request['is_private']=array('0',$user_id);
			}
		}
		// test section 
		/*$response_data['find_request']=$find_request;
		$response_data['trans_bid_cond']=$trans_bid_cond;
		$response_data['bid_assos']=$bid_assos;
		$response_data['stape']=$stape;*/
		
		// extra filter 
		if($trailer_id>0){
			$find_request['trailer_id']=$trailer_id;
		}
		if($loadtype_id>0){
			$find_request['loadtype_id']=$loadtype_id;
		}
		
		// text filter
		if(!empty($request_from)){
			$find_request['like']['pickup_location']=$request_from;
		}
		if(!empty($request_to)){
			$find_request['like']['dropoff_location']=$request_to;
		}
		if($request_weight>0){
			$find_request['weight']=$request_weight;
		}
		
		//$response_data['find_request']=$find_request;
		$assos_cond=array(
			'count'=>'1',
			'fields'=>array('request_id'),
			'trans_bid_cond'=>$trans_bid_cond,
			'bid_assos'=>$bid_assos,
		);
		$total_row = $this->getrequests($find_request,$assos_cond,$offset,$limit);
		if($total_row>0){
			$assos_cond=array(
				'count'=>'0',
				'trans_bid_cond'=>$trans_bid_cond,
				'bid_assos'=>$bid_assos,
			);
			$requests = $this->getrequests($find_request,$assos_cond,$offset,$limit);
		}
		else{
			$requests=array();
		}
		
		$response_data['total_row']=$total_row;
		$response_data['requests']=$requests;
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function create_chat(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$user_type = $this->logged_user['user_type'];
		// posted data 
		$request_id = $this->input->post('request_id');
		$other_user_id = $this->input->post('other_user_id');
		//validation section
		if(empty($request_id)){
			$this->response_message="The Request field is required.";
			$this->json_output($response_data);
		}
		if(empty($other_user_id)){
			$this->response_message="The User field is required.";
			$this->json_output($response_data);
		}
		
		$find_request=array(
			'request_id'=>$request_id
		);
		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
		if(empty($request)){
			$this->response_message="Request details not found.";
			$this->json_output($response_data);
		}
		//find if the chat room created 
		$find_cond=array(
			'request_id'=>$request_id
		);
		if($user_type==1){// transporteee
			$find_cond['transporter_id']=$user_id;
			$find_cond['user_id']=$other_user_id;
		}
		else{
			$find_cond['user_id']=$user_id;
			$find_cond['transporter_id']=$other_user_id;
		}
		
		$chat = $this->BaseModel->getData($this->tableNameChat,$find_cond);
		if(empty($chat)){
			// need to create the chat room 
			$find_cond['create_date']=$this->dateformat;
			$find_cond['update_date']=$this->dateformat;
			$chat_id = $this->BaseModel->insertData($this->tableNameChat,$find_cond);
		}
		else{
			// need to fetch all the masssages
			$chat_id = $chat['chat_id'];
		}
		$response_data['chat_id']=(string)$chat_id;
		$this->response_status=1;
		$this->response_message="Chat initiated successfully";
		$this->json_output($response_data);
	}
	
	public function chat_list(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$user_type = $this->logged_user['user_type'];
		// form data
		$page_no = $this->input->post('page_no');
		$page_no = ($page_no>1)?$page_no:1;
		$limit = $this->limit;
		$offset = (($page_no-1)*$limit);
		
		if($user_type==1){ // transporter want to see user detail
			$find_chat=array(
				'transporter_id'=>$user_id
			);
		}
		else{ // user section want to see the transporter
			$find_chat=array(
				'user_id'=>$user_id
			);
		}
		
		$extra=array(
			'user_type'=>$user_type,
			'is_count'=>'1'
		);
		
		$total_row = $this->getchats($find_chat,$extra);
		if($total_row>0){
			$extra=array(
				'user_type'=>$user_type,
				'limit'=>$limit,
				'offset'=>$offset,
				'order_by'=>array('update_date'=>'DESC')
			);
			$chats = $this->getchats($find_chat,$extra);
		}
		else{
			$chats=array();
		}
		$response_data['total_row']=$total_row;
		$response_data['chats']=$chats;
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function chat_messages(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$user_type = $this->logged_user['user_type'];
		//posted data 
		$chat_id = $this->input->post('chat_id');
		$page_no = $this->input->post('page_no');
		$page_no = ($page_no>1)?$page_no:1;
		$limit = $this->limit;
		$offset = ($page_no)*$limit;
		$find_cond=array(
			'chat_id'=>$chat_id
		);
		$extra = array(
			'user_id'=>$user_id,
			'is_count'=>'1'
		);
		$total_row = $this->getmessages($find_cond,$extra);
		if($total_row>0){
			$offset = ($total_row-$offset);
			if($offset<0){
				$offset=0;
			}
			$extra = array(
				'user_id'=>$user_id,
				'limit'=>$limit,
				'offset'=>$offset
			);
			$messages = $this->getmessages($find_cond,$extra);
		}
		else{
			$messages=array();
		}
		$response_data['total_row']=$total_row;
		$response_data['messages']=$messages;
		
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function send_message(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$user_type = $this->logged_user['user_type'];
		//posted data 
		$chat_id = $this->input->post('chat_id');
		$message_data = trim($this->input->post('message_data'));
		$last_message_id = trim($this->input->post('last_message_id'));
		if(empty($last_message_id)){
			$last_message_id=0;
		}
		
		// validate chat
		if(empty($chat_id)){
			$this->response_message="The Chat field is required.";
			$this->json_output($response_data);
		}
		if(empty($message_data)){
			$this->response_message="The Message field is required.";
			$this->json_output($response_data);
		}
		
		$find_cond=array(
			'chat_id'=>$chat_id,
		);
		$receiver_fls='';
		if($user_type=='1'){ //transporter
			$find_cond['transporter_id']=$user_id;
			$receiver_fls="user_id";
		}
		else{ //customer
			$find_cond['user_id']=$user_id;
			$receiver_fls="transporter_id";
		}
		$chat = $this->BaseModel->getData($this->tableNameChat,$find_cond);
		if(empty($chat)){
			$this->response_message="Chat Room not found.";
			$this->json_output($response_data);
		}
		
		$save_data=array(
			'chat_id'=>$chat_id,
			'user_id'=>$user_id,
			'message_type'=>'0',
			'message_data'=>$message_data,
			'create_date'=>$this->dateformat,
			'update_date'=>$this->dateformat,
		);
		$message_id = $this->BaseModel->insertData($this->tableNameMessage,$save_data);
		if($message_id>0){
			
			// get all the messages after laset seen message
			$is_revers=false;
			$find_cond=array(
				'chat_id'=>$chat_id,
			);
			$extra = array(
				'user_id'=>$user_id
			);
			if($last_message_id>0){
				$find_cond['message_id >']=$last_message_id;
			}
			else{
				//$find_cond['message_id']=$message_id;
				$extra['order_by']=array('message_id'=>'DESC');
				$extra['limit']=$this->limit;
				$extra['offset']=0;
				$is_revers=true;
			}
			
			$chat_meaages = $this->getmessages($find_cond,$extra);
			if($is_revers){
				$chat_meaages = array_reverse($chat_meaages);
			}
			$response_data['messages']=$chat_meaages;
			$this->response_message="Message send";
			$this->response_status='1';
			
			// update the receiver chat count 
			$receiver_user_id = $chat[$receiver_fls];
			$this->update_user_count($receiver_user_id,1,2);
			// send the notification of the reciever
			$sender_name = $this->logged_user['first_name']." ".$this->logged_user['last_name'];
			
			$nofification_data=array(
				'request_id'=>$chat['request_id'],
				'user_id'=>$user_id,
				'receiver_user_id'=>$receiver_user_id,
				'notification_type'=>'16',
				'chat_id'=>$chat['chat_id'],
				'sender_name'=>ucwords($sender_name),
			);
			$this->add_notification($nofification_data,$is_return=1,$is_saved=0);
		}
		else{
			$this->response_message="Message does not send";
		}
		$this->json_output($response_data);
	}
	
	public function delete_message(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$user_type = $this->logged_user['user_type'];
		// get posted data 
		$message_id = $this->input->post('message_id');
		
		if(empty($message_id)){
			$this->response_message="The Message field is required.";
			$this->json_output($response_data);
		}
		$find_cond=array(
			'message_id'=>$message_id
		);
		$extra=array(
			'user_id'=>$user_id
		);
		$message = $this->getmessages($find_cond,$extra);
		if(empty($message)){
			$this->response_message="Message details not found.";
			$this->json_output($response_data);
		}
		// if found 
		$save_data=array(
			'message_id'=>$message_id,
			'user_id'=>$user_id,
			'create_date'=>$this->dateformat,
			'update_date'=>$this->dateformat,
		);
		$delete_id = $this->BaseModel->insertData($this->tableNameUserDeleteMessage,$save_data);
		if(empty($delete_id)){
			$this->response_message="Message Deteling faild";
			$this->json_output($response_data);
		}
		$this->response_message="This message deleted successfully.";
		$this->response_status=1;
		$this->json_output($response_data);
	}
	
	public function rating_list(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$other_user_id = $this->input->post('other_user_id');
		
		$page_no = $this->input->post('page_no');
		$page_no = ($page_no>1)?$page_no:1;
		$limit = $this->limit;
		$offset = ($page_no-1)*$limit;
		
		$find_rattings=array(
			'receiver_user_id'=>$user_id,
			'is_blocked'=>'0'
		);
		if($other_user_id>0){
			$find_rattings['receiver_user_id']=$other_user_id;
		}
		$extra=array(
			'limit'=>$limit,
			'offset'=>$offset,
			'is_count'=>'1'
		);
		$total_row = $this->getratings($find_rattings,$extra);
		if($total_row>0){
			$extra['is_count']=0;
			$ratings = $this->getratings($find_rattings,$extra);
		}
		else{
			$ratings=array();
		}
		$this->response_status=1;
		$this->response_message="User rating list";
		$response_data['total_row']=$total_row;
		$response_data['ratings']=$ratings;
		$this->json_output($response_data);
	}
	
	public function give_rating(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$request_id = $this->input->post('request_id');
		$receiver_user_id = $this->input->post('receiver_user_id');
		$rating = $this->input->post('rating');
		$communication_rating = $this->input->post('communication_rating');
		$trust_rating = $this->input->post('trust_rating');
		$quality_rating = $this->input->post('quality_rating');
		$user_comment = $this->input->post('user_comment');
		// varification 
		if(empty($request_id) || $request_id<0){
			$this->response_message="The Request field is required.";
			$this->json_output($response_data);
		}
		if(empty($receiver_user_id) || $receiver_user_id<0){
			$this->response_message="The Rating Receiver field is required.";
			$this->json_output($response_data);
		}
		// data validate 
		$find_request = array(
			'request_id'=>$request_id,
			'request_status'=>REQUEST_COMPLED_STATUS
		);
		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
		if(empty($request)){
			$this->response_message="Request details not valid";
			$this->json_output($response_data);
		}
		if(empty($rating) || $rating<0){
			$rating=0;
		}
		if(empty($communication_rating) || $communication_rating<0){
			$communication_rating=0;
		}
		if(empty($trust_rating || $trust_rating<0)){
			$trust_rating=0;
		}
		if(empty($quality_rating || $quality_rating<0)){
			$quality_rating=0;
		}
		if(empty($user_comment)){
			$user_comment='';
		}
		// max rating value validate 
		if($rating>5){
			$rating=5;
		}
		if($communication_rating>5){
			$communication_rating=5;
		}
		if($trust_rating>5){
			$trust_rating=5;
		}
		if($quality_rating>5){
			$quality_rating=5;
		}
		// validate previous rating given or not
		$find_rating=array(
			'request_id'=>$request_id,
			'giver_user_id'=>$user_id,
		);
		$userrating = $this->BaseModel->getData($this->tableNameUserRating,$find_rating);
		if(!empty($userrating)){
			$this->response_message="You already given the rating on this request.";
			$this->json_output($response_data);
		}
		
		$save_data=array(
			'request_id'=>$request_id,
			'giver_user_id'=>$user_id,
			'receiver_user_id'=>$receiver_user_id,
			'rating'=>$rating,
			'communication_rating'=>$communication_rating,
			'trust_rating'=>$trust_rating,
			'quality_rating'=>$quality_rating,
			'user_comment'=>$user_comment,
			'create_date'=>$this->dateformat,
			'update_date'=>$this->dateformat,
		);
		$rating_id = $this->BaseModel->insertData($this->tableNameUserRating,$save_data);
		if($rating_id>0){
			$this->response_status=1;
			$this->response_message="Thanks for your rating.";
		}
		else{
			$this->response_message="Sorry! we have problem, please try after sometime";
		}
		$this->json_output($response_data);
	}

	public function notification_list(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$request_id = $this->input->post('request_id');
		$page_no = $this->input->post('page_no');
		$page_no = ($page_no>1)?$page_no:1;
		$limit = $this->limit;
		$offset = (($page_no-1)*$limit);
		$find_notifications=array(
			'receiver_user_id'=>$user_id,
			'is_blocked'=>'0'
		);
		if($request_id>0){
			$find_notifications['request_id']=$request_id;
		}
		
		$select_fields=array();
		$extra_cond = array(
			'is_count'=>'1'
		);
		$total_row = $this->getnotifications($find_notifications,$extra_cond);
		if($total_row>0){
			$extra_cond = array(
				'is_count'=>'0',
				'limit'=>$limit,
				'offset'=>$offset,
				'order_by'=>array(
					'notification_id'=>'DESC'
				)
			);
			$notifications = $this->getnotifications($find_notifications,$extra_cond);
			// update all the notification as read 
			$update_cond=array(
				'is_read'=>'0',
				'receiver_user_id'=>$user_id
			);
			$update_data=array(
				'is_read'=>'1',
				'update_date'=>$this->dateformat
			);
			$this->BaseModel->updateDatas($this->tableNameRequestNotification,$update_data,$update_cond);
			// valish the count of the user notification section 
			$this->update_user_count($user_id,0,1);
			
		}
		else{
			$notifications=array();
		}
		$this->response_status='1';
		$response_data['total_row']=$total_row;
		$response_data['notifications']=$notifications;
		$this->json_output($response_data);
	}
	
	public function notification_add(){
		$response_data=array();
		//$this->minimum_param_checked(1);
		//$user_id = $this->logged_user_id;
		$user_id = $this->input->post('user_id');
		$request_id = $this->input->post('request_id');
		$receiver_user_id = $this->input->post('receiver_user_id');
		$notification_type = $this->input->post('notification_type');
		if(empty($notification_type)){
			$notification_type='1';
		}
		//$is_saved = $this->input->post('is_saved');
		$notification_data=array(
			'request_id'=>$request_id,
			'user_id'=>$user_id,
			'receiver_user_id'=>$receiver_user_id,
			'notification_type'=>$notification_type
		);
		$is_saved=0;
		$response_data = $this->add_notification($notification_data,$is_return=1,$is_saved);
		$this->response_status='1';
		$this->json_output($response_data);
	}
	
	public function unread_notification(){
		$response_data=array();
		$this->minimum_param_checked(1);
		$user_id = $this->logged_user_id;
		$request_id = $this->input->post('request_id');
		if($request_id>0){ // normal way to get notification cout according request id
			$find_data=array(
				'receiver_user_id'=>$user_id,
				'is_read'=>'0'
			);
			if($request_id>0){
				$find_data['request_id']=$request_id;
			}
			$extra_cond=array(
				'is_count'=>'1',
			);
			$unread_notification = $this->getnotifications($find_data,$extra_cond);
			$unread_chat = 0;
		}
		else{
			// get notification count from counts table 
			$find_cond=array(
				'user_id'=>$user_id,
				'is_blocked'=>'0',
			);
			$select_fld=array();
			$count = $this->BaseModel->getData($this->tableNameUserCount,$find_cond,$select_fld);
			$unread_notification = isset($count['unread_notification'])?$count['unread_notification']:'0';
			$unread_chat = isset($count['unread_chat'])?$count['unread_chat']:'0';
		}
		
		$response_data['unread_notification']=$unread_notification;
		$response_data['unread_chat']=$unread_chat;
		$this->response_status='1';
		$this->json_output($response_data);
	}
	
	public function add_notification($add_data=array(),$is_return=0,$is_saved=1){
		$response_data=array();
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
				$result = $this->sendpushnotification($saved_data);
				$response_data['push_response']=$result;
			}
		}
		$response_data['notification_text']=$notification_text;
		if(!$is_return){
			$this->json_output($response_data);
		}
		else{
			return $response_data;
		}
	}
	
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
			$users = $this->BaseModel->getDatas($this->tableNameUserRequestKey,$find_user_push_keys,$select_fields,array(),$joins,0,0,$complexcond,$group_by);
			
			if(!empty($users)){
				foreach($users as $user){
					$user_type = $user['user_type'];
					$device_type = $user['device_type'];
					$device_push_key = $user['device_push_key'];
					$device_push_keys = explode(",",$device_push_key);
					
					$messages=array(
						'title'=>ucfirst($push_data['notification_text']),
						'body'=>ucfirst($push_data['notification_text']),
						'data'=>$push_data['data'],
					);
					$user_type=$user_type+1;
					
					if($device_type=='1'){ // android
						$result['andro_d']=$device_push_keys;
						$result['andro'][]= $this->android_push($messages,$device_push_keys,$user_type);
					}
					elseif($device_type=='2'){ // ios
						$result['ios_d']=$device_push_keys;
						$result['ios'][] = $this->ios_push($messages,$device_push_keys,$user_type);
					}
					else{
						// none
					}
				}
			}
		}
		return $result;
	}
	
	public function android_push($messages=array(),$device_push_keys=array(),$push_for=0){
		// $push_for , 1: customer, 2 : transportet/driver
		$result=array();
		if(!empty($device_push_keys) && $push_for>0){
			$andro_app_id='';
			if($push_for=='1'){
				//$andro_app_id='AAAARRRN46E:APA91bFEG1FNuZr1P_CrTyzmI3frkoYrdTvuHuimIpwgSjOuGLvLRcJUvjgbDRQn6diSJ4vO4nz5bxZeYi2NCQxaaTFVyDcWrnG_QuP5KgqbSWVmyjNnyquIR08Gv248RB5RjQ9oAxhW'; // customer app key fcm
				
				$andro_app_id='AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz'; // customer app key fcm
			}
			elseif($push_for=='2'){
				//$andro_app_id='AAAARRRN46E:APA91bFEG1FNuZr1P_CrTyzmI3frkoYrdTvuHuimIpwgSjOuGLvLRcJUvjgbDRQn6diSJ4vO4nz5bxZeYi2NCQxaaTFVyDcWrnG_QuP5KgqbSWVmyjNnyquIR08Gv248RB5RjQ9oAxhW'; // trans/driver app key fcm
				
				$andro_app_id='AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz'; // trans/driver app key fcm
			}
			
			if(!empty($andro_app_id)){
				// Set POST variables
				$url = 'https://fcm.googleapis.com/fcm/send';
				// add click event on notification message and sound
				
				$messages['sound']='default';
				$data = array();
				if(isset($messages['data'])){
					$data = $messages['data'];
					unset($messages['data']);
					// set extra datas 
					/*$messages['content-available']='1';
					$data = array_merge($data,$messages);*/
				}
				
				$fields = array(
					'registration_ids' => $device_push_keys, // multiple id
					'notification'=>$messages,
					'data'=>$data,
					//'priority'=>'high',
				);
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
	
	public function request_map(){
		$response_data=array();
		$request_id = $this->input->post('request_id');
		$is_saved = $this->input->post('is_saved');
		if(empty($request_id) || $request_id<0){
			$this->response_message="Request field is required.";
			$this->json_output($response_data);
		}
		$find_request = array(
			'request_id'=>$request_id
		);
		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
		if(empty($request)){
			$this->response_message="Request details invalid";
			$this->json_output($response_data);
		}
		
		$locations['markers'][]=array(
			'lat'=>$request['pickup_latitude'],
			'long'=>$request['pickup_longitude'],
			'place'=>'P',
		);
		$locations['markers'][]=array(
			'lat'=>$request['dropoff_latitude'],
			'long'=>$request['dropoff_longitude'],
			'place'=>'D',
		);
		$image_name = $this->requestmapimage($locations,$request_id,$is_saved);
		$response_data['image_name']=$image_name;
		$this->response_status='1';
		$this->json_output($response_data);
	}
	
	public function mark_expired_request(){
		$response_data=array();
		$target_date = date("Y-m-d",strtotime("-1 day"));
		$find_condition=array(
			'request_status <='=>'5', // driver not assigned or assigned but no farther action happend till pickup day plush one day 
			'pickup_date <'=>$target_date,
		);
		// for checking section 
		$requests = $this->BaseModel->getDatas($this->tableNameRequest,$find_condition);
		$response_data['requests'] = $requests;
		$this->response_status='1';
		$this->json_output($response_data);
	}
	
	// for back up section 
	public function create_usercount(){
		$response_data=array();
		$sql = "SELECT user_id,create_date,update_date FROM $this->dbprefix$this->tableNameUser WHERE is_deleted='0' AND user_id NOT IN (SELECT user_id FROM $this->dbprefix$this->tableNameUserCount)";
		$datas = $this->BaseModel->customSelect($sql);
		$response_data['users']=$datas;
		if(!empty($datas)){
			// munti row inserted from here 
			$this->BaseModel->insertDatas($this->tableNameUserCount,$datas);
		}
		$this->response_status='1';
		$this->json_output($response_data);
	}
}
?>