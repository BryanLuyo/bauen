@extends('../master_layout/web_shipper')
@section('custom_css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
<style>
@media(max-width: 800px){
    #pickup_date,#pickup_time{
        font-size:0.95rem;
    }
}

.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fff!important;
}

input[readOnly] {
    background: white !important;
}
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}
input[type=number] {
    -moz-appearance:textfield;
}
.size-input {
    padding-right:0 !important;
    text-align: center;
    flex: 1 0 auto;
    width: 20px !important;
    word-wrap: break-word;
}

.size-input--long {
    border-radius: 0 !important;
    border-right: 0;
}
.size-input--wide {
    border-right: 0;
}

.size-input__separator {
    display: flex;
    align-items:center;
    border-top: 1px solid rgb(204, 204, 204);
    border-bottom: 1px solid rgb(204, 204, 204);
}

.pac-container {
    z-index: 8;
    margin-top: -91px;
}
</style>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/bauenfreight/my_custom_js.js?v=1.0.2')}}"></script>
{{-- <script src="{{asset('public/assets/bauenfreight/bootstrap-datepicker.js')}}?v=1.0.1"></script> --}}
{{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}

<!-- autocomplet -->
<!-- getmap -->

<script>
    // The following example creates a marker in Stockholm, Sweden using a DROP
// animation. Clicking on the marker will toggle the animation between a BOUNCE
// animation and no animation.

document.getElementById("submit_your_request").disabled = false;

var map, marker, infoWindow, geocoder, vari;


var pickupInputElement = document.getElementById('pickup_location');
var dropInputElement = document.getElementById('dropoff_location');
var pidoInputElement = document.getElementById('pido');

// var de entrada de autocompletado

var pickup_autocomplete,drop_autocomplete;
var lastPickupPlace = null, lastDropoffPlace=null;
var IsPickupPlaceChange;
var IsDropoffPlaceChange;

// funcion de popup mapa google

function initMap(varia) {
  
  document.getElementById('id01').style.display='block'
    
  map = new google.maps.Map(document.getElementById('mapa'), {
    zoom: 15,
    center: {
      lat: -12.045852,  
      lng: -77.030515
    }
  });

  marker = new google.maps.Marker({
    map: map,
    draggable: true,
    animation: google.maps.Animation.DROP,
    position: {
     lat: -12.045852,  
     lng: -77.030515
    }
  });

  infoWindow = new google.maps.InfoWindow;

  geocoder = new google.maps.Geocoder; 

  vari = varia;

  //var pos;


  //console.log(vari);

  // si el text esta lleno 
  // de lo contrario Try HTML5 geolocation.


       /* if(pickupInputElement.value != '' || dropInputElement.value != ''){ 
           if(varia == 2){

                  pos = {
                    lat: document.getElementById("pick_lat").value,
                    lng: document.getElementById("pick_long").value
                  };
           } 
           else{
           }

       } else */ 

       if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            
            geocodeLatLng(pos,1);

          }, function() {
            handleLocationError(true, marker, map.getCenter());
          });
        } else {
          // Browser doesn't support Geolocation
          handleLocationError(false, marker, map.getCenter());
        }


  //marker.addListener('click', toggleBounce);
  marker.addListener('dragend', setDataMap) ;
  marker.addListener('dblclick',aceptarDireccion);
  //map.addListener('dragend', setDataMap1);
  //map.addListener('center_changed', setDataMap2);  

}

function  setDataMap2() {
    infoWindow.close();
    window.setTimeout(function() {
                  var center = map.getCenter();
                  marker.setPosition(center);
    }, 100)
}    

function  setDataMap1() {
   setDataMap();
}

function  setDataMap() {
    pos = posicionActual();
    geocodeLatLng(pos,1);
}


function posicionActual() {

  var longitud = marker.getPosition().lng();
  var latitud  = marker.getPosition().lat(); 
  var pos = {lat: latitud, lng:longitud}; 
 
  return pos;

}

function aceptarDireccion() {
  var pos = posicionActual();
  var geocodeLL = geocodeLatLng(pos);

  console.log(vari);

  geocodeLatLng(pos,vari);
}


/* function toggleBounce() {
  if (marker.getAnimation() !== null) {
    marker.setAnimation(null);

    var longitud = marker.getPosition().lng();
    var latitud  = marker.getPosition().lat();

    console.log(latitud+'/'+longitud);

    //geocodeLatLng(latitud, longitud);

  } else {
    marker.setAnimation(google.maps.Animation.BOUNCE);
  }
} */

function handleLocationError(browserHasGeolocation, marker, pos) {
marker.setPosition(pos);
console.log(browserHasGeolocation ?
                      'Error: The Geolocation service failed.' :
                      'Error: Your browser doesn\'t support geolocation.');
//marker.open(map);
    }    

function geocodeLatLng(pos, validacion) {

        geocoder.geocode({'location': pos}, function(results, status) {
          if (status === 'OK') {
            if (results[0]) {

                switch (validacion) {

                    case 1:

                        marker.setPosition(pos);
                        map.setCenter(pos);
                        //infoWindow.setContent(results[0].formatted_address);
                        //infoWindow.open(map, marker);
                        pidoInputElement.value= results[0].formatted_address;
                        
                    break;

                    case 2:
                        
                        pickupInputElement.value= results[0].formatted_address;
                        document.getElementById('id01').style.display='none';

                        document.getElementById("pick_long").value = "";
                        document.getElementById("pick_lat").value = "";
                        document.getElementById("pick_place_id").value = "";
                        //var place = pickup_autocomplete.getPlace();
                        document.getElementById("pick_long").value = pos.lng;
                        document.getElementById("pick_lat").value = pos.lat;
                        document.getElementById("pick_place_id").value = results[0].place_id;

                    break;

                    case 3:

                        dropInputElement.value = results[0].formatted_address;
                        document.getElementById('id01').style.display='none';

                        document.getElementById("drop_long").value = "";
                        document.getElementById("drop_lat").value = "";
                        document.getElementById("drop_place_id").value = "";
                        //var place = drop_autocomplete.getPlace();
                        document.getElementById("drop_long").value = pos.lng;
                        document.getElementById("drop_lat").value = pos.lat;
                        document.getElementById("drop_place_id").value = results[0].place_id; 


                    break;  
                    
                    /*    
                    default:
                    break; */  

                }

                

            } else {
              window.alert('No results found');
            }
          } else {
            window.alert('Geocoder failed due to: ' + status);
          }
        });
 }

//  funcion de entrada de autocompletado

function initAutocomplete() {

        var pickupInputElement = document.getElementById('pickup_location');
        var dropInputElement = document.getElementById('dropoff_location');

        pickup_autocomplete = new google.maps.places.Autocomplete((document.getElementById('pickup_location')),{types: []});
        pickup_autocomplete.addListener('place_changed', pickup_fillInAddress);
        drop_autocomplete = new google.maps.places.Autocomplete((document.getElementById('dropoff_location')),{types: []});
        drop_autocomplete.addListener('place_changed', drop_fillInAddress);

        document.addEventListener('click', function(event) {
            var isClickInsidePickup = pickupInputElement.contains(event.target);
            var isClickInsideDropoff = dropInputElement.contains(event.target);

            if (!isClickInsidePickup) {
              if (IsPickupPlaceChange == false) {
                  if (lastPickupPlace==null || pickupInputElement.value== ""){
                    pickupInputElement.value= "";
                    lastPickupPlace = null;
                  }
                  else{
                    pickupInputElement.value= lastPickupPlace.formatted_address;
                  }
              }
            }

            if (!isClickInsideDropoff) {
              if (IsDropoffPlaceChange == false) {
                  if (lastDropoffPlace==null || dropInputElement.value== ""){
                    dropInputElement.value= "";
                    lastDropoffPlace=null;
                  }
                  else{
                    dropInputElement.value= lastDropoffPlace.formatted_address;
                  }
              }
            }
        });
    }


    $("#pickup_location").keydown(function () {
          IsPickupPlaceChange = false;
      });

    $("#dropoff_location").keydown(function () {
        IsDropoffPlaceChange = false;
    });

    function pickup_fillInAddress() {
        document.getElementById("pick_long").value = "";
        document.getElementById("pick_lat").value = "";
        document.getElementById("pick_place_id").value = "";
        var place = pickup_autocomplete.getPlace();
        document.getElementById("pick_long").value = place.geometry.location.lng();
        document.getElementById("pick_lat").value = place.geometry.location.lat();
        document.getElementById("pick_place_id").value = place.place_id;
        lastPickupPlace = place;
        IsPickupPlaceChange = true;
        }

   function drop_fillInAddress() {
    document.getElementById("drop_long").value = "";
    document.getElementById("drop_lat").value = "";
    document.getElementById("drop_place_id").value = "";
    var place = drop_autocomplete.getPlace();
    document.getElementById("drop_long").value = place.geometry.location.lng();
    document.getElementById("drop_lat").value = place.geometry.location.lat();
    document.getElementById("drop_place_id").value = place.place_id;
    lastDropoffPlace = place;
    IsDropoffPlaceChange = true;
   }


//  fin funcion de entrada de autocompletado

</script>

<!-- fin getmap -->

<script>
    const $longInput = $('#long'),
          $wideInput = $('#wide'),
          $heighInput = $('#heigh'),
          $sizeInput = $('#size');

    const updateSizeInput = () => {
        let newSizeVal = '';
        if($longInput.val() != '') newSizeVal += $longInput.val()
        if($wideInput.val() != '') newSizeVal += 'x' + $wideInput.val()
        if($heighInput.val() != '') newSizeVal += 'x' + $heighInput.val()

        $sizeInput.val(newSizeVal)
    }

    $('#long, #wide, #heigh').keyup(() => {
        updateSizeInput()
    })


    let dimensionesFlag = 0;
       document.getElementById('fake-dimensiones').addEventListener('focus', function() {
           if(dimensionesFlag === 1) return
           dimensionesFlag = 1

           this.style.display = 'none'
           document.getElementById('real-dimensiones').style.display = 'flex'
           document.getElementById('long').focus()
       })
    
    // Desplegar listado de vehiculos
    const vehiclesDropdown = document.querySelector('.vehicles-dropdown')
    const vehiclesDropdownOptions = document.querySelectorAll('.vehicles-dropdown__item')
    const selectTrailer = document.querySelector('#trailer_id')

    // const closeVehiclesDropdown = (e) => {
    //     if(!e.target.classList.contains('vehicles-dropdown__item')) {
    //         console.log('quitar clase')
    //         vehiclesDropdown.classList.remove('show')
    //     } else console.log('no quites clase')
    // }

    selectTrailer.addEventListener('click', function(e) {
        e.preventDefault()
        vehiclesDropdown.classList.toggle('show')
        // if(vehiclesDropdown.classList.contains('show')) {
        //     vehiclesDropdown.classList.remove('show')
        //     window.removeEventListener('click', closeVehiclesDropdown)
        // } else {
        //     vehiclesDropdown.classList.add('show')
        //     window.addEventListener('click', closeVehiclesDropdown)
        // }
    })

    if(vehiclesDropdownOptions.length > 0) {
        vehiclesDropdownOptions.forEach(item => {
            item.addEventListener('click', function(e) {
                const clickedTrailerId = e.target.getAttribute('data-trailer-id')
                console.log('clickedTrailerId', clickedTrailerId)
                selectTrailer.value = clickedTrailerId

                vehiclesDropdown.classList.remove('show')
            })
        })
    }
</script>
    
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWUpWPdSO8timCKKTYLVL-FFpzwmuEVhQ&libraries=places&callback=initAutocomplete">
</script>



@endsection

@section('title')
<title>Bauen | Cotizar</title>
@endsection

@section('banner')
@endsection

@section('main_container')

<!--modal mapa -->
<div id="id01" class="w3-modal">
    <div class="w3-modal-content">
      <div style="padding:0px;" class="w3-container">

        <span onclick="document.getElementById('id01').style.display='none'"  style="right: 75px; z-index: 1;background-color: #a9a9a94d;" class="w3-button w3-display-topright">&times;</span>

                <div style="width: 60%;margin-left: 20%;margin-top: 7%; z-index: 111;position: absolute;" class="form-group form-group-lg">
                             <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon  glyphicon-map-marker"></span>
                        </span>
                        <input type="text" style="border-right: 0px;" class="form-control" name="pido" id="pido"  placeholder="" readonly />
                        <!-- <span class="input-group-addon">
                            <a onclick="initMap(2)"><img style="height:70%;cursor:pointer;" src="{{asset('public/assets/bauenfreight/images/icon-map.png')}}" alt=""/></a>
                        </span> -->
                        <span class="input-group-btn">
                        <button class="btn btn-lg btn-warning" type="button" onclick="aceptarDireccion()" >Confirmar</button>
                      </span>
                    </div>
                </div>
         <div id="mapa" style="height:500px;"></div>
      </div>
    </div>
</div>

<!-- modal cierre de flete -->

<!-- <button onclick="document.getElementById('id02').style.display='block'" class="w3-button w3-black">Open Modal</button> -->




<div id="id02" class="w3-modal">
    <div class="w3-modal-content" style="width: 450px; padding-top: 20px; padding-bottom: 20px;">
      <div class="w3-container">
       <button type="button" onclick="document.getElementById('id02').style.display='none'" class="w3-button w3-display-topright" style="font-size: 25px !important;font-weight: bold !important;padding: 0px 10px !important;background-color: #e9e9ed !important ">×</button>
       <!-- <span  >&times;</span> -->
       <form>
              <div class="form-group form-group-lg">
                <p class="text-center" id="idText1"><label style="color: #000000bf;">¿Desea fijar fecha de cierre para ver los fletes?</label></p>
                <p class="text-center" id="idText2">Esta funcionalidad permite visualizar las propuestas a partir de una fecha limite designada por usted</p>
                <p class="text-center" style="display:none" id="idText3"><label style="color: #000000bf;">No podrán ver los precios hasta este momento:</label></p>
                
                <div class="input-group center-block" style="width: 60%;" >
                <select class="form-control border-left-square" id="time_id" name="time_id" style="display:none; margin-bottom: 15px;" onchange="cambiaTime()">
                          <option value="1" >1 hora</option>
                          <option value="8" >8 horas</option>
                          <option value="24" selected >24 horas</option>
                          <option value="48" >48 horas</option>
                          <option value="168" >1 semana</option>
                          <option value="0" >custom</option>
                </select>
        
                <!-- <input type="number"  placeholder="ingrese hora" style="display:none"> -->
                <div id="displayValue" style="display:none; margin-bottom: 15px;">
                    <input type="number" class="form-control" placeholder="ingrese hora" id="ingresaNum" style="width:80%;" onkeyup="cambioTextNum()" >
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" style="height:46px;" onclick="cerrarme()" >x</button>
                    </span>
                </div>
                
                <div  style="display:none; margin-left: 12%; margin-bottom: 5%;" id = "rFC">
                    <div class="step completed">
                          <div class="v-stepper">
                            <div class="circle"></div>
                            <div class="line"></div>
                          </div>
                          <div class="content1">
                                <span class="stepper__time" id="fh">18/09/20 a las 05:10 pm</span>
                          </div>
                    </div>
                    <div class="step">
                      <div class="v-stepper">
                        <div class="circle"></div>
                        <div class="line"></div>
                      </div>
                          <div class="content1">
                            <span class="stepper__time" id="fhc">18/09/20 a las 06:10</span>
                          </div>
                     </div>
                </div>
                <button class="btn btn-default" style="width: 40%; float: left;" id = "btnFC" >Sí</button> 
                <button class="btn btn-warning pull-right" style="width: 50%;" id = "btnFCN">Cotizar ahora</button>
                <button class="btn btn-warning center-block" style="width: 40%; display:none; " id = "btnC" >Cotizar</button>

              </div>
            </div>
       </form>
      </div>
    </div>
</div>
  
<div class="right-body">
    @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
    @endif
    <div class="request-quote">
        <div class="row">
            <!--  <div class="col-xs-12 pb-4">
                <div class="main-breadcrumb">
                    Dashboard <i class="fa fa-angle-right"></i> Crear Cotización
                </div>
            </div> -->

            <div class="col-xs-12 col-sm-9 col-md-8">
                <form id="your_request" name="your_request" action="{{url('post-your-request')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group form-group-lg">
                        <label for="pickup_location">Origen y Destino</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon  glyphicon-map-marker"></span>
                            </span>
                            <input type="text" style="border-right: 0px;" class="form-control" name="pickup_location" id="pickup_location" placeholder="Ingrese el punto de origen. Si no aparece la dirección presione el mapa a la derecha"/>
                            <span class="input-group-addon">
                                <a onclick="initMap(2)"><img style="height:70%;cursor:pointer;" src="{{asset('public/assets/bauenfreight/images/icon-map.png')}}" alt=""/></a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group form-group-lg">
                        {{-- <label for="dropoff_location">Punto de Destino</label> --}}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon  glyphicon-flag"></span>
                            </span>
                            <input type="text" style="border-right: 0px;" class="form-control" name="dropoff_location" id="dropoff_location"  placeholder="Ingrese el punto de destino. Si no aparece la dirección presione el mapa a la derecha"/>
                            <span class="input-group-addon">
                                <a onclick="initMap(3)"><img style="height:70%;cursor:pointer;" src="{{asset('public/assets/bauenfreight/images/icon-map.png')}}" alt=""/></a>
                            </span>
                        </div>
                    </div>
                    <input type="hidden" id="pick_long" name="pick_long" value="">
                    <input type="hidden" id="pick_lat" name="pick_lat" value="">
                    <input type="hidden" name="pick_place_id" id="pick_place_id" value="" >
                    <input type="hidden" id="drop_long" name="drop_long" value="">
                    <input type="hidden" id="drop_lat" name="drop_lat" value="">
                    <input type="hidden" name="drop_place_id" id="drop_place_id" value="" >
                    <input type="hidden" name="close_time" id="close_time" value="" >
                    
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group form-group-lg">
                                <label for="pickup_date">Fecha</label>
                                <div class="input-group date" style="position:relative">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input type="text" name="pickup_date" placeholder="Fecha de recojo de la Carga" class="form-control" id="pickup_date" readonly="true" />
                                     
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-6">
                            <label for="pickup_time">Hora</label>
                            
                            <div class="form-group form-group-lg">
                                <div class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span> 
                                   <input type="text" name="pickup_time" id="pickup_time" class="form-control timepicker"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-lg">
                        <label for="trailer_id">{{trans('pdg.88')}}</label>
                        <div class="input-group">
                            @if(!empty($trailers_id))
                            <div class="vehicles-dropdown">
                                    @foreach($trailers_id as $cat)
                                        <div class="vehicles-dropdown__item" data-trailer-id="{{$cat->trailer_id}}">
                                            <div class="vehicles-dropdown__image"><img src="../uploads/trailers/{{$cat->image}}"></div>
                                            <span>{{$cat->name}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <span class="input-group-addon">
                                <i class="fa fa-indent  fa-truck"></i>
                                <!--<img src="{{asset('public/assets/bauenfreight/images/icon-triler-type.png')}}">-->
                            </span>
                            <select class="form-control border-left-square" id="trailer_id" name="trailer_id" onmousedown="(function(e){ e.preventDefault(); })(event, this)">
                                <option value="0" selected disabled>Seleccionar vehículo requerido</option>
                                @if(!empty($trailers_id))
                                    @foreach($trailers_id as $cat)
                                        <option value="{{$cat->trailer_id}}">{{$cat->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    
                    <div class="form-group form-group-lg">
                        <label for="loadtype_id">{{trans('pdg.105')}}</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                    <i class="fa fa-indent  fa-forklift"></i>
                                    <!--<img src="{{asset('public/assets/bauenfreight/images/icon-load-type.png')}}">-->
                            </span>
                            <select class="form-control border-left-square" id="loadtype_id" name="loadtype_id">
                                <option value="0" selected disabled>Seleccionar tipo de carga</option>
                                    @if(!empty($loadtypes_id))
                                        @foreach($loadtypes_id as $lt)
                                            <option value="{{$lt->loadtype_id}}">{{$lt->load_name}}</option>
                                        @endforeach
                                    @endif
                            </select>
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        {{-- <div class="col-xs-12">
                            <div class="form-group form-group-lg">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input type="number" class="form-control" name="request_amount" id="request_amount"  placeholder="Valor de la carga"/>
                                </div>
                            </div>
                        </div> --}}
                        
                        <div class="col-sm-6">
                            <div class="form-group form-group-lg">
                                <label for="weight">{{trans('pdg.89')}} (en toneladas)</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-open"></span>
                                    </span>
                                    <input type="number" class="form-control" name="weight" id="weight"  placeholder="Peso de la carga"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-group-lg">
                                <label for="long">Dimensiones (en metros)</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-stop"></span>
                                    </span>
                                    <input type="text" id="fake-dimensiones" class="form-control" placeholder="Dimensiones">
                                    <div style="display:none;" id="real-dimensiones">
                                        <input type="number" placeholder="Largo" id="long" class="form-control size-input size-input--long">
                                        <span class="size-input__separator">x</span>
                                        <input type="number" placeholder="Ancho" id="wide" class="form-control size-input size-input--wide">
                                        <span class="size-input__separator">x</span>
                                        <input type="number" placeholder="Alto" id="heigh" class="form-control size-input size-input--heigh">
                                    </div>
                                    <input type="hidden" name="size" id="size">
                                    {{-- <input type="number" class="form-control" name="size" id="size"  placeholder="{{trans('pdg.90')}}"/> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-lg">
                        <label for="description">Descripción e instrucciones de la carga</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-align-left"></span>
                            </span>
                            <textarea class="form-control" row="4" name="description" id="description" placeholder="Detalla lo que deseas transportar e ingresa cualquier información que pueda ser útil para nuestros expertos logísticos"></textarea>
                        </div>
                    </div>

					<div class="form-group form-group-lg">
                        <label for="description">Adjuntar</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-align-left"></span>
                            </span>
                            <textarea class="form-control" row="4" name="description" id="description" placeholder="Detalla lo que deseas transportar e ingresa cualquier información que pueda ser útil para nuestros expertos logísticos"></textarea>
                        </div>
                    </div>



                    {{-- <div class="form-group">
                        <label class="error">Campos obligatorios (*)</label>
                    </div> --}}
                    <div class="form-group ">
                        <button type="button" id="submit_your_request" class="btn btn-primary cta-primary btn-block raq-button btn-lg" style="text-transform: uppercase">{{trans('pdg.92')}}</button>
                    </div>

                </form>
            </div>

            <div class="col-sm-4 col-xs-12 ">
                <div class="benefits pull-right" style="border-radius:.3rem">
                    <p class="mb-3">Los transportistas verán tu requerimento y enviarán sus cotizaciones.</p>
                    <p class="mb-4">Tú decides, teniendo precios por adelantado.</p>
                    <ul>
                        <li class="benefits-one">1 | Cotizar</li>
                        <li class="benefits-two">2 | Cotizaciones en curso</li>
                        <li class="benefits-three">3 | En tránsito</li>
                        <li class="benefits-four">4 | Completadas</li>
                    </ul>
                    <p>Recomendamos usar el app disponible en iOS y Android. Cualquier duda o consulta ponerse en contacto a:<br><a href="mailto:sergio@bauenfreight.com" class="text-primary font-weight-bold">sergio@bauenfreight.com</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
