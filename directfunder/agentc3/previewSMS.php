<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

$phone = $_SESSION['tw_number'];


$cur_login_time = $_SESSION['cur_login_time'];
$response = array();
$response['status'] = 'Error';
$arizona_off = -7;
if (is_ajax()) 
{

	if (isset($_POST["sms_to"]) && !empty($_POST["sms_to"])) 
 	{
 		$sms_to = trim($_POST["sms_to"]);
 		$sms_sal = trim($_POST["sms_sal"]);
    	$sms_to_tok = explode(';',$sms_to);
    	$sms_to_cnt = count($sms_to_tok);
		$sms_flag = 0;
	             	
    	$sms_to = $_POST["sms_to"];
	    if (isset($_POST["sms_body"]) && !empty($_POST["sms_body"])) 
	    { 
	    	$sms_body = $_POST["sms_body"];
	    	if ($sms_to == "Bulk Message")
	    	{
				$sms_body = $sms_sal.','."<br>".$sms_body;					
			}else
			{
				$to_phone = preg_replace("/[^0-9]*/s", "",$sms_to_tok[0]);
			
				$sql_sel_cus = sprintf("select p_fl_nm from customer_info where (p_ph1 like '%s') or (p_ph2 like '%s') or (p2_ph1 like '%s') or (p3_ph1 like '%s') limit 1",$to_phone,$to_phone,$to_phone,$to_phone);
				$resb_sel_cus = mysql_query($sql_sel_cus) or die(mysql_error());	
				if ($recb_cus = mysql_fetch_assoc($resb_sel_cus))
				{
					$to_name = $recb_cus['p_fl_nm'];
				}
			
				mysql_query("set time_zone='-7:00';");
				
				if ($sms_to_cnt>1)
					$sms_body = $sms_sal.' '.$to_name.','."<br>".$sms_body;					
			}		
		
			
			$response['sms_body']	= $sms_body;	
			$response['status'] = 'Success';					
   		}   
	}
}


echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>