@extends('../master_layout/web')

@section('custom_js')
@endsection

@section('custom_css')
@endsection

@section('title')
<title>Bauen | Verifique su cuenta para continuar</title>
@endsection

@section('main_container')
<div class="right-body">
    <div class="shipper-home wow fadeInUp">
        <div class="container">
          
          @if($valid_data == true)
            <div class="login">
              @if(Session::has('message'))
                  <div class="alert alert-info">
                      <a class="close" data-dismiss="alert">x</a>
                      {{Session::get('message')}}
                      {{Session::forget('message')}}
                  </div>
              @endif

              <h5 class="text-center">Activar cuenta</h5>
              <p class="text-center mb-4">Para culminar con la activación de su cuenta debe generar una contraseña en el siguiente formulario.</p>
              <form action="#">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" required>
                  </div>
                </div>
                <button type="submit" class="btn btn-custom ml-0" style="width:100%">Ingresar</button>
              </form>
            </div>
          @else
            <div class="alert alert-danger my-5">
              Es probable que su cuenta ya haya sido activada. Intente ingresar o póngase en contacto con el área de atención al cliente.
            </div>
          @endif
        </div>
    </div>
</div>
@endsection