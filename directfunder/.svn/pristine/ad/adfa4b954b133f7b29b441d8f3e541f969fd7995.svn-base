<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");
//  Get your Google account, password, recoveryemail and Google voice phone number
require_once("includes/GeeVee/GeeVeeAPI.php");

$phone = '+1'.$_SESSION['google_voice_ph'];

if (empty($_SESSION['geevee']))
{
	$geevee=new GeeVeeAPI($_SESSION['google_acc_nm'],$_SESSION['google_acc_pwd']); // create GeeVeeAPI Object for call and sms
	$_SESSION['geevee'] = serialize($geevee);	
}
$geevee = unserialize($_SESSION['geevee']);
	
//$geevee=new GeeVeeAPI($_SESSION['google_acc_nm'],$_SESSION['google_acc_pwd']);	// Google Voice API

$cur_login_time = $_SESSION['cur_login_time'];
$response = array();
$response['status'] = 'Error';
$arizona_off = -7;
if (is_ajax()) 
{
	if(isset($geevee))
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
		    	//send SMS
		    					
				for ($i=0;$i<$sms_to_cnt;$i++)
				{
					$sms_body = $_POST["sms_body"];
					$to_phone = preg_replace("/[^0-9]*/s", "",$sms_to_tok[$i]);
					
					$sql_sel_cus = sprintf("select p_fl_nm from customer_info where (p_ph1 like '%s') or (p_ph2 like '%s') or (p2_ph1 like '%s') or (p3_ph1 like '%s') limit 1",$to_phone,$to_phone,$to_phone,$to_phone);
					$resb_sel_cus = mysql_query($sql_sel_cus) or die(mysql_error());	
					if ($recb_cus = mysql_fetch_assoc($resb_sel_cus))
					{
						$to_name = $recb_cus['p_fl_nm'];
					}
				
					mysql_query("set time_zone='-7:00';");
					
					$sms_time = date('Y-m-d H:i:s',($msg['start_time']+3600*$arizona_off*1000)/1000);
					
					if ($sms_to_cnt>1)
						$sms_body = $sms_sal.' '.$to_name.','."\r\n".$sms_body;					
					$res = $geevee->sendSMS($sms_to_tok[$i], $sms_body);								
					
				}
				$response['status'] = 'Success';					
	   		}   
   		}
	}
}

$all = $geevee->getAllMessages();

/* get latest sms time from sms_log_info */	
$sql_mail = "select max(sms_utime) as max_sms_time from sms_log_info";
$sql_res = mysql_query($sql_mail) or die(mysql_error());	
$sql_result = mysql_fetch_assoc($sql_res);
if (isset($sql_result) and $sql_result['max_sms_time'] != 0)
{	
}else
	$sql_result['max_sms_time']=0;

$all_info = $all['conversations_response']['conversationgroup'];
foreach($all_info as $a)
{
	
	foreach($a['call'] as $msg)
	{
		if ($msg['start_time'] > $sql_result['max_sms_time'])
		{
			$flag = true;	
			if (($msg['type'] == 11)||($msg['type'] == 10))
			{
				$sms_body = $msg['message_text'];
				$sms_time = date('Y-m-d H:i:s',($msg['start_time']+3600*$arizona_off*1000)/1000);
				$sms_utime = $msg['start_time'];
				$sms_id = $msg['id'];
				$sms_from = $msg['phone_number'];
				$sms_to = $msg['did'];
				//$sms_flag = $msg['status'];
				$sms_flag = 1;
				if ($msg['type'] == 11) //send sms
				{
					$sms_from = $msg['did'];
					$sms_to = $msg['phone_number'];
				}				
				mysql_query("set time_zone='-7:00';");
				$sql_sms = sprintf('insert into sms_log_info(sms_time,sms_utime,log_time,  content, from_phone,  to_phone, sms_flag,sms_id) values ("%s","%.12e",sysdate(),"%s","%s","%s","%d","%s")',$sms_time,$sms_utime,$sms_body,$sms_from,$sms_to,$sms_flag, $sms_id);				
				mysql_query($sql_sms) or die(mysql_error());		
				$response['sms_news']++;	    	
			}
		}
	}

	if ($flag != true)
		break;
	
}

/*==================================================================================================*/

//SMS Sent
$sql="select count(sms_flag) as sms_sent from sms_log_info where  from_phone ='".$phone."' and sms_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_sent']=$res_rec['sms_sent'];

//SMS Receive
$sql="select count(sms_flag) as sms_recv from sms_log_info where  to_phone ='".$phone."' and sms_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_recv']=$res_rec['sms_recv'];


$response['sms_sent'] = $_SESSION['sms_sent'];		
$response['sms_recv'] = $_SESSION['sms_recv'];


echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>