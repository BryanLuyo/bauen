<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Vehicledocuments extends MY_Controller{
	function __construct(){
		parent::__construct();
		// session validation checked 
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$user_id=0;
		$vehicle_id=0;
		$email_phone_no='';
		$documenttype_id=0;
		$plate_no='';
		$user=array();
		$vehicle=array();
		$user_selected_fileds = array('user_id','first_name','last_name','phone_no','email');
		$vehicle_selected_fileds = array('vehicle_id','plate_no','purchase_year');
		// fileter option 
		if($this->input->post('user_id')){
			$user_id = $this->input->post('user_id');
		}
		if($this->input->post('vehicle_id')){
			$vehicle_id = $this->input->post('vehicle_id');
		}
		if($this->input->post('documenttype_id')){
			$documenttype_id = $this->input->post('documenttype_id');
		}
		
		if($this->input->post('email_phone_no')){
			$email_phone_no = $this->input->post('email_phone_no');
		}
		
		if($this->input->post('plate_no')){
			$plate_no = $this->input->post('plate_no');
		}
		
		$find_vehicle_docs=array();
		if($user_id>0){
			$find_vehicle_docs['user_id']=$user_id;
		}
		if($vehicle_id>0){
			$find_vehicle_docs['vehicle_id']=$vehicle_id;
		}
		if($documenttype_id>0){
			$find_vehicle_docs['documenttype_id']=$documenttype_id;
		}
		if(!empty($email_phone_no)){
			$user_id=0;
			$vehicle_id=0;
			$find_user=array();
			if(filter_var($email_phone_no,FILTER_VALIDATE_EMAIL)){
				$find_user['email']=$email_phone_no;
			}
			else{
				$find_user['phone_no']=$email_phone_no;
			}
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_selected_fileds);
			if(!empty($user)){
				$find_vehicle_docs['user_id']=$user['user_id'];
			}
			else{
				$find_vehicle_docs['user_id']='0';
			}
		}
		if(!empty($plate_no)){
			$user_id=0;
			$vehicle_id=0;
			$find_vehicle=array(
				'plate_no'=>$plate_no
			);
			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle,$vehicle_selected_fileds);
			if(!empty($vehicle)){
				$find_vehicle_docs['vehicle_id'] = $vehicle['vehicle_id'];
			}
			else{
				$find_vehicle_docs['vehicle_id'] ='0';
			}
		}
		$select_fields=array();
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameVehicle,
				'join_with'=>$this->tableNameVehicleDocument,
				'join_type'=>'inner',
				'join_on'=>array('vehicle_id'=>'vehicle_id'),
				'select_fields'=>$vehicle_selected_fileds,
			),
			array(
				'table_name'=>$this->tableNameDocumentType,
				'join_with'=>$this->tableNameVehicleDocument,
				'join_type'=>'inner',
				'join_on'=>array('documenttype_id'=>'documenttype_id'),
				'select_fields'=>array('document_title')
			),
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameVehicleDocument,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>$user_selected_fileds
			)
		);
		$vehicledocuments = $this->BaseModel->getDatas($this->tableNameVehicleDocument,$find_vehicle_docs,$select_fields,$order_by,$joins);
		
		// common section 
		if($user_id>0){
			$user_find=array(
				'user_id'=>$user_id
			);
			$user = $this->BaseModel->getData($this->tableNameUser,$user_find,$user_selected_fileds);
		}
		if($vehicle_id>0){
			$find_vehicle=array(
				'vehicle_id'=>$vehicle_id
			);
			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle,$vehicle_selected_fileds);
		}
		$find_doctype=array(
			'document_for'=>'2'
		);
		$documenttypes = $this->getdocumenttypes($find_doctype);
		// assing data into view
		$data['documenttypes']=$documenttypes;
		$data['vehicledocuments']=$vehicledocuments;
		$data['user_id']=$user_id;
		$data['vehicle_id']=$vehicle_id;
		$data['documenttype_id']=$documenttype_id;
		$data['email_phone_no']=$email_phone_no;
		$data['plate_no']=$plate_no;
		$data['user']=$user;
		$data['vehicle']=$vehicle;
		$this->loadview('vehicle_document_list',$data);
	}
	
	public function view($vehicle_document_id=0){
		$data=array();
		if($vehicle_document_id>0){
			$vehicle_selected_fileds = array('vehicle_id','plate_no','purchase_year');
			$find_cond=array(
				'vehicle_document_id'=>$vehicle_document_id
			);
			$joins=array(
				array(
					'table_name'=>$this->tableNameDocumentType,
					'join_with'=>$this->tableNameVehicleDocument,
					'join_type'=>'inner',
					'join_on'=>array('documenttype_id'=>'documenttype_id'),
					'select_fields'=>array('document_title')
				)
			);
			$vehicledocument = $this->BaseModel->getData($this->tableNameVehicleDocument,$find_cond,array(),array(),$joins);
			if(empty($vehicledocument)){
				$this->session->set_flashdata('error','Vehicle Document details not found');
				redirect(BASE_FOLDER.'vehicledocuments');
			}
			$vehicledocument['file_name']=base_url('uploads/documents/'.$vehicledocument['file_name']);
			$find_vehicle=array(
				'vehicle_id'=>$vehicledocument['vehicle_id']
			);
			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle,$vehicle_selected_fileds);
			$data['document']=$vehicledocument;
			$data['vehicle']=$vehicle;
		}
		else{
			$this->session->set_flashdata('error','Vehicle Document info missing');
			redirect(BASE_FOLDER.'vehicledocuments');
		}
		// form section 
		if($this->input->server('REQUEST_METHOD')==strtoupper('post')){
			$cancelled = $this->input->post('cancelled');
			$approved = $this->input->post('approved');
			$update_data=array(
				'update_date'=>$this->dateformat
			);
			if($cancelled=='Reject'){
				$update_data['document_status']='2';
				$this->session->set_flashdata('success','Document cancelled successfully');
			}
			
			if($approved=='Approve'){
				$update_data['document_status']='1';
				$this->session->set_flashdata('success','Document approved successfully');
			}
			$find_doc=array(
				'vehicle_document_id'=>$vehicle_document_id
			);
			$this->BaseModel->updateDatas($this->tableNameVehicleDocument,$update_data,$find_doc);
			redirect(BASE_FOLDER.'vehicledocuments/view/'.$vehicle_document_id);
		}
		$this->loadview('document_view',$data);
	}
	
	/*
	public function view($vehicle_document_id=0){
		$data=array();
		if($vehicle_document_id>0){
			$user_selected_fileds = array('user_id','first_name','last_name','phone_no','email');
			$vehicle_selected_fileds = array('vehicle_id','plate_no','purchase_year');
			$find_cond=array(
				'vehicle_document_id'=>$vehicle_document_id
			);
			$select_flds=array();
			$order_by=array();
			$joins=array(
				array(
					'table_name'=>$this->tableNameVehicle,
					'join_with'=>$this->tableNameVehicleDocument,
					'join_type'=>'inner',
					'join_on'=>array('vehicle_id'=>'vehicle_id'),
					'select_fields'=>$vehicle_selected_fileds,
				),
				array(
					'table_name'=>$this->tableNameDocumentType,
					'join_with'=>$this->tableNameVehicleDocument,
					'join_type'=>'inner',
					'join_on'=>array('documenttype_id'=>'documenttype_id'),
					'select_fields'=>array('document_title')
				),
				array(
					'table_name'=>$this->tableNameUser,
					'join_with'=>$this->tableNameVehicleDocument,
					'join_type'=>'inner',
					'join_on'=>array('user_id'=>'user_id'),
					'select_fields'=>$user_selected_fileds
				)
			);
			$vehicledocument = $this->BaseModel->getData($this->tableNameVehicleDocument,$find_cond,$select_flds,$order_by,$joins);
			if(empty($vehicledocument)){
				$this->session->set_flashdata('error','Vehicle Document details not found');
				redirect(BASE_FOLDER.'vehicledocuments');
			}
			$data['document']=$vehicledocument;
		}
		else{
			$this->session->set_flashdata('error','Vehicle Document info missing');
			redirect(BASE_FOLDER.'vehicledocuments');
		}
		
		$this->loadview('document_view',$data);
	}
	*/
}
?>