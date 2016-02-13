<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
$cur_login_time = $_SESSION['cur_login_time'];
$cur_time = time();

$response = array();
$phone = '+1'.$_SESSION['google_voice_ph'];

$to_phone = preg_replace("/[^0-9]*/s", "",$_POST['call_to']);
$to_phone = '+1'.$to_phone;		

mysql_query("set time_zone='-7:00';");
$sql_mail = sprintf("insert into call_log_info(start_time,log_time,from_phone,to_phone,call_flag) values (sysdate(),sysdate(),'%s','%s','%d')",$phone,$to_phone,0);
//$sql_mail = "insert into call_log_info(start_time, log_time,from_phone,to_phone,call_flag) values (sysdate(),sysdate(),'" .$_SESSION['google_voice_ph']."','".$_POST['call_to']."','0')";
$res_sel = mysql_query($sql_mail) or die(mysql_error() . "go select error");

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
									
$response['status'] = 'Success';					
$response['calls_made'] = $_SESSION['calls_made'];		
$response['calls_con'] = $_SESSION['calls_con'];
$response['conv_minutes'] = $_SESSION['conv_minutes'];
$response['ratio'] = $_SESSION['ratio'];
echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>