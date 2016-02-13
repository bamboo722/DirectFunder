<?php
require("cnf.php");
require('mail/sendmail.php');
require('mail/pdf.php');

header('Content-Type: application/json');

$name = GetField("name");
$Email = GetField("Email");
$phone = GetField("phone");
$amountNeeded = GetField("amountNeeded");
$comment = GetField("Comment");
	
if(Validation() == false) {
	echo "{\"status\": \"ERR\", \"msg\": \"There is an error in your submission.\"}";
	exit();
}
$to = $_POST["Email"];
$subject = "Mail to admin";
$from ="info@directfunder.com";
$message = ReadTemplate("mail/admin.txt");
foreach($_REQUEST as $key=>$val) {
	$message = str_replace("{{".$key."}}",$val,$message);
}

$sent=true;

foreach ($applyList as $admin) {
	sleep(1);
	$sent = $sent && sendMail($admin,$subject,$message,$from);
}
if ($sent==true) {
	$data ="{\"status\": \"OK\", \"msg\": \"Thank you for your submit.\"}";
} else {
	$data ="{\"status\": \"ERR\", \"msg\": \"server error has occurred..Please try to submit again.\"}";
}
echo $data;

function GetField($item) {
	if(!isset($_POST[$item])) {
		return "";
	}
	return $_POST[$item];
}
function Validation() {
	if ($GLOBALS['name'] =="") return false;
	if ($GLOBALS['phone'] =="") return false;
	if ($GLOBALS['Email'] == "") return false;
	if ($GLOBALS['amountNeeded'] =="") return false;
	return true;
}
?>