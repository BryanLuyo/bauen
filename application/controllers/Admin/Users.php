<?php

class Users extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$find_user=array(
			'user_type'=>'0',
			'super_parent_id'=>'0',
		);
		$select_fields=array();
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameIndustryType,
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('industrytype_id'=>'industrytype_id'),
				'select_fields'=>array('industrytype_name')
			)
		);
		$users = $this->BaseModel->getDatas($this->tableNameUser,$find_user,$select_fields,$order_by,$joins);
		// set data 
		$data['users']=$users;
		$this->loadview('user_list',$data);
	}
	
	public function transporters(){
		$data=array();
		$find_user=array(
			'user_type'=>'1', // driver or transporter
			'is_company'=>'1',
			'super_parent_id'=>'0'
		);
		$select_fields=array();
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameIndustryType,
				'join_with'=>$this->tableNameUser,
				'join_type'=>'left',
				'join_on'=>array('industrytype_id'=>'industrytype_id'),
				'select_fields'=>array('industrytype_name')
			)
		);
		$users = $this->BaseModel->getDatas($this->tableNameUser,$find_user,$select_fields,$order_by,$joins);
		// set data 
		$data['users']=$users;
		$this->loadview('transporter_list',$data);
	}
	
	public function drivers($user_id=0){
		$data=array();
		if($user_id){
			// validate the user as transporter 
			$find_cond=array(
				'user_id'=>$user_id,
				'user_type'=>'1',
				'is_company'=>'1'
			);
			$select_flds=array('first_name','last_name','phone_no','email');
			$transporter = $this->BaseModel->getData($this->tableNameUser,$find_cond,$select_flds);
			if(empty($transporter)){
				$this->session->set_flashdata('error','Transporter details not found');
				redirect(BASE_FOLDER.'users/transporters');
			}
			$data['transporter']=$transporter;
		}
		else{
			$this->session->set_flashdata('error','Transporter info missing');
			redirect(BASE_FOLDER.'users/transporters');
		}
		// find drivers 
		$find_driver=array(
			'parent_user_id'=>$user_id,
			'user_type'=>'1'
		);
		$select_fields=array();
		$drivers = $this->BaseModel->getDatas($this->tableNameUser,$find_driver,$select_fields);
		// asing data 
		$data['drivers']=$drivers;
		$this->loadview('driver_list',$data);
	}
	
	public function vehicles($user_id=0){
		$data=array();
		if($user_id>0){
			// validate the user as transporter 
			$find_cond=array(
				'user_id'=>$user_id,
				'user_type'=>'1',
				'is_company'=>'1'
			);
			$select_flds=array('first_name','last_name','phone_no','email');
			$transporter = $this->BaseModel->getData($this->tableNameUser,$find_cond,$select_flds);
			if(empty($transporter)){
				$this->session->set_flashdata('error','Transporter details not found');
				redirect(BASE_FOLDER.'users/transporters');
			}
			$data['transporter']=$transporter;
		}
		else{
			$this->session->set_flashdata('error','Transporter info missing');
			redirect(BASE_FOLDER.'users/transporters');
		}
		$trailer_id=0;
		// find vehicle section 
		$find_vehicle=array(
			'user_id'=>$user_id
		);
		//filter option
		if($this->input->post('trailer_id')){
			$trailer_id = $this->input->post('trailer_id');
		}
		if($trailer_id>0){
			$find_vehicle['trailer_id']=$trailer_id;
		}
		$select_flds=array();
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameTrailer,
				'join_with'=>$this->tableNameVehicle,
				'join_type'=>'inner',
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'select_fields'=>array('name trailer_name')
			)
		);
		$vehicles = $this->BaseModel->getDatas($this->tableNameVehicle,$find_vehicle,$select_flds,$order_by,$joins);
		// common section
		// assing the vehicles 
		$data['vehicles']=$vehicles;
		$data['trailers']=$this->gettrailers();
		$data['trailer_id']=$trailer_id;
		$this->loadview('vehicle_list',$data);
	}
}
?>