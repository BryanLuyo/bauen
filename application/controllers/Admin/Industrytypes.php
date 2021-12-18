<?php
class Industrytypes extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$find_industry=array();
		$industrytypes = $this->BaseModel->getDatas($this->tableNameIndustryType,$find_industry);
		// set data 
		$data['industrytypes']=$industrytypes;
		$this->loadview('industrytype_list',$data);
	}
	
	public function add(){
		$data=array();
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'industrytype_name',
				'label'=>'Name',
				'rules'=>'trim|required|callback_uniquename',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.'
				)
			)
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'industrytype_name'=>$this->input->post('industrytype_name'),
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			$insdustrytype_id = $this->BaseModel->insertData($this->tableNameIndustryType,$save_data);
			if($insdustrytype_id>0){
				$this->session->set_flashdata('success','Industry Type saved successfully');
				redirect(BASE_FOLDER.'industrytypes');
			}
			else{
				$this->session->set_flashdata('error','Industry Type does not saved successfully');
			}
		}
		$this->loadview('industrytype_add',$data);
	}
	
	public function edit($industrytype_id=0){
		$data=array();
		if($industrytype_id>0){
			$find_cond=array(
				'industrytype_id'=>$industrytype_id
			);
			$industrytype = $this->BaseModel->getData($this->tableNameIndustryType,$find_cond);
			if(empty($industrytype)){
				$this->session->set_flashdata('error','Industry Type details not found');
				redirect(BASE_FOLDER.'industrytypes');
			}
			$data['industrytype']=$industrytype;
		}
		else{
			$this->session->set_flashdata('error','Industry Type info missing');
			redirect(BASE_FOLDER.'industrytypes');
		}
		//
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'industrytype_name',
				'label'=>'Name',
				'rules'=>'trim|required|callback_uniquename['.$industrytype_id.']',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.'
				)
			)
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'industrytype_name'=>$this->input->post('industrytype_name'),
				'update_date'=>$this->dateformat,
			);
			$this->BaseModel->updateDatas($this->tableNameIndustryType,$save_data,$find_cond);
			$this->session->set_flashdata('success','Industry Type updated successfully');
			redirect(BASE_FOLDER.'industrytypes');
		}
		$this->loadview('industrytype_edit',$data);
	}
	
	public function uniquename($name='', $id=0){
		if(!empty($name)){
			$find_cond=array(
				'UPPER(industrytype_name)'=>strtoupper($name)
			);
			if($id>0){
				$find_cond['industrytype_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameIndustryType,$find_cond);
			if(!empty($tableRow)){
				return false;
			}
		}
		return true;
	}
}
?>