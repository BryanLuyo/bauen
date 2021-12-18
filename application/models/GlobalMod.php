<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GlobalMod extends CI_Model{

  public function __construct(){
    parent::__construct();
    $this->load->database();
    $this->db->close();
    $this->db->initialize();
  }

  public function listOrder_conditional_limit($table='',$select=array('*'),$condicional=array(),$orden=array(),$records=0 , $init = 0){
     $this->db->select($select);
     $this->db->where($condicional);
     $this->db->order_by('1', 'desc'); 
     $this->db->limit($init, $records); 
     $query = $this->db->get($table);
     return $query->result();

    /*$query = $this->db->query("SELECT * FROM trns_requests limit 0,50");
		$return = $query->result();*/
    return $return;
   }

   public function Join($select = '*',$table1,$table2,$join,$conditional){
    $this->db->select($select);
    $this->db->from($table1);
    $this->db->join($table2, $join,'INNER');
    $this->db->where($conditional);
    $query = $this->db->get();   
    $response=$query->result();
    return $response;
   }

   public function JoinTEST($select = '*',$table1,$table2,$join,$conditional){
    $this->db->select($select);
    $this->db->from($table1);
    $this->db->join($table2, $join,'INNER');
    $this->db->where($conditional);
    $query = $this->db->get();
    echo '<pre>';
    print_r($this->db->last_query());
    echo '</pre>';
   
    $response=$query->result();
    return $response;
   }

  function count_filtered(){
    $this->db->reconnect();
		$this->_get_datatables_query();
		$query = $this->db->get();
		$response=$query->num_rows();
    //$this->db->close();
    return $response;
	}


  public function query($query=''){
    //$this->db->query('SET SQL_BIG_SELECTS=1');
    $this->db->reconnect();
		$query = $this->db->query($query);
		$return = $query->result();
    //$this->db->close();
    return $return;
	}



	public function count_all($table)	{
    $this->db->reconnect();
		$this->db->from($table);
		$return = $this->db->count_all_results();
    //$this->db->close();
    return $return;
	}


  public function get_list_all($select='',$table=''){
    $this->db->reconnect();
    $this->db->select($select);
    $query=$this->db->get($table);
    $return = $query->result();
    //$this->db->close();
    return $return;
  }


 



  public function get_list_Where($select='',$table='',$where_array=''){
    $this->db->reconnect();
    $this->db->query('SET SQL_BIG_SELECTS=1');
    $this->db->select($select);
    $this->db->where($where_array);
    $query=$this->db->get($table);
    $return= $query->result();
    //$this->db->close();
    return $return;

  }
  public function get_list_Whereorden($select='',$table='',$where_array='',$order=''){
    $this->db->reconnect();
    $this->db->select($select);
    $this->db->where($where_array);
    $this->db->order_by($order);
    $query=$this->db->get($table);
    $return= $query->result();
    return $return;
  }

 public function get_count_having($select='',$numberpos='',$having='',$table=''){
   $this->db->reconnect();
   $query = $this->db
              ->select($select)
              ->group_by($numberpos)
              ->having($having)
              ->get($table);
   $return= $query->result();
   //$this->db->close();
   return $return;
 }
  public function get_max_where($row,$where,$table){
    $this->db->reconnect();
	  $this->db->select_max($row);
    $this->db->where($where);
    $result = $this->db->get($table);
    $return= $result->result();
    //$this->db->close();
    return $return;

	}
  public function get_list_group($select,$group,$order,$having,$table){
    $this->db->reconnect();
     $this->db->select($select);
     $this->db->group_by($group);
     $this->db->order_by($order);
     $this->db->having($having);
     $query=$this->db->get($table);
     $return= $query->result();
     //$this->db->close();
     return $return;
  }

  public function queryInsert($query){
    $this->db->reconnect();
    $this->db->query($query);
    ////$this->db->close();
  }


  public function proc_insert($data,$table){
    $this->db->reconnect();
    $this->db->insert($table,$data);
    ////$this->db->close();
  }

  public function insertBacth($table='',$data=''){
    $this->db->insert_batch($table, $data);
  }

  public function updateBatch($table='',$data='',$where=''){
    $this->db->update_batch($table, $data, $where);
  }

  public function proc_insert_id($data,$table){
    $this->db->reconnect();
    $this->db->insert($table,$data);
    $return = $this->db->insert_id();
    ////$this->db->close();
    return $return;
 }

 public function proc_update($set,$table,$where){
  $this->db->reconnect();
  $this->db->update($table, $set, $where);
  return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
  ////$this->db->close();
	}


}
