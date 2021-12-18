@extends('../master_layout/web_shipper')

@section('title')
<title>Bauen | Solicitudes canceladas y vencidas</title>
@endsection

@section('title')
<title>Bauenfreight</title>
@endsection



@section('banner')
@endsection

@section('main_container')
<div class="right-body">
  <div class="shipper-home wow fadeInUp">
    @if(Session::has('message'))
      <div class="alert alert-info">
          <a class="close" data-dismiss="alert">×</a>
          {{Session::get('message')}}
          {{Session::forget('message')}}
      </div>
    @endif
    <ul class="nav nav-tabs custom-tab" style="margin-bottom: 1rem;">
      <li class="active">
        <a data-toggle="tab" href="#cancelled">Canceladas</a>
      </li>
      <li>
        <a data-toggle="tab" href="#expired">Vencidas</a>
      </li>
    </ul>
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="cancelled">
        <div class="transit-request">
          @if(!empty($get_requests_data))
            @php $cancelled_count = 0 @endphp
            @foreach($get_requests_data as $requests_data)
              @if($requests_data->request_status == 4)
                @php $cancelled_count++;      	 
			    @endphp
			    
                <div class="row-request">
                  <div class="date"><span>{{date("d",strtotime($requests_data->create_date))}}</span>{{date("M",strtotime($requests_data->create_date))}}</div>
                  <div class="request-info">
                    <a class="icon-map" href="{{url('request-list-details-'.base64_encode($requests_data->request_id.'||'.env("APP_KEY")))}}"><img src="{{asset('public/assets/bauenfreight/images/icon-map.png')}}" alt=""/></a>
                    <p class="time">{{trans('pdg.108')}} {{date("h:i A",strtotime($requests_data->create_date))}}</p>
                    <p><i class="fa fa-flag"></i>{{$requests_data->pickup_location}}</p>
                    <p><i class="fa fa-map-marker"></i> {{$requests_data->dropoff_location}}</p>
                  </div>
                </div>
              @endif
            @endforeach
            {!! with(new App\Pagination\HDPresenter($get_requests_data))->render(); !!}
            @if($cancelled_count == 0)
              <p>No tiene cotizaciones canceladas.</p>
            @endif
          @endif
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="expired">
        <div class="transit-request">
          @if(!empty($get_requests_data))
            @php $expired_count = 0 @endphp
            @foreach($get_requests_data as $requests_data)
              @if($requests_data->request_status == 14)
                @php $expired_count++; @endphp
                <div class="row-request">
                  <div class="date"><span>{{date("d",strtotime($requests_data->create_date))}}</span>{{date("M",strtotime($requests_data->create_date))}}</div>
                  <div class="request-info">
                    <a class="icon-map" href="{{url('request-list-details-'.base64_encode($requests_data->request_id.'||'.env("APP_KEY")))}}"><img src="{{asset('public/assets/bauenfreight/images/icon-map.png')}}" alt=""/></a>
                    <p class="time">{{trans('pdg.108')}} {{date("h:i A",strtotime($requests_data->create_date))}}</p>
                    <p><i class="fa fa-flag"></i>{{$requests_data->pickup_location}}</p>
                    <p><i class="fa fa-map-marker"></i> {{$requests_data->dropoff_location}}</p>
                  </div>
                </div>
              @endif
            @endforeach
            {!! with(new App\Pagination\HDPresenter($get_requests_data))->render(); !!}
            @if($expired_count == 0)
              <p>No tiene cotizaciones vencidas.</p>
            @endif
          @endif
        </div>
      </div>
    </div>
	  </div>
         
         </div>
@endsection