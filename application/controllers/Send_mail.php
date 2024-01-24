<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Send_mail extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        echo "<pre>";
        $this->load->library('My_PHPMailer');
        $mail = new PHPMailer;
        $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'send.smtp.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'no-reply@awarathon.com';                 // SMTP username
        $mail->Password = 'atom1001'; 
        // $mail->Username             =  "product@awarathon.com";
        // $mail->Password             =  "Rahul@1122";
        $mail->SMTPSecure           =  "tls";
        $mail->Port                 =  "587";                          // SMTP password
        // $mail->Port = 25;                                    // TCP port to connect to
        $mail->XMailer              =  ' ';
        $mail->SMTPOptions          =  array(
            'ssl'                   => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );
        $mail->From = 'no-reply@awarathon.com';
        $mail->FromName = 'Awarathon';
        $mail->addAddress('jagdisha.patel@awarathon.com');               // Name is optional
        $mail->addAddress('rahul@awarathon.com');
       
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'SMTP Testing';
        $mail->Body    = 'This mail send for testing purpose';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
        
    }
    function send_email()
    {
        $this->load->library('My_PHPMailer');
        $mail                       =  new PHPMailer;
        $mail->SMTPDebug            =  0;
        $mail->isSMTP();
        $mail->Host                 =  "smtp.gmail.com";
        $mail->SMTPAuth             =  "1";
        // $mail->Username             =  "info@awarathon.com";
        // $mail->Password             =  "atom4949";
        $mail->Username             =  "jagdisha.patel@awarathon.com";
        $mail->Password             =  "jagdisha@awarathon";
        $mail->SMTPSecure           =  "tls";
        $mail->Port                 =  "587";
        $mail->XMailer              =  ' ';
        $mail->SMTPOptions          =  array(
            'ssl'                   => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );
        
         $mail->addAddress("jagdisha@mworks.in", "Jagdisha Patel");
     
        $mail->isHTML(true);
        $mail->setFrom("info@awarathon.com", "Awarathon");
        $mail->SMTPKeepAlive        =  true;
        $mail->CharSet              =  'UTF-8';
       
        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->Mailer               =  "smtp";
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }

    
}
