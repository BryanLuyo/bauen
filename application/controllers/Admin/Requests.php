<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Requests extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$user_id=0;
		$transporter_id=0;
		$driver_id=0;
		$trailer_id=0;
		$request_status_key=-1;
		$loadtype_id=0;
		$email_phone_no='';
		$pickup_date_from='';
		$pickup_date_to='';
		$user=array();
		// user select flds
		$user_select_fields=array('user_id','first_name','last_name','email','phone_no','user_type','is_company');
		
		$find_request=array();
		$assos_cond=array(
			'fields'=>array(),
			'order_by'=>array(),
			'count'=>'0'
		);
		// filter section 
		if($this->input->server('REQUEST_METHOD')=='POST'){
			if($this->input->post('user_id')){
				$user_id=$this->input->post('user_id');
			}
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
		if($transporter_id>0){
			$find_request['transporter_id']=$transporter_id;
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
		
		if($request_status_key>-1){
			$find_request['request_status']=$request_status_key;
			$request_status_key+=1;
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
					// transporter section
					if($user['is_company']){
						$find_request['transporter_id']=$user['user_id'];
					}
					else{
						$find_request['driver_id']=$user['user_id'];
					}
				}
				else{
					$find_request['user_id']=$user['user_id'];
				}
			}
			else{
				$this->session->set_flashdata('error','No record found for this search<br> Show all results');
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
		
		// get user details 
		
		if($user_id>0 || $transporter_id>0 || $driver_id>0){
			$user_id = ($user_id)?$user_id:(($transporter_id)?$transporter_id:$driver_id);
			$find_user=array(
				'user_id'=>$user_id
			);
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_select_fields);
		}
		// get the request 
		
		$requests = $this->getrequestsV2($find_request,$assos_cond);
		//$requests = $this->getrequests();
		
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
		$this->loadview('request_list',$data);
	}
	
	public function details($request_id=0){
		$data=array();
		if($request_id>0){
			$find_cond=array(
				'request_id'=>$request_id
			);
			$request = $this->getrequests($find_cond);
			if(empty($request)){
				$this->session->set_flashdata('error','Request details not found');
				redirect(BASE_FOLDER.'requests');
			}
			//
			$request = $request[0];
			$data['request']=$request;
			// now get all the bids od the request
			$find_bid=array(
				'request_id'=>$request_id
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
			$this->session->set_flashdata('error','Request info missing');
			redirect(BASE_FOLDER.'requests');
		}
		$this->loadview('request_details',$data);
	}
	
	public function deletebid($bid_id=0,$request_id=0){
		if($bid_id>0 && $request_id>0){
			$find_cond=array(
				'bid_id'=>$bid_id,
				'request_id'=>$request_id,
				'bid_status'=>'0'
			);
			$bid = $this->BaseModel->getData($this->tableNameRequestBid,$find_cond);
			if(!empty($bid)){
				$update_data=array(
					'update_date'=>$this->dateformat,
					'is_admin_delete'=>'1'
				);
				$this->BaseModel->updateDatas($this->tableNameRequestBid,$update_data,$find_cond);
				$this->session->set_flashdata('success','Bid details removed successfully');
			}
			else{
				$this->session->set_flashdata('error','Invalid Bid Information');
			}
		}
		// redirect section 
		if($request_id>0){
			redirect(BASE_FOLDER.'requests/details/'.$request_id);
		}
		else{
			$this->session->set_flashdata('error','Request info missing');
			redirect(BASE_FOLDER.'requests');
		}
	}
	
	
	
		
	
		
}
?>