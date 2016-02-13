<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");	// Email
require_once("includes/GeeVee/GeeVeeAPI.php");		// Google Voice sms and call

$response = array();
$response['status']="";
$response['sms_news']=0;
$response['email_news']=0;
$response['call_news']=0;
$agent_name = $_SESSION['user_login'];
$arizona_off = -7;
/*========================================= Get Email Notification ==============================================*/ 
//PHP - Get Gmail new messages (unread) from Atom Feed
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


/* get latest time from mail_log_info */
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
	    $sql_mail = sprintf('insert into mail_log_info(mail_rcvr, mail_subject,mail_body,send_dt,from_nm,from_address,mail_stat,recv_dt,link) values ("%s","%s","%s",sysdate(),"%s","%s","%s","%s","%s")',$_SESSION['google_acc_nm'],$current_entry['title'],$current_entry['summary'],$current_entry['author']['name'],$current_entry['author']['email'],$mail_state,$current_entry['modified'],$current_entry['link']);
		//$sql_mail = "insert into mail_log_info(mail_rcvr, mail_subject,mail_body,send_dt,from_nm,from_address,mail_stat,recv_dt,link) values ('".$_SESSION['google_acc_nm']."',".'"'.$current_entry['title'].'"'.",".'"'.$current_entry['summary'].'"'.",sysdate(),'".$current_entry['author']['name']."','".$current_entry['author']['email']."','".$mail_state."','".$current_entry['modified']."','".$current_entry['link']."')";
	    mysql_query($sql_mail) or die(mysql_error());	
	}
}

$sql_mail = "select count(*) as news_cnt from mail_log_info where mail_stat=1";
$sql_res = mysql_query($sql_mail) or die(mysql_error());	
$sql_result = mysql_fetch_assoc($sql_res);
$response['email_news']=$sql_result['news_cnt'];



/*================================= Get Google Voice Notification ==================================*/
// Use GeeVeeAPI
 
if (empty($_SESSION['geevee']))
{
	$geevee=new GeeVeeAPI($_SESSION['google_acc_nm'],$_SESSION['google_acc_pwd']); // create GeeVeeAPI Object for call and sms
	$_SESSION['geevee'] = serialize($geevee);	
}
$geevee = unserialize($_SESSION['geevee']);
$all = $geevee->getAllMessages();
$flag= false;
$phone = '+1'.$_SESSION['google_voice_ph'];


/* get latest Call time from call_log_info */	
$sql_mail = "select max(start_utime) as max_start_time from call_log_info";
$sql_res = mysql_query($sql_mail) or die(mysql_error());	
$sql_result = mysql_fetch_assoc($sql_res);
if (isset($sql_result) and $sql_result['max_start_time'] != 0)
{	
}else
	$sql_result['max_start_time']=0;

if (isset($all))
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
}


/* get latest Call time from call_log_info */	
$sql_mail = "select max(sms_utime) as max_sms_time from sms_log_info";
$sql_res = mysql_query($sql_mail) or die(mysql_error());	
$sql_result = mysql_fetch_assoc($sql_res);
if (isset($sql_result) and $sql_result['max_sms_time'] != 0)
{	
}else
	$sql_result['max_sms_time']=0;
$flag = false;

if (isset($all))
{
	if (isset($all['conversations_response']) && isset($all['conversations_response']['conversationgroup']))	
	{
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
	}
}
	

/*==================================================================================================*/


$phone = '+1'.$_SESSION['google_voice_ph'];
//Calls Made
$sql="select count(call_flag) as call_made from call_log_info where  from_phone like ('%".$phone. "%') and start_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['calls_made']=$res_rec['call_made'];

//Calls connect
$sql="select count(call_flag) as call_con from call_log_info where   TIME_TO_SEC(conv_time)>0 and  (from_phone like ('%".$phone. "%') or to_phone like ('%".$phone. "%')) and start_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['calls_con']=$res_rec['call_con'];

//Conversation minutes
$sql="select (SUM(TIME_TO_SEC(conv_time))) as call_time_secs from call_log_info where  (from_phone like ('%".$phone. "%') or to_phone like ('%".$phone. "%')) and start_time > '".$_SESSION['last_logout']."'";
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
$sql="select count(sms_flag) as sms_sent from sms_log_info where  from_phone ='".$phone."' and sms_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_sent']=$res_rec['sms_sent'];

//SMS Receive
$sql="select count(sms_flag) as sms_recv from sms_log_info where  to_phone ='".$phone."' and sms_time > '".$_SESSION['last_logout']."'";
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


echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>