<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class loadtypesModel extends CI_Model
{
	function __construct(){
		$this->load->database();
	}
    public function getLoadtypes(){
    	try {
    		  $query = $this->db->query("SELECT  loadtype_id,load_name
										  from trns_loadtypes 
										  where is_blocked=0 and is_deleted=0");
       		 return $query->result();
    	} catch (Exception $e) {
    		return  $e->getMessage();
    	}
  
    }
}
