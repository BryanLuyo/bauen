@extends('../master_layout/web_shipper')

@section('title')
<title>Bauen | Administradores</title>
@endsection

@section('banner')
@endsection

@section('custom_js')
  <script>
  const formWaiting = boolean => {
    if(boolean == true) {
      submitBtn.classList.add('disabled');
      submitBtn.innerText = 'Espere...';
    } else {
      submitBtn.classList.remove('disabled');
      submitBtn.innerText = 'Agregar administrador';
    }

    return;
  }

  const restartErrorMsgs = () => {
    firstNameError.classList.add('d-none');
    lastNameError.classList.add('d-none');
    emailError.classList.add('d-none');
  }

  const validateEmail = email => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }

  const validateCelphone = phone => {
    const parsedPhone = parseInt(phone);
    return parsedPhone.toString().length == 9 && !isNaN(parsedPhone)
  }

  createAdmin.addEventListener('submit', function(e) {
    e.preventDefault();
    restartErrorMsgs();

    if(firstName.value == '') {
      firstNameError.classList.remove('d-none');
    }

    if(lastName.value == '') {
      lastNameError.classList.remove('d-none');
    }

    if(!validateEmail(email.value)) {
      emailError.classList.remove('d-none');
    }

    if(!validateCelphone(phoneNo.value)) {
      phoneNoError.classList.remove('d-none');
    }

    if(firstName.value == '' || lastName.value == '' || !validateEmail(email.value) || !validateCelphone(phoneNo.value)) return false;
    
    formWaiting(true);
    createAdmin.submit();
  });

  $(document).ready(function () {
    // Delete modal
    $('.delete-btn').on('click', function() {
      const subadminId = $(this).data('subadmin-id');
      
      $('#user_id').val(subadminId);
      $('#removeModal').modal('show');
    })
  });
  </script>
@endsection

@section('main_container')
<div class="right-body">
  <div class="shipper-home">
    <div class="tab-content">
      <div class="row">
        <div class="col-xs-12 pb-4">
            <div class="main-breadcrumb">
                Dashboard <i class="fa fa-angle-right"></i> Administradores
            </div>
        </div>

        <div class="col-xs-12 mb-4">
          <button class="btn btn-custom ml-0" data-toggle="modal" data-target="#addModal">Agregar administrador</button>
        </div>
        
        @if(Session::has('message'))
          <div class="col-xs-12 mb-4">
            <div class="alert alert-info mb-0">
              <a class="close" data-dismiss="alert">×</a>
              {{Session::get('message')}}
              {{Session::forget('message')}}
            </div>
          </div>
        @endif

        <div class="col-xs-12">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th style="width:70px">ID</th>
                  <th>Nombre</th>
                  <th>Correo</th>
                  <th style="width:90px">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @if( empty($subadmins) )
                  <tr>
                    <td colspan="4">No se encontraron resultados.</td>
                  </tr>
                @else
                  @foreach($subadmins as $subadmin)
                    <tr>
                      <td>{{ $subadmin->user_id }}</td>
                      <td>{{ $subadmin->first_name }} {{ $subadmin->last_name }}</td>
                      <td>{{ $subadmin->email }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          {{-- <button class="btn btn-custom ml-0 mr-2">Editar</button> --}}
                          <button class="btn btn-light delete-btn" data-subadmin-id="{{ $subadmin->user_id }}">Eliminar</button>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
	</div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Agregar Administrador</h4>
        </div>
        <div class="modal-body">
          <form action="{{ url('subadmins') }}" method="POST" id="createAdmin">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
              <div class="col-xs-12 mb-2">
                <div>
                  <input type="text" class="form-control" placeholder="Nombres" name="first_name" id="firstName">
                  <small class="text-danger d-none" id="firstNameError">Debe ingresar este campo</small>
                </div>
              </div>
              <div class="col-xs-12 mb-2">
                <div>
                  <input type="text" class="form-control" placeholder="Apellidos" name="last_name" id="lastName">
                  <small class="text-danger d-none" id="lastNameError">Debe ingresar este campo</small>
                </div>
              </div>
              <div class="col-xs-12 mb-2">
                <div>
                  <input type="text" class="form-control" placeholder="Correo electrónico" name="email" id="email">
                  <small class="text-danger d-none" id="emailError">Ingrese un correo válido</small>
                </div>
              </div>
              <div class="col-xs-12 mb-2">
                <div>
                  <input type="tel" class="form-control" placeholder="Teléfono celular" name="phone_no" id="phoneNo">
                  <small class="text-danger d-none" id="phoneNoError">Ingrese un número de celular válido</small>
                </div>
              </div>
              <div class="col-xs-12">
                <button type="submit" class="btn btn-custom ml-0" id="submitBtn" style="width:100%">Crear administrador</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<!-- Remove Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-body text-center">
          ¿Realmente desea eliminar a este administrador?
        </div>
        <div class="modal-footer text-center">
          <form action="{{ url('subadmins/delete') }}" method="POST" class="d-inline-block">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="user_id" name="user_id">
            <button type="submit" class="btn btn-custom ml-0">Eliminar</button>
          </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

@endsection