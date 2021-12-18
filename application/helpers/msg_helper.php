<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if(!function_exists('action_message')){
	function action_message($action_name='',$language=1){
		$language_messages=array(
			'trailer_name_req'=>array(
				'1'=>'language message',
				'2'=>''
			)
		);
		if(isset($language_messages[$action_name][$language])){
			return $language_messages[$action_name][$language];
		}
		else{
			return "";
		}
	}
}
?>