@extends('../master_layout/web_shipper') @section('custom_js')
<!--
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUO8nenuL0gE8nXFKD9QVZ0npzP_Cf6uo&callback=initMap">
</script>-->
@endsection @section('title')
<title>Bauen | Pedidos </title>
@endsection @section('banner') @endsection @section('main_container')
<style>
    .badge {
        display: inline-block;
        margin-left: auto;
        background: #ba4645;
        color: white;
        line-height: 2em;
        padding: 0 5px;
        border-radius: 10px;
        font-weight: 400;
        display: inline-block;
        margin-left: auto;
    }
</style>
<div class="right-body">
    
	
    @if(Session::has('message'))
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert">x</a> {{Session::get('message')}} {{Session::forget('message')}}
    </div>
    @endif
    <div class="shipper-home wow fadeInUp">
        <!-- <div class="row">
            <div class="col-xs-12 pb-4">
                <div class="main-breadcrumb">
                    Dashboard <i class="fa fa-angle-right"></i> Cotizaciones en Curso
                </div>
            </div>
        </div> -->
		
		<div class="row">
		  <div class="col-xs-12 pb-4">
				 <form id="formSearch" name="formSearch" action="{{url('request-list')}}" method="GET">
				 <div class="form-group form-group-lg" style="margin-bottom: 0px !important;">
						<div class="input-group">
							<span class="input-group-addon" style="background-color: #eeededcf; border: 0px;">
								<span class="glyphicon glyphicon-search"></span>
							</span>
							<input type="text" style="background-color: #eeededcf; border: 0px;" class="form-control" name="search" id="search" placeholder="Buscar palabra clave (Código de asignacion, RQ, Origen, Destino, Descripción)" onkeyup="buscar()">
						</div>
				   </div>
				  </form> 
		  </div>
		</div>  

        <div id="list">
            <div class="transit-request">
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
                @if(empty($get_requests_data) || count($get_requests_data) == 0)
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-8 d-flex flex-column align-items-center justify-content-center py-5 text-center">
                            <img src="{{asset('public/assets/bauenfreight/images/no_bid_found2.png')}}" class="img-responsive" style="width:380px">
                            <strong class="display-4 font-weight-bold mb-0 mt-4">AUN NO TIENE ÓRDENES EN CURSO</strong>
                            <span>Cotizaciones que aún no han sido aceptadas o asignadas a un transportista</span>
                            <a href="{{url('your-quote')}}" class="btn btn-custom shadow px-4 py-2 ml-0 mt-4">Cotizar ahora</a>
                        </div>
                    </div>
                @else
                    @foreach($get_requests_data as $requests_data)
                        <div class="row-request ">
                            <div class="row">
                                    <div class="col-md-9">
                                            <div class="date d-none d-md-block">
                                              <small class="muted">Recojo</small>
                                              <span>{{date("d",strtotime($requests_data->pickup_date))}}</span>
                                              {{date("M",strtotime($requests_data->pickup_date))}}
                                              <small class="date-time">{{$requests_data->pickup_time}}</small>
                                            </div>
                                            
                                            <div class="request-info">
                                              @php
                                                $datosArray = (array)$requests_data;
                                              @endphp
                                              @if( array_key_exists('company_name', $datosArray) )
                                                <div class="request-quote">
                                                  <h5 class="my-0" style="text-transform: none"><span>{{$datosArray['company_name']}}</span></h5>
                                                </div>
                                              @endif
                                  
                                            @if($requests_data->create_date != '')
                                                <p style="text-transform:uppercase">
                                                  <b>CREADA EL {{$days_dias[date("l", strtotime($requests_data->create_date))]}} {{date("d/m/y", strtotime($requests_data->create_date))}} a las {{date("H:i", strtotime($requests_data->create_date))}}
                                                  </b>
                                                </p>
                                            @endif
                                            Solicitado por {{ $requests_data->sub_first_name }} {{ $requests_data->sub_last_name }}
                            
                                              <p>
                                                <i class="fa fa-flag"></i>
                                                {{$requests_data->pickup_location}}</p>
                                              <p>
                                                <i class="fa fa-map-marker"></i> 
                                                {{$requests_data->dropoff_location}}</p>
                                              
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

                                <div class="col-md-3 d-flex flex-column text-md-right mt-4 mt-md-0 pt-md-4 pr-md-5">
                                    <div>
                                        <a href="{{url('request-list-details-'.base64_encode($requests_data->request_id.'||'.env(" APP_KEY ")))}}" class="btn btn-custom shadow-sm ml-0" style="font-weight:400">
                                            @if($requests_data->bids_count == 0)
                                                Aún sin cotizaciones
                                            @elseif($requests_data->bids_count == 1)
                                                <span class="pill-number bg-secondary text-white mr-1">1</span> cotización
                                            @else
                                            <span class="pill-number bg-secondary text-white mr-1">{{$requests_data->bids_count}}</span> cotizaciones
                                            @endif

                                        </a>
                                    </div>
                                    {{-- <div>
                                        <a href="#" class="btn  btn-custom" onclick="event.preventDefault();
                                        document.getElementById('{{'request-edit-form-'.strval($requests_data->request_id)}}').submit();">Editar</a>
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-custom" onclick="event.preventDefault();
                                        document.getElementById('{{'request-delete-form-'.strval($requests_data->request_id)}}').submit();">Cancelar</a>
                                    </div> --}}
                                </div>
                            </div>

                            <div class="request-info-container">
                                <form id="{{'request-delete-form-'.strval($requests_data->request_id)}}" action="{{ url('/request/delete') }}" method="POST" style="display: none;">
                                    <input type="hidden" name="request_id" value="{{$requests_data->request_id}}"> {{ csrf_field() }}
                                </form>
                                <form id="{{'request-edit-form-'.strval($requests_data->request_id)}}" action="{{route('requestEdit',['request_id' =>$requests_data->request_id ])}}" method="GET" style="display: none;">
                                    <input type="hidden" name="request_id" value="{{$requests_data->request_id}}">
                                    <input type="hidden" name="pickup_location" value="{{$requests_data->pickup_location}}">
                                    <input type="hidden" name="pick_place_id" value="{{$requests_data->pickup_place_id}}">
                                    <input type="hidden" name="pick_lat" value="{{$requests_data->pickup_latitude}}">
                                    <input type="hidden" name="pick_long" value="{{$requests_data->pickup_longitude}}">
                                    <input type="hidden" name="dropoff_location" value="{{$requests_data->dropoff_location}}">
                                    <input type="hidden" name="drop_place_id" value="{{$requests_data->dropoff_place_id}}">
                                    <input type="hidden" name="drop_lat" value="{{$requests_data->dropoff_latitude}}">
                                    <input type="hidden" name="drop_long" value="{{$requests_data->dropoff_longitude}}">
                                    <input type="hidden" name="pickup_date" value="{{$requests_data->pickup_date}}">
                                    <input type="hidden" name="pickup_time" value="{{$requests_data->pickup_time}}">
                                    <input type="hidden" name="trailer_id" value="{{$requests_data->trailer_id}}">
                                    <input type="hidden" name="request_amount" value="{{$requests_data->request_amount}}">
                                    <input type="hidden" name="weight" value="{{$requests_data->weight}}">
                                    <input type="hidden" name="size" value="{{$requests_data->size}}">
                                    <input type="hidden" name="description" value="{{$requests_data->description}}">
                                    <input type="hidden" name="request_image" value="{{$requests_data->request_image}}"> {{ csrf_field() }}
                                </form>
                            </div>
                            {{-- <div class="d-flex" style="margin-top: 15px;">
                                <div>
                                    <a href="{{url('request-list-details-'.base64_encode($requests_data->request_id.'||'.env(" APP_KEY ")))}}" class="btn btn-custom">Ver propuestas</a>
                                </div>
                                <div>
                                    <a href="#" class="btn  btn-custom" onclick="event.preventDefault();
                                    document.getElementById('{{'request-edit-form-'.strval($requests_data->request_id)}}').submit();">Editar</a>
                                </div>
                                <div>
                                    <a href="#" class="btn btn-custom" onclick="event.preventDefault();
                                    document.getElementById('{{'request-delete-form-'.strval($requests_data->request_id)}}').submit();">Cancelar</a>
                                </div>
                            </div> --}}

                        </div>
                    <?php //} ?>
                    @endforeach
                    {!! with(new App\Pagination\HDPresenter($get_requests_data))->render(); !!}
                @endif
            </div>
        </div>

    </div>
</div>

<script>

  function buscar() {
	  var search = document.getElementById("search").value;
	  $("#formSearch").submit();
  }
     
</script>

@endsection