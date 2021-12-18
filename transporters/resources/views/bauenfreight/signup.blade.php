@extends('../master_layout/web')

@section('custom_js')
<script src="{{asset('public/assets/bauenfreight/my_custom_js.js?v=1.1.3')}}"></script>
<script>
$(document).ready(function () {
     $('input[type="radio"][name="a"]').change(function() {
         var selected_val = $('input[type="radio"][name="a"]:checked').val() ;
         if(selected_val == 1){
            $(".tab11").css("display", "block");
            $(".tab22").css("display", "none"); 
         }else{
            $(".tab11").css("display", "none");
            $(".tab22").css("display", "block");
         }
     });

    $('#company_name').hide()
    $('#industrytype_id').hide()
    $('#ruc_no').hide()

     const toggleCompanyFields = (show) => {
         if(show) {
             $('#company_name').show()
             $('#industrytype_id').show()
             $('#ruc_no').show()
         } else {
             $('#company_name').hide()
             $('#industrytype_id').hide()
             $('#ruc_no').hide()
         }
     }

     $('#is_company').change(function() {
        const isCompany = $(this).val()

        if(isCompany == 1) {
            toggleCompanyFields(true)
        } else if (isCompany == 0) {
            toggleCompanyFields(false)
        }
     })
  });
  
   document.getElementById("submit_company_signup_form").disabled = false;
   document.getElementById("submit_signup_form").disabled = false;

</script>
@endsection

@section('custom_css')
<style>
    input.form-control[readOnly] {
      background: #fff !important;
    }
</style>
@endsection

@section('title')
<title>Bauen | Conectando cargas con transportistas homologados - Bauen Freight SAC - Transporte de Carga, Fletes, Carga de Pago, Peru</title>
@endsection

@section('banner')
@endsection

@section('main_container')
<div class="container">
    <div class="registration">
        {{-- @if(Session::has('message'))
            <div class="alert alert-info" style= "margin-top : 30px ; text-align: center">
                <a class="close" data-dismiss="alert">×</a>
                {{Session::get('message')}}
                {{Session::forget('message')}}
            </div>
        @endif --}}
        @if(Session::has('message'))
            <div class="alert alert-info" style= "margin-top : 30px ; text-align: center">
                <a class="close" data-dismiss="alert">×</a>
                @if(Session::get('message') == 'Invalid phone number: The string supplied is too long to be a phone number.' || Session::get('message') == 'INVALID_PHONE_NUMBER : TOO_LONG')
                    El número de teléfono no es válido.
                @elseif(Session::get('message') == 'INVALID_PHONE_NUMBER : TOO_SHORT')
                    El número de teléfono es muy corto.
                @elseif(Session::get('message') == 'The email address is invalid.')
                    La cuenta de correo es inválida.
                @elseif(Session::get('message') == 'The email address is already in use by another account.')
                    El correo ya esta siendo usado por otra cuenta.
                @else
                    {{Session::get('message')}}
                @endif
            </div>
            {{Session::forget('message')}}
        @endif
        @php
            $selected_signup_type = 1;
            if(Session::has('signup_type_id') && Session::get('signup_type_id') == 2) {
                $selected_signup_type = 2;
            }
        @endphp
        <h5>{{trans('pdg.57')}}</h5>
            <div class="singtabs">
                <div class="user-type">
                    <label class="t1">
                        <input type="radio" name="a" id="select_tab_1" value="1" @if($selected_signup_type == 1) checked @endif><span></span>{{trans('pdg.58')}}</label> 
                    <label class="t2">
                        <input type="radio" id="select_tab_2" name="a" value="2" @if($selected_signup_type == 2) checked @endif><span></span>{{trans('pdg.59')}}</label>
                </div>

                <div class="tab11" @if($selected_signup_type == 2) style="display:none;" @endif>
                   <form id="signup_form" action="{{url('post-signup')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-black-tie" aria-hidden="true"></i></span>
                            <select name="is_company" id="is_company" class="form-control">
                                <option value="" selected disabled>Particular o Empresa</option>
                                <option value="0">Particular</option>
                                <option value="1">Empresa</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="company_name">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-building" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="company_name"  placeholder="Nombre de empresa"/>
                        </div>
                    </div>
                    <div class="form-group" id="industrytype_id">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                            <select name="industrytype_id" class="form-control">
                                <option value="" selected disabled>Industria a la que pertenece</option>
                                @foreach ($industry_id as $industry)
                                    <option value="{{$industry->industrytype_id}}">
                                        {{$industry->industrytype_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="ruc_no">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-id-card" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="ruc_no"  placeholder="RUC"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="first_name"  placeholder="{{trans('pdg.60')}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="last_name"  placeholder="{{trans('pdg.61')}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                            <input type="email" class="form-control" name="user_email"  placeholder="{{trans('pdg.62')}}" autocomplete="false" readonly onfocus="if (this.hasAttribute('readonly')) {
                                this.removeAttribute('readonly');
                                this.blur();    this.focus();  }"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-flag" aria-hidden="true"></i></span>
                            <select name="is_countri__" id="is_countri__" class="form-control">
                                <option value="" selected disabled>Pais</option>
                                <option value="51">Perú(+51)</option>
                                <option value="502">Guatemala(+502)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                            <input type="tel" class="form-control" name="phone_no" id="phone_no"  placeholder="{{trans('pdg.63')}}"/>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-slack" aria-hidden="true"></i></span>
                            <input type="tel" class="form-control" name="dni_no"   placeholder="DNI"/>
                        </div>
                    </div> --}}
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="password" id="password"  placeholder="{{trans('pdg.64')}}" autocomplete="false" readonly onfocus="if (this.hasAttribute('readonly')) {
                                this.removeAttribute('readonly');
                                this.blur();    this.focus();  }"/>
                        </div>
                    </div>
<!--                     <div class="form-group required">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="retypepassword"   placeholder="{{trans('pdg.66')}}"/>
                        </div>
                    </div> -->
                    <div class="form-group ">
                        <button type="submit" id="submit_signup_form" class="btn btn-primary btn-block login-button">{{trans('pdg.10')}}</button>
                    </div>
                    </form>
                </div>

                <div class="tab22" @if($selected_signup_type == 1) style="display:none !important;" @endif>
                   <form id="company_signup_form" action="{{url('post-company-signup')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-hospital-o" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="company_name"  placeholder="{{trans('pdg.76')}}" @if(Cookie::has('company_name')) value="{{Cookie::get('company_name')}}" @endif/>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <div class="input-group ">
                            <span class="input-group-addon">
                                <i class="fa fa-building-o" aria-hidden="true"></i>
                            </span>
                            <select name="industry_id" class="form-control" id="industry_id" placeholder="Industry">
                                <option value="0">{{trans('pdg.75')}}</option>
                                @if(!empty($industry_id)) @foreach($industry_id as $cat)
                                <option value="{{$cat->industrytype_id}}">{{$cat->industrytype_name}}</option>
                                @endforeach @endif
                            </select>
                        </div>
                    </div> -->
                    
                    {{-- <div class="form-group required">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-slack" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="RUC_no"  placeholder="RUC"/>
                        </div>
                    </div> --}}
                     <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="first_name"  placeholder="{{trans('pdg.60')}}" @if(Cookie::has('first_name')) value="{{Cookie::get('first_name')}}" @endif/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="last_name"  placeholder="{{trans('pdg.61')}}" @if(Cookie::has('last_name')) value="{{Cookie::get('last_name')}}" @endif/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="user_email"  placeholder="{{trans('pdg.62')}}" autocomplete="false" @if(Cookie::has('user_email')) value="{{Cookie::get('user_email')}}" @endif readonly onfocus="if (this.hasAttribute('readonly')) {
                                this.removeAttribute('readonly');
                                this.blur();    this.focus();  }"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-flag" aria-hidden="true"></i></span>
                            <select name="is_countri__" id="is_countri__" class="form-control">
                                <option value="" selected disabled>Pais</option>
                                <option value="51">Perú(+51)</option>
                                <option value="502">Guatemala(+502)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="phone_no" id="phone_no"  placeholder="{{trans('pdg.63')}}" @if(Cookie::has('phone_no')) value="{{Cookie::get('phone_no')}}" @endif/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="company_password" id="company_password"  placeholder="{{trans('pdg.64')}}" autocomplete="false" readonly onfocus="if (this.hasAttribute('readonly')) {
                                this.removeAttribute('readonly');
                                this.blur();    this.focus();  }"/>
                        </div>
                    </div>
   <!--                  <div class="form-group required">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="retypepassword" placeholder="{{trans('pdg.66')}}"/>
                        </div>
                    </div> -->
                    <div class="form-group ">
                        <button type="submit" id="submit_company_signup_form" class="btn btn-primary btn-block login-button">{{trans('pdg.10')}}</button>
                    </div>
                    </form>
                </div>
            </div>
    </div>


</div>

@endsection