function category_banner_chenge_status(id){    
    var dec = window.atob(id);
    var res = dec.split('||');
    var item_id = res[1];
    var item_details = $("#category_banner_details_"+item_id).val();
    var output = $.parseJSON(item_details);  
    if(output.status == 1){
        var status = 2 ;
    }else{
        var status = 1;
    }
  $.post('api/user-change-status',
            {
                id: item_id,
                status: status,
                table_name: 'category_wise_banner',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-category-wise-banner' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-category-wise-banner' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
})
};




function banner_chenge_status(id){    
    //console.log(id);
    var dec = window.atob(id);
    //console.log(dec);
    var res = dec.split('||');
   // console.log(res[1]);
    var item_id = res[1];
    var item_details = $("#banner_details_"+item_id).val();
    var output = $.parseJSON(item_details);
    //console.log(item_details);
    //console.log(output.id);
    if(output.status == 1){
        var status = 2 ;
    }else{
        var status = 1;
    }
  $.post('api/user-change-status',
            {
                id: item_id,
                status: status,
                table_name: 'banner',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-banner' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-banner' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
})
};
function slider_chenge_status(id){    
    var dec = window.atob(id);
    var res = dec.split('||');
    var item_id = res[1];
    var item_slider = $("#slider_details_"+item_id).val();
    var output = $.parseJSON(item_slider);
    if(output.status == 1){
        var status = 2 ;
    }else{
        var status = 1;
    }
  $.post('api/user-change-status',
            {
                id: item_id,
                status: status,
                table_name: 'slider',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-slider' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-slider' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
});
};
