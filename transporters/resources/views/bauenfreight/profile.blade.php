@extends('../master_layout/web_shipper')

@section('custom_js')
<script>
  $(document).ready(function() {
    const aboutEditMode = (boolean) => {
      if(boolean) {
        $('#about-p').hide()
        $('#update-about').show()
      } else {
        $('#about-p').show()
        $('#update-about').hide()
      }
    }

    $('#update-about-cancel').click(() => aboutEditMode(false))

    $('#edit_about').click( e => {
      e.preventDefault()
      aboutEditMode(true)
    })
  })
</script>
@endsection

@section('custom_css')
  <style type="text/css">
    .update-about {
      display: none;
    }
  </style>
@endsection

@section('title')
<title>Bauenfreight</title>
@endsection

<?php
// echo '<pre>';
// print_r($details);
// echo '</pre>';
?>

@section('banner')
@endsection

@section('main_container')
<div class="right-body">
    @if(Session::has('message'))
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert">×</a>
       {{Session::get('message')}}
        {{Session::forget('message')}}
    </div>
    @endif
     <div class="profile">
@if(!empty($details))	 
	  	<div class="user-info">
	  	  <div class="user-photo">
          @if(!empty($details->image)) 
            <img src="{{url('../uploads/users/')}}/{{$details->image}}"  alt=""/>
          @else
            <img src="{{asset('public/assets/bauenfreight/images/user-placeholder.gif')}}"  alt=""/>
          @endif
        </div>
        <div class="info"> {{$details->first_name}} {{$details->last_name}}</div>
  	    <a data-toggle="modal" data-target="#edit_picture" class="btn-editpic"><i class="fa fa-pencil"></i> {{trans('pdg.81')}}</a>
      </div>
      <div class="row user-details">
	  		<div class="col-sm-6 col-xs-12 font-avenir">
          <p class="title">Contact Info</p>
          <p><i class="fa fa-phone"></i> {{trans('pdg.82')}}: {{$details->phone_no}}</p>
          <p><i class="fa fa-envelope"></i> E-mail: <a mailto:>{{$details->email}}</a> </p>
          @if($details->address != '' || $details->country_code)
            <p>
              <i class="fa fa-map-marker"></i>
              @if($details->address != '') {{$details->address}}, @endif @if($details->country_code != ''){{$details->country_code}}@endif
            </p>
          @endif
      
        <!--
      <div class="form-group">
        <label class="sr-only" for="txtAddress">Amount (in dollars)</label>
        <div class="input-group">
          <div class="input-group-addon"><i class="fa fa-map-marker"></i></div>
          <input type="text" class="form-control" id="txtAddress" placeholder="Address" value="{{$details->address}}">          
        </div>
      </div>-->

      <p class="title">{{trans('pdg.83')}} (<a href="#" id="edit_about"><i class="glyphicon glyphicon-pencil" style="min-width: 13px;"></i> Actualizar</a>)</p>
			<p id="about-p" class="about-p">@if($details->about_us != ''){{$details->about_us}}@else Aún no has llenado este campo. @endif</p>
      <div class="form-group update-about" id="update-about">
        <form action="{{url('post-profile')}}" method="POST" id="update-about-form">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="user_id" value="{{$details->user_id}}">
          <textarea cols="4" rows="4" class="form-control" name="about_us">{{$details->about_us}}</textarea>
          <input type="submit" class="btn btn-custom" style="margin-left:0; margin-top: 10px;" value="Actualizar">
          <button type="button" class="btn btn-link" style="margin-top: 10px;" id="update-about-cancel">Cancelar</button>
        </form>
      </div>

			</div>			
			<div class="col-sm-6 col-xs-12 font-avenir">
        <p class="title">Rating</p>
        <p>
          <img src="{{asset('public/assets/bauenfreight/images/star-red.png')}}" alt="">
          <img src="{{asset('public/assets/bauenfreight/images/star-red.png')}}" alt="">
          <img src="{{asset('public/assets/bauenfreight/images/star-gray.png')}}" alt=""> 
          <img src="{{asset('public/assets/bauenfreight/images/star-gray.png')}}" alt="">
          <img src="{{asset('public/assets/bauenfreight/images/star-gray.png')}}" alt="">
        </p>
<!--			<p class="title">Over All Bids</p>
			<p>65%</p>-->
			</div>
	  	</div>
	  	<!--<div class="user-details">
        <p class="title">{{trans('pdg.83')}}</p>
        <p>
        {{$details->about_us}}               
        </p>
      <textarea name="" id="" cols="4" rows="4" class="form-control" placeholder="{{$details->about_us}}"></textarea>
			</div>-->
	  
@else
<div>User Details Not Found</div>
@endif
    
    
    </div>
         </div>

<!--------------------------------change password---------------------------------->
<div id="edit_picture" class="modal fade" role="dialog">
  <div class="modal-dialog">

   <!--  Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit picture</h4>
      </div>
        <form action="{{'upload-user-image'}}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="user_id" value="{{Session::get('web_user_id')}}">
      <div class="modal-body ">
         
        <div class="form-horizontal">
            <div class="control-group">
               <label class="control-label label_class_css" for="">Select image:</label>
               <input type="file" class="form-control"  name="user_image">  
                @if(!empty($details->image)) 
                      <img src="{{url('../uploads/users/')}}/{{$details->image}}" width="42" height="42"  alt=""/>
                    @else
                    <img src="{{asset('public/assets/bauenfreight/images/user-placeholder.gif')}}"  width="42" height="42" alt=""/>
                    @endif
            </div>			<input type="hidden" name="user_id" value="{{$details->user_id}}">
        </div>
           
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-default">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
         </form>
    </div>
  </div>
</div>

<!--------------------------------change password---------------------------------->


@endsection