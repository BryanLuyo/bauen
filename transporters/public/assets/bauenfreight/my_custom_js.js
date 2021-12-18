$().ready(function(){
    

    $("#login_form").validate({
        rules:{
            user_email : "required",
            password : {
                        required :true ,
                       // minlength : 6
                        }
        },
        messages : {
            user_email : "Por favor, ingrese un correo válido",
            password : {
                required : "Por favor, ingrese su contraseña",
               //minlength : ""
            }
        }
    });
    
    
    $("#signup_form").validate({
		invalidHandler: function(event, validator) {
			// 'this' refers to the form
			var errors = validator.numberOfInvalids();
			if (errors) { 
			   document.getElementById("submit_signup_form").disabled = false;
			}
		},
        rules:{
            user_email : "required",
            is_countri__ : "required",
            phone_no : "required",
            password : {
                        required :true ,
                        minlength : 6
                        },
            retypepassword : {
                        required :true ,
                        equalTo : "#password"
                        },
           first_name : "required",
           is_company: "required",
           company_name: {
               required: function(element) {
                   return $('#is_company').val() == 1
               }
           },
           industrytype_id: {
               required: function(element) {
                   return $('#is_company').val() == 1
               }
           },
           ruc_no: {
               required: function(element) {
                   return $('#is_company').val() == 1
               }
           }
          
        },
        messages : {
            user_email : "Por favor, ingrese una dirección de correo válida",
            phone_no : "Por favor, ingrese un número válido",
            is_countri__ : "Seleccione un Pais",
            password : {
                required : "Por favor, ingrese su contraseña",
                minlength : "Lo sentimos, su contraseña debe contener al menos 6 caractéres"
            },
            retypepassword : {
                required : "Please repeat your password",
                equalTo : "Your retypepassword must be same as your password"
            },
             first_name : "Por favor, ingrese su nombre",
             is_company: "Seleccione el tipo de generador",
             company_name: "Ingrese el nombre de la empresa",
             industrytype_id: "Seleccione el tipo de industria",
             ruc_no: "Ingrese su número de RUC"
        }
    });
    
    
    $("#company_signup_form").validate({
		invalidHandler: function(event, validator) {
			// 'this' refers to the form
			var errors = validator.numberOfInvalids();
			if (errors) { 
			   document.getElementById("submit_company_signup_form").disabled = false;
			}
		},
        rules:{
        	is_countri__ : "required",
            user_email : "required",
            phone_no : "required",
           company_password : {
                        required :true ,
                        minlength : 6
                        },
            retypepassword : {
                        required :true ,
                        equalTo : "#company_password"
                        },
           first_name : "required",
          
        },
        messages : {
            user_email : "Por favor, ingrese una cuenta de correo electrónico válida",
            is_countri__ : "Seleccione un Pais",
            phone_no : "Por favor, ingrese un número válido",
            company_password : {
                required : "Por favor, ingrese su contraseña",
                minlength : "Su contraseña debe contener al menos 6 caractéres"
            },
            retypepassword : {
                required : "Please repeat your password",
                equalTo : "Your retypepassword must be same as your password"
            },
             first_name : "Por favor, ingrese su nombre",
        }
    });
     $("#your_request").validate({
		invalidHandler: function(event, validator) {
			// 'this' refers to the form
			var errors = validator.numberOfInvalids();
			if (errors) {
				
			   //document.getElementById('id02').style.display='none'; 		
			   document.getElementById("submit_your_request").disabled = false;
			   
			}
		},
        ignore: [],
        rules:{
            pickup_location : "required",
           dropoff_location : {
                        required :true ,
                            },
            pickup_date : {
                        required :true ,
                        },
            pickup_time : "required",
            trailer_id : {
            		required :true,
            		min: 1
            		},
            loadtype_id : {
            		required :true,
            		min: 1
            		},
            weight : "required",
            request_amount: "required",
            size : "required",
            description : "required"
          
        },
        messages : {
            pickup_location : "Por favor, ingrese el Punto de Origen",
            dropoff_location : {
                required : "Por favor, ingrese el Punto de Destino",
                          },
            pickup_date : {
                required : "Por favor, ingrese la fecha de recojo",
                            },
             pickup_time : "Por favor, ingrese la fecha de recojo",
             trailer_id : "Por favor, ingrese el tipo de trailer requerido",
             loadtype_id : "Por favor, ingrese el tipo de carga que desea trasladar",
             weight : "Por favor, ingrese el peso de la carga",
             size : "Por favor, ingrese las dimensiones de la carga",
             description : "Por favor, ingrese las instrucciones de la carga, terminos de pago o comentarios en este recuadro",
        }
    });
    
    
    });
	
	
    $("#submit_your_request").click(function(){
  
       //confirmEnviar();
	   initCF();
    });
	
	// popup tiempo cierre de fletes
	
	function initCF() {
		
	  document.getElementById("btnC").disabled = false;
	  document.getElementById("btnFCN").disabled = false;
	
	  document.getElementById('id02').style.display='block'; 
	
	} 
	
	
	$("#btnFC").click(function(){	
 
	   initCF1();
	   
	   return false;
	   
    });
	
	$("#btnC").click(function(){

       initCF1Normal();		
  
       confirmEnviar();
	   
    });
	
	$("#btnFCN").click(function(){
		
	   initCF1Normal();
  
       confirmEnviar();
	   
    });
	
	function initCF1Normal() {
		
		document.getElementById('id02').style.display='none'; 
		
	    document.getElementById('idText1').style.display='block';
		document.getElementById('idText2').style.display='block';	  
		document.getElementById('btnFC').style.display='block';
		document.getElementById('btnFCN').style.display='block';	
		
		document.getElementById('idText3').style.display='none';
		document.getElementById('time_id').style.display='none';
		document.getElementById('btnC').style.display='none'; 
		document.getElementById('rFC').style.display='none';
		
	}
	
	
	
	
	function confirmEnviar() {
		
      document.getElementById("submit_your_request").disabled = true;
	  document.getElementById("btnC").disabled = true;
	  document.getElementById("btnFCN").disabled = true;
	  
      //document.getElementById("submit_your_request").value = "Enviando...";
      console.log("form_oki");
      $("#your_request").submit();
      /* setTimeout(function(){
        document.getElementById("submit_your_request").disabled = false;
        //document.getElementById("submit_your_request").value = "Enviar";
      }, 10000); */
      return false;
    }
	
	if (document.getElementById("close_time")){
		document.getElementById("close_time").value =  "";
	}
	
	if (document.getElementById("btnC")){
		document.getElementById("btnC").disabled = false;
	}

    if (document.getElementById("btnFCN")){
		document.getElementById("btnFCN").disabled = false;
	}		
	
	
	
	
	
	function initCF1() {
		
	  document.getElementById('idText1').style.display='none';
      document.getElementById('idText2').style.display='none';	  
	  document.getElementById('btnFC').style.display='none';
	  document.getElementById('btnFCN').style.display='none';	
	
	  document.getElementById('idText3').style.display='block';
	  document.getElementById('time_id').style.display='block';
	  document.getElementById('btnC').style.display='block'; 
	  document.getElementById('rFC').style.display='block';
	  
	  
	  document.getElementById('fh').innerHTML =  fechaActual(horas);
	  document.getElementById('fhc').innerHTML =  fechaActual(24);
	  document.getElementById("close_time").value =  setFechaActual(24);
	  
	}
	
	var currentdate; 
	var dia;
	var mes;
	var anio;
	var hora;
    var minutos;
	var horas = 0;
	//var datetime;

    function addHoras (horas) {
		currentdate = new Date(); 
		currentdate.setHours(currentdate.getHours() + horas); 
	} 	
	
	function fechaFormato () {
		
	    dia = currentdate.getDate();
	    mes = (currentdate.getMonth()+1);
	    anio = currentdate.getFullYear();
	    hora = currentdate.getHours();
        minutos = currentdate.getMinutes(); 
		
		if ((dia >= 0)&&(dia <= 9)){ 
		  dia="0"+dia; 
		}
		
		if ((mes >= 0)&&(mes <= 9)){ 
		  mes="0"+mes; 
		}
		
		if ((hora >= 0)&&(hora <= 9)){ 
		  hora="0"+hora; 
		}
		
		if ((minutos >= 0)&&(minutos <= 9)){ 
		  minutos="0"+minutos; 
		}
		
	}
	
	
    function  fechaActual(horas) {	
	
	  addHoras(horas);
	
	  fechaFormato();
		
      var datetime = dia + "/" 
       + mes + "/" 
       + anio + " a las " 
       + hora + ":" 
       + minutos;
	
	   return datetime;
	   
	}
	
	function setFechaActual(horas) {
		
		var datetime = anio + "-" 
       + mes + "-" 
       + dia + " " 
       + hora + ":" 
       + minutos;

	   return datetime;
		
	}
	
	
    function cambiaTime() {
		 
		 var horas = parseInt(document.getElementById("time_id").value);
		 
		 var horas1 = parseInt(document.getElementById("ingresaNum").value);
		 
         document.getElementById("fhc").innerHTML = fechaActual(horas);
		 
		 document.getElementById("close_time").value =  setFechaActual(horas);
		 
		 if(horas == 0) {
			 document.getElementById('displayValue').style.display='block';
			 document.getElementById('time_id').style.display='none';
			 document.getElementById('fh').innerHTML =  fechaActual(horas);
			 
			 if(isNaN(horas1)) {
				 horas1 = 0;
		     }			 	
	         
	         document.getElementById('fhc').innerHTML =  fechaActual(horas1);
			 
			 document.getElementById("close_time").value =  setFechaActual(horas1);
			 
		 }
		 
		 
	 }
	 
	 function cerrarme() {
		 
		 document.getElementById('displayValue').style.display='none';
	     document.getElementById('time_id').style.display='block';
		 
		 document.getElementById("time_id").value = 24;
		 document.getElementById('fh').innerHTML =  fechaActual(horas);
	     document.getElementById('fhc').innerHTML =  fechaActual(24);
		 document.getElementById("close_time").value =  setFechaActual(horas);
		
	 }
	 
	 function cambioTextNum() {

		 var horas = parseInt(document.getElementById("ingresaNum").value);
		 
		 if(isNaN(horas)) {
			  horas = 0;
		 }
		  
		 document.getElementById("fhc").innerHTML = fechaActual(horas);
		 
		 document.getElementById("close_time").value =  setFechaActual(horas);
		  
	 }
	
	


    $("#update_your_request").click(function(){
         console.log("actualiza");
         $("#your_request").submit();
      });    


    $("#submit_login_form").click(function(){
		$("#login_form").submit();
    });

    $("#submit_signup_form").click(function(){
        //$("#signup_form").submit();
		confirmEnviar1();
    });
    
    $("#submit_company_signup_form").click(function(){
       // console.log("form_ok");
       //$("#company_signup_form").submit();
	   confirmEnviar2();
    });
	
	
	function confirmEnviar1() {
      document.getElementById("submit_signup_form").disabled = true;
      console.log("form_oki1");
      $("#signup_form").submit();
      return false;
    }
	
	
	function confirmEnviar2() {
      document.getElementById("submit_company_signup_form").disabled = true;
      console.log("form_oki2");
	  $("#company_signup_form").submit();
      return false;
    }
	
	
	
	