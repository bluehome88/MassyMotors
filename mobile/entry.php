<?php

$fname = strtoupper($_GET['fname']);
$lname = strtoupper($_GET['lname']);
$email = $_GET['email'];
$phone = strtoupper($_GET['phone']);
$ssnumber = strtoupper($_GET['ssnumber']);
$udid = strtoupper($_GET['udid']);

$subject = 'SUBMISSION FROM MOBILE APP: ' . strtoupper($fname) . ' ' .strtoupper( $lname);
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: website@massystorestt.com" . "\r\n";

$message = 'Name: ' .  $fname . ' ' . $lname;
$message .= '<br/>Email: ' .  $email;
$message .= '<br/>Phone: ' .  $phone;
$message .= '<br/>SS Number: ' .  $ssnumber;
$message .= '<br/>Device ID: ' .  $udid;
$message .= '<p>&nbsp;</p><p>-- end --</p>';
$message .= '<br/><strong>Do not respond to this email</strong>';



if(
    mail(
        'rjkirton@kirtontt.com',
        $subject,
        $message, $headers
    )){
    
    echo $_GET['callback'] . '('.json_encode(array('response'=>'true')).')';
} else {
    echo $_GET['callback'] . '('.json_encode(array('error'=>'Unable to send list.')).')';
}
?>