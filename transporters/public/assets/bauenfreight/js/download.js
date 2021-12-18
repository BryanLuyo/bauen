$( document ).ready(function() {

  if( navigator.userAgent.match(/Android/i)){

      $('#download-link').attr("href", "https://play.google.com/store/apps/developer?id=Bauen");
  }
  else if(navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)|| navigator.userAgent.match(/iPod/i)){

      $('#download-link').attr("href", "https://itunes.apple.com/us/developer/sergio-olcese/id1288677361?mt=8");
  }
 else {
    
    $('#download-link').attr("href", "http://www.bauenfreight.com");
  }

});
