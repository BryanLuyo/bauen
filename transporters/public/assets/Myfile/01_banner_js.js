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
// function Delete_banner() {
//        swal({
//        title: "Are you sure?",
//        text: "Are you want to delete this Sub Category!",
//        type: "warning",
//        showCancelButton: true,
//        confirmButtonColor: "#DD6B55",
//        confirmButtonText: "Yes, delete it!",
//        closeOnConfirm: false
//    });
//};