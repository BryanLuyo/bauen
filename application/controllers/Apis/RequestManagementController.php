<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class RequestManagementController extends CI_Controller {

    function __construct() {
        parent::__construct();
        /*
        //include BASEPATH . 'services/RequestService.php';
        //$requests = $this->requestService->retrieveAll();        
        //$this->requestService = new RequestService();
        
        */
    }


    public function list() {
        echo json_encode( [ 'status' => true, 'message'=> 'Sergio pagame oe.', 'body' => "Gorrita" ] );;
    }

}