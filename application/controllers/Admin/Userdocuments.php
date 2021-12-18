<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Userdocuments extends MY_Controller{
	function __construct(){
		parent::__construct();
		// session validation checked 
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$user_id=0;
		$parent_user_id=0;
		$documenttype_id=0;
		$email_phone_no='';
		$user=array();
		$user_selected_fileds = array('user_id','first_name','last_name','phone_no','email');
		
		// user filter option 
		if($this->input->post('user_id')){
			$user_id = $this->input->post('user_id');
		}
		if($this->input->post('parent_user_id')){
			$parent_user_id = $this->input->post('parent_user_id');
		}
		if($this->input->post('documenttype_id')){
			$documenttype_id = $this->input->post('documenttype_id');
		}
		if($this->input->post('email_phone_no')){
			$email_phone_no = $this->input->post('email_phone_no');
		}
		// assing in to onditions
		$find_user_docs=array();
		if($user_id>0){
			$find_user_docs['user_id']=$user_id;
		}
		if($parent_user_id>0){
			$find_user_docs['uploader_id']=$parent_user_id;
		}
		if($documenttype_id>0){
			$find_user_docs['documenttype_id']=$documenttype_id;
		}
		if(!empty($email_phone_no)){
			$user_id=0;
			$parent_user_id=0;
			$find_user=array();
			if(filter_var($email_phone_no,FILTER_VALIDATE_EMAIL)){
				$find_user['email']=$email_phone_no;
			}
			else{
				$find_user['phone_no']=$email_phone_no;
			}
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$user_selected_fileds);
			if(!empty($user)){
				$find_user_docs['user_id']=$user['user_id'];
			}
			else{
				$find_user_docs['user_id']='0';
			}
		}
		
		$select_fileds=array();
		$order_by=array();
		$joins=array(
			array(
				'table_name'=>$this->tableNameUser,
				'join_with'=>$this->tableNameUserDocument,
				'join_type'=>'inner',
				'join_on'=>array('user_id'=>'user_id'),
				'select_fields'=>array('first_name','last_name','email','phone_no')
			),
			array(
				'table_name'=>$this->tableNameDocumentType,
				'join_with'=>$this->tableNameUserDocument,
				'join_type'=>'inner',
				'join_on'=>array('documenttype_id'=>'documenttype_id'),
				'select_fields'=>array('document_title')
			),
		);
		$userdocuments = $this->BaseModel->getDatas($this->tableNameUserDocument,$find_user_docs,$select_fileds,$order_by,$joins);
		// common section 
		$find_doctype=array(
			'document_for'=>'1'
		);
		$documenttypes = $this->getdocumenttypes($find_doctype);
		
		// get user details
		$user_cond=array();
		if($user_id>0){
			$user_cond['user_id']=$user_id;
		}
		elseif($parent_user_id){
			$user_cond['user_id']=$parent_user_id;
		}
		if(!empty($user_cond)){
			$user = $this->BaseModel->getData($this->tableNameUser,$user_cond,$user_selected_fileds);
		}
		
		// assing data into view
		$data['documenttypes']=$documenttypes;
		$data['documenttype_id']=$documenttype_id;
		$data['userdocuments']=$userdocuments;
		$data['user_id']=$user_id;
		$data['parent_user_id']=$parent_user_id;
		$data['email_phone_no']=$email_phone_no;
		$data['user']=$user;
		$this->loadview('user_document_list',$data);
	}
	
	public function view($user_document_id=0){
		$data=array();
		if($user_document_id>0){
			$find_document = array(
				'user_document_id'=>$user_document_id
			);
			$joins=array(
				array(
					'table_name'=>$this->tableNameDocumentType,
					'join_with'=>$this->tableNameUserDocument,
					'join_type'=>'inner',
					'join_on'=>array('documenttype_id'=>'documenttype_id'),
					'select_fields'=>array('document_title')
				)
			);
			$document = $this->BaseModel->getData($this->tableNameUserDocument,$find_document,array(),array(),$joins);
			if(!empty($document)){
				$user_id = $document['user_id'];
				$document['file_name']=base_url('uploads/documents/'.$document['file_name']);
				$find_user=array(
					'user_id'=>$user_id
				);
				$select_fields=array('user_id','first_name','last_name','email','phone_no');
				$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$select_fields);
				$data['user']=$user;
				$data['document']=$document;
			}
			else{
				$this->session->set_flashdata('error','Document details not found.');
				redirect(BASE_FOLDER.'userdocuments');
			}
		}
		else{
			$this->session->set_flashdata('error','Document info missing.');
			redirect(BASE_FOLDER.'userdocuments');
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
				'user_document_id'=>$user_document_id
			);
			$this->BaseModel->updateDatas($this->tableNameUserDocument,$update_data,$find_doc);
			redirect(BASE_FOLDER.'userdocuments/view/'.$user_document_id);
		}
		$this->loadview('document_view',$data);
	}
}
?>