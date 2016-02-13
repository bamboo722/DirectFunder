<?php
require_once("prospects/includes/dbconnect.php");  
require("cnf.php");
require('mail/sendmail.php');
require('mail/pdf.php');

header('Content-Type: application/json');

	$fName = GetField("fName");
	$mName = GetField("mName");
	$lName = GetField("lName");
	$Email = GetField("Email");
	$hPhone = GetField("hPhone");
        $hPhone = preg_replace("/[^0-9]*/s", "",$hPhone);
	$cPhone = GetField("cPhone");
        $cPhone = preg_replace("/[^0-9]*/s", "",$cPhone);
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
  
        $stateBusiness = GetField("stateBusiness");
	$legalBusinessName = GetField("legalBusinessName");
	$IndustryType = GetField("IndustryType");
	$federalTax = GetField("federalTax");
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
	$haveWebsite = GetField("haveWebsite");
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
	if(!is_array($val)) {
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

/*** Start sending buyer info to buyer_info_master ***/
        
// create customer_id 

$sql_sel = "select max(auto_id) maxid from customer_info";
$res_sel = mysql_query($sql_sel) or die(mysql_error()."11");
$rec_sel = mysql_fetch_assoc($res_sel);
$recid = $rec_sel['maxid'];
//echo "recid : ";
//echo $recid;
       
$flagg = 0;
		
// insert full_name, mobile_phone,email,amount_needed(ammount_requested), comment into customer_infoer 
$sql_ins1 = "SELECT * from customer_info where auto_id = '".$recid."' ";
$sql_ins1_resul = mysql_query($sql_ins1) or die(mysql_error()."go select error");
$rec_sel = mysql_fetch_assoc($sql_ins1_resul);
if($rec_sel['customer_id']!='')
{
   mysql_query("DELETE FROM customer_info where auto_id = '".$recid."' ") or die(mysql_error());
   $sql_ins = "insert into customer_info "
                . "(customer_id, apply_dt, priority_opt, cust_apl_by, cust_upd_by,cust_upd_dt,agent,follow_up,funding_dt,amt_granted,"
                . "cred_review_fee_amt,cred_review_fee_dt_paid,cred_estab_fee_amt,cred_estab_fee_dt_paid,liq_fee_amt,"
                . "liq_fee_dt_paid,miscel_fee_amt,miscel_fee_dt_paid,df_cnslt_fee_amt,df_cnslt_fee_date_paid,cred_repair_fee_amt,cred_repair_fee_dt_paid,lst_log_info,"
                . "dur_info,calls_md_info,calls_conn_info,conv_min_info,eml_snd_info,eml_rcv_info,lead_src,bst_time_to_call,p_psn2,p_fl_nm,p_ph1,p_ph2,p_eml1,p_eml2,p_amt_req,p_cmt,p_fr_nm,p_mi_nm,"
                . "p_la_nm,p_hm_ph,p_hm_addr,p_ye_addr,p_city,p_state,p_zip,p_dob,p_ss,p_is_us,p_mam_maiden_nm,p_drv_lic,p_unq_id,p_hv_af,p_wh_af,p_hv_dod,"
                . "p_wh_dod,p_bnk_nm,b_stg,b_leg_nm,b_ent_typ,b_ind_typ,b_fed_tax_id,b_ph,b_fax,b_addr,b_city,b_state,b_zip,b_ye_busi,b_empl,b_reg_state,b_wb_site,"
                . "b_bnk_nm,b_acpt_cred_card,b_hv_cred_card,b_seeking,b_hv_not_show_cred_card,b_wht_bnk_issu_thm,b_hv_401k_ira,b_how_much,b_cred_usr,b_cred_pwd)"
                . "values ('".$rec_sel['customer_id']."','".$rec_sel['apply_dt']."','".$rec_sel['priority_opt']."','".$rec_sel['cust_apl_by']."','".$rec_sel['cust_upd_by']."','".$rec_sel['cust_upd_dt']."','".$rec_sel['agent']."','".$rec_sel['follow_up']."','".$rec_sel['funding_dt']."','".$rec_sel['amt_granted']
                ."','".$rec_sel['cred_review_fee_amt']."','".$rec_sel['cred_review_fee_dt_paid']."','".$rec_sel['cred_estab_fee_amt']."','".$rec_sel['cred_estab_fee_dt_paid']."','".$rec_sel['liq_fee_amt']."','".$rec_sel['liq_fee_dt_paid']
                ."','".$rec_sel['miscel_fee_amt']."','".$rec_sel['miscel_fee_dt_paid']."','".$rec_sel['df_cnslt_fee_amt']."','".$rec_sel['df_cnslt_fee_date_paid']."','".$rec_sel['cred_repair_fee_amt']."','".$rec_sel['cred_repair_fee_dt_paid']
                ."','".$rec_sel['lst_log_info']."','".$rec_sel['dur_info']."','".$rec_sel['calls_md_info']."','".$rec_sel['calls_conn_info']."','".$rec_sel['conv_min_info']."','".$rec_sel['eml_snd_info']
                ."','".$rec_sel['eml_rcv_info']."','".$rec_sel['lead_src']."','".$rec_sel['bst_time_to_call']."','".$rec_sel['p_psn2']."','".$rec_sel['p_fl_nm']."','".$rec_sel['p_ph1']."','".""."','".$rec_sel['p_eml1']."','".""."','".$rec_sel['p_amt_req']."','".$rec_sel['p_cmt']
                ."','".$fName ."','".$mName."','".$lName ."','".$hPhone."','".$hAddress."','".$Years 
                ."','".$City."','".$State."','".$zCode ."','".$Dob."','".$ss."','".$usCitizen 
                ."','".$mMaidenName ."','".$driveLicense ."','".$uiWord ."','".$haveFMService ."','".$whoFMService ."','".$haveAnyoneElse
                ."','".$whoAnyoneElse."','".$personalBank ."','".$stateBusiness."','".$legalBusinessName ."','".$entityType."','".$IndustryType 
                ."','".$federalTax ."','".$businessPhone."','".$fax ."','".$businessAddress ."','".$businessCity ."','".$businessState 
                ."','".$businessZCode ."','".$yearInBusiness."','".$employees ."','".$registeredBusiness ."','".$urlWebsite  ."','".$businessBank 
                ."','".$acceptCreditAspayment ."','".$merchantAccount ."','".$seeking ."','".$dontShowPearrsonalCredit."','".$urlShowPersonalCredit."','".$haveIRA 
                ."','".$haveIRAHowMuch ."','".$usrReport ."','".$pswReport ."')";
//    echo $sql_ins;
    mysql_query($sql_ins) or die(mysql_error());
    $flagg = 1;
    
    // auto mail sending to the signed up user start 
    if ($to != '')
    {
        $sql_mail = "insert into mail_log_info(customer_id, mail_rcvr,mail_subject,mail_body,send_dt,from_nm,from_address) values ('"."','".$rec_sel['customer_id'].$to."', '".mysql_real_escape_string($subject1)."', '".mysql_real_escape_string($message1)."',sysdate(), 'Administrator','".$from."')";
        mysql_query($sql_mail) or die(mysql_error()); 
    }
}
/********************************************************************************************/   


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