<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class ConactController extends Controller
{
    public function sendemail(Request $request)
    {

        $name= $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $msg = $request->msg;
        $emailb = "hello@kasunanuranga.me";
        $mail = new PHPMailer(true);
        date_default_timezone_set('Asia/Colombo');
        $tdate= date("Y/m/d");
        $ttime = date('H.i A ');
        $sub = $request->name." Send Massage";
        $mail->isSMTP();
        $mail->CharSet = "utf-8";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "kasunanuranga.me";
        $mail->Port = 465;
        $mail->Username = "hello@kasunanuranga.me";
        $mail->Password = "Nightmayer1997";
        $mail->setFrom('hello@kasunanuranga.me', 'Automailer System');
        $mail->Subject =  $sub;
        $mail->MsgHTML("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
            <html xmlns='http://www.w3.org/1999/xhtml'>
            <head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>

            <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
            </head>
            <body style='margin: 0; padding: 0;'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr>
               <td style='padding: 10px 0 30px 0;'>
                   <table align='center' border='0' cellpadding='0' cellspacing='0' width='600' style='border: 1px solid #cccccc; border-collapse: collapse;'>
                       <tr>
                           <td align='center'>
                               <img src='https://baileys.lk/img/mailheader.jpg' alt='Email' width='600' height='350' style='display: block;' />
                           </td>
                       </tr>
                       <tr>
                           <td bgcolor='#ffffff' style='padding: 40px 30px 40px 30px;'>
                               <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                   <tr>
                                       <td style='padding: 20px 0 30px 0; color: #2f2e2e; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;'>
                                         Details of customer message,<br /><br />

                                         <strong>Customer Name :</strong>$name<br />
                                         <strong>Customer email :</strong>$email<br />
                                         <strong>Phone Number :</strong>$phone<br />
                                         <strong>Customer Message :</strong>$msg<br />



                            <br /><br /><br /><br />
                                       </td>
                                   </tr>

                               </table>
                           </td>
                       </tr>
                       <tr>
                       <table>
                           <td bgcolor='#2f2e2e' style='padding: 30px 30px 30px 30px;'>
                               <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                               <tr>
                               <td style='color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;' width='75%'>
                                 Copyright &copy;<script>document.write(new Date().getFullYear());</script><br/>
                                 <font color='#ffffff'>All rights reserved | This Email is made with <font color= 'orange' style='color:#ffb005;'>by techtiko.lk</font></font>
                                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                 </td>
                               </tr>
                               </table>
                           </td>
                       </tr>
                   </table>
               </td>
            </tr>
            </table>

            </body>
            </html>");
        $mail->addAddress($emailb, "Customer message");
        $mail->send();
        return redirect()->back();


    }
}
