<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class documenttypes extends CI_Controller {
    
    function __construct(){
        parent::__construct();
       $this->load->model('bauenservices/documenttypesModel');
    }
    public function list(){
        $document_for = $this->input->post('document_for');

        if(empty($document_for)){
              $data['status'] = false;
              $data['message'] = "No se envio el parametro document_for";
         }else{
            $data = array();
            $data['body'] = array();
            $object =$this->documenttypesModel->getDocumenttypes($document_for);
            if (count($object)>0) {
              $data['status'] = true;
              $data['body'] = $object;
            }else{
              $data['status'] = false;
              $data['message'] = "No se encontraron registros";
            }
         }
        print (json_encode($data));
    }


}

