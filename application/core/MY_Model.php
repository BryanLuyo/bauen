<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Model extends CI_Model{
	private $tblSelectFields='';
	private $tblConditions='';
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->tblSelectFields='';
		$this->tblConditions=array();
	}
	
	private function table_full_name($table_name=''){
		return $this->db->dbprefix($table_name);
	}
	
	public function getData($tableName='',$tableConditions=array(),$selectFields=array(),$orderBy=array(),$joinDetails=array(),$complexCondition=array()){
		$this->tblSelectFields='';
		$this->tblConditions=array();
		$tableName = $this->table_full_name($tableName); //get table actuale name
		// table select fields
		$this->tblSelectFields=$tableName.'.*';
		if(!empty($selectFields)){
			if(is_array($selectFields)){
				$this->tblSelectFields=$tableName.'.'.implode(', '.$tableName.'.',$selectFields);
			}
			else{
				$this->tblSelectFields=$selectFields;
			}
			
		}
		//echo $this->tblSelectFields;
		// condition build section
		if(!empty($tableConditions)){
			foreach($tableConditions as $key=>$val){
				if($key=='like'){
					if(is_array($val) && count($val)>0){
						foreach($val as $k=>$v){
							$this->db->like($tableName.'.'.$k,$v, 'both');
						}
					}
				}
				else{
					if(is_array($val) && count($val)>0){
						$this->db->where_in($tableName.'.'.$key,$val);
					}
					else{
						$this->tblConditions[$tableName.'.'.$key]=$val;

					}
				}
			}
			
		}

		//$this->tblConditions[$tableName.'.is_deleted']='0';
		if(!isset($tableConditions['is_deleted'])){
			$this->tblConditions[$tableName.'.is_deleted']='0';			
		}
		// order by
		if(!empty($orderBy) && is_array($orderBy)){
			foreach($orderBy as $field=>$by){
				$this->db->order_by($tableName.'.'.$field,$by);
			}			
		}
		
		// join section  tblConditions
		if(!empty($joinDetails) && is_array($joinDetails)){
			foreach($joinDetails as $joinDetail){
				$this->joinconstruct($joinDetail);
			}			
		}
		
		//marge all the condition with complex condition 
		if(!empty($complexCondition)){
			$this->tblConditions=array_merge($this->tblConditions,$complexCondition);			
		}
		
		// now build the query here 
		//return json_encode($this->tblConditions);
 
		$tbl_row = $this->db->select($this->tblSelectFields)
			->get_where($tableName,$this->tblConditions)
			->row_array();
		
			$this->debugmodel($this->db->last_query());
			
			// HLM 
//echo "<pre>";
//print_r($this->db->last_query());
//echo "</pre>";
//exit();
		
		return $tbl_row;
	}



	public function getDatas($tableName='',$tableConditions=array(),$selectFields=array(),$orderBy=array(),$joinDetails=array(),$offset=0,$limit=0,$complexCondition=array(),$group_by=array(),$is_count=false){
		$this->tblSelectFields='';
		$this->tblConditions=array();
		$tableName = $this->table_full_name($tableName); //get table actuale name
		// table select fields
		$this->tblSelectFields=$tableName.'.*';
		if(!empty($selectFields)){
			if(is_array($selectFields)){
				// for special section
				$special_select_field='';
				if(isset($selectFields['special_select_field'])){
					$special_select_field = $selectFields['special_select_field'];
					unset($selectFields['special_select_field']);
				}
				
				if(!empty($selectFields)){
					$this->tblSelectFields=$tableName.'.'.implode(', '.$tableName.'.',$selectFields);
				}
				
				if(!empty($special_select_field)){
					$this->tblSelectFields.=", ".$special_select_field;
				}
			}
			else{
				$this->tblSelectFields=$selectFields;
			}
		}
		//echo $this->tblSelectFields;
		// condition build section
		if(!empty($tableConditions)){
			foreach($tableConditions as $key=>$val){
				if($key=='like'){
					if(is_array($val) && count($val)>0){
						foreach($val as $k=>$v){
							$this->db->like($tableName.'.'.$k,$v, 'both');
						}
					}
				}
				else{
					if(is_array($val) && count($val)>0){
						$this->db->where_in($tableName.'.'.$key,$val);
					}
					else{
						$this->tblConditions[$tableName.'.'.$key]=$val;
					}
				}
			}
		}
		//$this->tblConditions[$tableName.'.is_deleted']='0';
		if(!isset($tableConditions['is_deleted'])){
			$this->tblConditions[$tableName.'.is_deleted']='0';
		}
		// order by
		if(!empty($orderBy) && is_array($orderBy)){
			foreach($orderBy as $field=>$by){
				$this->db->order_by($tableName.'.'.$field,$by);
			}
		}
		
		// join section  tblConditions
		if(!empty($joinDetails) && is_array($joinDetails)){
			foreach($joinDetails as $joinDetail){
				$this->joinconstruct($joinDetail);
			}
		}
		
		//marge all the condition with complex condition 
		if(!empty($complexCondition)){
			if(is_array($complexCondition)){
				$this->tblConditions=array_merge($this->tblConditions,$complexCondition);
			}
			else{
				$this->db->where($complexCondition);
			}
		}
		
		//limit section 
		if($limit>0){
			$offset=($offset>0)?$offset:0;
			$this->db->limit($limit,$offset);
		}
		//group by section 
		if(!empty($group_by)){
			if(is_array($group_by)){
				foreach($group_by as $groupby){
					$this->db->group_by($groupby);
				}
			}
			else{
				$this->db->group_by($group_by);
			}
		}
		// now build the query here 
		if($is_count){
			$tbl_rows = $this->db->select($this->tblSelectFields,true)
			->from($tableName)
			->where($this->tblConditions)
			->count_all_results();
		}
		else{
			$tbl_rows = $this->db->select($this->tblSelectFields)
			->get_where($tableName,$this->tblConditions)
			->result_array();
		}
		 //#ED
		 //echo '<pre>';
		//	print_r($this->db->last_query());
		// echo '</pre>';
		//$this->debugmodel($this->db->last_query());
		$this->debugmodel($this->db->last_query());
		return $tbl_rows;
	}
	
	public function debugmodel($sql){
		$log   = "____________________________________________________________________________________________________";
		$log  .= "\n";
		$log.= $sql.PHP_EOL;
		file_put_contents('./logs/debugModel_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
		return true;
	}

	public function insertData($tableName='',$savedata=array()){
		if(empty($tableName)) {
			return false;
		}
		$full_table_name = $this->table_full_name($tableName);
		$this->db->insert($full_table_name,$savedata);
		$table_id = $this->db->insert_id();
		//#ED
			/*echo '<pre>';
			print_r($this->db->last_query());
			echo '</pre>';*/
		return $table_id;
	}


	public function updateDatas($tableName='',$set_data=array(),$condition=array()) {
		if(empty($tableName)) {
			return false;
		}
		$full_table_name = $this->table_full_name($tableName);
		if(!empty($set_data)) {
			$this->db->update($full_table_name,$set_data,$condition,false);
			//#ED
			/*echo '<pre>';
			print_r($this->db->last_query());
			echo '</pre>';*/
			return true;
		}
		return false;
	}

	
	public function removeDatas($tableName='',$condition=array()) {
		if(empty($tableName)) {
			return false;
		}
		if(!empty($condition) && is_array($condition)) {
			$set_data=array(
				'is_deleted'=>'1',
				'update_date'=>date('Y-m-d H:i:s'),
				'deleted_date'=>date('Y-m-d H:i:s')
			);
			$condition['is_deleted']='0';
			$status = $this->updateDatas($tableName,$set_data,$condition);
			return $status;
		}
		return false;
	}
	

	public function insertDatas($tableName='',$savedatas=array()) {
		if(empty($tableName)) {
			return false;
		}
		$full_table_name = $this->table_full_name($tableName);
		$this->db->insert_batch($full_table_name,$savedatas);
		$table_id = true;
		return $table_id;
	}


	public function tableRow($tableName='',$conditions=array()) {
		$tableName = $this->table_full_name($tableName); //get table actuale name
		if(!empty($tableName)){
			$conditions['is_deleted']='0';
			$count = $this->db->from($tableName)
				->where($conditions)
				->count_all_results();
			return $count;
		}
		return 0;
	}
	

	private function joinconstruct($joindata=array()) {
		/*array(
			'table_name'=>'',
			'table_name_alias'=>'',
			'join_type'=>'',// left,inner,outer,right
			'join_with'=>'',
			'join_on'=>array(
				'join_with_field'=>'join_field'
			),
			'oncond'=>array(
				'field'=>'value'
			),
			'conditions'=>array(
				'field'=>'value'
			),
			'select_fields'=>array()
		);*/
		
		if(!empty($joindata) && is_array($joindata)){
			
			if(isset($joindata['table_name']) && !empty($joindata['table_name'])){
				// if custome 
				$is_custom = (isset($joindata['is_custom']) && !empty($joindata['is_custom']))?$joindata['is_custom']:'0';
				if($is_custom){
					$full_table_name = $joindata['table_name'];
				}
				else{
					$full_table_name = $this->table_full_name($joindata['table_name']);
				}
				
				$table_name_alias = (isset($joindata['table_name_alias']) && !empty($joindata['table_name_alias']))?$joindata['table_name_alias']:'';
				
				$table_name = (empty($table_name_alias))?$full_table_name:$table_name_alias;
				
				if(isset($joindata['join_with']) && !empty($joindata['join_with'])){
					$join_with = $this->table_full_name($joindata['join_with']);
					if(isset($joindata['join_with_alias']) && !empty($joindata['join_with_alias'])){
						$join_with = $joindata['join_with_alias'];
					}
					if(isset($joindata['join_on']) && !empty($joindata['join_on'])){
						$join_ons = $joindata['join_on'];
						$on_data='';
						if(is_array($join_ons)){
							foreach($join_ons as $join_with_fld=>$join_to_fld){
								if(empty($on_data)){
									$on_data=$join_with.'.'.$join_with_fld.'='.$table_name.'.'.$join_to_fld;
								}
								else{
									$on_data.=' AND '.$join_with.'.'.$join_with_fld.'='.$table_name.'.'.$join_to_fld;
								}
							}
						}
						else{
							$on_data=$join_ons;
						}
						
						if(isset($joindata['oncond']) && !empty($joindata['oncond'])){
							$oncond = $joindata['oncond'];
							if(is_array($oncond)){
								foreach($oncond as $key=>$val){
									if(is_array($val) && count($val)>0){
										$val = implode(",",$val);
										$on_data.=' AND '.$table_name.'.'.$key.' IN('.$val.')';
									}
									else{
										// is any operator assing with 
										$keys = explode(" ",$key);
										$key = (isset($keys[0]) && !empty($keys[0]))?$keys[0]:$key;
										$key_e = (isset($keys[1]) && !empty($keys[1]))?$keys[1]:"=";
										// 
										$key = $key.' '.$key_e;
										$on_data.=' AND '.$table_name.'.'.$key.'"'.$val.'"';
									}
								}
							}
							else{
								$on_data.=" AND ".$oncond;
							}
						}
						$join_type='inner';
						if(isset($joindata['join_type']) && !empty($joindata['join_type'])){
							$join_type = $joindata['join_type'];
						}
						// conditions 
						if(isset($joindata['conditions']) && !empty($joindata['conditions'])){
							$condtions = $joindata['conditions'];
							foreach($condtions as $key=>$val){
								if($key=='like'){
									if(is_array($val) && count($val)>0){
										foreach($val as $k=>$v){
											$this->db->like($table_name.'.'.$k,$v, 'both');
										}
									}
								}
								else{
									if(is_array($val) && count($val)>0){
										$this->db->where_in($table_name.'.'.$key,$val);
									}
									else{
										$this->tblConditions[$table_name.'.'.$key]=$val;
									}
								}
							}
						}
						// select fileds
						if(isset($joindata['select_fields']) && !empty($joindata['select_fields'])){
							$select_fileds = $joindata['select_fields'];
							if($join_type=='left'){
								$strs='';
								if(is_array($select_fileds)){
									foreach($select_fileds as $select_filed){
										// alias present 
										$fld_name='';
										$alias_name='';
										$select_filed = explode(' ',$select_filed);
										if(count($select_filed)==2){
											$fld_name = $select_filed[0];
											$alias_name = $select_filed[1];
										}
										else{
											$alias_name = $fld_name = $select_filed[0];
										}
										//
										if(empty($strs)){
											$strs='IFNULL('.$table_name.'.'.$fld_name.',"") '.$alias_name;
										}
										else{
											$strs.=', IFNULL('.$table_name.'.'.$fld_name.',"") '.$alias_name;
										}
										
										/*
										if(empty($strs)){
											$strs='IFNULL('.$table_name.'.'.$select_filed.',"") '.$select_filed;
										}
										else{
											$strs.=', IFNULL('.$table_name.'.'.$select_filed.',"") '.$select_filed;
										}
										*/
									}
									
									$select_fileds = $strs;
								}
							}
							else{
								if(is_array($select_fileds)){
									$select_fileds = $table_name.'.'.implode(', '.$table_name.'.',$select_fileds);
								     
									if(isset($joindata['select_fields_json']) && !empty($joindata['select_fields_json'])) {
									    $select_fields_json = $joindata['select_fields_json']['fields'];	
										$select_fields_json_alias = $joindata['select_fields_json']['alias'];
										$sfj = '';
										foreach($select_fields_json as $key => $select_filed_json){
											if($sfj != '') {
												if(count($select_fields_json) == $key + 1 ){
													$sfj.= ", '  \"".$select_filed_json."\": \"' , CONCAT('',".$table_name.".".$select_filed_json.",'\"' )";
												} else {
													$sfj.= ", '  \"".$select_filed_json."\": \"' , CONCAT('',".$table_name.".".$select_filed_json.",'\" , ' )";
												}
											} else {
												$sfj.= "'\"".$select_filed_json."\": \"' , CONCAT('',".$table_name.".".$select_filed_json.",'\" , ' )";
											}
										}	
										$select_fileds.= ', CONCAT("[{",GROUP_CONCAT('.$sfj.'),"}]") AS '.$select_fields_json_alias;
									} 
									
								}
							}
							// marge the fileds 
							$this->tblSelectFields.=', '.$select_fileds;
						}
						$table_name = $full_table_name;
						if(!empty($table_name_alias)){
							$table_name.=' as '.$table_name_alias;
						}
						$this->db->join($table_name,$on_data,$join_type);
					}
				}
			}
		}
	}
	
	
	public function customSelect($sql=''){
		$response=array();
		if(!empty($sql)){
			$response = $this->db->query($sql)->result_array();
		}
		return $response;
	}
	

	public function queryResult($query=''){
		$query = $this->db->query($query);
		$return = $query->result();
		return $return;
	}


	public function customQuery($sql=''){
		if(!empty($sql)){
			$this->db->query($sql);
		}
	}
	
	public function lastquery(){
		$query = $this->db->last_query();
		return $query;
	}


       public function get_map_by_lng_lat($data) {
        //echo '<pre/>'; print_r($data); exit;
        $api_key="AIzaSyCAeL_PSmeFP-hNntq8_v6d5wLf6hBdlhY";
        $map_url ="https://maps.googleapis.com/maps/api/staticmap?key=".$api_key."&size=1500x350&format=JPEG";
        $color = "green";
        $start_point=array();
        $end_point=array();
        $markers = '';
        foreach ($data['markers'] as $marker) {
            $place = $marker['place'];
            $lat = $marker['lat'];
            $long = $marker['long'];


            $markers .= "&markers=color:$color%7Clabel:$place%7C$lat,$long";
            if (!empty($color)) {
                $color = "yellow";
            }
            if (empty($start_point)) {
                $start_point[] = array(
                    'lat' => $lat,
                    'long' => $long
                );
            } else {
                if (empty($end_point)) {
                    $end_point[] = array(
                        'lat' => $lat,
                        'long' => $long
                    );
                }
            }
        }

        $path_points = array_merge($start_point,$end_point);
        $patha="&path=color:red|weight:2";
        foreach($path_points as $path_point){
            $patha.="|".$path_point['lat'].",".$path_point['long'];
        }

        $map_url.=$markers;//.$patha;
        $map_url;
        $request_id = rand(1, 4000);
        $image_name = time()."request_".$request_id.".jpg";
        copy($map_url,'uploads/requests/'.$image_name);
        return $image_name;
    }


	
}
?>