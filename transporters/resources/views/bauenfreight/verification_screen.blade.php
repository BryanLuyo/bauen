@extends('../master_layout/web_shipper_no_sidebar')

@section('custom_js')
    <script>
        $(window).ready(function() {

            setTimeout(() => {
                $('.code-input').first().focus()
            }, 1000)

            $('.code-input').keyup(function(e) {
                if( $.isNumeric(String.fromCharCode(e.which-48)) || $.isNumeric(String.fromCharCode(e.which)) ) {
                    console.log('is number', String.fromCharCode(e.which))
                    if($(this).val().length > 1) { // Sólo un número por input
                        let finalVal = $(this).val().substring(0, 1)
                        $(this).val(finalVal)
                    }

                    if($(this).val().length <= 0) return; // Al menos un dígito para continuar con el código
                    
                    let nextElement = $(this).next();
                    if(nextElement.hasClass('code-input')) {
                        nextElement.removeAttr('disabled')
                        nextElement.focus()
                    } else {
                        $('button[type=submit]').focus()
                    }
                } else if(e.which === 8) {
                    console.log('is delete', String.fromCharCode(e.which))
                    let prevElement = $(this).prev();
                    if(prevElement.hasClass('code-input')) {
                        prevElement.focus()
                        $(this).attr('disabled', true)
                    }
                } else {
                    console.log('else', String.fromCharCode(e.which))
                    $(this).val('')
                }
            })
        })

        const resendCode = (e) => {
            e.preventDefault();
            if(confirm('¿Realmente desea que reenviemos el código?')) {
                window.location="{{URL::to('api/user-active-account-resend-code')}}";
            } else {
                console.log('no se reenvia')
            }
        }
    </script>
@endsection

@section('custom_css')
<style type="text/css">
    input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type="number"] {
        -moz-appearance: textfield;
    }
    .verification-container {
        max-width: 460px;
        width: 100%;
        margin: 50px auto;
        text-align: center;
    }
    
    .code-input-container {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .code-input {
        width: 45px;
        height: 50px;
        margin: 0 4px;
        text-align: center;
        font-size: 20px;
    }

    .text-warning {
        color: #B72C43;
    }
</style>
@endsection

@section('title')
<title>Bauen | Verifique su cuenta para continuar</title>
@endsection

@section('main_container')
<div class="right-body">
    <div class="shipper-home wow fadeInUp">
        <div class="verification-container">
            @if(Session::has('message'))
                <div class="alert alert-info">
                    <a class="close" data-dismiss="alert">x</a>
                    {{Session::get('message')}}
                    {{Session::forget('message')}}
                </div>
            @endif
            <div class="request-quote">
                <h5>CÓDIGO DE VERIFICACIÓN</h5>
                <p>Ingrese el código de verificación enviado por e-mail y sms aquí</p>
            </div>
            <div class="transit-request">
                <form action="{{url('api/user-active-account')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_id" value="{{ Session::get('web_user_id') }}">
                    <div class="code-input-container">
                        <input class="code-input form-control" id="code-input" type="number" name="code01" max="9" size="1">
                        <input disabled class="code-input form-control" type="number" name="code02" max="9" size="1">
                        <input disabled class="code-input form-control" type="number" name="code03" max="9" size="1">
                        <input disabled class="code-input form-control" type="number" name="code04" max="9" size="1">
                        <input disabled class="code-input form-control" type="number" name="code05" max="9" size="1">
                        <input disabled class="code-input form-control" type="number" name="code06" max="9" size="1">
                    </div>
                    <p><a class="text-warning" href="#" onclick="resendCode(event)">Re enviar</a> código de verificación</p>
                    <div class="request-quote">
                        <button type="submit" class="btn btn-primary btn-block raq-button btn-lg">Ingresar código</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection