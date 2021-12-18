<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Chats extends MY_Controller{
	public $transporter_id;
	function __construct(){
		parent::__construct();
		$this->trans_session_off();
		$this->transporter_id=$user_id = $this->session->userdata(SES_TRANS_ID);
	}
	public function index(){
		redirect(BASE_FOLDER_TRANS.'requests');
	}
	
	public function add($request_id=0){
		$data=array();
		if($request_id>0){
			$find_request=array(
				'request_id'=>$request_id
			);
			$request = $this->BaseModel->getData($this->tableNameRequest,$find_request);
			if(empty($request)){
				$this->session->set_flashdata('error','Request details not found');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			$user_id = $request['user_id'];
			$find_chat=array(
				'request_id'=>$request_id,
				'transporter_id'=>$this->transporter_id
			);
			$chat = $this->BaseModel->getData($this->tableNameChat,$find_chat);
			if(empty($chat)){
				$save_data=array(
					'request_id'=>$request_id,
					'user_id'=>$user_id,
					'transporter_id'=>$this->transporter_id,
					'create_date'=>$this->dateformat,
					'update_date'=>$this->dateformat,
				);
				$chat_id = $this->BaseModel->insertData($this->tableNameChat,$save_data);
				if(empty($chat_id)){
					$this->session->set_flashdata('error','Chat initiating faild.');
					redirect(BASE_FOLDER_TRANS.'requests');
				}
			}
			else{
				$chat_id = $chat['chat_id'];
			}
			redirect(BASE_FOLDER_TRANS.'chats/messages/'.$chat_id);
		}
		else{
			$this->session->set_flashdata('error','Request info missing');
			redirect(BASE_FOLDER_TRANS.'requests');
		}
	}
	
	public function messages($chat_id=0){
		$data=array();
		if($chat_id>0){
			$find_chat=array(
				'chat_id'=>$chat_id,
				'transporter_id'=>$this->transporter_id
			);
			$chat = $this->BaseModel->getData($this->tableNameChat,$find_chat);
			if(empty($chat)){
				$this->session->set_flashdata('error','Chat details not found.');
				redirect(BASE_FOLDER_TRANS.'requests');
			}
			$find_message=array(
				'chat_id'=>$chat_id
			);
			$extra = array(
				'user_id'=>$this->transporter_id
			);
			$messages = $this->getmessages($find_message,$extra);
			
			$data['messages']=$messages;
			$data['chat_id']=$chat_id;
			$this->loadviewtrans('chat_message',$data);
		}
		else{
			$this->session->set_flashdata('error','Chat info missing');
			redirect(BASE_FOLDER_TRANS.'requests');
		}
	}
	
	public function addmessage(){
		$status=0;
		$message='post not happend';
		$chat=array();
		if($this->input->server('REQUEST_METHOD')==strtoupper('post')){
			$chat_id = $this->input->post('chat_id');
			$message_data = trim($this->input->post('message_data'));
			$message_type = $this->input->post('message_type');
			if($chat_id>0){
				if(!empty($message_data)){
					$find_chat=array(
						'chat_id'=>$chat_id,
						'transporter_id'=>$this->transporter_id
					);
					$chat = $this->BaseModel->getData($this->tableNameChat,$find_chat);
					if(!empty($chat)){
						$save_data=array(
							'chat_id'=>$chat_id,
							'user_id'=>$this->transporter_id,
							'message_type'=>'0',
							'message_data'=>$message_data,
							'create_date'=>$this->dateformat,
							'update_date'=>$this->dateformat,
						);
						$message_id = $this->BaseModel->insertData($this->tableNameMessage,$save_data);
						if($message_id>0){
							$find_message=array(
								'message_id'=>$message_id,
							);
							$extra = array(
								'user_id'=>$this->transporter_id
							);
							$chat = $this->getmessages($find_message,$extra);
							if(!empty($chat)){
								$chat=$chat[0];
								$chat['create_date']=date("G:i A",strtotime($chat['create_date']));
								$chat['first_name']=ucwords($chat['first_name']);
								$chat['last_name']=ucwords($chat['last_name']);
								if(empty($chat['image'])){
									$chat['image']=base_url('assets/dist/img/avatar5.png');
								}
							}
							$status=1;
							$message="Message send";
						}
						else{
							$message="Message sending faild";
						}
					}
					else{
						$message="Invalid Chat window";
					}
				}
				else{
					$message="Message cannot blank";
				}
			}
			else{
				$message="Chat info missing";
			}
		}
		die(json_encode(array('status'=>$status,'message'=>$message,'chat'=>$chat)));
	}
}
?>