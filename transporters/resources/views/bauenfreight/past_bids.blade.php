@extends('../master_layout/web_shipper')

@section('custom_js')
@endsection




@section('title')
<title>Bauenfreight</title>
@endsection



@section('banner')
@endsection

@section('main_container')
<div class="right-body">
  
   <div class="past-bids">
 <!--  get_requests_data -->
 <div class="tab-content">
    
  @if(!empty($get_requests_data))
   @foreach($get_requests_data as $requests_data)
    <div id="completed" class="tab-pane fade in active" style="padding: 10px">
 <div class="row-bidst">
 	<div class="row">
            <div class="col-sm-12 col-xs">

                <div class="box-map">
                <div class="address">
                    <span class="pull-left"><i class="fa fa-flag"></i>{{$requests_data->pickup_location}}</span>
                     <span class="pull-right"><i class="fa fa-map-marker"></i> {{$requests_data->dropoff_location}}</span>
                </div>
                <div style="width:100%;height:300px" >
                    @if(!empty($requests_data->request_image))
                    <img src="{{url('../uploads/requests/')}}/{{$requests_data->request_image}}" alt="" class="img-map"/> 
                    @else
                    <img src="{{asset('public/assets/bauenfreight/images/04_map.png')}}" alt="" class="img-map"/> 
                    @endif
                </div>
              </div>
            </div>			
        </div>
        <p class="desc" style="margin: 20px;">{{$requests_data->description}}</p>
 </div>
   <div class="info">
	  <div class="compname"><span>{{trans('pdg.84')}} Name</span>
	  {{date("M d, Y h:i A",strtotime($requests_data->create_date))}}
	  </div>
            <div class="details"><div class="info-row"><label>Request amount</label>		
            <div>	${{$requests_data->request_amount}}</div></div>
<!--            <div class="info-row"> <label>Distance covered</label>	
            <div>50km</div></div>-->
<div class="info-row"><label>Stars rating</label> <div> 
        <img src="{{asset('public/assets/bauenfreight/images/star-red.png')}}" alt=""/> 
                    <img src="{{asset('public/assets/bauenfreight/images/star-red.png')}}" alt=""/> 
                    <img src="{{asset('public/assets/bauenfreight/images/star-gray.png')}}"  alt=""/>
                    <img src="{{asset('public/assets/bauenfreight/images/star-gray.png')}}"  alt=""/> 
                    <img src="{{asset('public/assets/bauenfreight/images/star-gray.png')}}"  alt=""/>
            </div> 
</div></div>
	  </div>
    </div>
    @endforeach
      {!! with(new App\Pagination\HDPresenter($get_requests_data))->render(); !!}
 
  @endif
  

</div>

  
  
    </div>
</div>

@endsection