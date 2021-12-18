@extends('../master_layout/web')

@section('custom_js')
<script>
  $( document ).ready(function() {
    
  if( navigator.userAgent.match(/Android/i)){
    
      $('.download-link').attr("href", "https://play.google.com/store/apps/developer?id=Bauen");
    }
  else if(navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)|| navigator.userAgent.match(/iPod/i)){

    $('.download-link').attr("href", "https://itunes.apple.com/us/developer/sergio-olcese/id1288677361?mt=8");
  }
 else {
   
    $('.download-link').attr("href", "http://www.bauenfreight.com");
  }

});
</script>
@endsection

@section('title')
<title>Bauen | ¿Quienes somos?</title>
@endsection

@section('banner')
  {{-- <div class="about-signup wow fadeInUp">
  	<div class="iphone wow fadeInLeft"></div>
  	<a href="/transporter/signup" class="btn-signup wow fadeInRight">Registrate<span>Ahora</span></a>
  </div> --}}
@endsection

@section('custom_css')
  <style>
    .text-primary {
      color: #fec40e !important;
    }

    .lead {
      font-weight: 100 !important;
      font-size: 24px !important;
      line-height: 1.3em;
    }

    @media screen and (min-width: 768px) {
      .lead {
        font-size: 30px !important;
      }
    }

    .py-section {
      padding-top: 3rem;
      padding-bottom: 1rem;
    }

    .bg-light {
      background-color: #f2f1ee;
    }

    .text-md {
      font-size: 20px;
      font-weight: 100;
    }

    .bg-linear {
      background: rgb(174,13,39);
      background: linear-gradient(90deg, rgba(174,13,39,1) 50%, rgba(254,196,14,1) 50%);
    }

    .bg-primary {
      background: rgba(174,13,39,1);
    }

    .bg-secondary {
      background: rgba(254,196,14,1);
    }

    .btn-primary {
      background-color: #fec40e;
      color: #050505 !important;
      border-color: #fec40e !important;
    }

    .btn-primary:hover {
      color: #050505 !important;
      background-color: #f1b805;
    }

    .btn-secondary {
      background-color: #ae0d27;
      border-color: #ae0d27;
      color: #fff !important;
    }

    .btn-secondary:hover {
      background-color: #880c20;
    }

    .btn-light:hover {
      background-color: #1f1f1f;
    }

    .mb-3 {
      margin-bottom: 2rem;
    }

    @media screen and (min-width: 768px) {
      .mb-md-0 {
        margin-bottom: 0 !important;
      }
    }
    .page-header {
    position: relative;
    padding-top: 30px;
    padding-bottom: 10px;
    margin: 0 !important;
    border-bottom: 0;
  }

  @media screen and (min-width: 768px) {
    .page-header {
      padding-top: 100px;
      padding-bottom: 100px;
    }
  }

  .page-header__container {
    position: relative;
    z-index: 2;
  }

  .page-header__copy {
    font-weight: 600 !important;
    font-weight: 500 !important;
    margin-bottom: 2rem;
    color: #fff;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
  }

  .page-header__author {
    font-weight: 500 !important;
    color: #fff;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
  }

  .page-header__img {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.9;
  }
  </style>
@endsection

@section('main_container')
  <div class="page-header">
    <div class="container page-header__container how-it-works">
      <p class="lead text-center page-header__copy">La tecnología puede impulsar la eficiencia del transporte generando ahorro; mejores tiempos de respuesta, transparencia, trazabilidad de la carga, información a la mano, estimaciones de tiempo, evaluaciones de desempeño y mucho más.</p>
      
      <p class="text-center text-md page-header__author">Sergio Olcese - Bauen | CEO & Founder</p>
    </div>
    <img class="page-header__img" src="{{asset('public/assets/bauenfreight/images/img-about.jpg')}}">
  </div>

  <div class="bg-light py-section  wow slideInUp">
    <div class="container">
      <div class="col-md-6">
          <h2 class="h2" style="margin-top:0;margin-bottom:1rem;">Tecnología para el Transporte de Carga</h2>
          <p class="text-md">Bauen es una herramienta para transportar de manera rápida, transparente, eficiente y segura. Con cada carga, estamos ayudando a los transportistas y dadores de carga a construir una mejor manera de trasladar cosas. Buscamos aumentar la eficiencia del transporte con el uso de tecnología e ir transmitiendo los beneficios que derivan de ello hasta los clientes.</p>
          <a href="{{url('how-it-works')}}" class="btn btn-lg btn-custom mb-3 mb-md-0" style="margin-left:0;margin-top:1rem;">COMO FUNCIONA</a>
      </div>
      <div class="col-md-6">
        <div class="embed-responsive embed-responsive-16by9 hoverable">
          <iframe class="embed-responsive-item" src=" https://www.youtube.com/embed/O7oMjN13Pgg?rel=0" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-primary py-section hidden-md hidden-lg hidden-xl  wow slideInUp">
    <div class="container">
      <div class="col-xs-12 text-center" style="color:#fff;">
        <i class="glyphicon glyphicon-upload" style="font-size:4rem;"></i>
        <h2 class="h2" style="margin-top:0;margin-bottom:1rem;">CLIENTE</h2>
        <p class="text-md">Solicita propuestas de transporte con precios por adelantado</p>
        <a href="#" class="download-link btn btn-lg btn-primary" style="margin-left:0;margin-top:1rem;">FREE TRIAL</a>
      </div>
    </div>
  </div>
  <div class="bg-secondary py-section hidden-md hidden-lg hidden-xl  wow slideInUp">
    <div class="container">
      <div class="col-xs-12 text-center" style="color:#fff;">
        <i class="glyphicon glyphicon-download" style="font-size:4rem;"></i>
        <h2 class="h2" style="margin-top:0;margin-bottom:1rem;">TRANSPORTISTA</h2>
        <p class="text-md">Consigue la carga que necesitas desde tu móbil y con tus propios precios</p>
        <a href="#" class="download-link btn btn-lg btn-secondary" style="margin-left:0;margin-top:1rem;">REGISTRO</a>
      </div>
    </div>
  </div>

  <div class="bg-linear py-section hidden visible-md visible-lg visible-xl  wow slideInUp">
    <div class="container">
      <div class="col-md-4 col-md-offset-1 text-center" style="color:#fff;">
          <i class="glyphicon glyphicon-upload" style="font-size: 4rem;"></i>
          <h2 class="h2" style="margin-top:0;margin-bottom:1rem;">CLIENTE</h2>
          <p class="text-md">Solicita propuestas de transporte con precios por adelantado</p>
          <a href="#" class="download-link btn btn-lg btn-custom btn-primary" style="margin-left:0;margin-top:1rem;">FREE TRIAL</a>
        </div>
        <div class="col-md-4 col-md-offset-2 text-center">
          <i class="glyphicon glyphicon-download" style="font-size: 4rem;"></i>
          <h2 class="h2" style="margin-top:0;margin-bottom:1rem;">TRANSPORTISTA</h2>
          <p class="text-md">Consigue la carga que necesitas desde tu móbil y con tus propios precios</p>
          <a href="#" class="download-link btn btn-lg btn-custom btn-secondary" style="margin-left:0;margin-top:1rem;">REGISTRO</a>
      </div>
    </div>
  </div>
@endsection