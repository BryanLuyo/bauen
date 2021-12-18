<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class trailersModel extends CI_Model
{    
	function __construct(){
	parent::__construct();
        $this->load->database();
	}
    public function getTrailers($language_id){
    	try {
        $query = $this->db->query("SELECT  t.trailer_id,t.name,t.image,t.min_load,t.max_load 
									from trns_trailers t
									inner join trns_language_trailers lt
									on t.trailer_id=lt.trailer_id
									where t.is_blocked=0 and t.is_deleted=0 and lt.language_id='".$language_id."'");
        return $query->result();
    	} catch (Exception $e) {
    		return  $e->getMessage();
    	}
 
    }
}
