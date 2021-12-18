<?php

namespace App\Http\Controllers\Buy_Sell\web;

use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\model\common_function;
use App\model\user;
use App\model\variable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use Cookie;


//
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Firebase;



class homecontroller extends Controller {

    private $result = 0;
    private $message = 0;
    private $details = 0;
    private $img_url = 0;


    //
    private $firebase;
    private $fbauth;
    private $fbuser_details;
    private $fbnewuser;


    public function __construct() {
        $this->user = new user();
        $this->common_model = new common_function();
        $this->variable = new variable();


        // Firebase conections
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/firebase_credentials.json');
        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();

        $this->fbauth = $this->firebase->getAuth();

    }

    public function index() {
        // if (Session::has('web_user_id')) {
        //     return redirect('/shipper');
        // } else {
        //     return view("bauenfreight.home");
        // }
        return view("bauenfreight.home");
        
    }

    public function about() {
        return view("bauenfreight.about");
    }

    public function how_it_works() {
//         return "test job";
        return view("bauenfreight.how_it_works");
    }

    public function carriers() {
        return view("bauenfreight.carriers");
    }

    public function shipper2() {
        $count_requests_data = array(
            'user_id' => Session::get('web_user_post_id'),
            'get_result_type' => 1
        );
        $count_requests = $this->user->count_requests($count_requests_data);
        $get_requests_data = array(
            'user_id' => Session::get('web_user_post_id'),
            'get_result_type' => 0
        );
        $get_requests_data = $this->user->get_requests($get_requests_data);
        // return array($count_requests);
        return view("bauenfreight.shipper", ['count_requests' => $count_requests, 'get_requests_data' => $get_requests_data]);
    }


    public function verification_screen() {
        return view("bauenfreight.verification_screen");
    }
	
	
	public function validator_session() {
		
		if(Session::get('web_super_parent_id') > 0 ) {
			
			return Session::get('web_super_parent_id');
			
		} else {
			
			header('Location: '.url('/logout'));
			exit();
			
		}	
		
	}

   public function shipper() {

        // $user_id = Session::get('web_user_post_id');
        $user_id = $this->validator_session();

        $count_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => 1
        );
        $count_requests = $this->user->count_requests($count_requests_data);

        $get_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => 0
        );
        //return Session::get('web_user_post_id');

        $get_requests_data = $this->user->get_requests($get_requests_data);
        //return $get_requests_data;
        $get_user_request_keys_data = $this->user->get_user_request_keys($user_id);
        //return dd($get_requests_data);

        /* Trailers */
        $trailers = $this->common_model->get_table("trns_trailers");

        $device_type=$device_unique_code=$user_request_key=null;
        if (count($get_user_request_keys_data)>0){
              $user_request_key_object = $get_user_request_keys_data[0];
              $device_type = $user_request_key_object->device_type;
              $device_unique_code = $user_request_key_object->device_unique_id;
              $user_request_key = $user_request_key_object->request_key;


          }

          $user_data= array(
            'user_id'=> $user_id,//Session::get('web_user_post_id')
            'device_type' =>$device_type,
            'device_unique_code' => $device_unique_code,
            'user_request_key' => $user_request_key
          );




        
 
        return view("bauenfreight.shipper", [
            'count_requests' => $count_requests,
            'get_requests_data' => $get_requests_data,
            'trailers' => $trailers,
            'user_data'=>$user_data
        ]);
        //return view("bauenfreight.shipper_disabled");
    }


    public function transit_requests(Request $request) {

        // $user_id = Session::get('web_user_post_id');
        $user_id = $this->validator_session();
		
		$search = $request->input("search");

        $get_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => array(2,3,5,6,7,8,9,10,11,12),
            'ignore_deleted' => true,
			'order' => array('orderBy' => 'requests.pickup_date',
							 'type'    => 'asc',	
			                 ),
			'search' => $search				 
        );
        //return Session::get('web_user_post_id');

        $get_requests_data = $this->user->get_requests($get_requests_data);
        //return $get_requests_data;
        $get_user_request_keys_data = $this->user->get_user_request_keys($user_id);
        if( count($get_requests_data) > 0 ) {
            for ($i = 0; $i < count($get_requests_data); $i++) {
                $transporter_id = $get_requests_data[$i]->transporter_id;
                if($transporter_id != '') {
                    $transporter = $this->common_model->find_details_table_by_field('trns_users', 'user_id', $transporter_id, 1);
                    if($transporter) {
                        $get_requests_data[$i]->company_name = $transporter->company_name;
                    }
                }
            }
        }
        //return dd($get_requests_data);


        $device_type=$device_unique_code=$user_request_key=null;
        if (count($get_user_request_keys_data)>0){
              $user_request_key_object = $get_user_request_keys_data[0];
              $device_type = $user_request_key_object->device_type;
              $device_unique_code = $user_request_key_object->device_unique_id;
              $user_request_key = $user_request_key_object->request_key;
          }

          $user_data= array(
            'user_id'=> $user_id,//Session::get('web_user_post_id')
            'device_type' =>$device_type,
            'device_unique_code' => $device_unique_code,
            'user_request_key' => $user_request_key
          );
        
        //   dd($get_requests_data);
        return view("bauenfreight.transit_requests", ['get_requests_data' => $get_requests_data,'user_data'=>$user_data]);
        //return view("bauenfreight.shipper_disabled");
    }
    
    
    public function completed_requests_search(Request $request) {

        $user_id = $this->validator_session();
		
		$search = $request->input("search");
		
        // $user_id = Session::get('web_user_post_id');

        $get_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => 13,
            'ignore_deleted' => true,
			'order' => array('orderBy' => 'requests.pickup_date',
							 'type'    => 'desc',
			                 ),
			'search' => $search			 
        );

        $get_requests_data = $this->user->get_requests($get_requests_data);
        
    /*
        echo "<pre>";
        print_r($get_requests_data);
        echo "</pre>";*/

        if( count($get_requests_data) > 0 ) {
            for ($i = 0; $i < count($get_requests_data); $i++) {
                $transporter_id = $get_requests_data[$i]->transporter_id;
                if($transporter_id != '') {
                    $transporter = $this->common_model->find_details_table_by_field('trns_users', 'user_id', $transporter_id, 1);
                    if($transporter) {
                        $get_requests_data[$i]->company_name = $transporter->company_name;
                    }
                }
            }
        }

        $get_user_request_keys_data = $this->user->get_user_request_keys($user_id);

        $device_type=$device_unique_code=$user_request_key=null;
        if (count($get_user_request_keys_data)>0){
              $user_request_key_object = $get_user_request_keys_data[0];
              $device_type = $user_request_key_object->device_type;
              $device_unique_code = $user_request_key_object->device_unique_id;
              $user_request_key = $user_request_key_object->request_key;
          }

          $user_data= array(
            'user_id'=> $user_id,
            'device_type' =>$device_type,
            'device_unique_code' => $device_unique_code,
            'user_request_key' => $user_request_key
          );
        
        return view("bauenfreight.request_search_trans", ['get_requests_data' => $get_requests_data,'user_data'=>$user_data]);
    }

    public function completed_requests(Request $request) {

        $user_id = $this->validator_session();
		
		$search = $request->input("search");
		
        // $user_id = Session::get('web_user_post_id');

        $get_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => 13,
            'ignore_deleted' => true,
			'order' => array('orderBy' => 'requests.pickup_date',
							 'type'    => 'desc',
			                 ),
			'search' => $search			 
        );

        $get_requests_data = $this->user->get_requests($get_requests_data);
        
    /*
        echo "<pre>";
        print_r($get_requests_data);
        echo "</pre>";*/

        if( count($get_requests_data) > 0 ) {
            for ($i = 0; $i < count($get_requests_data); $i++) {
                $transporter_id = $get_requests_data[$i]->transporter_id;
                if($transporter_id != '') {
                    $transporter = $this->common_model->find_details_table_by_field('trns_users', 'user_id', $transporter_id, 1);
                    if($transporter) {
                        $get_requests_data[$i]->company_name = $transporter->company_name;
                    }
                }
            }
        }

        $get_user_request_keys_data = $this->user->get_user_request_keys($user_id);

        $device_type=$device_unique_code=$user_request_key=null;
        if (count($get_user_request_keys_data)>0){
              $user_request_key_object = $get_user_request_keys_data[0];
              $device_type = $user_request_key_object->device_type;
              $device_unique_code = $user_request_key_object->device_unique_id;
              $user_request_key = $user_request_key_object->request_key;
          }

          $user_data= array(
            'user_id'=> $user_id,
            'device_type' =>$device_type,
            'device_unique_code' => $device_unique_code,
            'user_request_key' => $user_request_key
          );
        
        return view("bauenfreight.completed_requests", ['get_requests_data' => $get_requests_data,'user_data'=>$user_data]);
    }

    public function done_requests() {

        $user_id = $this->validator_session();
        // $user_id = Session::get('web_user_post_id');

        $get_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => array(4, 14)
        );

        $get_requests_data = $this->user->get_requests($get_requests_data);
        $get_user_request_keys_data = $this->user->get_user_request_keys($user_id);

        $device_type=$device_unique_code=$user_request_key=null;
        if (count($get_user_request_keys_data)>0){
              $user_request_key_object = $get_user_request_keys_data[0];
              $device_type = $user_request_key_object->device_type;
              $device_unique_code = $user_request_key_object->device_unique_id;
              $user_request_key = $user_request_key_object->request_key;
          }

          $user_data= array(
            'user_id'=> $user_id,
            'device_type' =>$device_type,
            'device_unique_code' => $device_unique_code,
            'user_request_key' => $user_request_key
          );
        
        return view("bauenfreight.done_requests", ['get_requests_data' => $get_requests_data,'user_data'=>$user_data]);
    }

    public function contacts() {
        return view("bauenfreight.contacts");
    }

    public function your_quote() {
		
		$user_id = $this->validator_session();

        $trailers_id = $this->common_model->get_table_sort_by("trns_trailers", "name", "ASC");
        $loadtypes_id = $this->common_model->get_table_sort_by("trns_loadtypes", "load_name", "ASC");
        
        return view("bauenfreight.your_quote", ['trailers_id' => $trailers_id, 'loadtypes_id' => $loadtypes_id]);
    }

    // Subadmins
    public function subadmins_list() {
		
        if(Session::has('is_user_super_admin') && Session::get('is_user_super_admin') == 0) {
		
			$user_id = $this->validator_session();

			$subadmins = $this->user->get_subadmins($user_id);

			return view("bauenfreight.subadmins_list", ['subadmins' => $subadmins]);
			
		} else {
			
			Session::put('message', 'Usted no esta autorizado para ingresar ha esta area.');
			
			return redirect()->back();
			
		}	
    }

    public function add_subadmin(Request $data) {
        $validator = Validator::make($data->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email'
            ]);
            
        if ($validator->fails()) {
            Session::put('message', 'Ocurrió un error al validar los campos.');
        } else {
            // Validar si existe el email
            $check_data = array(
                'email' => $data->input('email'),
                'phone_no' => $data->input('phone_no'),
                'get_result_type' => 3
            );
            
            $check_result = $this->user->get_user($check_data);

            if (empty($check_result)) {
                $user_id = Session::get('web_user_post_id');

                //Insert user in laravel
                $digits = 6;
                $verify_code = rand(pow(10, $digits-1), pow(10, $digits)-1);

                $this->value = array(
                    'first_name' => $data->input('first_name'),
                    'last_name' => $data->input('last_name'),
                    'email' => $data->input('email'),
                    'is_user_verify' => 0,
                    'is_email_verify' => 0,
                    'email_verify_token' => rand(0, 1000),
                    'user_type' => 0,
                    'create_date' => date('Y-m-d H:i:s'),
                    'update_date' => date('Y-m-d H:i:s'),
                    'verification_code' => $verify_code,
                    'super_parent_id' => $user_id,
                    'phone_no' => $data->input('phone_no'),
                );

                $this->details = $this->common_model->insert_table('trns_users', $this->value);
                
                if ($this->details > 0) {
                    $link = url('subadmin-active-account-' . time() . '-' . base64_encode($data->input('email')) . '-' . base64_encode($verify_code). '-' . base64_encode('+51' . $data->input('phone_no')). '-' . base64_encode($data->input('first_name') . ' ' . $data->input('last_name')));
               
                    $email_data = array(
                        'name' => $data->input('first_name') . ' ' . $data->input('last_name'),
                        'active_account' => $link,
                        'verification_code' => $verify_code
                    );

                    $email = $data->input('email');
                    $phone_no = $data->input('phone_no');
                    $language_id = 1;

                    Mail::send('email.verifying_subadmin_email', ['data' => $email_data], function($message) use ($email) {
                        $message->from('info@bauenfreight.com', 'Bauen - Registro');
                        $message->to($email)->subject('Queremos verificar su identidad por su propia seguridad');
                    });

                    Session::put('message', "Un correo de verificación ha sido enviado al correo del administrador.");

                    /* $encode64 = base64_encode($phone_no.'-'.$language_id.'-'.$verify_code);
                    $endpoint = "https://www.bauenfreight.com/api/sendsmsapi/$phone_no/$language_id/$verify_code/".base64_encode($phone_no.$verify_code);
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('GET', $endpoint, ['query' => []]);

                    // put 

                    Session::put('web_user_id', $this->details);
                    Session::put('web_user_post_id', $this->details);
                    Session::put('web_user_name', $data->input('first_name'));
                    Session::put('web_user_email', $email);

                    Cookie::queue('web_user_id', $this->details, 525600);
                    Cookie::queue('web_user_post_id', $this->details, 525600);
                    Cookie::queue('web_user_name', $data->input('first_name'), 525600);
                    Cookie::queue('web_user_email', $email, 525600); */


                } else {
                    Session::put('message', 'Ocurrió un error al intentar crear el administrador. Por Favor, inténtelo nuevamente.');
                }

            } else {
                Session::put('message', 'El correo electrónico y/o el número de telefono ya están en uso.');
            }
        }

        return redirect('/subadmins');
        //return redirect('/verification');
    }

    public function delete_subadmin(Request $request) {
        if(!$request->has('user_id')) {
            Session::put('message', 'No se pudo eliminar el administrador (104).');
            return redirect('/subadmins');
        }

        $subadmin_id = $request->input('user_id');

        $user_id = Session::get('web_user_post_id');

        $check_data = array(
            'user_id' => $subadmin_id,
            'get_result_type' => 2
        );

        $get_user_existence = $this->user->get_user($check_data);

        if(!empty($get_user_existence)) {
            if($get_user_existence->super_parent_id == $user_id) {
                $value = array(
                    'is_deleted' => 1,
                    'deleted_date' => date('Y-m-d H:i:s')
                );

                $result = $this->common_model->update_table('trns_users', $value, 'user_id', $get_user_existence->user_id);
                
                if ($result > 0) {
                    Session::put('message', 'El administrador ha sido eliminado con éxito.');
                } else {
                    Session::put('message', 'No se pudo eliminar el administrador (103).');
                }
            } else {
                Session::put('message', 'No se pudo eliminar el administrador (102).');
            }
        } else {
            Session::put('message', 'No se pudo eliminar el administrador (101).');
        }

        return redirect("/subadmins");
    }

    public function subadmin_active_account($time, $email, $code, $phone, $namelastname, Request $request) {
        $valid_data = false;
        $decoded_email = base64_decode($email);
        $decoded_code = base64_decode($code);
        $decoded_phone = base64_decode($phone);
        $decoded_namelastname = base64_decode($namelastname);

        // Find user by email
        $check_data = array(
            'email' => $decoded_email,
            'get_result_type' => 4
        );

        $get_user_existence = $this->user->get_user($check_data);

        if(empty($get_user_existence)) {
            Session::put('message', 'El enlace es inválido (101).');
        } else {
            if(
            $get_user_existence->verification_code != $decoded_code || 
            $get_user_existence->is_user_verify == 1 || 
            $get_user_existence->is_email_verify == 1) { // Check if code match and isn't already verified
                Session::put('message', 'El enlace es inválido (102).');
            } else {
                $valid_data = true;
            }
        }

        // Password is sent
        if($request->has('password')) {
            
            //Insert user in Firebase

                $this->fbuser_details = [
                    'email' => $decoded_email,
                    'emailVerified' => false,
                    'phoneNumber' => $decoded_phone,
                    'password' => md5($request->input('password')),
                    'displayName' => $decoded_namelastname,
                ];
    
                try {

                    $this->fbnewuser = $this->fbauth->createUser($this->fbuser_details);

                    //update user in bd

                    $value = array(
                        'password' => md5($request->input('password')),
                        'showpass'=>$request->input('password'),
                        'is_user_verify' => 1,
                        'is_email_verify' => 1,
                        'email_verify_token' => rand(0, 1000),
                        'update_date' => date('Y-m-d H:i:s'),
                        'firebase_id' => $this->fbnewuser->uid,
                    );


                    $result = $this->common_model->update_table('trns_users', $value, 'user_id', $get_user_existence->user_id);

                    if ($result > 0) {
                        Session::put('web_user_id', $get_user_existence->user_id);
                        Session::put('web_user_post_id', $get_user_existence->user_id);
                        Session::put('web_super_parent_id', $get_user_existence->super_parent_id);
                        Session::put('web_user_name', $get_user_existence->first_name);
                        Session::put('web_user_email', $get_user_existence->email);
                        Session::put('is_user_verify', 1);

                        Cookie::queue('web_user_id', $get_user_existence->user_id, 525600);
                        Cookie::queue('web_user_post_id', $get_user_existence->user_id, 525600);
                        Cookie::queue('web_super_parent_id', $get_user_existence->super_parent_id, 525600);
                        Cookie::queue('web_user_name', $get_user_existence->first_name, 525600);
                        Cookie::queue('web_user_email', $get_user_existence->email, 525600);
                        Cookie::queue('is_user_verify', 1, 525600);
                        Session::put('message', 'Cuenta activada de manera satisfactoria.');
                        return redirect('shipper');
                    } else {
                        Session::put('message', 'No pudimos procesar la solicitud. Por favor, inténtelo nuevamente.');
                    } 


                } catch(\Kreait\Firebase\Exception\AuthException | \Kreait\Firebase\Exception\InvalidArgumentException $e) {
                    Session::put('message', 'Ocurrió un error al intentar crear el administrador en firebase.');
                    return redirect('/subadmins');
                } 

        }

        return view("bauenfreight.subadmin_active_account", ['valid_data' => $valid_data]);
    }
    // Close Subadmins

    public function request_list(Request $request) {

        $user_id = $this->validator_session();
		
		$search = $request->input("search");

        $get_requests_data = array(
            'user_id' => $user_id,
            'get_result_type' => array(0, 1),
            'ignore_deleted' => true,
			'order' => array('orderBy' => 'requests.pickup_date',
							 'type'    => 'asc',	
			                 ),
			'search' => $search
        );

        $get_requests_data = $this->user->get_requests($get_requests_data);
        if( count($get_requests_data) > 0 ) {
            for ($i = 0; $i < count($get_requests_data); $i++) {
                $request_id = $get_requests_data[$i]->request_id;

                $bids_count = $this->common_model->count_all_table('trns_request_bids', 'request_id', $request_id, 1);
                $get_requests_data[$i]->bids_count = $bids_count;
            }
        }

        // $newData = array();
        // $c=0;
        // foreach ($get_requests_data as $key ) {
        //  if($key->is_deleted != 1 || $key->is_deleted != '1'){
        //     array_push($newData, $key);
        //     $c++;
        //  }
        // }
        
        return view("bauenfreight.request_list", ['get_requests_data' => $get_requests_data]);
    }

    public function request_list_details($data) {
        
		$user_id = $this->validator_session();

        $val = explode("||", base64_decode($data));
        $id = $val[0];
        //return $id;
        $get_requests_data = array(
            'request_id' => $id,
            'user_id' => $user_id,
            'get_result_type' => 15,
        );

        $get_requests_data = $this->user->get_requests($get_requests_data);
		
		
        $get_bid_list = $this->user->get_requests_details($id);
		$get_bid_list_count = $this->user->get_requests_details($id, 1);
		
        //return $get_bid_list;
        if( is_object($get_requests_data) ) {
            $trailer_details = (!isset($get_requests_data->trailer_id)) ? null : $this->common_model->find_details_table_by_field('trns_trailers', 'trailer_id', $get_requests_data->trailer_id, 1);

            $driver = (!isset($get_requests_data->driver_id)) ? null : $this->common_model->find_details_table_by_field('trns_users', 'user_id', $get_requests_data->driver_id, 1);
    
            $vehicle = (!isset($get_requests_data->vehicle_id)) ? null : $this->common_model->find_details_table_by_field('trns_vehicles', 'vehicle_id', $get_requests_data->vehicle_id, 1);
    
            $transporter = (!isset($get_requests_data->transporter_id)) ? null : $this->common_model->find_details_table_by_field('trns_users', 'user_id', $get_requests_data->transporter_id, 1);
			
			$loadtypes_details = (!isset($get_requests_data->loadtype_id)) ? null : $this->common_model->find_details_table_by_field('trns_loadtypes', 'loadtype_id', $get_requests_data->loadtype_id, 1);
        } else {
            $trailer_details = null;

            $driver = null;
    
            $vehicle = null;
    
            $transporter = null;
			
			$loadtypes_details = null;
        }


        $request_track = $this->common_model->find_details_table_by_field('trns_request_driver_locations', 'request_id', $id, 2);

        // dd($get_bid_list);
        return view("bauenfreight.request_list_details", [
            'requests_data' => $get_requests_data,
            'get_bid_list' => $get_bid_list,
			'get_bid_list_count' => $get_bid_list_count,
            'trailer_details' => $trailer_details,
            'driver' => $driver,
            'vehicle' => $vehicle,
            'transporter' => $transporter,
            'request_track' => $request_track,
			'loadtypes_details' => $loadtypes_details,
        ]);
    }

    public function message() {
        $this->value = array(
            'user_id' => Session::get('web_user_id'),
            'get_result_type' => 1
        );
        $this->details = $this->user->fiend_chat_list($this->value);
        // return  $this->details;
        return view("bauenfreight.message", ['message_list' => $this->details]);
    }

    public function message_details(Request $data) {
        $this->value = array(
            'chat_id' => $data->input('chat_id'),
            'get_result_type' => 1
        );
        $this->details = $this->user->chat_details($this->value);
        if (!empty($this->details)) {
            $this->result = $this->variable->message[1];
            $this->message = 'message get success';
        } else {
            $this->result = $this->variable->message[0];
            $this->message = 'message details not found';
        }
        return Response::make([
                    'result' => $this->result,
                    'message' => $this->message,
                    'details' => $this->details
        ]);
    }

    public function write_message(Request $data) {
        $result = 0;
        $validator = Validator::make($data->all(), [
                    'chat_id' => 'required',
                    'user_id' => 'required',
                    'message_data' => 'required'
        ]);
        if ($validator->fails()) {
            $this->result = $this->variable->message[0];
            $this->message = 'Please enter all value';
        } else {
            $this->value = array(
                'chat_id' => $data->input('chat_id'),
                'user_id' => $data->input('user_id'),
                'message_data' => $data->input('message_data'),
                'create_date' => date('Y-m-d H:i:s')
            );
            $result = $this->common_model->insert_table('trns_messages', $this->value);
            if ($result > 0) {
                $this->result = $this->variable->message[1];
                $this->message = 'Mensaje enviado satisfactoriamente';
            } else {
                $this->result = $this->variable->message[0];
                $this->message = 'Envio de mensaje fallido';
            }
        }
        return Response::make([
                    'result' => $this->result,
                    'message' => $this->message,
                    'details' => $result
        ]);
    }

    public function profile() {
        $this->img_url = $this->variable->message[2];
        $this->value = array(
            'user_id' => Session::get('web_user_id'),
            'get_result_type' => 2
        );
        $this->details = $this->user->get_user($this->value);
        if (!empty($this->details)) {
            return view("bauenfreight.profile", ['details' => $this->details, 'img_url' => $this->img_url]);
        }
        return view("bauenfreight.profile");
    }

    public function update_profile(Request $data) {
        $this->value = array(
            'user_id' => Session::get('web_user_id'),
            'about_us' => $data->input('about_us'),
            'get_result_type' => 2
        );
        $this->details = $this->user->update_user_about($this->value);
        switch ($this->details) {
            case 1:
                Session::put('message', 'La información ha sido actualizada correctamente.');
                break;
            case 0:
                Session::put('message', 'Algo salió mal, inténtelo nuevamente.');
                break;
            case -1:
                Session::put('message', 'Por favor, llenar todos los campos correctamente.');
                break;
        }
        return redirect()->back();
    }

    public function upload_user_image(Request $data) {
        $validator_img = Validator::make($data->all(), ['user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
        $result = 0;
        if ($validator_img->fails()) {
            Session::put('message', 'This profile image not update .');
            return redirect('/profile');
        } else {
            $this->value = array(
                'user_id' => Session::get('web_user_id'),
                'get_result_type' => 2
            );
            $this->details = $this->user->get_user($this->value);
            $path = $this->variable->message[8] . $this->details->image;
            if (file_exists($path) && !empty($this->details->image)) {
                unlink($path);
            }
            $file = $data->file('user_image');
            $user_image = $file->getClientOriginalExtension();
            $user_image = time() . rand() . '.' . $user_image;
            //return $user_image;
            $destinationPath = $this->variable->message[8];
            $file->move($destinationPath, $user_image);
            $user_images_data = array(
                'user_id' => Session::get('web_user_id'),
                'image' => $user_image,
                'update_date' => date('Y-m-d H:i:s')
            );
            $result = $this->common_model->update_table('trns_users', $user_images_data, 'user_id', Session::get('web_user_id'));
        }
        if ($result > 0) {
            Session::put('message', 'This profile image  update succesfuly.');
            return redirect('/profile');
        } else {
            Session::put('message', 'This profile image not updated .');
            return redirect('/profile');
        }
    }

    public function past_bids() {
        // return view("bauenfreight.past_bids");
        $get_requests_data = array(
            'user_id' => Session::get('web_user_post_id'),
            'get_result_type' => 0
        );
        $get_requests_data = $this->user->get_requests($get_requests_data);
        return view("bauenfreight.past_bids", ['get_requests_data' => $get_requests_data]);
    }


    public function total_notification() {
        $this->value = array(
            'user_id' => Session::get('web_user_id'),
            'get_result_type' => 1
        );
        $this->details = $this->user->total_notification($this->value);
        if (!empty($this->details)) {
            $this->result = $this->variable->message[1];
            $this->message = "Notification found";
        } else {
            $this->result = $this->variable->message[0];
            $this->message = "Notification not found";
        }
        return Response::make([
                    'result' => $this->result,
                    'message' => $this->message,
                    'details' => $this->details
        ]);
    }

    public function request_accept($id,$user,$bid) {
        $bid_model = $this->common_model->find_details_table_by_field('trns_request_bids', 'bid_id', $bid, 1);

        $user_images_data = array(
            'granted_amount' => $bid_model->bid_amount,
            'transporter_id' => $bid_model->creater_id,
            'request_status' => 2,
            'update_date' => date('Y-m-d H:i:s'),
            'bid_id'=>$bid
        );

        $this->details = $this->common_model->update_table('trns_requests', $user_images_data, 'request_id', $id);
        if ($this->details > 0) {
            // Update request status track
            $actual_request = $this->common_model->find_details_table_by_field('trns_requests', 'request_id', $id, 1);

            $request_track = json_decode($actual_request->request_status_track);

            $actual_track = array(
                'request_status' => 2, // 2 = bid accepted by customer
                'create_date' => date('Y-m-d H:i:s')
            );

            array_push($request_track, $actual_track);

            $request_track_json = json_encode($request_track);
            
            $this->common_model->update_table('trns_requests', array('request_status_track' => $request_track_json), 'request_id', $id);
            
            $this->result = $this->variable->message[1];
            $this->message = "Request status updated successfully";
        } else {
            $this->result = $this->variable->message[0];
            $this->message = "Request status update failure";
        }
        $endpoint = "https://www.bauenfreight.com/api/user/newrequest";
        
                $client = new \GuzzleHttp\Client();
                $response = $client->Request('POST', $endpoint,
                [
                    'form_params' => [
                        'user_id' => $user,
                        'trailer_id' => 0,
                        'request_id' =>$id,
                        'type_notification'=> 2,
                        'message' => 'Ganó la carga Nro.'.$id.'. Asigne chofer y camión.'
                    ]
                ]);
        $this->details = $this->common_model->update_table('trns_request_bids', array('bid_status'=>2), 'bid_id', $bid);
        Session::put('message', 'Solicitud aceptada con éxito. Esperando asignación de conductor y vehículo');
        return redirect('/transit-requests');
        /* return Response::make([
          'result' => $this->result,
          'message' => $this->message,
          'details' => $this->details
          ]); */
    }

    public function get_trailers() {
        $this->details = $this->user->get_trailers();
        if (!empty($this->details)) {
            $this->result = $this->variable->message[1];
            $this->message = "Get trailers list  successfully";
        } else {
            $this->result = $this->variable->message[0];
            $this->message = "Get trailers list failure";
        }
        return Response::make([
                    'result' => $this->result,
                    'message' => $this->message,
                    'details' => $this->details
        ]);
        //return $this->details;
    }

    public function post_request(Request $data) {

        $user_id = $this->validator_session();

        if($user_id == null || $user_id == '') {
            $this->message = 'No se ha podido crear la cotización. Por favor, inténtelo nuevamente en unos minutos.';

            Session::put('message', $this->message);
            return redirect('/your-quote');
        }

        $validator_img = Validator::make($data->all(), [
                    'pickup_location' => 'required',
                    'dropoff_location' => 'required',
                    'pickup_date' => 'required',
                    'pickup_time' => 'required',
                    'trailer_id' => 'required',
                    'loadtype_id' => 'required',
                    'weight' => 'required',
                    'size' => 'required',
                    'description' => 'required',
                    'pick_long' => 'required',
                    'pick_lat' => 'required',
                    'pick_place_id' => 'required',
                    'drop_long' => 'required',
                    'drop_lat' => 'required',
                    'drop_place_id' => 'required'
        ]);
        if ($validator_img->fails()) {
            $this->result = $this->variable->message[0];
            // $this->message = $this->variable->message[30];
            $this->message = 'Ocurrió un error al validar los campos.';
            $this->details = $validator_img->errors();
        } else {
                $origin_zip = $data->input('pick_lat');
				$origin_country = $data->input('pick_long');
				$dest_zip = $data->input('drop_lat');
                $dest_country = $data->input('drop_long');

            
			
			

            $endpoint = "https://www.bauenfreight.com/api/request/distance/".$origin_zip."/".$origin_country."/".$dest_zip."/".$dest_country;
            $client = new \GuzzleHttp\Client();
            $response = $client->Request('GET', $endpoint , [
                'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json'
                ]]);
            
            $result = json_decode($response->getBody()->getContents()); 
			
			date_default_timezone_set('America/Lima');	
			
			$ct_count = $this->common_model->count_all_table('trns_user_transporter', 'id_customer', $user_id, 1);
			
			if($ct_count > 0) {
				$is_premium = '1';
			} else {
				$is_premium = '0';
			}
			
            $this->value = array(
                'user_id' => $user_id,
                'creater_id' => Session::get('web_user_id'),
                'pickup_location' => $data->input('pickup_location'),
                'dropoff_location' => $data->input('dropoff_location'),
                'pickup_date' => date('Y-m-d H:i:s', strtotime($data->input('pickup_date'))),
                'pickup_time' => $data->input('pickup_time'),
                'trailer_id' => $data->input('trailer_id'),
                'loadtype_id' => $data->input('loadtype_id'),
                'request_amount' => $data->input('request_amount'),
                'weight' => $data->input('weight'),
                'size' => $data->input('size'),
                'description' => $data->input('description'),
                'create_date' => date('Y-m-d H:i:s'),
                'pickup_place_id' => $data->input('pick_place_id'),
                'pickup_latitude' => $origin_zip ,
                'pickup_longitude' => $origin_country,
                'dropoff_place_id' => $data->input('drop_place_id'),
                'dropoff_latitude' => $dest_zip,
                'dropoff_longitude' => $dest_country,
                'route_duration'=> $result->time,
                'route_distance' => $result->distance,
                'request_amount' => 0,
				'is_premium' => $is_premium,
            );


            
                
            $get_map_data['markers'][0] = array(
                'lat' => $this->value['pickup_latitude'],
                'long' => $this->value['pickup_longitude'],
                'place' => 'P',
            );
            $get_map_data['markers'][1] = array(
                'lat' => $this->value['dropoff_latitude'],
                'long' => $this->value['dropoff_longitude'],
                'place' => 'D',
            );
            $get_map = $this->common_model->get_map_by_lng_lat($get_map_data);
            $temp = 0;
            if (!empty($get_map)) {
                $this->value['request_image'] = $get_map;
				
				//ingreso de fecha de vencimiento de request
				
				if($data->input('close_time') != "") {
					$this->value['close_bid_time'] = $data->input('close_time');
				}
				
				//adicionales carga ancha 
				
				$carga = $data->input('size');
				$ancho_carga = explode('x',$carga);
				$ancho_carga = $ancho_carga[1];
				
				//adicionales carga largo
				
				$largo_carga = explode('x',$carga);
				$largo_carga = $largo_carga[0];
				
				//adicionales carga alto
			
				$alto_carga = explode('x',$carga);
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
					$this->value['additional'] = $adicionales;
				} 
                
                if($temp==0){
                    $this->details = $this->common_model->insert_table('trns_requests', $this->value);
                    $temp++;
                    $endpoint = "https://www.bauenfreight.com/api/user/newrequest";
                    $client = new \GuzzleHttp\Client();
                    $response = $client->Request('POST', $endpoint,
                    [
                        'form_params' => [
                            'user_id' => base64_encode($user_id),
                            'trailer_id' => $data->input('trailer_id'),
                            'request_id' =>$this->details,
                            'type_notification'=> 0                            
                        ]
                    ]);
                }           
                if ($this->details > 0) {
                    $this->result = $this->variable->message[1];
                    $this->message = 'Su pedido ha sido ingresado con éxito';
                    Session::put('message', $this->message);
                    return redirect('/request-list');
                } else {
                    $this->result = $this->variable->message[0];
                    $this->message = 'Lo sentimos. No se ha podido registrar su pedido';
                }
            } else {
                $this->result = $this->variable->message[0];
                $this->message = 'No se ha podido subir la imagen';
            }
        }
        Session::put('message', $this->message);
        return redirect('/your-quote');
    }


	public function privacy_policy(){
        return view("bauenfreight.privacy_policy");
    }
    public function terms_and_conditions(){
        return view("bauenfreight.terms_and_conditions");
    }
    ///prueba request edit
    public function requestEdit($request_id, Request $data){

      $trailers_id = $this->common_model->get_table("trns_trailers");
      $dataArray =  $data->all();



     /* $this->common_model->find_table_by_field($'trns_requests', $field, $field_value, $id = null, $id_val = null)*/

      $pickup_date =  $dataArray['pickup_date'];
      $pickup_date_array = explode('-',$pickup_date);
      $dataArray['pickup_date'] =  $pickup_date_array[2]."/".$pickup_date_array[1]."/".$pickup_date_array[0];
      $pickup_time = $dataArray['pickup_time'];
      $pickup_time_array =  explode(':', $pickup_time);
      $pickup_time_array[0] = intval($pickup_time_array[0]);
      $pickup_time_array[1] = intval($pickup_time_array[1]);
      $dataArray['pickup_time'] = strval($pickup_time_array[0]).":".strval($pickup_time_array[1]);
      $dataArray['previous_request_image'] =  $dataArray['request_image'];
      
      return view("bauenfreight.request_list_edit",['data' => $dataArray,'trailers_id' => $trailers_id]);
    }
	
	public function requestEditCloseTime($request_id, Request $data){
		
		
		date_default_timezone_set('America/Lima');

		$this->value['close_bid_time'] = date('Y-m-d H:i:s');
		
		if(trim($data->input('comentarioAdelanto')) != '') {
		
			$this->value['cometary_close_bid'] = $data->input('comentarioAdelanto');
	
		}
		
		$this->details = $this->common_model->update_table('trns_requests',$this->value,'request_id',$request_id);
		
		if ($this->details > 0) {    
                    $this->result = $this->variable->message[1];

                    //$this->message = 'request added successfully';
                    $this->message = 'Solicitud actualizada satisfactoriamente';
        } else {
                    $this->result = $this->variable->message[0];
                    //$this->message = 'request added failure';
                    $this->message = 'Error al actualizar la solicitud';
        }
		
		Session::put('message', $this->message);
		
        return redirect('/request-list-details-'.base64_encode($request_id.'||'.env(" APP_KEY ")));
	
	} 
	

    public function softdelete_requests(Request $data) {
        //return $data['request_id'];
        //return $data->input('request_id');
        $result = $this->common_model->update_table('trns_requests', array('is_deleted'=>1), 'request_id', $data->input('request_id'));
        Session::put('message', 'Solicitud eliminada');
        return redirect('/request-list');

    }

    public function update_request(Request $data) {
		
		$user_id = $this->validator_session();
		
        $validator_img = Validator::make($data->all(), [
                    'pickup_location' => 'required',
                    'dropoff_location' => 'required',
                    'pickup_date' => 'required',
                    'pickup_time' => 'required',
                    'trailer_id' => 'required',
                    'weight' => 'required',
                    'size' => 'required',
                    'description' => 'required',
                    'pick_long' => 'required',
                    'pick_lat' => 'required',
                    // 'pick_place_id' => 'required',
                    'drop_long' => 'required',
                    'drop_lat' => 'required',
                    // 'drop_place_id' => 'required'
        ]);
        if ($validator_img->fails()) {
            $this->result = $this->variable->message[0];
            $this->message = $this->variable->message[30];
            $this->message = 'Error por campo requerido';


            $this->details = $validator_img->errors();
            return $this->details;

       
        } else {
			
            $fecha = explode("/", $data->input('pickup_date'));
		
			if(count($fecha) > 1){
			  $fecha = $fecha[0].'-'.$fecha[1].'-'.$fecha[2];
			} else {
			  $fecha = $fecha[0];
			}
			
			date_default_timezone_set('America/Lima');

            $this->value = array(
                'user_id' => $user_id,
                'creater_id' => Session::get('web_user_id'),
				//'user_id' => $data->input('user_id'),
                //'creater_id' => $data->input('creater_id'),
                'pickup_location' => $data->input('pickup_location'),
                'dropoff_location' => $data->input('dropoff_location'),
                'pickup_date' => date('Y-m-d', strtotime($fecha)),
                'pickup_time' => $data->input('pickup_time'),
                'trailer_id' => $data->input('trailer_id'),
                'request_amount' => $data->input('request_amount'),
                'weight' => $data->input('weight'),
                'size' => $data->input('size'),
                'description' => $data->input('description'),
                'create_date' => date('Y-m-d H:i:s'),
                'pickup_place_id' => $data->input('pick_place_id'),
                'pickup_latitude' => $data->input('pick_lat'),
                'pickup_longitude' => $data->input('pick_long'),
                'dropoff_place_id' => $data->input('drop_place_id'),
                'dropoff_latitude' => $data->input('drop_lat'),
                'dropoff_longitude' => $data->input('drop_long')
            );
            $get_map_data['markers'][0] = array(
                'lat' => $this->value['pickup_latitude'],
                'long' => $this->value['pickup_longitude'],
                'place' => 'P',
            );
            $get_map_data['markers'][1] = array(
                'lat' => $this->value['dropoff_latitude'],
                'long' => $this->value['dropoff_longitude'],
                'place' => 'D',
            );
            $get_map = $this->common_model->get_map_by_lng_lat($get_map_data);

            // /***********************************aaaaaaaaaaaaaaaaaaaaaaaaaa**************************************** */
            if (!empty($get_map)) {
                
                $this->value['request_image'] = $get_map;
                $request_id = $data->input('request_id');

				$update_data= array('description' => 'aaaaaaaaaa');
				
				//adicionales carga ancha 
				
				$ancho_carga = $data->input('size');
				$ancho_carga = explode('x',$ancho_carga);
				$ancho_carga = $ancho_carga[1];
				$adicionales = '';
				
				if($ancho_carga > 3 && $ancho_carga <= 3.5) {
					$adicionales = '1 escolta';	
				} elseif ($ancho_carga > 3.5 && $ancho_carga <=  4) {
					$adicionales = '2 escoltas';
				} elseif ($ancho_carga > 4 ) {
				    $adicionales = '2 escoltas + policia';
				}
				
				if($adicionales != '') {
					$this->value['additional'] = $adicionales;
				} 

                $this->details = $this->common_model->update_table('trns_requests',$this->value,'request_id',$request_id);
                // return array('details' => $this->details, 'value' => $this->value);
                if ($this->details > 0) {
                    unlink('../uploads/requests/' . $data->input('previous_request_image'));
                    $this->result = $this->variable->message[1];

                    //$this->message = 'request added successfully';
                    $this->message = 'Solicitud actualizada satisfactoriamente';
                } else {
                    $this->result = $this->variable->message[0];
                    //$this->message = 'request added failure';
                    $this->message = 'Error al actualizar la solicitud';
                }
            } else {
                $this->result = $this->variable->message[0];
                $this->message = 'Image not save';
            }
        }


        Session::put('message', $this->message);
        return redirect('/request-list');
    }


}
