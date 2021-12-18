<?php
class Loadtypes extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$find_cond=array(
		);
		$select_fields=array('loadtype_id','load_name','is_blocked');
		$loadtypes = $this->getloadtypes($find_cond,$select_fields);
		$data['loadtypes']=$loadtypes;
		$this->loadview('loadtype_list',$data);
	}
	
	public function add(){
		$data=array();
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'load_name',
				'label'=>'Load Name',
				'rules'=>'trim|required|callback_unieque_name',
				'errors'=>array(
					'unieque_name'=>'The %s is already exists.'
				)
			)
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'load_name'=>$this->input->post('load_name'),
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			$loadtype_id = $this->BaseModel->insertData($this->tableNameLoadType,$save_data);
			if($loadtype_id>0){
				$this->session->set_flashdata('success','Load Type saved successfully');
				redirect(BASE_FOLDER.'loadtypes');
			}
			else{
				$this->session->set_flashdata('error','Load Type saveing faild');
			}
		}
		
		$this->loadview('loadtype_add',$data);
	}
	public function edit($loadtype_id=0){
		$data=array();
		if($loadtype_id>0){
			$find_cond=array(
				'loadtype_id'=>$loadtype_id
			);
			$loadtype = $this->getloadtypes($find_cond);
			if(empty($loadtype)){
				$this->session->set_flashdata('error','Load Type details not found');
				redirect(BASE_FOLDER.'loadtypes');
			}
			$data['loadtype']=$loadtype[0];
		}
		else{
			$this->session->set_flashdata('error','Load Type missing');
			redirect(BASE_FOLDER.'loadtypes');
		}
		// form section 
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'load_name',
				'label'=>'Load Name',
				'rules'=>'trim|required|callback_unieque_name['.$loadtype_id.']',
				'errors'=>array(
					'unieque_name'=>'The %s is already exists.'
				)
			)
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'load_name'=>$this->input->post('load_name'),
				'update_date'=>$this->dateformat,
			);
			$this->BaseModel->updateDatas($this->tableNameLoadType,$save_data,$find_cond);
			$this->session->set_flashdata('success','Load Type updated successfully');
			redirect(BASE_FOLDER.'loadtypes');
		}
		$this->loadview('loadtype_edit',$data);
	}
	
	public function unieque_name($name='', $id=0){
		if(!empty($name)){
			$find_cond=array(
				'UPPER(load_name)'=>strtoupper($name)
			);
			if($id>0){
				$find_cond['loadtype_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameLoadType,$find_cond);
			if($tableRow){
				return false;
			}
		}
		return true;
	}
}
?>