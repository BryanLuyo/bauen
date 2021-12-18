<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Documents extends MY_Controller{
	public $transporter_id;
	function __construct(){
		parent::__construct();
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);
	}
	
	public function index(){
		$data=array();
		$find_doct=array(
			'user_id'=>$this->transporter_id,
			'uploader_id'=>$this->transporter_id,
		);
		$userdocuments = $this->getuserdocuments($find_doct);
		$find_doc_type=array(
			'document_for'=>'1'
		);
		$data['documenttypes']=$this->getdocumenttypes($find_doc_type);
		$data['userdocuments']=$userdocuments;
		$this->loadviewtrans('user_document_list',$data);
	}
	
	public function add(){
		$data=array();
		$this->load->library(array('form_validation'));
		$rules = array(
			array(
				'field'=>'documenttype_id',
				'label'=>'Document Type',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array(
					'greater_than'=>'The %s field is required.'
				)
			)
		);
		if(!isset($_FILES['image']['name']) || empty($_FILES['image']['name'])){
			$rules[]=array(
				'field'=>'imgreq',
				'label'=>'Document File',
				'rules'=>'trim|required',
				'errors'=>array()
			);
		}
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			// again validate image section 
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$file_name = $this->uploadimage($_FILES['image'],'documents');
				if(!empty($file_name)){
					$save_data=array(
						'documenttype_id'=>$this->input->post('documenttype_id'),
						'file_name'=>$file_name,
						'user_id'=>$this->transporter_id,
						'uploader_id'=>$this->transporter_id,
						'document_status'=>'0',
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$document_id = $this->BaseModel->insertData($this->tableNameUserDocument,$save_data);
					if($document_id>0){
						$this->session->set_flashdata('success','Document File uploaded successfully');
						redirect(BASE_FOLDER_TRANS.'documents/add');
					}
					else{
						$this->session->set_flashdata('success','Document File uploading faild');
					}
				}
				else{
					$this->session->set_flashdata('error','Document File uploading faild');
				}
			}
			else{
				$this->session->set_flashdata('error','Document File is required.');
			}
		}
		//value assing
		$find_doc_type=array(
			'document_for'=>'1'
		);
		$data['documenttypes']=$this->getdocumenttypes($find_doc_type);
		$this->loadviewtrans('user_document_add',$data);
	}
	
	public function view($document_id=0){
		$data=array();
		if($document_id>0){
			$find_doct=array(
				'user_document_id'=>$document_id
			);
			$document = $this->getuserdocuments($find_doct);
			if(empty($document)){
				$this->session->set_flashdata('error','Document details not found');
				redirect(BASE_FOLDER_TRANS.'documents');
			}
			$data['document']=$document[0];
			// get user details 
			$find_user=array(
				'user_id'=>$document[0]['user_id']
			);
			$select_flds=array('user_id','first_name','last_name','email','phone_no','is_company');
			$user = $this->BaseModel->getData($this->tableNameUser,$find_user,$select_flds);
			$data['user']=$user;
		}
		else{
			$this->session->set_flashdata('error','Document info missing');
			redirect(BASE_FOLDER_TRANS.'documents');
		}
		$this->loadviewtrans('document_view',$data);
	}
	
}
?>