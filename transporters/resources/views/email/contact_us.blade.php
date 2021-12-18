<!DOCTYPE html>
<html>
   <head>
       <meta name="viewport" content="width=device-width">
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   </head>
   <body bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; margin: 0; padding: 0;">
       <div style="font-family:Arial,Helvetica,sans-serif">
           <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#eeeeee" align="center">
               <tbody>
                   <tr>
                       <td>
                           <table width="690" cellspacing="0" cellpadding="0" border="0" bgcolor="#eeeeee" align="center">
                               <tbody>
                                   <tr>
                                       <td height="110" bgcolor="#eeeeee" align="center" style="padding:0;margin:0;font-size:1;line-height:0" colspan="3">
                                           <table width="690" cellspacing="0" cellpadding="0" border="0" align="center">
                                               <tbody>
                                                   <tr>
                                                       <td colspan="3" valign="middle" align="left" style="padding:0;margin:0;font-size:1;line-height:0">
                                                           <img src="{{asset('public/assets/bauenfreight/images/logo.png')}}" style="width:25%;" height="100" />
                                                       </td>
                                                   </tr>
                                               </tbody>
                                           </table>
                                       </td>
                                   </tr>
                                   <tr>
                                       <td height="30"></td>
                                   </tr>
                                   <tr bgcolor="#ffffff">
                                       <td width="30"></td>
                                       <td>
                                           <table width="630" cellspacing="0" cellpadding="0" border="0" align="center">
                                               <tbody>
                                                   <tr>
                                                       <td width="630" height="50" style="padding:0;margin:0;font-size:1;line-height:0" colspan="3"></td>
                                                   </tr>
                                                   <tr>
                                                       <td style="padding:0;margin:0;font-size:1;line-height:0" colspan="3">
                                                           <p style="color:#404040;font-size:16px;font-weight:bold;line-height:20px;padding:0;margin:0">Hi ,</p>
                                                       </td>
                                                   </tr>
                                                   <tr>
                                                       <td height="20"></td>
                                                   </tr>
                                                   <tr>
                                                       <td colspan="3">
                                                           <p style="font-size:16px;line-height:20px;padding:0;margin:0;margin-bottom:16px">
                                                          {{ $data['client_message']}} .<br>
                                                           
                                                           </p>
                                                       </td>
                                                   </tr>
                                                   <tr>
<td height="50" style="padding:0;margin:0;font-size:1;line-height:0" colspan="3"><img width="100%" height="1" src="{{asset('public/assets/images/unnamed.gif')}}" class=""></td>
                                                   </tr>
                                                   <tr>
                                                       <td width="185"></td>
                                                       <td >
                                                           <table width="260" cellspacing="0" cellpadding="0" bgcolor="#51B9E8" align="left" style="font-family:Arial,sans-serif;border-radius:2px;height:50px">
                                                               <tbody>
                                                                   <tr>
                                                                       <td align="center" style="margin:0;line-height:1em;font-size:16px;text-align:center" >
                                                                          
                                                                           {{ $data['client_name']}} person conctact with us. <br>
                                                                           His mail id : {{ $data['client_email']}} <br>
                                                                          
                                                                       </td>
                                                                   </tr>
                                                               </tbody>
                                                           </table>
                                                       </td>
                                                       <td width="30"></td>
                                                   </tr>
                                                   <tr>
                                                       <td height="50" style="padding:0;margin:0;font-size:1;line-height:0" colspan="3"><img width="100%" height="1" src="{{asset('public/assets/images/unnamed.gif')}}" class=""></td>
                                                   </tr>                                                   
                                                   <tr>
                                                       <td style="padding:0;margin:0;font-size:1;line-height:0" colspan="3">
                                                           <p style="font-size:16px;line-height:20px;padding:0;margin:0">
                                                           If you don't request to Contact Us, please ignore this email. Don't worry. Your password will be safe.<br>
                                                           If at any point you are having trouble, please do not hesitate to contact us by emailing <a target="_blank" style="color:#0070bf;font-weight:normal" href="mailto:{{ config('constants.SUPPORT_EMAIL') }}">{{ config('constants.SUPPORT_EMAIL') }}</a>. 
                                                           </p>
                                                       </td>
                                                   </tr>
                                                   <tr>
                                                       <td height="30" style="padding:0;margin:0;font-size:1;line-height:0" colspan="3"></td>
                                                   </tr>
                                                   <tr>
                                                       <td>
                                                           <p style="font-size:16px;line-height:20px;padding:0;margin:0">Regards,</p>
                                                           <p style="font-size:16px;line-height:20px;padding:0;margin:0">The {{ config('constants.APP_NAME') }} Team.</p>
                                                       </td>
                                                   </tr>
                                                   <tr>
                                                       <td height="90" style="padding:0;margin:0;font-size:1;line-height:0" colspan="3"></td>
                                                   </tr>
                                               </tbody>
                                           </table>
                                       </td>
                                       <td width="30" bgcolor="#ffffff"></td>
                                   </tr>
                               </tbody>
                           </table>
                       </td>
                   </tr>
                   <tr>
                       <td>
<table width="690" cellspacing="0" cellpadding="0" border="0" bgcolor="#eeeeee" align="center">
                               <tbody>
                                   <tr>
                                       <td height="30" colspan="2"></td>
                                   </tr>
                                   <tr>
                                   
                                   </tr>
                                   <tr>
                                       <td height="5" colspan="2"></td>
                                   </tr>
                                   <tr>
                                       <td height="80" colspan="2"></td>
                                   </tr>
                               </tbody>
                           </table>
                       </td>
                   </tr>
               </tbody>
           </table>
       </div>
   </body>
</html>