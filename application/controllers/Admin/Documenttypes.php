<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Documenttypes extends MY_Controller{
	function __construct(){
		parent::__construct();
		// session validation 
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$find_doctype=array();
		$documenttypes = $this->BaseModel->getDatas($this->tableNameDocumentType,$find_doctype);
		// data assing 
		$data['documenttypes']=$documenttypes;
		$data['document_fors']=$this->getdocument_for();
		$this->loadview('documenttype_list',$data);
	}
	
	public function add(){
		$data=array();
		$this->load->library(array('form_validation'));
		$rules = array(
			array(
				'field'=>'document_title',
				'label'=>'Title',
				'rules'=>'trim|required|callback_uniquename',
				'errors'=>array(
					'uniquename'=>'This %s value is already exists.'
				)
			),
			array(
				'field'=>'document_for',
				'label'=>'Document For',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array(
					'greater_than'=>'The %s field is required.'
				)
			),
			
		);
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'document_title'=>$this->input->post('document_title'),
				'document_for'=>$this->input->post('document_for'),
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			
			$documenttype_id = $this->BaseModel->insertData($this->tableNameDocumentType,$save_data);
			if($documenttype_id>0){
				$this->session->set_flashdata('success','Document Type details saved successfully');
				redirect(BASE_FOLDER.'documenttypes');
			}
			else{
				$this->session->set_flashdata('error','Document Type details not saved successfully');
			}
		}
		
		// value assing to view 
		$data['document_fors']=$this->getdocument_for();
		$this->loadview('documenttype_add',$data);
	}
	
	public function edit($documenttype_id=0){
		$data=array();
		// validate 
		if($documenttype_id>0){
			$find_doctype=array(
				'documenttype_id'=>$documenttype_id
			);
			$documenttype = $this->BaseModel->getData($this->tableNameDocumentType,$find_doctype);
			if(empty($documenttype)){
				$this->session->set_flashdata('error','Document Type details not found');
				redirect(BASE_FOLDER.'documenttypes');
			}
			$data['documenttype']=$documenttype;
		}
		else{
			$this->session->set_flashdata('error','Document Type info missing');
			redirect(BASE_FOLDER.'documenttypes');
		}
		// for section 
		$this->load->library(array('form_validation'));
		$rules = array(
			array(
				'field'=>'document_title',
				'label'=>'Title',
				'rules'=>'trim|required|callback_uniquename',
				'errors'=>array(
					'uniquename'=>'This %s value is already exists.'
				)
			),
			array(
				'field'=>'document_for',
				'label'=>'Document For',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array(
					'greater_than'=>'The %s field is required.'
				)
			),
			
		);
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'document_title'=>$this->input->post('document_title'),
				'document_for'=>$this->input->post('document_for'),
				'update_date'=>$this->dateformat,
			);
			
			$this->BaseModel->updateDatas($this->tableNameDocumentType,$save_data,$find_doctype);
			$this->session->set_flashdata('success','Document Type details updated successfully');
			redirect(BASE_FOLDER.'documenttypes');
		}
		
		// value assing to view 
		$data['document_fors']=$this->getdocument_for();
		$this->loadview('documenttype_edit',$data);
	}
	
	public function uniquename($name='', $id=0){
		if(!empty($name)){
			$find_cond=array(
				'UPPER(document_title)'=>strtoupper($name)
			);
			if($id>0){
				$find_cond['documenttype_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameDocumentType,$find_cond);
			if($tableRow>0){
				return false;
			}
		}
		return true;
	}
	
}
?>