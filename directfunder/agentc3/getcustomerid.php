<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

$response = array();
$response['customer_id']='';
if (is_ajax())	
{
	if (isset($_POST['phone']) and $_POST['phone'] != '')
	{
		$sql_sel = sprintf("select customer_id from customer_info where (p_ph1 like '%s') or (p_ph2 like '%s') or (p2_ph1 like '%s') or (p3_ph1 like '%s') limit 1",$_POST['phone'],$_POST['phone'],$_POST['phone'],$_POST['phone']);
		$res = mysql_query($sql_sel) or die(mysql_error());	
		$res_rec=mysql_fetch_assoc($res);
		$response['customer_id']=$res_rec['customer_id'];
	}
}
$response['status'] = 'Success';					
			

echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>