<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class documenttypesModel extends CI_Model
{
	function __construct(){
		$this->load->database();
	}
    public function getDocumenttypes($document_for){
    	try {
    		  $query = $this->db->query("SELECT  documenttype_id,document_title
                                         from trns_documenttypes where is_blocked=0 and is_deleted=0 and document_for='".$document_for."'");
       		 return $query->result();
    	} catch (Exception $e) {
    		return  $e->getMessage();
    	}
  
    }
}
