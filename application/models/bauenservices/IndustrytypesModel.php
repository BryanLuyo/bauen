<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class industrytypesModel extends CI_Model
{
	function __construct(){
		$this->load->database();
	}
    public function getIndustrytypes(){
    	try {
    		  $query = $this->db->query("SELECT industrytype_id,industrytype_name from trns_industrytypes where is_blocked=0 and is_deleted=0");
       		 return $query->result();
    	} catch (Exception $e) {
    		return  $e->getMessage();
    	}
  
    }
}
