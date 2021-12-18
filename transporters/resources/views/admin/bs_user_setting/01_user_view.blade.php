@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection



@section('content_header')
 <a href="{{url('admin-user-list')}} "><i class="icon-home"></i>Registered User</a>
@endsection

@section('content')

<div class="span12">
    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">User Search</h5>
        </div>
        <div class="widget-content nopadding">

            <form class="form-horizontal" method="GET" action="" >
                
                 <div class="control-group">
                    <label class="control-label"><b>Search by email:</b></label>
                    <div class="controls " >  
                       <input type="text" name="search_key" value="@if(!empty($search['search_key'])){{$search['search_key']}}@endif">
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Filter" class="btn btn-success">
                    <a href="{{URL::to('admin-user-list')}}" class="btn btn-default">Reset</a>
                    <a href="javascript:void(0);" onclick="open_notification_modal()" class="btn btn-info">Send notification</a>
                    
                </div>
            </form>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">User list</h5>
<!--<a href="#" class="btn btn-mini btn-primary pull-right button_class">Add New</a>-->
        </div>
        @if(!empty($user_list))
        <?php $sl = 0 ;?>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                    <th><input type="checkbox" name="" id="select_all" value="all" onclick="select_all();" /> &nbsp; Select all</th>
                        <th>SL.No</th>
                        <th>User Name</th>
                        <th>User Image</th>
                        <th>User Email</th>
                        <th>Phon No</th>
                        <th>Status</th>
                        <th>Action</th>
                       

                    </tr>
                </thead>
                <tbody>

                    @foreach ($user_list as $user) 
                    <?php $sl = $sl+1; ?>
                    <tr>
                    <td class="center"><input type="checkbox" name="select_[]" class="user_select" onclick="check_select_all()" value="{{$user->id}}" /></td>

                        <td class="center">{{ $sl}} </td>
                        <td class="center"> {{ ucwords($user->user_name)}}</td>
                        <td class="center"> 
                            @if(!empty($user->user_image))
                            <img src="{{asset('public/assets/images/'.$user->user_image)}}" alt="" height="42" width="42">
                             @else 
                             <img src="{{asset('public/assets/images/01_dummy_user.png')}}" alt="" height="42" width="42">
                             @endif
                        </td>
                        
                        <td class="center"> {{ ucwords($user->user_email)}}</td>
                        <td class="center"> {{ $user->user_phone_num}}</td>
                         
                        <td class="center"> @if ($user->status==1) {{"Active"}} @else {{"Block"}}  @endif            
                            
                        </td> 
                       
                        

                        <td class=" ">
                            <input type="hidden" id="user_details_{{$user->id}}" value="{{json_encode($user)}}">                            
                             @if ($user->status==1)  
                             <a class="btn btn-mini mergin_one" onclick="user_chenge_status('{{base64_encode(env('APP_KEY').'||'.$user->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"block"}} 
                            </a>
                             @else 
                             <a  class="btn btn-mini mergin_one" onclick="user_chenge_status('{{base64_encode(env('APP_KEY').'||'.$user->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"Active"}}      
                                </a>
                             @endif 
                      </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!!$user_list->appends(['search_key'=>Request::get('search_key')])->setPath('')->render()!!}
        </div>
        @else
        <div class="widget-content nopadding">  <h2>User not find</h2> </div>
        @endif
    </div>
</div>


@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/item_js.js')}}"></script>
<script>
var selected_user_array = [];
function select_all(){
    if ($('#select_all').is(":checked"))
{
    $(".user_select").attr('checked',true); 

    selected_user_array = [];
    $(".user_select").each(function(index,item){
        selected_user_array.push($(".user_select").eq(index).val());
    });

}else{
    selected_user_array = [];
    $(".user_select").attr('checked',false);   
}
console.log(selected_user_array);
}

function check_select_all(){
    var i=0;
    selected_user_array = [];
    $(".user_select").each(function(index,item){
       
       if( $(".user_select").eq(index).is(":checked")){
        selected_user_array.push($(".user_select").eq(index).val()); 
       }else{
        i++;
       }
       if(i>0){
        $("#select_all").attr('checked',false);   
 
       }else{
        $("#select_all").attr('checked',true);   

           }
    });
    console.log(selected_user_array);
}

</script>
<script>
function open_notification_modal(){

    if(selected_user_array.length == 0){
        alert("Please select atleast one recipient.");
    }else{
    $('#notification_recipient').val(selected_user_array);
    $('#notification_message').val("");
    $('#notification_modal').modal('show');   
    }
}

</script>
<script>
$('#notification_modal_form').validate({
            rules: {
                notification_recipient: {
                    required: true
                },
                notification_message: {
                    required: true, 
                           } 
            },
            messages: {
                notification_recipient: {
                        required: 'Please select atleast one recipient.',
                    },
                    notification_message: {
                        required: 'Notification message can not be blank..',
                }
            },
            submitHandler: function(form) {
               $.ajax({
                   url: form.action,
                   type: form.method,
                   data: $(form).serialize(),
                   dataType: "json",
                   success: function(response) {
                       console.log(response);
                      alert(response.message);
                      $("#notification_modal").modal('toggle');
                   },
                   beforeSend: function(){
                   },
                  complete: function(){
                  },
                  error: function(event,status){
                    alert("Something went wrong. Please try again later.");
                  }
               });
           }
       });


       $('#notification_modal_form').on('hidden.bs.modal', function () {
           $(this).find('form').trigger('reset');
});
</script>
@endsection
<!--------------------------------change password---------------------------------->
<div id="notification_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

   <!--  Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Send Notification</h4>
      </div>
        <form method="POST" id="notification_modal_form" name="notification_modal_form" action="{{url('admin-send-notification')}}">

      <div class="modal-body ">
          
        <div class="form-horizontal">


        <div class="control-group">
               <label class="control-label label_class_css" for=""></label>
                <input type="hidden" id="notification_recipient" class="form-control input_class_css" name="notification_recipient" value="" />    
            </div>
          
            <div class="control-group">
               <label class="control-label label_class_css" for="">Notification message:</label>
                <textarea class="form-control input_class_css" rows="5" cols="60" id="notification_message" name="notification_message">    
                </textarea>                                
            </div>
        </div>
            
      </div>
      <div class="modal-footer">
          <button type="submit" id="send_notification" class="btn btn-default">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
         </form>
    </div>
  </div>
</div>

<!--------------------------------change password---------------------------------->