<?php

class Admins extends MY_Controller{
	function __construct(){
		parent::__construct();
	}
	
	public function login(){
		$this->admin_session_on();
		$data=array();
		$this->load->library(array('form_validation'));
		$rules = array(
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email|callback_emailpresent',
				'errors'=>array(
					'emailpresent'=>'%s not found'
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
			$find_admin=array(
				'email'=>$this->input->post('email'),
				'password'=>md5($this->input->post('password'))
			);
			$select_fields=array('admin_id','name','is_super_admin','admin_type');
			$admin = $this->BaseModel->getData($this->tableNameAdmin,$find_admin,$select_fields);
			//$this->pr($admin);
			if(empty($admin)){
				$this->session->set_flashdata('error','Email or password does not match');
			}
			else{
				$this->admin_session_set($admin);
				redirect(BASE_FOLDER.'dashboard');
			}
		}
		
		$this->loadview('login',$data);
	}
	
	public function emailpresent($email=''){
		if(!empty($email)){
			$find_cond = array(
				'email'=>$email
			);
			$tableRow = $this->BaseModel->tableRow($this->tableNameAdmin,$find_cond);
			if($tableRow){
				return true;
			}
		}
		return false;
	}
	
	public function dashboard(){
		$this->admin_session_off();
		$data=array();
		$staticson=array('customers'=>'0','transporters'=>'0','requests'=>'0');
		$total_counts=array(
			'today'=>$staticson,
			'week'=>$staticson,
			'month'=>$staticson,
		);
		$select_section='count(*) total_record';
		$week_no = date("W");
		$month_no = date("n");
		$yr=date("Y");
		foreach($total_counts as $key=>$static){
			foreach($static as $stat_for=>$stat_val){
				$table_name='';
				$main_fld='create_date';
				$conditions=array();
				if($stat_for=='customers'){
					$table_name=$this->tableNameUser;
					$conditions['user_type']='0';
					$conditions['is_user_verify']='1';
				}
				elseif($stat_for=='transporters'){
					$table_name=$this->tableNameUser;
					$conditions['user_type']='2';
					$conditions['is_company']='1';
					$conditions['is_user_verify']='1';
				}
				elseif($stat_for=='requests'){
					$table_name=$this->tableNameRequest;
					$conditions['user_id >']='0';
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
		//get all request 
		$find_cond=array(
			'user_id >'=>'0',
			'request_status'=>'0'
		);
		$requests = $this->getrequests($find_cond);
		//assing data 
		$data['total_counts']=$total_counts;
		$data['requests']=$requests;
		$this->loadview('dashboard',$data);
	}
	
	public function logout(){
		$this->admin_session_destroy();
	}
}
?>