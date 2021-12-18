// JavaScript Document

jQuery(window).load(function () {
		$(".loader").fadeOut("slow");

		let headerHeight = $('.inner').outerHeight()
		$('#header-foo').height(headerHeight + 'px')
});	
	
	
$(document).ready(function () {
	$(".tab2").hide();
	$(".t1").click( function(){
		//alert();
		$(".tab1").fadeIn();
		$(".tab2").fadeOut();
	});

	$(".t2").on("click", function(){
		$(".tab1").fadeOut();
		$(".tab2").fadeIn();
	});
	
	$('.banner-carousel').owlCarousel({
		loop: true,
		autoplay: false,
		smartSpeed: 5000,
		animateOut: 'fadeOut',
		animateIn: 'fadeIn',
		mouseDrag: false,
		items: 1,
		dots:true
	});

	wow = new WOW({
		animateClass: 'animated',
		offset: 100,
		callback: function (box) {
			//console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
		}
	});
	wow.init();

	if($(window).width() > 768){	
		$(".profile-icon").click(function (){
			$(".sub-menu").fadeIn();
			});
			
		$("body").click(function (e){
		if(!$(e.target).closest(".profile-icon, .sub-menu").length){
				$(".sub-menu").fadeOut();
				}
			else{
				$(".sub-menu").fadeIn();
				}
			});	
	} else{
		$(".profile-icon").click(function (){
			$(".sub-menu").fadeToggle();
		});
	}




	$('.menu-icon').click(function () {
		if ($('.menu-icon').hasClass("open")) {
			$('.menu-icon').removeClass('open');
			$("header").removeClass('active');
			
			
		}
		else {
			$('.menu-icon').addClass('open');
			$("header").addClass('active');
			
			
		}
	});



	if($(window).width() <= 991 && !$(".right-links .top-nav ul > a").length){
		$(".right-links .top-nav ul").prepend($(".top-links a").not($(".get-quote")));
	}

	$(".notification").click(function(){
		let $notificationMenu = $(this).find(".notification-menu") 

		if($notificationMenu.is(':visible')) {
			$notificationMenu.fadeOut();
		} else {
			$notificationMenu.fadeIn();
		}
	});	

	// $("body").click(function (e){
	// 	if(!$(e.target).closest(".notification, .notification-menu").length){
	// 		$(".notification-menu").fadeOut();
	// 	} else{
	// 		$(".notification-menu").fadeIn();
	// 	}
	// });

	
	
	$(".member_list li").click(function(){
		
		$(".chat_sidebar").hide();
		$(".message_section").show();
	});	

	$(".btn-msg-back").click(function(){
		$(".chat_sidebar").show();
		$(".message_section").hide();
	});
	
	
     var nowDate = new Date(); 
     var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0); 
	
	$('#pickup_date').datepicker({
		startDate: today,
		format: 'dd-mm-yyyy',
		language: 'es',
		autoclose: true,
	});
	
	$('.timepicker').wickedpicker({
		title: 'HORA DE RECOJO',
		now: '09:00'
	});
  
	
	$('.selectpicker').selectpicker();
	
	
});

$(window).resize(function(e){
	let headerHeight = $('.inner').outerHeight()
	$('#header-foo').height(headerHeight + 'px')
	
if($(window).width() <= 991 && !$(".right-links .top-nav ul > a").length){
$(".right-links .top-nav ul").prepend($(".top-links a").not($(".get-quote")));
}
else if($(window).width() > 991 ){
	$(".top-links").prepend($(".right-links .top-nav ul > a"));
	}
});

$(window).scroll(function(){
	// if($(window).width() > 768){
	// 	var winscroll= $(this).scrollTop();
	// 	var hdrheight= $("header").outerHeight();
	
	// 	if(  winscroll > hdrheight ){
	// 		$("header").addClass("fixed");
	// 		}			
	// 	else{
	// 		$("header").removeClass("fixed");
	// 		}
	// }
});


function timerInterval(time) {
		 
		// Set the date we're counting down to
		var countDownDate = new Date(time).getTime();

		// Update the count down every 1 second
		var x = setInterval(function() {

		  // Get today's date and time
		  var now = new Date().getTime();
			
		  // Find the distance between now and the count down date
		  var distance = countDownDate - now;
			
		  // Time calculations for days, hours, minutes and seconds
		  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			
		  // Output the result in an element with id="demo"
		  document.getElementById("idTimer").innerHTML = days + "d " + hours + ":"
		  + minutes + ":" + seconds;
			
		  // If the count down is over, write some text 
		  if (distance < 0) {
			clearInterval(x);
			document.getElementById("idTimer").innerHTML = "EXPIRO";
			window.location.reload(true);
		  }
		}, 1000);
		
}


$("#btnFC1").click(function(){

    $("#form_et").submit();
   
});




var currentdate; 
var dia;
var mes;
var anio;
var hora;
var minutos;
var horas = 0;
//var datetime;

function addHoras (horas) {
	currentdate = new Date(); 
	currentdate.setHours(currentdate.getHours() + horas); 
} 	

function fechaFormato () {
	
	dia = currentdate.getDate();
	mes = (currentdate.getMonth()+1);
	anio = currentdate.getFullYear();
	hora = currentdate.getHours();
	minutos = currentdate.getMinutes(); 
	
	if ((dia >= 0)&&(dia <= 9)){ 
	  dia="0"+dia; 
	}
	
	if ((mes >= 0)&&(mes <= 9)){ 
	  mes="0"+mes; 
	}
	
	if ((hora >= 0)&&(hora <= 9)){ 
	  hora="0"+hora; 
	}
	
	if ((minutos >= 0)&&(minutos <= 9)){ 
	  minutos="0"+minutos; 
	}
	
}


function setFechaActual(horas) {
	   
	   addHoras(horas);
	
	   fechaFormato();
		
		var datetime = anio + "-" 
       + mes + "-" 
       + dia + " " 
       + hora + ":" 
       + minutos;

	   return datetime;
		
}






