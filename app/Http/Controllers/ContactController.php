<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class ContactController extends Controller
{
    public function sendmail(Request $request): \Illuminate\Http\RedirectResponse
    {

        $name= $request->form_name;
        $email = $request->form_email;
        $phone = $request->form_phone;
        $msg = $request->msg;
        $category = $request->form_need;
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
        $mail->Host = "hello@kasunanuranga.me";
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

            </html>");
        $mail->addAddress($emailb, "Customer message");
        $mail->send();

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = "utf-8";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "hello@kasunanuranga.me";
        $mail->Port = 465;
        $mail->Username = "hello@kasunanuranga.me";
        $mail->Password = "Nightmayer1997";
        $mail->setFrom('hello@kasunanuranga.me', 'kasunanuranga.me');
        $mail->Subject =  "Thank you contact us!";
        $mail->MsgHTML("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                <html xmlns='http://www.w3.org/1999/xhtml'>
                <head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>

                <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
                </head>

                </html>");
        $mail->addAddress($email, $email);
        $mail->send();

        return view();


    }





}
