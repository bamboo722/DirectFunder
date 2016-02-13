<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");
	
$response = array();
$response['status'] = "no opened";
$response['opened_res'] = "";
$opened_cnt=0;
// select mails which are sent and not opened 
$sql_sel = sprintf("select mail_id,mail_rcvr from mail_log_info where mail_id is not null and is_opened='%d' and mail_stat='%d' and from_address='%s'",0,0,$_SESSION['google_acc_nm']);
$sql_res = mysql_query($sql_sel) or die(mysql_error());
while ($sql_rec = mysql_fetch_assoc($sql_res))
{
	$id = $sql_rec['mail_id']; 
	$from = $_SESSION['google_acc_nm']; 
	$to = $sql_rec['mail_rcvr'];
	$checkid = "Id:" . $id;
	$fh = fopen("http://www.directfunder.com/agentc3/email.txt", "r");
	$read = false; // init as false
	while (($buffer = fgets($fh)) !== false) {
		if (strpos($buffer, $checkid) !== false) {
			$a = explode("%",$buffer);
			$read = true;
			break; // Once you find the string, you should break out the loop.
		}
	}
 	fclose($fh);
	
	if ($read == true) {
		mysql_query("set time_zone='-7:00';");
		$sql_upd = sprintf("update mail_log_info set is_opened='%d', opened_time=sysdate() where mail_id='%d'",1,$id);
		$sql_upd_res = mysql_query($sql_upd) or die(mysql_error());
		$response['status'] = 'opened';		
		$response['opened_res'].= "Mail sent from ". $from ." To " . $to." has been opened on ".$a[1].";";
		$opened_cnt++;	
	} else {
		
	}
}
echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>