<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- jvectormap -->
  <link rel="stylesheet" href="<?=base_url('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css')?>">
	<section class="content-header">
      <h1><small></small></h1>
      <ol class="breadcrumb">
        <li><a href="<?=base_url(BASE_FOLDER_TRANS.'dashboard')?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Messages</li>
      </ol>
    </section>
	
	<section class="content">
      <div class="row">
		<section class="col-lg-2"></section>
		<section class="col-lg-8 connectedSortable">
			<div class="box box-success">
				<div class="box-header">
				  <i class="fa fa-comments-o"></i>
				  <h3 class="box-title">Messages</h3>
				</div>
				<div class="box-body chat" id="chat-box">
				  <!-- chat item -->
				  <?php
					if(!empty($messages)){
						foreach($messages as $message){
							if(empty($message['image'])){
								$message['image']=base_url('assets/dist/img/avatar5.png');
							}
					?>
					<div class="item">
					<img src="<?=$message['image']?>" alt="user image" class="online">
					<p class="message">
					  <a href="javascript:void(0)" class="name">
						<small class="text-muted pull-right"><i class="fa fa-clock-o"></i> <?=date("G:i A",strtotime($message['create_date']))?></small>
						<?=ucwords($message['first_name'].' '.$message['last_name'])?>
					  </a>
					  <?php
						if($message['message_type']==0){
						echo $message['message_data'];
						}
					  ?>
					</p>
					<?php
					if($message['message_type']==1){
					?>
					<div class="attachment">
					  <h4>Attachments:</h4>
					  <p class="filename"></p>
					  <div class="col-sm-8">
						<img class="img-responsive" src="<?=$message['message_data']?>" alt="Photo">
					  </div>
					</div>
					<?php
					}
					?>
					<!-- /.attachment -->
				  </div>	
					<?php
						}
					}
				  ?>
				  <!-- /.item -->
				</div>
				<!-- /.chat -->
				<div class="box-footer">
				  <div class="input-group col-lg-12">
					<form method="post">
					<textarea class="form-control" name="message_data" id="message_data" placeholder="Type message..."></textarea>
					<button type="submit" class="btn btn-success mt-5 pull-right" id="messagesend"><i class="fa fa-send"></i>&nbsp;Send</button>
					</form>
				  </div>
				</div>
			</div>
		</section>
		<section class="col-lg-2"></section>
      </div>
      <!-- /.row -->
    </section>
	

<script>
	var chat_id = "<?=$chat_id?>";
	$(document).ready(function(){
		//Make the dashboard widgets sortable Using jquery UI
		$(".connectedSortable").sortable({
			placeholder: "sort-highlight",
			connectWith: ".connectedSortable",
			handle: ".box-header, .nav-tabs",
			forcePlaceholderSize: true,
			zIndex: 999999
		});
		$(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");
		//SLIMSCROLL FOR CHAT WIDGET
		$('#chat-box').slimScroll({
			height: '400px'
		});
		
		$("#messagesend").bind('click',sendmessage);
	});
	function sendmessage(e){
		e.preventDefault();
		var message_data=$("#message_data").val().trim();
		console.log(message_data.length);
		if(message_data.length>0){
			var post_data={'message_data':message_data,'is_ajax':1,'chat_id':chat_id,'message_type':0};
			var url = "<?=base_url(BASE_FOLDER_TRANS.'chats/addmessage')?>";
			$.ajax({
				url:url,
				type:'POST',
				dataType:'json',
				data:post_data,
				beforeSend:function(){
					$(".sitepreloader").show();
				},
				complete:function(){
					$(".sitepreloader").hide();
				},
				success:function(response){
					if(response.status==0){
						alert(response.message);
					}
					else{
						var message = response.chat;
						// success section
						var html = '<div class="item"><img src="'+message.image+'" alt="user image" class="online"><p class="message"><a href="javascript:void(0)" class="name"><small class="text-muted pull-right"><i class="fa fa-clock-o"></i> '+message.create_date+'</small>'+message.first_name+" "+message.last_name+'</a>'+message.message_data+'</p></div>';
						$("#chat-box").append(html);
						$("#message_data").val('');
					}
				},
				error:function(response){
					
				}
			});
		}
		else{
			alert("Please enter your message.");
		}
	}
	
</script>
<!-- Slimscroll -->
<script src="<?=base_url('assets/plugins/slimScroll/jquery.slimscroll.min.js')?>"></script>