<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");
require_once("includes/Services/Twilio.php");	


$response = array();
$response['status'] = 'Error';

if (is_ajax())
{
	if (isset($_POST['start_ind']) and $_POST['start_ind'] != "")
	{
		$phone =$_SESSION['tw_number'];

		$accountSid = $_SESSION['tw_account_sid'];
		$authToken  = $_SESSION['tw_auth_token'];
		$from = $_SESSION['tw_number'];

		$client = new Services_Twilio($accountSid, $authToken,'2010-04-01');

		$start_ind = $_POST['start_ind'];
		$cnt=0;
		
		$myfile = new SplFileObject($_POST['sms_addr']);
		$myfile->seek($start_ind); 
		
		while (!$myfile->eof()) 
		{
			$to_phone = $myfile->fgets();
			$sms_body = $_POST['sms_body'];
			$sms_sal = $_POST['sms_sal'];
			
			$to_phone = trim($to_phone);
			$sql_sel_cus = sprintf("select p_fl_nm from customer_info where (p_ph1 like '%s') or (p_ph2 like '%s') or (p2_ph1 like '%s') or (p3_ph1 like '%s') limit 1",$to_phone,$to_phone,$to_phone,$to_phone);
			$resb_sel_cus = mysql_query($sql_sel_cus) or die(mysql_error());	
			if ($recb_cus = mysql_fetch_assoc($resb_sel_cus))
			{
				$to_name = $recb_cus['p_fl_nm'];
			}
		
			
			$sms_body = $sms_sal.' '.$to_name.','."<br>".$sms_body;					
	    	
	    	
	    	if ($to_phone=="" or $sms_body == "")
	    	{
					
			}else
			{
				$sms = $client->account->messages->sendMessage(
					$from,
					$to_phone,
					$sms_body
				);
			}
	    	
	    	$cnt++;
	    	if ($cnt==5)
	    		break;
	    		
	    	$response['status'] = 'Success';	
		}
		
		$sql_sms = "select max(sms_utime) as max_sms_time from sms_log_info";
		$sql_res = mysql_query($sql_sms) or die(mysql_error());	
		$sql_result = mysql_fetch_assoc($sql_res);
		if (isset($sql_result) and $sql_result['max_sms_time'] != 0)
		{	
		}else
			$sql_result['max_sms_time']=0;
		
		$sms_flag = 1;		
		
		foreach ($client->account->sms_messages as $sms) {		
		    $row = array(
		        $sms->sid, $sms->from, $sms->to, $sms->date_sent,
		        $sms->status, $sms->direction, $sms->price, $sms->body
		    );
		    $sms_utime = strtotime($sms->date_sent);
		    if ($sms_utime > $sql_result['max_sms_time']) // date_sent
		    {
				mysql_query("set time_zone='-7:00';");
				$sms_sent = date("Y-m-d H:i:s",$sms_utime);    
				$sql_sms = sprintf('insert into sms_log_info(log_time,sms_time,sms_utime,content, from_phone,  to_phone, sms_flag,sms_id,direction,status) values (sysdate(),"%s","%d","%s","%s","%s","%d","%s","%s","%s")',$sms_sent,$sms_utime, $sms->body,$sms->from,$sms->to,$sms_flag, $sms->sid, $sms->direction,$sms->status);				
				mysql_query($sql_sms) or die(mysql_error());					
			}else
				break;
		}
		
		$myfile = null;
	}
}

//SMS Sent
$sql="select count(sms_flag) as sms_sent from sms_log_info where  from_phone ='".$phone."' and log_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_sent']=$res_rec['sms_sent'];

//SMS Receive
$sql="select count(sms_flag) as sms_recv from sms_log_info where  to_phone ='".$phone."' and log_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_recv']=$res_rec['sms_recv'];

echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>