<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class industrytypes extends CI_Controller {
    
    function __construct(){
        parent::__construct();
       $this->load->model('bauenservices/industrytypesModel');
    }
    public function list(){
        $data = array();
        $data['body'] = array();
        $object =$this->industrytypesModel->getIndustrytypes();
        if (count($object)>0) {
          $data['status'] = true;
          $data['body'] = $object;
        }else{
          $data['status'] = false;
          $data['message'] = "No se encontraron registros";
        }
        print (json_encode($data));
    }


}

