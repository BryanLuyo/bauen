@extends('../master_layout/web')

@section('custom_js')
<script src="{{asset('public/assets/bauenfreight/my_custom_js.js')}}"></script>
@endsection




@section('title')
<title>Bauen | Conectando cargas con transportistas homologados - Bauen Freight SAC - Transporte de Carga, Fletes, Carga de Pago, Peru</title>
@endsection



@section('banner')
@endsection

@section('main_container')
  <div class="container">
  <div class="login">
       @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
           {{Session::get('message')}}<strong></strong>
            {{Session::forget('message')}}
        </div>
        @endif
  <h5>{{trans('pdg.94')}}</h5>
  <form id="login_form" action="{{url('post-login')}}" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="form-group">
          <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
              <input type="email" class="form-control" name="user_email" value="@if(Cookie::has('user_email')) {{Cookie::get('user_email')}} @endif" placeholder="{{trans('pdg.62')}}"/>
          </div>
      </div>
      <div class="form-group">
              <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
              <input type="password" class="form-control" name="password" placeholder="{{trans('pdg.64')}}"/>
              </div>
      </div>  	
      <div class="form-group ">
        <button type="submit" id="submit_login_form" class="btn btn-primary btn-block login-button">{{trans('pdg.95')}}</button>
      </div>
	    <div class="row remember">
        <div class="col-sm-6 col-xs-12">
          <!--   Remember  <label class="toggle-check">
                <input type="checkbox"><span></span>
            </label>-->
        </div>
        <div class="col-sm-6 col-xs-12 text-right">
            <a href="#" data-toggle="modal" data-target="#forgot_password" class="btn-editpic">{{trans('pdg.96')}}</a>
        </div>
      </div>
      <div class="create-account">
          <a href="{{url('signup')}}">{{trans('pdg.97')}}</a>
      </div>
  </form>
  
  </div>
  </div>
<!--------------------------------change password---------------------------------->
<div id="forgot_password" class="modal fade"  role="dialog">
  <div class="modal-dialog">

   <!--  Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Recuperar contraseña</h4>
      </div>
        <form action="{{url('user-forgot-password')}}" method="post" name="Forgot_Password">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">               
            <div class="modal-body ">         
              <div class="form-horizontal">
                <div class="control-group">
                   <label class="control-label label_class_css" for="">Correo Electrónico</label>
                   <input type="email" class="form-control"  name="user_email">                
                </div>
            </div>
            </div>
          <div class="modal-footer">
            <input type="submit" class="btn btn-custom" name="submit_forgot_password" value="Enviar">           
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </form>
    </div>
  </div>
</div>

<!--------------------------------change password---------------------------------->

@endsection