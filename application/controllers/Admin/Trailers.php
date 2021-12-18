<?php

class Trailers extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->admin_session_off();
	}
	
	public function index(){
		$data=array();
		$find_trailer = array();
		$selected_fields=array();
		$group_by=array('trailer_id');
		$joins=array(
			array(
				'table_name'=>$this->tableNameLanguageTrailer,
				'join_type'=>'left',
				'join_with'=>$this->tableNameTrailer,
				'join_on'=>array('trailer_id'=>'trailer_id'),
				'select_fields'=>'group_concat(trailer_name order by language_id asc SEPARATOR  "<br/>") trailer_name'
			)
		);
		$trailers = $this->BaseModel->getDatas($this->tableNameTrailer,$find_trailer,$selected_fields,array(),$joins,$offset=0,$limit=0,$complexCondition=array(),$group_by);
		// assign to the view
		$data['trailers']=$trailers;
		$this->loadview('trailer_list',$data);
	}
	
	public function add(){
		$data=array();
		$this->load->library(array('form_validation'));
		$rules=array(
			/*array(
				'field'=>'name',
				'label'=>'Name',
				'rules'=>'trim|required|callback_uniquename',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.',
					//'required'=>action_message('trailer_name_req',APP_LANG)
				)
			),*/
			array(
				'field'=>'en_name',
				'label'=>'Name(English)',
				'rules'=>'trim|required|callback_uniquename',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.',
				)
			),
			array(
				'field'=>'es_name',
				'label'=>'Name(Spanish)',
				'rules'=>'trim|required|callback_uniquename',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.',
				)
			),
			array(
				'field'=>'min_load',
				'label'=>'Minimum Load',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array()
			),
			array(
				'field'=>'max_load',
				'label'=>'Maximum Load',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array()
			),
		);
		// file required 
		if(!isset($_FILES['image']['name']) || empty($_FILES['image']['name'])){
			$rules[]=array(
				'field'=>'imagereq',
				'label'=>'Image',
				'rules'=>'required',
				'errors'=>array()
			);
		}
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			//upload the image 
			$filename = $this->uploadimage($_FILES['image'],'trailers');
			$en_name = $this->input->post('en_name');
			$es_name = $this->input->post('es_name');
			$create_date = $this->dateformat;
			$name = $en_name;
			if($this->language_id=='2'){
				$name = $es_name;
			}
			
			$save_data=array(
				'name'=>$name,
				'min_load'=>$this->input->post('min_load'),
				'max_load'=>$this->input->post('max_load'),
				'image'=>$filename,
				'create_date'=>$create_date,
				'update_date'=>$create_date
			);
			$trailer_id = $this->BaseModel->insertData($this->tableNameTrailer,$save_data);
			if($trailer_id>0){
				// now add the language section 
				$save_datas=array(
					array(
						'trailer_id'=>$trailer_id,
						'trailer_name'=>$en_name,
						'language_id'=>'1',
						'create_date'=>$create_date,
						'update_date'=>$create_date
					),
					array(
						'trailer_id'=>$trailer_id,
						'trailer_name'=>$es_name,
						'language_id'=>'2',
						'create_date'=>$create_date,
						'update_date'=>$create_date
					)
				);
				$this->BaseModel->insertDatas($this->tableNameLanguageTrailer,$save_datas);
				$this->session->set_flashdata('success','Trailer details saved successfully');
				redirect(BASE_FOLDER.'trailers');
			}
			else{
				$this->session->set_flashdata('error','Trailer details not saved successfully');
			}
		}
		$this->loadview('trailer_add',$data);
	}
	
	public function edit($trailer_id=0){
		$data=array();
		if($trailer_id>0){
			$find_trailer=array(
				'trailer_id'=>$trailer_id
			);
			$trailer = $this->BaseModel->getData($this->tableNameTrailer,$find_trailer);
			if(empty($trailer)){
				$this->session->set_flashdata('error','Trailer details not found');
				redirect(BASE_FOLDER.'trailers');
			}
			$data['trailer']=$trailer;
			// get english language section 
			$trailer_flds=array(
				'language_trailer_id','trailer_name'
			);
			$jons=array(
				array(
					'table_name'=>$this->tableNameLanguageTrailer,
					'join_with'=>$this->tableNameLanguage,
					'join_type'=>'left',
					'join_on'=>array('language_id'=>'language_id'),
					'oncond'=>$find_trailer,
					'select_fields'=>$trailer_flds
				)
			);
			$trailer_names = $this->BaseModel->getDatas($this->tableNameLanguage,array(),array('language_id','language_name','sort_name'),array(),$jons);
			$data['trailer_names']=$trailer_names;
		}
		else{
			$this->session->set_flashdata('error','Trailer info missing');
			redirect(BASE_FOLDER.'trailers');
		}
		
		//form section 
		$this->load->library(array('form_validation'));
		$rules=array(
			/*array(
				'field'=>'name',
				'label'=>'Name',
				'rules'=>'trim|required|callback_uniquename['.$trailer_id.']',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.',
					//'required'=>action_message('trailer_name_req',APP_LANG)
				)
			),*/
			array(
				'field'=>'en_name',
				'label'=>'Name(English)',
				'rules'=>'trim|required|callback_uniquename['.$trailer_id.']',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.',
				)
			),
			array(
				'field'=>'es_name',
				'label'=>'Name(Spanish)',
				'rules'=>'trim|required|callback_uniquename['.$trailer_id.']',
				'errors'=>array(
					'uniquename'=>'This %s is already exists.',
				)
			),
			array(
				'field'=>'min_load',
				'label'=>'Minimum Load',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array()
			),
			array(
				'field'=>'max_load',
				'label'=>'Maximum Load',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array()
			),
		);
		// file required 
		if( empty($trailer['image']) && (!isset($_FILES['image']['name']) || empty($_FILES['image']['name']))){
			$rules[]=array(
				'field'=>'imagereq',
				'label'=>'Image',
				'rules'=>'required',
				'errors'=>array()
			);
		}
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$en_name = $this->input->post('en_name');
			$es_name = $this->input->post('es_name');
			$en_language_trailer_id = $this->input->post('en_language_trailer_id');
			$es_language_trailer_id = $this->input->post('es_language_trailer_id');
			$create_date = $this->dateformat;
			$name = $en_name;
			if($this->language_id=='2'){
				$name = $es_name;
			}
			
			$save_data=array(
				'name'=>$name,
				'min_load'=>$this->input->post('min_load'),
				'max_load'=>$this->input->post('max_load'),
				'update_date'=>$create_date
			);
			//upload the image
			if(!empty($_FILES['image']['name'])){
				$filename = $this->uploadimage($_FILES['image'],'trailers');
				if(!empty($filename)){
					$save_data['image']=$filename;
					// remove old image 
					if(!empty($trailer['image'])){
						$this->removeimage($trailer['image'],'trailers');
					}
				}
			}
			$this->BaseModel->updateDatas($this->tableNameTrailer,$save_data,$find_trailer);
			// update the language file
			if($en_language_trailer_id>0){
				$up_cond=array(
					'trailer_id'=>$trailer_id,
					'language_trailer_id'=>$en_language_trailer_id
				);
				$up_data=array(
					'trailer_name'=>$en_name,
					'update_date'=>$create_date
				);
				$this->BaseModel->updateDatas($this->tableNameLanguageTrailer,$up_data,$up_cond);
			}
			else{
				$ln_save_data=array(
					'trailer_id'=>$trailer_id,
					'language_id'=>'1',
					'trailer_name'=>$en_name,
					'create_date'=>$create_date,
					'update_date'=>$create_date
				);
				$this->BaseModel->insertData($this->tableNameLanguageTrailer,$ln_save_data);
			}
			
			if($es_language_trailer_id>0){
				$up_cond=array(
					'trailer_id'=>$trailer_id,
					'language_trailer_id'=>$es_language_trailer_id
				);
				$up_data=array(
					'trailer_name'=>$es_name,
					'update_date'=>$create_date
				);
				$this->BaseModel->updateDatas($this->tableNameLanguageTrailer,$up_data,$up_cond);
			}
			else{
				$ln_save_data=array(
					'trailer_id'=>$trailer_id,
					'trailer_name'=>$es_name,
					'language_id'=>'2',
					'create_date'=>$create_date,
					'update_date'=>$create_date
				);
				$this->BaseModel->insertData($this->tableNameLanguageTrailer,$ln_save_data);
			}
			$this->session->set_flashdata('success','Trailer details updated successfully');
			redirect(BASE_FOLDER.'trailers');
		}
		$this->loadview('trailer_edit',$data);
	}
	
	/*public function uniquename($name='', $id=0){
		if(!empty($name)){
			$find_cond=array(
				'UPPER(name)'=>strtoupper($name)
			);
			if($id>0){
				$find_cond['trailer_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameTrailer,$find_cond);
			if(!empty($tableRow)){
				return false;
			}
		}
		return true;
	}*/
	public function uniquename($name='', $id=0){
		if(!empty($name)){
			$find_cond=array(
				'UPPER(trailer_name)'=>strtoupper($name)
			);
			if($id>0){
				$find_cond['trailer_id !=']=$id;
			}
			$tableRow = $this->BaseModel->tableRow($this->tableNameLanguageTrailer,$find_cond);
			if(!empty($tableRow)){
				return false;
			}
		}
		return true;
	}
	
}
?>