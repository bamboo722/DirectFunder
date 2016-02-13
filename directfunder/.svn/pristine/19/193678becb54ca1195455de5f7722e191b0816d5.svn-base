<?php 
@session_start();
ob_start();
if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/Services/Twilio/Capability.php");		// Twilio Call
require_once("includes/Services/Twilio.php");	
require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");	// Email


$response = array();

$response['status']="";
$response['sms_news']=0;
$response['email_news']=0;
$response['call_news']=0;

if (is_ajax())
{
	$agent_name = $_SESSION['user_login'];

	$tw_phone = $_SESSION['tw_number'];
	
	$arizona_off = -7;


	/**
	*  Get Call Notification 
	*/

	// get latest Call time from call_log_info 
	$sql_mail = "select max(start_utime) as max_start_time from call_log_info";
	$sql_res = mysql_query($sql_mail) or die(mysql_error());	
	$sql_result = mysql_fetch_assoc($sql_res);
	if (isset($sql_result) and $sql_result['max_start_time'] != 0)
	{	
	}else
		$sql_result['max_start_time']=0;

	$accountSid = $_SESSION['tw_account_sid'];
	$authToken  = $_SESSION['tw_auth_token'];
	$version = '2010-04-01';


	$capability = new Services_Twilio($accountSid, $authToken, $version);

	try {
	    // Get Recent Calls
	    foreach ($capability->account->calls as $call) {
	    	$call_from =  $call->from;
			$call_conv = $call->duration;
			$call_to = $call->to;
			$call_id = $call->parent_call_sid;
			if ((strlen(preg_replace("/[^0-9]*/s", "",$call->from))!=11) or (strlen( preg_replace("/[^0-9]*/s", "",$call->to))!=11))
				continue;
			$call_utime = strtotime($call->start_time);
			
			if ($call_utime <= $sql_result['max_start_time'])
			{
				break;
			}
			
			$call_time = date('Y-m-d H:i:s',$call_utime);
					
			$call_flag = 1;
			$conv_hr = (int)($call_conv/3600);
			$conv_min = (int)(($call_conv%3600)/60);
			$conv_sec = (int)($call_conv%60);
			$call_conv = $conv_hr.':'.$conv_min.':'.$conv_sec;
			
			mysql_query("set time_zone='-7:00';");
			$sql_call = sprintf('insert into call_log_info(start_time,start_utime,log_time,  conv_time, from_phone,  to_phone, call_flag,call_id) values ("%s","%d",sysdate(),"%s","%s","%s","%d","%s")',$call_time,$call_utime,$call_conv,$call_from,$call_to,$call_flag, $call_id);				
			mysql_query($sql_call) or die(mysql_error());		
			$response['call_news']++;	    	
						

	    }
	} catch (Exception $e) {
		$response['Error'] = $e->getMessage();	    
	}


	/**
	*  Get Email Notification
	*/

	// except SMS mail
	$username = urlencode($_SESSION['google_acc_nm']);	// gmail account
	$password = $_SESSION['google_acc_pwd'];	// gmail account password
	$tag = '';

	$handle = curl_init();
	$options = array( 
		  CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_HEADER         => false,
	      CURLOPT_FOLLOWLOCATION => false,
	      CURLOPT_SSL_VERIFYHOST => '0',
	      CURLOPT_SSL_VERIFYPEER => '1',
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_SSL_VERIFYHOST => 0,		                  
	      CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
	      CURLOPT_VERBOSE        => true,
	      CURLOPT_URL            => 'https://'.$username.':'.$password.'@mail.google.com/mail/feed/atom/'.$tag,
				);
				
	curl_setopt_array($handle, $options);
	$output = (string)curl_exec($handle);
	$xml = simplexml_load_string($output);

	if (curl_errno($handle)) {
	  $response['status'] = 'Error: ' . curl_error($handle);
	  
	}

	curl_close($handle);

	$data = array();
	$data['entries'] = array();
	$data['title'] = (string)$xml->title;
	$data['fullcount'] = (int)$xml->fullcount;
	$data['tagline'] = (string)$xml->tagline;
	$data['modified'] = (string)$xml->modified;


	// get latest time from mail_log_info 
	$old_dt=0;
	$sql = "select max(recv_dt) as latest_recv_dt from mail_log_info where mail_stat>=1";
	$sql_res = mysql_query($sql) or die(mysql_error() . "go select error");
	$sql_result = mysql_fetch_assoc($sql_res);
	$num_rows = (int)(mysql_num_rows($sql_res));
	$old_dt = strtotime($sql_result['latest_recv_dt']);

	foreach ($xml->entry as $entry){
	    $current_entry = array();
	    $current_entry['modified'] = (string)$entry->modified;
	    $current_entry['modified'] = new DateTime( $current_entry['modified']);
	    $current_entry['modified'] = $current_entry['modified']->format('Y-m-d H:i:s');
	    $feed_dt = strtotime($current_entry['modified']);
	    
	    
	    if ($old_dt < $feed_dt){	
	    
		    $current_entry['author'] = array();
		    $current_entry['contributor'] = array();
		    $current_entry['title'] = (string)$entry->title;
		    
		    /* if this email is sms email, then we except it */
		    $temp_title = trim($current_entry['title']);
		    $temp_title = substr($temp_title,0,8);
		    if ($temp_title=="SMS from")
		    {	    
				continue;
			}
		    
		    $current_entry['summary'] = (string)$entry->summary;
		    $current_entry['link'] = (string)$entry->link['href'];
		  
		    
		    $current_entry['author']['name'] = (string)$entry->author->name;
		    $current_entry['author']['email'] = (string)$entry->author->email;
		    
		    $data['entries'][0] = $current_entry;
		    $mail_state = 1;
		    		  
		    //consider ' character to insert sql_string 
		    //   by add " to sql_string
		    mysql_query("set time_zone='-7:00';");
		    $sql_mail = sprintf('insert into mail_log_info(mail_rcvr, mail_subject,mail_body,send_dt,from_nm,from_address,mail_stat,recv_dt,link) values ("%s","%s","%s",sysdate(),"%s","%s","%s","%s","%s")',$_SESSION['google_acc_nm'],mysql_real_escape_string($current_entry['title']),mysql_real_escape_string($current_entry['summary']),$current_entry['author']['name'],$current_entry['author']['email'],$mail_state,$current_entry['modified'],$current_entry['link']);
		    mysql_query($sql_mail) or die(mysql_error());	
		}
	}

	$sql_mail = "select count(*) as news_cnt from mail_log_info where mail_stat=1";
	$sql_res = mysql_query($sql_mail) or die(mysql_error());	
	$sql_result = mysql_fetch_assoc($sql_res);
	$response['email_news']=$sql_result['news_cnt'];



	/**
	*  Get SMS Notification
	*/
	/* get latest sms time from sms_log_info */	
	$sql_sms = "select max(sms_utime) as max_sms_time from sms_log_info";
	$sql_res = mysql_query($sql_sms) or die(mysql_error());	
	$sql_result = mysql_fetch_assoc($sql_res);
	if (isset($sql_result) and $sql_result['max_sms_time'] != 0)
	{	
	}else
		$sql_result['max_sms_time']=0;
	
	$sms_flag = 1;		
		
	try {
		
		foreach ($capability->account->sms_messages as $sms) {		
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
				$response['sms_news']++;	    	
			}else
				break;
		}	
	}catch (Exception $e) {
		$response['Error'] = $e->getMessage();	    
	}

	/*==================================================================================================*/



	//Calls Made
	$sql="select count(call_flag) as call_made from call_log_info where  from_phone like ('%".$tw_phone. "%') and start_time > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error()."11");
	$res_rec=mysql_fetch_assoc($res);
	$_SESSION['calls_made']=$res_rec['call_made'];

	//Calls connect
	$sql="select count(call_flag) as call_con from call_log_info where   TIME_TO_SEC(conv_time)>0 and  (from_phone like ('%".$tw_phone. "%') or to_phone like ('%".$tw_phone. "%')) and start_time > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error()."11");
	$res_rec=mysql_fetch_assoc($res);
	$_SESSION['calls_con']=$res_rec['call_con'];

	//Conversation minutes
	$sql="select (SUM(TIME_TO_SEC(conv_time))) as call_time_secs from call_log_info where  (from_phone like ('%".$tw_phone. "%') or to_phone like ('%".$tw_phone. "%')) and start_time > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error()."11");
	$res_rec=mysql_fetch_assoc($res);
	$call_time_secs = $res_rec['call_time_secs'];
					
	$_SESSION['conv_minutes']=0;
	if ($call_time_secs !=NULL)
	{
		//if ($_SESSION['calls_made']>0)
			$_SESSION['conv_minutes']=ceil($call_time_secs/60);
	}

	$cur_time = time();
	$duration_secs = $cur_time-$_SESSION['admin_login_time'];

	if (($call_time_secs != NULL)&& ($call_time_secs!=0))
	{
		//$_SESSION['ratio']=sprintf("%.1f",(float)($call_time_secs*100/$duration_secs));	
		$_SESSION['ratio']=sprintf("%.1f",(float)($_SESSION['conv_minutes']*100/((int)($duration_secs/60))));	
	}else
		$_SESSION['ratio']=0;	
										
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

	//SMS Sent
	$sql="select count(sms_flag) as sms_sent from sms_log_info where  from_phone ='".$tw_phone."' and log_time > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error() . "go select error");
	$res_rec=mysql_fetch_assoc($res);
	$_SESSION['sms_sent']=$res_rec['sms_sent'];

	//SMS Receive
	$sql="select count(sms_flag) as sms_recv from sms_log_info where  to_phone ='".$tw_phone."' and log_time > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error() . "go select error");
	$res_rec=mysql_fetch_assoc($res);
	$_SESSION['sms_recv']=$res_rec['sms_recv'];

		
	$response['calls_made'] = $_SESSION['calls_made'];
	$response['calls_con'] = $_SESSION['calls_con'];
	$response['conv_minutes'] = $_SESSION['conv_minutes'];
	$response['email_sent'] = $_SESSION['email_sent'];
	$response['email_recv'] = $_SESSION['email_recv'];
	$response['sms_sent'] = $_SESSION['sms_sent'];
	$response['sms_recv'] = $_SESSION['sms_recv'];
	$response['ratio'] = $_SESSION['ratio'];

	if ($response['status'] == "" )
		$response['status'] = 'Success';		
}



echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>