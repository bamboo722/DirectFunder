<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");
require_once("includes/GeeVee/GeeVeeAPI.php");
$response = array();

if (isset($_POST['call_to']) and $_POST['call_to']!="")
{
	
	$to_phone = $_POST['call_to'];	
	$forward_phone='+1'.$_SESSION['skype_number'];

	if (empty($_SESSION['geevee']))
	{
		$geevee=new GeeVeeAPI($_SESSION['google_acc_nm'],$_SESSION['google_acc_pwd']); // create GeeVeeAPI Object for call and sms
		$_SESSION['geevee'] = serialize($geevee);	
	}
	$geevee = unserialize($_SESSION['geevee']);
	
	$res = $geevee->callNumber($to_phone,$forward_phone);	
	$response['status'] = 'Success';
	
	/* get call history */
	$all = $geevee->getAllMessages();
	
	$flag= false;
	$phone = $_SESSION['tw_number'];

	/* get latest Call time from call_log_info */	
	$sql_mail = "select max(start_utime) as max_start_time from call_log_info";
	$sql_res = mysql_query($sql_mail) or die(mysql_error());	
	$sql_result = mysql_fetch_assoc($sql_res);
	if (isset($sql_result) and $sql_result['max_start_time'] != 0)
	{	
	}else
		$sql_result['max_start_time']=0;

	/*if (isset($all))
	{
		if (isset($all['conversations_response']) && isset($all['conversations_response']['conversationgroup']))	
		{
			$all_info = $all['conversations_response']['conversationgroup'];
			foreach($all_info as $a)
			{	
				foreach($a['call'] as $msg)
				{
					if ($msg['start_time'] > $sql_result['max_start_time'])
					{
						$flag = true;	
						if (($msg['type'] == 8)||($msg['type'] == 17)||($msg['type'] == 0)||($msg['type'] == 1))
						{
							if (($msg['type']==1) and ($a['conversation']['label']['1']=="received"))  //conv
							{
								$call_from = $msg['phone_number'];
								$call_conv = $msg['duration'];
								$call_to = $msg['number_called'];
							}else if (($msg['type']==17) and ($a['conversation']['label']['1']=="placed"))  //placed 
							{
								$call_from = $phone;
								$call_conv = $msg['duration'];
								$call_to = $msg['phone_number'];
							}else if (($msg['type']==8) and ($a['conversation']['label']['1']=="placed"))  //placed 
							{
								$call_from = $msg['did'];
								$call_conv = $msg['duration'];
								$call_to = $msg['phone_number'];
							}else
							{
								$call_from = $msg['phone_number'];
								$call_conv = 0;
								$call_to = $msg['number_called'];
							} 
						
							$call_time = date('Y-m-d H:i:s',($msg['start_time']+3600*$arizona_off*1000)/1000);
							$call_utime = $msg['start_time'];
							$call_id = $msg['id'];
							
							$call_flag = 1;
							
							$conv_hr = (int)($call_conv/3600);
							$conv_min = (int)(($call_conv%3600)/60);
							$conv_sec = (int)($call_conv%60);
							
							$call_conv = $conv_hr.':'.$conv_min.':'.$conv_sec;
							
							mysql_query("set time_zone='-7:00';");
							$sql_call = sprintf('insert into call_log_info(start_time,start_utime,log_time,  conv_time, from_phone,  to_phone, call_flag,call_id) values ("%s","%.12e",sysdate(),"%s","%s","%s","%d","%s")',$call_time,$call_utime,$call_conv,$call_from,$call_to,$call_flag, $call_id);				
							mysql_query($sql_call) or die(mysql_error());		
							$response['call_news']++;	    	
							
						}
					}
				}
				if ($flag != true)
					break;			
			}
		}
	}*/

}else
{
	$response['status'] = 'Error';
}
					
echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>