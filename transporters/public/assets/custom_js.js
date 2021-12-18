$(document).ready(function (){
    //Change Language
    $("#languageSwitcher").change(function(){
        var local = $(this).val();
            var _token = $("input[name=_token]").val();
             $.post('/buy_sell_application/api/language',
                {
                    locale:local , 
                    _token:_token
                }, function (data, status) {
                console.log(data);
            });
             //window.location.href = '/buy_sell_application/';
           // window.location.reload(true);
        }); 
        
});
      /*  $.ajax({
            url:"/language",
            type:'POST',
            data : {locale:local , _token:_token},
            datatype:'json',
            success:function(data){
                console.log(data);
            },
            error:function(data){
                
            },
            beforeSend:function(){
                
            },
            complete:function(data){
                window.location.reload(true);
            }
        }); */
        
  