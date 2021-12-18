 @if(Session::has('message'))
      <div class="alert alert-info">
          <a class="close" data-dismiss="alert">×</a>
          {{Session::get('message')}}
          {{Session::forget('message')}}
      </div>
    @endif
    @if(empty($get_requests_data) || count($get_requests_data) == 0)
      <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center">
          <img src="{{asset('public/assets/bauenfreight/images/no_bid_found2.png')}}" class="img-responsive" style="width:380px">
          <strong class="display-4 font-weight-bold mb-0 mt-4">AUN NO TIENE ÓRDENES COMPLETADA</strong>
          {{-- <span>Cotizaciones que aún no han sido aceptadas por usted</span> --}}
          <a href="{{url('your-quote')}}" class="btn btn-custom shadow px-4 py-2 ml-0 mt-4">Cotizar ahora</a>
      </div>
    @else
      @php
        $days_dias = array(
        'Monday'=>'Lunes',
        'Tuesday'=>'Martes',
        'Wednesday'=>'Miércoles',
        'Thursday'=>'Jueves',
        'Friday'=>'Viernes',
        'Saturday'=>'Sábado',
        'Sunday'=>'Domingo'
        );
      @endphp

      @foreach($get_requests_data as $requests_data)
        <div class="row-request">
          <div class="row">
            <div class="col-md-9">
              <div class="text-success d-none" style="float:right;margin-left: 1rem;">
                <strong style="font-size:1.6rem">
                  S/ {{$requests_data->granted_amount}}.00
                </strong>
                <small><sup>+ IGV</sup></small>
              </div>

              <div class="date d-none d-md-block">
                <small class="muted">Recojo</small>
                <span>{{date("d",strtotime($requests_data->pickup_date))}}</span>
                {{date("M",strtotime($requests_data->pickup_date))}}
                <small class="date-time">{{ $requests_data->pickup_time }}</small>
              </div>
              
              <div class="request-info">
                {{-- <a class="icon-map" href="{{url('request-list-details-'.base64_encode($requests_data->request_id.'||'.env("APP_KEY")))}}"><img src="{{asset('public/assets/bauenfreight/images/icon-map.png')}}" alt=""/></a> --}}
                {{-- <p class="time">{{trans('pdg.108')}} {{date("h:i A",strtotime($requests_data->create_date))}}</p> --}}
                {{-- <strong style="color:#ae0d27">ORDEN N° {{$requests_data->request_id}}</strong> --}}
                
                @php
                  $datosArray = (array)$requests_data;
                @endphp
                @if( array_key_exists('company_name', $datosArray) )
                  <div class="request-quote">
                    <h5 class="my-0" style="text-transform: none"><span>{{$datosArray['company_name']}}</span></h5>
                  </div>
                @endif
    
                @if($requests_data->completed_date != '')
                  <p class="text-secondary" style="text-transform:uppercase">
                    <b>ORDEN N° {{$requests_data->request_id}} COMPLETADA EL {{$days_dias[date("l", strtotime($requests_data->completed_date))]}} {{date("d-y", strtotime($requests_data->completed_date))}} a las {{date("H:i", strtotime($requests_data->completed_date))}}
                  </p>
                @else
                  <p class="text-secondary"><b>ORDEN N° {{$requests_data->request_id}} COMPLETADA</b></p>
                  @endif
                </b></p>
                Solicitado por {{ $requests_data->sub_first_name }} {{ $requests_data->sub_last_name }}
    
                @if($requests_data->granted_amount != 0)
                  {{-- <p><i class="fa fa-money"></i> <span>S/{{$requests_data->granted_amount}} </span><i style="display:inline;width:100%;color:#3b3b3b">(No incluye IGV)</i></p> --}}
                @endif
                <p>
                  <i class="fa fa-flag"></i>
                  {{$requests_data->pickup_location}}
                </p>

                <p>
                  <i class="fa fa-map-marker"></i> 
                  {{$requests_data->dropoff_location}}
                </p>

                <p class="d-md-none">
                  <i class="fa fa-clock-o"></i>
                  Recojo el {{date("d/m/Y h:i A",strtotime($requests_data->pickup_date))}}
                </p>

                <hr class="my-1 d-md-none">
                <p>
                  <b class="d-block d-md-none">DESCRIPCIÓN</b>
                  {{$requests_data->description}}
                </p>
              </div>
            </div>
            <div class="col-md-3 d-flex flex-column text-md-right mt-2 mt-md-4 mt-md-0">
              {{-- <div class="text-success d-none d-md-block"> --}}
              <div class="text-success">
                <strong style="font-size:1.6rem">
                  S/ {{$requests_data->granted_amount}}.00
                </strong>
                <small><sup>+ IGV</sup></small>
              </div>
              <a href="{{url('request-list-details-'.base64_encode($requests_data->request_id.'||'.env("APP_KEY")))}}" class="btn btn-custom mt-2 mt-md-4 mx-0">Ver detalles de la carga</a>
            </div>
          </div>
        </div>
      @endforeach
      {!! with(new App\Pagination\HDPresenter($get_requests_data))->render(); !!}
   @endif
  
   </div>