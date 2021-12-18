<?php

defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");

class Api extends MY_Controller{

	private $response_status;

	private $response_message;

    private $response_body;

	

	private $logged_user_id;

	private $dflt_country_id;

	private $limit;

	private $success_message;

	private $push_message;



	private $subAdmins;

	private $subAdminsId;

	



	function __construct() {

		parent::__construct();

		/**MODE DEV ERROR INIT*/

		ini_set('display_errors', 0);

		ini_set('display_startup_errors', 0);

		error_reporting(E_ALL);

		/**END DEV */

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

	//prueba git-ftp

	// public function debug($prinf=''){

	// 	if(isset($prinf)){

	// 	    $log  = "____________________________________________________________________________________________________";

	// 	    $log  .= "\n";

  //   		$log  .= "Data: ";

	// 		$log.=json_encode($prinf).PHP_EOL;

	// 		file_put_contents('./logs/api_debug_'.date("j.n.Y").'.txt', $log, FILE_APPEND);

	// 	}

	// }



	private function request_checked() {

		if($this->input->server('REQUEST_METHOD')!=strtoupper('post')){

			$this->response_status=-1;

			$this->json_output();

		}

		$this->write_log();

		$this->minimum_param_checked();

	}

	

	

	public function write_log() {

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

	



	private function minimum_param_checked($is_log_user=0) {

		$this->logged_user_id = $this->input->post('user_id');

		$user_has_key = $this->input->post('user_request_key');

		$device_type = $this->input->post('device_type');

		$device_unique_code = $this->input->post('device_unique_code');

		$device_push_key = $this->input->post('device_push_id');

		$this->language_id = $this->input->post('language_id');


		if(empty($this->language_id)) {

			$this->language_id=1; // default id

		}


		switch($is_log_user) {

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

					$this->response_message= ($this->language_id==1) ? 'Please complete your data to continue' : 'Por favor completa tus datos para continuar';

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

						'table_name_alis'=>'U',

						'join_with'=>$this->tableNameUserRequestKey,

						'join_type'=>'inner',

						'join_on'=>array('user_id'=>'user_id'),

						'conditions'=>array('is_deleted'=>'0'),

						'select_fields'=>array('first_name','last_name','user_type','image','phone_no','is_phone_no_verify','is_company','user_status','super_parent_id')

					),

				);
				

				$fld_arr=array('user_id', 'device_type');

				$userdata = $this->BaseModel->getData($this->tableNameUserRequestKey,$find_has,$fld_arr,array(),$joins);


				if(empty($userdata)) {

					$this->response_message="AUTHENTICATION FAILED";

					$this->response_status=-3;

					$this->json_output();

				} else {

					$this->logged_user = $userdata;

					//$this->pr($userdata);

				}

				

			break;

			    

			default:

			    

				$this->response_status=-2;

				$this->response_message="Los parametros ingresados para el pedido son inválidos. Contacte a nuestros operadores";

				$this->json_output();

				

			break;

		}

	}

	



	private function json_output($response_data=array()){

		switch($this->response_status){

			case -1:

				$this->response_message="Auch! Su pedido no pudo ser procesado";

				$this->response_status=0;

				$this->response_body=null;

				break;

			case -2:

				$this->response_message="Los parametros ingresados para el pedido son inválidos. Contacte a nuestros operadores";

				$this->response_status=0;

                                $this->response_body=null;

				break;

			case -3:

				$this->response_message=($this->language_id==1) ? 'Login again to use Bauen' : 'Vuelve a ingresar tu cuenta para usar Bauen';

				$this->response_status=0;

                $this->response_body=null;

				break;

			default:

				break;

		}

		$response = array(

			'status'=>$this->response_status,

			'message'=>$this->response_message,

            'body' => $this->response_body

		);

		$response = array_merge($response,$response_data);

		die(json_encode($response));

	}





	private function json_output_json($response_data=array()){

		switch($this->response_status){

			case -1:

				$this->response_message="Auch! Su pedido no pudo ser procesado";

				$this->response_status=0;

                $this->response_body=null;

				break;

			case -2:

				$this->response_message="Los parametros ingresados para el pedido son inválidos. Contacte a nuestros operadores";

				$this->response_status=0;

                $this->response_body=null;

				break;

			case -3:

				$this->response_message=($this->language_id==1) ? 'Login again to use Bauen' : 'Vuelve a ingresar tu cuenta para usar Bauen';

				$this->response_status=0;

                $this->response_body=null;

				break;

			default:

			$this->response_body=$response_data;

				break;

		}

		$response['status'] = $this->response_status;

		$response['message'] = $this->response_message;

		$response['body'] = $this->response_body;

		

		echo json_encode($response);

	}





	private function json_output_data($response_data=array()){



		switch($this->response_status){

			case -1:

				$this->response_message="Auch! Su pedido no pudo ser procesado";

				$this->response_status=0;

				break;

			case -2:

				$this->response_message="Los parametros ingresados para el pedido son inválidos. Contacte a nuestros operadores";

				$this->response_status=0;

				break;

			case -3:

				$this->response_message= ($this->language_id==1) ? 'Login again to use Bauen' : 'Vuelve a ingresar tu cuenta para usar Bauen';

				$this->response_status=0;

				break;

			default:

				break;

		}

		$response = array(

			'status'=>$this->response_status,

			'message'=>$this->response_message,

                        'body' => $this->response_data



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

	

	protected function userdetails($user_id=0,$super_parent_id=0){

		if(empty($user_id)){

			return array();

		}

		// get the users basic details 

		$find_cond=array(

			'user_id'=>$user_id,

			'is_blocked'=>array('0','2'),

			//'is_deleted'=>array('1','0')

		);

		$select_flds=array('user_id','is_blocked','first_name','last_name','email','phone_no','user_type','image','dni_no','is_company','company_name','company_licence_no','ruc_no','is_user_verify','verification_code','about_us','address','firebase_id');

		//,'latitude','longitude'

		// make it string 

		$tb = $this->dbprefix.$this->tableNameUser;

		$select_flds = $tb.'.'.implode(", $tb.",$select_flds);

		$order_by=array();

		$joins=array(

			array(

				'table_name'=>$this->tableNameIndustryType,

				'join_with'=>$this->tableNameUser,

				'join_type'=>'left',

				'join_on'=>array('industrytype_id'=>'industrytype_id'),

				'select_fields'=>array('industrytype_name')

			),

		);

		

		if($super_parent_id>0){

			$joins= array(

				// get company details

				array(

					'table_name'=>$this->tableNameUser,

					'table_name_alias'=>'SU',

					'join_with'=>$this->tableNameUser,

					'join_type'=>'inner',

					'join_on'=>array('super_parent_id'=>'user_id'),

					'select_fields'=>array('company_name','company_licence_no','ruc_no','about_us','address'),
					
				    'select_fields_json'=>array('alias' => 'user_sa', 
					                            'fields' => array('first_name', 'last_name', 'email', 'firebase_id')),	

					'oncond'=>array('is_deleted'=>'0')

				),

				array(

					'table_name'=>$this->tableNameUserRating,

					'join_with'=>$this->tableNameUser,

					'join_type'=>'left',

					'join_on'=>array('super_parent_id'=>'receiver_user_id'),

					'select_fields'=>'IFNULL(TRUNCATE(AVG(rating),2),0) rating',

					'oncond'=>array('is_deleted'=>'0')

				),

				array(

					'table_name'=>$this->tableNameIndustryType,

					'join_with'=>$this->tableNameUser,

					'join_with_alias'=>'SU',

					'join_type'=>'left',

					'join_on'=>array('industrytype_id'=>'industrytype_id'),

					'select_fields'=>array('industrytype_name')

				),

			);

		}

		else{

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

		}

		

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

				'is_blocked'=>array('0','2')

			);

			if($super_parent_id>0){

				$find_rattings['receiver_user_id']=$super_parent_id;

			}

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

			if($super_parent_id>0){

				$find_req_summery['user_id']=$super_parent_id;

			}

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

			'is_blocked'=>array('0','2')

		);

		$industrytypes = $this->getindustrytypes($find_industrytype);

		$find_trailer=array(

			'is_blocked'=>array('0','2'),

			'is_default'=>'0'

		);

		$trailers = $this->gettrailers($find_trailer);

		// find another trailer 

		$find_another_trailer=array(

			'is_blocked'=>array('0','2'),

			'is_default'=>'1'

		);

		$another_trailer = $this->gettrailers($find_another_trailer);

		if(!empty($another_trailer)){

			$another_trailer = $another_trailer[0];

		}

		// user doc type

		$find_user_doc_type=array(

			'is_blocked'=>array('0','2'),

			'document_for'=>'1'

		);

		$user_document_types = $this->getdocumenttypes($find_user_doc_type);

		$find_vehicle_doc_type=array(

			'is_blocked'=>array('0','2'),

			'document_for'=>'2'

		);

		$vehicle_document_types = $this->getdocumenttypes($find_vehicle_doc_type);

		// load types 

		$find_loadtype=array(

			'is_blocked'=>array('0','2')

		);

		$loadtypes = $this->getloadtypes($find_loadtype);

		

		$response_data['industrytypes']=$industrytypes;

		$response_data['trailers']=$trailers;

		$response_data['user_document_types']=$user_document_types;

		$response_data['vehicle_document_types']=$vehicle_document_types;

		$response_data['loadtypes']=$loadtypes;

		$response_data['another_trailer']=$another_trailer;

		

		$response_data['termscondition']= base_url('users/termsconditions'); //"https://google.com";

		$response_data['suport_email']="sergio@bauenfreight.com";

		$response_data['suport_phone']="942879517";

		$response_data['term_of_service']=base_url('users/termsconditions'); //"http://google.com";

		// base url 

		$site_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://www.bauenfreight.com").$_SERVER['HTTP_HOST'];

		$response_data['site_url']=$site_url;

		

		$this->response_status=1;

		$this->json_output($response_data);

	}

	

	public function industrytype(){

		$response_data=array();

		$find_industrytype=array(

			'is_blocked'=>array('0','2')

		);

		$industrytypes = $this->getindustrytypes($find_industrytype);

		

		$this->response_status=1;

		$response_data['industrytypes']=$industrytypes;

		$this->json_output($response_data);

	}

	

	public function trailers(){

		$response_data=array();

		$find_trailer=array(

			'is_blocked'=>array('0','2'),

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

			'is_blocked'=>array('0','2'),

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

			'is_blocked'=>array('0','2'),

			'document_for'=>'2'

		);

		$vehicle_document_types = $this->getdocumenttypes($find_vehicle_doc_type);

		$response_data['vehicle_document_types']=$vehicle_document_types;

		$this->response_status=1;

		$this->json_output($response_data);

	}

	

	public function registration(){

		$response_data = array();

		//$data_post = $this->post();

		$lg = $this->input->post('language_id');

		$language_id = empty($lg) ? 1 : $lg;



		$this->load->library(array('form_validation'));

		$this->load->helper(array('array'));		

		$is_company = $this->input->post('is_company');		

		$this->logpost('registration');

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

					'user_unique_email'=> ($language_id==1) ?  'The entered email already exists. Please try with another one or recover your password' : 'El correo electrónico ingresado ya existe. Por favor intenta con otro correo o recupera tu clave'

				)

			),

			array(

				'field'=>'phone_no',

				'label'=>'Phone No.',

				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',

				'errors'=>array(

					'user_unique_phone_no'=>($language_id==1) ? 'The phone number entered is already registered. Please try with another one or recover your password' : 'El número de teléfono ingresado ya está registrado. Por favor intenta con otro o recupera tu clave',

					'valid_phone_no'=> ($language_id==1) ? 'Please enter a valid mobile phone number %s.' : 'Por favor ingresa un número de celular válido ',

				)

			),

			array(

				'field'=>'password',

				'label'=>'Password',

				'rules'=>'trim|required',

				'errors'=>array()

			)

		);

		// if the user registration as company then 

		$user_type = $this->input->post('user_type');

		if(!empty($is_company) && $user_type==1){

			$com_rules=array(

				array(

					'field'=>'company_name',

					'label'=>'Company Name',

					'rules'=>'trim|required',

					'errors'=>array()

				),

				/* array(

					'field'=>'industrytype_id',

					'label'=>'Industry Type',

					'rules'=>'trim|required',

					'errors'=>array(

						'greater_than'=> ($language_id==1) ? 'Please enter your industry type' : 'Por favor ingresa el tipo de industria en la cual laboras'

					)

				) */

			);

			// merge the array 

			$rules = array_merge($rules,$com_rules);

		}

		$this->form_validation->set_rules($rules);

		$this->form_validation->set_error_delimiters('','');

		if($this->form_validation->run()===true){

			$data = $this->input->post();

			$first_name = $this->input->post('first_name');

			$email = $this->input->post('email');

			$phone_no = $this->input->post('phone_no');

			$password = $this->input->post('password');

			$dni_no = $this->input->post('dni_no');

			$user_type = $this->input->post('user_type');



			$last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : '' ;			

			$ruc_no = !empty($_POST['ruc_no']) ? $_POST['ruc_no'] : '' ;

			$company_name = !empty($_POST['company_name']) ? $_POST['company_name'] : '' ;

			$company_licence_no = !empty($_POST['company_licence_no']) ? $_POST['company_licence_no'] : '' ;

			$industrytype_id = !empty($_POST['industrytype_id']) ? $_POST['industrytype_id'] : '' ;

			$country_code = !empty($_POST['country_code']) ? $_POST['country_code'] : '' ;			

			$about_us = !empty($_POST['about_us']) ? $_POST['about_us'] : '' ;

			$address = !empty($_POST['address']) ? $_POST['address'] : '' ;

			$latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : '' ;

			$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : '' ;

			$firabase_id = !empty($_POST['firebase_id']) ? $_POST['firebase_id'] : '' ;			



			// generate the verification code 

			$verify_code = $this->verify_code(); // must send on mobile for mobile number validation

			// email verification token send 

			$email_verify_token = $this->email_verify_token();

			$ids_typeindustrial = explode(',',$industrytype_id);

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

				'industrytype_id'=>isset($ids_typeindustrial[0])?$ids_typeindustrial[0]:$industrytype_id,

				'country_code'=>$country_code,

				'verification_code'=>$verify_code,

				'email_verify_token'=>$email_verify_token,

				'about_us'=>$about_us,

				'address'=>$address,

				'latitude'=>$latitude,

				'longitude'=>$longitude,

				'create_date'=>$this->dateformat,

				'update_date'=>$this->dateformat,

				'firebase_id'=>$firabase_id,

				'is_blocked' => ($user_type==1) ? 2 : 0

			);			

			$user_id = $this->BaseModel->insertData($this->tableNameUser,$save_data);

			

			for($i=0;$i<count($ids_typeindustrial);$i++){

				$data_insert = array("id"=>null,"industrytype_id"=>$ids_typeindustrial[$i],"user_id"=>$user_id);

				$this->BaseModel->insertData("users_industrytype",$data_insert);

			}



			if($user_id>0){				

				unset($save_data["showpass"]); 

				unset($save_data["password"]); 

				$response_data=$save_data;

				// create request token or this user 

				$request_key = $this->generate_request_key($user_id,$is_new=1);

				// user info saved successfully

				// send sms with verification code 

				$this->sendsms($phone_no,$verify_code);

				// send the email verify link 

				$this->send_email_verify_link($email,$email_verify_token,$verify_code);

				// get user details

				$datatemp=$this->userdetails($user_id);

				$response_data['user_id'] = $datatemp['user_id'];

				$response_data['image'] = $datatemp['image'];

				$response_data['industrytype_name'] = $datatemp['industrytype_name'];				

				$response_data['user_request_key'] = $request_key;	

				//$response_data['array_temps'] = $datatemp;			

				// test response detail

				$this->response_status=1;

				$this->response_message= ($language_id==1) ? 'Thank you for Registering. For your security, we have sent a code to your mobile phone and to your email to verify your account' : 'Gracias por registrarte. Por tu seguridad, hemos enviado un código a tu correo electrónico y celular para verificar tu cuenta';

				// create user count table 

				$save_count_data=array(

					'user_id'=>$user_id,

					'create_date'=>$this->dateformat,

					'update_date'=>$this->dateformat,

				);

				$this->BaseModel->insertData($this->tableNameUserCount,$save_count_data);

			}

			else{

				$this->response_message= ($language_id==1) ? 'Error processing the data. Try again.' : 'Hubo un error al procesar la data ingresada. Vuelva a intentarlo';

			}

		}

		else{

			$error = validation_errors();

			$this->response_message=$error;

		}

		$this->json_output_json($response_data);

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

			// sms send api integration

			$verify_code= ($this->language_id==1) ? "Your verification code is $verify_code This code is personal, do not share it" : "Su código de verificación de Bauen es el $verify_code";

			$this->twilio_send_sms($phone_no,$verify_code);

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

		$this->logged_user_id = $this->input->post('user_id');

		//$this->minimum_param_checked(1);

		if(empty($verify_code)){

			$this->response_message= ($this->language_id==1) ? 'Please enter your verification code to validate your account' : 'Por favor ingresa el código de verificación para validar tu cuenta';

			$this->json_output($response_data);

			return false;

		}

		$find_cond = array(

			'user_id'=>$this->logged_user_id,

			'verification_code'=>$verify_code, 

			//'is_blocked'=>array('0','2')

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



			$this->success_message = '¡Felicidades, tu cuenta ha sido verificada con éxito!';

			// $this->success_message = ($this->language_id==1) ? 'Your account has been successfully verified!' : '¡Felicidades, tu cuenta ha sido verificada con éxito!';

		

			$this->response_message=$this->success_message;



            //hlm



            $bodyData = $this->input->post();

		        

			//$bodyData ['firebase_id'] =  $firebase_id;

		    //$bodyData['request_id'] = $request_id;

            $this->response_body =  $bodyData;		





			$response_data = $this->userdetails($this->logged_user_id);

		}

		else{

			$this->response_message= 'El código de verificación no es válido. Por favor intenta nuevamente'; 

			// $this->response_message= ($this->language_id==1) ? 'The entered verification code is invalid. Please try again' : 'El código de verificación no es válido. Por favor intenta nuevamente'; 

		}

		//$this->json_output($user);

		$this->json_output($response_data);

	}

	

	public function resend_verify_code(){

		$response_data=array();

		$phone_no = $this->input->post('phone_no');

		$user_id = $this->input->post('user_id');

		

		if($user_id>0){

			$find_cond=array(

				'is_blocked'=>array('0','2'),

				'user_id'=>$user_id

			);

		}

		else{

			if(empty($phone_no)){

				$this->response_message="Ingrese su número de teléfono";

				$this->json_output($response_data);

			}

			else{

				// validate format 

				if(!$this->valid_phone_no($phone_no)){

					$this->response_message="Por favor, ingrese un número de teléfono válido";

					$this->json_output($response_data);

				}

				else{

					$find_cond=array(

						'is_blocked'=>array('0','2'),

						'phone_no'=>$phone_no

					);

				}

			}

		}

		

		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);

		if(!empty($user)){

			if($user['is_user_verify']){

				$this->response_message= ($this->language_id==1) ? 'This account has been already verified' : 'Esta cuenta ya ha sido verificada anteriormente';

				$this->json_output($response_data);

			}

			elseif($user['is_phone_no_verify']){

				$this->response_message="Éste número de teléfono ya ha sido verificado con nosotros";

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

				$this->response_message="Un código de verificación ha sido enviado a la dirección de correo ingresada. Abra el link para confirmar su identidad";

				$this->response_status=1;

			}

		}

		else{

			$this->response_message="No se encontraron resultados.";

		}

		$this->json_output($response_data);

	}

	

	

	public function forgotpassword(){

		$email = $this->input->post('email');

		if(empty($email)){

			$this->response_message="Para reestablecer su contraseña, ingrese la cuenta de correo que tiene registrada en Bauen. Gracias.";

			$this->json_output();

		}

		if(!filter_var($email,FILTER_VALIDATE_EMAIL)){

			$this->response_message= ($this->language_id==1) ? "Please enter a valid email address" : "Por favor, ingresa una cuenta de correo electrónico válida";

			$this->json_output();

		}

		// find section 

		$find_cond=array(

			'email'=>$email,

			'is_blocked'=>array('0','2')

		);

		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);

		if(empty($user)){

			$this->response_message= ($this->language_id==1) ? "The email you entered is not registered in Bauen, please enter a valid one" : "El correo electrónico ingresado no está registrado en Bauen, por favor ingresa uno válido";

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

		$this->response_message= ($this->language_id==1) ? "We've sent an you email with the password reset instructions" : "Te hemos enviado un correo electrónico con las instrucciones para reestablecer contraseña";

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

		$this->logpost('login');

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

				'is_blocked'=>array('0','2'),

			);			

			$select_fields=array('user_id','user_type','super_parent_id');

			$user = $this->BaseModel->getData($this->tableNameUser,$find_cond);

			

			if(!empty($user)){

				$user_id = $user['user_id'];

				// validate the user type 			

				if( ((int) $user_type) != ((int) $user['user_type']) ){

					$this->response_message="Auch! Los datos del usuario no coinciden. Contáctense con nosotros. Error: parameter user_type is incorrect user_type (sended): " . $user_type . " DB user_type: " . $user['user_type'] . ' email: ' . $user['email'];

					$this->json_output($response_data);

					return false;

				} else {

					if($user_type==1){//transporter section

						if( ((int) $is_company) != ((int) $user['is_company']) ){

							$this->response_message="Auch! Los datos del usuario no coinciden. Contáctense con nosotros. Error: Invalid user details, parameter user_type == 1 and is_company combination is incorrect. Second";

							$this->json_output($response_data);

							return false;

						}

					}

				}

				// update the hax key 

				$hax_key = $this->generate_request_key($user_id,$is_new=0);

				$super_parent_id = $user['super_parent_id'];

				$response_data=$this->userdetails($user_id,$super_parent_id);

				$response_data['user_request_key']=$hax_key;

				$response_data['super_parent_id'] = $user['super_parent_id'];

				$this->response_status=1;

				$this->success_message = ($this->language_id == 1) ? "Welcome to Bauen!" : "¡Bienvenido a Bauen!";

				$this->response_message = $this->success_message;

			}

			else{

				$this->success_message = ($this->language_id==1) ? "Incorrect e-mail or password, please try again" : "Correo electrónico o contraseña incorrecta, por favor intenta nuevamente";

				$this->response_message= $this->success_message;

			}

		}

		else{

			$erros = validation_errors();

			$this->response_message=$erros;

		}



        //$this->response_json($response_data);
		

		$this->json_output_json($response_data);

	}

	

	

	public function user_profile(){

		$response_data=array();

		$user_id = $this->input->post('user_id');

		$other_user_id = $this->input->post('other_user_id');

		$hax_key = $this->input->post('request_key');

		//$this->logpost('user_profile');

		$this->minimum_param_checked(1);

		if($other_user_id>0){

			$response_data=$this->userdetails($other_user_id);

		}

		else{

			$super_parent_id = $this->logged_user['super_parent_id'];

			$response_data=$this->userdetails($user_id,$super_parent_id);

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

			

			$response_data = $this->userdetails($user_id);

			$response_data['user_request_key']=$this->input->post('user_request_key');

			$this->response_status=1;

			$this->response_message= ($this->language_id==1) ? "Good job! Your profile has been updated successfully" : "¡Enhorabuena! Su perfil ha sido actualizado exitosamente";

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

				$this->response_message= ($this->language_id==1) ? "Image upload failed. Try again." : "La imagen no ha podido subirse. Por favor intenta nuevamente";

			}

		}

		else{

			$this->response_message= ($this->language_id==1) ? "Please insert an image file" : "Sube una imagen";

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

				'is_blocked'=>array('0','2')

			);

			$row = $this->BaseModel->tableRow($this->tableNameUser,$find_cond);

			if($row){

				$this->response_status=1;

				$this->response_message="Se encontrón el correo electrónico";

			}

			else{

				$this->response_message="Los detalles de la cuenta de correo no han sido encontrados en nuestros registros";

			}

		}

		else{

			$this->response_message="El formato de correo ingresado es inválido (Ej. usuario@ejemplo.com";

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

			$this->response_message="Lo sentimos! Su pedido no se ha podido procesar. Pongase en contacto con nosotros";

			$this->json_output($response_data);

		}

		if(empty($request_id)){

			$this->response_message="No hemos podido encontrar los detalles del pedido";

			$this->json_output($response_data);

		}

		if(empty($file)){

			$this->response_message="No ha seleccionado ningún archivo.";

			$this->json_output($response_data);

		}

		//

		$find_req=array(

			'request_id'=>$request_id,

			'is_blocked'=>array('0','2'),

			'status'=>'1',

			'worker_id'=>$user_id

		);

		$request = $this->BaseModel->getData($this->tableNameServiceRequest,$find_req);

		if(empty($request)){

			$this->response_message="Los detalles del pedido no fueron encontrados";

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

			$this->response_message="Fallo la subida de su archivo a nuestros registros. Intente nuevamente o póngase en contacto con nosotros";

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

			$this->response_message="Pedido inválido. Intente nuevamente";

			$this->json_output($response_data);

		}

		if(empty($request_id)){

			$this->response_message="Falta algún dato para procesar el pedido. Póngase en contacto con nosotros";

			$this->json_output($response_data);

		}

		if(empty($file)){

			$this->response_message="No hay seleccionado ningún archivo.";

			$this->json_output($response_data);

		}

		//

		$find_req=array(

			'request_id'=>$request_id,

			'is_blocked'=>array('0','2'),

			'status'=>'1',

			'worker_id'=>$user_id

		);

		$request = $this->BaseModel->getData($this->tableNameServiceRequest,$find_req);

		if(empty($request)){

			$this->response_message="No se encontró algun detalle para completar su solicitud";

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

				$this->response_message="No se ha podido procesar el documento ingresado";

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

			$this->response_message="No se ha podido procesar su pedido. Intente nuevamente";

		}

		else{

			// valid for transporter

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

			$this->response_message="No se ha podido procesar su pedido. Intente nuevamente";

		}

		else{

			// valid for transporter

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

		$creater_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		else{

			$user_id = $creater_id;

		}

		

		// validate the request only for transporter

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="No se ha podido validar su pedido";

		}

		else{

			$this->load->library(array('form_validation'));

			$rules=array(

				array(

					'field'=>'trailer_id',

					'label'=>'Trailer',

					'rules'=>'trim|required|greater_than[0]',

					'errors'=>array(

						'greater_than'=>($this->language_id==1) ? 'Vehicle type is required' : 'Por favor, ingrese el tipo de vehículo'

					)

				),

				array(

					'field'=>'plate_no',

					'label'=>'Plate No.',

					'rules'=>'trim|required|callback_unique_plate_no',

					'errors'=>array(

						'unique_plate_no'=> ($this->language_id==1) ? 'Please insert a Plate Number' : 'Por favor, ingrese el número de placa del vehículo'

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

				$vehicle_color = $this->input->post('vehicle_color');

				if(empty($vehicle_color)){

					$vehicle_color=DEFAULT_TRUCK_COLOR;// hex color code

				}

				$save_data=array(

					'user_id'=>$user_id,

					'trailer_id'=>$this->input->post('trailer_id'),

					'truck_brand'=>$this->input->post('truck_brand'),

					'truck_model'=>$this->input->post('truck_model'),

					'plate_no'=>$this->input->post('plate_no'),

					'purchase_year'=>$this->input->post('purchase_year'),

					'vehicle_minload'=>$this->input->post('vehicle_minload'),

					'vehicle_maxload'=>$this->input->post('vehicle_maxload'),

					'vehicle_color'=>$vehicle_color,

					'creater_id'=>$creater_id,

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

										'creater_id'=>$creater_id,

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

					$this->response_message="No se ha podido registrar el vehiculo. Intente nuevamente";

				}

			}

			else{

				$errors = validation_errors();

				$this->response_message=$errors;

			}

		}

		$this->json_output($response_data);

	}


	public function add_vehicle_v1(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$creater_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		else{

			$user_id = $creater_id;

		}

		

		// validate the request only for transporter

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="No se ha podido validar su pedido";

		}

		else{

			$this->load->library(array('form_validation'));

			$rules=array(

				array(

					'field'=>'trailer_id',

					'label'=>'Trailer',

					'rules'=>'trim|required|greater_than[0]',

					'errors'=>array(

						'greater_than'=>($this->language_id==1) ? 'Vehicle type is required' : 'Por favor, ingrese el tipo de vehículo'

					)

				),

				array(

					'field'=>'plate_no',

					'label'=>'Plate No.',

					'rules'=>'trim|required|callback_unique_plate_no',

					'errors'=>array(

						'unique_plate_no'=> ($this->language_id==1) ? 'Please insert a Plate Number' : 'Por favor, ingrese el número de placa del vehículo'

					)

				),

				array(

					'field'=>'plate_trailer',

					'label'=>'Plate Trailer',

					'rules'=>'trim|required',

					'errors'=>array(

						'unique_plate_no'=> ($this->language_id==1) ? 'Please insert a Plate Trailer' : 'Por favor, ingrese el número de placa del trailer'

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

				$vehicle_color = $this->input->post('vehicle_color');

				if(empty($vehicle_color)){

					$vehicle_color=DEFAULT_TRUCK_COLOR;// hex color code

				}

				$save_data=array(

					'user_id'=>$user_id,

					'trailer_id'=>$this->input->post('trailer_id'),

					'truck_brand'=>$this->input->post('truck_brand'),

					'truck_model'=>$this->input->post('truck_model'),

					'plate_no'=>$this->input->post('plate_no'),

					'plate_trailer'=>$this->input->post('plate_trailer'),

					'purchase_year'=>$this->input->post('purchase_year'),

					'vehicle_minload'=>$this->input->post('vehicle_minload'),

					'vehicle_maxload'=>$this->input->post('vehicle_maxload'),

					'vehicle_color'=>$vehicle_color,

					'creater_id'=>$creater_id,

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

										'creater_id'=>$creater_id,

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

					$this->response_message="Vehículo registrado exitosamente";

				}

				else{

					$this->response_message="No se ha podido registrar el vehiculo. Intente nuevamente";

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

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		

		// validate the request only for transporter

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="No se ha podido procesar su pedido. Intente nuevamente";

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

					$vehicle_color = $this->input->post('vehicle_color');

					if(empty($vehicle_color)){

						$vehicle_color=DEFAULT_TRUCK_COLOR;// hex color code

					}

					$save_data=array(

						'trailer_id'=>$this->input->post('trailer_id'),

						'truck_model'=>$this->input->post('truck_model'),

						'truck_brand'=>$this->input->post('truck_brand'),

						'plate_no'=>$this->input->post('plate_no'),

						'purchase_year'=>$this->input->post('purchase_year'),

						'vehicle_minload'=>$this->input->post('vehicle_minload'),

						'vehicle_maxload'=>$this->input->post('vehicle_maxload'),

						'vehicle_color'=>$vehicle_color,

						'update_date'=>$this->dateformat,

					);

					$this->BaseModel->updateDatas($this->tableNameVehicle,$save_data,$find_vehicle);

					$this->response_status=1;



					$this->success_message = ($this->language_id) ? "Vehicle details updated successfully!" : "¡Los detalles del vehículo han sido actualizados correctamente! ";

					$this->response_message= $this->success_message;

				}

				else{

					$this->response_message="No se han encontrado los datos del vehículo. Intente nuevamente";

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

			$this->response_message="No se ha podido procesar su pedido. Intente nuevamente";

		}

		else{

			$creater_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			else{

				$user_id = $creater_id;

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

					'rules'=>'trim|required|valid_email|callback_user_unique_email',

					'errors'=>array(

						'user_unique_email'=>($this->language_id==1) ? 'This e-mail address  is already registered' : 'Este correo electrónico ya está registrado'

					)

				),

				array(

					'field'=>'phone_no',

					'label'=>'Phone No.',

					'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',

					'errors'=>array(

						'valid_phone_no'=>'Enter valid %s',

						'user_unique_phone_no'=> ($this->language_id==1) ? 'This phone number already is already registered' : 'El número de teléfono ingresado ya está registrado'

					)

				),

				array(

					'field'=>'password',

					'label'=>'Password',

					'rules'=>'trim|required',

					'errors'=>array()

				)

			);

			$this->form_validation->set_rules($rules);

			$this->form_validation->set_error_delimiters('','');

			if($this->form_validation->run()===true){

				$verification_code = $this->verify_code();

				$password = $this->input->post('password');

				$ruc_no = $this->input->post('ruc_no');

				$dni_no = $this->input->post('dni_no');

				$licence_no = $this->input->post('licence_no');

				$last_name = $this->input->post('last_name');

				if(empty($ruc_no)){

					$ruc_no='';

				}

				if(empty($dni_no)){

					$dni_no='';

				}

				if(empty($licence_no)){

					$licence_no='';

				}

				if(empty($last_name)){

					$last_name='';

				}

				

				$save_data=array(

					'parent_user_id'=>$user_id,

					'user_type'=>'1',

					'is_company'=>'0',

					'verification_code'=>$verification_code,

					'is_phone_no_verify'=>'1',

					'is_user_verify'=>'1',

					'ruc_no'=>$ruc_no,

					'dni_no'=>$dni_no,

					'licence_no'=>$licence_no,

					'phone_no'=>$this->input->post('phone_no'),

					'email'=>$this->input->post('email'),

					'last_name'=>$last_name,

					'first_name'=>$this->input->post('first_name'),

					'password'=>md5($password),

					'showpass'=>$password,

					'creater_id'=>$creater_id,

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

					$this->response_message="¡Lo sentimos! Los detalles del conductor no se han podido registrar";

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

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

				array(

					'field'=>'phone_no',

					'label'=>'Phone No.',

					'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no['.$driver_id.']',

					'errors'=>array(

						'valid_phone_no'=>'Enter valid %s',

						'user_unique_phone_no'=> ($this->language_id==1) ? 'This phone number already is already registered' : 'El número de teléfono ingresado ya está registrado'

					)

				),

				/*array(

					'field'=>'email',

					'label'=>'Email',

					'rules'=>'trim|required|valid_email|callback_user_unique_email['.$driver_id.']',

					'errors'=>array(

						'user_unique_email'=> ($this->language_id==1) ? 'This e-mail address  is already registered' : 'Este correo electrónico ya está registrado';

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

				),*/

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

					$ruc_no = $this->input->post('ruc_no');

					$dni_no = $this->input->post('dni_no');

					$licence_no = $this->input->post('licence_no');

					$last_name = $this->input->post('last_name');

					if(empty($ruc_no)){

						$ruc_no='';

					}

					if(empty($dni_no)){

						$dni_no='';

					}

					if(empty($licence_no)){

						$licence_no='';

					}

					if(empty($last_name)){

						$last_name='';

					}

					$save_data=array(

						'ruc_no'=>$ruc_no,

						'dni_no'=>$dni_no,

						'licence_no'=>$licence_no,

						'phone_no'=>$this->input->post('phone_no'),

						'last_name'=>$last_name,

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

					$this->success_message = ($this->language_id == 1) ? 'Driver details updated successfully' : 'Se han actualizado los datos del conductor satisfactoriamente';

					$this->response_message= $this->success_message;

				}

				else{

					$this->response_message="No se han encontrado los datos del conductor en nuestros registros";

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

			$this->response_message="Orden incorrecta";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

			$vehicle_id = $this->input->post('vehicle_id');

			if(empty($vehicle_id)){

				$this->response_message="No hemos podido procesar su solicitud en este momento (the vehicle field is required)";

				$this->json_output($response_data);

			}

			$find_vehicle = array(

				'user_id'=>$user_id,

				'vehicle_id'=>$vehicle_id

			);

			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle);

			if(empty($vehicle)){

				$this->response_message="Información del vehículo inválida";

				$this->json_output($response_data);

			}

			// now delete the vehicle 

			$this->BaseModel->removeDatas($this->tableNameVehicle,$find_vehicle);

			$this->response_status=1;

			$this->response_message= ($this->language_id==1) ? "The vehicle has been removed from the app successfully" : "Se ha eliminado el vehículo exitosamente";

		}

		$this->json_output($response_data);

	}

	

	public function delete_driver(){

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for transporter

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

			$driver_id = $this->input->post('driver_id');

			if(empty($driver_id)){

				$this->response_message="Hay algun dato que se requiere (driver_id) para completar la solicitud";

				$this->json_output($response_data);

			}

			$find_driver = array(

				'super_parent_id'=>'0',

				'parent_user_id'=>$user_id,

				'user_id'=>$driver_id,

				'user_type'=>'1',

				'is_company'=>'0'

			);

			$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver);

			if(empty($driver)){

				$this->response_message="Información del conductor inválida";

				$this->json_output($response_data);

			}

			// delete the driver 

			$this->BaseModel->removeDatas($this->tableNameUser,$find_driver);

			$this->response_message= ($this->language_id==1) ? "Driver deleted successfully" : "El conductor se ha eliminado del app exitosamente";

			$this->response_status=1;

		}

		$this->json_output($response_data);

	}

	

	public function add_vehicle_image(){

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for transporter

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message=($this->language_id==1) ?  "Please contact customer support" : "Por favor, contacte atención al cliente";

		}

		else{

			$creater_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			else{

				$user_id = $creater_id;

			}

			

			$vehicle_id = $this->input->post('vehicle_id');

			$image = array();

			if(empty($vehicle_id)){

				$this->response_message="El campo Vehículo es requerido.";

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

							'creater_id'=>$creater_id,

							'create_date'=>$this->dateformat,

							'update_date'=>$this->dateformat,

						);

						$vehicle_image_id = $this->BaseModel->insertData($this->tableNameVehicleImage,$save_data);

						if($vehicle_image_id>0){

							$this->response_message="La imagen ha sido súbida con éxito";

							$this->response_status=1;

							$response_data=array(

								'vehicle_image_id'=>$vehicle_image_id,

								'image_file'=>base_url('uploads/vehicles/'.$file_name)

							);

						}

						else{

							$this->response_message=($this->language_id==1) ? "Vehicle image was not uploaded. Please try again":"Lo sentimos! No se ha podido subir la imagen. Por favor intente nuevamente";

						}

					}

					else{

						$this->response_message=($this->language_id==1) ? "Vehicle image was not uploaded. Please try again":"Lo sentimos! No se ha podido subir la imagen. Por favor intente nuevamente";

					}

				}

			}

			else{

				$this->response_message="Se requiere subir la imagen para completar la solicitud";

			}

		}

		$this->json_output($response_data);

	}

	

	public function vehicle_documents(){

		$response_data=array();

		$this->minimum_param_checked(1);

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

			$this->response_message="No se ha podido procesar su solicitud";

		}

		else{

			$creater_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			else{

				$user_id = $creater_id;

			}

			

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

							'creater_id'=>$creater_id,

							'create_date'=>$this->dateformat,

							'update_date'=>$this->dateformat,

						);

						$vehicle_document_id = $this->BaseModel->insertData($this->tableNameVehicleDocument,$save_data);

						if($vehicle_document_id>0){

							$this->response_message="Gracias! El documento ha sido registrado correctamente";

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

							$this->response_message="No se ha podido grabar los detalles de los vehiculos. Vuelva a intentarlo";

						}

					}

					else{

						$this->response_message="No se han podido subir los documentos del vehiculo";

					}

				}

				else{

					$this->response_message="No hemos podido encontrar los detalles del vehículo";

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

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

					$this->response_message="El documento del vehículo ha sido eliminado correctamente";

					$this->response_status=1;

				}

				else{

					$this->response_message="No se ha podido eliminar el documento seleccionado";

				}

			}

			else{

				$this->response_message="No se han encontrado los detalles del documento";

			}

		}

		$this->json_output($response_data);

	}

	

	// #MARK: Place bid (actual one working): AND WORKS AS UPDATE AND INSERT MTFCK

	public function placebid() {

		$idsToNotify = array();

		$asociados = '';



		$response_data = array();

		

		$this->minimum_param_checked(1);



		$this->logpost('placebid');

		// validate the request only for transporter

		if($this->logged_user['user_type'] != 1 || $this->logged_user['is_company'] == 0) {

			$this->response_message = "Invalid request";

		} else {

			$creater_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id > 0) {

				$user_id = $super_parent_id;

			} else {

				$user_id = $creater_id;

				$asociados = $this->BaseModel->getDatas($this->tableNameUser, array('super_parent_id' => $user_id, 'is_blocked' => 0, 'is_deleted' => 0), array('user_id')); 



				if($asociados) {

					foreach ($asociados as $key => $asociado) {

						array_push($idsToNotify, $asociado['user_id']);

					}

				}

			}

			

			$this->load->library(array('form_validation'));

			$rules = array(

				array(

					'field' => 'request_id',

					'label' => 'Request',

					'rules' => 'trim|required|greater_than[0]',

					'errors' => array( 'greater_than' => 'The %s field is required.' )

				),

				array(

					'field' => 'bid_amount',

					'label' => 'Bid Amount',

					'rules' => 'trim|required|greater_than[0]',

					'errors' => array( 'greater_than' => 'The %s field is required.' )

				),

			);



			$this->form_validation->set_rules($rules);

			$this->form_validation->set_error_delimiters('', '');

			

			if($this->form_validation->run() === true) { // see if everything is conforming to form restrictions

				$request_id = $this->input->post('request_id');

				$bid_amount = $this->input->post('bid_amount');

				$bid_comment = $this->input->post('bid_comment');



				// validate request 

				$find_request = array( 'request_id' => $request_id );

				$joins = array(

					array(

						'table_name' => $this->tableNameRequestBid,

						'join_type' => 'left',

						'join_with' => $this->tableNameRequest,

						'join_on' => array('request_id'=>'request_id'),

						'oncond' => array('user_id'=>$user_id,'is_deleted'=>'0','is_blocked'=>array('0','2')),

						'conditions' => array(),

						'select_fields' => array('bid_id','bid_status'),

					)

				);



				$request = $this->BaseModel->getData($this->tableNameRequest, $find_request, array(), array(), $joins);

				if(empty($request)) {

					$this->response_message = "Request details not found.";

					$this->json_output($response_data);

				}

				$request_status = $request['request_status'];

				

				if(!in_array($request_status, array('0', '1', '4'))) {

					$this->response_message = "¡Demasiado tarde! Ya no puedes enviar tu cotización. El cliente ya ha seleccionado a alguien";

					$this->json_output($response_data);

				}



				if(empty($bid_comment)) {

					$bid_comment = '';

				}

				

				if(empty($request['bid_id'])) {

					// create new bid 

					$save_data = array(

						'user_id' => $user_id,

						'request_id' => $request_id,

						'bid_amount' => $bid_amount,

						'bid_comment' => $bid_comment,

						'creater_id' => $creater_id,

						'create_date' => $this->dateformat,

						'update_date' => $this->dateformat,

					);



					$bid_id = $this->BaseModel->insertData($this->tableNameRequestBid,$save_data);

					

					if($bid_id > 0) {

						switch ($this->input->post('language_id')) {

							case 1:

								$this->push_message = "You received a bid";

								break;

							case 2:

								$this->push_message = "Haz recibido una cotización";

							default:

								$this->push_message = "Haz recibido una cotización";

								break;

						}



						//Primer push de placebid

						$this->debug(array('placebid'));

						$this->debug(array('first bid' => true, 'user_id' => $request['user_id'], 'push_message' => $this->push_message, 'rquest_id' => $request_id));

						$this->customer_push($request['user_id'], $this->push_message, 1, $request_id);



						if(count($idsToNotify) > 0) {

							foreach($idsToNotify as $asociado_id) {

								$this->customer_push($asociado_id, $this->push_message, 1, $request_id);

							}

						}



						$this->response_status = 1;

						//$this->response_message="¡Excelente! Su cotización ha sido enviada con éxito al cliente. Puede editarla en Mis Propuestas";

						$this->response_message = "Bien! Su cotización ha sido enviada con éxito. Puede editarla en Mis Propuestas";

						// $this->response_message= $request['user_id'];

						//update the status of the bid 

						if($request_status == 0) {

							$update_data = array(

								'request_status' => 1,

								'update_date' => $this->dateformat

							);

							// track the request status 

							$request_status_track = json_decode($request['request_status_track']);

							$request_status_track[] = array(

								'request_status' => $update_data['request_status'],

								'create_date' => $update_data['update_date']

							);

							$update_data['request_status_track'] = json_encode($request_status_track);

							$this->BaseModel->updateDatas($this->tableNameRequest, $update_data, $find_request);

						}

						

						// create a notification 

						$notification_data=array(

							'request_id'=>$request_id,

							'user_id'=>$user_id,

							'receiver_user_id'=>$request['user_id'],

							'notification_type'=>'1',

							'amount'=>$bid_amount

						);

						//#ED

						$this->debug(array('placebid'));

						$this->debug(array('add notification' => '2546', $notification_data));



						$this->add_notification($notification_data, $is_return = 1);

					} else {

						$this->response_message = "Su cotización no pudo ser enviada. Intente nuevamente";

					}

				} else {

					// only bid in status 0 which means not taken?

					if($request['bid_status'] == 0 ) {

						$find_bid=array( 'user_id' => $user_id, 'request_id' => $request_id, 'bid_id' => $request['bid_id'] );



						$save_data=array( 'bid_amount' => $bid_amount, 'bid_comment' => $bid_comment, 'update_date' => $this->dateformat, );



						$this->BaseModel->updateDatas($this->tableNameRequestBid, $save_data, $find_bid);



						switch ($this->input->post('language_id')) {

							case 1:

								$this->push_message = "A bid has been updated";

								break;

							case 2:

								$this->push_message = "Se ha actualizado una cotización";

							default:

								$this->push_message = "Se ha actualizado una cotización";

								break;

						}

						// Segundo push de placebid

						$this->debug(array('placebid'));

						$this->debug(array('second push' => '2573', 'user_id' => $request['user_id'], 'push_message' => $this->push_message, 'rquest_id' => $request_id));

						$this->customer_push($request['user_id'], $this->push_message, 1, $request_id);

						

						$this->response_status = 1;

						$this->response_message="El monto de su cotización ha sido actualizado exitosamente. Muchas gracias.";

						// $this->response_message = $request['user_id'];

						// create a notification for update bid amount

						$notification_data=array(

							'request_id'=>$request_id,

							'user_id'=>$user_id,

							'receiver_user_id'=>$request['user_id'],

							'notification_type'=>'17',

							'amount'=>$bid_amount

						);



						$this->debug(array('add notification 2' => '2588', $notification_data));

						$this->add_notification($notification_data, $is_return = 1);

					} else {

						$this->response_message= "¡Lo sentimos! No se ha podido modificar el precio de la cotización";

					}

				}

			} else {

				$this->response_message = validation_errors();

			}

		}

		//$this->logpost("placebid");

		$this->json_output($response_data);

	}

	



	public function confirm_cancel_bid(){

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for transporter

		$this->logpost('confirm_cancel_bid');

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$user_id_conf = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

				/*$find_request = array(

					'request_id'=>$request_id,

					'bid_id'=>$bid_id,

					'request_status'=>'2', // accept by customer

				);

				$joins=array(

					array(

						'table_name'=>$this->tableNameRequestBid,

						'join_with'=>$this->tableNameRequest,

						'join_type'=>'inner',

						'join_on'=>array('bid_id'=>'bid_id'),

						'oncond'=>array('bid_status'=>'1','user_id'=>$user_id,'is_blocked'=>array('0','2'),'is_deleted'=>'0'),

						'select_fields'=>array('bid_amount')

					)

				);

				$request = $this->BaseModel->getData($this->tableNameRequest,$find_request,array(),array(),$joins);

				*/

				$Query = "select r.*,rb.bid_amount from trns_".$this->tableNameRequestBid." rb inner join trns_".$this->tableNameRequest." r on r.request_id = rb.request_id where rb.user_id =".$user_id." and r.request_status = 2 and r.is_deleted=0 and r.is_blocked in (0,2) and rb.is_blocked in (0,2) and rb.is_deleted=0 and r.request_id =".$request_id." and rb.bid_id=".$bid_id;

				

				$request = $this->BaseModel->queryResult($Query);

				/*echo '<pre>';

				print_r($request);

				echo '</pre>';*/

				if(empty($request)){

					$this->response_message="Orden no encontrada.";

					$this->json_output($response_data);

				}

				// track the request status 

				//$request_status_track = json_decode($request['request_status_track']);

				// update the bid table

				$find_bid=array(

					'request_id'=>$request_id,

					'bid_id'=>$bid_id,

					'user_id'=>$user_id,

					'bid_status'=>'1', // customer confirm

				);

				$update_data=array(

					'update_date'=>$this->dateformat,

					'bid_status'=>$bid_status,

					'cancel_comment'=>$cancel_comment,

				);

				$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$find_bid);

				

				if($bid_status==2){

					// tranporti accept the bid

					// bid confirmation on request 

					$trans_bid_amount=$request[0]->bid_amount;

					//hlm

					$update_req=array(

						'transporter_id'=>$user_id,

						'request_status'=>'3',// accepted by transporter

						'granted_amount'=>$trans_bid_amount,

						'update_date'=>$this->dateformat,

						'ta_date'=>$this->dateformat,

					);

					// lost all other bid of this request 

					$update_bids=array(

						'request_id'=>$request_id,

						'bid_status'=>'0',

						'bid_id !='=>$bid_id

					);

					$update_data=array(

						'bid_status'=>'4', //lost

						'update_date'=>$this->dateformat,

					);

					$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$update_bids);

					$this->response_message="¡Listo! Se ha confirmado que acepta la propuesta con éxito";

					$notification_type='3';

				}

				else{

					// transporti reject the bid

					$update_req=array(

						'request_status'=>'4',// cancelled by transporter

						'update_date'=>$this->dateformat,

					);



					switch ($this->language_id) {

						case 1:

							$this->success_message = "La propuesta ha sido cancelada.";

							break;

						case 2:

							$this->success_message = "Se canceló con éxito.";

						default:

							$this->success_message = "Se canceló con éxito.";

							break;

					}

					$this->response_message=$this->success_message;

					$notification_type='4';

				}

				

				// update the request acording the bid action 

				$request_status_track[]=array(

					'request_status'=>$update_req['request_status'],

					'create_date'=>$update_req['update_date']

				);

				$update_req['request_status_track']=json_encode($request_status_track);
				$update_req['user_id_conf']=$user_id_conf;

				

				$find_request = array(

					'request_id'=>$request_id,

					'bid_id'=>$bid_id,

					'request_status'=>'2', // accept by customer

				);

				$this->BaseModel->updateDatas($this->tableNameRequest,$update_req,$find_request);

				$this->response_status=1;

				// create a notification 

				$notification_data=array(

					'request_id'=>$request_id,

					'user_id'=>$user_id,

					'receiver_user_id'=>$request[0]->user_id,

					'notification_type'=>$notification_type,

				);

				$this->add_notification($notification_data,$is_return=1);

			}

			else{

				$this->response_message = validation_errors();

			}

		}

		$this->logpost('confirm_cancel_bid');

		$this->json_output($response_data);

	}
	

	public function assingdriver(){

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for transporter

		$this->logpost('assingdriver');

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

					'request_id' => $request_id,

					'transporter_id' => $user_id,

					'request_status'=>'3',

					'bid_id >' => '0'

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

						$find_customer=array(

							'user_id'=>$request['creater_id'],

							'user_type'=>'0'

						);


						$select_fields=array();


						$find_transporter=array(

							'user_id'=>$request['transporter_id'],

							'user_type'=>'1'

						);

						//verifica si inserto usuario confirmacion transporte
						

						if($request['user_id_conf'] == 0){

							$user_id_conf = $request['transporter_id'];

						} else {

							$user_id_conf = $request['user_id_conf'];

						}

						$find_transporter_ta=array(

							'user_id'=>$user_id_conf,

							'user_type'=>'1'

						);


						$find_transporter_super=array(

							'user_id'=>$user_id,

							'user_type'=>'1',

						);


						$select_fields=array();


						$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver,$select_fields);

						$customer = $this->BaseModel->getData($this->tableNameUser,$find_customer,$select_fields);

						$transporter = $this->BaseModel->getData($this->tableNameUser,$find_transporter,$select_fields);

						$transporter_accept = $this->BaseModel->getData($this->tableNameUser,$find_transporter_ta,$select_fields);

						$transporter_super = $this->BaseModel->getData($this->tableNameUser,$find_transporter_super,$select_fields);


						if(!empty($driver)){

							if(!$driver['is_blocked']){

								// validate driver not in transit mode 

								//hlm

								/* if($driver['user_status']==3){

									$this->response_message="El conductor seleccionado se encuentra activo en otro pedido";

									$this->json_output($response_data);

								} */

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

										/* if($vehicle['vehicle_status']==3){

											$this->response_message=($this->language_id==1) ? "The vehicle is currently not available or in transit":"El vehículo seleccionado actualmente no está disponible o se encuentra en tránsito";

											$this->json_output($response_data);

										}*/

										

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



										$this->success_message = ($this->language_id==1) ? "Driver & vehicle were assigned successfully!" : "El conductor y el camión han sido asignados al servicio satisfactoriamente!";



										$this->response_message= $this->success_message;

										// notification section

										// create a notification for customer

										$notification_data=array(

											'request_id'=>$request_id,

											'user_id'=>$user_id,

											'receiver_user_id'=>$request['user_id'],

											'notification_type'=>'5'

										);

										$this->add_notification($notification_data,$is_return=1);



										$find_trailer=array(

											'trailer_id'=>$request['trailer_id'],

										);

										$select_fields=array();

										$trailer = $this->BaseModel->getData($this->tableNameTrailer,$find_trailer,$select_fields);


										$find_loadtype=array(

											'loadtype_id'=>$request['loadtype_id'],

										);

										$select_fields=array();

										$loadtype = $this->BaseModel->getData($this->tableNameLoadType,$find_loadtype,$select_fields);

										$select_fields=array();


										$find_requestBid=array(

											'request_id'=>$request['request_id'],

										);

										$requestBid = $this->BaseModel->getData($this->tableNameRequestBid,$find_requestBid,$select_fields);


										//send mail customer


										$email_data=array(

											'request_id'=>$request_id,

											'hora_recojo'=>$request['pickup_date'],

											'hora_recojo_2'=>$request['pickup_time'],

											'conductor'=>$transporter_accept['first_name'].' '.$transporter_accept['last_name'],

											'company_name'=>$transporter['company_name'],

											'conductor_dni'=>$driver['dni_no'],

											'licencia'=>$driver['licence_no'],

											'placa_tracto'=>$vehicle['plate_no'],

											'placa_trailer'=>$vehicle['plate_trailer'],

											'tipo_trailer'=>$trailer['name'],

											'tipo_carga'=>$loadtype['load_name'],

											'name_customer'=>$customer['first_name'].' '.$customer['last_name'],

											'cel_transport'=>$transporter_super['phone_no'],

											'amount'=>$request['granted_amount'],

											'description'=>$request['description']

										);


										$this->sendemail(7,$transporter_accept['email'],$email_data);


										// create a notification for driver

										$notification_data=array(

											'request_id'=>$request_id,

											'user_id'=>$user_id,

											'receiver_user_id'=>$driver_id,

											'conductor'=>$transporter['first_name'].' '.$transporter['last_name'],

											'notification_type'=>'6'

										);

										$this->add_notification($notification_data,$is_return=1);


										$this->sendemail(8,$customer['email'],$email_data);


									}

									else{

										$this->response_message= ($this->language_id==1) ? "This vehicle is currently blocked by Bauen":"Este vehículo actualmente se encuentra bloqueado por Bauen";

									}

								}

								else{

									$this->response_message="No se han encontrado los datos del vehículo";

								}

							}

							else{

								$this->response_message= ($this->language_id==1) ? "Contact Bauen to activate this driver's account. Thank you" : "Pongase en contacto con Bauen para activar la cuenta de este conductor. Gracias";

							}

						}

						else{

							$this->response_message= ($this->language_id==1) ? "Driver details not found. Please contact us": "No se han encontrado los detalles de la cuenta del conductor. Por favor contáctanos";

						}

					}

					else{

						$this->response_message="Ya se ha asignado Conductor y Vehiculo a este pedido anteriormente";

					}

				}

				else{

					$this->response_message="Información de orden incorrecta.";

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

		$this->logpost('my_bids');

		// validate the request only for transporter

		if($this->logged_user['user_type'] !=1 || $this->logged_user['is_company']==0){

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

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

				'is_blocked'=>array('0','2'),

				'is_deleted'=>0

			);

			$find_request=array(

				'is_blocked'=>array('0','2')

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

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			

			$bid_id = $this->input->post("bid_id");

			if(empty($bid_id)){

				$this->response_message="No se ha podido completar el pedido. Falta el dato bid_id para completar la operación. Póngase en contacto con nosotros";

				$this->json_output($response_data);

			}

			$find_bid=array(

				'bid_id'=>$bid_id,

				'user_id'=>$user_id,

			);

			$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_bid);

			if(empty($bid)){

				$this->response_message="Propuesta no encontrada.";

				$this->json_output($response_data);

			}

			if(in_array($bid['bid_status'],array('1','2'))){

				$this->response_message=($this->language_id==1) ? "You cannot delete this bid, it has already been accepted by the customer":"No puede eliminar esta cotización, el cliente ya la ha aceptado";

				$this->json_output($response_data);

			}

			//now remove the bid 

			$this->BaseModel->removeDatas($this->tableNameRequestBid,$find_bid);

			$this->response_message= ($this->language_id==1) ? "No problem. The bid was successfully deleted" : "No hay problema. La cotización fue eliminada con éxito";

			$this->response_status=1;

		}

		//$this->logpost("delete_bid");

		$this->json_output($response_data);

	}

	
	public function find_image_phone() {
		$response_data=array();

		$email = $this->input->post('email');
		$request = $this->BaseModel->getData($this->tableNameUser, array('email' => $email)); 
		
		if(empty($request)){
			$this->response_status = -1;
			$response_data['body']['message']= "User not found.";
		} else {
			$this->response_status = 1;
			$base_path = "http://3.137.91.53/uploads/users/";
			$response_data['body']['image'] = ($request['image'] == '') ? null : $base_path . $request['image'];
			$response_data['body']['phone_no'] = $request['phone_no'];
			$response_data['body']['message'] = "Success";
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

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$response_data['request_status']=$this->getdriverchangestatus();

			$this->response_message="Driver allowed request status";

			$this->response_status=1;

		}

		$this->json_output($response_data);

	}

	

	// #MARK: Like why dude

	public function update_request_status() {

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for driver

		if($this->logged_user['user_type'] !=1) { //|| $this->logged_user['is_company']==1

			$this->response_message="Invalid request";

		} else {

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			$is_driver = 1;

			

			$request_id = $this->input->post('request_id');

			$request_status = $this->input->post('request_status');

			

			// validte the data 

			if(empty($request_id)) {

				$this->response_message="El campo Orden es requerido.";

				$this->json_output($response_data);

			}



			//validate the driver valied request status 

			$driver_allowed = $this->getdriverchangestatus();

			if(!array_key_exists($request_status,$driver_allowed)) {

				$this->response_message="Estado de orden incorrecto.";

				$this->json_output($response_data);

			}



			// request details validate 

			$find_request=array(

				'request_id'=>$request_id,

				'request_status <'=>$request_status,

			);



			$joins=array();

			if($this->logged_user['is_company']) {

				$is_driver = 0;

				if($super_parent_id > 0) {

					$user_id = $super_parent_id;

				}

				$find_request['transporter_id'] = $user_id;

				// get the driver status

				$joins=array(

					array(

						'table_name' => $this->tableNameUser,

						'join_with' => $this->tableNameRequest,

						'join_type' => 'inner',

						'join_on' => array('driver_id'=>'user_id'),

						'oncond' => array('parent_user_id' => $user_id),

						'select_fields' => array('user_status')

					)

				);

			} else {

				$find_request['driver_id'] = $user_id;

			}

			

			$request = $this->BaseModel->getData($this->tableNameRequest, $find_request,array(), array(), $joins);

			if(empty($request)) {

				$this->response_message = "Orden no encontrada.";

				$this->json_output($response_data);

			}

			

			// get details from the request

			$vehicle_id = $request['vehicle_id'];

			$driver_id = $request['driver_id'];

			

			//update the request status

			$update_data = array(

				'update_date'=>$this->dateformat,

				'request_status'=>$request_status

			);

			

			if($request_status==REQUEST_COMPLED_STATUS){ // completed the request

				// Track points
				$find_track_request = array(
					'request_id' => $request_id
				);

				$track_request = $this->BaseModel->getDatas('request_driver_locations', $find_track_request);

				$update_data['completed_date']=$this->dateformat;

				// Trailer image
				$trailer_image = $this->BaseModel->getData('trailers', array('trailer_id' => $request['trailer_id']));


				// need to creaate the image

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

				if(is_array($track_request) && count($track_request) > 0) {
	
					foreach($track_request as $track){
						$track_data = array(
							'lat'=>$track['latitude'],
							'long'=>$track['longitude']
						);

						if(!empty($trailer_image)) {
							$track_data['icon'] = 'http://3.137.91.53/uploads/trailers/' . $trailer_image['image_icon'];
						} else {
							$track_data['place'] = 'P';
						}

						$locations['markers'][] = $track_data;
					}
				}

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

			//hlm

			if($update_data['request_status'] == 6){

				$update_data['it_date'] = $update_data['update_date'];
			}

			

			$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);

			

			//now update the driver status 

			$update_driver_cond=array(

				'user_id'=>$driver_id,

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

				if($is_driver){

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

				else{

					if($request['user_status']!=3){ // in transit mode

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

			}



			$this->success_message = ($this->language_id==1) ? "Order status updated successfully" :  "El status de la orden ha sido actualizado exitosamente";

			$this->response_message= $this->success_message;

			$this->response_status=1;

			// create a notification

			$notification_type = $request_status;
			
			if($request_status=='6'){//trip start

				$notification_type='7';

			}elseif($request_status=='7'){//cargando

				$notification_type='9';

			}elseif($request_status=='8'){//cargado

				$notification_type='10';

			}elseif($request_status=='9'){//trip start

				$notification_type='11';

			}elseif($request_status=='10'){//llego destino

				$notification_type='12';	

			}elseif($request_status=='11'){//descargando

				$notification_type='13';	

			}elseif($request_status=='12'){//Descarga finalizada

				$notification_type='14';	

			}elseif($request_status=='13'){//completed

				$notification_type='15';	

			}

			

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

		if($this->logged_user['user_type'] == 0 || $this->logged_user['is_company']==1){

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$this->load->library(array('form_validation'));

			$rules=array(

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

				$request_status = 0;

				// validate the request 

				if($request_id>0){

					$find_request=array(

						'request_id'=>$request_id,

						'driver_id'=>$user_id,

						'request_status >= '=>'5',

						'request_status <= '=>REQUEST_COMPLED_STATUS,

					);

					$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);

					if(empty($request)){

						$this->response_message="Orden no encontrada.";

						$this->json_output($response_data);

					}



					$response_data['request_status'] = $request['request_status'];

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

				//
				$find_request_cond = array(

					'request_id'=>$request_id

				);
		
				$update_request_data = array(

					'last_location_update_date' => $this->dateformat

				);

				$request = $this->BaseModel->updateDatas($this->tableNameRequest,$update_request_data,$find_request_cond);
				//



				if($driver_location_id>0){

					$this->response_message="Ubicación actualizada.";

					$this->response_status=1;

				}

				else{

					$this->response_message="No se pudo actualizar la ubicación.";

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

			$this->response_message="Orden incorrecta.";

		}

		else{

			$user_id = $this->logged_user_id;

			$request_id = $this->input->post('request_id');

			$location_datas = $this->input->post('location_datas'); // its a json string 

			/*

				$location_datas=[{"latitude":"","longitude":""}]

			*/

			if(empty($request_id) || $request_id<0){

				$this->response_message="El campo Orden es requerido.";

				$this->json_output($response_data);

			}

			if(empty($location_datas)){

				$this->response_message="El campo Ubicación es requerido.";

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

				$this->response_message="No se encontró la orden.";

				$this->json_output($response_data);

			}

			if(is_array($location_datas) && count($location_datas)>0){

				$this->response_message="Ubicación actualizada.";

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

					//
					$find_request_cond = array(

						'request_id' => $request_id

					);
			
					$update_request_data = array(

						'last_location_update_date' => $this->dateformat

					);

					$request = $this->BaseModel->updateDatas($this->tableNameRequest,$update_request_data,$find_request_cond);
					//

				}

			}

			else{

				$this->response_message="No se pudo actualizar la ubicación.";

			}

			$this->response_status=1;

		}

		$this->json_output($response_data);

	}

	

	//hlm 04/09/19

	

  public function driver_location_update_in_time(){

		$response_data=array();

		$this->minimum_param_checked(1);



		 $user_id = 23;

			

				$request_id = $this->input->post('request_id');

				$latitude = $this->input->post('latitude');

				$longitude = $this->input->post('longitude');

				

				

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

				$driver_location_id = $this->BaseModel->insertData($this->tableNameRequestDriverLocationInTime,$save_data);

				if($driver_location_id>0){

					$this->response_message="Ubicación actualizada.";

					$this->response_status=1;

				}

				else{

					$this->response_message="No se pudo actualizar la ubicación.";

				}

			

		$this->json_output($response_data);

	}

	

	public function completed_requests_db() {


	 $response_data=array();

     $date = $this->input->post('date');

     $tipo_dashboard  = $this->input->post('tipo_dashboard');


        switch ($tipo_dashboard) {

        	case 1:

        	    $select_fields='COUNT(*) AS count, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date, SUM(IF(TIMESTAMPDIFF(SECOND, ta_date, it_date) <= 10800, 1 , 0)) AS ontime_count';

        	    $rs = 13;

        		switch ($date) {

        			 case 'today':
        			 	$select_fields='DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date, pickup_time AS hour, IF(TIMESTAMPDIFF(SECOND, ta_date, it_date) <= 10800, 1 , 0) AS ontime_count';
		                $group = array();
		             break;
		            case 'yesterday':
		                $select_fields='DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date, pickup_time AS hour, IF(TIMESTAMPDIFF(SECOND, ta_date, it_date) <= 10800, 1 , 0) AS ontime_count';
		                $group = array();
		             break; 
		            case 'thisWeek':
		                $group =  array('weekday(pickup_date)');
		             break;
		            case 'thisMonth':
		                $group =  array('DAYOFMONTH(pickup_date)');
		             break;
		            case 'lastMonths':
		                $group =  array('YEAR(pickup_date)', 'MONTH(pickup_date)');
		             break;
		            default :
		               $group=array(); 
        		}

        		break;

        	case 2:

        	    $select_fields='COUNT(*) AS count, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date, pickup_time AS hour';

        	    $rs = 13;

        		switch ($date) {

        			 case 'today':
        			    $select_fields='DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date, pickup_time AS hour';
		                $group = array();
		             break;
		            case 'yesterday':
		                $select_fields='DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date, pickup_time AS hour';
		                $group = array();
		             break; 
		            case 'thisWeek':
		                $group =  array('weekday(pickup_date)');

		             break;
		            case 'thisMonth':
		                $group =  array('DAYOFMONTH(pickup_date)');
		             break;
		            case 'lastMonths':
		                $group =  array('YEAR(pickup_date)', 'MONTH(pickup_date)');
		             break;
		            default :
		               $group=array(); 
        		}

        		break;

        	case 3:

        	    $select_fields='COUNT(*) AS count, trailer_id';

        	    $rs = 13;

        		$group = array('trailer_id');

        		switch ($date) {

        			 case 'today':
        			    $select_fields='COUNT(*) AS count, trailer_id';
		                $group = array('trailer_id');
		             break;
		            case 'yesterday':
		                $select_fields='COUNT(*) AS count, trailer_id';
		                $group = array('trailer_id');
		             break; 
		            case 'thisWeek':
		                $select_fields='COUNT(*) AS count, trailer_id, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
		                $group = array('trailer_id');

		             break;
		            case 'thisMonth':
		                $select_fields='COUNT(*) AS count, trailer_id, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
		                $group = array('trailer_id');
		             break;
		            case 'lastMonths':
		                $select_fields='COUNT(*) AS count, trailer_id, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
		                $group = array('trailer_id', 'YEAR(pickup_date)', 'MONTH(pickup_date)');
		             break;
		            default :
		              $group = array('trailer_id');
        		}

        		break;

        	case 4:
        	    $select_fields='COUNT(*) AS count, request_status, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
        	    $rs = array(13,14);
        		$group = array('request_status');

        		switch ($date) {

        			 case 'today':
        			    $select_fields='COUNT(*) AS count, request_status';
		                $group = array('request_status');
		             break;
		            case 'yesterday':
		                $select_fields='COUNT(*) AS count, request_status';
		                $group = array('request_status');
		             break; 
		            case 'thisWeek':
		                $select_fields='COUNT(*) AS count, request_status, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
		                $group = array('request_status', 'weekday(pickup_date)');

		             break;
		            case 'thisMonth':
		                $select_fields='COUNT(*) AS count, request_status, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
		                $group = array('request_status', 'DAYOFMONTH(pickup_date)');
		             break;
		            case 'lastMonths':
		                $select_fields='COUNT(*) AS count, request_status, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';
		                $group = array('request_status', 'YEAR(pickup_date)', 'MONTH(pickup_date)');
		             break;
		            default :
		              $group = array('request_status');
        		}

        		break;

        		

        	default:

        	    $select_fields='COUNT(*) AS count, DATE_FORMAT(pickup_date, "%d-%m-%Y") AS date';

        	    $rs = 13;

        		$group = array();

        		break;

        }



        switch ($date){

            case 'today':
                $wr =  'pickup_date = CURDATE()';
                $group_by =  $group;

             break;

            case 'yesterday':

                 $wr =  'pickup_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';

                $group_by =  $group;

             break; 

            case 'thisWeek':

                  $wr =  'week(pickup_date) = week(CURDATE())';

                $group_by =  $group;

             break;

            case 'thisMonth':

                $wr =  'MONTH(pickup_date) = MONTH(CURDATE())';

                $group_by =  $group;

             break;

            case 'lastMonths':

                $wr =  'pickup_date > date_add(pickup_date, INTERVAL -6 MONTH)';  

                $group_by =  $group;

             break;

            default :

               $group_by=$group; 

        } 


		$find_user=array(

			'user_id'=>$this->input->post('user_id'),

			'user_type'=>'0'

		);

		$select_fields_user=array();

		$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$select_fields_user);

		//valida si tiene un superadmin

        if($user['super_parent_id'] > 0 ) {

        	$user_id = $user['super_parent_id'];

        } else {

        	$user_id = $this->input->post('user_id');

        }

        $get_requests_data = array(

            'user_id' => $user_id

        );

        

         if($rs != 0) {

             $status =  array( 'request_status' => $rs);

            $get_requests_data = array_merge($get_requests_data, $status);

         }


         if($tipo_dashboard ==  1) {

            $wr.=  ' AND NOT (ta_date IS NULL AND it_date IS NULL) ';  

         }  


        $get_requests_data = $this->BaseModel->getDatas($this->tableNameRequest,$get_requests_data,$select_fields,array(),array(),0,0,$wr,$group_by);

        $this->response_status=1;

        $response_data['requests']=$get_requests_data;

		$this->json_output($response_data);
        

	}
	


	public function edit_driver_request() {

		$this->minimum_param_checked(1);

		$response_data=array();

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0) {

				$user_id = $super_parent_id;

		}

		$driver_id = $this->input->post('driver_id');
		$request_id = $this->input->post('request_id');

		$find_request=array(
					'request_id' => $request_id,
					'transporter_id' => $user_id,
		);

		$select_fields=array();

	    $request = $this->BaseModel->getData($this->tableNameRequest,$find_request,$select_fields);


	    //print_r($request);
	    //exit();


	    if(!empty($request)) {


	    	$find_driver=array(

							'parent_user_id'=>$user_id,

							'user_id'=>$driver_id,

							'user_type'=>'1',

							'is_company'=>'0'

						);

	        $driver = $this->BaseModel->getData($this->tableNameUser,$find_driver,$select_fields);

	        if(!empty($driver)) {
	        	
	        	if(!$driver['is_blocked']) {

	        		$update_data=array(

										'driver_id'=>$driver_id,
										'update_date'=>$this->dateformat

										);

	        		$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);

					$this->response_status=1;

					$this->success_message = ($this->language_id==1) ? "Driver assigned successfully!" : "El conductor han sido asignados al servicio satisfactoriamente!";


					$this->response_message= $this->success_message;


	        	} else {
	        		$this->response_message= ($this->language_id==1) ? "Contact Bauen to activate this driver's account. Thank you" : "Pongase en contacto con Bauen para activar la cuenta de este conductor. Gracias";

	        	}

	        } else {

	        	$this->response_message= ($this->language_id==1) ? "Driver details not found. Please contact us": "No se han encontrado los detalles de la cuenta del conductor. Por favor contáctanos";

	        }


	    } else {

	    	$this->response_message="Información de orden incorrecta.";

	    }


	    $this->json_output($response_data);


	}


	public function edit_vehicle_request(){

		$this->minimum_param_checked(1);

		$response_data=array();

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0) {

				$user_id = $super_parent_id;

		}

		$vehicle_id = $this->input->post('vehicle_id');
		$request_id = $this->input->post('request_id');

		$find_request=array(
					'request_id' => $request_id,
					'transporter_id' => $user_id,
		);

		$select_fields=array();

	    $request = $this->BaseModel->getData($this->tableNameRequest,$find_request,$select_fields);

	    if(!empty($request)) {


	    	$find_vehicle=array(

									'user_id'=>$user_id,

									'vehicle_id'=>$vehicle_id,

								);

			$select_fields=array();

		    $vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle,$select_fields);

	        if(!empty($vehicle)) {
	        	
	        	if(!$vehicle['is_blocked']) {

	        		$update_data=array(

										'vehicle_id'=>$vehicle_id,
										'update_date'=>$this->dateformat

										);

	        		$this->BaseModel->updateDatas($this->tableNameRequest,$update_data,$find_request);

					$this->response_status=1;

					$this->success_message = ($this->language_id==1) ? "Vehicle assigned successfully!" : "El vehiculo han sido asignados al servicio satisfactoriamente!";


					$this->response_message= $this->success_message;


	        	} else {
	        		$this->response_message= ($this->language_id==1) ? "This vehicle is currently blocked by Bauen":"Este vehículo actualmente se encuentra bloqueado por Bauen";
	        	}

	        } else {

	        	$this->response_message="No se han encontrado los datos del vehículo";

	        }


	    } else {

	    	$this->response_message="Información de orden incorrecta.";

	    }


	    $this->json_output($response_data);

		
	}

	/*

	public function response_time_transporter(){

	} */

		

	public function user_is_blocked() {

		$this->minimum_param_checked(1);

		$response_data = array();



		$user_id = $this->input->post('user_id');

		if($user_id) {

			$find_data = array('user_id' => $user_id);

			$result = $this->BaseModel->getData($this->tableNameUser,$find_data, array('is_blocked'));

			$response_data = $result;

			$this->response_status = 1;

		} else {

			$this->response_status = -2;

		}



		$this->json_output($response_data);

	}



	// customer section 

	public function place_request1(){

		

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for user

		if($this->logged_user['user_type'] !=0 ){

			$this->response_message="Orden incorrecta.";

		}



		else{

			$creater_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			else{

				$user_id = $creater_id;

			}

			$_POST['user_id']=$user_id; // for anable in the post 

			

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

				/*$save_data=array('user_id','pickup_location','pickup_latitude','pickup_longitude','pickup_place_id','dropoff_location','dropoff_latitude','dropoff_longitude','dropoff_place_id','pickup_date','pickup_time','trailer_id','weight','size','request_amount','description','loadtype_id');*/



				$save_data=array('user_id','pickup_location','pickup_latitude','pickup_longitude','dropoff_location','dropoff_latitude','dropoff_longitude','pickup_date','pickup_time','trailer_id','weight','size','description','loadtype_id');

				$save_data = elements($save_data,$this->input->post());

				$save_data['creater_id']=$creater_id;

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

						$this->response_message="El nombre del vehículo ya esta en uso.";

						$this->json_output(array());

						return false;

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

					$this->response_message="¡Perfecto! Hemos recepcionado su pedido con éxito. Nos pondremos en contacto a traves de Ofertas en Curso e irá recibiendo notificaciones conforme lleguen los precios ";

					$this->response_status=1;

				}

                               

				else{

					$this->response_message="Su pedido no ha podido ser ingresado con éxito. Intente nuevamente";

				}

			}

			else{

				$this->response_message=validation_errors();

			}

		}

		//$this->logpost("place_request1");

		$this->json_output($response_data);

	}



	public function customer_push2() {

		$user_email = $this->input->post('user_email');

		$find_data = array('email' => $user_email);
		$searched_user = $users = $this->BaseModel->getData($this->tableNameUser,$find_data);

		$result=array();

			$find_user_push_keys=array(
				// 'user_id'=>525,
				'user_id'=> $searched_user['user_id'],

			);



			$select_fields='device_type,GROUP_CONCAT(device_push_key) device_push_key';

			$joins=array(

				array(

					'table_name'=>$this->tableNameUser,

					'join_type'=>'inner',

					'join_with'=>$this->tableNameUserRequestKey,

					'join_on'=>array('user_id'=>'user_id'),

					'oncond'=>array('is_blocked'=>array('0','2'),'is_deleted'=>'0'),

					'select_fields'=>array('user_type')

				)

			);

			$group_by=array('device_type');

			$tb = $this->dbprefix.$this->tableNameUserRequestKey;

			$cudate=$this->dateformat;

			$users = $this->BaseModel->getDatas($this->tableNameUserRequestKey,$find_user_push_keys,$select_fields,array(),$joins,0,0,array(),$group_by);

			$firebase_id = $users[0]['firebase_id'];



			if(!empty($users)){

				foreach($users as $key => $user){

					$user_type = $user['user_type'];

					$device_type = $user['device_type'];

					$device_push_key = $user['device_push_key'];

					$device_push_keys = explode(",",$device_push_key);

					$user_type=$user_type+1;

					if($device_type=='1'){ // android

						$result['andro_d']=$device_push_keys;

						$result['andro'][]= $this->push('Tienes un nuevo mensaje',$device_push_key, $screen);

					}

					elseif($device_type=='2'){ // ios

						$result['ios_d']=$device_push_keys;

						$result['ios'][] = $this->ios_push($messages,$device_push_key,$user_type);

					}

					else {

						// none

					}

				}

			}



		return $result;

	}



	public function place_request(){

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for user

		if($this->logged_user['user_type'] !=0 ){

			$this->response_message="Invalid request";

		}

		else{

			$creater_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			else{

				$user_id = $creater_id;

			}

			$_POST['user_id']=$user_id; // for anable in the post 

			$device_type = $this->logged_user['device_type'];

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

						'valid_date_format'=>($this->language_id==1) ? 'The Pickup Date field format should be yyyy-mm-dd':'El formato de la fecha de recojo debe ser aaaa-mm-dd',

						'valid_date'=>($this->language_id==1) ? 'The pickup date is invalid' : 'La fecha de recojo no es válida',

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

				/*$save_data=array('user_id','pickup_location','pickup_latitude','pickup_longitude','pickup_place_id','dropoff_location','dropoff_latitude','dropoff_longitude','dropoff_place_id','pickup_date','pickup_time','trailer_id','weight','size','request_amount','description','loadtype_id');*/

				$save_data=array('user_id','pickup_location','pickup_latitude','pickup_longitude','dropoff_location','dropoff_latitude','dropoff_longitude','pickup_date','pickup_time','trailer_id','weight','request_amount','size','description','loadtype_id');

				$save_data = elements($save_data,$this->input->post());

				$save_data['creater_id']=$creater_id;

				$save_data['create_date']=$this->dateformat;

				$save_data['update_date']=$this->dateformat;

				$save_data['device_type'] = $device_type;

				

				// another trailer type section 

				$other_trailer_txt = $this->input->post('other_trailer_txt');

				if(!empty($other_trailer_txt)){

					// find the name is already preasent or name

					if($this->trailer_uniquename($other_trailer_txt)){

						$save_data['other_trailer_txt']=$other_trailer_txt;

					}

					else{

						$this->response_message="This trailer name already exists";

						$this->json_output(array());

					}

				}
				

				//calculate the co-ordinate distance among the souce and destination location 

				$origin_zip = $save_data['pickup_latitude'];

				$origin_country = $save_data['pickup_longitude'];

				$dest_zip = $save_data['dropoff_latitude'];

				$dest_country = $save_data['dropoff_longitude'];

				$googleKey = 'AIzaSyDQKBD7N6NQQA9QHC-U7zy32SmIdfh7jsg';


				$distance = $this->getDistance($origin_zip,$origin_country,$dest_zip,$dest_country,$googleKey);

				//$distance = $this->distance_calculate($save_data['pickup_latitude'],$save_data['pickup_longitude'],$save_data['dropoff_latitude'],$save_data['dropoff_longitude']);

				$save_data['route_distance'] = $distance['distance'];// in meter

				$save_data['route_duration'] = $distance['time'];
				$save_data['route_duration_num'] = $distance['time_num'];
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


				$get_map_data['markers'][0] = array(

					'lat' => $this->input->post('pickup_latitude'),

					'long' => $this->input->post('pickup_longitude'),

					'place' => 'P',

				);

				$get_map_data['markers'][1] = array(

					'lat' => $this->input->post('dropoff_latitude'),

					'long' => $this->input->post('dropoff_longitude'),

					'place' => 'D',

				);

		
				$request_id = 0;

				$get_map = $this->BaseModel->get_map_by_lng_lat($get_map_data);


				if (!empty($get_map) ) {

					$save_data['request_image'] = $get_map;
					
					//validacion de request: con el mismo origen,destino,creador y creado en menos de un minuto

					$this->validationODCT($save_data);

					// validate date format
					
					$find_cond=array(
							'id_customer'=>$user_id
					);
						
					//ingreso de cotizacion premium
					
					$customerFilter = $this->BaseModel->getDatas($this->tableNameUserTransporter,$find_cond);
					
					if(count($customerFilter) > 0) {
						$save_data['is_premium'] = '1';
					}
					
					//ingreso de tiempo de cierre de bid
					
					if($this->input->post('close_bid_time') != null) {
					
						$close_bid_time = $this->input->post('close_bid_time');
						$save_data['close_bid_time'] = $close_bid_time;
					
					}
					
					//adicionales carga ancha 
				
					$carga = $this->input->post('size');
					$ancho_carga = explode('X',$carga);
					$ancho_carga = $ancho_carga[1];
					
					//adicionales carga largo 
				
					$largo_carga = explode('X',$carga);
					$largo_carga = $largo_carga[0];
					
					//adicionales carga alta
				
					$alto_carga = explode('X',$carga);
					$alto_carga = $alto_carga[2];
					
					$adicionales = '';
					
					if($largo_carga > 20.51) {
						$adicionales = '1 escolta';
					}
					
					if($alto_carga > 4.81) {
						$adicionales = '1 escolta';
					}
					
					if($ancho_carga > 3 && $ancho_carga <= 3.5) {
						$adicionales = '1 escolta';	
					} elseif ($ancho_carga > 3.5 && $ancho_carga <=  4) {
						$adicionales = '2 escoltas';
					} elseif ($ancho_carga > 4 ) {
						$adicionales = '2 escoltas + policia';
					}
					
					if($adicionales != '') {
						$save_data['additional'] = $adicionales;
					} 

					$request_id = $this->BaseModel->insertData($this->tableNameRequest,$save_data);
			 
					//$request_id=1;

					if($request_id>0){

						$trailer_id =  $this->input->post('trailer_id');
											   
                        //hlm 06/08/2020   -- Se crea para filtrar usuarios transporter
						
						$tfc_assos=array();
						$tfc_join_type="left"; 
						
						if(count($customerFilter) > 0){
							$tfc_assos = array ('id_customer' => $user_id);
							$tfc_join_type="inner";
						}		
										   
						$joins=array(

							array(
								'table_name'=>$this->tableNameUserTransporter,
								'join_with'=>$this->tableNameUser,
								'join_type'=>$tfc_join_type,
								'join_on'=>array('user_id'=>'id_transporter'),
								'select_fields'=>array('id_customer'),
								'conditions'=>$tfc_assos
							),

						);	
						
						$find_cond = array('user_type'=>1,
										   'parent_user_id' => 0,
										   'super_parent_id'=>0,
										   'is_blocked'=>0,
										   'is_deleted'=>0);	

						$drivers = $this->BaseModel->getDatas($this->tableNameUser,$find_cond,array(),array(),$joins); // obtiene transportistas que van a ser notificados
						
						$idsToNotify = array();

						$usersSelected = array();

						foreach ($drivers as $key => $driver) { 

							$user_id = $driver['user_id'];

							$vehiclesSelected = $this->BaseModel->getDatas($this->tableNameVehicle,array('user_id'=>$user_id,'trailer_id'=>$trailer_id,'vehicle_status'=>1,'is_deleted'=>0));
																																			

							if( count ($vehiclesSelected) > 0){

								array_push($idsToNotify,$user_id);

								array_push($usersSelected,$user);

							}

						}
						
						
						$subAdmins = array();

						$idsSubAdminsToNotify = array();


						foreach($idsToNotify as $idKey => $id) {

							$subAdmins = $this->BaseModel->getDatas($this->tableNameUser, array('super_parent_id'=>$id));

							foreach($subAdmins as $saKey => $subAdmin) {

								array_push($idsSubAdminsToNotify, $subAdmin['user_id']);

							}

						}

						// $this->success_message = $idsSubAdminsToNotify;

						foreach($idsSubAdminsToNotify as $idsKeys => $saIds) {

							array_push($idsToNotify, $saIds);

						}


						$notificationLogs=array();

						$notificationData=null;

						$log=null;

						$currentUser = $this->BaseModel->getData($this->tableNameUser,array('user_id'=> $this->input->post('user_id')));

						$firebase_id = $currentUser['firebase_id'];
						
						


						foreach ($idsToNotify as $key => $user_id) {

							$notificationData=  array('receiver_user_id' => $user_id, 'notification_text' => "request_id:".strval($request_id). ",".strval($firebase_id) );

							$notificationData['data'] =  $this->input->post();

							$notificationData['notification_type'] = 2;

							$msg_notif = "Nueva carga disponible para cotizar. Presione aquí para ver el detalle.";

							$log = $this->sendpushnotificationSimple($notificationData, $msg_notif, $request_id, 2);

							array_push($notificationLogs, $log);

							$notificationData=null;

						}


						$this->success_message = "Su pedido ha sido ingresado con éxito";

						$this->response_message= $this->success_message;

						$this->response_status = 1 ;  

						$bodyData = $this->input->post();

						$bodyData ['firebase_id'] =  $firebase_id;

						$bodyData['request_id'] = $request_id;

						$this->response_body =  $bodyData;	

					} else {

						$this->response_message="Su orden no pudo ser procesada.";
					}

				} else {

				$this->response_message = 'No se pudo guardar la imagen.' ;

				}                           

			} else {

					$this->response_message=validation_errors();

			}

		}

		//$this->logpost("place_request");

		$this->text_($response_data);

		$this->json_output($response_data);

	}


	public function validationODCT($data){
		
		$find_request=array(

					'pickup_latitude' => $data['pickup_latitude'],

					'pickup_longitude' => $data['pickup_longitude'],

					'dropoff_latitude'=>$data['dropoff_latitude'],
					
					'dropoff_longitude'=>$data['dropoff_longitude'],
					
					'creater_id' => $data['creater_id'],

					'create_date' => date("Y-m-d H:i", strtotime($data['create_date']))

				);

	    $select_fields = 'pickup_latitude, pickup_longitude, dropoff_latitude, dropoff_longitude, creater_id, DATE_FORMAT(create_date, "%Y-%m-%d %H:%i") AS date';

	    $request = $this->BaseModel->getData($this->tableNameRequest,$find_request,$select_fields);
		
		
		if (count($request) > 0) {
			
			$this->response_message = "No se puede enviar el mismo origen y destino en este momento, espere porfavor un minuto";

			$this->json_output(array());
		}
		
		
	}


	public function getDistance($origin_zip,$origin_country,$dest_zip,$dest_country,$googleKey){

			$origin = $origin_zip.",".$origin_country;

			$destination = $dest_zip.",".$dest_country;

			$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$origin&destinations=$destination&key=$googleKey";

			$api = file_get_contents($url);

			$data = json_decode($api);

			$rdata["distance"] = round(((int)$data->rows[0]->elements[0]->distance->value / 1000),2)." km";

			$rdata["time"] = $data->rows[0]->elements[0]->duration->text;
			$rdata["time_num"] = $data->rows[0]->elements[0]->duration->value;
			if(!$rdata["distance"]){

				$rdata = $data;

			}			

			return $rdata;

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

		$this->response_status=1;

		$this->json_output($response_data);

	}

	



	// #MARK: Edit request

	public function edit_request() {

		$response_data = array();

		$this->minimum_param_checked(1);



		$pickup_date = $this->input->post('pickup_date');

		$pickup_time = $this->input->post('pickup_time');

		$description = $this->input->post('description');



		if($pickup_date == '' && $pickup_time == '' && $description == '') {

			$this->response_message = ($this->language_id==1) ? "The order has been updated successfully":"Se ha actualizado la orden con éxito";

			$this->response_status = 1;

		}



		// validate the request only for user

		elseif($this->logged_user['user_type'] != 0) {

			$this->response_message="Invalid request";

		} else {

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			

			if($super_parent_id > 0) {

				$user_id = $super_parent_id;

			}

			

			$this->load->library(array('form_validation'));

			$rules = array(

				array(

					'field' => 'request_id',

					'label' => 'Request',

					'rules' => 'trim|required|greater_than[0]',

					'errors'=> array(

						'greater_than' => 'The %s field is required.'

					)

				)

			);



			if($pickup_date != '') {

				array_push($rules, array(

					'field'=>'pickup_date',

					'label'=>'Pickup Date',

					'rules'=>'trim|required|callback_valid_date_format|callback_valid_date',

					'errors'=>array(

						'valid_date_format'=>'The Pickup Date field format should be yyyy-mm-dd.',

						'valid_date'=>'The Pickup Date is invalid.',

					)

				));

			}



			if($pickup_time != '') {

				array_push($rules, array(

					'field'=>'pickup_time',

					'label'=>'Pickup Time',

					'rules'=>'trim|required',

					'errors'=>array()

				));

			}



			if($description != '') {

				array_push($rules, array(

					'field' => 'description',

					'label' => 'Description',

					'rules' => 'trim|required',

					'errors' => array()

				));

			}



			$this->form_validation->set_rules($rules);

			$this->form_validation->set_error_delimiters('', '');



			if($this->form_validation->run() === true) {

				$request_id = $this->input->post('request_id');

				

				// validate the request section 

				$find_request = array(

					'request_id' => $request_id,

					'user_id' => $user_id,

					'request_status <' => REQUEST_COMPLED_STATUS

				);

				$request = $this->BaseModel->getData($this->tableNameRequest, $find_request);

				if(!empty($request)) {

					$update_data = array('update_date' => $this->dateformat);

					if($pickup_date != '') $update_data['pickup_date'] = $pickup_date;

					if($pickup_time != '') $update_data['pickup_time'] = $pickup_time;

					if($description != '') $update_data['description'] = $description;



					$this->BaseModel->updateDatas($this->tableNameRequest, $update_data, $find_request);

					$this->response_message = ($this->language_id==1) ? "The order has been updated successfully":"Se ha actualizado la orden con éxito";

					$this->response_status = 1;

				} else {

					$this->response_message = "Orden no encontrada.";

				}

			} else {

			$error = validation_errors();

			$this->response_message = $error;

			}

		}



		$this->json_output($response_data);

	}

	



	// #MARK: Request Bids

	public function request_bids(){

		$response_data=array();

		

		$this->minimum_param_checked(1);

		// validate the request only for transporter and user

		if(($this->logged_user['user_type'] !=0) && ($this->logged_user['is_company']==0)){

			$this->response_message="Invalid request";

			$this->json_output($response_data);

		}

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $parent_user_id;

		}

		

		// receive params 

		$request_id = $this->input->post('request_id');

		$bid_status = $this->input->post('bid_status');

		$search_text = $this->input->post('search_text');

		

		$page_no = $this->input->post('page_no');



		$this->logpost('request_bids');



		$page_no = ($page_no>1)?$page_no:1;

		$limit = $this->limit;

		$offset = ($page_no-1)*$limit;

		//validation section 

		if(empty($request_id)){

			$this->response_message="El campo Orden es requerido.";

			$this->json_output($response_data);

		}

		$find_request = array(

			'request_id'=>$request_id

		);

		

		/* if($this->logged_user['user_type']==0){

			$find_request['user_id']=$user_id;

		} */

		

		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);

		if(empty($request)){

			$this->response_message="Orden no encontrada.";

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

		$this->response_status=1;

		$response_data['total_row']=$total_row;

		$response_data['bids']=$requestbids;

		$this->json_output($response_data);

	}

	



	// #MARK: Bid Accept

	public function bid_accept() {

		

		$response_data = array();

		$this->minimum_param_checked(1);

		// validate the request only for user

		if($this->logged_user['user_type'] != 0) {

			$this->response_message="Orden incorrecta.";

		} else {

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}

			//tableNameRequestBid

			// receive the posted data 

			$request_id = $this->input->post('request_id');

			$bid_id = $this->input->post('bid_id');



			$this->logpost('bid_accept');



			// validation section 

			if(empty($request_id)){

				$this->response_message="El campo Orden es requerido.";

				$this->json_output($response_data);

			}

			if(empty($bid_id)){

				$this->response_message="El campo Propuesta es requerido.";

				$this->json_output($response_data);

			}

			$find_request = array(

				'request_id'=>$request_id,

				'user_id'=>$user_id,

				'request_status'=>array('1', '4') // should not be 0. But as it seems that the request status is used in multiple things and the bid has to be accepted then this case is pssible

			);

			$request = $this->BaseModel->getData($this->tableNameRequest, $find_request);

			if(empty($request)){

				$this->response_status = 1;

				$this->response_message="Pedido aceptado con éxito. Vaya a Tracking para hacerle seguimiento al pedido una vez que el conductor inicie sesión en su aplicación";

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

				$this->response_message="Esta cotización ha sido cancelada por el transportista. Por favor, elija otra opción";

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

				//'bid_status'=>'1',

				'bid_status'=>'2',

				'update_date'=>$this->dateformat

			);

			$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_bid,$find_bid);

			// notification section 

			$notification_data=array(

				'request_id'=>$request_id,

				'user_id'=>$user_id,

				'receiver_user_id'=>$bid['user_id'],

				'notification_type'=>'6'

			);

			$this->add_notification($notification_data,$is_return=1);

			$this->success_message = ($this->language_id==1) ? "Bid accepted successfully! Waiting for driver and vehicle to be assigned" : "Hemos recibido su orden. Esperando asignación de chofer y camión";

			$this->response_message = $this->success_message;

			$this->response_status=1;

		}

		//$this->response_json($response_data);

		$this->json_output($response_data);

	}

	



	// #MARK: Request track

	// for all type users

	public function request_track(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		

		$this->logpost('request_track');

		// receive the posted data 

		$request_id = $this->input->post('request_id');

		if(empty($request_id)){

			$this->response_message="El campo Orden es requerido.";

			$this->json_output($response_data);

		}

		$find_request=array(

			'request_id'=>$request_id

		);

		if($this->logged_user['user_type']==1){

			if($this->logged_user['is_company']==1){

				if($super_parent_id>0){

					$find_request['transporter_id']=$super_parent_id;

				}

				else{

					$find_request['transporter_id']=$user_id;

				}

			}

			else{

				$find_request['driver_id']=$user_id;

			}

		}

		else{

//hlm

			if($super_parent_id>0){

				$find_request['user_id']=$super_parent_id;

			}

			else{ 

				$find_request['user_id']=$user_id;

			}

		}

		

		$select_fields=array();

		//$request = $this->BaseModel->getData($this->tableNameRequest,$find_request,$select_fields);

		$request = $this->getrequests($find_request);

		if(empty($request)){

			$this->response_message="No se encontró la orden.";

			$this->json_output($response_data);

		}

		else{

			$request = $request[0];

			$response_data=$request;

		}

		$request_status_track = json_decode($request['request_status_track']);

		$response_data['status_track']=$request_status_track;

		$this->response_status=1;

		$this->response_message="Request status tracking";

		

		$this->json_output($response_data);

	}

	

	public function logpost($urlapi=''){

		if(isset($_POST)){

		    $log  = "===================INIT==========================";

		    $log  .= "\n";

    		$log  .="URL: ".$urlapi."\n";

    		$log  .= "Data: ";

			$log.=json_encode($_POST).PHP_EOL;

			file_put_contents('./logs/data_log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);

		}

	}



	public function response_json($json=array()){

			$log  = "===================INIT JSON==========================";

			$log.=json_decode($json);

			file_put_contents('./logs/data_log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);

	}



	public function text_($text=''){

		

		file_put_contents('./logs/data_log_'.date("j.n.Y").'.txt', $text, FILE_APPEND);

}



	// #MARK: Request List

	public function request_list() {

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$user_type = $this->logged_user['user_type'];

		$is_company = $this->logged_user['is_company'];

		$super_parent_id = $this->logged_user['super_parent_id'];

		

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

		$this->logpost('request_list');

		

		
		if(empty($request_status)) {

			$request_status = 0;

		}

		$trans_bid_cond=array();

		$bid_assos=array();

		$find_request=array();

		

		if($user_type==0) {

			// for user section

			$find_request['user_id'] = $user_id;

            //hlm 
            
			if($super_parent_id>0) {

				$find_request['user_id'] = $super_parent_id;
				
				$user_id = $super_parent_id;

			}

			$find_request['request_status'] = $request_status;

		} else {

			// transportet and driver section 

			if($is_company == 0) {

				// driver section

				$find_request['driver_id'] = $user_id;

				if($request_status>5) {

					$find_request['request_status'] = $request_status;

				} elseif($request_status=='1') {

					$find_request['request_status <'] ='13';

					$find_request['request_status >='] ='5';

				} else {

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

				if($super_parent_id>0){

					$user_id=$super_parent_id;

				}

				

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
		
		//verifica si hay usuarios transporter filtrados por usuarios
		
		$tfc_assos = array(); 
		$filterct = array();
		
		if($user_type==0) {
			$find_cond=array(
				'id_customer'=>$user_id
			);
			
			$filterct = $this->BaseModel->getDatas($this->tableNameUserTransporter,$find_cond);
			
		}  else {
			$find_cond=array(
				'id_transporter'=>$user_id
			);
			$filterct = $this->BaseModel->getDatas($this->tableNameUserTransporter,$find_cond);
		} 
		
		
		
		if(count($filterct) > 0) {
			
			/* if($user_type==0) {
				$tfc_assos = array ('id_customer' => $user_id);
			} else {
				$tfc_assos = array ('id_transporter' => $user_id);	
			} */
			
		} else {	
			$find_request['is_premium']='0';
		}
		
		//filtro cierre de bids
		
		if(isset($_SERVER['APP_ENV'])){
			if($_SERVER['APP_ENV'] == 'development'){
				 $fecha_servidor = ' NOW() ';
			} 
		}  else {
				 $fecha_servidor = ' DATE_ADD(NOW(), INTERVAL 2 HOUR) ';
		}	
		
		if($user_type==1) {
			$find_request['complexCondition'] = ' IF(`trns_requests`.`close_bid_time`IS NULL , '.$fecha_servidor.' , `trns_requests`.`close_bid_time`) >=  '."'".$this->dateformat."'";
		}
		

		$assos_cond=array(

			'count'=>'1',

			'fields'=>array('request_id'),

			'trans_bid_cond'=>$trans_bid_cond,

			'bid_assos'=>$bid_assos,
			
			'tfc_assos' => $tfc_assos,

		);

		$total_row = $this->getrequests($find_request,$assos_cond,$offset,$limit);
		

		if($total_row>0){

			$assos_cond=array(

				'count'=>'0',

				'trans_bid_cond'=>$trans_bid_cond,

				'bid_assos'=>$bid_assos,

				'order_by' => array('pickup_date'=>'DESC'),
				
				'tfc_assos' => $tfc_assos,

			);

			$requests = $this->getrequests($find_request,$assos_cond,$offset,$limit);

		}

		else{

			$requests=array();

		}

		

		$response_data['total_row']=$total_row;

		$response_data['requests']=$requests;

		$response_data['find_request']=$find_request;

		$response_data['assos_cond']=$assos_cond;

		$response_data['offset']=$offset;

		$response_data['limit']=$limit;

		$this->response_status = 1;

		//DEBUG

		//$this->response_json($response_data);

		//

		$this->json_output($response_data);

	}

	



	// #MARK: Create Chat

	public function create_chat(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$user_type = $this->logged_user['user_type'];

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		

		// posted data 

		$request_id = $this->input->post('request_id');

		$other_user_id = $this->input->post('other_user_id');

		//$this->logpost('create_chat');

		//validation section

		if(empty($request_id)){

			$this->response_message="El campo Orden es requerido.";

			$this->json_output($response_data);

		}

		if(empty($other_user_id)){

			$this->response_message="El campo Usuario es requerido.";

			$this->json_output($response_data);

		}

		

		$find_request=array(

			'request_id'=>$request_id

		);

		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);

		if(empty($request)){

			$this->response_message="No se encontró la orden.";

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

			$find_cond['creater_id']=$user_id;

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

		$this->response_message="El chat ha iniciado.";

		//$this->logpost("create chat");

		$this->json_output($response_data);

	}

	



	// #MARK: Chat list

	public function chat_list(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$user_type = $this->logged_user['user_type'];

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id=$super_parent_id;

		}

		

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

	



	// #MARK: Chat messages

	public function chat_messages(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$user_type = $this->logged_user['user_type'];

		$super_parent_id = $this->logged_user['super_parent_id'];

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

				'super_parent_id'=>$super_parent_id,

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

	



	// #MARK Send message

	public function send_message(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$creater_id = $this->logged_user_id;

		$user_type = $this->logged_user['user_type'];

		$super_parent_id = $this->logged_user['super_parent_id'];

		

		if($super_parent_id>0){

			$user_id=$super_parent_id;

		}

		else{

			$user_id=$creater_id;

		}

		

		//posted data 

		$chat_id = $this->input->post('chat_id');

		$message_data = trim($this->input->post('message_data'));

		$last_message_id = trim($this->input->post('last_message_id'));

		if(empty($last_message_id)){

			$last_message_id=0;

		}

		

		// validate chat

		if(empty($chat_id)){

			$this->response_message="El campo Chat es requerido.";

			$this->json_output($response_data);

		}

		if(empty($message_data)){

			$this->response_message="El campo Mensaje es requerido.";

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

			$this->response_message="No se encontró el chat.";

			$this->json_output($response_data);

		}

		

		$save_data=array(

			'chat_id'=>$chat_id,

			'user_id'=>$creater_id,

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

			$this->response_message="Mensaje enviado.";

			$this->response_status=1;

			

			// update the receiver chat count 

			$receiver_user_id = $chat[$receiver_fls];

			//$this->update_user_count($receiver_user_id,1,2);

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

			$this->response_message="No se pudo enviar el mensaje.";

		}

		$this->json_output($response_data);

	}

	



	// #MARK: Delete message

	public function delete_message(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$user_type = $this->logged_user['user_type'];

		// get posted data 

		$message_id = $this->input->post('message_id');

		

		if(empty($message_id)){

			$this->response_message="El campo Mensaje es requerido.";

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

			$this->response_message="No se encontró el mensaje.";

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

			$this->response_message="No se pudo eliminar el mensaje.";

			$this->json_output($response_data);

		}

		$this->response_message="El mensaje ha sido eliminado.";

		$this->response_status=1;

		$this->json_output($response_data);

	}

	



	// #MARK: List rating

	public function rating_list(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		$other_user_id = $this->input->post('other_user_id');

		

		$page_no = $this->input->post('page_no');

		$page_no = ($page_no>1)?$page_no:1;

		$limit = $this->limit;

		$offset = ($page_no-1)*$limit;

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		$find_rattings=array(

			'receiver_user_id'=>$user_id,

			'is_blocked'=>array('0','2')

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

		$this->response_message="Rating del usuario";

		$response_data['total_row']=$total_row;

		$response_data['ratings']=$ratings;

		$this->json_output($response_data);

	}

	



	// #MARK: Give rating

	public function give_rating(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$creater_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		$user_type = $this->logged_user['user_type'];

		$is_company = $this->logged_user['is_company'];

		

		if($super_parent_id>0){

			$user_id=$super_parent_id;

		}

		else{

			$user_id = $creater_id;

		}

		

		$request_id = $this->input->post('request_id');

		$receiver_user_id = $this->input->post('receiver_user_id');

		$rating = $this->input->post('rating');

		$communication_rating = $this->input->post('communication_rating');

		$trust_rating = $this->input->post('trust_rating');

		$quality_rating = $this->input->post('quality_rating');

		$user_comment = $this->input->post('user_comment');

		

		// varification 

		if(empty($request_id) || $request_id<0){

			$this->response_message="El campo Orden es requerido.";

			$this->json_output($response_data);

		}

		if(empty($receiver_user_id) || $receiver_user_id<0){

			$this->response_message="El campo Rating es requerido.";

			$this->json_output($response_data);

		}

		// data validate 

		$find_request = array(

			'request_id'=>$request_id,

			'request_status'=>REQUEST_COMPLED_STATUS

		);

		// validate respective user 

		if($user_type=='1'){

			// transporter

			if($is_company){

				// transporties

				$find_request['transporter_id']=$user_id;

			}

			else{

				// driver section 

				$find_request['driver_id']=$user_id;

			}

		}

		else{

			$find_request['user_id']=$user_id;

		}

		

		// join 

		$joins=array(

			array(

				'table_name'=>$this->tableNameUserRating,

				'join_with'=>$this->tableNameRequest,

				'join_type'=>'left',

				'join_on'=>array('request_id'=>'request_id'),

				'oncond'=>array('is_blocked'=>array('0','2'),'is_deleted'=>'0','giver_user_id'=>$user_id),

				'select_fields'=>array('rating_id')

			),

		);

		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request,array(),array(),$joins);

		

		if(empty($request)){

			$this->response_message="Detalles de la orden incorrectos.";

			$this->json_output($response_data);

		}

		else{

			// validate previous rating given or not

			if(!empty($request['rating_id'])){

				$this->response_message=($this->language_id==1) ? "This service has already been rated" : "Este servicio ya ha sido calificado anteriormente";

				$this->json_output($response_data);

			}

		}

		

		// save section 

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

		/*$find_rating=array(

			'request_id'=>$request_id,

			'giver_user_id'=>$user_id,

		);

		$userrating = $this->BaseModel->getData($this->tableNameUserRating,$find_rating);

		if(!empty($userrating)){

			$this->response_message="Ya nos ha dado su opinión de este servicio antes";

			$this->json_output($response_data);

		}

		*/

		$save_data=array(

			'request_id'=>$request_id,

			'giver_user_id'=>$user_id,

			'receiver_user_id'=>$receiver_user_id,

			'rating'=>$rating,

			'communication_rating'=>$communication_rating,

			'trust_rating'=>$trust_rating,

			'quality_rating'=>$quality_rating,

			'user_comment'=>$user_comment,

			'creater_id'=>$creater_id,

			'create_date'=>$this->dateformat,

			'update_date'=>$this->dateformat,

		);

		$rating_id = $this->BaseModel->insertData($this->tableNameUserRating,$save_data);

		if($rating_id>0){

			$this->response_status=1;

			$this->response_message=($this->language_id==1) ? "Rating submitted":"La calificación al servicio ha sido ingresada";

		}

		else{

			$this->response_message=($this->language_id==1) ? "Please try again":"Por favor, intenta nuevamente";

		}

		$this->json_output($response_data);

	}





	// #MARK: Notification List

	public function notification_list(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		

		$request_id = $this->input->post('request_id');

		$page_no = $this->input->post('page_no');

		$page_no = ($page_no>1)?$page_no:1;

		$limit = $this->limit;

		$offset = (($page_no-1)*$limit);



		$find_notifications=array(

			'receiver_user_id'=>$user_id,

			'is_blocked'=>array('0','2')

		);

		if($request_id>0){

			$find_notifications['request_id']=$request_id;

		}

		

		$select_fields=array();

		$extra_cond = array(

			'is_count'=>'1'

		);

		$total_row = $this->getnotifications($find_notifications,$extra_cond);

		if($total_row>0) {

			$extra_cond = array (

				'is_count'=>'0',

				'limit'=>$limit,

				'offset'=>$offset,

				'order_by'=>array( 'notification_id'=>'DESC' )

			);

			$notifications = $this->getnotifications($find_notifications,$extra_cond);

			// update all the notification as read 

			$update_cond=array( 'is_read' => '0', 'receiver_user_id' => $user_id );

			$update_data=array( 'is_read' => '1', 'update_date' => $this->dateformat );

			$this->BaseModel->updateDatas($this->tableNameRequestNotification,$update_data,$update_cond);

			// valish the count of the user notification section 

			$this->update_user_count($user_id,0,1);

		} else {

			$notifications=array();

		}

		$this->response_status=1;

		$response_data['total_row']=$total_row;

		$response_data['notifications']=$notifications;

		//$this->logpost("notification_list");

		$this->json_output($response_data);

	}

	



	// #MARK: Notification add

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

		$this->response_status=1;

		//$this->logpost("notification_add");

		$this->json_output($response_data);

	}

	



	// #MARK: Unread notifications

	public function unread_notification(){

		$response_data=array();

		$this->minimum_param_checked(1);

		$user_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if($super_parent_id>0){

			$user_id = $super_parent_id;

		}

		

		$request_id = $this->input->post('request_id');

		////$this->logpost("unread_notification");

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

			$this->customer_push($user_id, 'Tienes un nuevo mensaje', 0);

			$unread_chat = 0;

		}

		else{

			// get notification count from counts table 

			$find_cond=array(

				'user_id'=>$user_id,

				'is_blocked'=>array('0','2'),

			);

			$select_fld=array();

			$count = $this->BaseModel->getData($this->tableNameUserCount,$find_cond,$select_fld);

			$unread_notification = isset($count['unread_notification'])?$count['unread_notification']:'0';

			$unread_chat = isset($count['unread_chat'])?$count['unread_chat']:'0';

		}

		

		$response_data['unread_notification']=$unread_notification;

		$response_data['unread_chat']=$unread_chat;



		// $this->customer_push($user_id, 'Tienes un nuevo mensaje', 0);

		$this->response_status=1;

		////$this->logpost("unread_notification");

		$this->json_output($response_data);

	}

	



	// #MARK: Request map

	public function request_map(){

		$response_data=array();

		$request_id = $this->input->post('request_id');

		$is_saved = $this->input->post('is_saved');

		if(empty($request_id) || $request_id<0){

			$this->response_message="El campo Orden es requerido";

			$this->json_output($response_data);

		}

		$find_request = array(

			'request_id'=>$request_id

		);

		$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);

		if(empty($request)){

			$this->response_message="Detalles de la orden incorrectos.";

			$this->json_output($response_data);

		}

		

		$this->logpost('request_map');



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

		$this->response_status=1;

		$this->json_output($response_data);

	}

	



	// #MARK: MARK EXPIRED REQUEST

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

		$this->response_status=1;

		$this->json_output($response_data);

	}

	



	// #MARK: Create user count, for back up section 

	public function create_usercount(){

		$response_data=array();

		$sql = "SELECT user_id,create_date,update_date FROM $this->dbprefix$this->tableNameUser WHERE is_deleted='0' AND user_id NOT IN (SELECT user_id FROM $this->dbprefix$this->tableNameUserCount)";

		$datas = $this->BaseModel->customSelect($sql);

		$response_data['users']=$datas;

		if(!empty($datas)){

			// munti row inserted from here 

			$this->BaseModel->insertDatas($this->tableNameUserCount,$datas);

		}

		$this->response_status=1;

		$this->json_output($response_data);

	}

	



	// #MARK: Create sub admin 

	public function create_sub_admin() {

		$response_data=array();

		$this->minimum_param_checked(1);

		//$response_data = $this->logged_user;

		$this->load->library(array('form_validation'));

		$this->load->helper(array('array'));

		$creater_id = $this->logged_user_id;

		$super_parent_id = $this->logged_user['super_parent_id'];

		if(empty($super_parent_id)) {

			$super_parent_id = $creater_id;

		}

		

		$user_type = $this->logged_user['user_type'];

		$is_company = $this->logged_user['is_company'];

		

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

					'user_unique_email'=>($this->language_id==1) ? 'This email is already registered':'Este correo ya ha sido registrado anteriormente'

				)

			),

			array(

				'field'=>'phone_no',

				'label'=>'Phone No.',

				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',

				'errors'=>array(

					'user_unique_phone_no'=>($this->language_id==1) ? 'This phone number is already registered with us':'Este número de teléfono ya se encuentra registrado con nosotros',

					'valid_phone_no'=>($this->language_id==1) ? 'Please enter a valid mobile phone number':'Por favor ingresa un número de celular válido',

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

		$this->form_validation->set_error_delimiters('','');



		if($this->form_validation->run()===true) {

			$first_name = $this->input->post('first_name');

			$last_name = $this->input->post('last_name');

			$email = $this->input->post('email');

			$phone_no = $this->input->post('phone_no');

			$password = $this->input->post('password');

			$dni_no = $this->input->post('dni_no');

			//default section 

			if(empty($last_name)) {

				$last_name='';

			}



			if(empty($dni_no)) {

				$dni_no='';

			}

			

			// save the user 

			$save_data = array(

				'first_name' => $first_name,

				'last_name' => $last_name,

				'email' => $email,

				'phone_no' => $phone_no,

				'user_type' => $user_type,

				'password' => md5($password),

				'showpass' => $password,

				'dni_no' => $dni_no,

				'is_company' => $is_company,

				'parent_user_id' => $creater_id,

				'super_parent_id' => $super_parent_id,

				'is_user_verify' => '1',

				'is_phone_no_verify' => '1',

				'is_email_verify' => '1',

				'create_date' => $this->dateformat,

				'update_date' => $this->dateformat,

				'deleted_date' => ''

			);

			

			$user_id = $this->BaseModel->insertData($this->tableNameUser,$save_data);

			if($user_id>0) {

				$this->response_message=($this->language_id==1) ? "The account has been created! The username will be the email address you just provided":"¡Cuenta creada con éxito! El usuario será la dirección de correo electrónico brindada";

				$this->response_status=1;

			} else {

				$this->response_message="Tenemos un error al procesar la información. Intente nuevamente.";

			}

		} else {

			$error = validation_errors();

			$this->response_message=$error;

		}

		$this->json_output($response_data);

	}

	



	// #MARK: Test SMS

	public function test_sms() {

		$response_data=array();

		$phone = $this->input->post('phone_no');

		$body = $this->input->post('code');

		// send sms

		$body = "Su código de verificación de Bauen es ".$body;

		$response_data = $this->twilio_send_sms($phone,$body);

		$this->json_output($response_data);

	}





	// #MARK: Delete Request

	public function delete_request() {

		$response_data=array();

		$this->minimum_param_checked(1);

		// validate the request only for user



		//$this->logpost("delete_request");



		if($this->logged_user['user_type'] !=0 ) {

			$this->response_message="Orden inválida.";

		} else {

			$user_id = $this->logged_user_id;

			$super_parent_id = $this->logged_user['super_parent_id'];

			if($super_parent_id>0){

				$user_id = $super_parent_id;

			}



			$request_id = $this->input->post("request_id");

			if(empty($request_id)){

				$this->response_message="La orden ingresada no existe.";

				$this->json_output($response_data);

			}

			$find_request=array(

				'request_id'=>$request_id,

				'user_id'=>$user_id,

			);

			$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);

			if(empty($request)){

				$this->response_message="No se encontró la información de la orden.";

				$this->json_output($response_data);

			}

			

			//now remove the request 

			$this->BaseModel->removeDatas($this->tableNameRequest,$find_request);

			$this->response_message="La orden ha sido eliminada.";

			$this->response_status=1;



		}

		

		$this->json_output($response_data);

	}

	



	// #MARK: Delete Request

    public function getCompanyByUserId() {

		$user_id = $this->input->post('user_id');

		//$this->minimum_param_checked(1);

		$find_cond=array(

			'user_id'=>$user_id,

			'is_blocked'=>array('0','2'),

			//'is_deleted'=>array('1','0')

		);



		$select_flds=array('user_id','first_name','last_name','email','phone_no','user_type','image','dni_no','is_company','company_name','company_licence_no','ruc_no','is_user_verify','verification_code','about_us','address','firebase_id');

		$tb = $this->dbprefix.$this->tableNameUser;

		$select_flds = $tb.'.'.implode(", $tb.",$select_flds);



		$user = $this->BaseModel->getData($this->tableNameUser,$find_cond,$select_flds);

		$response = array( 'status' => 1, 'message'=>$user, );

		$this->json_output($response);

	}


	public function subadminscustomer() {

		$this->minimum_param_checked(0);
		$user_id=$this->input->post('user_id');
		$response_data=array();
		//find the subadmins 
		$find_cond=array(
			'super_parent_id'=>$user_id
		);
		$subadmins = $this->BaseModel->getDatas($this->tableNameUser,$find_cond);
		$response_data['subadmins']=$subadmins;
		$this->json_output($response_data);

	}
	
	public function addsubadmincustomer(){
		
		$this->minimum_param_checked(0);
		$creater_id=$this->input->post('user_id');
		$user_id=$this->input->post('user_id');
		
		$data=array();
			
		$verification_code = rand(9999,1000000);
		$password = $this->input->post('password');
		$email_user = $this->input->post('email');
		$phone_number = $this->input->post('phone_no');
		$firstname = $this->input->post('first_name');
		$lastname = $this->input->post('last_name');
		$save_data=array(
			'parent_user_id'=>$creater_id,
			'creater_id'=>$creater_id,
			'user_type'=>'0',
			'is_company'=>'1',
			'super_parent_id'=>$user_id,
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
				$this->response_message="Los datos del sub administrador se han registrado satisfactoriamente";
			}else{
				$this->response_message = 'No se pudo guardar los detalles del sub administrador.';
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
			$this->response_message = 'No se pudo guardar los detalles del sub administrador.';
		}
		$this->json_output($data);
	}
	
	public function editsubadmincustomer(){
		
		$this->minimum_param_checked(0);
		$creater_id=$this->input->post('user_id');
		$user_id=$this->input->post('user_id');
		$subadmin_id=$this->input->post('subadmin_id');
		
		$data=array();
		if($subadmin_id>0){
			$find_driver=array(
				'user_id'=>$subadmin_id,
				'super_parent_id'=>$user_id,
				'user_type'=>'0',
				'is_company'=>'1'
			);
			$subadmin = $this->BaseModel->getData($this->tableNameUser,$find_driver);
			if(empty($subadmin)){
				$this->response_message = 'No se encontraron los detalles del sub administrador.';
			}
			$data['subadmin']=$subadmin;
		}
		else{
			$this->response_message = 'No se encontraron los detalles del sub administrador.';
		}
		
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
		$this->response_message = 'Los detalles del sub administrador han sido actualizados.';	

		$this->json_output($data);

	}


}
