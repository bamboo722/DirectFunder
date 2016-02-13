<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");

$response = array();
$response['status'] = 'Error';
if (isset($_POST['auto_id']) and $_POST['auto_id']!="")
{
	$sql_ins = sprintf('delete from instruction_info where auto_id="%d";',$_POST['auto_id']);
	mysql_query($sql_ins) or die(mysql_error() . "go select error");
	$response['status'] = 'Success';
}
echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>
