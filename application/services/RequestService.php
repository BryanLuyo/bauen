<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

include BASEPATH . 'models/Request.php';


class RequestService {
    function __construct() {
    }

    public function retrieveAll() {
        $request = new Request();
        return $request->all();
    }


}