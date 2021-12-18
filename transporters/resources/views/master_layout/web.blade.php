<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
@yield('title')

<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/bootstrap.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/font-awesome.min.css')}}" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i'" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/owl.carousel.min.css')}}" />
<!-- <link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/slider.css')}}" /> -->
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/animate.css')}}" />

<link rel="stylesheet" href="{{asset('public/assets/bauenfreight/css/bootstrap-datepicker.css')}}"/>
<link rel="stylesheet" href="{{asset('public/assets/bauenfreight/css/wickedpicker.css')}}"/>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css"/>
  <link rel="icon" href="public/favicon.ico" type="image/x-icon">


<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/styles.css')}}" />

@yield('custom_css')

<style>
  header.inner {
	/* position: fixed; */
}
  header.inner .welcome {
    background: #585858;
    position: relative;
    top: 0;
    width: 100%;
    z-index: 2;
    padding: 7px 0;
    opacity:1
}
header .header-wrapper {
    background: rgba(173,13,39,0.8);
    color: #fff;    
    z-index: 1;
    width: 100%;
    
}

.bootstrap-select.btn-group:not(.input-group-btn) {
  margin-bottom: 0 !important;
}

</style>

@yield('metadata')

</head>

<body>
<div class="loader"></div>
<div class="main-wrapper">
  <div id="header-foo"></div>
  <header class="inner fixed"  style="z-index:9">
  <div class="welcome" id="mihead1">
    <div class="container ">
    <div class="row">
   <div class="col-sm-4 col-xs-6">
   {{-- {{trans('pdg.7') }} &nbsp; &nbsp; --}}
   
   
     @php
	    if($_SERVER['REQUEST_URI'] != "/transporter/how-it-works") {
	 @endphp
	 
    <select class="selectpicker languageSwitcher" data-width="fit" style="z-index:9">
      @if(!Session::has('locale'))
      <option    style="z-index:9" data-content='<span class="flag-icon flag-icon-pe"></span> Español'>Español</option>
      <option   style="z-index:9" data-content='<span class="flag-icon flag-icon-us"></span> English' value= "en" <?php if(Session::has('locale') && Session::get('locale')=="en") 
      { ?> selected="" <?php } ?>>
      English
      </option>
      @else	
      <option   style="z-index:9" data-content='<span class="flag-icon flag-icon-us"></span> English' value= "en" <?php if(Session::has('locale') && Session::get('locale')=="en") 
      { ?> selected="" <?php } ?>>
      English
      </option>
      <option    style="z-index:9" data-content='<span class="flag-icon flag-icon-pe"></span> Español' value="ar" <?php if(Session::has('locale') && Session::get('locale')=="ar") 
      { ?> selected="" <?php } ?>>
      Español
      </option>
      @endif
	</select>
   
     @php
	    }  
	 @endphp
	 
   
   </div>
   <div class="col-sm-8 col-xs-6 text-right top-links">
        @if(Session::has('web_user_id')) 
        <a href="{{url('shipper')}}" style="color:#fec40e;">Ir al Panel</a>
        <a href="{{url('logout')}}"> {{trans('pdg.8') }}</a>
        @else
        <a href="{{url('signin')}}">{{trans('pdg.9') }} </a>
       <a href="{{url('signup')}}">{{trans('pdg.10') }}  </a>
        @endif
       
       <a href="{{url('contacts')}}">{{trans('pdg.11') }}</a> 
       <a href="{{url('your-quote')}}" class="get-quote">{{trans('pdg.12') }}</a>
   </div>
   </div>
    </div>
    </div>
    
      <div class="header-wrapper" id="mihead2"><div class="container ">
      <div  class="menu-icon"> <span></span> <span></span> <span></span> </div>
       <a class="logo" href="{{url('/')}}"><img src="{{asset('public/assets/bauenfreight/images/logo.png')}}"  alt="" class="img-responsive"></a>
        <div class="right-links">
        
          <div class="top-nav">
            <ul>
              <li><a href="{{url('/')}}">{{trans('pdg.2')}}</a></li>
              <li><a href="{{url('about')}}">{{trans('pdg.6')}}</a></li>
              <li><a href="{{url('how-it-works')}}">{{trans('pdg.3')}}</a></li> 
              <li><a href="{{url('shipper')}}">{{trans('pdg.4')}} </a></li> 
              <li><a href="{{url('../Transporter/login')}}">{{trans('pdg.5')}}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>
  <section>
  @yield('banner')
  </section>		
  <section class="body-wrapper">
        @yield('main_container')
  </section>
<footer class="wow fadeIn">
  <div class="footer-links wow fadeInUp">
      <div class="container">
          <div class="row" style="padding-bottom: 40px;">
              <div class="col-sm-8 contact">
                  <div class="flogo" style="margin-top: 45px;">
                  <a href="{{url('/')}}"><img src="{{asset('public/assets/bauenfreight/images/logo.png')}}" class="img-responsive" style="width: 200px;"></a>
                  </div>
              </div>
              <div class="col-sm-2 ">
                  <h5>{{trans('pdg.24') }}</h5>
                  <ul>
                      <li><a href="{{url('/')}}">{{trans('pdg.2') }}</a></li>
                      <li><a href="{{url('about')}}">{{trans('pdg.6') }}</a></li>
                      <li><a href="{{url('how-it-works')}}">{{trans('pdg.3') }}</a></li> 
                  </ul>
              </div>
              <div class="col-sm-2">
			    
				  <h5>Get started</h5>
                  <ul>
			     	  @if(Session::has('web_user_id')) 
						<li><a href="{{url('shipper')}}" style="color:#fec40e;">Ir al Panel</a></li>
						<li><a href="{{url('logout')}}"> {{trans('pdg.8') }}</a></li>
					   @else
						<li><a href="{{url('signin')}}">{{trans('pdg.9') }} </a></li>
					    <li><a href="{{url('signup')}}">{{trans('pdg.10') }}  </a></li>
					   @endif
					   
					    <li><a href="{{url('contacts')}}">{{trans('pdg.11') }}</a></li>
                  </ul>
				  
              </div>
          </div>
		  <div class="row" style="border-top: solid 1px #646464;">
				<div class="col-sm-10" style="padding-top: 38px;">
				   <h6>Av. Javier Prado Oeste 2061, San Isidro - Lima, Perú.</h6>
				</div>
				<div class="col-sm-2">
					 <div class="social-links">
						  <a href="https://www.facebook.com/BauenFreight/?hc_ref=ART7wsZaVsRNBpSs-8BAcimSBquUcueRoyHBxMHvPLsbApCpgCJDhRtHqJ711ARSwbE" class="fa fa-facebook"></a>
						  <a href="www.twitter.com" class="fa fa-twitter"></a>
						  <a href="https://www.linkedin.com/in/sergio-olcese-57745214b" class="fa fa-linkedin"></a>
						  <a href="https://www.youtube.com/playlist?list=UUneK2O6oKItOBj8HXm4K_WA" class="fa fa-youtube-play"></a>
					  </div>
				</div>
		  </div>
		  
      </div>
  </div>
<div class="copy">
<div class="container">
{{trans('pdg.22') }}  &copy; 2021  BAUEN FREIGHT.    |     <a  href="{{url('privacy-policy')}}">{{trans('pdg.20') }}</a>     |    <a href="{{url('terms-and-conditions')}}">{{trans('pdg.21') }}</a></div>
</div>
</footer>
  </div>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/jquery.min.js')}}"></script> 
<script>
  $( document ).ready(function() {
    var h1 = document.getElementById('mihead1'); 
    var h2 = document.getElementById('mihead2'); 
    var hf = (h1.clientHeight)+(h2.clientHeight);
    
  
    $('#sectionbanner').css('position','relative');
    $('#sectionbanner').css('top',hf+'px');
});
</script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/owl.carousel.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/wickedpicker.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js"></script>

<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/wow.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/script.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/jquery.validate.min.js')}}"></script>



<!– WARRNIIGG -->
<!--
<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/791558152160a197071f44c89/11aad28b9f76c0d6bdc67a799.js");</script>

<script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us16.list-manage.com","uuid":"791558152160a197071f44c89","lid":"0a3f1b9eb3"}) })</script>


-->


<script>	
  $(document).ready(function (){
    $(".languageSwitcher").change(function(){
      var local = $(this).val();
      var _token = $("input[name=_token]").val();          	
      $.post("{{url('api/lang')}}", {
        locale:local , 
        _token:_token
      }, function (data, status) {
        console.log(data);
        window.location.reload(true);
      });           
    });        
  });
</script>
@yield('custom_js')
</body>
</html>