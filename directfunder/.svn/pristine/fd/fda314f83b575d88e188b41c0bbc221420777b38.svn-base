<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

$response = array();

$sql_upd = sprintf("update auto_dial_info set duration='%d',is_called='%d',called_number='%d' where user_id='%s'",0,0,0,$_SESSION['user_login']);
mysql_query($sql_upd) or die(mysql_error());	
	
$response['status'] = 'Success';

echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>