@extends('../master_layout/web_shipper')

@section('custom_js')
<script>
    
var session_id = {{Session::get('web_user_id')}} ;
var pull_class = "";


function format_date(created_at){
            var options = {  year: 'numeric', month: 'short', day: 'numeric'};
                    var today = new Date(created_at);
                    var formated_date = today.toLocaleDateString("en-US", options);
                    return formated_date;
            }
 function get_recipient_id(chat_id){  
    //alert(chat_id);
     $('#chat_id_wize_details').html("");
    $("#chat_id_for_send_msg").val('');
    $("#chat_id_for_send_msg").val(chat_id);
  
     $('.chat_sidebar').css("display","block");
		//console.log(chat_id);
            $.ajax({
            type: 'POST',
                    url:'{{url('api/user-chat-details')}}',
                    data:{
                    chat_id:chat_id                          
                    },
                    success: function(data){
                        console.log(data);
                        var append_data = "";
                          //  append_data += ' ';
                            if (jQuery.isEmptyObject(data) == false)
                                {
                                    for(var i=0;i<data.details.length;i++){
                                        if(data.details[i]['user_id'] == session_id ){                                           
                                            var css_clas_1 = 'pull-right';
                                            var css_clas_2 = 'pull-left';
                                            var admin_chat = 'admin_chat';
                                            var message_data = 'style="text-align: right ; background: #ecfefe;"';
                                        }else{
                                            var css_clas_1 = 'pull-left';
                                            var css_clas_2 = 'pull-right';
                                            var admin_chat = '';
                                            var message_data = 'style="text-align: left ;background: #feecef;"';
                                        }
                                        append_data += '<li class="left clearfix ' + admin_chat+'">';
                                        append_data +=  '<span class="chat-img1">';
                                      //  append_data +=  '<img src="{{asset('public/assets/bauenfreight/images/img-user.jpg')}}" alt="User Avatar" class="img-circle">';
                                        append_data +=   '</span>';
                                        append_data +=  '<div class="chat-body1 clearfix ' + css_clas_1 +'">';
                                        append_data +=   '<p '+ message_data+'>'+data.details[i]['message_data']+'</p>';
                                        append_data += 	'<div class="chat_time '+ css_clas_1 +'">'+format_date(data.details[i]['create_date'])+'</div>';
                                        append_data +=  '</div>';
                                        append_data +=  '</li>';     
                                    }
                                }
                                 $('.chat_sidebar').css("display","block");
                                $('#chat_id_wize_details').append(append_data);
                                 $("#msg_div").animate({ scrollTop: $('#msg_div').prop("scrollHeight")}, 2000);
                                
                            }
                });  
               
            };
</script>
<script>
$("#write_message").click(function(){
    var message_data = $("#message_description").val();
    var user_id = session_id;
    var chat_id = $("#chat_id_for_send_msg").val();
    $.post('api/write-message',
            {
                message_data: message_data,
                user_id: user_id,
                chat_id: chat_id,
            }, function (data, status) {
        if (data.result == 'TRUE') {
            get_recipient_id(chat_id);
            $("#message_description").val("");
            // window.location.reload();
            //swal("Success", "Status updated successfully !", "success");
        }else{
            alert("Message not send");
            // window.location.href = 'admin-category-wise-banner' ;
             //swal("Oops!", "Unable to update Status !", "error");
        }});
});

</script>
@endsection




@section('title')
<title>Bauenfreight</title>
@endsection



@section('banner')
@endsection

@section('main_container')
<div class="right-body">
   <div class="message wow fadeInUp">
   <input type="hidden" id="chat_id_for_send_msg" value="">
   <h5 >{{trans('pdg.79')}} </h5>
   <div class="col-sm-3 chat_sidebar" style="display: block">
    	 <div class="row">
            <div id="custom-search-input">
               <div class="input-group col-md-12">
                   <input type="text" class="  search-query form-control" placeholder="{{trans('pdg.80')}}"/>
                  <button class="btn btn-danger" type="button">
                  <span class="fa fa-search"></span>
                  </button>
               </div>
            </div>
            <div class="member_list">
               <ul class="list-unstyled">
                   @if(!empty($message_list))
                   @foreach($message_list as $list)
                  <li class="left clearfix" onclick="get_recipient_id({{$list->chat_id}})">
                        <span class="chat-img pull-left">
                            @if(!empty($details->image))                          
                                <img src="{{$img_url}}{{$details->image}}"  alt=""/>
                            @else
                                <img src="public/assets/bauenfreight/images/user-placeholder.gif" alt="User Avatar" class="img-circle">
                            @endif
                        </span>
                     <div class="chat-body clearfix">
                        <div class="header_sec">
                           <strong class="primary-font">{{$list->trns_name}}</strong> <strong class="pull-right">
                          {{date("M d, Y h:i A",strtotime($list->create_date))}}</strong>
                        </div>
                        <div class="contact_sec">
                            @if (strlen($list->message_data) > 25) {{substr($list->message_data,0,25)."..."}} 
                                    @else {{$list->message_data}}
                                    @endif
                            <span class="badge pull-right">{{$list->total_msg}}</span>
                        </div>
                     </div>
                  </li>
                  @endforeach
                  @endif              
               </ul>
            </div></div>
         </div>
         <!--chat_sidebar-->
         <div class="col-sm-9 message_section">
         <a class="btn-msg-back">&lt; Back</a>
		 <div class="row">
                       <!--new_message_head-->		 
		 <div class="chat_area" id="msg_div">
                    <!------------------------------------------------------------------------------------>
                        <ul class="list-unstyled" id="chat_id_wize_details">
                        </ul>
                    <!------------------------------------------------------------------------------------>
		 </div><!--chat_area-->
          <div class="message_write">
              <textarea class="form-control" placeholder="type a message" id="message_description"></textarea>
		 <div class="clearfix"></div>
		 <div class="chat_bottom">
                     <span class="pull-right btn btn-msgsend" id="write_message">
                    Send</span></div>
		 </div>
		 </div>
         </div> <!--message_section-->
         </div>
         
         </div>

@endsection