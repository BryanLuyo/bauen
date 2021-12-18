/*
required related script section
*/
if (typeof jQuery === "undefined") {
  throw new Error("AdminLTE requires jQuery Developre js");
}
// JS_BASE_URL set the base path if needed
var calling_type=0;// 0=form post, 1= status change, 2=delete 3=select data change 
var curTrg='';
function servicecall(url,data){
	if(url=='undefined' || url==''){
		alert("Calling URL not set");
	}
	else{
	
		if (url.toLowerCase().indexOf(JS_BASE_URL) == 0){
			url = JS_BASE_URL+url;
		}
		var datatype='json';
		if(calling_type==3){
			datatype='html';
		}
		
		$.ajax({
			url:url,
			type:'post',
			dataType:datatype,
			data:data,
			beforeSend:function(){
				$(".sitepreloader").show();
			},
			success:function(response){
			
				console.log(response);
				
				if(calling_type==3){
					selectdatamanupulate(response);
				}
				else{
					ajaxresponsesection(response);
				}
			},
			error:function(error){
			
				console.log(error.responseText);
				
			},
			complete:function(){
				console.log("complete");
				$(".sitepreloader").hide();
			}
		});
	}
}

function ajaxresponsesection(response){
	var html="";
	if(response.status==1){
		html='<div class="box-body">\
		<div class="alert alert-success alert-dismissible">\
		<i class="icon fa fa-check"></i>'+response.message+'</div></div>';
	}
	else{
		html='<div class="box-body">\
		<div class="alert alert-danger alert-dismissible">\
		<i class="icon fa fa-ban"></i>'+response.message+'</div></div>';
	}
	
	$(".mr-alert").html(html).show();
	// change the html section 
	if(calling_type==1){
		// status change
		var is_blocked=response.is_blocked;
		var btnvw='';
		var url = $(curTrg).attr('url');
		if(is_blocked==0){
			// make unblock view
			btnvw="<i class='fa fa-unlock'></i> Unblock";
		}
		else{
			// make block view
			btnvw="<i class='fa fa-lock'></i> Block";
			url+='/1';
		}
		
		$(curTrg).html(btnvw).attr('href',url);
	}
	if(calling_type==2){
		if(response.status==1){
			var tdoby = $(curTrg).parents('tbody');
			$($(curTrg).parents('tr')).remove();
			if($(tdoby).find('tr').length==0){
				location.reload();
			}
		}
	}
	curTrg='';
	dismissalert();
}

function selectdatamanupulate(response){
	var target_selct = $(curTrg).attr('trgt_sel');
	$(".content ."+target_selct+"").html(response);
}

// status change section 
$(document).on('click','.mr-blockunblock',function(e){
	e.preventDefault();
	curTrg = $(e.currentTarget);
	var call_url = $(curTrg).attr('href');
	var tbl_name = $(curTrg).attr('tbl_name');
	var data={
		'is_ajax':1,
		'tbl_name':tbl_name
	};
	calling_type=1;// status changed
	servicecall(call_url,data);
});
// record delete section 
$(document).on('click','.mr-deleterow',function(e){
	e.preventDefault();
	
	curTrg = $(e.currentTarget);
	var call_url = $(curTrg).attr('href');
	var tbl_name = $(curTrg).attr('tbl_name');
	var data={
		'is_ajax':1,
		'tbl_name':tbl_name
	};
	calling_type=2;// delete action 
	if(confirm("Are you sure, You want to remove this record?")){
		servicecall(call_url,data);
	}
	else{
		curTrg='';
	}
});

$(document).on('change','.mr-onchange',function(e){
	e.preventDefault();
	curTrg = $(e.currentTarget);
	var tbl_name = $(curTrg).attr('trgt_sel');
	var call_url = JS_BASE_URL+'admins/get'+tbl_name;
	var flds = $(curTrg).attr('id');
	var fld_val=$(curTrg).val();
	var data={
		'is_ajax':1,
		fld:flds,
		fld_val:fld_val
	};
	calling_type=3;// select action 
	servicecall(call_url,data);
});

$(document).on('click','.mr-casetypeaddremove',function(e){
	e.preventDefault();
	curTrg = $(e.currentTarget);
	var call_url = $(curTrg).attr('href');
	var data={
		'is_ajax':1
	};
	calling_type=3;// select action 
	servicecall(call_url,data);
});