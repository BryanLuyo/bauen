 $("#subcategory_edit_submit").click(function(){
    var subcategory_id = $("#subcategory_id").val();
    var subcategory_name = $("#subcategory_name").val();
    var category_id = $("#category_id").val();
    console.log('hi jon');
    $.post('api/admin-edit-subcategory',
            {
                subcategory_id: subcategory_id,
                subcategory_name: subcategory_name,
                category_id: category_id

            }, function (data, status) {
               
        if (data.result == 'TRUE') {
             window.location.href = 'admin-subcategory-list';
            swal("Password Change!", data.message + "!", "success");/****admin-subcategory-list********sweet alert  for success***************************/

        } else {
            swal("Oops!", data.message + "!", "error");/************sweet alert  for error***************************/
            window.location.href = 'admin-subcategory-list';
        }
    });
 });
 function Delete_subcategory(data) {
    var dec = window.atob(data);   
    var res = dec.split('||');  
    var id = res[1];
    console.log(id);
    swal({
        title: "Are you sure?",
        text: "Are you want to delete this Sub Category!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function () {

        $.post('api/admin-delete-subcategory',
                {
                    subcategory_id: id,
                }, function (data, status) {
            if (data.result == 'TRUE') {
                swal("Deleted!", data.message + "!", "success");/************sweet alert  for success***************************/
                $("#others_members_" + id).remove();
                window.location.href = 'admin-subcategory-list';
                /************remove div after product delete complete***************************/
            } else {
                swal("Oops!", data.message + "!", "error");/************sweet alert  for error***************************/
                window.location.href = 'admin-subcategory-list';
            }
        });
    });
}
;



function subcat_chenge_status(id){    
    //console.log(id);
    var dec = window.atob(id);
    //console.log(dec);
    var res = dec.split('||');
   // console.log(res[1]);
    var item_id = res[1];
    var item_details = $("#sub_cat_details_"+item_id).val();
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
                table_name: 'subcategory',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-subcategory-list' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-subcategory-list' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
})
};

function chenge_cat_status(id){    
    //console.log(id);
    var dec = window.atob(id);
    //console.log(dec);
    var res = dec.split('||');
   // console.log(res[1]);
    var item_id = res[1];
    var item_details = $("#category_details_"+item_id).val();
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
                table_name: 'category',
            }, function (data, status) {
        if (data.result == 'TRUE') {
             window.location.href = 'admin-category-list' ;
            swal("Success", "Status updated successfully !", "success");
        }else{
             window.location.href = 'admin-category-list' ;
             swal("Oops!", "Unable to update Status !", "error");
        }
})
};





$().ready(function(){
    jQuery.validator.addMethod('filesize', function(value, element, param) {
   return this.optional(element) || (element.files[0].size <= param) 
});   
    $("#admin_category_add").validate({
        rules:{
            category_name : "required",
            category_image : {
                        required :true ,
                       // extension: "jpg|jpeg|png",
                        filesize : 2048445
                        }
//            category_description : {
//                        required :true ,
//                        minlength : 20
//                        }
        },
        messages : {
            category_name : "Please enter category name",
            category_image : {
                required : "Please upload category image",
               // extension : "Your file must be image file",
                filesize : " file size must be with in 2 mb "
            },
//            category_description : {
//                required : "Please enter your description",
//                minlength : "Your description must be at list 20 characters long"
//            }
        }
    });
    $("#admin_subcategory_add").validate({
        rules:{
            category_id : "required",
            subcategory_name : "required",
//            subcategory_image : {
//                        required :true ,
//                       // extension: "jpg|jpeg|png",
//                        filesize : 2048445
//                        },
//            subcategory_description : {
//                        required :true ,
//                        minlength : 20
//                        }
        },
        messages : {
            category_id :"Please select category ID",
            subcategory_name : "Please enter category name",
//            subcategory_image : {
//                required : "Please upload subcategory image",
//               // extension : "Your file must be image file",
//                filesize : " file size must be with in 2 mb "
//            },
//            subcategory_description : {
//                required : "Please enter your description",
//                minlength : "Your description must be at list 20 characters long"
//            }
        }
    });
});



