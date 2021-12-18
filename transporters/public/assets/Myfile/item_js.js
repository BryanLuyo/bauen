/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * var json_data = $.parseJSON(master_cloth_json);
 */
 var image_url = $("#gobal_image_url").val();
function open_edit_modal(id){
    
    //console.log(id);
 var all_data =  $("#image_id_"+id).val();  
     //console.log(all_data);
      $('#set_edit_image').html('');
      var loc = JSON.parse(all_data);
      console.log(image_url);
      console.log(loc['item_image_name']);
       $("#item_images_id").val(loc['id']);   
      $('#set_edit_image').attr("src",image_url+'/'+loc['item_image_name']);
     $('#image_edit_myModal').modal('show');
}
 function delete_ad_images(id){    
  //  console.log(id); //user-delete-post-image     
   swal({
        title: "Are you sure?",
        text: "Are you want to delete this image!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function () {
        $.post('api/user-delete-post-image',
                {
                    item_images_id: id,
                }, function (data, status) {
            if (data.result == 'TRUE') {
                swal("Deleted!", data.message + "!", "success");/************sweet alert  for success***************************/
                $("#image_delete_id_" + id).remove();
                window.location.reload();
                /************remove div after product delete complete***************************/
            } else {
                swal("Oops!", data.message + "!", "error");/************sweet alert  for error***************************/
               window.location.reload();
            }
        });
    });
}
 
 /*
 $("#item_edit_submit").click(function(){
    var item_id = $("#edit_item_id").val();    
    var item_name = $("#edit_item_name").val();
    var subcategory_id = $("#edit_subcategory_id").val();
    var price = $("#edit_price").val();
    var description = $("#edit_description").val();
    var item_tags = $("#edit_item_tags").val();
    var video_link = $("#edit_video_link").val();
    console.log(item_id);
    $.post('api/user-edit-post',
            { 
                item_id: item_id ,
                item_name : item_name,
                subcategory_id: subcategory_id ,
                price : price,
                description: description ,
                item_tags : item_tags,
                video_link: video_link 
            }, function (data, status) {
                console.log(data.result);              
                if (data.result == 'TRUE') {             
                   swal("Success", "Ad updated successfully !", "success");
				   window.location.href = 'admin-user-post' ;
                }else{
                    swal("Oops!", "Unable to update Ad !", "error");
					window.location.href = 'admin-user-post' ;
                }
 });
 });
*/
function user_chenge_status(id){    
    //console.log(id);
    var dec = window.atob(id);
    //console.log(dec);
    var res = dec.split('||');
   // console.log(res[1]);
    var item_id = res[1];
    var item_details = $("#user_details_"+item_id).val();
    var output = $.parseJSON(item_details);
   // console.log(item_details);
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
                table_name: 'users',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-user-list' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-user-list' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
});
};






var my_env = $("#my_env").val();
var my_env = $("#my_env").val();
var my_enc = window.btoa(my_env);
//$("#select_cloth_size_id").click(function () {
//    console.log("hi priyo");
//});



function chenge_featured_status(id){  
    var dec = window.atob(id);
    var res = dec.split('||');
    var item_id = res[1];
    var status = $("#item_details_featured_status_"+item_id).val();
    if(status == 1){
        var Featured_status = 0 ;
        
        var show_status = 'Non-Featured';
    }else{
        var Featured_status = 1;
      
        var show_status = 'Featured';
    }
  $.post('api/user-change-featured-status',
            {
                id: item_id,
                is_premium_item: Featured_status,
                table_name: 'item',
            }, function (data) {
        if (data.result == 'TRUE') {
            $("#item_details_featured_status_"+item_id).val(Featured_status);
            $("#product_featured_change_"+item_id).text("");
            $("#product_featured_change_"+item_id).text(show_status);
            swal("Success", "Featured Status updated successfully !", "success");
        }else{          
            swal("Oops!", "Unable to update Featured Status !", "error");
        }
    });
};
function chenge_status(id){  
    var dec = window.atob(id);
    var res = dec.split('||');
    var item_id = res[1];
    var status = $("#item_details_status_"+item_id).val();
    if(status == 1){
        var status = 2 ;
        var showstatus = 'Blocked';
        var show_status = 'Active';
    }else{
        var status = 1;
        var showstatus = 'Active';
        var show_status = 'Block';
    }
  $.post('api/user-change-status',
            {
                id: item_id,
                status: status,
                table_name: 'item',
            }, function (data) {
        if (data.result == 'TRUE') {
            $("#product_status_"+item_id).text("");
            $("#product_status_change_"+item_id).text("");
            $("#product_status_"+item_id).text(showstatus);
            $("#product_status_change_"+item_id).text(show_status);                 
            $("#item_details_status_"+item_id).val('');    
            if(showstatus == "Blocked"){
                $("#item_details_status_"+item_id).val('2');
            }else if(showstatus == "Active"){
                $("#item_details_status_"+item_id).val('1');
            }
            swal("Success", "Status updated successfully !", "success");
        }else{          
             swal("Oops!", "Unable to update Status !", "error");
        }
    });
};
//edit_user_ad
function edit_user_ad(id){    
    //console.log(id);
    var dec = window.atob(id);
    //console.log(dec);
    var res = dec.split('||');
   // console.log(res[1]);
    var item_id = res[1];
    var item_details = $("#item_details_"+item_id).val();
    var output = $.parseJSON(item_details);
    console.log(item_details);
    //console.log(output.id);
    
//  $.post('api/user-change-status',
//            {
//                id: item_id,
//                status: status,
//                table_name: 'item',
//            }, function (data, status) {
//        if (data.result == 'TRUE') {
//             window.location.href = 'admin-user-post' ;
//            swal("Success", "Status updated successfully !", "success");
//        }else{
//             window.location.href = 'admin-user-post' ;
//             swal("Oops!", "Unable to update Status !", "error");
//        }
//})
};

$().ready(function(){
 $("#category_id").change(function(){
     var category_id = $("#category_id").val();
     //console.log(category_id);
     $.post('api/admin-get-subcategory',
            { 
                category_id: category_id                
            }, function (data, status) {
                //console.log(data.result);
                $('#subcategory_id').html('');
                if (data.result == 'TRUE') {
                    //console.log(data.details);
                    var toAppend = '';
                    toAppend += '<option value="">Select Sub-Category</option>'; 
                    for(var i=0;i<data.details.length;i++){
                    toAppend += '<option value='+data.details[i]['id']+'>'+data.details[i]['subcategory_name']+'</option>';
                    }
                    $('#subcategory_id').append(toAppend);
                }else{
                    $('#subcategory_id').html('');
                }
 });
 });
  });
   $("#submit_item_search").click(function(){
      var sub_cat = $( "#subcategory_id option:selected" ).text();
     //alert(sub_cat);
    $("#set_sub_cat").val(sub_cat);
  });