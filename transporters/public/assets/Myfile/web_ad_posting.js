$().ready(function(){
     $("#contact_email").validate({
        rules:{
            client_name : "required",
            client_email : "required",
            client_email_subject : {
                        required :true 
                        },
            client_email_message : {
                        required :true ,
                        minlength : 20
                        }
        },
        messages : {
            client_name :"Please enter your Name",
            client_email : "Please enter Email Id",
            client_email_subject : {
                required : "Please enter Email subject",               
            },
            client_email_message : {
                required : "Please enter Message",
                minlength : "Your description must be at list 20 characters long"
            }
        }
    });    
    
      $("#submit_category").change(function(){
     var category_id = $("#submit_category").val();
     console.log(category_id);
     $.post('api/admin-get-subcategory',
            { 
                category_id: category_id                
            }, function (data, status) {
                console.log(data.result);
               // $('#subcategory_id_submit').html('');
               var $select=   $('#subcategory_id_submit').selectize({
            });
               var selectize = $select[0].selectize;
               selectize.clearOptions();
                if (data.result == 'TRUE') {
                    console.log(data.details);
                    var toAppend = '';
                   
                    
                    for(var i=0;i<data.details.length;i++){
                         console.log(data.details[i]['subcategory_name']);
                    toAppend += '<option value='+data.details[i]['id']+'>'+data.details[i]['subcategory_name']+'</option>';
                    selectize.addOption({value:data.details[i]['id'], text: data.details[i]['subcategory_name']});
                    }                   
                   
                    $('#subcategory_id_submit').append(toAppend);
                     

               


                }else{
                    $('#subcategory_id_submit').html('');
                }
                selectize.refreshOptions();
              
 });
 });   
    
    /************************************************************************************************/
     $("#ad_post_submit_done").click(function(){
         
         if( $("#item_name").val() == ""){
            alert("Please enter your Item Name");
            $("#item_name").focus();
            return false;
         }else if($("#description").val() == ""){
            alert("Please enter your Item Description");
            $("#description").focus();
            return false;
         }else if($("#price").val() == ""){
            alert("Please enter your Item Price");
            $("#item_name").focus();
            return false;
         }else if($("#submit_category").val() == ""){
            alert("Please enter your Category");
            $("#submit_category").focus();
            return false;
         }else if($("#subcategory_id_submit").val() == ""){
            alert("Please enter your Sub-Category");
            $("#subcategory_id_submit").focus();
            return false;
         }else if($("#country_id").val() == ""){
             alert("Please select country");
            $("#country_id").focus();
            return false;
         }else if($("#city").val() == ""){
              alert("Please enter city");
            $("#city").focus();
            return false;
         }else{
          $("#ad_post_submit").submit();
        }
     });
    });
 /*   
   var types= {
    'jpg' :"1",
    'avi' :"2",
    'png' :"1",
    "mov" : "2",
}
//onchange="checkeExtension(this.value,'submit');"
function checkeExtension(filename,submitId) {
    var re = /\..+$/;
    var ext = filename.match(re);
    var type = $("input[type='radio']:checked").val();
    var submitEl = document.getElementById(submitId);

    if (types[ext] == type) {
        submitEl.disabled = false;
        return true;
    } else {
        alert("Invalid file type, please select another file");
        submitEl.disabled = true;
        return false;
    }  
}
*/
    
  /**************************************************************************************************/  
     $("#contact_email_submit").click(function(){     
         $("#contact_email").submit();
         
     });
     
     /****************************item remove******************************************/
     
     function user_delete_ad(item_id) {
    var item_id = item_id;
    swal({
        title: "Are you sure?",
        text: "Are you want to remove this ad from ad list!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, remove it!",
        closeOnConfirm: false
    }, function () {
        $.post('api/user-deactive-post',
                {
                    item_id: item_id,                  
                }, function (data, status) {

            if (data.result == 'TRUE') {
                swal("Removed!", data.message + "!", "success");/************swite alert after  remove product***************************/
//                $(".my_fev_item_" + product_id).removeAttr('id');
//                $('.my_fev_item_' + product_id).attr("onclick", 'add_to_fev(' + product_id + ')');
                $("#my_ad_delete_" + item_id).remove();
                setTimeout(function () {
                    //    location.reload();
                }, 2000);

            } else {
                swal("Oops!", data.message, "error");
                setTimeout(function () {
                    //   location.reload();
                }, 2000);

                //alert(data.message);
            }
        });
    });
    //my_wish_delete_(product_id)
}
/* ************************************wishlist to move product on cart product******************************************************* */
     
     
     
     /********************************************************************/