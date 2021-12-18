<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Drivers extends My_Controller{
	public $transporter_id;
	function __construct(){
		parent::__construct();
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);
	}
	
	public function index(){
		$data=array();
		$find_driver=array(
			'parent_user_id'=>$this->transporter_id,
			'user_type'=>'1',
			'is_company'=>'0',
		);
		$drivers = $this->getdrivers($find_driver);
		
		// assing data in view 
		$data['drivers']=$drivers;
		$this->loadviewtrans('driver_list',$data);
	}
	public function add(){
		$this->trans_session_off();
		$data=array();
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email|callback_user_unique_email',
				'errors'=>array(
					'user_unique_email'=>'This %s is already exists.'
				)
			),
			array(
				'field'=>'phone_no',
				'label'=>'Phone No.',
				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no',
				'errors'=>array(
					'valid_phone_no'=>'The %s field must contain a valid phone no.',
					'user_unique_phone_no'=>'This %s is already exists.'
				)
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'dni_no',
				'label'=>'DNI No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'ruc_no',
				'label'=>'RUC No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$user_id=$this->transporter_id;
			$verification_code = rand(9999,1000000);
			$password = $this->input->post('password');
			$save_data=array(
				'parent_user_id'=>$user_id,
				'user_type'=>'1',
				'is_company'=>'0',
				'verification_code'=>$verification_code,
				'is_phone_no_verify'=>'1',
				'is_user_verify'=>'1',
				'ruc_no'=>$this->input->post('ruc_no'),
				'dni_no'=>$this->input->post('dni_no'),
				'phone_no'=>$this->input->post('phone_no'),
				'email'=>$this->input->post('email'),
				'last_name'=>$this->input->post('last_name'),
				'first_name'=>$this->input->post('first_name'),
				'password'=>md5($password),
				'showpass'=>$password,
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			// image section 
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image = $_FILES['image'];
				$image_name = $this->uploadimage($image,'users');
				if(!empty($image_name)){
					$save_data['image']=$image_name;
				}
			}
			$driver_id = $this->BaseModel->insertData($this->tableNameUser,$save_data);
			if($driver_id>0){
				$this->session->set_flashdata('success','Driver details saved successfully');
				redirect(BASE_FOLDER_TRANS.'drivers');
			}
			else{
				$this->session->set_flashdata('error','Driver details saving faild');
			}
		}
		$this->loadviewtrans('driver_add',$data);
	}
	
	public function edit($driver_id=0){
		$this->trans_session_off();
		$data=array();
		if($driver_id>0){
			$find_driver=array(
				'user_id'=>$driver_id,
				'parent_user_id'=>$this->transporter_id,
				'user_type'=>'1',
				'is_company'=>'0'
			);
			$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver);
			if(empty($driver)){
				$this->session->set_flashdata('error','Driver details not found.');
				redirect(BASE_FOLDER_TRANS.'drivers');
			}
			$data['driver']=$driver;
		}
		else{
			$this->session->set_flsahdata('error','Driver info missing');
			redirect(BASE_FOLDER_TRANS.'drivers');
		}
		
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'email',
				'label'=>'Email',
				'rules'=>'trim|required|valid_email|callback_user_unique_email['.$driver_id.']',
				'errors'=>array(
					'user_unique_email'=>'This %s is already exists.'
				)
			),
			array(
				'field'=>'phone_no',
				'label'=>'Phone No.',
				'rules'=>'trim|required|callback_valid_phone_no|callback_user_unique_phone_no['.$driver_id.']',
				'errors'=>array(
					'valid_phone_no'=>'The %s field must contain a valid phone no.',
					'user_unique_phone_no'=>'This %s is already exists.'
				)
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'dni_no',
				'label'=>'DNI No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			array(
				'field'=>'ruc_no',
				'label'=>'RUC No.',
				'rules'=>'trim|required',
				'errors'=>array()
			),
			
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$old_image = $driver['image'];
			
			$password = $this->input->post('password');
			$save_data=array(
				'ruc_no'=>$this->input->post('ruc_no'),
				'dni_no'=>$this->input->post('dni_no'),
				'phone_no'=>$this->input->post('phone_no'),
				'email'=>$this->input->post('email'),
				'last_name'=>$this->input->post('last_name'),
				'first_name'=>$this->input->post('first_name'),
				'password'=>md5($password),
				'showpass'=>$password,
				'update_date'=>$this->dateformat,
			);
			// image section 
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image = $_FILES['image'];
				$image_name = $this->uploadimage($image,'users');
				if(!empty($image_name)){
					$save_data['image']=$image_name;
					//remove old image 
					$this->removeimage($old_image,'users');
				}
			}
			$this->BaseModel->updateDatas($this->tableNameUser,$save_data,$find_driver);
			$this->session->set_flashdata('success','Driver details updated successfully');
			redirect(BASE_FOLDER_TRANS.'drivers');
		}
		$this->loadviewtrans('driver_edit',$data);
	}

	public function documents($driver_id=0){
		$data=array();
		if($driver_id>0){
			$find_driver = array(
				'user_id'=>$driver_id,
				'parent_user_id'=>$this->transporter_id,
				'user_type'=>'1',
				'is_company'=>'0'
			);
			$select_flds=array('user_id','first_name','last_name','email','phone_no');
			$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver,$select_flds);
			if(empty($driver)){
				$this->session->set_flashdata('error','Driver details not found');
				redirect(BASE_FOLDER_TRANS.'drivers');
			}
			$data['user']=$driver;
		}
		else{
			$this->session->set_flashdata('error','Driver info missing');
			redirect(BASE_FOLDER_TRANS.'drivers');
		}
		// find doct list 
		$documenttype_id=0;
		if($this->input->post('documenttype_id')){
			$documenttype_id = $this->input->post('documenttype_id');
		}
		$find_docs=array(
			'user_id'=>$driver_id,
			'uploader_id'=>$this->transporter_id
		);
		if($documenttype_id>0){
			$find_docs['documenttype_id']=$documenttype_id;
		}
		$userdocuments = $this->getuserdocuments($find_docs);
		
		$find_doc=array(
			'document_for'=>'1'
		);
		$data['documenttypes']=$this->getdocumenttypes($find_doc);
		$data['userdocuments']=$userdocuments;
		$data['documenttype_id']=$documenttype_id;
		$this->loadviewtrans('driver_document_list',$data);
	}
	
	public function adddocument($driver_id=0){
		$data=array();
		if($driver_id>0){
			$find_driver = array(
				'user_id'=>$driver_id,
				'parent_user_id'=>$this->transporter_id,
				'user_type'=>'1',
				'is_company'=>'0'
			);
			$select_flds=array('user_id','first_name','last_name','email','phone_no','is_company');
			$driver = $this->BaseModel->getData($this->tableNameUser,$find_driver,$select_flds);
			if(empty($driver)){
				$this->session->set_flashdata('error','Driver details not found');
				redirect(BASE_FOLDER_TRANS.'drivers');
			}
			$data['user']=$driver;
		}
		else{
			$this->session->set_flashdata('error','Driver info missing');
			redirect(BASE_FOLDER_TRANS.'drivers');
		}
		//
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
						'user_id'=>$driver_id,
						'uploader_id'=>$this->transporter_id,
						'document_status'=>'0',
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$document_id = $this->BaseModel->insertData($this->tableNameUserDocument,$save_data);
					if($document_id>0){
						$this->session->set_flashdata('success','Document File uploaded successfully');
						redirect(BASE_FOLDER_TRANS.'drivers/adddocument/'.$driver_id);
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
		// assing data 
		$doc_cond=array(
			'document_for'=>'1'
		);
		$data['documenttypes']=$this->getdocumenttypes($doc_cond);
		$this->loadviewtrans('user_document_add',$data);
	}

	public function viewdocument($document_id=0){
		$data=array();
		if($document_id>0){
			$find_doct=array(
				'user_document_id'=>$document_id
			);
			$document = $this->getuserdocuments($find_doct);
			if(empty($document)){
				$this->session->set_flashdata('error','Document details not found');
				redirect(BASE_FOLDER_TRANS.'drivers');
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
			redirect(BASE_FOLDER_TRANS.'drivers');
		}
		$this->loadviewtrans('document_view',$data);
	}
}
?>