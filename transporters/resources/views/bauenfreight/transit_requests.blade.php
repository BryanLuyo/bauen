@extends('../master_layout/web_shipper')

@section('title')
<title>Bauen | Customer admin    |  Conectando cargas con transportistas homologados - Bauen Freight SAC - Transporte de Carga, Fletes, Carga de Pago</title>
@endsection

@section('custom_js')
<script>

    // The following example creates complex markers to indicate beaches near
      // Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
      // to the base of the flagpole.

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: {lat: -12.0202532, lng: -77.1478511}
        });

        setMarkers(map);
      }

      // Data for the markers consisting of a name, a LatLng and a zIndex for the
      // order in which these markers should display on top of each other.
  

      function setMarkers(map) {
        var image = {
          url: "{{asset('public/assets/bauenfreight/images/pin.png')}}",
          // This marker is 20 pixels wide by 32 pixels high.
          size: new google.maps.Size(25, 36),
          // The origin for this image is (0, 0).
          origin: new google.maps.Point(0, 0),
          // The anchor for this image is the base of the flagpole at (0, 32).
          anchor: new google.maps.Point(0, 32)
        };
        // Shapes define the clickable region of the icon. The type defines an HTML
        // <area> element 'poly' which traces out a polygon as a series of X,Y points.
        // The final coordinate closes the poly by connecting to the first coordinate.
        var shape = {
          coords: [1, 1, 1, 20, 18, 20, 18, 1],
          type: 'poly'
        };
        var beaches = [];
        _user_id = "{{$user_data['user_id']}}"
        _device_type = "{{$user_data['device_type']}}"
        _device_unique_code = "{{$user_data['device_unique_code']}}"
        _user_request_key = "{{$user_data['user_request_key']}}"

        $.post('http://www.bauenfreight.com/api/request_list',
                  {
                      device_type: _device_type, // se obtiene de user_request_keys
                      device_unique_code: _device_unique_code, // se obtiene de user_request_keys
                      user_id:_user_id,
                      user_request_key: _user_request_key
                  }, function (data, status) {
                       var data = JSON.parse(data);

                       if (data.status == 1) {
                           
requests =  data.requests;
if (requests==null || !requests){ console.log("Fail");}
else{
console.log("Success");
                           console.log(data);
                           
                                 
                           for (i = 0, len = requests.length; i < len; i++) {
                               currRequest= requests[i];
                               currArrayRequest = [ currRequest['pickup_location'], parseFloat(currRequest['pickup_latitude']), parseFloat(currRequest['pickup_longitude'])]
                               beaches.push(currArrayRequest);
                           }
                           console.log(beaches);

                           
                           for (var i = 0; i < beaches.length; i++) {
                             var beach = beaches[i];
                             var marker = new google.maps.Marker({
                               position: {lat: beach[1], lng: beach[2]},
                               map: map,
                               icon: image,
                               shape: shape,
                               title: beach[0],
                               zIndex: beach[3]
                             });
                           }
}
                       } else {
                           console.log("Fail");
                          
                       }
                  });
        
        
      }
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUO8nenuL0gE8nXFKD9QVZ0npzP_Cf6uo&callback=initMap">
    </script>
@endsection





@section('title')
<title>Bauen | En tránsito</title>
@endsection



@section('banner')
@endsection

@section('main_container')
<div class="right-body">
   <div class="shipper-home wow fadeInUp">
  <div class="tab-content">
    <!-- <div class="row">
      <div class="col-xs-12 pb-4">
          <div class="main-breadcrumb">
              Dashboard <i class="fa fa-angle-right"></i> Ordenes en Tránsito
          </div>
      </div>
    </div> -->
	
	<div class="row">
      <div class="col-xs-12 pb-4">
	  
	         <form id="formSearch" name="formSearch" action="{{url('transit-requests')}}" method="GET">
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

    <div id="list" >
    <div class="transit-request">
    @if(Session::has('message'))
      <div class="alert alert-info">
          <a class="close" data-dismiss="alert">×</a>
          {{Session::get('message')}}
          {{Session::forget('message')}}
      </div>
    @endif
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
            <img src="{{asset('public/assets/bauenfreight/images/no_bids_in_transit.png')}}" class="img-responsive" style="width:380px">
            <strong class="display-4 font-weight-bold mb-0 mt-4">NO TIENE ÓRDENES EN TRÁNSITO</strong>
            <span>HagA el seguimiento de sus ordenes aquí y en su celular, descargando nuestra app</span>
            <a href="{{url('your-quote')}}" class="btn btn-custom shadow px-4 py-2 ml-0 mt-4">Cotizar ahora</a>
        </div>
      </div>
    @else
		@foreach($get_requests_data as $requests_data)
      <div class="row-request">
          <div class="row">
            <div class="col-md-9">
              <div class="date d-none d-md-block">
                <small class="muted">Recojo</small>
                <span>{{date("d",strtotime($requests_data->pickup_date))}}</span>
                {{date("M",strtotime($requests_data->pickup_date))}}
                <small class="date-time">{{ $requests_data->pickup_time }}</small>
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

                @if($requests_data->update_date != '')
                  <p class="text-secondary" style="text-transform:uppercase">
                    <b>ORDEN N° {{$requests_data->request_id}} 
                      @if($requests_data->request_status == 2)
                      ESPERANDO ASIGNACIÓN DE CONDUCTOR Y TRANSPORTISTA
                      @elseif($requests_data->request_status == 3)
                      ACEPTADO POR TRANSPORTISTA
                      @elseif($requests_data->request_status == 5)
                      CONDUCTOR Y VEHICULO ASIGNADO
                      @elseif($requests_data->request_status == 6)
                      EN CAMINO
                      @elseif($requests_data->request_status == 7)
                      CARGANDO
                      @elseif($requests_data->request_status == 8)
                      CARGADO
                      @elseif($requests_data->request_status == 9)
                      INICIÓ RUTA
                      @elseif($requests_data->request_status == 10)
                      LLEGÓ AL PUNTO
                      @elseif($requests_data->request_status == 11)
                      DESCARGANDO
                      @elseif($requests_data->request_status == 12)
                       DESCARGADO
                      @endif
                     EL {{$days_dias[date("l", strtotime($requests_data->update_date))]}} {{date("d-y", strtotime($requests_data->update_date))}} a las {{date("H:i", strtotime($requests_data->update_date))}}
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

            <div class="col-md-3 d-flex flex-column text-md-right mt-4 mt-md-0">
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