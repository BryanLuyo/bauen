@extends('../master_layout/web')

@section('custom_js')
@endsection




@section('title')
<title>Bauen | Home | Conectando cargas con transportistas homologados - Bauen Freight SAC - Transporte de Carga, Fletes, Carga de Pago</title>
@endsection



@section('banner')
<section class="banner">
     <div class="owl-carousel banner-carousel">
        <div class="item" style="background-image:url('{{asset('public/assets/bauenfreight/images/banner1.jpg')}}');">  
        <div class="container wow fadeIn"> <div class="ban-cont ">
        <h4>{{trans('pdg.27') }}</h4>
        <div class="cont">
            {{trans('pdg.28')}}
        </div>
<div class="btn-row">
    <a href="{{url('shipper')}}">{{trans('pdg.4')}}</a>
    <a href="{{url('signup')}}">{{trans('pdg.5')}}</a>
</div>
        </div></div>
              </div>
        <div class="item" style="background-image:url('{{asset('public/assets/bauenfreight/images/banner2.png')}}');"><div class="container wow fadeIn"> <div class="ban-cont ">
        <h4>{{trans('pdg.27') }}</h4>
        <div class="cont"> 
            {{trans('pdg.28')}}
        </div>
<div class="btn-row">
    <a class="wow fadeInUp" href="{{url('shipper')}}">{{trans('pdg.4')}}</a>
    <a class="wow fadeInUp" href="{{url('signup')}}">{{trans('pdg.5')}}</a>
</div>
        </div></div> </div>
    </div>
 </section>
@endsection

@section('main_container')

  <div class="services">
  <div class="container">
  <div class="col-sm-3 text-center wow fadeInUp" data-wow-delay=".1s">
  <div class="icon"><img src="{{asset('public/assets/bauenfreight/images/s1.png')}}" alt=""></div>
  <h5>{{trans('pdg.29')}}</h5>
  <p>
    {{trans('pdg.30')}}
  </p>
  </div>
  <div class="col-sm-3 text-center  wow fadeInUp" data-wow-delay=".2s">
  <div class="icon"><img src="{{asset('public/assets/bauenfreight/images/s2.png')}}" alt=""></div>
  <h5> {{trans('pdg.31')}}</h5>
  <p> {{trans('pdg.32')}}</p>
  </div>
  <div class="col-sm-3 text-center  wow fadeInUp" data-wow-delay=".3s"> <div class="icon"><img src="{{asset('public/assets/bauenfreight/images/s3.png')}}" alt=""></div>
  <h5>{{trans('pdg.33')}}</h5>
   <p>
       {{trans('pdg.34')}}
   </p>
  </div>
  <div class="col-sm-3 text-center  wow fadeInUp" data-wow-delay=".4s"><div class="icon"><img src="{{asset('public/assets/bauenfreight/images/s4.png')}}" alt=""></div>
  <h5>{{trans('pdg.35')}} </h5>
  <p>
     {{trans('pdg.36')}} 
  </p>
  </div>
  </div>
  </div>
  
  <div class="aboutus">
  <div class="container wow fadeInUp" data-wow-delay=".2s">
  <h5>{{trans('pdg.37')}} </h5>
  <p>
      {{trans('pdg.38')}} 
  </p>
<a href="." class="btn btn-quote">{{trans('pdg.39')}} </a>  </div>
  </div>
  
  <div class="features">
  <div class="container wow fadeIn">
  <div class="wow fadeInRight">
  <ul>
  <li>
  <h6>{{trans('pdg.40')}}</h6>
  <p>Bauen is free to download.</p>
</li>
<li>
  <h6>{{trans('pdg.42')}}</h6>
  <p>Bauen matches you with Jobs on the lanes you like</p>
</li>
<li>
  <h6>{{trans('pdg.44')}}</h6>
  <p>
      {{trans('pdg.41')}}
  </p>
</li>
<li>
  <h6>IMPROVE EFFICIENCY</h6>
  <p>Less miles driven without loads improve efficiency.</p>
</li>

<li>
  <h6>{{trans('pdg.45')}}</h6>
  <p>{{trans('pdg.43')}}</p>
</li>
  </ul>
  </div>
  </div>
  </div>
  
  <div class="app-benefits">
  <div class="container">
  <h5 class="wow fadeInDown">{{trans('pdg.46')}}</h5>
  <div class="benefits-table">
  <div class="col-left wow fadeInLeft">
  <ul>
  <li>{{trans('pdg.47')}}<img src="{{asset('public/assets/bauenfreight/images/tick.png')}}"></li>
  <li> {{trans('pdg.48')}} <img src="{{asset('public/assets/bauenfreight/images/tick.png')}}"></li>
  </ul>
  </div>
  <div class="col-center wow fadeInUp"><img src="{{asset('public/assets/bauenfreight/images/img-benefit.png')}}"></div>
  <div class="col-right wow fadeInRight">
   <ul>
  <li><img src="{{asset('public/assets/bauenfreight/images/tick.png')}}"> {{trans('pdg.49')}}</li>
  <li><img src="{{asset('public/assets/bauenfreight/images/tick.png')}}"> {{trans('pdg.50')}}</li>
  </ul>
  </div>
  </div>
  </div>
  </div>
  
  
  <div class="shippers-carriers">
  <div class="container">
  <a class="btn-get-free-logistics wow fadeInUp"><strong>{{trans('pdg.51')}}</strong></a>
  
  <div class="row">
  <div class="col-sm-6">
  <div class="box-shipper wow fadeInLeft">
  <h5>{{trans('pdg.4')}}</h5>
  <div class="box-content">
  <div class="options">
  <ul>
  <li><img src="{{asset('public/assets/bauenfreight/images/icon-shipper1.png')}}"> {{trans('pdg.52')}}</li>

<li><img src="{{asset('public/assets/bauenfreight/images/icon-shipper2.png')}}"> {{trans('pdg.53')}} </li>

<li><img src="{{asset('public/assets/bauenfreight/images/icon-shipper3.png')}}">  Information</li>
</ul>
</div>
<div class="shippers-txt">"Bauen atiende las necesidades de sus clientes para asegurar un buen servicio y clientes satisfechos."
<div class="by"><div class="l">Sergio Olcese<br>

CEO  &nbsp - &nbsp

Bauen Freight</div><div  class="r"> <img src="{{asset('public/assets/bauenfreight/images/logo.png')}}"> </div>  </div>
</div>
  </div>
  </div>
  </div>
  <div class="col-sm-6">
  <div class="box-carriers wow fadeInRight">
  <h5>{{trans('pdg.5')}}</h5>
  <div class="box-content">
  <div class="options">
  <ul>
  <li><img src="{{asset('public/assets/bauenfreight/images/icon-career1.png')}}"> {{trans('pdg.54')}}</li>

<li><img src="{{asset('public/assets/bauenfreight/images/icon-career2.png')}}"> {{trans('pdg.55')}}  </li>

<li><img src="{{asset('public/assets/bauenfreight/images/icon-career3.png')}}"> {{trans('pdg.56')}}</li>
</ul>
</div>
<div class="shippers-txt">"Bauen esta trabajando para desarrollar tecnolog??a que resulta ??til para la industria de transporte."
<div class="by"><div class="l">Pedro Gutierrez<br>

Jefe de Transportes</div><div  class="r"> <img src="{{asset('public/assets/bauenfreight/images/05_logo.png')}}"> </div> </div>
</div>
  </div>
  </div>
  </div>
  <div></div>
  </div>
  </div>
  </div>
  
<!--  <div class="services-type">
<div class="container">
<img src="{{asset('public/assets/bauenfreight/images/img-home-footer.png')}}"  alt="" class="img-responsive"/>
</div>
</div>-->
@endsection