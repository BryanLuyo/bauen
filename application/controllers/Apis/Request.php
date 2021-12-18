<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'libraries/REST_Controller.php');
require_once(APPPATH.'controllers/Apis/Users.php');

use Restserver\libraries\REST_Controller;
//use Users;
class Request extends Users{

 
    private $response_status;
	private $response_message;
    private $response_body;

	private $logged_user_id;
    private $success_message;
	private $table ;
	private $requestkeys;
	
	private $language_id;
	private $data_doby;
	private $data_status;
	private $data_message;
	

    public function __construct(){

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

		parent::__construct();

			/**MODE DEV ERROR INIT*/
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			/**END DEV */

			$this->load->model('GlobalMod');
            $this->response_status = 1;
			$this->language_id = 1;
			
			$this->table = "trns_requests";
			$this->requestkeys = "trns_user_request_keys";
			$this->data_doby = array();
			$this->data_message = '';
			$this->data_status = 500;
            
	  }


	public function distance_get($origin_zip,$origin_country,$dest_zip,$dest_country){
			$origin = $origin_zip.",".$origin_country;
			$destination = $dest_zip.",".$dest_country;
			$googleKey = 'AIzaSyDQKBD7N6NQQA9QHC-U7zy32SmIdfh7jsg';
			$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$origin&destinations=$destination&key=$googleKey";
			$api = file_get_contents($url);
			$data = json_decode($api);
			$rdata["distance"] = round(((int)$data->rows[0]->elements[0]->distance->value / 1000),2)." km";
			$rdata["time"] = $data->rows[0]->elements[0]->duration->text;
			if(!$rdata["distance"]){
				$rdata = $data;
			}			
			$this->response($rdata);
	}

	/*
	REQUEST POR IDE DE USUARIO
	public function list_requestUserId_post(){
		$data = $this->post();
		$this->minimum_param_checked(0,$data);

		if($this->data_status == 401){
			$data['message'] = $this->data_message;
			$data['body'] = $this->data_body;
			$data['status'] = 401;
			$this->response($data);
			return false;
		}
		
		$conditional = array('user_id'=>$data['user_id'],'is_deleted'=>0);
		$requestUser = $this->GlobalMod->get_list_Where('*',$this->table,$conditional);
		$this->response(array('body'=>$requestUser,'message'=>'List request id user TRUE','status'=>200));
	}*/
	  
	/***[TAREA PROGRAMADA] is_deleted=1 despues de pasar 2 semanas de su inserción o modificación*/
	public function delete_request_get(){      
		//$this->debug_request(array('True Cron'))  ;
        $QueryUpdateForCron = "update ".$this->table." set request_status=14 , deleted_date=now() where 
        pickup_date=date_sub(curdate(),interval 2 day) and is_deleted=0 and request_status <= 1";        
        $this->GlobalMod->queryInsert($QueryUpdateForCron);
	}
	
	
	/***[TAREA PROGRAMADA] request_status=13 despues de pasar 1 semana de su inserción o modificación en request_status = 6 en transito*/
	public function completed_request_get(){      
		//$this->debug_request(array('True Cron'))  ;
        $QueryUpdateForCron = "update ".$this->table." set request_status=13 , completed_date=now() where 
        pickup_date=date_sub(curdate(),interval 7 day) and is_deleted=0 and request_status IN(2,3,4,5,6,7,8,9,10,11,12)"; 
		
		echo "hola";
        //$this->GlobalMod->queryInsert($QueryUpdateForCron);
	}
	
	
	public function notifications_request_get(){      
		$day = 21;
		$userQuery = "select * FROM ".$this->table."  where is_deleted=0 
		and pickup_date = date_sub(curdate(),interval $day day) order by 1 desc";
		$dataUsers = $this->GlobalMod->query($userQuery);
		$data=null;
		foreach ($dataUsers as $key) {
			$query = "select * FROM trns_user_request_keys where user_id =".$key->user_id." and is_deleted = 0 and is_blocked=0";        
			$data = $this->GlobalMod->query($query);
			$data['request_id'] = $key->request_id;
		}

        $this->response($data);
	}
	
	/***[REST] paginación de 30/30 */
    public function list_request_pagination_post($page=0){        
        $n_records = 30;
        $init = $page*$n_records;
        
		$data_post = $this->post();
		$this->minimum_param_checked(0,$data_post);

		if($this->data_status == 401){
			$data['message'] = $this->data_message;
			$data['body'] = $this->data_body;
			$data['status'] = 401;
			$this->response($data);
			return false;
		}

		$conditional = array('is_deleted'=>0);
		$orden = array('create_date');
        $select = array();

        $response = array();
        $response =$this->GlobalMod->query("select * from ".$this->table."  
        where is_deleted = 0 order by create_date desc limit ".$init.",".$n_records);

        $success_message = (count($response)>0) ? count($response). ' Request encontrados' : 'No se encontraron request para este caso';
        $data['message'] = $success_message;
        $data['body'] = $response;
        $data['status'] = 200;
        $this->response($data);
    }


	/*
	RETORNA TODOS LOS REQUEST CON ESTADO IS_DELETED IGUAL A CERO
	public function list_request_all_post(){
		$data_post = $this->post();
		$response = array();
		$conditional = array('is_deleted'=>0);
		
        $response = $this->GlobalMod->get_list_Where('*',$this->table,$conditional);
        $success_message = (count($response)>0) ? 'Exito' : 'Sin registros';
        $data['message'] = $success_message;
        $data['body'] = $response;
        $data['status'] = 200;
        $this->response($data);
	}*/

	
    public function request_id_post(){
        $data_post = $this->post();
        $this->minimum_param_checked(0,$data_post);

		if(!isset($data_post['request_id'])){
            return false;
		}		
		
		if($this->data_status != 200){
			$data['message'] = $this->data_message;
			$data['body'] = $this->data_body;
			$data['status'] = $this->data_status;
			$this->response($data);
			return false;
		}

        $response = array();
        /*$conditional = array('request_id'=>$data_post['request_id']);
		$response = $this->GlobalMod->get_list_Where('*',$this->table,$conditional);*/
		
		$Query = "select r.*,t.name trailer_name,l.load_name load_name FROM trns_trailers t inner join trns_requests r
		on t.trailer_id = r.trailer_id inner join trns_loadtypes l 
		on l.loadtype_id = r.loadtype_id where r.request_id = ".$data_post['request_id'];;

		$response = $this->GlobalMod->query($Query);
        $success_message = (count($response)>0) ? 'Exito' : 'Sin registros';
        $data['message'] = $success_message;
        $data['body'] = $response;
        $data['status'] = 200;
       	$this->response($data);
    }


	/****CODIGO A REUTILIZAR  */
	private function minimum_param_checked($is_log_user=0,$data_post=array()) {
        
		$this->logged_user_id = $data_post['user_id'];
		$user_has_key = $data_post['user_request_key'];
		$device_type = $data_post['device_type'];
		$device_unique_code = $data_post['device_unique_code'];
		$device_push_key = $data_post['device_push_id'];
        $this->language_id = empty($data_post['language_id']) ? 1 : $data_post['language_id'];
		
		
		switch($is_log_user) {
			case 0:
				if(!in_array($device_type,array('1','2'))){
					$this->response_status=0;
					$this->response_message="Device type missing";
				}
				if(empty($device_unique_code)){
					$this->response_status=0;
					$this->response_message="Device key missing";
				}
				if(empty($this->logged_user_id)){
					$this->response_status=0;
					$this->response_message="Falta ingresar algun dato de usuario";
				}
				if(empty($user_has_key) || empty($device_push_key)){
					$this->response_status=0;
					$this->response_message="Token key missing";
				}
				break;			
			default:
				$this->response_status=-2;
				$this->response_message="Los parametros ingresados para el pedido son inválidos. Contacte a nuestros operadores";
				break;
		}
		
		
		try {
			if($this->response_status!=1){
				$this->data_body = array();
				$this->data_status =401;
				$this->data_message =$this->response_message;
			}else{
	
				$conditionals=array(
					'us.user_id'=>$this->logged_user_id,
					'ur.request_key'=>$user_has_key,
					'ur.device_type'=>$device_type,
					'ur.device_unique_id'=>$device_unique_code,
					'us.is_deleted'=>'0',
					'device_push_key'=>$device_push_key
				);
	
				$joins = 'us.user_id=ur.user_id';
				$userdata = $this->GlobalMod->Join('ur.*','trns_users us',$this->requestkeys .' ur',$joins,$conditionals);
				if(count($userdata)==0){
					$this->data_body = array();
					$this->data_status =401;
					$this->data_message = "Los parametros establecidos no cumplen nuestras condiciones.";
				}else{
					$this->data_status =200;
				}
				
			}
		} catch (\Throwable $th) {
				$this->data_body = array();
				$this->data_status =505;
				$this->data_message ="Error inesperado 505";
		}
	}


	public function debug_request($array=null){		
		    $log  = "=========================";
			$log.=json_encode($array).PHP_EOL;
			file_put_contents('./logs/debug_request'.date("j.n.Y").'.json', $log, FILE_APPEND);		
	}

}