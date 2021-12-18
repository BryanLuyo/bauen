<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class trailers extends CI_Controller {
    
    function __construct(){
        parent::__construct();
       $this->load->model('bauenservices/trailersModel');
    }
    public function list(){
        $language_id = $this->input->post('language_id');
        if(empty($language_id)){
             $language_id=1; 
         }
        $data = array();
        $data['body'] = array();
        $object =$this->trailersModel->getTrailers($language_id);
        if (count($object)>0) {
           $i=0;
            foreach ($object as $key => $v) {
              $v->image = base_url('uploads/trailers/'.$v->image);
              $object[$i]->image=$v->image;
              $i++;
            }
          $data['status'] = true;
          $data['body'] = $object;
        }else{
          $data['status'] = false;
          $data['message'] = "No se encontraron registros";
        }
       print (json_encode($data));
    }
}

