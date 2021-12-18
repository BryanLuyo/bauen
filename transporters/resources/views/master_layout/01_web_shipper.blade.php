<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
@yield('title')
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/bootstrap.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/font-awesome.min.css')}}" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i'" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i'" />
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/owl.carousel.min.css')}}" />
<!--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />-->
<!--<link rel="stylesheet" href="{{asset('public/assets/bauenfreight/fonts/font-awesome.css')}}" type="text/css">-->
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/animate.css')}}" />


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css"/>


<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/styles.css')}}" />
<style>
    label.error{
        color: red;
        font-size: 14px;
    }
</style>
@yield('custom_css')
</head>

<body>
<div class="loader"></div>
<div class="main-wrapper">
  <header class="inner">
 
    
      <div class="header-wrapper"><div class="container-fluid">
      
       <a class="logo" href="{{url('/')}}"><img src="{{asset('public/assets/bauenfreight/images/logo.png')}}"  alt="" class="img-responsive"></a>
	   
	   
		<div class="col-sm-4 col-xs-6" style="margin-top: 10px;">
			<select class="selectpicker languageSwitcher" data-width="fit">
			<option data-content='<span class="flag-icon flag-icon-us"></span> English' value= "en" <?php if(Session::has('locale') && Session::get('locale')=="en") { ?> selected="" <?php } ?>>English</option>
			<option  data-content='<span class="flag-icon flag-icon-pe"></span> Espa単ol' value="ar" <?php if(Session::has('locale') && Session::get('locale')=="ar") { ?> selected="" <?php } ?>>Espa単ol</option>
			</select>
		</div>
		<div class="col-sm-3 col-xs-6 top-links" style="margin-top: 10px">
            <a href="{{url('your-quote')}}" class="btn btn-primary">{{trans('pdg.12') }}</a>
		</div>
	   
	   
        <div class="post-login-welcome">
            <div class="fa fa-bell-o notification" id="notification_data_count" data-count="0">
        	<div class="notification-menu">
				<ul class="notification_data_details">
				</ul>
			</div>
			</div>
            Welcome <a>
                 @if(Session::has('web_user_name')) 
                {{Session::get('web_user_name')}}
                @endif
            </a>
          @if(Session::has('web_user_id')) 
        <a href="{{url('logout')}}"  class="btn-logout">{{trans('pdg.8') }} </a>
        @endif
         
        </div>
      </div>
    </div>
  </header>
  
  <section class="body-wrapper">
  
  
  <div class="container-fluid">
  <div class="left-nav">
  	<ul>
  	<li class="active"><a href="{{url('shipper')}}"><i class="fa fa-home fa-fw"></i><span>Shipper Home</span></a></li>
  	<li><a href="{{url('request-list')}}"><i class="fa fa-indent  fa-fw"></i><span>Licitaciones electrónicas</span></a></li>
  	<li><a href="{{url('message')}}"><i class="fa fa-envelope-o  fa-fw"></i><span>Messenger</span></a></li>
  	<li><a href="{{url('profile')}}"><i class="fa fa-user  fa-fw"></i><span>Perfil</span></a></li>
  	<li><a href="{{url('past-bids')}}"><i class="fa fa-gavel  fa-fw"></i><span>Mis Rutas</span></a></li>
  	</ul>




 
  </div>
   @yield('main_container')
  
  </div>

  </section>
<footer class="wow fadeIn">

<div class="footer-links wow fadeInUp">


<div class="container">
<div class="row">
<div class="col-sm-5 contact">
<h5>Contact Us</h5>
<div class="flogo">
   <a href="{{url('/')}}">
        <img src="{{asset('public/assets/bauenfreight/images/logo.png')}}" class="img-responsive">
    </a>
</div>
<p>{{trans('pdg.13') }}</p>
 <p>{{trans('pdg.14') }}</p>
 <p>{{trans('pdg.15') }} </p>
 
 <div class="social-links">
     <a href="https://www.facebook.com/BauenFreight/?hc_ref=ART7wsZaVsRNBpSs-8BAcimSBquUcueRoyHBxMHvPLsbApCpgCJDhRtHqJ711ARSwbE" class="fa fa-facebook"></a>
     <a href="www.twitter.com" class="fa fa-twitter"></a>
     <a href="https://www.linkedin.com/in/sergio-olcese-57745214b" class="fa fa-linkedin"></a>
     <a href="https://www.youtube.com/playlist?list=UUneK2O6oKItOBj8HXm4K_WA" class="fa fa-youtube-play"></a>
 </div>
</div>
<div class="col-sm-2 ">
<h5>{{trans('pdg.24') }}</h5>
<ul>
<li><a href="{{url('/')}}">{{trans('pdg.2') }}</a></li>
<li><a href="{{url('about')}}"> {{trans('pdg.6') }}</a></li>
<li><a href="{{url('how-it-works')}}">   {{trans('pdg.3') }}</a></li> 
<li><a href="{{url('shipper')}}">    {{trans('pdg.4') }} </a></li> 
<li><a href="{{url('signup')}}">    {{trans('pdg.5') }}</a></li>
</ul>
</div>

<div class="col-sm-5 contact-form">
<h5>Send your message</h5>
<h5>{{trans('pdg.25') }}</h5>
 <form action="{{url('send-your-message')}}" method="post" name="send_your_message">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group"><input type="text" name="client_name" class="form-control input-lg" placeholder="{{trans('pdg.16') }}"> </div>
        <div class="form-group"><input type="email" name="client_email" class="form-control input-lg" placeholder="{{trans('pdg.17') }}"> </div>
            <div class="form-group">
                <textarea type="text" name="client_message" class="form-control input-lg" rows="4" placeholder="{{trans('pdg.18') }}"></textarea> 
            </div>
            <div class="form-group text-center">
                <input type="submit" value="{{trans('pdg.19') }}" class="btn btn-lg btn-send">
            </div>
    </form>
</div>
</div>
</div>
</div>






<div class="copy">
<div class="container">

{{trans('pdg.22') }}  &copy; 2018  BAUEN FREIGHT.    |     <a  href="{{url('privacy-policy')}}">{{trans('pdg.20') }}</a>     |    <a href="{{url('terms-and-conditions')}}">{{trans('pdg.21') }}</a></div>


</div>
</footer>
  </div>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/jquery.min.js')}}"></script> <script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/owl.carousel.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/wow.js')}}"></script>

<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/wickedpicker.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/script.js')}}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js"></script>

<!– WARRNIIGG -->

<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/791558152160a197071f44c89/11aad28b9f76c0d6bdc67a799.js");</script>

<script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us16.list-manage.com","uuid":"791558152160a197071f44c89","lid":"0a3f1b9eb3"}) })</script>







    @yield('custom_js')
    <script>
    $(document).ready(function () {
    get_customer_notification();
});
setInterval(function(){  get_customer_notification();}, 3000);
function get_customer_notification() {
    var customer_id =  {{Session::get('web_user_id')}};
    if (customer_id > 0) {
         $.get("total-notification", function(data, status){
        if (data.result == 'TRUE') {
            $(".notification_data_details").html("");
            $("#notification_data_count").attr("data-count",data.details.length);
            var details = "";           
            for (var i = 0; i < data.details.length; i++) {
                        details += '<li> <a href = "javascript:void(0);" > ' + data.details[i].notification_text + ' </a>  </li>';
                    }
            $(".notification_data_details").html(details);
        }else {
             $("#notification_data_count").attr("data-count",0);
                $(".notification_data_details").html("");
            }
    });
    
    } else {

        $('.notification-content').html('<li> <a href = "javascript:void(0);" >No unread notification found.</b> </a></li>');
        $('.noti-count').text(0);
    }
}

    
    </script>
	<script>	
        $(document).ready(function (){
        //Change Language
      //  $('.selectpicker').selectpicker();
        
        $(".languageSwitcher").change(function(){
        var local = $(this).val();
	var _token = $("input[name=_token]").val();          	
	$.post("{{url('api/lang')}}",
                {
                    locale:local , 
                    _token:_token
                }, function (data, status) {
                console.log(data);
		window.location.reload(true);
                });           
            });        
        });
	</script>
</body>
</html>
