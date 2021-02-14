<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'phpmailer/vendor/autoload.php';

$main_array = Array();

if(isset($_POST['email']) && !empty($_POST['email'])){
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
        //your site secret key
        $secret = 'Google API Secret Key';
        //get verify response data
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if($responseData->success){
            //contact form submission code
            
            // Send email to our user
            $subject = 'Contact via Sachin Artani'; // Give the email a subject
            $name = !empty($_POST['username'])?$_POST['username']:'';
            $email = !empty($_POST['email'])?$_POST['email']:'';
            $message = !empty($_POST['message'])?$_POST['message']:'';
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
               
            try {
                //Server settings
                //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = '{SMTP HOST}';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication 
                $mail->Username = '{SMTP USERNAME}';                 // SMTP username
                $mail->Password = '{SMTP PASSWORD}';                           // SMTP password
                $mail->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587;                                    // TCP port to connect to
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            
                //Recipients
                $mail->setFrom('drop@sachinartani.com', 'Sachin Artani');
                $mail->addAddress('drop@sachinartani.com', 'Sachin Artani'); 
                $mail->addReplyTo($email, $name); 
                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $subject;
                
                $mail->Body = '<p style="font-family:verdana;">Hey Yo Sachin! Someone called <b>'.$name.'</b> sent something to you.<br>His email is <b>'.$email.'
                </b><br>This is what he sent -<br>'.$message.'</p>';
                
                $mail->send();
                $success = 1;
			    $rtnMsg = "I got your mail. Thank you! I'll reply you shortly.";
            } catch (Exception $e) {
                $success = 0;
			    $rtnMsg = "Oh no! Something is wrong. Your email got lost in the space.";
            }
            
        } else {
            $success = 0;
		    $rtnMsg = "Robot verification failed, please try again.";
        }
    } else {
        $success = 0;
	    $rtnMsg = "Please click on the reCAPTCHA box.";
    } 
} else {
    $success = 0;
    $rtnMsg = "Hey, Shoo! Shoo! You cannot come here like this.";
}

$main_array['success'] = $success;
$main_array['rtnMsg'] = $rtnMsg;

$json_array = json_encode($main_array, JSON_FORCE_OBJECT);
die($json_array);

?>
