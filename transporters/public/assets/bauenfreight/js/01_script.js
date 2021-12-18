// JavaScript Document

jQuery(window).load(function () {
    $(".loader").fadeOut("slow");
});	
	
	
$(document).ready(function () {
	$('#pickup_date').datepicker({});	$('.timepicker').wickedpicker();  		$('.selectpicker').selectpicker();

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
		}
		else{
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
	$(this).find(".notification-menu").fadeIn();	
		
	});	
$("body").click(function (e){
				if(!$(e.target).closest(".notification, .notification-menu").length){
						$(".notification-menu").fadeOut();
						}
					else{
						$(".notification-menu").fadeIn();
						}
					});	
	
	
	
$(".member_list li").click(function(){
	
	$(".chat_sidebar").hide();
	$(".message_section").show();
});	
$(".btn-msg-back").click(function(){
	$(".chat_sidebar").show();
	$(".message_section").hide();
});	
});

$(window).resize(function(e){
	
if($(window).width() <= 991 && !$(".right-links .top-nav ul > a").length){
$(".right-links .top-nav ul").prepend($(".top-links a").not($(".get-quote")));
}
else if($(window).width() > 991 ){
	$(".top-links").prepend($(".right-links .top-nav ul > a"));
	}
});

$(window).scroll(function(){
	if($(window).width() > 768){
		var winscroll= $(this).scrollTop();
		var hdrheight= $("header").outerHeight();
	
		if(  winscroll > hdrheight ){
			$("header").addClass("fixed");
			}			
		else{
			$("header").removeClass("fixed");
			}
	}
});