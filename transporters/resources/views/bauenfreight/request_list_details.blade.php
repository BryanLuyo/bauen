{{-- <meta http-equiv="content-type" content="text/html; utf-8"> --}}
@extends('../master_layout/web_shipper')

@section('custom_js')
			
    <script>
        let pickup_latitude = "{{$requests_data->pickup_latitude}}",
        pickup_longitude = "{{$requests_data->pickup_longitude}}",
        pickup_location = "{{$requests_data->pickup_location}}",
        dropoff_latitude = "{{$requests_data->dropoff_latitude}}",
        dropoff_longitude = "{{$requests_data->dropoff_longitude}}",
        dropoff_location = "{{$requests_data->dropoff_location}}";

        const requestTrack = {!! json_encode($request_track) !!};
		
		//funcion timer
		if("{{$requests_data->close_bid_time}}" != "" ) {
			timerInterval("{{$requests_data->close_bid_time}}");
		}

        function initMap() {
            var polylineOptionsActual = {
            strokeColor: '#fec40e',
            strokeOpacity: 1.0,
            strokeWeight: 4
            };
            var directionsService = new google.maps.DirectionsService();
            var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true, polylineOptions: polylineOptionsActual});
            var haight = new google.maps.LatLng(pickup_latitude, pickup_longitude);
            var oceanBeach = new google.maps.LatLng(dropoff_latitude, dropoff_longitude);
            var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: {lat: -12.0202532, lng: -77.1478511}
            });

            if(requestTrack != null && requestTrack.length > 0) {
                const trackIcon = {
                    url: "{{asset('public/assets/bauenfreight/images/track-icon-marker.png')}}",
                    size: new google.maps.Size(30, 30),
                    origin: new google.maps.Point(0, 0)
                };
                const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

                requestTrack.forEach(track => {
                    let newDate = new Date(track.create_date);
                    let dayName = days[newDate.getDay()];
                    let monthName = months[newDate.getMonth()];
                    let hours = (newDate.getHours() < 10 ? '0': '') + newDate.getHours();
                    let minutes = (newDate.getMinutes() < 10 ? '0': '') + newDate.getMinutes();

                    let formattedDate = `${dayName} ${newDate.getDate()} de ${monthName} ${hours}:${minutes}`;

                    let trackInfowindow = new google.maps.InfoWindow({
                        content: `
                        <div id="content" class="mt-2 mx-1 firstHeading">
                            <small style="color:black">Estuvo aquí el:</small><br>
                            ${formattedDate}
                        </div>`
                    });

                    let trackMarker = new google.maps.Marker({
                        position: new google.maps.LatLng(track.latitude, track.longitude),
                        map: map,
                        icon: trackIcon
                    });

                    google.maps.event.addListener(trackMarker, 'click', function() {
                        trackInfowindow.open(map, trackMarker);
                    });
                })
            }

            var request = {
                origin: haight,
                destination: oceanBeach,
                travelMode: google.maps.TravelMode['DRIVING']
            };
            directionsService.route(request, function(response, status) {
                if (status == 'OK') {
                directionsDisplay.setDirections(response);
                }
            });
            directionsDisplay.setMap(map);
            setMarkers(map);

            setTimeout(() => {
            map.setZoom(map.getZoom() - 1);
            }, 2000);
        }

        function setMarkers(map) {
            var bounds = new google.maps.LatLngBounds();
            // var pickupInfowindow = new google.maps.InfoWindow();
            // var dropoffInfowindow = new google.maps.InfoWindow();

            var contentStringPickup = `
            <div id="content">
                <div id="siteNotice">
                </div>
                <h2 id="firstHeading" class="firstHeading mt-3 mb-1">${pickup_location}</h2>
            </div>`;

            var pickupInfowindow = new google.maps.InfoWindow({
                content: contentStringPickup
            });

            var contentStringDropoff = `
            <div id="content">
                <div id="siteNotice">
                </div>
                <h2 id="firstHeading" class="firstHeading mt-3 mb-1">${dropoff_location}</h2>
            </div>`;

            var dropoffInfowindow = new google.maps.InfoWindow({
                content: contentStringDropoff
            });

            var pickupIcon = {
                url: "{{asset('public/assets/bauenfreight/images/pickup-marker.png')}}",
                size: new google.maps.Size(32, 38),
                origin: new google.maps.Point(0, 0)
            };

            var dropoffIcon = {
                url: "{{asset('public/assets/bauenfreight/images/dropoff-marker.png')}}",
                size: new google.maps.Size(32, 38),
                origin: new google.maps.Point(0, 0)
            };

            var pickupMarker = new google.maps.Marker({
                position: new google.maps.LatLng(pickup_latitude, pickup_longitude),
                map: map,
                icon: pickupIcon
            });

            bounds.extend(pickupMarker.position);

            google.maps.event.addListener(pickupMarker, 'click', function() {
                pickupInfowindow.open(map, pickupMarker);
            });

            pickupInfowindow.open(map,pickupMarker);

            var dropoffMarker = new google.maps.Marker({
                position: new google.maps.LatLng(dropoff_latitude, dropoff_longitude),
                map: map,
                icon: dropoffIcon
            });

            bounds.extend(dropoffMarker.position);

            google.maps.event.addListener(dropoffMarker, 'click', function() {
                dropoffInfowindow.open(map, dropoffMarker);
            });

            dropoffInfowindow.open(map,dropoffMarker);

            // map.fitBounds(bounds, {top:'100px'});
            // setTimeout(() => {
            //     map.setZoom(map.getZoom() - 5);
            // }, 1000);
        }

        function setAcceptBidUrl(url) {
            document.getElementById('acceptBidUrl').href = url
        }
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUO8nenuL0gE8nXFKD9QVZ0npzP_Cf6uo&callback=initMap">
    </script>
@endsection

@section('custom_css')
    <style>
        .gm-style .gm-style-iw-c {
            padding-top: 0!important;
        }

        @media screen and (min-width: 768px) {
            .request-details-btns {
                margin-top: -4rem!important;
            }
        }

        a[href^="http://maps.google.com/maps"]{display:none !important}
        a[href^="https://maps.google.com/maps"]{display:none !important}

        .gmnoprint, .gmnoprint a, .gmnoprint span, .gm-style-cc {
            display:none;
        }
        .gmnoprint div {
            background:none !important;
        }
        .request-detail-list {
            display: flex;
            flex-direction: column;
        }
        .request-detail-list,
        .request-detail-container {
            /* box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2); */
            /* border-radius: 10px; */
            padding: 0;
        }

        .request-detail-container {
            margin-bottom: 20px;
        }

        .request-detail-item {
            border-bottom: 1px solid #e1e1e1;
            padding: 20px 30px;
            order: 1;
        }

        .request-detail-item.winner {
            border: 2px solid #fec40e;
            order: 0;
        }

        .request-detail-item .row {
            display: block;
        }

        @media screen and (min-width: 768px) {
            .request-detail-item .row {
                display: flex;
            }
        }

        .request-detail-item:not(.winner):not(:first-child):last-child {
            border-bottom: 0;
        }

        .v-center {
            justify-content: center;
        }
        .h-center {
            align-items: center;
        }

        .request-detail-item-btn-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 10px;
        }

        @media screen and (min-width: 768px) {
            .request-detail-item-btn-container {
                justify-content: center;
                margin-top: 0;
            }
        }

        .trailer-details {
            display: flex;
            align-items: center;
        }

        .trailer-image {
            height: 22px;
            width: 22px;
        }
        
        .map-container {
            position: relative;
        }

        .location_details {
            /* padding-left: 0; */
            list-style: none;
            margin: 0;
            position: absolute;
            bottom: 15px;
            left: 15px;
            text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.1);
            padding: 15px;
            background: rgba(220,220,220, 0.5)
        }

        .location_details li {
            display: flex;
            align-items: center;
        }

        .location_details li:first-child {
            margin-bottom: 5px;
        }

        .custom-th {
            min-width:220px;
            padding-right:35px;
            vertical-align:top;
        }
    </style>
@endsection

@section('title')
<title>Bauen | Detalle</title>
@endsection

@section('main_container')
@if(!empty($requests_data))

<div id="id03" class="w3-modal">
    <div class="w3-modal-content" style="width: 450px; padding-top: 20px; padding-bottom: 20px;">
      <div class="w3-container">
       <!-- <span onclick="document.getElementById('id02').style.display='none'" class="w3-button w3-display-topright">&times;</span> -->
       
	   <form id="form_et" name="form_et" method="GET" action="{{route('requestEditCloseTime',['request_id' =>$requests_data->request_id ])}}">
			  <div class="form-group form-group-lg">
				<p class="text-center" style="font-family: 'Montserrat', sans-serif!important;"><label style="color: #000000bf; margin-bottom: 0px;">¿Desea ver los precios ahora?</label></p>
				<!-- <p class="text-center" style="margin-bottom: 17px;">No podrá recibir más precios a partir del momento</p> -->
				<div class="input-group" style="display: block;">
					<textarea class="form-control" style="margin-bottom: 17px; font-family: 'Montserrat', sans-serif!important; font-size: 14px !important; " row="8" name="comentarioAdelanto" id="comentarioAdelanto" placeholder="Comentario (no obligatorio)..."></textarea>
                </div>
				
				<div class="input-group center-block" style="width: 90%;" >
				
				<button class="btn btn-warning" style="width: 40%; float: left; margin-left: 7%;" id = "btnFC1" >Sí</button> 
				<button class="btn btn-default pull-right" style="width: 40%; margin-right: 7%;" onclick="document.getElementById('id03').style.display='none'; return false;">Seguir recibiendo</button>

				</div>
			</div>
	   </form>
      </div>
    </div>
</div>	

<div class="right-body" style="padding:0;">
	
	@if(Session::has('message'))
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert">x</a> {{Session::get('message')}} {{Session::forget('message')}}
    </div>
    @endif
	
    <div class="row">
        <div class="col-md-12">
            <div class="map-container">
                <div id="map" style="width:100%;height:300px"></div>
                {{-- <ul class="location_details">
                    <li>
                        <img class="trailer-image" src="{{url('/public/assets/bauenfreight/images/pickup-icon.png')}}">
                        &nbsp;{{$requests_data->pickup_location}}
                    </li>
                    <li>
                        <img class="trailer-image" src="{{url('/public/assets/bauenfreight/images/dropoff-icon.png')}}">
                        &nbsp;{{$requests_data->dropoff_location}}
                    </li>
                </ul> --}}
            </div>
        </div>
        
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
			<input type="hidden" name="user_id" value="{{$requests_data->user_id}}">
			<input type="hidden" name="creater_id" value="{{$requests_data->creater_id}}">
            <input type="hidden" name="request_image" value="{{$requests_data->request_image}}"> {{ csrf_field() }}
        </form>

        @if(($requests_data->transporter_id < 3) || ( $requests_data->request_status == 2 ))
            <div class="col-xs-12 mt-3 pr-5 pl-4 text-md-right request-details-btns">
                <a href="#" class="btn  btn-custom shadow-sm" style="font-weight:400" onclick="event.preventDefault();
                document.getElementById('{{'request-edit-form-'.strval($requests_data->request_id)}}').submit();">Editar pedido</a>
                <a href="#" class="btn btn-custom shadow-sm" style="font-weight:400" onclick="event.preventDefault();
                document.getElementById('{{'request-delete-form-'.strval($requests_data->request_id)}}').submit();">Cancelar</a>
            </div>
        @endif

        <div class="col-xs-12 py-4">
            <div class="main-breadcrumb">
                Dashboard <i class="fa fa-angle-right"></i> Ordenes <i class="fa fa-angle-right"></i> Orden #{{$requests_data->request_id}}
            </div>
        </div>
    </div>

    <div class="col-md-12 request-detail-container">
        @if( in_array($requests_data->request_status, array(7, 9, 10, 13)) )
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

                $required_status = array(7, 9, 10, 13);
                $required_status_label = array(
                    '7' => 'Cargando',
                    '9' => 'En ruta',
                    '10' => 'En destino',
                    '13' => 'Completada'
                );

                $track = $requests_data->request_status_track;
                $hasTrack = $track != '';
                if($hasTrack) {
                    $track = json_decode($track, true);
                }
            @endphp
            <div class="request-detail-item text-center">
                <div class="d-block pb-4">
                    <h2 class="lead font-weight-bold mt-0 mb-0 text-uppercase">{{$required_status_label[$requests_data->request_status]}}</h2>
                    <small>{{$days_dias[date("l", strtotime($requests_data->update_date))]}} {{date('d/m/Y', strtotime($requests_data->update_date))}} a las {{date('h:i a', strtotime($requests_data->completed_date))}}</small>
                </div>
                <ul class="stepper">
                    <li class="stepper__item">
                        <span class="stepper__label">Solicitado</span>
                        <div class="stepper__datetime">
                            <span class="stepper__time">{{ date('h:i a', strtotime($requests_data->create_date)) }}</span><br>
                            <span class="stepper__date">{{ date('d/m/y', strtotime($requests_data->create_date)) }}</span>
                        </div>
                    </li>
                @if(is_array($track))
                @foreach($track as $track_item)
                        @if ( in_array($track_item['request_status'], $required_status) )
                            <li class="stepper__item">
                                <span class="stepper__label">{{ $required_status_label[$track_item['request_status']] }}</span>
                                <div class="stepper__datetime">
                                    <span class="stepper__time">{{ date('h:i a', strtotime($track_item['create_date'])) }}</span><br>
                                    <span class="stepper__date">{{ date('d/m/y', strtotime($track_item['create_date'])) }}</span>
                                </div>
                            </li>
                        @endif
                @endforeach
                @endif
                </ul>
                <style>
                .stepper {
                display: flex;
                flex-direction: column;
                padding-left: 0;
                list-style: none;
                max-width: 200px;
                margin-right: auto;
                margin-left: auto;
                }
                .stepper__label {
                text-transform: uppercase;
                font-size: 1.1rem;
                font-weight: bold;
                position: absolute;
                top: 0;
                left: 50px;
				width:100%;
                }
                .stepper__datetime {
                position: absolute;
                text-align: left;
                bottom: .5rem;
                left: 50px;
                }
                .stepper__time {
                font-size: 1.3rem;
                }
                .stepper__date {
                opacity: 0.5;
                }
                .stepper__item {
                display: inline-block;
                position: relative;
                text-align: center;
                height: 80px;
                }
                .stepper__item:not(:last-child)::after {
                content: '';
                display: block;
                position: absolute;
                top: 32px;
                left: 14px;
                width: 4px;
                height: 100%;
                background-color: #fec40e;
                }
                .stepper__item::before {
                content: '';
                --size: 2rem;
                width: var(--size);
                height: var(--size);
                background-color: #fec40e;
                border-radius: 50%;
                display: block;
                }

                @media screen and (min-width: 960px) {
                    .stepper {
                    flex-direction: row;
                    margin-top: 2rem;
                    margin-bottom: 4rem;
                    margin-right: 0;
                    margin-left: 0;
                    max-width: 100%;
                    }

                    .stepper__item {
                    flex: 1;
                    height: auto;
                    }
                    
                    .stepper__item::before {
                    margin: auto;
                    }

                    .stepper__item:not(:last-child)::after {
                    top: calc(50% - 2px);
                    left: 50%;
                    width: 100%;
                    height: 4px;
                    }

                    .stepper__label {
                    top: -2rem;
                    left: 50%;
                    transform: translateX(-50%);
                    }

                    .stepper__datetime {
                    text-align: center;
                    bottom: -3.8rem;
                    left: 50%;
                    transform: translateX(-50%);
                    }
                }
                </style>
            </div>
        @endif

        <div class="p-4">
            <h3 class="h4 mt-0 mb-3" style="font-weight:bold">Información de la Carga</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        @if($transporter != null && $transporter->company_name != '')
                            <div class="col-xs-6 col-md-6 mb-1">Empresa</div>
                            <div class="col-xs-6 col-md-6 mb-1"><b>{{$transporter->company_name}}</b></div>
                        @endif
                        <div class="col-xs-6 col-md-6 mb-1">Fecha de recojo</div>
                        <div class="col-xs-6 col-md-6 mb-1"><b>{{$requests_data->pickup_date}}</b></div>

                        <div class="col-xs-6 col-md-6 mb-1">Hora de recojo</div>
                        <div class="col-xs-6 col-md-6 mb-1"><b>{{$requests_data->pickup_time}}</b></div>
                        
                        @if($driver != null)
                            <div class="col-xs-6 col-md-6 mb-1">Conductor</div>
                            <div class="col-xs-6 col-md-6 mb-1"><b>{{$driver->first_name}} {{$driver->last_name}}</b></div>
                        @endif
                        @if($vehicle != null)
                            <div class="col-xs-6 col-md-6 mb-1">Placa</div>
                            <div class="col-xs-6 col-md-6 mb-1"><b>{{$vehicle->plate_no}}</b></div>
                        @endif

                        @if($driver == null || $vehicle == null)
                        <div class="col-xs-6 col-md-6 mb-1">Tipo de carga</div>
                        <div class="col-xs-6 col-md-6 mb-1">
							@if($loadtypes_details != null && $loadtypes_details->load_name != '')
								<b>{{ $loadtypes_details->load_name }}</b>
						    @endif
                        </div>
                        @endif
						<div class="col-xs-6 col-md-6 mb-1">Adicionales</div>	
						<div class="col-xs-6 col-md-6 mb-1"><b>{{ ($requests_data->additional != null) ? $requests_data->additional : 'N/A'}}</b></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        @if($driver != null && $vehicle != null)
                        <div class="col-xs-6 col-md-6 mb-1">Tipo de carga</div>
                        <div class="col-xs-6 col-md-6 mb-1">
							@if($loadtypes_details != null && $loadtypes_details->load_name != '')
								<b>{{ $loadtypes_details->load_name }}</b>
						    @endif
                        </div>
                        @endif

                        <div class="col-xs-6 col-md-6 mb-1">Peso</div>
                        <div class="col-xs-6 col-md-6 mb-1"><b>{{$requests_data->weight}} toneladas</b></div>
                        <div class="col-xs-6 col-md-6 mb-1">Dimensiones</div>
                        <div class="col-xs-6 col-md-6 mb-1"><b>{{$requests_data->size}} metros</b></div>
                        <div class="col-xs-6 col-md-6 mb-1">Distancia total</div>
                        <div class="col-xs-6 col-md-6 mb-1"><b>@if(is_numeric($requests_data->route_distance)) {{ round($requests_data->route_distance) }} @else {{ $requests_data->route_distance }} @endif</b></div>
                        <div class="col-xs-6 col-md-6 mb-1">Tipo de trailer</div>
                        <div class="col-xs-6 col-md-6 mb-1">
                            <div class="trailer-details">
                                <b><span class="trailer-name">{{$trailer_details->name}} </span></b>
                                <img class="trailer-image" src="{{url('../uploads/trailers/' . $trailer_details->image)}}">
                            </div>
                        </div>
					
						
                    </div>
                </div>
            </div>
			<div class="row">
				<div class="col-xs-3 col-md-3 mb-1">Instrucciones</div>
                <div class="col-xs-9 col-md-9 mb-1" style="padding-left: 5.4%;"><b>{{$requests_data->description}}</b></div>
			</div>
            <hr>
			
			@php
				date_default_timezone_set('America/Lima');
		
				$today = date("Y-m-d H:i:s"); 
				$expire = $requests_data->close_bid_time;	
				$today_dt = new DateTime($today); 
				
				if($expire != '') {
					$expire_dt = new DateTime($expire); 
				} else {
					$expire_dt = $today_dt;
				}
						
			@endphp
			
			
			@php if ($expire != '' && $expire_dt >= $today_dt ) : @endphp
			
            <div class="row" style="padding-bottom: 30px; padding-top: 30px;">
			    <div class="col-xs-4"></div>
                <div class="col-xs-4 center-block">
					<p style="text-align:center; line-height: 34px;">
						 Hasta ahora se han recibido 
						 <b>
						 {{count($get_bid_list_count)}} 
						 </b> Propuestas </br>
						Quedan </br>
						<b style="font-size: 30px;" id="idTimer"></b></br>
						para apertura de propuestas</br>
						
						<button class="btn  btn-custom shadow-sm" style="width: 50%;margin-top: 15px;" onclick="document.getElementById('id03').style.display='block';" >Ver precios ahora</button>
					</p>
				</div>
                <div class="col-xs-4"></div>
            </div>
			
			@php endif;  @endphp
			
        </div>
    </div>
    
    <div style="margin: 0 2rem 2rem">
        <div class="request-list row" style="paddingtop:0px;">  
            @if(!empty($get_bid_list))
                <div class="px-2">
                    <h3 class="h4" style="margin-top: 0; font-weight: bold;">
                        <span class="pill-number bg-secondary text-white mr-1">{{count($get_bid_list)}}</span> 
                        Cotizaciones
                    </h3>
                </div>
                <div class="request-detail-list">
                    @if(is_array($get_bid_list))
                    @foreach($get_bid_list as $bid)
                        <div class="request-detail-item px-2 {{ $bid->bid_status == 2 ? 'winner' : '' }}">
                            {{-- <pre> --}}
                            @php
                            // var_dump($bid);
                            @endphp
                            {{-- </pre> --}}
                            <div class="row">
                                <div class="col-xs-3 col-sm-4 col-md-2">
                                    @if(!empty($bid->main_image))
                                        <img src="{{url('../uploads/users/')}}/{{$bid->main_image}}" alt="" class="img-responsive" width="100" /> 
                                    @else
                                        <img src="{{asset('public/assets/bauenfreight/images/user-placeholder.gif')}}" alt="" class="img-responsive" width="100" />
                                    @endif
                                </div>
                                <div class="col-xs-8 col-md-4 d-flex v-center" style="flex-direction: column;">
                                    <p class="h4 mb-0">{{$bid->main_first_name}} {{$bid->main_last_name}} ({{$bid->main_company_name}})</p>
                                    <p class="mb-0"><b>S/ {{$bid->bid_amount}}</b> <small><i>No incluye IGV</i></small></p>
                                    @if ($bid->bid_status == 2)
                                        <small><i>Aceptado el {{date("d/m/Y h:i A",strtotime($bid->update_date))}}</i></small>
                                    @elseif ($requests_data->request_status > 1 && $requests_data->request_status < 8)
                                        <small><i>Enviado el {{date("d/m/Y h:i A",strtotime($bid->create_date))}}</i></small>
                                    @else
                                        <small><i>Enviado el {{date("d/m/Y h:i A",strtotime($bid->update_date))}}</i></small>
                                    @endif
                                </div>
                                <div class="col-xs-12 col-md-6 request-detail-item-btn-container">
                                    @if($requests_data->request_status == 0 || $requests_data->request_status == 1)
                                        <button type="button" class="btn btn-lg btn-custom" data-toggle="modal" data-target="#acceptBidModal" onclick="setAcceptBidUrl('{{url('api/request-accept-'.$requests_data->request_id.'-'.$bid->user_id.'-'.$bid->bid_id)}}')">
                                            Aceptar propuesta
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@endif
<div class="modal fade" id="acceptBidModal" tabindex="-1" role="dialog" aria-labelledby="acceptBidModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="row">
            <div class="col" style="text-align: center;">
              Por favor, confirme que desea aceptar este pedido
            </div>
          </div>
          <div class="row">
            <div class="col" style="margin-top:1rem; text-align: center;">
              <a href="#" id="acceptBidUrl" class="btn btn-custom">Aceptar</a>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection