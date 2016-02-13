<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");


$response = array();

$response['status']="";


//Email Sent
$sql="select count(mail_stat) as email_sent from mail_log_info where  from_address ='".$_SESSION['google_acc_nm']."' and send_dt > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['email_sent']=$res_rec['email_sent'];

					
//Email Receive
$sql="select count(mail_stat) as email_recv from mail_log_info where  mail_rcvr ='".$_SESSION['google_acc_nm']."' and send_dt > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['email_recv']=$res_rec['email_recv'];
 
$response['email_sent'] = $_SESSION['email_sent'];
$response['email_recv'] = $_SESSION['email_recv'];

if ($response['status'] == "" )
	$response['status'] = 'Success';		


echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>