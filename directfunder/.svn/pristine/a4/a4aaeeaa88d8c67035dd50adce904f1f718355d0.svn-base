<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

$response = array();
$response['status'] = 'Error';
if (isset($_POST['customers']) and $_POST['customers'] != "")
{
	$cutomer_ary = explode(';',$_POST['customers']);
	
	/**
	* init auto_dial_info table
	**/
	$sql_del = sprintf("delete from auto_dial_info where user_id='%s';",$_SESSION['user_login']);
	$result = mysql_query($sql_del) or die(mysql_error());
	  
	  
	for ($i=0;$i<count($cutomer_ary);$i++)
  	{
  		
		$sql_ins = sprintf("insert into auto_dial_info (user_id,customer_id,p_fl_nm,ph_1,ph_2,p2_ph1,p2_ph2,p3_ph1,p3_ph2, duration,is_called,called_number) select '%s',customer_id,p_fl_nm,p_ph1,p_ph2,p2_ph1,p2_ph2,p3_ph1,p3_ph2,'%d','%d',%d from customer_info where customer_id = '%s'",$_SESSION['user_login'],0,0,0,$cutomer_ary[$i]);
		mysql_query($sql_ins) or die(mysql_error());
	}
	$response['status'] = 'Success';	
}

echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>