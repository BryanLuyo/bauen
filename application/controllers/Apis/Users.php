<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

class Users extends REST_Controller{

    private $response_status;
	private $response_message;
    private $response_body;

	private $logged_user_id;
    private $success_message;
    private $table ;
    private $tableUserRequest;
	
	private $language_id;
	private $data_doby;
	private $data_status;
	private $data_message;
	private $notifications_type;
	private $fbkey;
    
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
			
            $this->table = "trns_users";
            $this->tableUserRequest = "trns_user_request_keys";

			$this->data_doby = array();
			$this->data_message = 'Error de petición HTTP';
			$this->data_status = 500;
			
			$this->notifications_type = array("bloqueo"=>99,"chat"=>16);
			$this->fbkey='AAAAixr2VP0:APA91bHKQt450GkfaMZeZ6IunusL0Z65ACXbe7gnNh6Rqzx6Bb4hYelt54iusT1bwc4SfhvHMq_kRhbv3WPStg7eBWTO_Wgp5YwY8_sScZMgTmlNZ1Uvoz1zhhXf4JHU4VcBt8VHTdxz'; // customer app 
        
      }


      public function sendin_sms_get() {


      	   $sid = 'ACcd6a5da0d009b1d2119ba20ec90a469b';
		   $token = '220b3f801a413127d0e31a504e936935';
		    $from='+18583754121';
		
			$result='';
			$erro='';

			/*
			if(empty($to) || empty($body)){
				return array('result'=>$result,'error'=>$erro);;
			}*/
			
		   // resource url & authentication
		   $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json';
		   $auth = $sid . ':' . $token;
		   $fields = 
			   '&To=' .  urlencode( '+51936949810' ) . 
			   '&From=' . urlencode( $from ) . 
			   '&Body=' . urlencode( 'Hola Que tal' );

		   // start cURL
		   $res = curl_init();
		   curl_setopt( $res, CURLOPT_URL, $uri );
		   curl_setopt( $res, CURLOPT_POST, 3 ); // number of fields
		   curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
		   curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
		   curl_setopt( $res, CURLOPT_RETURNTRANSFER, true ); // don't echo
		   curl_setopt( $res, CURLOPT_SSL_VERIFYHOST, 0);
		   curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, 0);
		   $result = curl_exec( $res );
		   if($result===false){
			$erro = curl_error($res);	 
		   }
		   else{
			   $erro=array();
		   }
		   curl_close( $res );	   
		   return array('result'=>$result,'error'=>$erro);


      	   /*
	      	$sid = 'ACcd6a5da0d009b1d2119ba20ec90a469b';
			$token = '220b3f801a413127d0e31a504e936935';
			$from='+18583754121';

			$client = new Client($sid, $token);
			$client->messages->create(
			    // Where to send a text message (your cell phone?)
			    '+51959912116',
			    array(
			        'from' => $from,
			        'body' => 'panza quieres pene ?'
			    )
			);*/

      }

	  private function send_sms( $to='', $body='' ) {
		$sid = 'ACcd6a5da0d009b1d2119ba20ec90a469b';
		$token = '220b3f801a413127d0e31a504e936935';
		$from='+18583754121';
		
		$result='';
		$erro='';
		if(empty($to) || empty($body)){
			return array('result'=>$result,'error'=>$erro);;
		}
		
	   // resource url & authentication
	   $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json';
	   $auth = $sid . ':' . $token;
	   $fields = 
		   '&To=' .  urlencode( '+'.$to ) . 
		   '&From=' . urlencode( $from ) . 
		   '&Body=' . urlencode( $body );

	   // start cURL
	   $res = curl_init();
	   curl_setopt( $res, CURLOPT_URL, $uri );
	   curl_setopt( $res, CURLOPT_POST, 3 ); // number of fields
	   curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
	   curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
	   curl_setopt( $res, CURLOPT_RETURNTRANSFER, true ); // don't echo
	   curl_setopt( $res, CURLOPT_SSL_VERIFYHOST, 0);
	   curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, 0);
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

	public function sendsmsapi_get($phone_no='',$language_id=1,$verify_code='',$codehas=''){
		$dc = base64_decode($codehas);
		if($phone_no!='' && $verify_code!='' && $codehas!='' && ($language_id==0 || $language_id==1)){
			$verify_code= ($language_id==0) ? "$verify_code is your verification code for bauen" : "Su código de verificación de Bauen es el $verify_code";
			$this->send_sms($phone_no,$verify_code);
			$data['message'] = $verify_code;
			$data['body'] = $verify_code;
			$data['status'] = 200;
			$this->response($data);
		}else{
			$data['message'] = 'Error';
			$data['body'] = 'No se envio el sms,Verifique con su proveedor';
			$data['status'] = 200;
			$this->response($data);
		}	
	}

	public function logout_post(){
		$data_post = $this->post();
		$this->debug_request($data_post);
		$this->minimum_param_checked(0,$data_post);
		$data = array();
		if($this->response_status!=1){
			$data['message'] = $this->data_message;
			$data['body'] = $this->data_body;
			$data['status'] = 401;
			$this->response($data);
			return false;
		}

		$set = array('device_push_key'=>NULL,'is_deleted'=>1);
		$where = array(
			'device_unique_id'=>$data_post['device_unique_code'],
			'user_id'=>$data_post['user_id'],
			'request_key'=>$data_post['user_request_key'],
			'is_deleted' => 0
		);
		
		if($this->GlobalMod->proc_update($set,$this->tableUserRequest,$where)){
			$data['message'] = 'No enviaremos notificaciones a este dispositivo';
			$data['body'] = array();
			$data['status'] = 200;
		}else{
			$data['message'] = 'No se pudo quitar la llave,Query invalida';
			$data['body'] = array();
			$data['status'] = 400;
		}
		$this->debug_request($data_post);
		$this->response($data);

	}

    public function blockestatuschange_post($iduser=0,$is_blocked=0){

        if(empty($this->post())){
			$data['message'] = $this->data_message;
			$data['body'] = $this->data_body;
			$data['status'] = 401;
			$this->response($data);
			return false;
        }

        $set = array('is_blocked'=>$is_blocked);
        $where =array('user_id'=>$iduser);
        $userRequest = array();
        $adicional = array();

        if($this->GlobalMod->proc_update($set,$this->table,$where)){
            
            $userRequest = $this->GlobalMod->get_list_Where('device_push_key',$this->tableUserRequest,array('user_id'=>$iduser,'is_deleted'=>0));
            $key_divice = [];
            $c = 0;
            foreach($userRequest as $key){
                $key_divice[$c] = $key->device_push_key;
                $c++;
            }
            $this->data_message = ($is_blocked==0) ? 'Se activo tu cuenta con exito' : 'Su cuenta ha sido bloqueado';            
            $this->data_message = (count($userRequest)>0) ? $this->notificationpush('Bauen',$key_divice,$this->data_message,$iduser,$this->notifications_type["bloqueo"], $adicional) : 'Not Divice'; 
                   
        }else{
            $this->data_message = 'Usuario no encontrado';
        }

        $data['message'] = $this->data_message;
		$data['body'] = $userRequest;
		$data['status'] = 200;
        $this->response($data);
    }
	
	public function chats_post(){
		$data= $this->post();

		$to_email = $data['email_to'];
		$from_email = $data['email_from'];
		$message = isset($data['message'])? $data['message']: 'Alguien intento enviarle un mensaje'  ;
		$iduser = isset($data['user_id']) ? $iduser = $data['user_id'] : 0 ;
		

		if(isset($to_email)){
			$Query = "select distinct(device_push_key) from trns_user_request_keys 
			where user_id in(select user_id from trns_users where email = '$to_email') and device_push_key is not null;";
			$scren = $this->notifications_type["chat"];
			$devices = $this->GlobalMod->query($Query);
			$device_push_key = [];

			foreach($devices as $key){
				if(strlen($key->device_push_key)>10){
					$device_push_key[] = $key->device_push_key;	
				}			
			}
			if(count($device_push_key)>0){
				$emisor = $this->GlobalMod->get_list_Where('*',$this->table,array('email'=>$from_email));
				$message = $emisor[0]->first_name.' '.$emisor[0]->last_name.': '.$message;

				//hlm
                $receptor = $this->GlobalMod->get_list_Where('*',$this->table,array('email'=>$to_email));

               	$efn  =  $emisor[0]->first_name;
               	$etdc =  $emisor[0]->email_verify_token;

                $rfn  =  $receptor[0]->first_name;
                $rtdc =  $receptor[0]->email_verify_token;


                $adicional = array('email_to' => $to_email,
                	               'from_email' => $from_email, 
                	               'first_name_emisor' => $efn , 
                	               'first_name_receptor' => $rfn,
                	               'token_receptor' => $rtdc,
                	               'token_emisor' => $etdc
                	                   );


				$title = 'Bauen Messenger';
				$this->notificationpush($title,$device_push_key,$message,$iduser,$scren,$adicional);
				$this->response(array("body"=>array(),"status"=>200,"message"=>"Hemos enviado tu notificación con exito"));				
			}else{
				$this->response(array("body"=>array(),"status"=>400,"message"=>"El usuario no puede recibir notificaciones en este momento,intente más tarde"));
			}
		}else{
			$this->response(array("body"=>array(),"status"=>400,"message"=>"Email no encontrado"));
		}
		//$this->debug_request($data);
	}

	public function new_request_post(){
		$data_post['user_id'] = $this->input->post('user_id');		
		$data_post['trailer_id'] = $this->input->post('trailer_id');
		$type_notification = $this->input->post('type_notification');
		$this->debug_request($data_post);
		if(isset($data_post['user_id'])){
			//$iduser = base64_decode($data_post['user_id']);
			
			$iduser = $data_post['user_id'];
			$QueryPushNotifications = '';
			if($type_notification==0 || $type_notification ==1){
				$QueryPushNotifications = "select distinct(device_push_key)device_push_key, device_type FROM trns_user_request_keys where user_id in 
				(select distinct(user_id) 
				from trns_vehicles 
				where trailer_id = ".$data_post['trailer_id']."
				and is_deleted=0 
				and vehicle_status=1) and device_push_key is not null and device_push_key != '' 
				and is_deleted = 0 and is_blocked=0;";
			}else{
				$QueryPushNotifications = "select distinct(device_push_key)device_push_key , device_type FROM trns_user_request_keys where user_id = ".$data_post['user_id']."
				and device_push_key is not null and device_push_key != '' 
				and is_deleted = 0 and is_blocked=0;";
			}
			
			
			$dataResponse = $this->GlobalMod->query($QueryPushNotifications);
			$dataN_Android = array();
			$dataN_iOs = array();
			foreach ($dataResponse as $key ) {
				if($key->device_type==1){
					array_push($dataN_Android,$key->device_push_key);
				}else{
					array_push($dataN_iOs,$key->device_push_key);
				}				
			}

			if($type_notification==0 || $type_notification ==1){
				//#iOs
				$dataFirebaseIOS = array (		
					'message' => 'New shipment available. Press here to see the details.',
					'type_notification' => $type_notification,
					'request_id' => $data_post['request_id'],				
					'vibrate'	=> 1,
					'sound'		=> 1,
					'user_id' => $iduser,				
				);

				$notificationIOS = array(
					'notification' => array(
					'title' => 'Bauen',
					'body' => 'New shipment available. Press here to see the details.'
				));

				//#Android
				$notificationAndroid = array (
					'notification' => array(
						'title' => 'Bauen',
						'message'=> 'New shipment available. Press here to see the details.',
						'body'=> 'New shipment available. Press here to see the details.'
					)
				);

				$dataFirebaseAndroid = array (	
						'message' => 'New shipment available. Press here to see the details.',
						'type_notification' => $type_notification,
						'request' => $data_post['request_id'],				
						'vibrate'	=> 1,
						'sound'		=> 1,
						'user_id' => $iduser,
					
				);
			}else{
				//#iOs
				$input_message = $this->input->post('message');
				$message = isset($input_message) ? $input_message : 'Gano la subasta ';
				$dataFirebaseIOS = array(
							'title' => 'Bauen',
							'body' => $message
						);
			
				$notificationIOS["request_id"] = $data_post['request_id'];
				$notificationIOS["user_id"] = $data_post['user_id'];
				$notificationIOS["message"] = $message;
				$notificationIOS["type_notification"] = $type_notification;
				$notificationIOS["vibrate"] = 1;
				$notificationIOS["sound"] = 1;

				//#Android
				$notificationAndroid = array(
					'title' => 'Bauen',
					'message' => $message,
					'body' => $message,
				);

				$dataFirebaseAndroid["message"] = $message;
				$dataFirebaseAndroid["notification_type"] = $type_notification;
				$dataFirebaseAndroid["request_id"] = $data_post['request_id'];
				$dataFirebaseAndroid["user_id"] = $data_post['user_id'];
			}
			

			$result = $this->pushFirebase('Bauen',$dataN_Android,$dataFirebaseAndroid,$notificationAndroid);
			$result = $this->pushFirebase('Bauen',$dataN_iOs,$dataFirebaseIOS,$notificationIOS);
			$this->response(array('status'=>200,'message'=>'sus notificaciones han sido enviadas con exito.','body'=>$dataN));
		}else{
			$this->response(array('status'=>400,'message'=>'Error problemas de autentificación','body'=>$result));
		}
		
	}

	private function pushFirebase($title='Bauen',$divecesKeys=array(),$dataFirebase=array(),$notificationFirebase=array()){
        $result=array();
        $url = 'https://fcm.googleapis.com/fcm/send';
    
        $fields = array (
            'registration_ids' => $divecesKeys,
            'notification' => $notificationFirebase,
            'data' => $dataFirebase
        );
        
        $headers = array(
            'Authorization: key=' . $this->fbkey,
            'Content-Type: application/json'
		);	
		
		$this->debug_request($fields);
		$this->debug_request($headers);
    
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
	
    public function notificationpush($title='Bauen',$device_push_key=array(),$message='',$user_id=0,$screen,$adicional){
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
            'Authorization: key=' . $this->fbkey,
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
			$userdata = $this->GlobalMod->Join('ur.*','trns_users us','trns_user_request_keys ur',$joins,$conditionals);
			if(count($userdata)==0){
				$this->data_body = array();
				$this->data_status =401;
				$this->data_message = "Los parametros establecidos no cumplen nuestras condiciones.";
			}
			
		}
	}


	public function debug_request($array=array()){		
			$log =json_encode($array).PHP_EOL;
			file_put_contents('./logs/debug_user'.date("j.n.Y").'.json', $log, FILE_APPEND);		
	}
}