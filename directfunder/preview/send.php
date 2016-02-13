<?php
require("cnf.php");
require('mail/sendmail.php');
require('mail/pdf.php');

header('Content-Type: application/json');

	$fName = GetField("fName");
	$mName = GetField("mName");
	$lName = GetField("lName");
	$Email = GetField("Email");
	$hPhone = GetField("hPhone");
	$cPhone = GetField("cPhone");
	$hAddress = GetField("hAddress");
	$Years = GetField("Years");
	$City = GetField("City");
	$State = GetField("State");
	$zCode = GetField("zCode");
	$Dob = GetField("Dob");
	$ss = GetField("ss");
	$usCitizen = GetField("usCitizen");
	$mMaidenName = GetField("mMaidenName");
	$driveLicense = GetField("driveLicense");
	$uiWord = GetField("uiWord");
	$seeking = GetField("seeking");
	$legalBusinessName = GetField("legalBusinessName");
	$IndustryType = GetField("IndustryType");
	$federalTax = GetField("federalTax");
	$federalTaxTitle = "";//GetField("federalTaxTitle");
	$businessPhone = GetField("businessPhone");
	$fax = GetField("fax");
	$businessAddress = GetField("businessAddress");
	$businessCity = GetField("businessCity");
	$businessState = GetField("businessState");
	$businessZCode = GetField("businessZCode");
	$yearInBusiness = GetField("yearInBusiness");
	$acceptCreditAspayment = GetField("acceptCreditAspayment");
	$merchantAccount = GetField("merchantAccount");
	$entityType = GetField("entityType");
	$employees = GetField("employees");
	$registeredBusiness = GetField("registeredBusiness");
	$haveWebsite = "";//GetField("haveWebsite");
	$urlWebsite = GetField("urlWebsite");
	$personalBank = GetField("personalBank");
	$businessBank = GetField("businessBank");
	$dontShowPersonalCredit = GetField("dontShowPersonalCredit");
	$urlShowPersonalCredit = GetField("urlShowPersonalCredit");
	$haveIRA = GetField("haveIRA");
	$haveIRAHowMuch = GetField("haveIRAHowMuch");
	$givePermission = GetField("givePermission");
	$usrReport = GetField("usrReport");
	$pswReport = GetField("pswReport");
	$haveFMService = GetField("haveFMService");
	$whoFMService = GetField("whoFMService");
	$haveAnyoneElse = GetField("haveAnyoneElse");
	$whoAnyoneElse = GetField("whoAnyoneElse");
	$eSignature = "";//GetField("eSignature");
	$effectiveDate = "";//GetField("effectiveDate");
if(Validation() == false) {
	echo "{\"status\": \"ERR\", \"msg\": \"There is an error in your submission.\"}";
	exit();
}
$to = $_POST["Email"];
$subject1 = "Get started now";
$subject2 = "Welcome to DirectFunder";
$subject3 = "Directfunder Business Process";
$from ="info@directfunder.com";
$message1 = ReadTemplate("mail/GetStarted.txt");
$message2 = ReadTemplate("mail/Welcome.txt");
$message3 = ReadTemplate("mail/Business.txt");
$pdf = ReadTemplate("mail/pdf.txt");
$opdf = ReadTemplate("mail/opdf.txt");
foreach($GLOBALS as $key=>$val) {
	if (is_array($val)==false) {
		$pdf = str_replace("{{".$key."}}",$val,$pdf);
		$opdf = str_replace("{{".$key."}}",$val,$opdf);
	}
	//echo $key;
}
$pdfFile = md5($Email).'.pdf'; 
Output($pdfFile,$opdf);

$sent=true;
$sent = sendMail($to,$subject1,$message1,$from) && sendMail($to,$subject2,$message2,$from) && sendMail($to,$subject3,$message3,$from);

$message =  str_replace("{{ipAddress}}",get_client_ip(),$pdf);
$subject4 = "Mail to admin";

foreach ($emailList as $admin) {
	sleep(2);
	$sent = $sent && sendMailWithAttachment($admin,$subject4,$message,$from,$pdfFile);
}
//$sent = $sent && sendMailWithAttachment($admin,$subject4,$message,$from,$pdfFile);
if ($sent==true) {
	$data ="{\"status\": \"OK\", \"msg\": \"Thank you for your submit.Please check mail.\"}";
} else {
	$data ="{\"status\": \"ERR\", \"msg\": \"server error has occurred..Please try to submit again.\"}";
}
@unlink($pdfFile);
echo $data;

function GetField($item) {
	//$value="";
	if(!isset($_POST[$item])) {
		return "";
	}
	return $_POST[$item];
}
function Validation() {
	if ($GLOBALS['fName'] =="") return false;
	if ($GLOBALS['mName'] == "") return false;
	if ($GLOBALS['lName'] == "") return false;
	if ($GLOBALS['Email'] == "") return false;
	if ($GLOBALS['hPhone'] == "") return false;
	if ($GLOBALS['cPhone'] == "") return false;
	if ($GLOBALS['hAddress'] == "") return false;
	if ($GLOBALS['Years'] == "") return false;
	if ($GLOBALS['City'] == "") return false;
	if ($GLOBALS['State'] == "") return false;
	if ($GLOBALS['zCode'] == "") return false;
	if ($GLOBALS['Dob'] == "") return false;
	if ($GLOBALS['ss'] == "") return false;
	if ($GLOBALS['usCitizen'] == "") return false;
	if ($GLOBALS['mMaidenName'] == "") return false;
	if ($GLOBALS['driveLicense'] == "") return false;
	if ($GLOBALS['uiWord'] == "") return false;
	if ($GLOBALS['seeking'] == "") return false;
	if ($GLOBALS['legalBusinessName'] == "") return false;
	if ($GLOBALS['IndustryType'] == "") return false;
	if ($GLOBALS['federalTax'] == "") return false;
	//if ($GLOBALS['federalTaxTitle'] == "") return false;
	if ($GLOBALS['businessPhone'] == "") return false;
	if ($GLOBALS['fax'] == "") return false;
	if ($GLOBALS['businessAddress'] == "") return false;
	if ($GLOBALS['businessCity'] == "") return false;
	if ($GLOBALS['businessState'] == "") return false;
	if ($GLOBALS['businessZCode'] == "") return false;
	if ($GLOBALS['yearInBusiness'] == "") return false;
	if ($GLOBALS['employees'] == "") return false;
	if ($GLOBALS['registeredBusiness'] == "") return false;
	/*if ($GLOBALS['haveWebsite'] == "") return false;
	if ($GLOBALS['haveWebsite'] == "Yes") {
		if ($GLOBALS['urlWebsite'] == "") return false;	
	}*/
	if ($GLOBALS['personalBank'] == "") return false;
	if ($GLOBALS['businessBank'] == "") return false;
	if ($GLOBALS['dontShowPersonalCredit'] == "") return false;
	if ($GLOBALS['dontShowPersonalCredit'] == "Yes") {
		if ($GLOBALS['urlShowPersonalCredit'] == "") return false;
	} else {
		$GLOBALS['urlShowPersonalCredit'] = "";
	}
	if ($GLOBALS['haveIRA'] == "") return false;
	if ($GLOBALS['haveIRA'] == "Yes") {
		if ($GLOBALS['haveIRAHowMuch'] == "") return false;
	} else {
		$GLOBALS['haveIRAHowMuch']="";
	}
	if ($GLOBALS['givePermission'] == "") return false;
	if ($GLOBALS['givePermission'] == "Yes") {
		if ($GLOBALS['usrReport'] == "") return false;
		if ($GLOBALS['pswReport'] == "") return false;
	} else {
		$GLOBALS['usrReport'] ="";
		$GLOBALS['pswReport'] ="";
	}
	if ($GLOBALS['haveFMService'] == "") return false;
	if ($GLOBALS['haveFMService'] == "Yes") {
		if ($GLOBALS['whoFMService'] == "") return false;
	} else {
		$GLOBALS['whoFMService'] = "";
	}
	if ($GLOBALS['haveAnyoneElse'] == "") return false;
	if ($GLOBALS['haveAnyoneElse'] == "Yes") {
		if ($GLOBALS['whoAnyoneElse'] == "") return false;
	} else {
		$GLOBALS['whoAnyoneElse'] ="";
	}
	//if ($GLOBALS['eSignature'] == "") return false;
	//if ($GLOBALS['effectiveDate'] =="") return false;
	if ($GLOBALS['acceptCreditAspayment'] == "") return false;
	if ($GLOBALS['merchantAccount'] == "") return false;
	if ($GLOBALS['entityType'] == "") return false;	
	return true;
}/*
function RequiredValidation($item) {
	$value = GetField($item);
	if($value ==""){
		return $errMsg = "{\"status\" = \"ERR\", \"msg\" = \"Field is required.\"}";
	} 
	return "";
}*/
//var_dump($_REQUEST);
?>