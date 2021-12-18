<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vehicles extends MY_Controller{
	public $transporter_id;
	function __construct(){
		parent::__construct();
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);
	}
	public function index(){
		$data=array();
		$trailer_id=0;
		$vehicle_status=0;
		//get filter data
		if($this->input->post('trailer_id')){
			$trailer_id = $this->input->post('trailer_id');
		}
		if($this->input->post('vehicle_status')){
			$vehicle_status = $this->input->post('vehicle_status');
		}
		
		$find_vehicle=array(
			'user_id'=>$this->transporter_id
		);
		if($trailer_id>0){
			$find_vehicle['trailer_id']=$trailer_id;
		}
		if($vehicle_status>0){
			$find_vehicle['vehicle_status']=$vehicle_status;
		}
		
		$vehicles = $this->getvehicles($find_vehicle);
		$vehiclestatus = $this->getvehiclestatus();
		// assing data 
		$data['trailers']=$this->gettrailers();
		$data['vehicles']=$vehicles;
		$data['trailer_id']=$trailer_id;
		$data['vehicle_status']=$vehicle_status;
		$data['vehiclestatus']=$vehiclestatus;
		$this->loadviewtrans('vehicle_list',$data);
	}
	
	public function add(){
		$data=array();
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'trailer_id',
				'label'=>'Trailer',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array(
					'greater_than'=>'The %s field is required.'
				)
			),
			array(
				'field'=>'plate_no',
				'label'=>'Plate No.',
				'rules'=>'trim|required|callback_unique_plate_no',
				'errors'=>array(
					'unique_plate_no'=>'This %s is already exists.'
				)
			),
			array(
				'field'=>'purchase_year',
				'label'=>'Purchase Year',
				'rules'=>'trim|required|numeric',
				'errors'=>array()
			),
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'user_id'=>$this->transporter_id,
				'trailer_id'=>$this->input->post('trailer_id'),
				'plate_no'=>$this->input->post('plate_no'),
				'purchase_year'=>$this->input->post('purchase_year'),
				'create_date'=>$this->dateformat,
				'update_date'=>$this->dateformat,
			);
			$vehicle_id = $this->BaseModel->insertData($this->tableNameVehicle,$save_data);
			if($vehicle_id>0){
				$this->session->set_flashdata('success','Vehicle details saved successfully');
				redirect(BASE_FOLDER_TRANS.'vehicles');
			}
			else{
				$this->session->set_flashdata('error','Vehicle details saving faild');
			}
		}
		// assing data 
		$data['trailers']=$this->gettrailers();
		$this->loadviewtrans('vehicle_add',$data);
	}
	
	public function edit($vehicle_id=0){
		$data=array();
		if($vehicle_id>0){
			$find_vehicle=array(
				'vehicle_id'=>$vehicle_id,
				'user_id'=>$this->transporter_id
			);
			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle);
			if(empty($vehicle)){
				$this->session->set_flashdata('error','Vehicle details not found');
				redirect(BASE_FOLDER_TRANS.'vehicles');
			}
			$data['vehicle']=$vehicle;
		}
		else{
			$this->session->set_flashdata('error','Vehicle info missing');
			redirect(BASE_FOLDER_TRANS.'vehicles');
		}
		$this->load->library(array('form_validation'));
		$rules=array(
			array(
				'field'=>'trailer_id',
				'label'=>'Trailer',
				'rules'=>'trim|required|greater_than[0]',
				'errors'=>array(
					'greater_than'=>'The %s field is required.'
				)
			),
			array(
				'field'=>'plate_no',
				'label'=>'Plate No.',
				'rules'=>'trim|required|callback_unique_plate_no['.$vehicle_id.']',
				'errors'=>array(
					'unique_plate_no'=>'This %s is already exists.'
				)
			),
			array(
				'field'=>'purchase_year',
				'label'=>'Purchase Year',
				'rules'=>'trim|required|numeric',
				'errors'=>array()
			),
		);
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="has-error"><span class="help-block">','</span></div>');
		if($this->form_validation->run()===true){
			$save_data=array(
				'trailer_id'=>$this->input->post('trailer_id'),
				'plate_no'=>$this->input->post('plate_no'),
				'purchase_year'=>$this->input->post('purchase_year'),
				'update_date'=>$this->dateformat,
			);
			$this->BaseModel->updateDatas($this->tableNameVehicle,$save_data,$find_vehicle);
			$this->session->set_flashdata('success','Vehicle details updated successfully');
			redirect(BASE_FOLDER_TRANS.'vehicles');
		}
		// assing data 
		$data['trailers']=$this->gettrailers();
		$this->loadviewtrans('vehicle_edit',$data);
	}
	
	public function unique_plate_no($plate_no='', $id=0){
		if(!empty($plate_no)){
			$find_cond=array(
				'UPPER(plate_no)'=>strtoupper($plate_no)
			);
			if($id>0){
				$find_cond['vehicle_id !=']=$id;
			}
			$tablerow = $this->BaseModel->tableRow($this->tableNameVehicle,$find_cond);
			if($tablerow){
				return false;
			}
		}
		return true;
	}
	
	public function documents($vehicle_id=0){
		$data=array();
		if($vehicle_id>0){
			$find_vehicl=array(
				'vehicle_id'=>$vehicle_id,
				'user_id'=>$this->transporter_id
			);
			$vehicle = $this->getvehicles($find_vehicl);
			if(empty($vehicle)){
				$this->session->set_flashdata('error','Vehicle details not found');
				redirect(BASE_FOLDER_TRANS.'vehicles');
			}
			$vehicle = $vehicle[0];
			$data['vehicle']=$vehicle;
		}
		else{
			$this->session->set_flashdata('error','Vehicle info missing');
			redirect(BASE_FOLDER_TRANS.'vehicles');
		}
		// find the document 
		$documenttype_id=0;
		if($this->input->post('documenttype_id')){
			$documenttype_id = $this->input->post('documenttype_id');
		}
		$find_document=array(
			'user_id'=>$this->transporter_id,
			'vehicle_id'=>$vehicle_id
		);
		if($documenttype_id>0){
			$find_document['documenttype_id']=$documenttype_id;
		}
		$vehicledocuments = $this->getvehicledocuments($find_document);
		
		$data['vehicledocuments']=$vehicledocuments;
		//document types 
		$find_doc=array(
			'document_for'=>'2'
		);
		$data['documenttypes']=$this->getdocumenttypes($find_doc);
		$data['documenttype_id']=$documenttype_id;
		$this->loadviewtrans('vehicle_document_list',$data);
	}
	
	public function adddocument($vehicle_id=0){
		$data=array();
		if($vehicle_id>0){
			$find_vehicl=array(
				'vehicle_id'=>$vehicle_id,
				'user_id'=>$this->transporter_id
			);
			$vehicle = $this->getvehicles($find_vehicl);
			if(empty($vehicle)){
				$this->session->set_flashdata('error','Vehicle details not found');
				redirect(BASE_FOLDER_TRANS.'vehicles');
			}
			$vehicle = $vehicle[0];
			$data['vehicle']=$vehicle;
		}
		else{
			$this->session->set_flashdata('error','Vehicle info missing');
			redirect(BASE_FOLDER_TRANS.'vehicles');
		}
		//add section 
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
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$file_name = $this->uploadimage($_FILES['image'],'documents');
				if(!empty($file_name)){
					$save_data=array(
						'documenttype_id'=>$this->input->post('documenttype_id'),
						'file_name'=>$file_name,
						'vehicle_id'=>$vehicle_id,
						'user_id'=>$this->transporter_id,
						'document_status'=>'0',
						'create_date'=>$this->dateformat,
						'update_date'=>$this->dateformat,
					);
					$document_id = $this->BaseModel->insertData($this->tableNameVehicleDocument,$save_data);
					if($document_id>0){
						$this->session->set_flashdata('success','Document File uploaded successfully');
						redirect(BASE_FOLDER_TRANS.'vehicles/adddocument/'.$vehicle_id);
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
			'document_for'=>'2'
		);
		$data['documenttypes']=$this->getdocumenttypes($doc_cond);
		$this->loadviewtrans('vehicle_document_add',$data);
	}
	
	public function viewdocument($document_id=0){
		$data=array();
		if($document_id>0){
			$find_doct=array(
				'vehicle_document_id'=>$document_id
			);
			$document = $this->getvehicledocuments($find_doct);
			
			if(empty($document)){
				$this->session->set_flashdata('error','Document details not found');
				redirect(BASE_FOLDER_TRANS.'vehicles');
			}
			$data['document']=$document[0];
			// get vehicle details 
			$find_vehicle=array(
				'vehicle_id'=>$document[0]['vehicle_id']
			);
			$select_flds=array('vehicle_id','plate_no','purchase_year');
			$vehicle = $this->BaseModel->getData($this->tableNameVehicle,$find_vehicle,$select_flds);
			$data['vehicle']=$vehicle;
		}
		else{
			$this->session->set_flashdata('error','Document info missing');
			redirect(BASE_FOLDER_TRANS.'vehicles');
		}
		$this->loadviewtrans('document_view',$data);
	}
}
?>