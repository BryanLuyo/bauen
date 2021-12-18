<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if(!function_exists('permissionchecked')){
	function permissionchecked($controller='',$action=''){
		if(!empty($controller) && !empty($action)){
			$CI = &get_instance();
			$controller=strtolower($controller);
			$action=strtolower($action);
			if($CI->session->has_userdata(SES_ADMIN_TYPE)){
				$cpt_admin_type = $CI->session->userdata(SES_ADMIN_TYPE);
			}
			elseif($CI->session->has_userdata(SES_TRANS_TYPE)){
				$cpt_admin_type = $CI->session->userdata(SES_TRANS_TYPE);
			}
			else{
				return false;
			}
			
			$permissionsdata = $CI->permissiondata();


			/*echo "<pre>";
			print_r($permissionsdata);*/
			if(isset($permissionsdata[$controller][$cpt_admin_type])){
				$permissions = $permissionsdata[$controller][$cpt_admin_type];
				if(in_array($action,$permissions)){
					return true;
				}
			}
		}
		return false;
	}
}
if(!function_exists('request_status')){
	function request_status($status){
		$CI = &get_instance();
		$statusarray = $CI->getrequeststatus();
		if(isset($statusarray[$status])){
			$status = $statusarray[$status];
		}
		else{
			$status='Placed';
		}
		return $status;
	}
}

if(!function_exists('user_status')){
	function user_status($status){
		$CI = &get_instance();
		$statusarray = $CI->getuserstatus();
		if(isset($statusarray[$status])){
			$status = $statusarray[$status];
		}
		else{
			$status='Available';
		}
		return $status;
	}
}

if(!function_exists('vehicle_status')){
	function vehicle_status($status){
		$CI = &get_instance();
		$statusarray = $CI->getvehiclestatus();
		if(isset($statusarray[$status])){
			$status = $statusarray[$status];
		}
		else{
			$status='Available';
		}
		return $status;
	}
}


if(!function_exists('phoneno_format')){
	function phoneno_format($phone_no='',$country_calling_code=''){
		if(!empty($phone_no)){
			$ln = strlen($phone_no);
			if($ln<10){
				$phone_no = str_pad($phone_no,10,"0",STR_PAD_LEFT);
				$ln=10;
			}
			$sub_phone = substr($phone_no,-10);
			$pre_phone = substr($phone_no,0,$ln-10);
			if(empty($pre_phone)){
				if(!empty($country_calling_code)){
					$pre_phone=$country_calling_code;
				}
			}
			if(!empty($pre_phone)){
				if(strpos($pre_phone,'+')===false){
					$pre_phone='+'.$pre_phone;
				}
			}
			
			//formating the phone number  +country_code xxx-xxx-xxxx
			$phone_format = substr($sub_phone,0,3).'-'.substr($sub_phone,3,3).'-'.substr($sub_phone,6);
			$phone_no = $pre_phone.' '.$phone_format;
		}
		return $phone_no;
	}
}

if(!function_exists('display_date_format')){
	function display_date_format($display_date=''){
		if(!empty($display_date)){
			$display_date = date("m/d/Y",strtotime($display_date));
		}
		return $display_date;
	}
}

?>
