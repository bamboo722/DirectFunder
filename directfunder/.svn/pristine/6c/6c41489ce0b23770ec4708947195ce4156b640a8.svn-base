<?php
//phpinfo();
/****-------------------------------------------------------------------**************************	
		Purpose 	: 	This page will destroy the session and log off the user from the system
		Project 	:	Smart Travel Project	
	 	Developer 	: 	Kelvin Smith
		Version		:	1.0
	 	Create Date : 	15/02/2012     
****-------------------------------------------------------------------************************/
session_start();
require_once("includes/dbconnect.php");
/*--- login, logout, duration time log ---*/

if(!isset($_SESSION['user_login']) ||$_SESSION['user_login']=="")//session store admin name
{
	header("Location: adminlogin.php");//login in AdminLogin.php
}

header("Location: getNotification.php");//login in AdminLogin.php
$_SESSION['admin_logout_time']=time();

mysql_query("set time_zone='-7:00';");	
$sql="select sysdate() as cur_logout_time;";  
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['cur_logout_time']=$res_rec['cur_logout_time'];				



$sql=sprintf("select TIME_TO_SEC(TIMEDIFF('%s','%s')) as secs_diff;",$_SESSION['cur_logout_time'],$_SESSION['cur_login_time']);  
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$duration=$res_rec['secs_diff'];	


$seconds = $duration % 60;
$minutes = (int)(($duration % 3600) / 60);
$hours = (int)($duration / 3600);
$duration = $hours.":".$minutes.":".$seconds;

if ($_SESSION['calls_made']=="")
	$_SESSION['calls_made']=0;		
if ($_SESSION['calls_con']=="")
	$_SESSION['calls_con']=0;		
if ($_SESSION['conv_minutes']=="")
	$_SESSION['conv_minutes']=0;		
if ($_SESSION['email_sent']=="")
	$_SESSION['email_sent']=0;		
if ($_SESSION['email_recv']=="")
	$_SESSION['email_recv']=0;		
if ($_SESSION['sms_sent']=="")
	$_SESSION['sms_sent']=0;		
if ($_SESSION['sms_recv']=="")
	$_SESSION['sms_recv']=0;	
if ($_SESSION['ratio']=="")
	$_SESSION['ratio']=0;	
	

					
$sql="insert into agent_log_info (log_in,log_out,duration,dur_hour,dur_min,dur_sec,agent,call_made,call_conn,conv_min,eml_sent,eml_recv,sms_sent,sms_recv,ratio,newLeads,opened_emails,clickthroughs,retryLeads,followUpCount,past_due,delinquent,hotLeads,warmLeads,credit_checks,credit_repairs,credit_ready,pre_approveds,doc_sents,pending_fundings,fundedLeads,fee_pending,thirty_day_funding,sixty_day_funding,sixty_ninety_day_fundings,clients,other_opportunity,calls,email,sms) values ('". $_SESSION['cur_login_time'] ."','" . $_SESSION['cur_logout_time'] ."','" . $duration ."','" . $hours ."','". $minutes ."','" . $seconds."','".$_SESSION['user_login']."','".$_SESSION['calls_made']."','".$_SESSION['calls_con']."','".$_SESSION['conv_minutes']."','".$_SESSION['email_sent']."','".$_SESSION['email_recv']."','".$_SESSION['sms_sent']."','".$_SESSION['sms_recv']."','".$_SESSION['ratio']."','".$_SESSION['newLeads']."','".$_SESSION['opened_emails']."','".$_SESSION['clickthroughs']."','".$_SESSION['retryLeads']."','".$_SESSION['followUpCount']."','".$_SESSION['past_due']."','".$_SESSION['delinquent']."','".$_SESSION['hotLeads']."','".$_SESSION['warmLeads']."','".$_SESSION['credit_checks']."','".$_SESSION['credit_repairs']."','".$_SESSION['credit_ready']."','".$_SESSION['pre_approveds']."','".$_SESSION['doc_sents']."','".$_SESSION['pending_fundings']."','".$_SESSION['fundedLeads']."','".$_SESSION['fee_pending']."','".$_SESSION['thirty_day_funding']."','".$_SESSION['sixty_day_funding']."','".$_SESSION['sixty_ninety_day_fundings']."','".$_SESSION['clients']."','".$_SESSION['other_opportunity']."','".$_SESSION['call']."','".$_SESSION['email']."','".$_SESSION['sms']."')";  
$res=mysql_query($sql) or die(mysql_error()."11");

/*-----------------------------------------*/

/* set cur_login_time null */
$sql = sprintf("update admin_user set cur_login_time='%s' where user_id='%s'","0000-00-00 00:00:00",$_SESSION['user_login']);
$res=mysql_query($sql) or die(mysql_error()."11");

session_destroy();
setcookie("cookie_login","",time()-3600);
setcookie("cookie_password","",time()-3600);
header("Location: index.php");
?>