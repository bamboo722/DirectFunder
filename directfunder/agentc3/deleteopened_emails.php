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
if (isset($_POST['auto_no']) and $_POST['auto_no']!="")
{
	$auto_no = trim($_POST["auto_no"]);
    $auto_no_ary = explode(';',$auto_no);
    $auto_no_cnt = count($auto_no_ary);
    
    for ($i=0;$i<$auto_no_cnt;$i++)
    {
    	$sql_upd = sprintf("update  mail_log_info set is_opened=2 where auto_no = '%d'",$auto_no_ary[$i]);
		mysql_query($sql_upd) or die(mysql_error() . "go select error");
	
	}	
	$response['status'] = 'Success';
}
echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>
