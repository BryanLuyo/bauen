<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class users extends MY_Controller {
    
    function __construct(){
        parent::__construct();
       $this->load->model('bauenservices/usersModel');
    }

    private function json_output($response_data=array()){
          switch($this->response_status){
            case -1:
              $this->response_message="Your request not accepted";
              $this->response_status=0;
                                      $this->response_body=null;
              break;
            case -2:
              $this->response_message="Invalid request param set";
              $this->response_status=0;
                                      $this->response_body=null;
              break;
            case -3:
              $this->response_message="Authentication Error";
              $this->response_status=0;
                                      $this->response_body=null;
              break;
            default:
              break;
    }
    $response = array(
      'status'=>$this->response_status,
      'message'=>$this->response_message,
                   //     'body' => $this->response_body
    );
    if(is_array($response_data)){
      //$response = array_merge($response,$response_data);
    }
    $response = array_merge($response,$response_data);
    die(json_encode($response));
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
        
        $select_fields=array('user_id','user_type','super_parent_id');
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
            $response_data['status'] = false;
            $response_data['message'] = "Los detalles de usuario son inválidos";
              print (json_encode($response_data));
          }
          else{
            if($user_type==1){//transporter section
              if($is_company!=$user['is_company']){
               $response_data['status'] = false;
               $response_data['message'] = "Los detalles de usuario inválidos";
              print (json_encode($response_data));
              }
            }
          }
          // update the hax key 
          $hax_key = $this->generate_request_key($user_id,$is_new=0);
          $super_parent_id = $user['super_parent_id'];
          $users=$this->userdetails($user_id,$super_parent_id);
          $response_data['user_request_key']=$hax_key;
          $response_data['body'] = $users;
          $response_data['status'] = true;
          $response_data['message'] = "Autenticación correcta";
        }
        else{
          $response_data['status'] = false;
          $response_data['message'] = "Email o password no coinciden";
        }
      }
      else{
        $erros = validation_errors();
        //$this->response_message=$erros;
         $response_data['status'] = false;
         $response_data['message'] = $erros;
      }

         print (json_encode($response_data));
    }

  // verification code validation 
    public function verify_code_validate(){
        $response_data=array();
        $verify_code = $this->input->post('verify_code');
        $logged_user_id = $this->input->post('user_id');
      //  $this->minimum_param_checked(1);
        if(empty($verify_code)){
           $response_data['status'] = false;
           $response_data['message'] ="Ingrese su código de verificación";
          
        }
        $find_cond = array(
          'user_id'=>$logged_user_id,
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
          $users= $this->userdetails($logged_user_id);
          $response_data['body'] = $users;
          $response_data['status'] = true;
          $response_data['message'] = "Éxito. Tu cuenta ha sido verificada";
        }
        else{
          $response_data['status'] = false;
          $response_data['message'] = "El código es inválido";
        }
        print (json_encode($response_data));
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
            $this->response_message="Ingrese número de teléfono";
            $this->json_output($response_data);
          }
          else{
            // validate format 
            if(!$this->valid_phone_no($phone_no)){
              $this->response_message="Ingrese número de teléfono válido";
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
            $this->response_status=1;
            $this->response_message="Esta cuenta ya esta validada";
            $this->json_output($response_data);
          }
          elseif($user['is_phone_no_verify']){
            $this->response_status=1;
            $this->response_message="Este teléfono ya esta validado";
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
            $this->response_message="Hemos enviado un código de verificación a tu e-mail";
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
            $this->response_message="Por favor, ingrese su correo electrónico registrado con nosotros";
            $this->json_output();
          }
          if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $this->response_message="Ingrese su correo en un formato válido";
            $this->json_output();
          }
          // find section 
          $find_cond=array(
            'email'=>$email,
            'is_blocked'=>'0'
          );
          $user = $this->BaseModel->getData($this->tableNameUser,$find_cond);
          if(empty($user)){
            $this->response_status=0;
            $this->response_message="Este correo no se encuentra registrado con nosotros";
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
          $this->response_message="Haga click en el link de verificación enviado a su email";
          $this->response_status=1;
          //send email section 
          $email_data=array(
            'changelink'=>$changelink
          );
          $this->sendemail(2,$email,$email_data);
          $this->json_output();
  }
    public function getUser(){
        $response_data=array();
        $user_id = $this->input->post('user_id');
        $other_user_id = $this->input->post('other_user_id');
        //$hax_key = $this->input->post('request_key');
        //$this->minimum_param_checked(1);
        if($other_user_id>0){
          $users=$this->userdetails($other_user_id);
                if (count($users>0) ){
                  $response_data['body'] = $users;
                  $response_data['status'] = true;
                 
                }else{
                 $response_data['status'] = false;
                  $response_data['message'] = "No se encontraron registros";
                }
        }
        else{
          $super_parent_id = $this->logged_user['super_parent_id'];
          $users=$this->userdetails($user_id,0);
          // update the request key 
          $hax_key = $this->generate_request_key($user_id,$is_new=0);
          //$response_data['user_request_key']=$hax_key;

            if (count($users)>0 )
              {
                  $response_data['body'] = $users;
                  $response_data['status'] = true;
              }else
                {
                  $response_data['status'] = false;
                  $response_data['message'] = "No se encontraron registros";
                }
        }
       
       print (json_encode($response_data));
  }

    public function editUser(){
        $response_data=array();
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
          $response_data['status'] = false;
           $response_data['message'] = "No se ingreso el id del usuario a actualizar";
        }else{
              //$this->minimum_param_checked(1);
            $this->load->library(array('form_validation'));
            $image=array();
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
            );
           
            
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
                'dni_no'=>$this->input->post('dni_no'),
                'ruc_no'=>$this->input->post('ruc_no'),
              );
             $usuario=$this->usersModel->getUsuario($user_id);
             $old_phone_no=$usuario[0]->phone_no;
             $old_image=$usuario[0]->image;
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
              
              $users = $this->userdetails($user_id);
              //$response_data['user_request_key']=$this->input->post('user_request_key');

              $response_data['body'] = $users;
              $response_data['status'] = true;
              $response_data['message'] = "Información de usuario actualizada correctamente";
            }
            else{
              $response_data['status'] = false;
              $response_data['message'] = "Información de usuario no pudo ser actualizada";
            }
        }
        
       print (json_encode($response_data));
    }

    public function user_image_upload(){
       date_default_timezone_set('america/lima'); 
        $fecha = date('Y-m-d H:i:s');
        $response_data=array();
        $user_id = $this->input->post('user_id');
        $image=array();

        $usuario=$this->usersModel->getUsuario($user_id);
        $old_image=$usuario[0]->image;
        
        // image section 
        if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
          $image = $_FILES['image'];
          $image_name = $this->uploadimage($image,'users');
          if(!empty($image_name)){
            $update_data['image']=$image_name;
            $update_data['update_date']=$fecha ;
            // remove old i
            $this->removeimage($old_image,'users');
            $update_cond=array(
              'user_id'=>$user_id
            );
            $this->BaseModel->updateDatas($this->tableNameUser,$update_data,$update_cond);
            $response_data['status'] = true;
            $response_data['body'] = base_url('uploads/users/'.$image_name);
          }
          else{
            $response_data['status'] = false;
            $response_data['message'] = "Error al subir la imagen";
          }
        }
        else{
          $response_data['status'] = false;
          $response_data['message'] = "Falta el archivo de imagen";
        }
       print (json_encode($response_data));
    }
    public function userdetails($user_id=0,$super_parent_id=0){
          if(empty($user_id)){
            return array();
          }
          // get the users basic details 
          $find_cond=array(
            'user_id'=>$user_id,
            'is_blocked'=>'0',
            //'is_deleted'=>array('1','0')
          );
          $select_flds=array('user_id','first_name','last_name','email','phone_no','user_type','image','dni_no','is_company','company_name','company_licence_no','ruc_no','is_user_verify','verification_code','about_us','address','firebase_id');
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
              'is_blocked'=>'0'
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
    public function register(){

      $response_data=array();
      date_default_timezone_set('america/lima'); 
      $fecha = date('Y-m-d H:i:s');
      $is_company=$this->input->post('is_company');
      $data['is_company']=$is_company;
      $data['first_name']=$this->input->post('first_name');
      $data['last_name']=$this->input->post('last_name');
      $email=$this->input->post('email');
      $data['email'] =$email;
      $phone_no=$this->input->post('phone_no');
      $data['phone_no'] =$phone_no; 
      $data['password'] = $this->input->post('password');
      $data['dni_no'] = $this->input->post('dni_no');
      $data['user_type'] = $this->input->post('user_type');
      $data['ruc_no'] = $this->input->post('ruc_no');
      $data['company_name'] = $this->input->post('company_name');
      $data['company_licence_no'] = $this->input->post('company_licence_no');
      $data['industrytype_id'] = $this->input->post('industrytype_id');
      $data['country_code'] = $this->input->post('country_code');
      $data['firabase_id'] = $this->input->post('firebase_id');
      $data['create_date'] =$fecha;
      $data['update_date'] =$fecha;

      $verifEmail=count($this->usersModel->verificarEmail($data));
      $verifTelefono=count($this->usersModel->verificarTelefono($data));

      if ($verifEmail>0) {
        $response_data['status'] = false;
        $response_data['message'] = "Este email ya se encuentra registrado";
      }
      else if($verifTelefono>0)
        {
        $response_data['status'] = false;
        $response_data['message'] = "Este número telefonico ya se encuentra registrado con nosotros";
        }
      else{
          // generate the verification code 
          $verify_code = $this->verify_code(); // must send on mobile for mobile number validation
          // email verification token send 
          $email_verify_token = $this->email_verify_token();
          $data['verification_code'] =$verify_code;
          $data['email_verify_token'] =$email_verify_token;

          $object =$this->usersModel->getRegister($data);

          $idUser=$object[0]->id;
          //  $data = array();
          // $data['body'] = array();
          if ($idUser>=0) {
            $this->sendsms($phone_no,$verify_code);
            $this->send_email_verify_link($email,$email_verify_token,$verify_code);
            $response_data['body'] = $this->userdetails($idUser);
            $response_data['status'] = true;
            $response_data['message'] = "Datos guardados correctamente";
          }else{
            $response_data['status'] = false;
            $response_data['message'] = "Error al guardar";
          }
        }
       print (json_encode($response_data));
    }

    private function verify_code(){
    $verify_code = rand(99999,1000000);
    return $verify_code;
    }
  
    private function email_verify_token(){
    $email_verify_token = time().rand(999,1000000);
    return md5($email_verify_token);
    }
    private function sendsms($phone_no='',$verify_code=''){
      if(!empty($phone_no) && !empty($verify_code)){
        // sms send api integration
        $verify_code="Your OTP is $verify_code";
        $this->twilio_send_sms($phone_no,$verify_code);
         }
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
}

