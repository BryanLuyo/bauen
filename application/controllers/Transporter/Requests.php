<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Requests extends MY_Controller{
	public $transporter_id;
	function __construct(){
		parent::__construct();
		/**MODE DEV ERROR INIT*/
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		/**END DEV */
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);
		$this->transporter_blocked = $this->session->userdata(SES_TRANS_BLOCKED);
	}
	
	public function index(){
		$data=array();
		$user_id=0;
		$transporter_id=$this->transporter_id;
		$driver_id=0;
		$trailer_id=0;
		$request_status_key=-1;
		$loadtype_id=0;
		$email_phone_no='';
		$pickup_date_from='';
		$pickup_date_to='';

		$user=array();
		
		$blocked = $this->transporter_blocked;
		if($blocked == 2) {
			$data['blocked'] = true;
		}
		// user select flds
		$user_select_fields=array('user_id','super_parent_id','first_name','last_name','email','phone_no','user_type','is_company');
		
		$find_request=array();
		$assos_cond=array(
			'fields'=>array(),
			'order_by'=>array(),
			'count'=>'0',
			'trans_bid_cond'=>array(
				'user_id'=>$this->transporter_id
			)
		);
		// filter section 
		if($this->input->server('REQUEST_METHOD')=='POST'){
			// if($this->input->post('user_id')){
			// 	$user_id=$this->input->post('user_id');
			// }
			if($this->input->post('transporter_id')){
				$transporter_id=$this->input->post('transporter_id');
			}
			if($this->input->post('driver_id')){
				$driver_id=$this->input->post('driver_id');
			}
			if($this->input->post('trailer_id')){
				$trailer_id=$this->input->post('trailer_id');
			}

			if($this->input->post('request_status')){
				$request_status_key=$this->input->post('request_status');
			} else {
				$request_status_key = $request_status_key-1;
			}

			if($this->input->post('loadtype_id')){
				$loadtype_id=$this->input->post('loadtype_id');
			}
			if($this->input->post('email_phone_no')){
				$email_phone_no=$this->input->post('email_phone_no');
			}
			if($this->input->post('pickup_date_from')){
				$pickup_date_from=$this->input->post('pickup_date_from');
			}
			if($this->input->post('pickup_date_to')){
				$pickup_date_to=$this->input->post('pickup_date_to');
			}
			
		}
		
		// conditions 
		if($user_id>0){
			$find_request['user_id']=$user_id;
		}
		
		if($driver_id>0){
			$find_request['driver_id']=$driver_id;
		}
		
		if($trailer_id>0){
			$find_request['trailer_id']=$trailer_id;
		}
		if($loadtype_id>0){
			$find_request['loadtype_id']=$loadtype_id;
		}
		$is_allow_trans_cond=true;
		
		if($request_status_key>-1){
			if($request_status_key>0){
				// trans_cond
				// if(in_array($request_status_key), array('0', '1', '2')) {

				// }

				if(in_array($request_status_key,array('1','2','3','4'))){
					
					$trans_bid_assos=array(
						'is_blocked'=>'0'
					);
					if($request_status_key==2){
						$trans_bid_assos=array(
							'bid_status'=>'2'
						);
					}
					elseif($request_status_key==3){
						$trans_bid_assos=array(
							'bid_status'=>'2'
						);
					}
					elseif($request_status_key==4){
						$is_allow_trans_cond=false;
						$trans_bid_assos=array(
							'bid_status'=>'3'
						);
					}
					else{
						
					}
					// assos 
					$assos_cond['trans_bid_assos']=$trans_bid_assos;
					$find_request['request_status']=$request_status_key;
				}
				elseif(in_array($request_status_key,array('5','6','7','13','14'))){
					$is_allow_trans_cond=false;
					$find_request['driver_id >']=0;
					$find_request['vehicle_id >']=0;
					$find_request['transporter_id']=$this->transporter_id;
					$bid_assos=array(
						'user_id'=>$this->transporter_id
					);
					// assos 
					$assos_cond['bid_assos']=$bid_assos;
					$find_request['request_status']=$request_status_key;
				}
				else{
					$find_request['request_status']=$request_status_key;
				}
			}
			else{
				$find_request['request_status']=$request_status_key;
			}
			// $request_status_key=$request_status_key+1;
		}
		
		if(!empty($email_phone_no)){
			$find_user=array();
			if(filter_var($email_phone_no,FILTER_VALIDATE_EMAIL)){
				// email matched
				$find_user['email']=$email_phone_no;
			}
			else{
				// phone no matched 
				$find_user['phone_no']=$email_phone_no;
			}
			
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_select_fields);
			
			if(!empty($user)){
				if($user['user_type']==1){
					$is_allow_trans_cond=false;
					// transporter section
					if($user['is_company']){
						$transporter_id = $user['user_id'];
						if($user['super_parent_id']>0){
							$transporter_id = $user['super_parent_id'];
						}
						$find_request['transporter_id']=$transporter_id;
					}
					else{
						$find_request['driver_id']=$user['user_id'];
						$find_request['transporter_id']=$this->transporter_id;
					}
				}
				else{
					$user_id = $user['user_id'];
					if($user['super_parent_id']>0){
						$user_id = $user['super_parent_id'];
					}
					$find_request['user_id']=$user_id;
				}
			}
			
			$user_id=0;
			$transporter_id=0;
			$driver_id=0;
		}
		
		//pickup date filter 
		if(!empty($pickup_date_from) && !empty($pickup_date_to)){
			$find_request['pickup_date >=']=date("Y-m-d",strtotime($pickup_date_from));
			$find_request['pickup_date <=']=date("Y-m-d",strtotime($pickup_date_to));
		}
		else{
			if(!empty($pickup_date_from)){
				$find_request['pickup_date']=date("Y-m-d",strtotime($pickup_date_from));
			}
			if(!empty($pickup_date_to)){
				$find_request['pickup_date']=date("Y-m-d",strtotime($pickup_date_to));
			}
		}
		
		// is allow trans cond 
		if($is_allow_trans_cond){
			$find_request['transporter_id']=array('0',$this->transporter_id);
		}
		
		// get user details 
		
		if($user_id>0 || $transporter_id>0 || $driver_id>0){
			$user_id = ($user_id)?$user_id:(($transporter_id)?$transporter_id:$driver_id);
			$find_user=array(
				'user_id'=>$user_id
			);
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_select_fields);
		}
		// get the request 
		/*echo '<pre>';
				print_r($user);
			echo '</pre>';*/
		
		// $find_request['request_status'] = 2;
		$requests = $this->getrequestsV2($find_request,$assos_cond);
		
		/*$Query ="select r2.*, u2.first_name cus_first_name , u2.last_name  cus_last_name,
(select load_name FROM trns_loadtypes where loadtype_id=r2.loadtype_id) as load_name,
(select  name FROM trns_trailers where trailer_id=r2.trailer_id)as trailer_name,
(select bid_status FROM trns_request_bids where request_id = r2.request_id order by bid_id desc limit 1) as trans_bid_status,
(select count(x.bid_id) FROM trns_request_bids x where x.request_id = r2.request_id ) as total_bids,
(select bid_id FROM trns_request_bids where request_id = request_id = r2.request_id) as trans_bid_id
FROM trns_requests r2 
inner join trns_users u2 on u2.user_id = r2.user_id where r2.request_id
in (select b.request_id from trns_request_bids b where b.user_id = ".$user_id." order by 1 desc) and r2.is_deleted = 0 order by 1 desc;";


		$requests = $this->BaseModel->customSelect($Query);*/
		// assign data 
		$data['requests']=$requests;
		$data['user']=$user;
		$data['trailers']=$this->gettrailers();
		$data['request_status']=$this->getrequeststatus();
		$data['trailer_id']=$trailer_id;
		$data['user_id']=$user_id;
		$data['transporter_id']=$transporter_id;
		$data['request_status_key']=$request_status_key;
		$data['loadtype_id']=$loadtype_id;
		$data['loadtypes']=$this->getloadtypes();
		$data['email_phone_no']=$email_phone_no;
		$data['pickup_date_from']=$pickup_date_from;
		$data['pickup_date_to']=$pickup_date_to;
		$data['find_request']=$find_request;
		$data['assos_cond']=$assos_cond;
		$this->loadviewtrans('request_list',$data);
	}

	public function completed() {
		$data=array();
		$user_id=0;
		$transporter_id=$this->transporter_id;
		$driver_id=0;
		$trailer_id=0;
		$request_status_key=-1;
		$loadtype_id=0;
		$email_phone_no='';
		$pickup_date_from='';
		$pickup_date_to='';

		$user=array();
		
		$blocked = $this->transporter_blocked;
		if($blocked == 2) {
			$data['blocked'] = true;
		}
		// user select flds
		$user_select_fields=array('user_id','super_parent_id','first_name','last_name','email','phone_no','user_type','is_company');
		
		$find_request=array();
		$assos_cond=array(
			'fields'=>array(),
			'order_by'=>array(),
			'count'=>'0',
			'trans_bid_cond'=>array(
				'user_id'=>$this->transporter_id
			)
		);
		// filter section 
		if($this->input->server('REQUEST_METHOD')=='POST'){
			// if($this->input->post('user_id')){
			// 	$user_id=$this->input->post('user_id');
			// }
			if($this->input->post('transporter_id')){
				$transporter_id=$this->input->post('transporter_id');
			}
			if($this->input->post('driver_id')){
				$driver_id=$this->input->post('driver_id');
			}
			if($this->input->post('trailer_id')){
				$trailer_id=$this->input->post('trailer_id');
			}

			if($this->input->post('request_status')){
				$request_status_key=$this->input->post('request_status');
			} else {
				$request_status_key = $request_status_key-1;
			}

			if($this->input->post('loadtype_id')){
				$loadtype_id=$this->input->post('loadtype_id');
			}
			if($this->input->post('email_phone_no')){
				$email_phone_no=$this->input->post('email_phone_no');
			}
			if($this->input->post('pickup_date_from')){
				$pickup_date_from=$this->input->post('pickup_date_from');
			}
			if($this->input->post('pickup_date_to')){
				$pickup_date_to=$this->input->post('pickup_date_to');
			}
			
		}
		
		// conditions 
		if($user_id>0){
			$find_request['user_id']=$user_id;
		}
		
		if($driver_id>0){
			$find_request['driver_id']=$driver_id;
		}
		
		if($trailer_id>0){
			$find_request['trailer_id']=$trailer_id;
		}
		if($loadtype_id>0){
			$find_request['loadtype_id']=$loadtype_id;
		}
		$is_allow_trans_cond=true;
		
		if($request_status_key>-1){
			if($request_status_key>0){
				// trans_cond
				// if(in_array($request_status_key), array('0', '1', '2')) {

				// }

				if(in_array($request_status_key,array('1','2','3','4'))){
					
					$trans_bid_assos=array(
						'is_blocked'=>'0'
					);
					if($request_status_key==2){
						$trans_bid_assos=array(
							'bid_status'=>'2'
						);
					}
					elseif($request_status_key==3){
						$trans_bid_assos=array(
							'bid_status'=>'2'
						);
					}
					elseif($request_status_key==4){
						$is_allow_trans_cond=false;
						$trans_bid_assos=array(
							'bid_status'=>'3'
						);
					}
					else{
						
					}
					// assos 
					$assos_cond['trans_bid_assos']=$trans_bid_assos;
					$find_request['request_status']=$request_status_key;
				}
				elseif(in_array($request_status_key,array('5','6','7','13','14'))){
					$is_allow_trans_cond=false;
					$find_request['driver_id >']=0;
					$find_request['vehicle_id >']=0;
					$find_request['transporter_id']=$this->transporter_id;
					$bid_assos=array(
						'user_id'=>$this->transporter_id
					);
					// assos 
					$assos_cond['bid_assos']=$bid_assos;
					$find_request['request_status']=$request_status_key;
				}
				else{
					$find_request['request_status']=$request_status_key;
				}
			}
			else{
				$find_request['request_status']=$request_status_key;
			}
			// $request_status_key=$request_status_key+1;
		}
		
		if(!empty($email_phone_no)){
			$find_user=array();
			if(filter_var($email_phone_no,FILTER_VALIDATE_EMAIL)){
				// email matched
				$find_user['email']=$email_phone_no;
			}
			else{
				// phone no matched 
				$find_user['phone_no']=$email_phone_no;
			}
			
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_select_fields);
			
			if(!empty($user)){
				if($user['user_type']==1){
					$is_allow_trans_cond=false;
					// transporter section
					if($user['is_company']){
						$transporter_id = $user['user_id'];
						if($user['super_parent_id']>0){
							$transporter_id = $user['super_parent_id'];
						}
						$find_request['transporter_id']=$transporter_id;
					}
					else{
						$find_request['driver_id']=$user['user_id'];
						$find_request['transporter_id']=$this->transporter_id;
					}
				}
				else{
					$user_id = $user['user_id'];
					if($user['super_parent_id']>0){
						$user_id = $user['super_parent_id'];
					}
					$find_request['user_id']=$user_id;
				}
			}
			
			$user_id=0;
			$transporter_id=0;
			$driver_id=0;
		}
		
		//pickup date filter 
		if(!empty($pickup_date_from) && !empty($pickup_date_to)){
			$find_request['pickup_date >=']=date("Y-m-d",strtotime($pickup_date_from));
			$find_request['pickup_date <=']=date("Y-m-d",strtotime($pickup_date_to));
		}
		else{
			if(!empty($pickup_date_from)){
				$find_request['pickup_date']=date("Y-m-d",strtotime($pickup_date_from));
			}
			if(!empty($pickup_date_to)){
				$find_request['pickup_date']=date("Y-m-d",strtotime($pickup_date_to));
			}
		}
		
		// is allow trans cond 
		if($is_allow_trans_cond){
			$find_request['transporter_id']=array('0',$this->transporter_id);
		}
		
		// get user details 
		
		if($user_id>0 || $transporter_id>0 || $driver_id>0){
			$user_id = ($user_id)?$user_id:(($transporter_id)?$transporter_id:$driver_id);
			$find_user=array(
				'user_id'=>$user_id
			);
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_select_fields);
		}
		// get the request 
		/*echo '<pre>';
				print_r($user);
			echo '</pre>';*/
		
		// $find_request['request_status'] = 2;
		$requests = $this->getrequestsV2($find_request,$assos_cond);

		// assign data 
		$data['requests']=$requests;
		$data['user']=$user;
		$data['trailers']=$this->gettrailers();
		$data['request_status']=$this->getrequeststatus();
		$data['trailer_id']=$trailer_id;
		$data['user_id']=$user_id;
		$data['transporter_id']=$transporter_id;
		$data['request_status_key']=$request_status_key;
		$data['loadtype_id']=$loadtype_id;
		$data['loadtypes']=$this->getloadtypes();
		$data['email_phone_no']=$email_phone_no;
		$data['pickup_date_from']=$pickup_date_from;
		$data['pickup_date_to']=$pickup_date_to;
		$data['find_request']=$find_request;
		$data['assos_cond']=$assos_cond;

		$this->loadviewtrans('completed_requests', $data);
	}
	
	public function details($request_id=0){
		$data=array();
		if($request_id>0){
			$find_cond=array(
				'request_id'=>$request_id,
				'transporter_id'=>array('0',$this->transporter_id)
			);
			$request = $this->getrequests($find_cond);
			if(empty($request)){
				$this->session->set_flashdata('error','No se encontraron los detalles de la orden.');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			//
			$request = $request[0];
			$data['request']=$request;
			// now get all the bids od the request
			$find_bid=array(
				'request_id'=>$request_id,
				'user_id'=>$this->transporter_id
			);
			$extra=array();
			$requestbids = $this->getrequestbids($find_bid,$extra);
			$data['requestbids']=$requestbids;

			$rating_cond = array(
				'request_id'=>$request_id,
			);

			$rating = $this->getratings($rating_cond);
			$data['rating'] = $rating;
		}
		else{
			$this->session->set_flashdata('error','No se encontró la información de la orden.');
			redirect(BASE_FOLDER_TRANS.'requests');
		}
		$this->loadviewtrans('request_details',$data);
	}

	public function bids($request_id=0){
		$data=array();
		$creater_id=$this->session->userdata(SES_CREATOR_ID);
		if($request_id>0){
			$find_cond=array(
				'request_id'=>$request_id
			);
			//find cond of bid 
			$find_user_bid=array(
				'user_id'=>$this->transporter_id
			);
			$assos_cond=array(
				'trans_bid_cond'=>$find_user_bid
			);
			$request = $this->getrequests($find_cond,$assos_cond);
			if(empty($request)){
				$this->session->set_flashdata('error','No se encontraron los detalles de la orden.');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			$request = $request[0];
			$this->load->library(array('form_validation'));
			$rules=array(
				array(
					'field'=>'bid_amount',
					'label'=>'Bid Amount',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=>'El campo %s es requerido.'
					)
				)
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
			if($this->form_validation->run()===true){
				$bid_id = $this->input->post('bid_id');
				$bid_amount = $this->input->post('bid_amount');
				if($bid_id>0){
					$save_data=array(
						'bid_amount'=>$bid_amount,
						'update_date'=>$this->dateformat,
					);
					$update_cond=array(
						'bid_id'=>$bid_id,
						'user_id'=>$this->transporter_id,
						'request_id'=>$request_id,
					);
					// validate bid details 
					$bid = $this->BaseModel->getData($this->tableNameRequestBid,$update_cond);
					if(!empty($bid)){
						if($bid['bid_status']==0){
							$this->BaseModel->updateDatas($this->tableNameRequestBid,$save_data,$update_cond);
							$this->session->set_flashdata('success','El monto de su propuesta ha sido actualizado.');
							// create a notification for update bid amount
							$notification_data=array(
								'request_id'=>$request_id,
								'user_id'=>$this->transporter_id,
								'receiver_user_id'=>$request['user_id'],
								'notification_type'=>'17',
								'amount'=>$bid_amount
							);
							$this->add_notification($notification_data,$is_return=1);
						}
						else{
							$this->session->set_flashdata('error','No puede modificar el monto de su propuesta.');
						}
					}
					else{
						$this->session->set_flashdata('error','Detalles de la propuesta incorrectos.');
					}
					redirect(BASE_FOLDER_TRANS.'requests/bids/'.$request_id);
				}
				else{
					// insert new amount 
					$save_data=array(
						'user_id'=>$this->transporter_id,
						'request_id'=>$request_id,
						'bid_amount'=>$bid_amount,
						'creater_id'=>$creater_id,
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					
					$bid_id = $this->BaseModel->insertData($this->tableNameRequestBid,$save_data);
					if($bid_id>0){
						// now update the request in bid placed status :: 1
						if($request['request_status']==0){
							$update_request=array(
								'request_status'=>'1',
								'update_date'=>$this->dateformat
							);
							$update_cond=array(
								'request_id'=>$request_id,
								'request_status'=>'0'
							);
							// track the request status 
							$request_status_track = json_decode($request['request_status_track']);
							$request_status_track[]=array(
								'request_status'=>$update_request['request_status'],
								'create_date'=>$update_request['update_date']
							);
							$update_request['request_status_track']=json_encode($request_status_track);
							$this->BaseModel->updateDatas($this->tableNameRequest,$update_request,$update_cond);
						}
						// create a notification 
						$notification_data=array(
							'request_id'=>$request_id,
							'user_id'=>$this->transporter_id,
							'receiver_user_id'=>$request['user_id'],
							'notification_type'=>'1',
							'amount'=>$bid_amount
						);
						$this->add_notification($notification_data,$is_return=1);
						$this->session->set_flashdata('success','El monto de su propuesta ha sido guardado.');
						redirect(BASE_FOLDER_TRANS.'requests/bids/'.$request_id);
					}
					else{
						$this->session->set_flashdata('error','No se pudo guardar el monto de su propuesta.');
					}
				}
			}
			//
			$data['request']=$request;
			// now get all the bids od the request
			$find_bid=array(
				'request_id'		=>		$request_id,
				'user_id' 			=>		$this->transporter_id
			);
			$extra=array();
			$requestbids = $this->getrequestbids($find_bid,$extra);
			$data['requestbids']=$requestbids;
		}
		else{
			$this->session->set_flashdata('error','No se encontró la información de la orden.');
			redirect(BASE_FOLDER_TRANS.'requests');
		}
		
		$this->loadviewtrans('request_bid_list',$data);
	}
	
	public function bidaccept($request_id=0,$bid_action=0){
		$data=array();
		if($request_id>0){
			$find_cond=array(
				'request_id'=>$request_id
			);
			//find cond of bid 
			$find_user_bid=array(
				'user_id'=>$this->transporter_id
			);
			$assos_cond=array(
				'trans_bid_cond'=>$find_user_bid,
				'fields'=>array('request_id','user_id','bid_id','request_status_track')
			);
			$request = $this->getrequests($find_cond,$assos_cond);
			if(empty($request)){
				$this->session->set_flashdata('error','No se encontraron los detalles de la orden.');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			//
			$request = $request[0];
			// accepted bid 
			$bid_id = $request['bid_id'];
			$trans_bid_id = $request['trans_bid_id'];
			$trans_bid_status = $request['trans_bid_status'];
			$trans_bid_amount = $request['trans_bid_amount'];
			if($bid_id==$trans_bid_id){
				if($trans_bid_status==2){
					// now do the actual action
					if($bid_action==2 || $bid_action==3){
						$update_data=array(
							'bid_status'=>$bid_action,
							'update_date'=>$this->dateformat
						);
						$update_cond=array(
							'bid_id'=>$bid_id,
							'request_id'=>$request_id,
							'bid_status'=>'1',
							'user_id'=>$this->transporter_id
						);
						$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$update_cond);
						// now update the request table 
						$notification_type='0';
						if($bid_action==2){
							// bid confirmation 
							$update_req=array(
								'transporter_id'=>$this->transporter_id,
								'request_status'=>'3',// accept by transporter
								'granted_amount'=>$trans_bid_amount,
								'update_date'=>$this->dateformat,
							);
							$this->session->set_flashdata('success','La propuesta ha sido aceptada.');
							// lost all other bid of this request 
							$update_bids=array(
								'request_id'=>$request_id,
								'bid_status'=>'0',
								'bid_id !='=>$bid_id,
								'is_deleted'=>'0'
							);
							$update_data=array(
								'bid_status'=>'4', //loast
								'update_date'=>$this->dateformat,
							);
							$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$update_bids);
							$notification_type='3';
						}
						else{
							// bid cancelled
							$update_req=array(
								'request_status'=>'4',// cancel by transporter
								'update_date'=>$this->dateformat,
							);
							$this->session->set_flashdata('success','La propuesta ha sido cancelada.');
							$notification_type='4';
						}
						$update_cond=array(
							'request_id'=>$request_id,
							'bid_id'=>$bid_id,
							'request_status'=>'2' // accept by customer
						);
						// track the request status 
						$request_status_track = json_decode($request['request_status_track']);
						$request_status_track[]=array(
							'request_status'=>$update_req['request_status'],
							'create_date'=>$update_req['update_date']
						);
						$update_req['request_status_track']=json_encode($request_status_track);
						$this->BaseModel->updateDatas($this->tableNameRequest,$update_req,$update_cond);
						if($notification_type > '0'){
							// create a notification 
							$notification_data=array(
								'request_id'=>$request_id,
								'user_id'=>$this->transporter_id,
								'receiver_user_id'=>$request['user_id'],
								'notification_type'=>$notification_type,
							);
							$this->add_notification($notification_data,$is_return=1);
						}
					}
					else{
						$this->session->set_flashdata('error','No se encontró el estado de la propuesta.');
					}
				}
				else{
					$this->session->set_flashdata('error','Estado de la propuesta incorrecto.');
				}
			}
			else{
				$this->session->set_flashdata('error','Orden incorrecta.');
			}
		}
		else{
			$this->session->set_flashdata('error','No se encontró la información de la orden.');
		}
		redirect(BASE_FOLDER_TRANS.'requests');
	}
	
	public function assigndriver($request_id=0){
		$data=array();
		if($request_id>0){
			$find_cond=array(
				'request_id'=>$request_id,
				'request_status'=>'3',
				'transporter_id'=>$this->transporter_id
			);
			
			$request = $this->getrequests($find_cond);
			if(empty($request)){
				$this->session->set_flashdata('error','No se encontraron los detalles de la orden.');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			$request = $request[0];
			$data['request']=$request;
			//
			$this->load->library(array('form_validation'));
			$rules = array(
				array(
					'field'=>'driver_id',
					'label'=>'Driver',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=> 'El campo %s es requerido.'
					)
				),
				array(
					'field'=>'vehicle_id',
					'label'=>'Vehicle',
					'rules'=>'trim|required|greater_than[0]',
					'errors'=>array(
						'greater_than'=> 'El campo %s es requerido.'
					)
				),
			);
			
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
			if($this->form_validation->run()===true){
				$driver_id = $this->input->post('driver_id');
				$vehicle_id = $this->input->post('vehicle_id');
				$update_request=array(
					'driver_id'=>$driver_id,
					'vehicle_id'=>$vehicle_id,
					'request_status'=>'5', // driver assign
					'update_date'=>$this->dateformat
				);
				// track the request status 
				$request_status_track = json_decode($request['request_status_track']);
				$request_status_track[]=array(
					'request_status'=>$update_request['request_status'],
					'create_date'=>$update_request['update_date']
				);
				$update_request['request_status_track']=json_encode($request_status_track);
				
				$this->BaseModel->updateDatas($this->tableNameRequest,$update_request,$find_cond);
				
				// now change the status of the driver 
				$update_driver=array(
					'user_status'=>'1',
					'update_date'=>$this->dateformat
				);
				$user_cond=array(
					'user_id'=>$driver_id,
					'user_status !='=>'3'
				);
				$this->BaseModel->updateDatas($this->tableNameUser,$update_driver,$user_cond);
				
				// vehicle section 
				$update_vehicle=array(
					'vehicle_status'=>'1',
					'update_date'=>$this->dateformat
				);
				$vehicle_cond=array(
					'vehicle_id'=>$vehicle_id,
					'vehicle_status !='=>'3'
				);
				$this->BaseModel->updateDatas($this->tableNameVehicle,$update_vehicle,$vehicle_cond);
				
				// notification section
				// create a notification for customer
				$notification_data=array(
					'request_id'=>$request_id,
					'user_id'=>$this->transporter_id,
					'receiver_user_id'=>$request['user_id'],
					'notification_type'=>'5'
				);
				$this->add_notification($notification_data,$is_return=1);
				// create a notification for driver
				$notification_data=array(
					'request_id'=>$request_id,
					'user_id'=>$this->transporter_id,
					'receiver_user_id'=>$driver_id,
					'notification_type'=>'6'
				);
				$this->add_notification($notification_data,$is_return=1);
				
				$this->session->set_flashdata('success','El conductor y vehículo han sido asignados.');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			// get drivers 
			$find_driver=array(
				'parent_user_id'=>$this->transporter_id,
				'user_status !='=>'3',// not in transit
			);
			$data['drivers']=$this->getdrivers($find_driver);
			// get vehicles
			$find_vehicle=array(
				'user_id'=>$this->transporter_id,
				// 'trailer_id'=>$request['trailer_id'],
				'vehicle_status !='=>'3',// not in transit
			);
			$data['vehicles']=$this->getvehicles($find_vehicle);
			//$this->pr($data);
			$this->loadviewtrans('assign_driver',$data);
		}
		else{
			$this->session->set_flashdata('error','No se encontró la información de la orden.');
			redirect(BASE_FOLDER_TRANS.'requests');
		}
	}
}
?>