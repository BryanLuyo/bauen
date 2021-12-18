<?php

namespace App\Http\Controllers\Buy_Sell\auth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\model\user;
use Validator;
use Illuminate\Support\Facades\Response;
use App\model\common_function;
use App\model\variable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;
use Mail;
use Cookie;

//
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Firebase;

class web_auth_con extends Controller {

    private $result = 0;
    private $message = 0;
    private $details = 0;
    private $value = 0;
    private $validator_error = 0;

    //
    private $firebase;
    private $fbauth;
    private $fbuser_details;
    private $fbnewuser;

    public function __construct(Guard $auth) {
        $this->gurd = $auth;       
        
        $this->common_model = new common_function();
        $this->user = new user();
        $this->variable = new variable();

        // Firebase conections
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/firebase_credentials.json');
        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();

        $this->fbauth = $this->firebase->getAuth();
    }

    public function web_login() {
        if (Session::has('web_user_id')) {
            return redirect('/shipper');
        } else {
            return view("bauenfreight.signin");
        }
    }

    public function web_registration() {
    	
        if (Session::has('web_user_id')) {
            return redirect('/');
        } else {
            $industry_id = $this->common_model->get_table("trns_industrytypes");
            return view("bauenfreight.signup",['industry_id'=>$industry_id]);
        }
    }

    public function web_post_login(Request $data) {
        $validator = Validator::make($data->all(), [
                    'password' => 'required',
                    'user_email' => 'required|email']
        );

        // dd($data->input());
        
        if ($validator->fails()) {
            $this->result = $this->variable->message[0];
            $this->message = $this->variable->message[30];
            $this->validator_error = $validator->errors();
            Session::put('message', 'Este campo es obligatorio');
            return redirect('signin');
        } else {
            $this->value = array(
                'password' => md5($data->input('password')),
                'email' => $data->input('user_email'),
                'get_result_type' => 1
            );
			
			
            $this->details = $this->user->get_user($this->value);
            if (!empty($this->details)) {
                // if ($this->details->super_parent_id > 0) {
                //     Session::put('web_user_post_id', $this->details->super_parent_id);
                //     Cookie::queue('web_user_post_id', $this->details->super_parent_id, 525600);
                // } else {
                //     Session::put('web_user_post_id', $this->details->user_id);
                //     Cookie::queue('web_user_post_id', $this->details->user_id, 525600);
                // }
                //firebase login
                // try {
                //     $this->fbauth->verifyPassword($data->input('user_email'), $data->input('password'));
                // } catch (\Kreait\Firebase\Exception\AuthException | \Kreait\Firebase\Exception\Auth\InvalidPassword | \Kreait\Firebase\Exception\InvalidArgumentException $e) {
                //     Session::put('message', $e->getMessage());
                //     return redirect('signin');
                // }
				
				if($this->details->super_parent_id == 0 ){
					
					$web_super_parent_id = $this->details->user_id;
					
				} else {
					
					$web_super_parent_id = $this->details->super_parent_id;
					
				}

                Session::put('web_user_id', $this->details->user_id);
                Session::put('web_user_post_id', $this->details->user_id);
                Session::put('web_super_parent_id', $web_super_parent_id);
                Session::put('web_user_name', $this->details->first_name);
                Session::put('web_user_email', $this->details->email);
                Session::put('is_user_verify', $this->details->is_user_verify);
				Session::put('is_user_super_admin', $this->details->super_parent_id);

                Cookie::queue('web_user_id', $this->details->user_id, 525600);
                Cookie::queue('web_user_post_id', $this->details->user_id, 525600);
                Cookie::queue('web_super_parent_id', $web_super_parent_id, 525600);
                Cookie::queue('web_user_name', $this->details->first_name, 525600);
                Cookie::queue('web_user_email', $this->details->email, 525600);
                Cookie::queue('is_user_verify', $this->details->is_user_verify, 525600);
				Cookie::queue('is_user_super_admin', $this->details->super_parent_id, 525600);

                if ($this->details->is_user_verify == 1) {
                    return redirect('/shipper');
                } else {
                    Session::put('message', 'Tu cuenta aún no ha sido verificada. Por favor, verifica tu cuenta de correo para continuar.');
                    return redirect('/verification');
                    // return redirect('signin');
                }
            } else {
                // dd($data->input('user_email'));
                Session::put('message', trans('pdg.26'));
                // return redirect()->back()->withInput($data->only('user_email'));
                return redirect()->back()->withCookie(cookie('user_email', $data->input('user_email'), 60));
            }
        }
    }

    public function user_logout() {
        if (Session::has('web_user_id')) {
            Session::flush();

            Cookie::queue(Cookie::forget('web_user_id'));
            Cookie::queue(Cookie::forget('web_user_post_id'));
            Cookie::queue(Cookie::forget('web_super_parent_id'));
            Cookie::queue(Cookie::forget('web_user_name'));
            Cookie::queue(Cookie::forget('web_user_email'));
            Cookie::queue(Cookie::forget('is_user_verify'));
        }
        Session::put('message', "Se ha cerrado sesión sin ningún problema. Muchas gracias.");
        return redirect('signin');
    }

    public function web_company_post_signup(Request $data) {
       // RETURN $data->all();
        
        $validator = Validator::make($data->all(), [
            'company_name' => 'required',
            // 'RUC_no' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'user_email' => 'required|email',
            'phone_no' => 'required',
            'is_countri__' => 'required',
            'company_password' => 'required',
            // 'retypepassword' => 'required',
        ]);
        if ($validator->fails()) {
            Session::put('message', 'Este campo es obligatorio');
            return redirect('signup');
        }
        $table_name = 'trns_users';
        $check_data = array(
            'phone_no' => $data->input('is_countri__').''.$data->input('phone_no'),
            'email' => $data->input('user_email'),
            'get_result_type' => 3
        );
        
        $check_result = $this->user->get_user($check_data);
        if (empty($check_result)) {

            //Insert user in firebase
            $this->fbuser_details = [
                'email' => $data->input('user_email'),
                'emailVerified' => false,
                'phoneNumber' => '+'.$data->input('is_countri__').''.$data->input('phone_no'),
                'password' => $data->input('company_password'),
                'displayName' => $data->input('first_name') . ' ' . $data->input('last_name'),
            ];

            try {
                $this->fbnewuser = $this->fbauth->createUser($this->fbuser_details);
            } catch(\Kreait\Firebase\Exception\AuthException $e) {
                Session::put('message', $e->getMessage());
                
                Cookie::queue('company_name', $data->input('company_name'), 10);
                Cookie::queue('first_name', $data->input('first_name'), 10);
                Cookie::queue('last_name', $data->input('last_name'), 10);
                Cookie::queue('user_email', $data->input('user_email'), 10);
                Cookie::queue('phone_no', $data->input('is_countri__').''.$data->input('phone_no'), 10);
                Session::put('signup_type_id', 2);

                return redirect('signup');
            }

            // $this->fbuser = [
            //     'email' => $this->fbnewuser->email,
            //     'uid' => $this->fbnewuser->uid,
            // ];

            // try {
            //     $this->firebase->getDatabase()->getReference('users/' . $this->fbnewuser->uid)->set($this->fbuser);
            // } catch(\Kreait\Firebase\Exception\QueryException $e) {
            //     Session::put('message', $e->getMessage());
            //     return redirect('signup');
            // }
            //

            $digits = 6;
            $verify_code = rand(pow(10, $digits-1), pow(10, $digits)-1);

            //Insert user in laravel
            $this->value = array(
                'first_name' => $data->input('first_name'),
                'last_name' => $data->input('last_name'),
                'firebase_id' => $this->fbnewuser->uid,
                'email' => $data->input('user_email'),
                'phone_no' => $data->input('is_countri__').''.$data->input('phone_no'),
                'is_user_verify' => 0,
            	'is_email_verify' => 0,
            	'email_verify_token' => rand(0, 1000),
                'password' => md5($data->input('company_password')),
                'user_type' => 1,
                'company_name' => $data->input('company_name'),
                // 'dni_no' => $data->input('RUC_no'),
                'dni_no' => '',
                'create_date' => date('Y-m-d H:i:s'),
                'update_date' => date('Y-m-d H:i:s'),
                'is_company'=>1,
                'verification_code' => $verify_code
            );
            $this->details = $this->common_model->insert_table($table_name, $this->value);
            
            if ($this->details > 0) {

                $link = url('user-active-account-' . time() . '-' . base64_encode($data->input('user_email')) . '-' . base64_encode($this->fbnewuser->uid));
                
                $this->value['name'] = $data->input('first_name') . ' ' . $data->input('last_name');
                // $this->value['active_account'] = $link;
                $this->value['active_account'] = 'https://bauenfreight.com/Transporter/login?action=activate-account';

                $this->value['verification_code'] = $verify_code;

                $email = $data->input('user_email');
                Mail::send('email.verifying_email', ['data' => $this->value], function($message) use ($email) {
                    $message->from('info@bauenfreight.com', 'Bauen - Registro');
                    $message->to($email)->subject('Queremos verificar su identidad por su propia seguridad');
                });
                Session::put('message', "Te has registrado satisfactoriamente.");
                
                $phone_no = $data->input('is_countri__').''.$data->input('phone_no');
                $language_id = 1;
                $encode64 = base64_encode($phone_no.'-'.$language_id.'-'.$verify_code);
                $endpoint = "https://www.bauenfreight.com/api/sendsmsapi/$phone_no/$language_id/$verify_code/".rtrim(strtr(base64_encode($phone_no.$verify_code), '+/', '-_'), '=');
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', $endpoint, ['query' => []]);

                return redirect('https://bauenfreight.com/Transporter/login?action=mail-sent');
            
            } else {
                $this->fbauth->deleteUser($this->fbnewuser->uid);
                $this->firebase->getDatabase()->getReference('users/' . $this->fbnewuser->uid)->remove();
                Session::put('message', "No se ha podido registrar el Usuario. Intente de nuevo.");

                Cookie::queue('company_name', $data->input('company_name'), 10);
                Cookie::queue('first_name', $data->input('first_name'), 10);
                Cookie::queue('last_name', $data->input('last_name'), 10);
                Cookie::queue('user_email', $data->input('user_email'), 10);
                Cookie::queue('phone_no', $data->input('is_countri__').''.$data->input('phone_no'), 10);
                Session::put('signup_type_id', 2);

                return redirect('signup');
            }
        } else {
            Session::put('message', "Este correo electrónico o número de teléfono ya ha sido registrado con anterioridad.");

            Cookie::queue('company_name', $data->input('company_name'), 10);
            Cookie::queue('first_name', $data->input('first_name'), 10);
            Cookie::queue('last_name', $data->input('last_name'), 10);
            Cookie::queue('user_email', $data->input('user_email'), 10);
            Cookie::queue('phone_no', $data->input('is_countri__').''.$data->input('phone_no'), 10);
            Session::put('signup_type_id', 2);

            return redirect('signup');
        }
    }

    public function insert_firebase_rest(Request $data){
        $this->fbuser_details = [
            'email' => $data->input('user_email'),
            'emailVerified' => false,
            'phoneNumber' => '+51' . $data->input('phone_no'),
            'password' => $data->input('password'),
            'displayName' => $data->input('first_name') . ' ' . $data->input('last_name'),
        ];

        try {
            $this->fbnewuser = $this->fbauth->createUser($this->fbuser_details);
            return base64_encode($this->fbnewuser->uid);
        } catch(\Kreait\Firebase\Exception\AuthException | \Kreait\Firebase\Exception\InvalidArgumentException $e) {
            return null;
        }
    }

    public function delete_firebase_delete(Request $data){
        $uid = base64_decode($data->input('uid'));
        $this->fbuser = [
            'email' => $data->input('email'),
            'uid' => $uid,
        ];
        try {
            $this->firebase->getDatabase()->getReference('users/' . $uid)->set($this->fbuser);
        } catch(\Kreait\Firebase\Exception\QueryException $e) {            
            return "Error de respuesta";
        }

        try{
            $this->fbauth->deleteUser($uid);
            $this->firebase->getDatabase()->getReference('users/' . $uid)->remove();
            return $this->fbuser;
        } catch(\Kreait\Firebase\Exception\QueryException $e) {            
            return "Error al eliminar";
        }        
    }

    public function web_post_signup(Request $data) {
        $table_name = 'trns_users';
        $check_data = array(
            'phone_no' => $data->input('is_countri__').''.$data->input('phone_no'),
            'email' => $data->input('user_email'),
            'get_result_type' => 3
        );
        $check_result = $this->user->get_user($check_data);

        if (empty($check_result)) {
          
            $this->fbuser_details = [
                'email' => $data->input('user_email'),
                'emailVerified' => false,
                'phoneNumber' => '+'.$data->input('is_countri__').''.$data->input('phone_no'),
                'password' => $data->input('password'),
                'displayName' => $data->input('first_name') . ' ' . $data->input('last_name'),
            ];

            try {
                $this->fbnewuser = $this->fbauth->createUser($this->fbuser_details);
            } catch(\Kreait\Firebase\Exception\AuthException | \Kreait\Firebase\Exception\InvalidArgumentException $e) {
                Session::put('message', $e->getMessage());
                // return "Error 01";
                return redirect('signup?error=firebase-fail');
            }

            // $this->fbuser = [
            //     'email' => $this->fbnewuser->email,
            //     'uid' => $this->fbnewuser->uid,
            // ];

            // try {
            //     $this->firebase->getDatabase()->getReference('users/' . $this->fbnewuser->uid)->set($this->fbuser);
            // } catch(\Kreait\Firebase\Exception\QueryException $e) {
            //     Session::put('message', $e->getMessage());
            //     // return "Error 02";
            //     return redirect('signup?error=firebase-fail');
            // }
          
            $digits = 6;
            $verify_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $phone_no = $data->input('is_countri__').''.$data->input('phone_no');
            $language_id = 1;
            $email = $data->input('user_email');
             
            $this->value = array(
                'first_name' => $data->input('first_name'),
                'last_name' => $data->input('last_name'),
                'email' => $email,
                'firebase_id' => $this->fbnewuser->uid,
                'phone_no' => $phone_no,
				'is_user_verify' => 0,
				'is_email_verify' => 0,
				'email_verify_token' => rand(0, 1000),
                'password' => md5($data->input('password')),
                'user_type' => 0,
                'dni_no' => '',
                'create_date' => date('Y-m-d H:i:s'),
                'update_date' => date('Y-m-d H:i:s'),
                'verification_code' => $verify_code
            );

            if($data->input('is_company') == 1) {
                $this->value = array_merge($this->value, array(
                    'is_company' => 1,
                    'company_name' => $data->input('company_name'),
                    'industrytype_id' => $data->input('industrytype_id'),
                    'ruc_no' => $data->input('ruc_no')
                ));
            }
            
          $this->details = $this->common_model->insert_table($table_name, $this->value);

            if ($this->details > 0) {
              $link = url('user-active-account-' . time() . '-' . base64_encode($data->input('user_email')) . '-' . base64_encode($this->fbnewuser->uid));
               
                $this->value['name'] = $data->input('first_name') . ' ' . $data->input('last_name');
                $this->value['active_account'] = $link;
                $this->value['verification_code'] = $verify_code;

                $email = $data->input('user_email');
                Mail::send('email.verifying_email', ['data' => $this->value], function($message) use ($email) {
                    $message->from('info@bauenfreight.com', 'Bauen - Registro');
                    $message->to($email)->subject('Queremos verificar su identidad por su propia seguridad');
                });
                Session::put('message', "Un correo de verificación ha sido enviado a su correo. Presione el link que le hemos enviado para continuar.");

                $encode64 = rtrim(strtr(base64_encode($phone_no.'-'.$language_id.'-'.$verify_code), '+/', '-_'), '=');
                $endpoint = "https://www.bauenfreight.com/api/sendsmsapi/$phone_no/$language_id/$verify_code/".rtrim(strtr(base64_encode($phone_no.$verify_code), '+/', '-_'), '=');
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', $endpoint, ['query' => []]);

                Session::put('web_user_id', $this->details);
                Session::put('web_user_post_id', $this->details);
                Session::put('web_user_name', $data->input('first_name'));
                Session::put('web_user_email', $email);

                Cookie::queue('web_user_id', $this->details, 525600);
                Cookie::queue('web_user_post_id', $this->details, 525600);
                Cookie::queue('web_user_name', $data->input('first_name'), 525600);
                Cookie::queue('web_user_email', $email, 525600);

                return redirect('/verification');
            } else {
                $this->fbauth->deleteUser($this->fbnewuser->uid);
                $this->firebase->getDatabase()->getReference('users/' . $this->fbnewuser->uid)->remove();
                Session::put('message', "No se ha podido completar el Registro");
                // return "error 03";
                return redirect('signup?error=unsuccesfully');
            }
        } else {
            Session::put('message', "Este correo electrónico o número de teléfono ya ha sido registrado con anterioridad.");
            // return "error 042";
            return redirect('signup?error=already-exists');
        }
    }

    public function user_active_account(Request $request) {
        if($request->input('code01') == '' &&
        $request->input('code02') == '' &&
        $request->input('code03') == '' &&
        $request->input('code04') == '' &&
        $request->input('code05') == '' && $request->input('code06') == '' ) {
            Session::put('message', "Por favor, ingrese el código de 6 dígitos.");
            return redirect('/verification');
        } else {
            $this->verification_code = $request->input('code01') . $request->input('code02') . $request->input('code03') . $request->input('code04') . $request->input('code05') . $request->input('code06');
            $check_data = array(
                'user_id' => $request->input('user_id'),
                'get_result_type' => 2
            );
            $get_user_existence = $this->user->get_user($check_data);

            // dd($get_user_existence);

            if (!empty($get_user_existence)) {

                if($get_user_existence->verification_code == $this->verification_code) {
                    $value = array(
                        'is_user_verify' => 1,
                        'is_email_verify' => 1,
                        'email_verify_token' => rand(0, 1000),
                        'update_date' => "'".date('Y-m-d H:i:s')."'"
                    );

                    $result = $this->common_model->update_table('trns_users', $value, 'user_id', $request->input('user_id'));
                    if ($result > 0) {
                        // Verify firebase account
                        $this->fbuser_details = ['emailVerified' => true];
                        
                        //if($get_user_existence->firebase_id != ''){

                            try {
                                $this->fbauth->updateUser($get_user_existence->firebase_id, $this->fbuser_details);
                            } catch(Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                                Session::put('message', 'No se encontró el usuario.');
                                return redirect('/verification');
                            }

                        //}
                        //
                        Session::put('is_user_verify', 1);
                        Cookie::queue('is_user_verify', 1, 525600);

                        Session::put('message', 'Cuenta activada de manera satisfactoria.');
                        return redirect('shipper');
                    } else {
                        Session::put('message', 'No se ha podido activar la cuenta de Usuario. Pongánse en contacto con solcese@bauenfreight.com');
                        return redirect('/verification');
                    }
                } else {
                    Session::put('message', "El código de verificación es incorrecto.");
                    return redirect('/verification');
                }
    
                
            } else {
                Session::put('message', "El usuario no existe.");
                return redirect('/verification?error=user-not-found');
            }
        }

    }

    public function user_active_account_resend_code() {

        if(Session::has('web_user_id')) {
            $check_data = array(
                'user_id' => Session::get('web_user_id'),
                'get_result_type' => 2
            );

            $get_user_existence = $this->user->get_user($check_data);
            
            if (!empty($get_user_existence)) {
                $language_id = 1;
                
                $encode64 = rtrim(strtr(base64_encode($get_user_existence->phone_no.$get_user_existence->verification_code), '+/', '-_'), '=');
                $endpoint = "https://www.bauenfreight.com/api/sendsmsapi/$get_user_existence->phone_no/$language_id/$get_user_existence->verification_code/".$encode64;
                $client = new \GuzzleHttp\Client();
                
                if($response = $client->request('GET', $endpoint, ['query' => []])) {
                    Session::put('message', "El código ha sido reenviado.");
                    return redirect('/verification');
                } else {
                    Session::put('message', "No se pudo reenviar el código, inténtelo nuevamente.");
                    return redirect('/verification');
                }
            } else {
                Session::put('message', "No se encontró el usuario, inténtelo nuevamente.");
                return redirect('/verification');
            }

        } else {
            Session::put('message', "Ingrese sus credenciales antes de verificar su cuenta.");
            return redirect('signin');
        }
    }

    // public function user_active_account($time, $email, $uid) {
    //     if (!isset($time) || $time == "") {
    //         Session::put('message', "Este link no es válido");
    //         return redirect('signin');
    //     } elseif (!isset($email) || $email == "") {
    //         Session::put('message', "Este link no es válido");
    //         return redirect('signin');
    //     } else {
    //         $decoded_uid = base64_decode($uid);
    //         $decoded_email = base64_decode($email);
    //         $current_time = time();
    //         $time_dif = $current_time - $time;
    //         if ($time_dif > 3600) {
    //             Session::put('message', "Se ha vencido la sesión. Inicie sesión nuevamente");
    //             return redirect('signin');
    //         } else {
    //             $check_data = array(
    //                 'email' => $decoded_email,
    //                 'get_result_type' => 4
    //             );
    //             $get_email_existence = $this->user->get_user($check_data);
    //             if (!empty($get_email_existence)) {
    //                // return view('forgot_password.forgot', ['user' => $get_email_existence->user_id, 'done_flag' => 0]);
    //                 $value = array(
    //                     'is_user_verify' => 1,
    //                     'is_email_verify' => 1,
    //                     'email_verify_token' => rand(0, 1000),
    //                     'update_date' => "'".date('Y-m-d H:i:s')."'"
    //                 );

    //                 $iduser = $get_email_existence->user_id;
    //                 echo '<pre>';
    //                 print_r($iduser);
    //                 echo '</pre>';

    //                 $result = $this->common_model->update_table('trns_users', $value, 'user_id', $iduser);
    //                 if ($result > 0) {
    //                     // Verify firebase account
    //                     $this->fbuser_details = [
    //                         'emailVerified' => true 
    //                     ];

    //                     try {
    //                         $this->fbauth->updateUser($decoded_uid, $this->fbuser_details);
    //                     } catch(Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    //                         Session::put('message', 'No se encontró el usuario.');
    //                         return redirect('signin');
    //                     }
    //                     //

    //                     Session::put('message', 'Cuenta activada de manera satisfactoria.');
    //                     return redirect('signin');
    //                 } else {
    //                     Session::put('message', 'No se ha podido activar la cuenta de Usuario. Pongánse en contacto con solcese@bauenfreight.com');
    //                     return redirect('signin');
    //                 }
    //             } else {
    //                 Session::put('message', "Este link ha vencido");
    //                 return redirect('signin');
    //             }
    //         }
    //     }
    // }

    public function user_forgot_password(Request $data) {
        //$input = $data->all();
        if (empty($data->input('user_email'))) {
            Session::put('message', "Por favor ingrese el nombre de usuario (correo electrónico)");
            return redirect('signin');
        } else {
            $check_data = array(
                'email' => $data->input('user_email'),
                'get_result_type' => 4
            );
            $get_email_existence = $this->user->get_user($check_data);
            if ($get_email_existence && !empty($get_email_existence)) {
                $link = url('user-update-password-' . time() . '-' . base64_encode($data->input('user_email')));
                $userdata['name'] = $get_email_existence->first_name . ' ' . $get_email_existence->last_name;
                $userdata['forgot_password_url'] = $link;
                $email = $get_email_existence->email;

                Mail::send('forgot_password.forgot_password_email', ['data' => $userdata], function($message) use ($email) {
                    $message->from('info@bauenfreight.com', 'Bauen Freight App');
                    $message->to($email)->subject('Recuperar Contraseña - Bauen Freight App');
                });
                Session::put('message', "Hemos enviado un mensaje al correo " . $email . " con las instrucciones para reestablecer su contraseña.");
                return redirect('signin');
            } else {
                Session::put('message', "Este correo no se encuentra registrado con nosotros");
                return redirect('signin');
            }
        }
    }

    public function user_update_password($time, $email) {
        if (!isset($time) || $time == "") {
            Session::put('message', "Este link no es válido");
            return redirect('signin');
        } elseif (!isset($email) || $email == "") {
            Session::put('message', "This link is not valid.");
            return redirect('signin');
        } else {
            $decoded_email = base64_decode($email);
            $current_time = time();
            $time_dif = $current_time - $time;
            if ($time_dif > 3600) {
                Session::put('message', "Este link no es válido");
                return redirect('signin');
            } else {
                $check_data = array(
                    'email' => $decoded_email,
                    'get_result_type' => 4
                );
                $get_email_existence = $this->user->get_user($check_data);
                if (!empty($get_email_existence)) {
                    return view('forgot_password.forgot', ['user' => $get_email_existence->user_id, 'done_flag' => 0]);
                } else {
                    Session::put('message', "Este link ha vencido.");
                    return redirect('signin');
                }
            }
        }
    }

    public function reset_user_password(Request $data) {
        //return $data->all();
        // $table_name = 'trns_users';
        $validator = Validator::make($data->all(), [
                    'user_id' => 'required',
                    'password_1' => 'required']
        );
        if ($validator->fails()) {
            Session::put('message', 'Este campo es obligatorio');
            return redirect('signin');
        } else {
            $value = array(
                'is_user_verify' => 1,
                'is_email_verify' => 1,
                'email_verify_token' => rand(0, 1000),
                'update_date' => date('Y-m-d H:i:s'),
                'password' => md5($data->input('password_1'))
            );
            $result = $this->common_model->update_table('trns_users', $value, 'user_id', $data->input('user_id'));
            if ($result > 0) {
                Session::put('message', 'Su contraseña ha sido reestablecida.');
                return redirect('signin');
            } else {
                Session::put('message', 'Su contraseña no pudo ser reestablecida, pongáse en contacto con solcese@bauenfreight.com');
                return redirect('signin');
            }
        }
    }

    public function send_your_message(Request $data) {
        $validator = Validator::make($data->all(), [
                    'client_name' => 'required',
                    'client_email' => 'required',
                    'client_message' => 'required'
        ]);
        if ($validator->fails()) {
            Session::put('message', 'Este campo es obligatorio');
            return redirect()->back();
        } else {
            $value = array(
                'client_name' => $data->input('client_name'),
                'client_email' => $data->input('client_email'),
                'client_message' => $data->input('client_message')
            );
            $email = "priyo.ncr@gmail.com";
            Mail::send('email.contact_us', ['data' => $value], function($message) use ($email) {
                $message->from('info@bauenfreight.com', 'Bauen Freight App');
                $message->to($email)->subject('Forgot Password :: Bauen Freight App');
            });
        }
        return redirect()->back();
    }

}
