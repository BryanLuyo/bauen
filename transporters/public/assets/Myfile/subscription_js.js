function chenge_plan_status(id){    
    //console.log(id);
    var dec = window.atob(id);
    //console.log(dec);
    var res = dec.split('||');
   // console.log(res[1]);
    var item_id = res[1];
    var item_details = $("#plan_details_"+item_id).val();
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
                table_name: 'plan',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-plan-list' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-plan-list' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
})
};
