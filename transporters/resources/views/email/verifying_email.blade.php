<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
        $messageTitle = 'Estimado/a '.$data['name'] ;
        $message1 = 'Bienvenido y gracias por registrarse. Haga clic en el siguiente link para activar su cuenta o ingrese el siguiente código en la plataforma.';
        $messageBtn = 'Verificar cuenta';
        $message2 = "Si usted no se ha registrado con nosotros, por favor, ignore este correo electrónico. Si tiene algún problema, por favor no dude en escribirnos a: ";
        $messageFooter = 'Saludos,';
        $messageFooter2 = 'The '.config('constants.APP_NAME').'Bauen Team.';
        
        if(Session::has('locale') && (Session::get('locale')=="en" || Session::get('locale')=="ar")){ 
            $messageTitle = 'Mr.'.$data['name'] ;
            $message1 = 'Welcome and thank you for registering. 
            Click the link below to activate your account to login.';
            
            $messageBtn = 'Click here to Verify';
            $message2 = "If you have not registered with us, please ignore this email.
            If at any point you are having trouble, please do not hesitate to contact us by emailing us to: sergio@bauenfreight.com ";
            
            $messageFooter = 'Best regards,';
            $messageFooter2 = 'The '.config('constants.APP_NAME').'Bauen Team.';
        }
        ?>
</head>

<body bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none;">
    <table style="width:100%" border="0">
        <tr style="width:100%">
            <td style="width:8%"></td>
            <td>
                <div style="width:100%;">
                    <div style="background:#b72c43;font-family:Arial,Helvetica,sans-serif;width:100%;">
                        <div style="text-aling:center;padding:14px 20px 6px">
                            <img src="{{asset('public/assets/bauenfreight/images/logo.png')}}" />
                        </div>
                    </div>

                    <div style="height:40px;width:100%;background:#fff" class="espaciodiv"></div>

                    <div style="background:#fff;font-family:Arial,Helvetica,sans-serif;width:100%;">
                        <div style="text-aling:left">
                            <p style="color:#404040;font-size:16px;font-weight:bold;line-height:20px;padding:0;margin:0;text-align:justify">{{$messageTitle}},</p>
                        </div>
                        <div style="height:20px;width:100%;background:#fff" class="espaciodiv"></div>
                        <div style="text-aling:left">
                            <p style="font-size:16px;line-height:20px;padding:0;margin:0;margin-bottom:16px;text-align:justify">
                                {{$message1}}

                            </p>
                        </div>
                        <div style="height:20px;width:100%;background:#fff" class="espaciodiv"></div>
                        <div style="width:100%;text-align:center">
                            <table style="width:100%">
                                <tr style="width:100%">
                                    <td aling="center">
                                        <h3><?php echo 'Código: '.$data['verification_code']; ?> </h3>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="height:20px;width:100%;background:#fff" class="espaciodiv"></div>
                        <div style="text-aling:left">
                            <p style="font-size:16px;line-height:20px;padding:0;margin:0;text-align:justify">
                                {{$message2}} <br>
                                <a target="_blank" style="color:#0070bf;font-weight:normal" href="mailto:{{ config('constants.SUPPORT_EMAIL') }}">
                                                                {{ config('constants.SUPPORT_EMAIL') }}    
                                </a>
                            </p>
                        </div>
                        <div style="height:20px;width:100%;background:#fff" class="espaciodiv"></div>
                        <div style="text-aling:left">
                            <p style="font-size:16px;line-height:20px;padding:0;margin:0">
                                {{$messageFooter}}</p>
                            <p style="font-size:16px;line-height:20px;padding:0;margin:0">
                                {{$messageFooter2}}</p>
                        </div>
                        <div style="height:20px;width:100%;background:#fff" class="espaciodiv"></div>
                        <div style="height:20px;width:100%;background:#fff" class="espaciodiv"></div>

                    </div>
                </div>
            </td>
            <td style="width:8%"></td>
        </tr>
    </table>
</body>

</html>