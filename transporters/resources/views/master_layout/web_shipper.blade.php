<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
@yield('title')
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/bootstrap.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/font-awesome.min.css')}}" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i'" />
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i'" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/owl.carousel.min.css')}}" />
<!--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />-->
<!--<link rel="stylesheet" href="{{asset('public/assets/bauenfreight/fonts/font-awesome.css')}}" type="text/css">-->
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/animate.css')}}" />

<link rel="stylesheet" href="{{asset('public/assets/bauenfreight/css/bootstrap-datepicker.css')}}"/>
<link rel="stylesheet" href="{{asset('public/assets/bauenfreight/css/wickedpicker.css')}}"/>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css"/>
  <link rel="icon" href="public/favicon.ico" type="image/x-icon">


<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/styles.css?v=' . mt_rand())}}" />

<link rel="stylesheet" type="text/css" href="{{asset('public/assets/bauenfreight/css/w3.css')}}" />

@yield('custom_css')
</head>

<body>

<div class="loader"></div>
<div class="main-wrapper">
<header class="inner" style="position:fixed;top:0">
    <div class="header-wrapper header-shipper">
        <div class="container">
            <a class="logo" href="{{url('shipper')}}"><img src="{{asset('public/assets/bauenfreight/images/logo.png')}}"  alt="" class="img-responsive"></a>
            
            <div class="right-links">
                <div class="top-nav">
                  <ul>
                    <li @if (\Request::is('your-quote')) class="active" @endif>
                        <a href="{{url('your-quote')}}"><span>Cotizar</span></a>
                    </li>
                    <li @if (\Request::is('request-list')) class="active" @endif>
                        <a href="{{url('request-list')}}"><span>Cotizaciones en Curso</span></a>
                    </li>
                    <li @if (\Request::is('transit-requests')) class="active" @endif>
                        <a href="{{url('transit-requests')}}"><span>En tránsito</span></a>
                    </li>
                    <li @if (\Request::is('completed-requests')) class="active" @endif>
                        <a href="{{url('completed-requests')}}"><span>Completadas</span></a>
                    </li>
                    <li @if (\Request::is('done-requests')) class="active" @endif>
                        <a href="{{url('done-requests')}}"><span>Archivadas</span></a>
                    </li>
                  </ul>
                </div>
            </div>

            <div class="post-login-welcome">
                <span class="mr-auto mr-md-2">
                    {{trans('pdg.107') }} 
                    <a href="{{url('profile')}}">
                        @if(Session::has('web_user_name'))
                        {{ Session::get('web_user_name') }}
                        @endif
                    </a>
                </span>
                
                <div class="fa fa-bell-o notification" id="notification_data_count" data-count="0">
                    <div class="notification-menu">
                        <ul class="notification_data_details"></ul>
                    </div>
                </div>

                @if(Session::has('web_user_id')) 
                    <div style="display:inline-block;position:relative">
                        <a href="#" class="dropdown-toggle btn-pill text-white" id="drop3" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="drop3" style="right:0;left:auto;">
                            <li><a href="{{url('shipper')}}" class="text-dark">Dashboard</a></li>
                            @if(Session::has('is_user_super_admin') && Session::get('is_user_super_admin') == 0)
                                <li><a href="{{url('subadmins')}}" class="text-dark">Administradores</a></li>
                            @endif
                            <li><a href="{{url('profile')}}" class="text-dark">Perfil</a></li>
                            <li><a href="{{url('logout')}}" class="text-dark">Cerrar sesión</a></li>
                        </ul>
                    </div>
                @endif
                {{-- <a href="{{url('logout')}}"  class="btn-logout">{{trans('pdg.8') }} </a> --}}
            </div>
        </div>
    </div>
</header>
  
<section class="body-wrapper shipper">
    <div class="left-nav hidden-md hidden-lg" id="left-nav" style="position:fixed;">
        <ul>
            <li class="{{ Request::path() == 'shipper' ? 'active ': '' }}"><a href="{{url('shipper')}}"><i class="fa fa-home fa-fw"></i><span>Dashboard</span></a></li>
            <li class="{{ Request::path() == 'your-quote' ? 'active ': '' }}"><a href="{{url('your-quote')}}"><i class="fa fa-money  fa-fw"></i><span>Cotizar</span></a></li>
            <li class="{{ Request::path() == 'request-list' ? 'active ': '' }}"><a href="{{url('request-list')}}"><i class="fa fa-indent  fa-fw"></i><span>Cotizaciones en curso</span></a></li>
            <li class="{{ Request::path() == 'transit-requests' ? 'active ': '' }}"><a href="{{url('transit-requests')}}"><i class="glyphicon glyphicon-road"></i><span>En tránsito</span></a></li>
            <li class="{{ Request::path() == 'completed-requests' ? 'active ': '' }}"><a href="{{url('completed-requests')}}"><i class="glyphicon glyphicon-check"></i><span>Completadas</span></a></li>
            <li class="{{ Request::path() == 'done-requests' ? 'active ': '' }}"><a href="{{url('done-requests')}}"><i class="glyphicon glyphicon glyphicon-remove-circle"></i><span>Archivadas</span></a></li>
            <li class="{{ Request::path() == 'profile' ? 'active ': '' }}"><a href="{{url('profile')}}"><i class="fa fa-user  fa-fw"></i><span>Perfil</span></a></li>
        </ul>
    </div>

    {{-- <div class="container font-avenir pl-5 pl-md-3" id="main-container" style="background-color:#fff;min-height:calc(100vh - 150px)"> --}}
    <div class="container pl-5 pl-md-3" id="main-container" style="background-color:#fff;min-height:calc(100vh - 91px)">

        @yield('main_container')
    </div>
</section>

<footer id="footer">
    {{-- <div class="footer-links wow fadeInUp">
        <div class="container">
            <div class="row">
                <div class="col-sm-5 contact">
                    <div class="flogo">
                    <a href="{{url('/')}}"><img src="{{asset('public/assets/bauenfreight/images/logo.png')}}" class="img-responsive"></a>
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
                <div class="col-sm-4 ">
                    <h5>{{trans('pdg.24') }}</h5>
                    <ul>
                        <li><a href="{{url('/')}}">{{trans('pdg.2') }}</a></li>
                        <li><a href="{{url('about')}}">{{trans('pdg.6') }}</a></li>
                        <li><a href="{{url('how-it-works')}}">{{trans('pdg.3') }}</a></li> 
                        <li><a href="{{url('shipper')}}">{{trans('pdg.4') }} </a></li> 
                        <li><a href="{{url('signup')}}">{{trans('pdg.5') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="copy">
        <div class="container">
            {{trans('pdg.22') }}  &copy; 2019  BAUEN FREIGHT. | <a  href="{{url('privacy-policy')}}">{{trans('pdg.20') }}</a> | <a href="{{url('terms-and-conditions')}}">{{trans('pdg.21') }}</a>
        </div>
    </div>
</footer>
</div>

<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/owl.carousel.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/wow.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/wickedpicker.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/bootstrap-datepicker.es.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/script.js?v=1.0.1')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="{{asset('public/assets/bauenfreight/js/jquery.validate.min.js')}}"></script>

@yield('custom_js')
    <script>
        $(document).ready(function () {
            get_customer_notification();

            // const leftNav = $('#left-nav'),
            //       rightBody = $('.right-body'),
            //       header = $('.header-wrapper'),
            //       footer = $('#footer')

            // let newHeight = $(window).height() - (header.height() + footer.height())
            // if(leftNav.height() > newHeight) newHeight = leftNav.height()

            // leftNav.css('height', newHeight + 'px')
            // rightBody.css('height', newHeight + 'px')

            // // Left navbar sizing
            /* const mainContainer = $('#main-container')
            const leftNav = $('#left-nav')
            leftNav.css('min-height', mainContainer.height() + 'px') */

            // $(window).resize(function() {
            //     let newHeight = $(window).height() - (header.height() + footer.height())
            //     if(leftNav.height() > newHeight) newHeight = leftNav.height()

            //     leftNav.css('height', newHeight + 'px')
            //     rightBody.css('height', newHeight + 'px')
            // })

            //Change Language
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
            // $(document).on('click','.notification-link',function(e){
            //     e.preventDefault();
            //     console.log($(this).data('request-id'))
            // })
        });

        setInterval(function(){  get_customer_notification();}, 3000);
        
        function get_customer_notification() {
			
			//console.log("{{url('total-notification')}}");

            var customer_id =  {{Session::get('web_user_id')}};
            if (customer_id > 0) {
                $.get("{{url('total-notification')}}", function(data, status){
                if (data.result == 'TRUE') {
                    $(".notification_data_details").html("");
                    $("#notification_data_count").attr("data-count",data.details.length);
                    var details = "";           
                    for (var i = 0; i < data.details.length; i++) {
                        // details += `
                        //     <li>
                        //         <a href="{{url('view-notification/${data.details[0].request_id}')}}">${data.details[i].notification_text}</a>
                        //     </li>`;
                        details += '<li><a href="view-notification/'+ data.details[i].request_id +'">' + data.details[i].notification_text + '</a></li>';
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
</body>
</html>
