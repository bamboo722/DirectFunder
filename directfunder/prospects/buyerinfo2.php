<?php
//phpinfo();
/* * **-------------------------------------------------------------------**************************    

  Purpose     : 	Buyer Information Detail Page

  Project 	:	Sales Lead DB

  Developer 	: 	Kelvin Smith

  Create Date : 	12/17/2015

 * ***-------------------------------------------------------------------*********************** */
session_start();
if (!isset($_SESSION['user_login']) and ! isset($_COOKIE['cookie_login'])) {//session store admin name
    header("Location: index.php"); //login in AdminLogin.php
}
require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");	// Email
require_once("includes/GeeVee/GeeVeeAPI.php");		// Google Voice sms and call

 /*-- for tabs --*/
 //include('userprofile/controllers/base/meta-tags.php');
//include('userprofile/controllers/base/javascript.php');
//include('userprofile/controllers/base/meta-tags.php');
//include('userprofile/controllers/base/font.php');

   
    
//include('userprofile/controllers/base/font.php');        

/*function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}*/

//phpAlert("first");
if ($_POST['Quote'] == "Quote") {
    header("Location: sendindivquote.php?rid=" . $_GET['rid']);
    exit();
}

$filter = '';

if ($_POST['hdnTodaysFolloups'] == 1) {
    $filter = " AND follow_up = DATE(NOW()) AND priority_opt!='Delete'";
}

if ($_POST['hdnSevenDayOverdue'] == 1) {
    $filter = " AND follow_up < DATE(NOW()) AND follow_up >= DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) AND follow_up <> '0000-00-00' AND priority_opt!='Delete'";
}

if ($_POST['hdnThirtyDayOverdue'] == 1) {
    $filter = "AND follow_up < DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) AND follow_up <> '0000-00-00' AND  priority_opt!='Delete' ";
}


if ($_SESSION['hdnBuyingTimeThirty'] == 1) {
    $filter = " AND funding_dt >= DATE( NOW( ) ) AND funding_dt < DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <> '0000-00-00' ";
}

if ($_SESSION['hdnBuyingTimeSixty'] == 1) {
    $filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <> '0000-00-00' ";
}

if ($_SESSION['hdnBuyingTimeNinety'] == 1) {
    $filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <> '0000-00-00' ";
}

if ($_POST['is_opportunity'] == 1) {
    $filter = " AND is_opportunity_yes =1 ";
}

if ($_POST['no_follow_up_date'] == 1) {
    $filter = " AND ((follow_up is NULL) OR (follow_up = '0000-00-00')) AND priority_opt!='Clients' AND priority_opt!='Partners'  AND priority_opt!='Inactive'  AND priority_opt!='Not Interested'";
}

if ($_SESSION['hdnBuyingTimeEighty'] == 1) {
    $filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 180 DAY) AND funding_dt <> '0000-00-00' ";
}

if ($_SESSION['hdnVolume_Buyers'] == 1) {
    $filter = " AND equipment_volume_buyers = 1 ";
}

if ($_POST['priority_opt'] == "Delete") {
    $sql_del = "update customer_info set

			  priority_opt='Delete'

			  where customer_id='" . $_GET['rid'] . "'";

    mysql_query($sql_del) or die(mysql_error());

	/* update auto_dial_info table. delete phone number in auto dial list when a customer is deleted */
    $sql_del = sprintf("delete from auto_dial_info where  customer_id='%s'", $_GET['rid']);
	mysql_query($sql_del) or die(mysql_error());
			
    //inserting staff into staff_mast table end		 

	
	$_SESSION['search_manager']='';
    $_SESSION['search_agent']='';
    $_SESSION['search_customer']='';
    if ($_SESSION['user_group'] == "Admin")
    {
    	$_SESSION['search_manager']='search_manager';
		$_SESSION['search_agent']='search_agent';
		$_SESSION['search_customer']='search_customer';
	}else if ($_SESSION['user_group'] == "Manager")
    {
    	$_SESSION['search_agent']='search_agent';
    	$_SESSION['search_customer']='search_customer';
	}if ($_SESSION['user_group'] == "Agent")
    {
    	$_SESSION['search_customer']='search_customer';
	}
	
    header("Location: searchbuyer.php");

    exit();
}



if ($_POST['Cancel'] == "Cancel") {

    header("Location: buyerinfo2.php?rid=" . $_GET['rid'] . "&st=O");

    exit();
}

if ($_POST['Done'] == "Done") {

	$_SESSION['search_manager']='';
    $_SESSION['search_agent']='';
    $_SESSION['search_customer']='';
    if ($_SESSION['user_group'] == "Admin")
    {
    	$_SESSION['search_manager']='search_manager';
		$_SESSION['search_agent']='search_agent';
		$_SESSION['search_customer']='search_customer';
	}else if ($_SESSION['user_group'] == "Manager")
    {
    	$_SESSION['search_agent']='search_agent';
    	$_SESSION['search_customer']='search_customer';
	}if ($_SESSION['user_group'] == "Agent")
    {
    	$_SESSION['search_customer']='search_customer';
	}
	
    header("Location: searchbuyer.php");

    exit();
}
/* opportunity type name array */
 $opportuntiy_name_ary = array("Credit Review Fee","Credit Establishment Fee","Cash Liquidation Fee","Miscellaneous Fee","DF Consulting Fee","TaiTroVon Fee","Cash Advance","Equipment Financing","Other Financing Needs","Credit Pull","Credit Repair","Credit Monitoring","POS System","Credit Card Processing","Floor plan layout","3D store design","Real Estate Leasing","Lease Negotiation","Real Estat Purchase","Construction","Franchise Purchase","Web Design","Marketing Packages","Printing","Logo & Branding","LLC - Corporation Filing","Tax Advisor","Asset Protection Attorney","Liability Insurance","Equipment","Credit Establishment Fee","Supplies","Signage","","","","","","","","");
 
/* ------------------ Email Template Load ----------------------------- */
$sql_eml_templ = sprintf("select eml_templ_subj,eml_templ_cont,eml_templ_att,eml_templ_white from profile_info where user_username='%s'",$_SESSION['user_login']);
$resb_eml = mysql_query($sql_eml_templ) or die(mysql_error());	

if( mysql_num_rows($resb_eml) > 0) 
{
	$recb_eml = mysql_fetch_assoc($resb_eml);
	$eml_att = $recb_eml['eml_templ_att'];
	$eml_subj = $recb_eml['eml_templ_subj'];
	$eml_cont = $recb_eml['eml_templ_cont'];
	$eml_white = $recb_eml['eml_templ_white'];
	
}
/* --------------------------------------------------------------------- */


/* * ******************buyer information select start***************************** */

$buyer = "select * from customer_info where customer_id='" . $_GET['rid'] . "'";
$resb = mysql_query($buyer) or die(mysql_error() . "11");
$recb = mysql_fetch_assoc($resb);

// set the i variable to 0 to initialize row counter in the resultset
mysql_query("SET @i = -1; ") or die(mysql_error());

// get the row count (offset) of the customer_id
$sql_offset_position = "SELECT POSITION FROM (
				SELECT customer_id, @i:=@i+1 AS POSITION
				FROM customer_info 
				WHERE priority_opt!='Delete' " . $_SESSION['sql_order'] . "  
			 ) t WHERE customer_id = '" . $recb['customer_id'] . "'; ";

// echo $sql_offset_position;


$result_offset_position = mysql_query($sql_offset_position) or die(mysql_error());
$row_offset_position = mysql_fetch_assoc($result_offset_position);

if ($row_offset_position['POSITION'] > 0) {
    $sql_previous = "SELECT customer_id FROM customer_info WHERE priority_opt!='Delete' " . $_SESSION['sql_order'] . " LIMIT " . ($row_offset_position['POSITION'] - 1) . ",1 ";
    $result_previous = mysql_query($sql_previous) or die(mysql_error());
    $row_previous = mysql_fetch_assoc($result_previous);
}

$sql_next = "SELECT customer_id FROM customer_info WHERE priority_opt!='Delete' " . $_SESSION['sql_order'] . " LIMIT " . ($row_offset_position['POSITION'] + 1) . ",1 ";
$result_next = mysql_query($sql_next) or die(mysql_error());
$row_next = mysql_fetch_assoc($result_next);

$sql_record_count = "SELECT count(distinct customer_id) as totalCount from customer_info WHERE priority_opt!='Delete' " . $_SESSION['sql_order'];
$result_record_count = mysql_query($sql_record_count) or die(mysql_error());
$row_record_count = mysql_fetch_assoc($result_record_count);

/* Get Last login, Duration, Calls Made/Connected, Conversation Minutes, Emails Sent/Received */

if ($_POST['Submit'] != "Add")
{
	$recb['lst_log_info']= $_SESSION['last_logout'];
	$recb['dur_info'] = $_SESSION['duration'];
	$recb['calls_md_info'] = $_SESSION['calls_made'];
	$recb['calls_conn_info'] = $_SESSION['calls_con'];
	$recb['conv_min_info'] = $_SESSION['conv_minutes'];
	$recb['eml_snd_info'] = $_SESSION['email_sent'];
	$recb['eml_rcv_info'] = $_SESSION['email_recv'];
	$recb['sms_snd_info'] = $_SESSION['sms_sent'];
	$recb['sms_rcv_info'] = $_SESSION['sms_recv'];
	
	$recb['ratio'] = $_SESSION['ratio'];
}else
{
    $recb['lst_log_info'] = '';
    $recb['dur_info'] = '';
  //  phpAlert($recb['lst_log_info']);
}

/********************buyer information select end***************************** */


if ($_GET['ed'] == "T") {

    $log = "select * from conversation_log_info where auto_id='" . $_GET['aid'] . "'";

    $resl = mysql_query($log) or die(mysql_error() . "11");

    $recl = mysql_fetch_assoc($resl);
}

if ($_GET['ed'] == "edit") {

    $log = "select * from issues_log_info where auto_id='" . $_GET['aid'] . "'";

    $resIssues = mysql_query($log) or die(mysql_error() . "11");

    $recIssues = mysql_fetch_assoc($resIssues);
}

/* convert  mm/dd/yyyy to yyyy-mm-dd for insert data into mysql*/
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['apply_dt'])) == 0)
	$_POST['apply_dt']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['funding_dt'])) == 0)
	$_POST['funding_dt']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['cred_review_fee_dt_paid'])) == 0)
	$_POST['cred_review_fee_dt_paid']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['cred_estab_fee_dt_paid'])) == 0)
	$_POST['cred_estab_fee_dt_paid']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['liq_fee_dt_paid'])) == 0)
	$_POST['liq_fee_dt_paid']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['miscel_fee_dt_paid'])) == 0)
	$_POST['miscel_fee_dt_paid']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['df_cnslt_fee_date_paid'])) == 0)
	$_POST['df_cnslt_fee_date_paid']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['cred_repair_fee_dt_paid'])) == 0)
	$_POST['cred_repair_fee_dt_paid']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['p_dob'])) == 0)
	$_POST['p_dob']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['p2_dob'])) == 0)
	$_POST['p2_dob']=NULL;
if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['p3_dob'])) == 0)
	$_POST['p3_dob']=NULL;

if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['next_follow_up'])) == 0)
	$_POST['next_follow_up']=NULL;

if (isset($_POST['next_follow_up']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['next_follow_up'])) != 0)
	{
	   $follow_up = explode("/", $_POST['next_follow_up']);
       $_POST['next_follow_up'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}			
					
if (isset($_POST['apply_dt']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['apply_dt'])) != 0)
	{
		$follow_up = explode("/", $_POST['apply_dt']);
        $_POST['apply_dt'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
if (isset($_POST['funding_dt']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['funding_dt'])) != 0)
	{
		$follow_up = explode("/", $_POST['funding_dt']);
        $_POST['funding_dt'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
if (isset($_POST['cred_review_fee_dt_paid']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['cred_review_fee_dt_paid'])) != 0)
	{
		$follow_up = explode("/", $_POST['cred_review_fee_dt_paid']);
        $_POST['cred_review_fee_dt_paid'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
if (isset($_POST['cred_estab_fee_dt_paid']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['cred_estab_fee_dt_paid'])) != 0)
	{
		$follow_up = explode("/", $_POST['cred_estab_fee_dt_paid']);
        $_POST['cred_estab_fee_dt_paid'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  
	}
}
if (isset($_POST['liq_fee_dt_paid']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['liq_fee_dt_paid'])) != 0)
	{
		$follow_up = explode("/", $_POST['liq_fee_dt_paid']);
        $_POST['liq_fee_dt_paid'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
if (isset($_POST['miscel_fee_dt_paid']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['miscel_fee_dt_paid'])) != 0)
	{
		$follow_up = explode("/", $_POST['miscel_fee_dt_paid']);
        $_POST['miscel_fee_dt_paid'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
if (isset($_POST['df_cnslt_fee_date_paid']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['df_cnslt_fee_date_paid'])) != 0)
	{
		$follow_up = explode("/", $_POST['df_cnslt_fee_date_paid']);
        $_POST['df_cnslt_fee_date_paid'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  
	}
}
if (isset($_POST['cred_repair_fee_dt_paid']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['cred_repair_fee_dt_paid'])) != 0)
	{
		$follow_up = explode("/", $_POST['cred_repair_fee_dt_paid']);
        $_POST['cred_repair_fee_dt_paid'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  	
	}		
}
if (isset($_POST['p_dob']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['p_dob'])) != 0)
	{
		$follow_up = explode("/", $_POST['p_dob']);
        $_POST['p_dob'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}

if (isset($_POST['p2_dob']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['p2_dob'])) != 0)
	{
		$follow_up = explode("/", $_POST['p2_dob']);
        $_POST['p2_dob'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
if (isset($_POST['p3_dob']))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['p3_dob'])) != 0)
	{
		$follow_up = explode("/", $_POST['p3_dob']);
        $_POST['p3_dob'] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}	
}
/* -------------------------------------------------------------*/

/* convert yyyy-mm-dd to mm/dd/yyyy for output date  */
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['apply_dt'])) == 0)
	$recb['apply_dt']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['funding_dt'])) == 0)
	$recb['funding_dt']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['cred_review_fee_dt_paid'])) == 0)
	$recb['cred_review_fee_dt_paid']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['cred_estab_fee_dt_paid'])) == 0)
	$recb['cred_estab_fee_dt_paid']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['liq_fee_dt_paid'])) == 0)
	$recb['liq_fee_dt_paid']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['miscel_fee_dt_paid'])) == 0)
	$recb['miscel_fee_dt_paid']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['df_cnslt_fee_date_paid'])) == 0)
	$recb['df_cnslt_fee_date_paid']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['cred_repair_fee_dt_paid'])) == 0)
	$recb['cred_repair_fee_dt_paid']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['p_dob'])) == 0)
	$recb['p_dob']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['p2_dob'])) == 0)
	$recb['p2_dob']="";
if ((int)(preg_replace("/[^0-9]*/s", "",$recb['p3_dob'])) == 0)
	$recb['p3_dob']="";


if ($recb['apply_dt'] != "")
{
	$follow_up = explode("-", $recb['apply_dt']);
    $recb['apply_dt'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  			
}
if ($recb['funding_dt'] != "")
{
	$follow_up = explode("-", $recb['funding_dt']);
    $recb['funding_dt'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  			
}
if ($recb['cred_review_fee_dt_paid'] != "")
{
	$follow_up = explode("-", $recb['cred_review_fee_dt_paid']);
    $recb['cred_review_fee_dt_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['cred_estab_fee_dt_paid'] != "")
{
	$follow_up = explode("-", $recb['cred_estab_fee_dt_paid']);
    $recb['cred_estab_fee_dt_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['liq_fee_dt_paid'] != "")
{
	$follow_up = explode("-", $recb['liq_fee_dt_paid']);
    $recb['liq_fee_dt_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['miscel_fee_dt_paid'] != "")
{
	$follow_up = explode("-", $recb['miscel_fee_dt_paid']);
    $recb['miscel_fee_dt_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['df_cnslt_fee_date_paid'] != "")
{
	$follow_up = explode("-", $recb['df_cnslt_fee_date_paid']);
    $recb['df_cnslt_fee_date_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['cred_repair_fee_dt_paid'] != "")
{
	$follow_up = explode("-", $recb['cred_repair_fee_dt_paid']);
    $recb['cred_repair_fee_dt_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['p_dob'] != "")
{
	$follow_up = explode("-", $recb['p_dob']);
    $recb['p_dob'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  			
	
}
if ($recb['p2_dob'] != "")
{
	$follow_up = explode("-", $recb['p2_dob']);
    $recb['p2_dob'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
if ($recb['p3_dob'] != "")
{
	$follow_up = explode("-", $recb['p3_dob']);
    $recb['p3_dob'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  				
}
/* -------------------------------------------------------------*/

/* --------- convert all phone numbers like '**********' ------------*/
if (isset($_POST['p_ph1']))
	$_POST['p_ph1'] = preg_replace("/[^0-9]*/s", "",$_POST['p_ph1']);
if (isset($_POST['p_ph2']))
	$_POST['p_ph2'] = preg_replace("/[^0-9]*/s", "",$_POST['p_ph2']);
if (isset($_POST['p2_ph1']))
	$_POST['p2_ph1'] = preg_replace("/[^0-9]*/s", "",$_POST['p2_ph1']);
if (isset($_POST['p2_ph2']))
	$_POST['p2_ph2'] = preg_replace("/[^0-9]*/s", "",$_POST['p2_ph2']);
if (isset($_POST['p3_ph1']))
	$_POST['p3_ph1'] = preg_replace("/[^0-9]*/s", "",$_POST['p3_ph1']);
if (isset($_POST['p3_ph2']))
	$_POST['p3_ph2'] = preg_replace("/[^0-9]*/s", "",$_POST['p3_ph2']);
	
if (isset($recb['p_ph1']))
	$recb['p_ph1'] = preg_replace("/[^0-9]*/s", "",$recb['p_ph1']);
if (isset($recb['p_ph2']))
	$recb['p_ph2'] = preg_replace("/[^0-9]*/s", "",$recb['p_ph2']);
if (isset($recb['p2_ph1']))
	$recb['p2_ph1'] = preg_replace("/[^0-9]*/s", "",$recb['p2_ph1']);
if (isset($recb['p2_ph2']))
	$recb['p2_ph2'] = preg_replace("/[^0-9]*/s", "",$recb['p2_ph2']);
if (isset($recb['p3_ph1']))
	$recb['p3_ph1'] = preg_replace("/[^0-9]*/s", "",$recb['p3_ph1']);
if (isset($recb['p3_ph2']))
	$recb['p3_ph2'] = preg_replace("/[^0-9]*/s", "",$recb['p3_ph2']);
/* ------------------------------------------------------------------ */


if ($_POST['Submit'] == "Change") {
	
	/* Priority Change Log */
	if ($recb['priority_opt'] != $_POST['priority_opt'])
	{
		mysql_query("set time_zone='-7:00';");
		$prio_log = "insert into priority_change_log_info (change_time,old_prio,new_prio,agent_by,customer_id) values (sysdate(),'" .$recb['priority_opt']."','".$_POST['priority_opt']."','".$_SESSION['user_login']."','".$_GET['rid']."')";
		mysql_query($prio_log) or die(mysql_error());
	}
	/*=====================*/
	
    if ($_POST['priority_opt'] == "Delete") {
        $sql_del = "update customer_info set

				  priority_opt='Delete'

				  where customer_id='" . $_GET['rid'] . "'";

        mysql_query($sql_del) or die(mysql_error());

        //inserting staff into staff_mast table end		 
		
		$_SESSION['search_manager']='';
	    $_SESSION['search_agent']='';
	    $_SESSION['search_customer']='';
	    if ($_SESSION['user_group'] == "Admin")
	    {
	    	$_SESSION['search_manager']='search_manager';
			$_SESSION['search_agent']='search_agent';
			$_SESSION['search_customer']='search_customer';
		}else if ($_SESSION['user_group'] == "Manager")
	    {
	    	$_SESSION['search_agent']='search_agent';
	    	$_SESSION['search_customer']='search_customer';
		}if ($_SESSION['user_group'] == "Agent")
	    {
	    	$_SESSION['search_customer']='search_customer';
		}
	
        header("Location: searchbuyer.php");

        exit();
    } else {

        //checking whether nessary fields are empty 

        if ($_POST['apply_dt'] == "") {

            $msg = "Please Enter Date.";
        } elseif ($_POST['p_fl_nm'] == "") {

            $msg = "Please Enter Name.";
        }

        //checking whether nessary fields are empty end
        else {
        
        	/* update opportunity_info table */
			
			$sql_del = sprintf("delete from opportunity_info where customer_id = '%s'",$_GET['rid']);
			$res_sql = mysql_query($sql_del) or die(mysql_error() . "go select error");
			
			for ($i=0;$i<count($_POST['op_opportunity']); $i++)
			{
				/* convert DatePaid format from mm/dd/yyyy to yyyy-mm-dd */
				if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['op_date_paid'][$i])) == 0)
					$_POST['op_date_paid'][$i]=NULL;
				if (isset($_POST['op_date_paid'][$i]))
				{
					if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['op_date_paid'][$i])) != 0)
					{
						$follow_up = explode("/", $_POST['op_date_paid'][$i]);
				        $_POST['op_date_paid'][$i] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
					}		
				}
				
				/* conver phone type *********** : only numbers and erase space */
				
				if (isset($_POST['op_ph1'][$i]))
					$_POST['op_ph1'][$i] = preg_replace("/[^0-9]*/s", "",$_POST['op_ph1'][$i]);
				if (isset($_POST['op_ph2'][$i]))
					$_POST['op_ph2'][$i] = preg_replace("/[^0-9]*/s", "",$_POST['op_ph2'][$i]);					
				
				if ($i>=7 and $_POST['op_yes_no'][$i] == "Yes")
					$is_opportunity_yes = 1;
				$sql_ins = sprintf("insert into opportunity_info (customer_id,opportunity,yes_no,referal_company_name,referal_person_name,phone1,phone2,best_email,fee_amount,date_paid,notes) values ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$_GET['rid'],$_POST['op_opportunity'][$i],$_POST['op_yes_no'][$i],$_POST['op_ref_comp_nm'][$i],$_POST['op_ref_pers_nm'][$i],$_POST['op_ph1'][$i],$_POST['op_ph2'][$i],$_POST['op_bst_eml'][$i],$_POST['op_fee_amt'][$i],$_POST['op_date_paid'][$i],$_POST['op_notes'][$i]);
				$res_sel =  mysql_query($sql_ins) or die(mysql_error() . "go select error");
			}
			
			/* update funding_info table */
			
			$sql_del = sprintf("delete from funding_info where customer_id = '%s'",$_GET['rid']);
			$res_sql = mysql_query($sql_del) or die(mysql_error() . "go select error");
			
			for ($i=0;$i<count($_POST['fun_cd_name']); $i++)
			{
				if ($_POST['fun_cd_name'][$i]!='')
				{
					/* convert DatePaid format from mm/dd/yyyy to yyyy-mm-dd */
					if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['fun_expire_dt'][$i])) == 0)
						$_POST['fun_expire_dt'][$i]=NULL;
					if (isset($_POST['fun_expire_dt'][$i]))
					{
						if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['fun_expire_dt'][$i])) != 0)
						{
							$follow_up = explode("/", $_POST['fun_expire_dt'][$i]);
					        $_POST['fun_expire_dt'][$i] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
						}		
					}				
					
					$sql_ins = sprintf("insert into funding_info (customer_id,cd_name,cd_number,expire_dt,secu_code,appr_lim,incr_lim,cash_adv,issu_bnk,bnk_ph,bnk_web) values ('%s','%s','%s','%s','%s','%10.2f','%10.2f','%10.2f','%s','%s','%s')",$_GET['rid'],$_POST['fun_cd_name'][$i],$_POST['fun_cd_number'][$i],$_POST['fun_expire_dt'][$i],$_POST['fun_secu_code'][$i],$_POST['fun_appr_lim'][$i],$_POST['fun_incr_lim'][$i],$_POST['fun_cash_adv'][$i],$_POST['fun_issu_bnk'][$i],$_POST['fun_bnk_ph'][$i],$_POST['fun_bnk_web'][$i]);
					$res_sel =  mysql_query($sql_ins) or die(mysql_error() . "go select error");
				}				
			}
			
			/* update auto_dial_info table. so update phone number in auto dial list */
            $sql_upd = sprintf("update auto_dial_info set user_id='%s',ph_1='%s',ph_2='%s',duration='%d',is_called='%d',called_number='%d' where customer_id='%s'", $_POST['user_id'],$_POST['p_ph1'],$_POST['p_ph2'],0,0,0,$_GET['rid']);
			mysql_query($sql_upd) or die(mysql_error());
		
			
            $sql_upd = "update customer_info set 
					 					  priority_opt='" . $_POST['priority_opt'] . "',
                                          p_fl_nm = '" . $_POST['p_fl_nm'] . "',
                                          p_psn2 = '" . $_POST['p_psn2'] . "',
                                          p_psn3 = '" . $_POST['p_psn3'] . "',
                                          b_leg_nm='" . $_POST['b_leg_nm'] . "',          
                                          b_new_buss='" . $_POST['b_new_buss'] . "',          
                                          remarks='" . $_POST['remarks'] . "',   
                                          p1_cred_pwd='" . $_POST['p1_cred_pwd'] . "',     
                                          p1_cred_usr='" . $_POST['p1_cred_usr'] . "',      
                                          p2_cred_pwd='" . $_POST['p2_cred_pwd'] . "',     
                                          p2_cred_usr='" . $_POST['p2_cred_usr'] . "',      
                                          p3_cred_pwd='" . $_POST['p3_cred_pwd'] . "',     
                                          p3_cred_usr='" . $_POST['p3_cred_usr'] . "',     
                                          p_dob='" . $_POST['p_dob'] . "',     
                                          p_ss='" . $_POST['p_ss'] . "',      
                                          p2_dob='" . $_POST['p2_dob'] . "',     
                                          p2_ss='" . $_POST['p2_ss'] . "',                                      
                                          p3_dob='" . $_POST['p3_dob'] . "',     
                                          p3_ss='" . $_POST['p3_ss'] . "', 
                                          p2_relation='" . $_POST['p2_relation'] . "',     
                                          p3_relation='" . $_POST['p3_relation'] . "',     
                                          apply_dt='" . $_POST['apply_dt'] . "',
                                          p_hm_addr='" . $_POST['p_hm_addr'] . "',                                              
                                          p2_hm_addr='" . $_POST['p2_hm_addr'] . "',     
                                          p3_hm_addr='" . $_POST['p3_hm_addr'] . "',     
                                          lead_src='" . $_POST['lead_src'] . "',
                                          p_ph1='" . $_POST['p_ph1'] . "',
                                          p_city='" . $_POST['p_city'] . "',
                                          p2_city='" . $_POST['p2_city'] . "',
                                          p3_city='" . $_POST['p3_city'] . "',
                                          agent = '" . $_POST['user_id'] . "',
                                          manager = '" . $_POST['manager_id'] . "',
                                          p_ph2='" . $_POST['p_ph2'] . "',
                                          p2_ph1='" . $_POST['p2_ph1'] . "',
                                          p2_ph2='" . $_POST['p2_ph2'] . "',
                                          p3_ph1='" . $_POST['p3_ph1'] . "',
                                          p3_ph2='" . $_POST['p3_ph2'] . "',
                                          p_state='" . $_POST['p_state'] . "',
                                          p2_state='" . $_POST['p2_state'] . "',
                                          p3_state='" . $_POST['p3_state'] . "',
                                          funding_dt='" . $_POST['funding_dt'] . "',
                                          p_eml1='" . $_POST['p_eml1'] . "',
                                          p_eml2='" . $_POST['p_eml2'] . "',
                                          p2_eml='" . $_POST['p2_eml'] . "',
                                          p3_eml='" . $_POST['p3_eml'] . "',
					 					  p_zip = '" . $_POST['p_zip'] . "',
					 					  p2_zip = '" . $_POST['p2_zip'] . "',
					 					  p3_zip = '" . $_POST['p3_zip'] . "',
                                          b_addr = '" . $_POST['b_addr'] . "',
                                          is_opportunity_yes = '" .  $is_opportunity_yes . "',                                         
                                          bst_time_to_call='" . $_POST['bst_time_to_call'] . "',
					  					  cust_upd_by='" . $_SESSION['user_login'] . "',
										  cust_upd_dt= curdate(),
										  b_cred_usr = '" . $_POST['b_cred_usr'] . "', 
										  b_cred_pwd = '" . $_POST['b_cred_pwd'] . "', 
										  p_amt_req = '" . $_POST['p_amt_req'] . "', 
										  amt_granted = '" . $_POST['amt_granted'] . "', 
										  cred_review_fee_amt = '" . $_POST['cred_review_fee_amt'] . "', 
										  cred_review_fee_dt_paid = '" . $_POST['cred_review_fee_dt_paid'] . "', 
										  cred_estab_fee_amt = '" . $_POST['cred_estab_fee_amt'] . "', 
										  cred_estab_fee_dt_paid = '" . $_POST['cred_estab_fee_dt_paid'] . "', 
										  liq_fee_amt = '" . $_POST['liq_fee_amt'] . "', 
										  liq_fee_dt_paid = '" . $_POST['liq_fee_dt_paid'] . "', 
										  miscel_fee_amt = '" . $_POST['miscel_fee_amt'] . "', 
										  miscel_fee_dt_paid = '" . $_POST['miscel_fee_dt_paid'] . "', 
										  df_cnslt_fee_amt = '" . $_POST['df_cnslt_fee_amt'] . "',
                                          df_cnslt_fee_date_paid = '" . $_POST['df_cnslt_fee_date_paid'] . "', 
					  					  cred_repair_fee_amt = '" . $_POST['cred_repair_fee_amt'] . "',
                                          b_leg_nm = '" . $_POST['b_leg_nm'] . "',
                                          b_ind_typ = '" . $_POST['b_ind_typ'] . "',
                                          b_addr = '" . $_POST['b_addr'] . "',
                                          b_city = '" . $_POST['b_city'] . "',
                                          b_state = '" . $_POST['b_state'] . "',
                                          b_zip = '" . $_POST['b_zip'] . "',
                                          b_fed_tax_id = '" . $_POST['b_fed_tax_id'] . "',
                                          b_ph = '" . $_POST['b_ph'] . "',
                                          b_own_lease = '" . $_POST['b_own_lease'] . "',
                                          b_facs_no = '" . $_POST['b_facs_no'] . "',
                                          b_eml = '" . $_POST['b_eml'] . "',
                                          b_acc = '" . $_POST['b_acc'] . "',
                                          b_acc_ph = '" . $_POST['b_acc_ph'] . "',
                                          b_ent_typ = '" . $_POST['b_ent_typ'] . "',
                                          b_ye_busi = '" . $_POST['b_ye_busi'] . "',
                                          b_reg_state = '" . $_POST['b_reg_state'] . "',
                                          b_type = '" . $_POST['b_type'] . "',
                                          b_addition_addr = '" . $_POST['b_addition_addr'] . "',
                                          b_landlord = '" . $_POST['b_landlord'] . "',
                                          b_landlord_ph = '" . $_POST['b_landlord_ph'] . "',
                                          b_landlord_fr = '" . $_POST['b_landlord_fr'] . "',
                                          b_landlord_to = '" . $_POST['b_landlord_to'] . "',
                                          b_landlord_month_pmt = '" . $_POST['b_landlord_month_pmt'] . "',
                                          b_landlord_renew_opt = '" . $_POST['b_landlord_renew_opt'] . "',
                                          b_landlord_renew_ye = '" . $_POST['b_landlord_renew_ye'] . "',
                                          b_landlord_payment = '" . $_POST['b_landlord_payment'] . "',
                                          b_landlord_approx = '" . $_POST['b_landlord_approx'] . "',
                                          b_empl = '" . $_POST['b_empl'] . "',
                                          b_cash = '" . $_POST['b_cash'] . "',
                                          b_amex = '" . $_POST['b_amex'] . "',
                                          b_vs_mc = '" . $_POST['b_vs_mc'] . "',
                                          b_other = '" . $_POST['b_other'] . "',
                                          b_is_season = '" . $_POST['b_is_season'] . "',
                                          b_month_season_begin = '" . $_POST['b_month_season_begin'] . "',                                          
                                          b_month_season_to = '" . $_POST['b_month_season_to'] . "'
					  where customer_id ='" . $_GET['rid'] . "'";
           
            mysql_query($sql_upd) or die(mysql_error());

			
		
		    
			
				
			/* Save conversion log if subject is set */	
            //if (($_POST['spoke_to'] != '') or ($_POST['next_follow_up'] != '') or ($_POST['out_come'] != '')) {
            if ($_POST['out_come'] != '') {
            	
        		
                $follow_up = $_POST['next_follow_up'];                

				mysql_query("set time_zone='-7:00';");
                $sql_ins = "insert into conversation_log_info(customer_id,log_time,out_come,spoke_to,next_follow_up,record_by,record_dt)values('" . $_GET['rid'] . "',sysdate(),'" . clean($_POST['out_come']) . "','" . clean($_POST['spoke_to']) . "','" . clean($follow_up) . "','" . $_SESSION['user_login'] . "',curdate())";

                mysql_query($sql_ins) or die(mysql_error());
            }

            $follow_up = $_POST['next_follow_up'];
           /* $follow_up = explode("/", $follow_up);
            $follow_up = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];// mm/dd/yyyy -> yyyy-mm-dd*/

            /* update follow up date of buyer mast table */

            $mast_upd = "update customer_info set

			  follow_up='" . clean($follow_up) . "'

			  where customer_id='" . $_GET['rid'] . "'";

            mysql_query($mast_upd) or die(mysql_error());

            // log_subject
            //inserting staff into staff_mast table end		 

            header("Location: buyerinfo2.php?rid=" . $_GET['rid']);

            exit();
        }
    }
}

if ($_POST['Submit'] == "Add") {

    //checking whether nessary fields are empty 
 
    if ($_POST['apply_dt'] == "") {

        $msg = "Please Enter Date.";
    } elseif ($_POST['p_fl_nm'] == "") {

        $msg = "Please Enter Name.";
    }

    //checking whether nessary fields are empty end
    else {

        $sql_sel = "select max(customer_id) mxid from customer_info";
        $res_sel = mysql_query($sql_sel) or die(mysql_error() . "11");
        $rec_sel = mysql_fetch_assoc($res_sel);

        if ($rec_sel['mxid'] == '') {
            $recid = 'BY00000001';
        } else {
            $schlid = "select concat(substring(customer_id,1,2),lpad(max(convert(substring(customer_id,3),signed))+1,8,'0')) recid from customer_info";
            $schres = mysql_query($schlid) or die(mysql_error() . "go select error");
            $schrec = mysql_fetch_assoc($schres);
            $recid = $schrec['recid'];
        }
       
         /* insert opportunity_info table */
			
		$sql_del = sprintf("delete from opportunity_info where customer_id = '%s'",$recid);
		$res_sql = mysql_query($sql_del) or die(mysql_error() . "go select error");
			
		for ($i=0;$i<count($_POST['op_opportunity']); $i++)
		{
			/* convert DatePaid format from mm/dd/yyyy to yyyy-mm-dd */
			if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['op_date_paid'][$i])) == 0)
				$_POST['op_date_paid'][$i]=NULL;
			if (isset($_POST['op_date_paid'][$i]))
			{
				if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['op_date_paid'][$i])) != 0)
				{
					$follow_up = explode("/", $_POST['op_date_paid'][$i]);
			        $_POST['op_date_paid'][$i] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
				}		
			}
				
			/* conver phone type *********** : only numbers and erase space */
			
			if (isset($_POST['op_ph1'][$i]))
				$_POST['op_ph1'][$i] = preg_replace("/[^0-9]*/s", "",$_POST['op_ph1'][$i]);
			if (isset($_POST['op_ph2'][$i]))
				$_POST['op_ph2'][$i] = preg_replace("/[^0-9]*/s", "",$_POST['op_ph2'][$i]);					
				
			if ($i >= 7 and $_POST['op_yes_no'][$i] == "Yes")
				$is_opportunity_yes = 1;
			

			$sql_ins = sprintf("insert into opportunity_info (customer_id,opportunity,yes_no,referal_company_name,referal_person_name,phone1,phone2,best_email,fee_amount,date_paid,notes) values ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$recid,$_POST['op_opportunity'][$i],$_POST['op_yes_no'][$i],$_POST['op_ref_comp_nm'][$i],$_POST['op_ref_pers_nm'][$i],$_POST['op_ph1'][$i],$_POST['op_ph2'][$i],$_POST['op_bst_eml'][$i],$_POST['op_fee_amt'][$i],$_POST['op_date_paid'][$i],$_POST['op_notes'][$i]);
			$res_sel =  mysql_query($sql_ins) or die(mysql_error() . "go select error");
		}

 		/* insert funding_info table */
			
		$sql_del = sprintf("delete from funding_info where customer_id = '%s'",$_GET['rid']);
		$res_sql = mysql_query($sql_del) or die(mysql_error() . "go select error");
		
		for ($i=0;$i<count($_POST['fun_cd_name']); $i++)
		{
			if ($_POST['fun_cd_name'][$i]!='')
			{
				/* convert DatePaid format from mm/dd/yyyy to yyyy-mm-dd */
				if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['fun_expire_dt'][$i])) == 0)
					$_POST['fun_expire_dt'][$i]=NULL;
				if (isset($_POST['fun_expire_dt'][$i]))
				{
					if ((int)(preg_replace("/[^0-9]*/s", "",$_POST['fun_expire_dt'][$i])) != 0)
					{
						$follow_up = explode("/", $_POST['fun_expire_dt'][$i]);
				        $_POST['fun_expire_dt'][$i] = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
					}		
				}				
				
				$sql_ins = sprintf("insert into funding_info (customer_id,cd_name,cd_number,expire_dt,secu_code,appr_lim,incr_lim,cash_adv,issu_bnk,bnk_ph,bnk_web) values ('%s','%s','%s','%s','%s','%10.2f','%10.2f','%10.2f','%s','%s','%s')",$_GET['rid'],$_POST['fun_cd_name'][$i],$_POST['fun_cd_number'][$i],$_POST['fun_expire_dt'][$i],$_POST['fun_secu_code'][$i],$_POST['fun_appr_lim'][$i],$_POST['fun_incr_lim'][$i],$_POST['fun_cash_adv'][$i],$_POST['fun_issu_bnk'][$i],$_POST['fun_bnk_ph'][$i],$_POST['fun_bnk_web'][$i]);
				$res_sel =  mysql_query($sql_ins) or die(mysql_error() . "go select error");
			}
		}			
		
        $sql_ins = "insert into customer_info (customer_id,priority_opt,p_fl_nm,p_ph1,p_ph2,apply_dt,p_eml1,p_dob,p_ss,lead_src,p_eml2,p1_cred_usr,p1_cred_pwd,agent,p_hm_addr,p_city,p_state,manager,p_psn2,p2_ph1,p2_ph2,funding_dt,p2_relation,p2_dob,p2_ss,bst_time_to_call,p2_eml,p2_cred_usr,p2_cred_pwd,b_new_buss,p2_hm_addr,p2_city,p2_state,remarks,p_psn3,p3_ph1,p3_ph2,p3_relation,p3_dob,p3_ss,p3_eml,p3_cred_usr,p3_cred_pwd,p3_hm_addr,p3_city,p3_state,b_cred_usr,b_cred_pwd,amt_granted,cred_review_fee_amt,cred_review_fee_dt_paid,cred_estab_fee_amt,cred_estab_fee_dt_paid,liq_fee_amt,liq_fee_dt_paid,miscel_fee_amt,miscel_fee_dt_paid,df_cnslt_fee_amt,df_cnslt_fee_date_paid,cred_repair_fee_amt,cred_repair_fee_dt_paid,b_leg_nm,b_ind_typ,b_addr,b_city,b_state,b_zip,b_fed_tax_id,b_ph,b_own_lease,b_facs_no,b_eml,b_acc,b_acc_ph,b_ent_typ,b_ye_busi,b_reg_state,b_type,b_addition_addr,b_landlord,b_landlord_ph,b_landlord_fr,b_landlord_to,b_landlord_month_pmt,b_landlord_renew_opt,b_landlord_renew_ye,b_landlord_payment,b_landlord_approx,b_empl,b_cash,b_amex,b_vs_mc,b_other,b_is_season,b_month_season_begin,b_month_season_to,is_opportunity_yes,p_zip,p2_zip,p3_zip) values ('".$recid."','".$_POST['priority_opt']."','".$_POST['p_fl_nm']."','".$_POST['p_ph1']."','".$_POST['p_ph2']."','".$_POST['apply_dt']."','".$_POST['p_eml1']."','".$_POST['p_dob']."','".$_POST['p_ss']."','".$_POST['lead_src']."','".$_POST['p_eml2']."','".$_POST['p1_cred_usr']."','".$_POST['p1_cred_pwd']."','".$_POST['user_id']."','".$_POST['p_hm_addr']."','".$_POST['p_city']."','".$_POST['p_state']."','".$_POST['manager']."','".$_POST['p_psn2']."','".$_POST['p2_ph1']."','".$_POST['p2_ph2']."','".$_POST['funding_dt']."','".$_POST['p2_relation']."','".$_POST['p2_dob']."','".$_POST['p2_ss']."','".$_POST['bst_time_to_call']."','".$_POST['p2_eml']."','".$_POST['p2_cred_usr']."','".$_POST['p2_cred_pwd']."','".$_POST['b_new_buss']."','".$_POST['p2_hm_addr']."','".$_POST['p2_city']."','".$_POST['p2_state']."','".$_POST['remarks']."','".$_POST['p_psn3']."','".$_POST['p3_ph1']."','".$_POST['p3_ph2']."','".$_POST['p3_relation']."','".$_POST['p3_dob']."','".$_POST['p3_ss']."','".$_POST['p3_eml']."','".$_POST['p3_cred_usr']."','".$_POST['p3_cred_pwd']."','".$_POST['p3_hm_addr']."','".$_POST['p3_city']."','".$_POST['p3_state']."','".$_POST['b_cred_usr']."','".$_POST['b_cred_pwd']."','".$_POST['amt_granted']."','".$_POST['cred_review_fee_amt']."','".$_POST['cred_review_fee_dt_paid']."','".$_POST['cred_estab_fee_amt']."','".$_POST['cred_estab_fee_dt_paid']."','".$_POST['liq_fee_amt']."','".$_POST['liq_fee_dt_paid']."','".$_POST['miscel_fee_amt']."','".$_POST['miscel_fee_dt_paid']."','".$_POST['df_cnslt_fee_amt']."','".$_POST['df_cnslt_fee_date_paid']."','".$_POST['cred_repair_fee_amt']."','".$_POST['cred_repair_fee_dt_paid']."','".$_POST['b_leg_nm']."','".$_POST['b_ind_typ']."','".$_POST['b_addr']."','".$_POST['b_city']."','".$_POST['b_state']."','".$_POST['b_zip']."','".$_POST['b_fed_tax_id']."','".$_POST['b_ph']."','".$_POST['b_own_lease']."','".$_POST['b_facs_no']."','".$_POST['b_eml']."','".$_POST['b_acc']."','".$_POST['b_acc_ph']."','".$_POST['b_ent_typ']."','".$_POST['b_ye_busi']."','".$_POST['b_reg_state']."','".$_POST['b_type']."','".$_POST['b_addition_addr']."','".$_POST['b_landlord']."','".$_POST['b_landlord_ph']."','".$_POST['b_landlord_fr']."','".$_POST['b_landlord_to']."','".$_POST['b_landlord_month_pmt']."','".$_POST['b_landlord_renew_opt']."','".$_POST['b_landlord_renew_ye']."','".$_POST['b_landlord_payment']."','".$_POST['b_landlord_approx']."','".$_POST['b_empl']."','".$_POST['b_cash']."','".$_POST['b_amex']."','".$_POST['b_vs_mc']."','".$_POST['b_other']."','".$_POST['b_is_season']."','".$_POST['b_month_season_begin']."','".$_POST['b_month_season_to']."','".$is_opportunity_yes."','".$_POST['p_zip']."','".$_POST['p2_zip']."','".$_POST['p3_zip']."')";
        mysql_query($sql_ins) or die(mysql_error());
        
        /* insert into auto_dial_info table. so add new phone number into auto dial list */
        $sql_ins = sprintf("insert into auto_dial_info (user_id,customer_id,ph_1,ph_2,duration,is_called,called_number) values ('%s','%s','%s','%s','%d','%d','%d')",$_POST['user_id'],$recid,$_POST['p_ph1'],$_POST['p_ph2'],0,0,0);
		mysql_query($sql_ins) or die(mysql_error());
        
        $_SESSION['search_manager']='';
	    $_SESSION['search_agent']='';
	    $_SESSION['search_customer']='';
	    if ($_SESSION['user_group'] == "Admin")
	    {
	    	$_SESSION['search_manager']='search_manager';
			$_SESSION['search_agent']='search_agent';
			$_SESSION['search_customer']='search_customer';
		}else if ($_SESSION['user_group'] == "Manager")
	    {
	    	$_SESSION['search_agent']='search_agent';
	    	$_SESSION['search_customer']='search_customer';
		}if ($_SESSION['user_group'] == "Agent")
	    {
	    	$_SESSION['search_customer']='search_customer';
		}
	
        header("Location: searchbuyer.php");
        exit();
    }
}


if ($_POST['AddIssue'] == "Save Issue Log") {
    $sql_ins = "insert into issues_log_info
			(
			  customer_id,
			  `log_time`,
			  `issue`,
			  `outcome`,
			  `performed_by`
			) 
			values
			 (
			 '" . $_GET['rid'] . "',
			 CURRENT_TIMESTAMP(),
			 '" . clean($_POST['issue']) . "',
			 '" . clean($_POST['outcome']) . "',
			 '" . clean($_POST['performed_by']) . "'
			 )";

    mysql_query($sql_ins) or die(mysql_error());
}

if ($_POST['Add'] == "Save Call Log") {

    $follow_up = $_POST['next_follow_up'];
    /*$follow_up = explode("/", $follow_up);
    $follow_up = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];*/
    
    mysql_query("set time_zone='-7:00';");
    $sql_ins = "insert into conversation_log_info(customer_id,log_time,out_come,spoke_to,next_follow_up,record_by,record_dt) values('" . $_GET['rid'] . "', sysdate(),'" . clean($_POST['out_come']) . "','" . clean($_POST['spoke_to']) . "','" . clean($follow_up) . "','" . $_SESSION['user_login'] . "',curdate())";

    mysql_query($sql_ins) or die(mysql_error());

    /* update follow up date of buyer mast table */

    $mast_upd = "update customer_info set

			  follow_up='" . clean($_POST['next_follow_up']) . "'

			  where customer_id='" . $_GET['rid'] . "'";

    mysql_query($mast_upd) or die(mysql_error());

    /* update follow up date of buyer mast table end */



    //inserting staff into staff_mast table end		 

    header("Location: buyerinfo2.php?rid=" . $_GET['rid']);

    exit();
}

if ($_POST['EditIssue'] == "Edit Issue Log") {
    $log_upd = "update issues_log_info set
			  outcome='" . $_POST['outcome'] . "',
			  performed_by='" . $_POST['performed_by'] . "',
			  issue='" . $_POST['issue'] . "' 
			  where auto_id='" . $_GET['aid'] . "'";

    mysql_query($log_upd) or die(mysql_error());

    //inserting staff into staff_mast table end		 

    header("Location: buyerinfo2.php?rid=" . $_GET['rid'] . "&st=O");

    exit();
}


if ($_POST['Edit'] == "Edit Log") {
    $follow_up = $_POST['next_follow_up'];
   /* $follow_up = explode("/", $follow_up);
    $follow_up = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];*/
	mysql_query("set time_zone='-7:00';");
    $log_upd = "update conversation_log_info set log_time=sysdate(),
			  out_come='" . $_POST['out_come'] . "',
			  spoke_to='" . $_POST['spoke_to'] . "',
			  next_follow_up='" . $follow_up . "',
			  update_by='" . $_SESSION['user_login'] . "',
			  update_dt=curdate()
			  where auto_id='" . $_GET['aid'] . "'";

    mysql_query($log_upd) or die(mysql_error());

    //inserting staff into staff_mast table end		 

    header("Location: buyerinfo2.php?rid=" . $_GET['rid'] . "&st=O");

    exit();
}

$filterAgent = '';

if ($_SESSION['user_group'] == 'Agent')
    $filterAgent = " and agent='" . $_SESSION['user_login'] . "'";

if ($_SESSION['user_group'] == 'Manager')
    $filterAgent = " and agent='" . $_SESSION['user_login'] . "'";
    
// Todays's Task
$sql_follow_up = "SELECT count(customer_id) as followUpCount from customer_info where follow_up = DATE(NOW()) AND priority_opt!='Delete' $filterAgent";
$result_followup = mysql_query($sql_follow_up) or die(mysql_error());
$followup_count_array = mysql_fetch_assoc($result_followup);

// Past Due
$sql_seven_overdue = "SELECT count(customer_id) as overdueCount from customer_info where follow_up < DATE(NOW()) AND follow_up >= DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) AND follow_up <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_seven_overdue = mysql_query($sql_seven_overdue) or die(mysql_error());
$sevenoverdue_count_array = mysql_fetch_assoc($result_seven_overdue);

// Delinquent
$sql_thirty_overdue = "SELECT count(customer_id) as overdueCount from customer_info where follow_up < DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) AND follow_up <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_thirty_overdue = mysql_query($sql_thirty_overdue) or die(mysql_error());
$thirty_count_array = mysql_fetch_assoc($result_thirty_overdue);

// no follow up date
$sql_follow_up = "SELECT count(customer_id) as no_follow_up_date_count from customer_info where ((follow_up is null) or (follow_up = '0000-00-00')) AND priority_opt!='Delete'   AND priority_opt!='Clients' AND priority_opt!='Partners'  AND priority_opt!='Inactive'  AND priority_opt!='Not Interested' $filterAgent ";
$result_followup = mysql_query($sql_follow_up) or die(mysql_error());
$no_follow_up_date_array = mysql_fetch_assoc($result_followup);

// Funding Time within 30 days
$sql_buying_time_thirty = "SELECT count(customer_id) as dueCount from customer_info where funding_dt >= DATE( NOW( ) ) AND funding_dt < DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_thirty = mysql_query($sql_buying_time_thirty) or die(mysql_error());
$buying_time_thirty_count_array = mysql_fetch_assoc($result_buying_time_thirty);

// Funding Time within 31-60 days
$sql_buying_time_thirty_sixty = "SELECT count(customer_id) as dueCount from customer_info where funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_thirty_sixty = mysql_query($sql_buying_time_thirty_sixty) or die(mysql_error());
$buying_time_thirty_sixty_count_array = mysql_fetch_assoc($result_buying_time_thirty_sixty);

// Funding Time within 61-90 days
$sql_buying_time_sixty_ninety = "SELECT count(customer_id) as dueCount from customer_info where funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_sixty_ninety = mysql_query($sql_buying_time_sixty_ninety) or die(mysql_error());
$buying_time_sixty_ninety_count_array = mysql_fetch_assoc($result_buying_time_sixty_ninety);

// Funding Time within 91-180 days
/* $sql_buying_time_ninety_eighty = "SELECT count(customer_id) as dueCount from customer_info where funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 180 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
  $result_buying_time_ninety_eighty = mysql_query($sql_buying_time_ninety_eighty) or die(mysql_error());
  $buying_time_ninety_eighty_count_array = mysql_fetch_assoc($result_buying_time_ninety_eighty); */

// Call, Email, Sms
if ($_SESSION['call'] == 0)
{
	
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

	mysql_query("set time_zone='-7:00';");
    $sql_cur_time="select sysdate() as cur_time;";  
	$res_cur_time=mysql_query($sql_cur_time) or die(mysql_error()."11");	
	$rec_cur_time = mysql_fetch_assoc($res_cur_time);
	$_SESSION['logout_call_get_time'] =$rec_cur_time['cur_time'];
	$_SESSION['logout_sms_get_time'] =$rec_cur_time['cur_time'];
	
	$phone = '+1'.$_SESSION['google_voice_ph'];

	// Call
	if ($_SESSION['user_group'] == 'Admin')
		$sql_click_call = sprintf("SELECT count(*) as calls from call_log_info where log_time >= '%s'",$_SESSION['last_real_logout']);
	else
		$sql_click_call = sprintf("SELECT count(*) as calls from call_log_info where ((from_phone like '%s') or (to_phone like '%s')) and log_time >= '%s'",$phone,$phone,$_SESSION['last_real_logout']);
	$sql_click_call_resul = mysql_query($sql_click_call) or die(mysql_error());
	$sql_click_call_ary = mysql_fetch_assoc($sql_click_call_resul);
	$_SESSION['call'] = $sql_click_call_ary['calls'];
	
	// SMS
	if ($_SESSION['user_group'] == 'Admin')
		$sql_click_sms = sprintf("SELECT count(*) as sms from sms_log_info where log_time >= '%s'",$_SESSION['last_real_logout']);	
	else
		$sql_click_sms = sprintf("SELECT count(*) as sms from sms_log_info where ((from_phone like '%s') or (to_phone like '%s')) and log_time >= '%s'",$phone,$phone,$_SESSION['last_real_logout']);		
	$sql_click_sms_resul = mysql_query($sql_click_sms) or die(mysql_error());
	$sql_click_sms_ary = mysql_fetch_assoc($sql_click_sms_resul);
	$_SESSION['sms'] = $sql_click_sms_ary['sms'];
}else
{
	$sql_click_sms_ary['sms'] = $_SESSION['sms'];
	$sql_click_call_ary['calls'] = $_SESSION['call'];
}

// Email
if ($_SESSION['email'] == 0)
{
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
	mysql_query("set time_zone='-7:00';");
    $sql_cur_time="select sysdate() as cur_time;";  
	$res_cur_time=mysql_query($sql_cur_time) or die(mysql_error()."11");	
	$rec_cur_time = mysql_fetch_assoc($res_cur_time);
	$_SESSION['logout_email_get_time'] =$rec_cur_time['cur_time'];
	
	if ($_SESSION['user_group'] == 'Admin')
		$sql_click_email = sprintf("SELECT count(*) as email from mail_log_info where (send_dt >= '%s' )",$_SESSION['last_real_logout']);		
	else
		$sql_click_email = sprintf("SELECT count(*) as email from mail_log_info where ((from_address like '%s') or (mail_rcvr like '%s')) and (send_dt >= '%s')",$_SESSION['google_acc_nm'],$_SESSION['google_acc_nm'],$_SESSION['last_real_logout']);			
		
	$sql_click_email_resul = mysql_query($sql_click_email) or die(mysql_error());
	$sql_click_email_ary = mysql_fetch_assoc($sql_click_email_resul);
	$_SESSION['email'] = $sql_click_email_ary['email'];
}else
{
	$sql_click_email_ary['email'] = $_SESSION['email'];
}




if ($_SESSION['user_group'] == 'Manager')
{
	$sql_sel_agents = sprintf("select user_id from admin_user where owner='%s'",$_SESSION['user_login']);
	$sql_res_agents = mysql_query($sql_sel_agents) or die(mysql_error());
	$filterAgent = " and ( agent='" . $_SESSION['user_login'] . "'";
	while($sql_rec_agents = mysql_fetch_assoc($sql_res_agents))
	{
		if ($sql_rec_agents['user_id']!="")	
			$filterAgent .= " or agent='".$sql_rec_agents['user_id']."'";
	}
	$filterAgent .= ")";
}    
    
$sql_sum = "SELECT SUM(CASE WHEN priority_opt = 'New' THEN 1 ELSE 0 END) As newLeads,SUM(CASE WHEN priority_opt = 'Retry' THEN 1 ELSE 0 END) As retryLeads,SUM(CASE WHEN (apply_dt = now() ) THEN 1 ELSE 0 END) As today_task, SUM(CASE WHEN (priority_opt = 'Hot' ) THEN 1 ELSE 0 END) As hotLeads,SUM(CASE WHEN priority_opt = 'Warm' THEN 1 ELSE 0 END) As warmLeads,SUM(CASE WHEN priority_opt = 'Credit Check' THEN 1 ELSE 0 END) As credit_checks,SUM(CASE WHEN priority_opt = 'Credit Repair' THEN 1 ELSE 0 END) As credit_repairs,SUM(CASE WHEN priority_opt = 'Credit Ready' THEN 1 ELSE 0 END) As credit_ready,SUM(CASE WHEN priority_opt = 'Doc. Sent' THEN 1 ELSE 0 END) As doc_sents,SUM(CASE WHEN priority_opt = 'Funded' THEN 1 ELSE 0 END) As fundedLeads, SUM(CASE WHEN priority_opt = 'Pending Funding' THEN 1 ELSE 0 END) As pending_fundings, SUM(CASE WHEN priority_opt = 'Fee Pending' THEN 1 ELSE 0 END) As fee_pendings, SUM(CASE WHEN priority_opt = 'Pre-approved' THEN 1 ELSE 0 END) As pre_approveds,SUM(CASE WHEN priority_opt = 'Funded' THEN 1 ELSE 0 END) As fundedLeads, SUM(CASE WHEN priority_opt = 'Clients' THEN 1 ELSE 0 END) As clients,SUM(CASE WHEN priority_opt = 'Fee Pending' THEN 1 ELSE 0 END) As fee_pending,SUM(CASE WHEN priority_opt = 'Clickthroughs' THEN 1 ELSE 0 END) As clickthroughs,SUM(CASE WHEN priority_opt = 'Opened Emails' THEN 1 ELSE 0 END) As opened_emails,SUM(CASE WHEN is_opportunity_yes = 1 THEN 1 ELSE 0 END) As other_opportunity from customer_info WHERE 1 $filterAgent ";
$result_sum = mysql_query($sql_sum) or die(mysql_error());
$sum_array = mysql_fetch_assoc($result_sum);

/* opened emails */
$filterAgent='';
if ($_SESSION['user_group'] == 'Manager')
{
	$sql_sel_agents = sprintf("select user_id from admin_user where owner='%s'",$_SESSION['user_login']);
	$sql_res_agents = mysql_query($sql_sel_agents) or die(mysql_error());
	$filterAgent = " ( from_nm='" . $_SESSION['user_login'] . "'";
	while($sql_rec_agents = mysql_fetch_assoc($sql_res_agents))
	{
		if ($sql_rec_agents['user_id']!="")	
			$filterAgent .= " or from_nm='".$sql_rec_agents['user_id']."'";
	}
	$filterAgent .= ")";
}   
$sql_sum = "select count(*) as opened_emails from  mail_log_info where is_opened=1 $filterAgent";
$result_sum = mysql_query($sql_sum) or die(mysql_error());
$opened_email_array = mysql_fetch_assoc($result_sum);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Sales Lead DB</title>
        <!-- utf8 setting -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    
	    <!-- Bootstrap -->
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js" ></script>
 		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" ></script>
		
	
		<!-- jQuery (necessary for Right Navigation Bar) -->
  		<!-- <script src="js/jquery.js" type="text/javascript" async defer></script>  		-->
   		
        <style type="text/css">
        	/*************** New CSS - Kelvin Smith ***************************/
			.badge {
			  display: inline-block;
			  min-width: 10px;
			  padding: 1px 3px !important;
			  font-size: 12px;
			  font-weight: bold;
			  line-height: 1;
			  color: #fff;
			  text-align: center;
			  white-space: nowrap;
			  vertical-align: middle;
			  background-color: white !important; /* Kelvin */
			  border-radius: 10px;
			}

			/*----------------Pagination ----------------------------------*/
			div.pagination
			{
				padding: 3px;
			    margin: 3px;
			}

			div.pagination a
			{
			    padding: 2px 5px 2px 5px;
			    margin: 2px;
			    border: 1px solid #AAAADD;

			    text-decoration: none; /* no underline */
			    color: #000099;
			 }
			 div.pagination a:hover, div.pagination a:active
			  {
			    border: 1px solid #000099;
			    color: #000;
			}
			div.pagination span.current 
			{
				padding: 2px 5px 2px 5px;
				margin: 2px;
				border: 1px solid #970A00;
				font-weight: bold;
				background-color: #970A00;
				color: #FFF;
			}
			div.pagination span.disabled
			{
				padding: 2px 5px 2px 5px;
				margin: 2px;
				border: 1px solid #970A00;
				color: #970A00;
			}
						
			/* Login Page */

			  .main {
			    max-width: 520px;
			    margin: 0 auto;
				padding-top : 50px;
			  }
			  .login-or {
			    position: relative;
			    font-size: 18px;
			    color: #aaa;
			    margin-top: 10px;
			            margin-bottom: 10px;
			    padding-top: 10px;
			    padding-bottom: 10px;
			  }
			  .span-or {
			    display: block;
			    position: absolute;
			    left: 50%;
			    top: -2px;
			    margin-left: -25px;
			    background-color: #fff;
			    width: 50px;
			    text-align: center;
			  }
			  .hr-or {
			    background-color: #cdcdcd;
			    height: 1px;
			    margin-top: 0px !important;
			    margin-bottom: 0px !important;
			  }
			  h3 {
			    text-align: center;
			    line-height: 300%;
			  }

			.form-control-with-text {
			    margin-bottom: 5px;
			    display: inline-block;
			    width: 40%;
			    height: 34px;
			    padding: 6px 12px;
			    font-size: 14px;
			    line-height: 1.42857143;
			    color: #555;
			    background-color: #c7c8ed;
			    /* background-image: none; */
			    /* border: 1px solid #ccc; */
			    border-radius: 4px;
			    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
			    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
			    -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
			    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			    /* transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s; */
			}

			/* Last Login, Duration, Calls Made/Connected, ... , More Information */
			@media (max-width: 767px) {
			  .head-box{
			    color: #fff;
			    width:100px;
				height:52px;   
			    float:left;
			    margin: 10px 5px;
			    font-weight:bold;
			    font-size:12px;
			    font-family: sans-serif;
			    padding: 13px 0 13px 0 ;
			    border: 1px solid transparent;
			    border-radius: 6px;
			    border-bottom: 1px solid transparent;    
			    box-shadow: 0 1px 1px rgba(0,0,0,.05);
			  }
			}
			@media (min-width: 767px) {
			 .head-box{
			    color: #fff;
			    width:12%;
				height:52px;   
			    float:left;
			    margin: 10px 5px;
			    font-weight:bold;
			 	font-size:14px;
			    font-family: sans-serif;  
			    padding: 13px 0 13px 0 ;
			    border: 1px solid transparent;
			    border-radius: 6px;
			    border-bottom: 1px solid transparent;    
			    box-shadow: 0 1px 1px rgba(0,0,0,.05);
			  }
			 
			}

			/* dashboard icon (save, prev, next, print) */
			.dashbord{
				width:100%;
				float:left;
				margin-top:5px;
			}
			.dashbord-content{
				width:99%;
				float:left;
				border:solid 1px #248bc3;
				margin:1px;
			}
			.dashbord-icon-outer{
				width:100%;
				float:left;
				background:url(../buyer_details_img/dash-bg.jpg) repeat-x left top;
				padding:3px 0;
			}
			.dashbord-icon{
				width:16px;
				height:16px;
				float:left;
				margin:0 5px;
			}
			.dashbord-icon-text{
				float:left;
				margin:0 5px;
				font:bold 12px ;
				color:#000000;
				text-align:center;
				padding:3px 0 0 0;
			}

			/* Menu Separator */
			.divder-new {
			    height: 1px;
			    margin: 0px;
			    overflow: hidden;
			    background-color: #e5e5e5;
			}
			/* Menu */
			@media (min-width: 768px) {
			  .mobile-my-menu {
			    display: none !important;
			  }
			  
			  .mobile-info-box {			    
			    width:13% !important;
			  }
			  
			  .head-box-layer{
			  	width:80%;
			  }
			  .desktop-my-menu {
			    display:auto !important;
			  }
			}
			@media (max-width: 768px) {
			   .mobile-my-menu  {
			    display:auto !important;
			    font-size:18px !important;
			  }
			  
			  .head-box-layer{
			  	width:100%;
			  }
			  
			  .desktop-my-menu {
			    display: none!important;
			  }
			  
			  .mobile-info-box {
			    width:28% !important;
			  }
			}

			/* Main Content */
			@media (min-width: 768px) {
			  #my-main-content{
			  	width:100%;
			  	float:left;
			  }
			  #my-main-content-left{
			    width: 220px;
			    float: left;
			    border: solid 1px #248bc3;
			    margin : 32px 1px 1px 1px;
			  }
			   
			  .my-switch-vscroll{
			  	overflow-y: hidden !important;	
			  }
			  
			  #my-main-content-right{
			  	margin:0 0 0 235px;
			  	font-size:16px !important;
			  	font-weight:600 !important;
			  	color:black !important;
			  }
			  #my-main-content-right-content{
			  	width: 100%;
			    float: left;
			  }
			  .my-main-content-right-table{
			  	/*font-size:16px !important;*/
			  }
			}
			@media (max-width: 768px) {
			  #my-main-content{
			  	width:100%;  
			  }
			  #my-main-content-left{
			  }
			  .my-switch-vscroll{
			  	
			  }
			  #my-main-content-right{
			  	margin:0px;
			  	font-size:20px !important;
			  	font-weight:100 !important;
			  }
			  #my-main-content-right-content{
			  	width: 100%;
			    float: left;
			  }
			  .my-main-content-right-table{
			  /*	font-size:20px !important;
			  	font-weight:100;*/
			  }
			   
			}

			/* form control without padding and low height */
			.my-form-control {
			    padding:0px !important;
			    height:30px !important;
			    font-size:18px !important;
			    font-weight:300 !important;
			    color:black !important;
			}
			.my-form-control-left-text {
				margin-top:3px !important;
				padding-left:2px !important;
				padding-right:2px !important;
			    
			}
			
			/* text area without padding and low height */
			.my-textarea-control {
			    padding:0px !important;
			    font-size:18px !important;
			    font-weight:300 !important;
			    color:black !important;
			}
			
			/* table font with black */
			.my-table-font {
			   color:black !important;
			   font-weight:300 !important;
			}
			
			/* Scrollable Drop Menu */
			.my-scrollable-menu {
			    height: auto;
			    width:300px;
			    max-height: 350px;
			    overflow-x: hidden;
			    font-size:18px !important;
			}

			/* Seach Box */
			body {
			    padding-top: 50px;
			}
			.dropdown.dropdown-lg .dropdown-menu {
			    margin-top: -1px;
			    padding: 6px 20px;
			     font-size : 16px !important;
			}
			.input-group-btn .btn-group {
			    display: flex !important;
			}
			.btn-group .btn {
			    border-radius: 0;
			    margin-left: -1px;
			}
			.btn-group .btn:last-child {
			    border-top-right-radius: 4px;
			    border-bottom-right-radius: 4px;
			}
			.btn-group .form-horizontal .btn[type="submit"] {
			  border-top-left-radius: 4px;
			  border-bottom-left-radius: 4px;
			}
			.form-horizontal .form-group {
			    margin-left: 0;
			    margin-right: 0;
			}
			.form-group .form-control:last-child {
			    border-top-left-radius: 4px;
			    border-bottom-left-radius: 4px;
			}
			
			.my-form-group {
			    margin:0px !important;
			}
			@media screen and (min-width: 768px) {
			    #adv-search {
			        width: 300px;
			        margin: 0 auto;
			    }
			    .dropdown.dropdown-lg {
			        position: static !important;
			    }
			    .dropdown.dropdown-lg .dropdown-menu {
			        min-width: 300px;
			    }
			}
			@media screen and (max-width: 768px) {
			    #adv-search {
			        width: 300px;
			        margin: 0 auto;
			        font-size:18px !important;
			    }
			    .dropdown.dropdown-lg {
			        position: static !important;
			    }
			    .dropdown.dropdown-lg .dropdown-menu {
			        min-width: 300px;
			    }
			}
			/* Kelvin */
			@media (min-width: 1100px) {
			  .container {
			    width: 1070px;
			  }
			}
			
			/* SMS, EMAIL Button */
			.my-sms-button{
				height:30px;padding-top:4px;
			}
			/*************/
			
			/****************************************************************/
        </style>
        <script type="text/javascript">
	        //$(".loading").removeClass("loading");
	        setInterval(getEmailStatus, 1000*60*2);
			function getEmailStatus()
       		{
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "emailtrack.php",
					success: function(res)
					{
						console.log(res);
						if ((res.status=="opened") && (res.opened_res != ""))
						{
							var str="";
							var str_ary = res.opened_res.split(';');
							console.log(str_ary.length);
							for (i=0;i<str_ary.length-1;i++)
							{
								if (str_ary[i]!="")
									str += "<div class='alert alert-success'><strong>Success!</strong> "+str_ary[i]+" </div>"
							}
							document.getElementById("opened_emails").innerHTML = str;		
							$("#dialog_opened_emails").modal();		
							
							location.reload();						
						}else{
							/*var str="";
							str += "<div class='alert alert-warning'>There aren't any new opened emails.</div>"								
							document.getElementById("opened_emails").innerHTML = str;		
							$("#dialog_opened_emails").modal();*/
						}
						
					},
					error: function(res) {
						console.log("fail");
						console.log(res);
					}
				});			
			}
			
	        function submitForm()
	        {
	            $("#btnChangeSubmit").click();
	        }  
        	function wopen(url, name, w, h)
            {
                // Fudge factors for window decoration space.
                // In my tests these work well on all platforms & browsers.
                w += 120;
                h += 80;
                var win = window.open(url, name, 'width=' + w + ', height=' + h + ', location=no, menubar=no, status=no, toolbar=no, scrollbars=no, resizable=no, left=300');
            }
            function wopen2(url, name, w, h)
            {
                // Fudge factors for window decoration space.
                // In my tests these work well on all platforms & browsers.
                w += 120;
                h += 80;
                var win = window.open(url, name, 'width=' + w + ', height=' + h + ', location=no, menubar=no, status=no, toolbar=no, scrollbars=no, resizable=no, left=300');
            }
            function setFilterOnLeads(field, value,real_value)
            {
            	/* Set current session statistics value with value */
            	console.log("field :"+field);
            	console.log("value : "+real_value);
            	var data = {"stat_name":value,
            			"stat_val":real_value};
            	console.log("setFilterOnLeads");
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "setstatisticsvalue.php",	
					data : data,					
					success : function(res)
					{	
						/*if (res.status == "Success")
						{
						}*/
					},
					error:function(res)
					{
						console.log("fail" + res);
												
					}
				});					
				   
               if (value=="Opened Emails")
				{
					console.log("Opened Emails");
					window.open("showOpened_emails.php");						
				}else
				{
					$("#field_nm").val(field);
                	$("#val").val(value);
                	$("#btnSearch").click();	
				}
            }

            function setFilterOnLeadsOverdue(field, value,real_value)
            {
            	/* Set current session statistics value with value */
            	console.log("field :"+field);
            	console.log("value : "+real_value);
            	var data = {"stat_name":field,
            			"stat_val":real_value};
            	console.log("setFilterOnLeadsOverdue");
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "setstatisticsvalue.php",	
					data : data,					
					success : function(res)
					{	
						/*if (res.status == "Success")
						{
						}*/
					},
					error:function(res)
					{
						console.log("fail" + res);
												
					}
				});				
				
				if (field == 'call')
				{
					console.log('field-call');
					window.open("getHistory_all_calls.php");	
				}else if (field == 'email')
				{
					console.log('field-email');
					window.open("getHistory_all_emails.php");	
				}else if (field == 'sms')
				{
					console.log('field-sms');
					window.open("getHistory_all_sms.php");	
				}
                $("#field_nm").val('');
                $("#val").val('');
                $("#" + field).val(value);
                $("#btnSearch").click();
            }
            
            function ChangeSearchValue()
			{
				document.getElementById('val').value = document.getElementById('search_value').value;
				console.log(document.getElementById('val').value);
				$('#btnSearch').click();
			}
			
        	
        	var click_email_dialog,click_sms_dialog;
        		   
			
            $(document).ready(function () {
             	
				setInterval(startTime_Dur, 1000);
				setInterval(startTime_News,1000*60*5);
				
				//setInterval(startTime_Postidea, 1000*60*2);      
				//startTime_News();
				//startTime_Postidea();
               
 				/*================================ Voice call and sms =========================================*/
            
               	function checkRegexp( o, regexp, n ) {
			      if ( !( regexp.test( o.val() ) ) ) {
			        o.addClass( "ui-state-error" );
		
			        return false;
			      } else {
			        return true;
			      }
			   }
			   
			
				
			    $("#send_sms_btn").on("click", function() {	
			   		 //document.getElementById("msg_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
			    	 $("#dialog_sms").modal();							
				});
				
								
				$("#send_sms_btn_mobile").on("click", function() {	
				  //document.getElementById("msg_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
				  $("#dialog_sms").modal();							
				});
				
				
				
				/*================================ Email =========================================*/
						   
				$("#send_eml_btn").on("click", function() {			
					console.log("send email button");
					//document.getElementById("email_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
					
					$("#dialog-email").modal();		
				});
				
				$("#send_eml_btn_mobile").on("click", function() {					
					//document.getElementById("email_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
					$("#dialog-email").modal();
				});
				
			
				function startTime_News() {
				   console.log("get Notification");
				   $.ajax({
						type:"POST",
						dataType : "json",
						url : "getNotification.php",						
						success : function(res){
							console.log(res);
							//alert(res.status);
							if (res.status == "Success")
							{
							  /* sms news */
							   $sms_news_cnt = parseInt(res.sms_news);
							   console.log($sms_news_cnt);
							   if ($sms_news_cnt>=1)
							   {
							   	  document.getElementById("sms_news_span").innerHTML = $sms_news_cnt;
							   	  document.getElementById("sms_news_span_mobile").innerHTML = $sms_news_cnt;
							   	  $("#sms_news_span").css('visibility', 'visible');
							   	  $("#sms_news_span_mobile").css('visibility', 'visible');
							   	
							   }else
							   {
							   		$("#sms_news_span").css('visibility', 'hidden');
									$("#sms_news_span_mobile").css('visibility', 'hidden');
							   }			
							   
							  /* email news */		
							   $email_news_cnt = parseInt(res.email_news);
							   console.log($email_news_cnt);
							   if ($email_news_cnt>=1)
							   {
									document.getElementById("email_new_span").innerHTML = $email_news_cnt;
							   	   	document.getElementById("email_new_span_mobile").innerHTML = $email_news_cnt;
							   	  	$("#email_new_span").css('visibility', 'visible');
							   	 	$("#email_new_span_mobile").css('visibility', 'visible');
							   	
							   }else
							   {
							   		$("#email_new_span").css('visibility', 'hidden');
							   		$("#email_new_span_mobile").css('visibility', 'hidden');
							   }			
														
								document.getElementById("calls_div").innerHTML = res.calls_made+"/"+res.calls_con;
								document.getElementById("conv_div").innerHTML = res.conv_minutes;
								document.getElementById("ratio_div").innerHTML = res.ratio+"%";							
								document.getElementById("emls_div").innerHTML = res.email_sent+"/"+res.email_recv;						
							   	document.getElementById("sms_div").innerHTML = res.sms_sent+"/"+res.sms_recv;					   			   
							}
								
						},
						error:function(res)
						{
							console.log(res);
							clearInterval();							
						}
				   });
				   
				   $.ajax({
						type:"POST",
						dataType : "json",
						url : "emailtrack.php",
						success: function(res)
						{
							console.log(res);
							if ((res.status=="opened") && (res.opened_res != ""))
							{
								var str="";
								var str_ary = res.opened_res.split(';');
								console.log(str_ary.length);
								for (i=0;i<str_ary.length-1;i++)
								{
									if (str_ary[i]!="")
										str += "<div class='alert alert-success'><strong>Success!</strong> "+str_ary[i]+" </div>"
								}
								document.getElementById("opened_emails").innerHTML = str;		
								$("#dialog_opened_emails").modal();			
								
								location.reload();					
							}else{
								/*var str="";
								str += "<div class='alert alert-warning'>There aren't any new opened emails.</div>"								
								document.getElementById("opened_emails").innerHTML = str;		
								$("#dialog_opened_emails").modal();*/
							}
							
						},
						error:function(res)
						{
							console.log("error");
							console.log(res);							
							clearInterval();							
						}
					});			
						
				}			
	
				/*function startTime_Postidea() {
				   console.log("startTime_Postidea");
				   $.ajax({
						type:"POST",
						dataType : "json",
						url : "getNotification_postidea.php",						
						success : function(res){
							console.log(res);
							//alert(res.status);
							if (res.status == "Success")
							{
								document.getElementById("post_remarks").value = res.post_ideas;
							}
								
						},
						error:function(res)
						{
							document.getElementById("post_remarks").value ='';
							console.log("fail" + res);
							clearInterval();							
						}
				   });		
				}		*/
	
				function startTime_Dur() {
					
				    var d = new Date()
				    var off_az = -7;
					var utc = d.getTime()+(d.getTimezoneOffset()*60000);
					var nd = new Date(utc+(3600000*off_az));
					var now_seconds = parseInt(nd.getTime() / 1000);
					
					var dateString = document.getElementById("cur_login_time").value;
					var cur_login= new Date(dateString.replace(/-/g, '/'));
					
					n = cur_login.getTimezoneOffset();
				    var cur_login_seconds = parseInt(cur_login.getTime()/1000);
				    var dur = now_seconds-cur_login_seconds;
				    dur_hr = parseInt(dur/3600);
				    if (dur_hr.toString().length==1)
						dur_hr="0"+dur_hr;
					
				    dur_min = parseInt((dur%3600)/60);
				    if (dur_min.toString().length==1)
						dur_min="0"+dur_min;
				  
				    dur_sec = dur%60;
				    if (dur_sec.toString().length==1)
						dur_sec="0"+dur_sec;
				    
				    // check agent event every 15 minutes 
				    dur_fiften_min = dur%900;
				    if ((dur_fiften_min==0) &&(dur !=0))
				    {
						console.log("every 15 minutes");
				    	if (document.getElementById("no_event").value==1)
				    	{
							window.location.href="adminlogout.php";	     	
						}else
							document.getElementById("no_event").value=1;
					}				   
				    document.getElementById("dur_div").innerHTML = dur_hr+":"+dur_min+":"+dur_sec;
				}			
				
				$("#more_info_btn").on("click", function() {		
					console.log("more information");
					window.open("editprofile.php");					
				});
				
            });
            
            /* Email Preview */
			function previewEmail()
       		{
       			console.log("previewEmail");
				var form = document.getElementById('SendEmailUpload');
				var formData = new FormData(form);
       			
			    $.ajax({
			        url: 'previewEmail.php',
			        data: formData,
			        dataType : "json",
			        processData: false,
  					contentType: false,
			        type: 'POST',
			        success: function ( res ) {
			        	console.log("Success");
			        	console.log(res);
			        	if (res.status == "Success")
			        	{
			        		if (res.email_body != "")
			        		{
			        			console.log(res.email_body);
								document.getElementById("email_preview_div").innerHTML =res.email_body;								
          						$('#dialog_preview_email').modal();            					
							}							
						}							
			        }
			    });				   
			}
			
            function sendEmail()
       		{
			   	var valid = true;	
			   	console.log("sendemail");
			 	var form = document.getElementById('SendEmailUpload');
				var formData = new FormData(form);
				var xhr = new XMLHttpRequest();
				// Add any event handlers here...
				xhr.open('POST', 'sendemail.php', true);
				xhr.send(formData);

			 	console.log(formData);
		    	$("#dialog-email").modal('hide');
		  		$('#dialog_preview_email').modal('hide');      
		  		setTimeout(getEmailStatus,30*1000);
				return valid;						
			}
			
		    function ClicktoEmail(addr)
            {
            	console.log('ClicktoEMAIL');
            	document.getElementById("email_to").value = addr;
            	var str = document.getElementById("emls_div").innerHTML;
            	console.log(str);
            	var str_ary = str.split("/");
            	var email_sent_cnt = parseInt(str_ary[0])+1;
            	var email_recv_cnt = parseInt(str_ary[1]);
            	//document.getElementById("email_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
            	$("#dialog-email").modal();
            	
				document.getElementById("emls_div").innerHTML = email_sent_cnt+"/"+email_recv_cnt;	
			}
						
			function sendSMS(){
			   	var valid = true;
			//	valid = valid && checkRegexp(sms_to, /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/, "Phone address only allow : 0-9" );
			
 				if (valid){
					var data = {
						"sms_to":document.getElementById('phone_numbers').value,
						"sms_body":document.getElementById('msg_body').value						
					};
					console.log(data);
					
					//alert(data);
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "sendsms.php",
						data : data,
						success : function(res){
							console.log(res);
							document.getElementById("sms_div").innerHTML = res.sms_sent+"/"+res.sms_recv;	
							if (res.status == 'Success')
								alert("Send SMS Success!");								
							else if (res.status == 'Error')
								alert("Send SMS Error");
							
						},
						error:function(res)
						{
							console.log("fail" + res);
						}
					});
					$("#dialog_sms").modal('hide');					
					$('#dialog_preview_sms').modal('hide');      		
				}
				return valid;					
		   }
		    /* SMS Preview */
			function previewSMS()
       		{
       			console.log("PreviewSMS");      			
			
				var data = {
					"sms_to":document.getElementById('phone_numbers').value,
					"sms_sal":document.getElementById('msg_sal').value,
					"sms_body":document.getElementById('msg_body').value						
				};
				console.log(data);
       			
			    $.ajax({
			        url: 'previewSMS.php',
			        data: data,
			        type:"POST",
					dataType : "json",
			        success: function ( res ) {
			        	console.log("Success");
			        	console.log(res);
			        	if (res.status == "Success")
			        	{
			        		if (res.sms_body != "")
			        		{
			        			console.log(res.sms_body);
								document.getElementById("sms_preview_div").innerHTML =res.sms_body;								
          						$('#dialog_preview_sms').modal();            					
							}							
						}							
			        }
			    });				   
			}
			 
		          		 
			function ClicktoSMS(addr)
            {
            	console.log("ClicktoSMS");
            	
        		document.getElementById("phone_numbers").value = addr;
        		
        		var str = document.getElementById("sms_div").innerHTML;
            	console.log(str);
            	var str_ary = str.split("/");
            	var sms_sent_cnt = parseInt(str_ary[0])+1;
            	var sms_recv_cnt = parseInt(str_ary[1]);
            	
            	$("#dialog_phone").modal('hide');
            	$("#dialog_sms").modal();
				
				//document.getElementById("msg_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
				document.getElementById("sms_div").innerHTML = sms_sent_cnt+"/"+sms_recv_cnt;			
			}

				
			function ClicktoCall(addr)
	        {
	        	console.log("ClicktoCall : " + addr);
	        	if (addr=="")
	        	{
					alert("Phone number is wrong!");
				}	        		
	        	else
	        	{
	        		document.getElementById("connecting_number").innerHTML = addr;
					$("#dialog_connecting").modal();   	
					
					$("#dialog_phone").modal('hide');   	
	        	
		            var data = {"call_to":addr};
					console.log(data);
					
					//alert(data);
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "clicktocall.php",
						data : data,
						success : function(res){						
							console.log(res);
							if (res.status == 'Success')
							{
								setTimeout(function(){$("#dialog_connecting").modal('hide');},5*1000);
								
								var str = document.getElementById("calls_div").innerHTML;
				            	console.log(str);
				            	var str_ary = str.split("/");
				            	var calls_made = parseInt(str_ary[0])+1;
				            	var calls_connected = parseInt(str_ary[1]);
            					document.getElementById("calls_div").innerHTML = calls_made+"/"+calls_connected;		
            					
								//alert("Phone call is calls_div");
							}							
							else if (res.status == 'Error')
								alert("Phone call is failed");
							
						},
						error:function(res)
						{
							console.log("fail");
							console.log(res);
							
						}
					});			
				}
				
	        }      

			function ClicktoPhone(ph_num)
			{
				console.log("ClicktoPhone");
				
        		document.getElementById("dialog_phone_number").innerHTML = ph_num;
        		$("#dialog_phone").modal();
			}
	        /**
			* Get history of Emails, Call,SMS
			*/
			function GetEmailHistory(addr)
			{
				console.log("getHistory_email");
				window.open("getHistory_email.php");				
			}
			function GetCallHistory(addr)
			{
				console.log("GetCallHistory");
				window.open("getHistory_call.php");				
			}			
			function GetSMSHistory(addr)
			{
				console.log("getHistory_sms");
				window.open("getHistory_sms.php");		
			}
			
						     
			/**
			* Post idea and show all posted ideas.
			*/
			function postIdea(){
				console.log("postIdea");
				var data = {"post_content":document.getElementById('idea_content').value};
				
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "postidea.php",
					data : data,
					success : function(res){
						console.log("succ");
						console.log(res);
					},
					error:function(res)
					{
						console.log("fail" + res);
					}
				});
				$("#dialog_post_idea").modal('hide');
				
		    }
		   
			function ClickPostIdea(){
				console.log("ClickPostIdea");
        		$("#dialog_post_idea").modal();				
			}

			/**
			* Show all posted ideas.
			*/
			function ClickShowIdeas()
			{
				 window.open('showPosted_ideas.php');
			}
			
			
			/**
			* Get Agent Event
			*/
			function MyMouseDown()
			{
				
				document.getElementById("no_event").value = 0;
			}
			
			/**
			* whenever agent is changed, the manager should be changed 
			*/
			function ChangeAgent()
			{
				console.log("change agent");
				ind = document.getElementById('user_id').selectedIndex ;
				document.getElementById('manager_id').value = document.getElementById('manager_hidden_id').options[ind].value;
				
			}
			
        </script>   
    </head>
    <body onload onmousedown="MyMouseDown()">
        <script type="text/javascript" src="popcalendar.js"></script>
        <div>
	    	<input type="hidden" name="no_event" value="<?php echo $_SESSION['no_event']; ?>" id="no_event" />
    	</div>
    	
    	<!-- desktop menu -->
		<nav class="navbar  navbar-default navbar-fixed-top desktop-my-menu">
			<div class="container-fluid">
				<div class="navbar-header">
				      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar_desktop">
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span> 					        
				      </button>
				      <a class="navbar-brand" href="#">DirectFunder</a>
				</div>
				<div class="collapse navbar-collapse" id="myNavbar_desktop">
					<ul class="nav navbar-nav">
				      	<li class="active"><a href="searchbuyer.php?rst=1"><span class="glyphicon glyphicon-home"></span>&nbsp;Home</a></li>
				       
		                <li><a href="searchbuyer.php"><span class="glyphicon glyphicon-search"></span>&nbsp;Search</a></li>
		             	<li><a href="buyerinfo2.php?action=add"><span class="glyphicon glyphicon-plus"></span>&nbsp;Add</a></li>
					<?php
					if ($_SESSION['user_group'] == "Manager") 
					{
					?>
						<li><a href="manager_report.php"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Report</a></li>	
					<?php
					}else if ($_SESSION['user_group'] == "Admin") 
					{
					?>
						<li><a href="admin_report.php"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Report</a></li>	
					<?php
					}
					?>		                
		                <li class="dropdown">
		               		<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-cog" ></span>&nbsp;Setting<span class="caret"></span></a>
		                	<ul class="dropdown-menu">       
		                		<li style="background-color:white"><a href="editprofile.php"  style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-picture"></span>&nbsp;Profile Information</a></li>
		                		<li class="divder-new"></li>   
		                		<li style="background-color:white"><a href="passchange.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Change Password</a></li>
		                		<?php
								if ($_SESSION['user_group'] == "Manager") {
								?>
					               	<li class="divder-new"></li>
					                <li style="background-color:white"><a href="addagent.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-user"></span>&nbsp;Add Sales Agent</a></li>				                
					             <?php
					             }
					             ?>
				             	<?php
								if ($_SESSION['user_group'] == "Admin") {
								?>
									<li class="divder-new"></li>
					                <li style="background-color:white"><a href="addagent.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-user"></span>&nbsp;Add Sales Agent</a></li>				                
					          		<li class="divder-new"></li>
					             	<li style="background-color:white"> <a href="addlease.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;Add Lease Login</a></li>
					             	<li class="divder-new"></li>
					             	<li style="background-color:white"><a href="changeaccess.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Change Login Credential</a></li>			        
						            <li class="divder-new"></li>
						            <li style="background-color:white"><a href="xls_gen_info.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-download"></span>&nbsp;Export To Excel</a></li>		        
					             <?php
					             }
					             ?>
					             <li class="divder-new"></li>
						         <li style="background-color:white"><a href="importfromcsv.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-upload"></span>&nbsp;Import From Excel(*.csv)</a></li>	                    
		                    </ul>
		                </li>						
					</ul>
					<ul class="nav navbar-nav navbar-right" style="margin-top:0px;">				
					   	<!--li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
		  				<li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li-->
		  				<li><a href="help.php"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;Help</a></li>	                		
					  	<li><a href="#"><span class="glyphicon glyphicon-log-in"></span>&nbsp;Logged in as : <?php echo $_SESSION['user_login'];?></a></li>	        
				        <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a></li>
				    </ul>			      	                
			    </div>
			</div>
		</nav>	
			
		<!-- mobile Icon menu -->
		<nav class="navbar navbar-inverse navbar-fixed-top mobile-my-menu">
			<div class="container-fluid" style="padding:10px">
				<div class="row" id="bottomNav">
			    	<div class="col-xs-2 text-center">
						<a class="dropdown-toggle" data-toggle="dropdown" style="color:white" href="#"><span class="glyphicon glyphicon-align-justify" ></span><br>Menu</a>
				    	<ul class="dropdown-menu my-scrollable-menu">    
					      	<!-- Workspace : calls, emails, sms, task, past due, delinuent -->
					      	<li class="active"><a href="#">WORKSPACE<span class="sr-only">(current)</span></a></li>
					        
			                <li >
				                	<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Call:
									<?php
										$val_color = '#337ab7';
										$span_val = '';
										if ($_SESSION['call'] > $sql_click_call_ary['calls'])
										{
											$val_color = 'red';
											$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
										}else if ($_SESSION['call'] < $sql_click_call_ary['calls'])
										{
											$val_color = 'green';
											$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
										}
										//$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
									?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('call', '1','<?php echo $sql_click_call_ary['calls'];?>');return false;">
			                                <?= $sql_click_call_ary['calls'].' '.$span_val; ?></a>                            
			                        </p>
			                    </li>
							<li>
				                	<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Email:
									<?php
										$val_color = '#337ab7';
										$span_val = '';
										if ($_SESSION['email'] > $sql_click_email_ary['email'])
										{
											$val_color = 'red';
											$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
										}else if ($_SESSION['email'] < $sql_click_email_ary['email'])
										{
											$val_color = 'green';
											$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
										}
										//$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
									?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('email', '1','<?php echo $sql_click_email_ary['email'];?>');return false;">
			                                <?= $sql_click_email_ary['email'].' '.$span_val; ?></a>                            
			                        </p>
								</li>
							<li>
				                	<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">SMS:
									<?php
										$val_color = '#337ab7';
										$span_val = '';
										if ($_SESSION['sms'] > $sql_click_sms_ary['sms'])
										{
											$val_color = 'red';
											$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
										}else if ($_SESSION['sms'] < $sql_click_sms_ary['sms'])
										{
											$val_color = 'green';
											$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
										}
										//$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
									?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('sms', '1','<?php echo $sql_click_sms_ary['sms'];?>');return false;">
			                                <?= $sql_click_sms_ary['sms'].' '.$span_val; ?></a>                            
			                        </p>
							    </li>                    
					        <li >
				                   		<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Today's Task:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['followUpCount'] > $followup_count_array['followUpCount'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['followUpCount'] < $followup_count_array['followUpCount'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_followUpCount'] = $followup_count_array['followUpCount'];
											
										?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnTodaysFolloups', '1','<?php echo $followup_count_array['followUpCount'];?>');return false;">
				                                <?= $followup_count_array['followUpCount'].' '.$span_val; ?></a>                            
				                        </p>
					                </li>
		                    <li >   
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Past Due:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['past_due'] > $sevenoverdue_count_array['overdueCount'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";

											}else if ($_SESSION['past_due'] < $sevenoverdue_count_array['overdueCount'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_past_due'] = $sevenoverdue_count_array['overdueCount'];
											
										?>                                            
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnSevenDayOverdue', '1','<?php echo $sevenoverdue_count_array['overdueCount'];?>');
				                                            return false;">
				                                <?= $sevenoverdue_count_array['overdueCount'].' '.$span_val; ?>
				                            </a>                                            
				                        </p>
				                    </li>
							<li >
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Delinquent:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['delinquent'] > $thirty_count_array['overdueCount'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['delinquent'] < $thirty_count_array['overdueCount'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_delinquent'] = $thirty_count_array['overdueCount'];
											
										?>

				                                <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnThirtyDayOverdue', '1','<?php echo $thirty_count_array['overdueCount'];?>');
				                                            return false;">
				                                   <?= $thirty_count_array['overdueCount'].' '.$span_val; ?>
				                                </a>

				                        </p>
				                    </li>
	                		<li >
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">No FollowUp Date:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['no_follow_up_date'] > $no_follow_up_date_array['no_follow_up_date_count'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['no_follow_up_date'] < $no_follow_up_date_array['no_follow_up_date_count'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											
											
										?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('no_follow_up_date', '1','<?php echo $no_follow_up_date_array['no_follow_up_date_count'];?>');
				                                            return false;">
				                          	<?= $no_follow_up_date_array['no_follow_up_date_count'].' '.$span_val; ?>
				                          </a>
				                        </p>	
							        </li>        
					        
					        <!-- Opportunity : hot, credit ready,Pre-approved, Other opportunity -->
					        <li class="active"><a href="#">OPPORTUNITY<span class="sr-only">(current)</span></a></li>
					        <li >   
		                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Hot:
		                        <?php
									$val_color = '#337ab7';
									$span_val = '';
									if ($_SESSION['hotLeads'] > $sum_array['hotLeads'])
									{
										$val_color = 'red';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
									}else if ($_SESSION['hotLeads'] < $sum_array['hotLeads'])
									{
										$val_color = 'green';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
									}	
									$_SESSION['tmp_hotLeads'] = $sum_array['hotLeads'];										
								?>
		                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Hot','<?php echo $sum_array['hotLeads'];?>');
		                                        return false;">
		                               <?= $sum_array['hotLeads'].' '.$span_val; ?>
		                            </a>
		                        </p>
				            </li>
				            <li >
		                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Credit Ready:
                                <?php
									$val_color = '#337ab7';
									$span_val = '';
									if ($_SESSION['credit_ready'] > $sum_array['credit_ready'])
									{
										$val_color = 'red';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
									}else if ($_SESSION['credit_ready'] < $sum_array['credit_ready'])
									{
										$val_color = 'green';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
									}
									$_SESSION['tmp_credit_ready'] = $sum_array['credit_ready'];
								?>
                                    <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Credit Ready','<?php echo $sum_array['credit_ready'];?>');
                                                return false;">
                                       <?= $sum_array['credit_ready'].' '.$span_val; ?>
                                    </a>
                                </p>
					        </li>
					        <li>  
		                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Pre-approved:
		                        <?php
									$val_color = '#337ab7';
									$span_val = '';
									if ($_SESSION['pre_approveds'] > $sum_array['pre_approveds'])
									{
										$val_color = 'red';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
									}else if ($_SESSION['pre_approveds'] < $sum_array['pre_approveds'])
									{
										$val_color = 'green';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
									}	
									$_SESSION['tmp_pre_approveds'] = $sum_array['pre_approveds'];										
								?>
		                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Pre-approved','<?php echo $sum_array['pre_approveds'];?>');
		                                        return false;">
		                               <?= $sum_array['pre_approveds'].' '.$span_val; ?>
		                            </a>
		                        </p>
		                    </li>
	                        <li>
		                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Other Opportunity:
		                        <?php
									$val_color = '#337ab7';
									$span_val = '';
									if ($_SESSION['other_opportunity'] > $sum_array['other_opportunity'])
									{
										$val_color = 'red';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
									}else if ($_SESSION['other_opportunity'] < $sum_array['other_opportunity'])
									{
										$val_color = 'green';
										$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
									}
									$_SESSION['tmp_other_opportunity'] = $sum_array['other_opportunity'];
								?>
		                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('is_opportunity', '1','<?php echo $sum_array['other_opportunity'];?>');
		                                        return false;">
		                               <?= $sum_array['other_opportunity'].' '.$span_val; ?>
		                            </a>
		                        </p>                                                               
					    	</li>                
					        
					        
					        <!-- Sales : new leads, opened emails, clickthroughs, retry, hot, warm, credit check, credit repair, credit ready -->
					      	<li class="active"><a href="#">SALES<span class="sr-only">(current)</span></a></li>
					      	
					      	<li>
					             		<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">New Leads : <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['newLeads'] > $sum_array['newLeads'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['newLeads'] < $sum_array['newLeads'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
										?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'New','<?php echo $sum_array['newLeads'];?>'); return false;">
												<?= $sum_array['newLeads'].' '.$span_val;?></a>
										</p>
									</li>
	                		<li>
					                 	<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Opened Emails
										<?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['opened_emails'] > $opened_email_array['opened_emails'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['opened_emails'] < $opened_email_array['opened_emails'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_opened_emails'] = $opened_email_array['opened_emails'];
											
										?>
					                        <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Opened Emails','<?php echo $opened_email_array['opened_emails'];?>');
					                                    return false;">
					                           <?= $opened_email_array['opened_emails'].' '.$span_val; ?>
					                        </a>                                                
					                    </p>
				                    </li>
	                    	<li>
					                    <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Clickthroughs:
										<?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['clickthroughs'] > $sum_array['clickthroughs'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['clickthroughs'] < $sum_array['clickthroughs'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_clickthroughs'] = $sum_array['clickthroughs'];
											
										?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Clickthroughs','<?php echo $sum_array['clickthroughs'];?>');
					                                        return false;">
					                               <?= $sum_array['clickthroughs'].' '.$span_val; ?>
					                            </a>
					                    </p>
				                    </li>
	                    	<li>
										<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Retry :
										<?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['retryLeads'] > $sum_array['retryLeads'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['retryLeads'] < $sum_array['retryLeads'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_retryLeads'] = $sum_array['retryLeads'];
											
										?>
					                        <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Retry','<?php echo $sum_array['retryLeads'];?>');
					                                    return false;">
					                            <?= $sum_array['retryLeads'].' '.$span_val; ?>
					                        </a>
					                    </p>	                    
				                    </li> 	               			
	                    	<li >    
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px"> Warm:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['warmLeads'] > $sum_array['warmLeads'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['warmLeads'] < $sum_array['warmLeads'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_warmLeads'] = $sum_array['warmLeads'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Warm','<?php echo $sum_array['warmLeads'];?>');
				                                        return false;">
				                               <?= $sum_array['warmLeads'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
							<li >
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Credit Check:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['credit_checks'] > $sum_array['credit_checks'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['credit_checks'] < $sum_array['credit_checks'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_credit_checks'] = $sum_array['credit_checks'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Credit Check','<?php echo $sum_array['credit_checks'];?>');
				                                        return false;">
				                               <?= $sum_array['credit_checks'].' '.$span_val; ?>
				                            </a>                                                 
				                        </p>
				                    </li>
							<li >
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Credit Repair:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['credit_repairs'] > $sum_array['credit_repairs'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['credit_repairs'] < $sum_array['credit_repairs'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_credit_repairs'] = $sum_array['credit_repairs'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Credit Repair','<?php echo $sum_array['credit_repairs'];?>');
				                                        return false;">
				                               <?= $sum_array['credit_repairs'].' '.$span_val; ?>
				                            </a>                                             
				                        </p>
				                    </li>
	                     
					      
					      	
					      	<!-- Statistics : Pre-approved, Doc.Sent, Pending Funding, Funded, Fee Pending, 30 day funding, 60 day funding, 90 day funding, Clients, Other opportunity -->
					      	<li class="active"><a href="#">STATISTIC<span class="sr-only">(current)</span></a></li>				      	
				      		
	                    	<li >  
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Doc. Sent:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['doc_sents'] > $sum_array['doc_sents'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['doc_sents'] < $sum_array['doc_sents'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_doc_sents'] = $sum_array['doc_sents'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Doc. Sent','<?php echo $sum_array['doc_sents'];?>');
				                                        return false;">
				                               <?= $sum_array['doc_sents'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
	                   		<li >  
				                      	<p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Pending Funding :
				                      	<?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['pending_fundings'] > $sum_array['pending_fundings'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['pending_fundings'] < $sum_array['pending_fundings'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}	
											$_SESSION['tmp_pending_fundings'] = $sum_array['pending_fundings'];										
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Pending Funding','<?php echo $sum_array['pending_fundings'];?>');
				                                        return false;">
				                               <?= $sum_array['pending_fundings'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
	                    	<li >  
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Funded :
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['fundedLeads'] > $sum_array['fundedLeads'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['fundedLeads'] < $sum_array['fundedLeads'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}	
											$_SESSION['tmp_fundedLeads'] = $sum_array['fundedLeads'];										
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Funded','<?php echo $sum_array['fundedLeads'];?>');
				                                        return false;">
				                               <?= $sum_array['fundedLeads'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
	                    	<li >  
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Fee Pending :
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['fee_pending'] > $sum_array['fee_pending'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['fee_pending'] < $sum_array['fee_pending'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_fee_pending'] = $sum_array['fee_pending'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Fee Pending','<?php echo $sum_array['fee_pending'];?>');
				                                        return false;">
				                               <?= $sum_array['fee_pending'].' '.$span_val; ?>
				                            </a>                                                  
				                        </p>
				                    </li>
	                    	<li >  
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">30 day funding :
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['thirty_day_funding'] > $buying_time_thirty_count_array['dueCount'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['thirty_day_funding'] < $buying_time_thirty_count_array['dueCount'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_thirty_day_funding'] = $buying_time_thirty_count_array['dueCount'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeThirty', '1','<?php echo $buying_time_thirty_count_array['dueCount'];?>');
				                                            return false;">
				                            	<?= $buying_time_thirty_count_array['dueCount'].' '.$span_val; ?>
				                            </a>
				                     
				                        </p>
				                    </li>
	                    	<li >  
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">60 day funding:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['sixty_day_funding'] > $buying_time_thirty_sixty_count_array['dueCount'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['sixty_day_funding'] < $buying_time_thirty_sixty_count_array['dueCount'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_sixty_day_funding'] = $buying_time_thirty_sixty_count_array['dueCount'];
										?>                                     
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeSixty', '1','<?php echo $buying_time_thirty_sixty_count_array['dueCount'];?>');
				                                        return false;">
												<?= $buying_time_thirty_sixty_count_array['dueCount'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
	                    	<li >  
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">90 day funding:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['sixty_ninety_day_fundings'] > $buying_time_sixty_ninety_count_array['dueCount'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['sixty_ninety_day_fundings'] < $buying_time_sixty_ninety_count_array['dueCount'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}
											$_SESSION['tmp_sixty_ninety_day_fundings'] = $buying_time_sixty_ninety_count_array['dueCount'];
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeNinety', '1','<?php echo $buying_time_sixty_ninety_count_array['dueCount'];?>');
				                                        return false;">
												<?= $buying_time_sixty_ninety_count_array['dueCount'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
	                   		<li >   
				                        <p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Clients:
				                        <?php
											$val_color = '#337ab7';
											$span_val = '';
											if ($_SESSION['clients'] > $sum_array['clients'])
											{
												$val_color = 'red';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
											}else if ($_SESSION['clients'] < $sum_array['clients'])
											{
												$val_color = 'green';
												$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
											}	
											$_SESSION['tmp_clients'] = $sum_array['clients'];										
										?>
				                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Clients','<?php echo $sum_array['clients'];?>');
				                                        return false;">
				                               <?= $sum_array['clients'].' '.$span_val; ?>
				                            </a>
				                        </p>
				                    </li>
		                    	
							<!-- Click to : ClicktoCall, ClicktoSMS, ClicktoEmail -->
							
							<li class="active"><a href="#">Group Texts and Emails<span class="sr-only">(current)</span></a></li>
							<form class="form"  class="navbar-form navbar-left" style="padding:10px 15px 10px 15px;margin:5px 0px 5px 0px" method="post" action="searchbuyer.php">
								<div class="form-group my-form-group">
									<!--<button type="button" class="btn btn-success btn-md my-sms-button" id="phone_call_btn_mobile">Call<span id="call_new_span_mobile" style="visibility:hidden;" class="badge"></span></button>			    		-->
									<button type="button" class="btn btn-success btn-sm my-sms-button" id="send_sms_btn_mobile">SMS<span id="sms_news_span_mobile" style="visibility:hidden;" class="badge"></span></button>
									<button type="button" class="btn btn-success btn-sm my-sms-button" id="send_eml_btn_mobile">Email<span id="email_new_span_mobile" style="visibility:hidden;" class="badge"></span></button>
							    </div>											    						    	
							</form> 
							
							<!-- Auto Dialer -->
							<li class="active"><a href="#">Auto Dialer ...<span class="sr-only">(current)</span></a></li>
							<form class="form"  class="navbar-form navbar-left" style="padding:10px 15px 10px 15px;margin:5px 0px 5px 0px" method="post" action="searchbuyer.php">
								<div class="form-group" >
									<button type="button" class="btn btn-success btn-md my-sms-button" id = "auto_dialer_start_mobile" >Start</button>			    		
									<button type="button" class="btn btn-success btn-sm my-sms-button" id = "auto_dialer_stop_mobile" >Stop</button>
									<button type="button" class="btn btn-success btn-sm my-sms-button" id = "auto_dialer_restart_mobile" >Restart</button>
						    	</div>		
						    	
						    	<div class="form-group" style="margin:0px">
									<label style=text-align:left;font-weight:100;" >Calling : <br> </label>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<center>
											<span id="calling_person_mobile"></span>		
										</center>
									</div>												
						    	</div>									    						    	
						    	<!--<div class="form-group" style="margin-botton:0px">
									<label style="width:60%;text-align:left;font-weight:100;" >Interval Time : </label>
				                	<input class="form-control  my-form-control" style="width:20%;display:inline-block;padding:0px" type="text" name="auto_dialer_interval" id="auto_dialer_interval" value="5">min
						    	</div>																    						    	-->
							</form> 							
						
						</ul>   
					</div>
					<div class="col-xs-3 text-center">
					    		<a href="searchbuyer.php?rst=1" style="color:white"><span class="glyphicon glyphicon-home"></span><br>Home</a>
							</div>
					<div class="col-xs-2 text-center">
					    		<a href="buyerinfo2.php?action=add" style="color:white"><span class="glyphicon glyphicon-plus" ></span><br>Add</a>
							</div>
					<div class="col-xs-2 text-center">
								<!--<li class="dropdown" >-->
				               	<a class="dropdown-toggle" data-toggle="dropdown" style="color:white" href="#"><span class="glyphicon glyphicon-cog" ></span><br>Setting</a>
				                <ul class="dropdown-menu">          
				                	<li><a href="help.php"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;Help</a></li>	
				                	<li class="divder-new"></li>
			                		<li style="background-color:white"><a href="editprofile.php"  style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-picture"></span>&nbsp;Profile Information</a></li>
		                			<li class="divder-new"></li>
									<li ><a href="passchange.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Change Password</a></li>
									<?php
									if ($_SESSION['user_group'] == "Manager") {
									?>
										<li class="divder-new"></li>
										<li><a href="manager_report.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Report</a></li>	
										<li class="divder-new"></li>
						                <li style="background-color:white"><a href="addagent.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-user"></span>&nbsp;Add Sales Agent</a></li>
									<?php
						            }else if ($_SESSION['user_group'] == "Admin") {
									?>
										<li class="divder-new"></li>
										<li><a href="admin_report.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Report</a></li>	
										<li class="divder-new"></li>
						                <li style="background-color:white"><a href="addagent.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-user"></span>&nbsp;Add Sales Agent</a></li>
										<li class="divder-new"></li>
						             	<li style="background-color:white"> <a href="addlease.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;Add Lease Login</a></li>
						             	<li class="divder-new"></li>
						             	<li style="background-color:white"><a href="changeaccess.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Change Login Credential</a></li>			        						             	
							            <li class="divder-new"></li>
							            <li style="background-color:white"><a href="xls_gen_info.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-download"></span>&nbsp;Export To Excel</a></li>		        
						             <?php
						             }
						             ?>	
						             <li class="divder-new"></li>
						             <li style="background-color:white"><a href="importfromcsv.php" style="padding-top: 10px;padding-bottom:10px;"><span class="glyphicon glyphicon-upload"></span>&nbsp;Import From Excel(*.csv)</a></li>	                    
				               	</ul>				    
							</div>
					<div class="col-xs-3 text-center">
					    		<a href="adminlogout.php" style="color:white"><span class="glyphicon glyphicon-log-out"></span><br>Logout</a>
							</div>				    
				</div>			   
			</div>
		</nav>
				
		<!-- main content -->
		<div class="container" style="margin:1px;width:99%">
			
			<div id="my-main-content">
				
				 <br>
				<!-- Last Login, Duration, ... -->
				<div class="row">					
					<center>
						<div id="tab-container" class="head-box-layer">
		                    <ul class="etabs">                                
		                      	<div> 
		                            <table class="head-box desktop-my-menu" style="background-color: #ca6497;">
		                            	<tr align="center">
		                                  <td>Last Login</td>
		                                </tr>
		                                <tr align="center">
		                             	   <td><?php if (isset($recb['lst_log_info'])) {
		                                            echo $recb['lst_log_info'];
		                                         }else{
		                                            echo '0';
		                                         }?></td>
		                                </tr>
		                            </table>
		                        </div>
		                        <div style="margin-left: -20px">  
		                            <table class="head-box mobile-info-box" style="background-color: #3e75b4;">
		                            	<tr align="center">
		                                    <td>Duration</td>
		                                </tr>
		                                <tr align="center">
		                                     <td ><div id="dur_div"><?php if (isset($recb['dur_info'])) {
		                                            echo $recb['dur_info'];
		                                         }else{
		                                            echo '00:00:00';
		                                         }?></div>                                                      
		                                    </td>
		                                </tr>                                     
		                            </table>
		                        </div>
		                        <div> 
		                            <table class="head-box mobile-info-box" style="background-color: #5bba5f;">
		                              <tr align="center">
		                              	<td><a href="javascript:GetCallHistory();" style="color:#ffffff;">Calls Made/Connected</a></td>
		                              </tr>
		                              <tr align="center">
		                                <td><div id="calls_div"><?php if (isset($_SESSION['calls_made']) and isset($_SESSION['calls_con'])) {
		                                        echo $_SESSION['calls_made']."/".$_SESSION['calls_con'];
		                                     }else{
		                                        echo '0/0';
		                                     }?></div>
		                                </td>
		                              </tr>                                    
		                            </table>
		                        </div>
		                        <div> 
		                            <table class="head-box desktop-my-menu" style="background-color: #eeae52;">
		                              <tr align="center">
		                                <td>Conversation Minutes</td>
		                              </tr>
		                              <tr align="center">
		                                <td><div id="conv_div"><?php if (isset($_SESSION['conv_minutes'])) {
		                                        echo $_SESSION['conv_minutes'];
		                                     }else{
		                                        echo '0';
		                                     }?></div>
		                            	</td>
		                              </tr>                                     
		                            </table>
		                        </div>
		                        <div> 
		                           <table class="head-box desktop-my-menu" style="background-color: #d65752;">
		                              <tr align="center">
		                              	<td><a href="javascript:GetEmailHistory();" style="color:#ffffff;">Emails Sent/Received</a></td>
		                              </tr>
		                              <tr align="center">
		                                 <td><div id="emls_div"><?php if (isset($_SESSION['email_sent']) and isset($_SESSION['email_recv'])) {
		                                        echo $_SESSION['email_sent']."/". $_SESSION['email_recv'];
		                                     }else{
		                                        echo '0/0';
		                                     }?></div>
		                                </td>
		                              </tr>                                      
		                            </table>
		                        </div>            
		                        <div> 
		                            <table class="head-box desktop-my-menu" style="background-color: #3399cc;">
		                              <tr align="center">
		                               <td><a href="javascript:GetSMSHistory();" style="color:#ffffff;">SMS Sent/Received</a></td>
		                              </tr>
		                              <tr align="center">
		                                 <td><div id="sms_div"><?php if (isset($_SESSION['sms_sent']) and isset($_SESSION['sms_recv'])) {
		                                        echo $_SESSION['sms_sent']."/". $_SESSION['sms_recv'];
		                                     }else{
		                                        echo '0/0';
		                                     }?></div>
		                                </td>
		                              </tr>                                      
		                            </table>
		                        </div>        
		                        <div> 
		                          <table class="head-box mobile-info-box" style="background-color: #2BC5B5;">
		                              	<tr align="center">
		                               		<td>Ratio</td>
		                              	</tr>
		                            	<tr align="center">
		                                   <td ><div id="ratio_div"><?php if (isset($_SESSION['ratio'])) {
		                                        echo $_SESSION['ratio'].'%';
		                                     }else{
		                                        echo '0.0%';
		                                     }?></div>
			                            	</td>	                            	   
			                             </tr>
		                              	<tr align="center">
		                               		<td style="color:yellow">Goal:90% Plus</td>
		                              	</tr>                                      
		                            </table>
		                        </div> 	                              
		                   </ul>
		                </div>					
					</center>			
				</div>
				<br>
				
				<!--Search box-->
				<div class="row">
					<div class="col-md-offset-3 col-md-6 col-md-offset-3 ">
						<div class="input-group" id="adv-search" style="display: inline">
			                <input type="text" class="form-control my-form-control" id="search_value" style="width:70%;height:34px !important" placeholder="Search for" value="<?php
									if (isset($_POST['val'])) {
	                                    echo $_POST['val'];
	                                }?>"/>
			                <div class="input-group-btn">
			                    <div class="btn-group" >
			                    	<button type="button" name="Submit" style="height:34px" onclick="javascript:ChangeSearchValue();" class="btn btn-primary"  ><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
			                        <div class="dropdown dropdown-lg">
			                            <button type="button" class="btn btn-default dropdown-toggle" style="height:34px" data-toggle="dropdown" ><span class="caret"></span></button>
			                            <div class="dropdown-menu dropdown-menu-right">
			                                <form class="form-horizontal" id="search_form" method="post" action="searchbuyer.php" >
			                                	<div class="form-group"><input type="text" class="form-control" name="val" id="val"/></div>
			                                	<div class="form-group">
				                                    <label for="filter">Filter by</label>
				                                    <select class="form-control" id="field_nm" name="field_nm">
													<option value=""></option>
													<option value="lead_src" <?php if($_POST['field_nm']=="lead_src"){echo 'selected';}?>>Source</option>
				                                    <option value="p_fl_nm" <?php if($_POST['field_nm']=="p_fl_nm"){echo 'selected';}?>>Name</option>
				                                    <option value="p_ph" <?php
					                                    if ($_POST['field_nm'] == "p_ph") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Phone</option>
					                                <option value="p_eml" <?php
					                                    if ($_POST['field_nm'] == "p_eml") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Email</option>  
					                                <option value="priority_opt" <?php
					                                    if ($_POST['field_nm'] == "priority_opt") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Priority</option>			      
					                                <option value="out_come" <?php
					                                    if ($_POST['field_nm'] == "out_come") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Conversation</option>			      
				                                    <option value="cust_upd_dt" <?php
					                                    if ($_POST['field_nm'] == "cust_upd_dt") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Last Update</option>
				                                    
				                                    <option value="apply_dt" <?php
					                                    if ($_POST['field_nm'] == "apply_dt") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Start Date</option>
				                                   	
				                                   	<option value="funding_dt" <?php
					                                    if ($_POST['field_nm'] == "funding_dt") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Funding Time</option>                                  
				                                    
				                                    <option value="dob" <?php
					                                    if ($_POST['field_nm'] == "dob") {
					                                        echo 'selected';
					                                    }
					                                    ?>>DOB</option>           
					                                
					                                <option value="ss" <?php
					                                    if ($_POST['field_nm'] == "ss") {
					                                        echo 'selected';
					                                    }
					                                    ?>>SS#</option>     
					                                    
					                                <option value="login" <?php
					                                    if ($_POST['field_nm'] == "login") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Login</option>                 
					                               
					                                <option value="password" <?php
					                                    if ($_POST['field_nm'] == "password") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Password</option>              
					                                         
				                                    <option value="hm_addr" <?php
				                                        if ($_POST['field_nm'] == "hm_addr") {
				                                            echo 'selected';
				                                        }
				                                        ?>>Address</option>
				                                    <option value="city" <?php
					                                    if ($_POST['field_nm'] == "city") {
					                                        echo 'selected';
					                                    }
					                                    ?>>City</option>
				                                    <option value="city" <?php
					                                    if ($_POST['field_nm'] == "zip") {
					                                        echo 'selected';
					                                    }
					                                    ?>>ZIP</option>
					                                
					                                <option value="cd_name" <?php
					                                    if ($_POST['field_nm'] == "cd_name") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Card Name</option>
					                                    
					                                <option value="cd_number" <?php
					                                    if ($_POST['field_nm'] == "cd_number") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Card Number</option>
					                                
					                                <option value="issu_bnk" <?php
					                                    if ($_POST['field_nm'] == "issu_bnk") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Issuing Bank</option>
					                                
					                                <option value="opportunity" <?php
					                                    if ($_POST['field_nm'] == "opportunity") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Opportunity</option>
					                                
					                                <option value="referal_company_name" <?php
					                                    if ($_POST['field_nm'] == "referal_company_name") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Referal Company</option>
					                                
					                                <option value="referal_person_name" <?php
					                                    if ($_POST['field_nm'] == "referal_person_name") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Referal Person</option>
					                                
					                                <option value="fee_amount" <?php
					                                    if ($_POST['field_nm'] == "fee_amount") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Fee Amount</option>
					                                
					                                <option value="b_leg_nm" <?php
					                                    if ($_POST['field_nm'] == "b_leg_nm") {
					                                        echo 'selected';
					                                    }
					                                    ?>>Business Name</option>
				                                </select>		                                  
			                                  	</div>
	 											<div class="form-group">
													<div class="checkbox">
														<label><input type="checkbox" name="search_manager" value="search_manager" <?php  if ($_SESSION['user_group'] != "Admin") echo 'disabled'; else if ($_POST['search_manager'] == "search_manager") echo 'checked';?>>Manager</label>						      
												    </div>
												    <div class="checkbox">
														<label><input type="checkbox" name="search_agent" value="search_agent" <?php  if (($_SESSION['user_group'] != "Admin") and ($_SESSION['user_group'] != "Manager")) echo 'disabled'; else if ((($_SESSION['user_group'] == "Admin") or ($_SESSION['user_group'] == "Manager")) and ($_POST['search_agent'] == "search_agent")) echo 'checked';?>>Agent</label>
												    </div>
												    <div class="checkbox">
												      	<label><input type="checkbox"  name="search_customer" value="search_customer" <?php  if ($_POST['search_customer'] == "search_customer") echo 'checked';?>>Customer</label>
												    </div>
											    </div>
												
												<input type="hidden" name="hdnTodaysFolloups" value="0" id="hdnTodaysFolloups" />
					                            <input type="hidden" name="hdnSevenDayOverdue" value="0" id="hdnSevenDayOverdue" />
					                            <input type="hidden" name="hdnThirtyDayOverdue" value="0" id="hdnThirtyDayOverdue" />
					                            <input type="hidden" name="is_opportunity" value="0" id="is_opportunity" />
					                            <input type="hidden" name="hdnBuyingTimeThirty" value="0" id="hdnBuyingTimeThirty" />
					                            <input type="hidden" name="hdnBuyingTimeSixty" value="0" id="hdnBuyingTimeSixty" />
					                            <input type="hidden" name="hdnBuyingTimeNinety" value="0" id="hdnBuyingTimeNinety" />
					                            <input type="hidden" name="hdnBuyingTimeEighty" value="0" id="hdnBuyingTimeEighty" />
					                            <input type="hidden" name="hdnVolume_Buyers" value="0" id="hdnVolume_Buyers" />
					                            <input type="hidden" name="no_follow_up_date" value="0" id="no_follow_up_date" />
					                             <!-- Calculate for Duration -->
			                           			<input type="hidden" name="cur_login_time" value="<?php echo $_SESSION['cur_login_time']; ?>" id="cur_login_time" />
											    <button type="submit" class="btn btn-primary" name="Submit" id="btnSearch" value="Search"><span class="glyphicon glyphicon-search"></span>&nbsp;Search</button> 
			                                </form>
			                            </div>
			                        </div>
			                        
			                    </div>
			                </div>
			            </div>		            
			        </div>
			    </div>		
				<!-- Post box -->
	   			<!--<div class="row">
	   				<center>
		            	<table style="width:80%">
		                    <tr height="25px">
		                       	<td style="width:90%;" rowspan='2'>
		                        	<textarea style="font-size:11px; background:#F9F8C2; border:1px solid #999999;width:98%;height:100%;" name="post_remarks" id="post_remarks" ><?php 
		                            	if (isset($_POST['remarks'])) {
		                                    echo $_POST['remarks'];
		                                } else if (isset($recb['remarks'])) {
		                                    echo $recb['remarks'];
		                                } ?></textarea>
		                        </td>      
		                        <td>
		                     		<button class="btn btn-primary ladda-button" onclick = "javascript:ClickPostIdea();" style = "font-size:11px; padding:0 0; width:45px; height:20px;" data-style="zoom-in" type="submit"  name="submit" id="post_btn" value="PostMyIdea" />Post</button>	   	
		                        </td>
		                    </tr>
		                    <tr height="25px">
		                        <td> 
					           		<button class="btn btn-primary ladda-button" onclick = "javascript:ClickShowIdeas();" style = "font-size:11px; padding:0 0; width:45px; height:20px;"  data-style="zoom-in" type="submit"  name="submit" id="view_idea_btn" value="ShowPostedIdeas" onclick="window.open('showPosted_ideas.php');"/>View</button>	
					            </td>   					                                                                                                       
		                    </tr>
						</table>  					
	   				</center>            
	   			</div>-->
				<br>		
				
				<!-- Statistics box -->			
				<div id="my-main-content-left" style="margin:1px;">
					<!-- right navigation bar -->
		           	<div class="my-switch-vscroll desktop-my-menu">
						<div class="row" style="padding-top:2px;margin-left:2px;margin-right:2px;">
							<div class="col-xs-12" style="padding:1px">								
								<form class="form"  style="margin:2px 2px 2px 4px;" method="post" action="searchbuyer.php">
									<input type="hidden" name="hdnTodaysFolloups" value="0" id="hdnTodaysFolloups" />
		                            <input type="hidden" name="hdnSevenDayOverdue" value="0" id="hdnSevenDayOverdue" />
		                            <input type="hidden" name="hdnThirtyDayOverdue" value="0" id="hdnThirtyDayOverdue" />
		                            <input type="hidden" name="is_opportunity" value="0" id="is_opportunity" />
		                            <input type="hidden" name="hdnBuyingTimeThirty" value="0" id="hdnBuyingTimeThirty" />
		                            <input type="hidden" name="hdnBuyingTimeSixty" value="0" id="hdnBuyingTimeSixty" />
		                            <input type="hidden" name="hdnBuyingTimeNinety" value="0" id="hdnBuyingTimeNinety" />
		                            <input type="hidden" name="hdnBuyingTimeEighty" value="0" id="hdnBuyingTimeEighty" />
		                            <input type="hidden" name="hdnVolume_Buyers" value="0" id="hdnVolume_Buyers" />
		                            <input type="hidden" name="no_follow_up_date" value="0" id="no_follow_up_date" />
		                           
		                            <div class="panel panel-default" style="margin-bottom:5px">
		                            	<div class="panel-heading">Group Texts and Emails</div>
									    <div class="panel-body">
									    	<div class="form-group my-form-group">
									    		<center>
													<!--<button type="button" class="btn btn-success btn-md my-sms-button" id="phone_call_btn">Call<span id="call_new_span" style="visibility:hidden;" class="badge"></span></button>-->
													<button type="button" class="btn btn-success btn-sm " id="send_sms_btn">SMS<span id="sms_news_span" style="visibility:hidden;" class="badge"></span></button>
													<button type="button" class="btn btn-success btn-sm " id="send_eml_btn">Email<span id="email_new_span" style="visibility:hidden;" class="badge"></span></button>									    			
									    		</center>
									    	</div>											    						    	
									  	</div>
									  	
									  	
										<div class="panel-heading">WORKSPACE</div>
									    <div class="panel-body">
									    		<p>Call:
												<?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['call'] > $sql_click_call_ary['calls'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['call'] < $sql_click_call_ary['calls'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													//$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
												?><a href="javascript: setFilterOnLeadsOverdue('call', '1','<?php echo $sql_click_call_ary['calls'];?>');" style="color:<?php echo $val_color;?>" >
						                                <?= $sql_click_call_ary['calls'].' '.$span_val; ?></a>         
						                        </p>
												
												<p>Email:
												<?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['email'] > $sql_click_email_ary['email'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['email'] < $sql_click_email_ary['email'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													//$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
												?><a href="javascript: setFilterOnLeadsOverdue('email', '1','<?php echo $sql_click_email_ary['email'];?>');" style="color:<?php echo $val_color;?>" >
						                                <?= $sql_click_email_ary['email'].' '.$span_val; ?></a>         
											    </p>
												
												<p>SMS:
												<?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['sms'] > $sql_click_sms_ary['sms'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['sms'] < $sql_click_sms_ary['sms'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													//$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
												?><a href="javascript: setFilterOnLeadsOverdue('sms', '1','<?php echo $sql_click_sms_ary['sms'];?>');" style="color:<?php echo $val_color;?>" >
						                                <?= $sql_click_sms_ary['sms'].' '.$span_val; ?></a>     						                           
						                        </p>
												
												<p>Today's Task:
						                        <?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['followUpCount'] > $followup_count_array['followUpCount'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['followUpCount'] < $followup_count_array['followUpCount'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													$_SESSION['tmp_followUpCount'] = $followup_count_array['followUpCount'];
													
												?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnTodaysFolloups', '1','<?php echo $followup_count_array['followUpCount'];?>');return false;">
						                                <?= $followup_count_array['followUpCount'].' '.$span_val; ?></a>                            
						                        </p>
						                 
						                        <p>Past Due:
						                        <?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['past_due'] > $sevenoverdue_count_array['overdueCount'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";

													}else if ($_SESSION['past_due'] < $sevenoverdue_count_array['overdueCount'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													$_SESSION['tmp_past_due'] = $sevenoverdue_count_array['overdueCount'];
													
												?>                                            
						                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnSevenDayOverdue', '1','<?php echo $sevenoverdue_count_array['overdueCount'];?>');
						                                            return false;">
						                                <?= $sevenoverdue_count_array['overdueCount'].' '.$span_val; ?>
						                            </a>                                            
						                        </p>
						                   
						                        <p>Delinquent:
						                        <?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['delinquent'] > $thirty_count_array['overdueCount'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['delinquent'] < $thirty_count_array['overdueCount'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													$_SESSION['tmp_delinquent'] = $thirty_count_array['overdueCount'];
													
												?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnThirtyDayOverdue', '1','<?php echo $thirty_count_array['overdueCount'];?>');
						                                            return false;">
						                          	<?= $thirty_count_array['overdueCount'].' '.$span_val; ?>
						                          </a>
						                        </p>
						                        
						                         <p>No FollowUp Date:
						                        <?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['no_follow_up_date'] > $no_follow_up_date_array['no_follow_up_date_count'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['no_follow_up_date'] < $no_follow_up_date_array['no_follow_up_date_count'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													
													
												?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('no_follow_up_date', '1','<?php echo $no_follow_up_date_array['no_follow_up_date_count'];?>');
						                                            return false;">
						                          	<?= $no_follow_up_date_array['no_follow_up_date_count'].' '.$span_val; ?>
						                          </a>
						                        </p>
						                   </div> 
					                	<div class="panel-heading">OPPORTUNITY</div>
									    <div class="panel-body">
									    	<p>Hot:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['hotLeads'] > $sum_array['hotLeads'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['hotLeads'] < $sum_array['hotLeads'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}	
												$_SESSION['tmp_hotLeads'] = $sum_array['hotLeads'];										
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Hot','<?php echo $sum_array['hotLeads'];?>');
					                                        return false;">
					                               <?= $sum_array['hotLeads'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                        <p>Credit Ready:
		                                        <?php
													$val_color = '#337ab7';
													$span_val = '';
													if ($_SESSION['credit_ready'] > $sum_array['credit_ready'])
													{
														$val_color = 'red';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
													}else if ($_SESSION['credit_ready'] < $sum_array['credit_ready'])
													{
														$val_color = 'green';
														$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
													}
													$_SESSION['tmp_credit_ready'] = $sum_array['credit_ready'];
												?>
		                                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Credit Ready','<?php echo $sum_array['credit_ready'];?>');
		                                                        return false;">
		                                               <?= $sum_array['credit_ready'].' '.$span_val; ?>
		                                            </a>
		                                        </p>
		                                    <p>Pre-approved:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['pre_approveds'] > $sum_array['pre_approveds'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['pre_approveds'] < $sum_array['pre_approveds'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}	
												$_SESSION['tmp_pre_approveds'] = $sum_array['pre_approveds'];										
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Pre-approved','<?php echo $sum_array['pre_approveds'];?>');
					                                        return false;">
					                               <?= $sum_array['pre_approveds'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                   
					                        <p>Other Opportunity:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['other_opportunity'] > $sum_array['other_opportunity'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['other_opportunity'] < $sum_array['other_opportunity'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_other_opportunity'] = $sum_array['other_opportunity'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('is_opportunity', '1','<?php echo $sum_array['other_opportunity'];?>');
					                                        return false;">
					                               <?= $sum_array['other_opportunity'].' '.$span_val; ?>
					                            </a>
					                        </p>					                						                    
										</div> 
					                	
					                	<div class="panel-heading">SALES</div>		        
					                	<div class="panel-body">
					                		<p>New Leads : <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['newLeads'] > $sum_array['newLeads'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['newLeads'] < $sum_array['newLeads'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_newLeads'] = $sum_array['newLeads'];
											?><a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'New','<?php echo $sum_array['newLeads'];?>'); return false;">
													<?= $sum_array['newLeads'].' '.$span_val;?></a>
											</p>
										
						                 	<p>Opened Emails
											<?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['opened_emails'] > $opened_email_array['opened_emails'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['opened_emails'] < $opened_email_array['opened_emails'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_opened_emails'] = $opened_email_array['opened_emails'];
												
											?>
						                        <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Opened Emails','<?php echo $opened_email_array['opened_emails'];?>');
						                                    return false;">
						                           <?= $opened_email_array['opened_emails'].' '.$span_val; ?>
						                        </a>                                                
						                    </p>
					                   
						                    <p>Clickthroughs:
											<?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['clickthroughs'] > $sum_array['clickthroughs'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['clickthroughs'] < $sum_array['clickthroughs'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_clickthroughs'] = $sum_array['clickthroughs'];
												
											?>
						                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Clickthroughs','<?php echo $sum_array['clickthroughs'];?>');
						                                        return false;">
						                               <?= $sum_array['clickthroughs'].' '.$span_val; ?>
						                            </a>
						                    </p>
					                    
											<p>Retry :
											<?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['retryLeads'] > $sum_array['retryLeads'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['retryLeads'] < $sum_array['retryLeads'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_retryLeads'] = $sum_array['retryLeads'];
												
											?>
						                        <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Retry','<?php echo $sum_array['retryLeads'];?>');
						                                    return false;">
						                            <?= $sum_array['retryLeads'].' '.$span_val; ?>
						                        </a>
						                    </p>	                    
					                      
					                        <p> Warm:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['warmLeads'] > $sum_array['warmLeads'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['warmLeads'] < $sum_array['warmLeads'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_warmLeads'] = $sum_array['warmLeads'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Warm','<?php echo $sum_array['warmLeads'];?>');
					                                        return false;">
					                               <?= $sum_array['warmLeads'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                    
					                        <p>Credit Check:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['credit_checks'] > $sum_array['credit_checks'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['credit_checks'] < $sum_array['credit_checks'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_credit_checks'] = $sum_array['credit_checks'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Credit Check','<?php echo $sum_array['credit_checks'];?>');
					                                        return false;">
					                               <?= $sum_array['credit_checks'].' '.$span_val; ?>
					                            </a>                                                 
					                        </p>
					                    
					                        <p>Credit Repair:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['credit_repairs'] > $sum_array['credit_repairs'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['credit_repairs'] < $sum_array['credit_repairs'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_credit_repairs'] = $sum_array['credit_repairs'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Credit Repair','<?php echo $sum_array['credit_repairs'];?>');
					                                        return false;">
					                               <?= $sum_array['credit_repairs'].' '.$span_val; ?>
					                            </a>                                             
					                        </p>					                    	
					                	</div>
		                			    <div class="panel-heading">STATISTIC</div>
		                			    <div class="panel-body">		
		                			    	
					                        <p>Doc. Sent:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['doc_sents'] > $sum_array['doc_sents'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['doc_sents'] < $sum_array['doc_sents'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_doc_sents'] = $sum_array['doc_sents'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Doc. Sent','<?php echo $sum_array['doc_sents'];?>');
					                                        return false;">
					                               <?= $sum_array['doc_sents'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                    
					                      	<p>Pending Funding :
					                      	<?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['pending_fundings'] > $sum_array['pending_fundings'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['pending_fundings'] < $sum_array['pending_fundings'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}	
												$_SESSION['tmp_pending_fundings'] = $sum_array['pending_fundings'];										
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Pending Funding','<?php echo $sum_array['pending_fundings'];?>');
					                                        return false;">
					                               <?= $sum_array['pending_fundings'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                     
					                        <p>Funded :
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['fundedLeads'] > $sum_array['fundedLeads'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['fundedLeads'] < $sum_array['fundedLeads'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}	
												$_SESSION['tmp_fundedLeads'] = $sum_array['fundedLeads'];										
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Funded','<?php echo $sum_array['fundedLeads'];?>');
					                                        return false;">
					                               <?= $sum_array['fundedLeads'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                     
					                        <p>Fee Pending :
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['fee_pending'] > $sum_array['fee_pending'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['fee_pending'] < $sum_array['fee_pending'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_fee_pending'] = $sum_array['fee_pending'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Fee Pending','<?php echo $sum_array['fee_pending'];?>');
					                                        return false;">
					                               <?= $sum_array['fee_pending'].' '.$span_val; ?>
					                            </a>                                                  
					                        </p>
					                   
					                        <p>30 day funding :
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['thirty_day_funding'] > $buying_time_thirty_count_array['dueCount'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['thirty_day_funding'] < $buying_time_thirty_count_array['dueCount'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_thirty_day_funding'] = $buying_time_thirty_count_array['dueCount'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeThirty', '1','<?php echo $buying_time_thirty_count_array['dueCount'];?>');
					                                            return false;">
					                            	<?= $buying_time_thirty_count_array['dueCount'].' '.$span_val; ?>
					                            </a>
					                     
					                        </p>
					                       
					                        <p>60 day funding:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['sixty_day_funding'] > $buying_time_thirty_sixty_count_array['dueCount'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['sixty_day_funding'] < $buying_time_thirty_sixty_count_array['dueCount'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_sixty_day_funding'] = $buying_time_thirty_sixty_count_array['dueCount'];
											?>                                     
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeSixty', '1','<?php echo $buying_time_thirty_sixty_count_array['dueCount'];?>');
					                                        return false;">
													<?= $buying_time_thirty_sixty_count_array['dueCount'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                    
					                        <p>90 day funding:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['sixty_ninety_day_fundings'] > $buying_time_sixty_ninety_count_array['dueCount'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['sixty_ninety_day_fundings'] < $buying_time_sixty_ninety_count_array['dueCount'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}
												$_SESSION['tmp_sixty_ninety_day_fundings'] = $buying_time_sixty_ninety_count_array['dueCount'];
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeNinety', '1','<?php echo $buying_time_sixty_ninety_count_array['dueCount'];?>');
					                                        return false;">
													<?= $buying_time_sixty_ninety_count_array['dueCount'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                      
					                        <p>Clients:
					                        <?php
												$val_color = '#337ab7';
												$span_val = '';
												if ($_SESSION['clients'] > $sum_array['clients'])
												{
													$val_color = 'red';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-bottom'></span>";
												}else if ($_SESSION['clients'] < $sum_array['clients'])
												{
													$val_color = 'green';
													$span_val = "<span class='glyphicon glyphicon glyphicon-triangle-top'></span>";
												}	
												$_SESSION['tmp_clients'] = $sum_array['clients'];										
											?>
					                            <a href="#" style="color:<?php echo $val_color;?>" onClick="setFilterOnLeads('priority_opt', 'Clients','<?php echo $sum_array['clients'];?>');
					                                        return false;">
					                               <?= $sum_array['clients'].' '.$span_val; ?>
					                            </a>
					                        </p>
					                    
					                                                                                       
								    	</div>		                			    															    
									</div>
								</form>
							</div>
						</div>
					</div>  
				</div>
				
				<!-- Main content -->
				<div id="my-main-content-right">
            		<div id="my-main-content-right-content">        						
			   			<div class="row" style="margin-left:-5px">
			   				<div class="panel panel-default" style="margin:0px;padding:2px 0 2px">
			   					<!-- Dashboard icon -->	
								<div class="panel-heading" style="height:25px;padding:2px">
										 	<div class="dashbord-icon"><a href="#" onClick="submitForm(); return false;" style="color:#13a6ec"><span class="glyphicon glyphicon-floppy-disk"></span></a></div> <!--<img src="buyer_details_img/disk.gif" />-->
				<?php if ($_REQUEST['action'] != 'add') { ?>
											<div class="dashbord-strip"></div>
					<?php if ($row_offset_position['POSITION'] > 0) { ?>
				                            <div class="dashbord-icon"><a href="buyerinfo2.php?rid=<?= $row_previous['customer_id'] ?>" style="color:#13a6ec"><span class="glyphicon glyphicon-triangle-left"></span></a></div>
					<?php } else { ?>
											<div class="dashbord-icon"><span class="glyphicon glyphicon-triangle-left" style="color:#13a6ec"></span></div>
				<?php } ?>
											<div class="dashbord-icon-text" style="margin-top:-2px;padding:0px;font-size:14px"><?php echo $row_offset_position['POSITION'] + 1; ?> of <?= $row_record_count['totalCount'] ?></div>
				<?php if ($row_offset_position['POSITION'] < ($row_record_count['totalCount'] - 1)) { ?>
				                            <div class="dashbord-icon"><a href="buyerinfo2.php?rid=<?= $row_next['customer_id'] ?>" style="color:#13a6ec"><span class="glyphicon glyphicon-triangle-right"></span></a></div>
				<?php } else { ?>
				                            <div class="dashbord-icon"><img src="buyer_details_img/Play.gif" /></div>
				<?php } ?>
				                            <!--<div class="dashbord-icon">
				                            	<a href="addnote.php?uid=<?php echo $recb['customer_id']; ?>" onClick="wopen('addnote.php?uid=<?php echo $recb['customer_id']; ?>', 'popup', 500, 500); return false;" target="popup" style="color:#13a6ec"><span class="glyphicon glyphicon-floppy-disk"></span>
				                                </a>
				                            </div>-->
				                            <div class="dashbord-strip"></div>
				<?php } ?>
				                            <!--<div class="dashbord-icon" style="width:50%; text-align:center; color:#FF0000;"><?php echo $msg; ?><div class="dashbord-icon"></div></div>-->
									</div>

								<!-- Tabs -->
								<div class="panel-body" style="padding-left:2px;padding-right:2px">
										
				                        <form name="default_emplate" id="default_emplate" method="post" enctype="multipart/form-data">

				                          	<!-- Nav tabs -->
				                            <ul class="nav nav-tabs desktop-my-menu" style="font-size:14px">
											 	<li class="active"><a href="#lead_information" data-toggle="tab" aria-expanded="true" >Lead</a></li>
											    <li class=""><a href="#funding_tab" data-toggle="tab" aria-expanded="false" >Funding</a></li>
											    <li class=""><a href="#opportunity_tab" data-toggle="tab" aria-expanded="false" >Opportunity</a></li>
											    <li class=""><a href="#financial_tab" data-toggle="tab" aria-expanded="false" >Financial</a></li>
											    <li class=""><a href="#log_tab" data-toggle="tab" aria-expanded="false" >History</a></li>											    
											 </ul>
											<ul class="nav nav-tabs mobile-my-menu" style="font-size:16px !important;">
											 	<li class="active"><a href="#lead_information" data-toggle="tab" aria-expanded="true" style="padding:6px">Lead</a></li>
											    <li class=""><a href="#funding_tab" data-toggle="tab" aria-expanded="false" style="padding:6px">Fund..</a></li>
											    <li class=""><a href="#opportunity_tab" data-toggle="tab" aria-expanded="false" style="padding:6px">Oppor..</a></li>
											    <li class=""><a href="#financial_tab" data-toggle="tab" aria-expanded="false" style="padding:6px">Fina..</a></li>
											    <li class=""><a href="#log_tab" data-toggle="tab" aria-expanded="false" style="padding:6px">History</a></li>											    
											 </ul>

											<!-- Tab panes -->
				                            <div class="tab-content">
				                                <div class="tab-pane fade in active" id="lead_information" >
				                                	<h3>Lead Information</h3>				                                	
				                                	<div class="panel-group">
				                                		<div class="panel panel-default" id="basic_panel">
				                                			<div class="panel-body">
					                                			<div class="row">
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group" style="margin-bottom:0px">
																			<center>
																				<label style="width:30%;text-align:left" for="apply_dt">Date</label>
																				<input name="apply_dt" id="apply_dt" class="form-control  my-form-control" style="display:inline-block;width:65%" type="text"   onFocus='popUpCalendar(this, document.default_emplate.apply_dt, "mm/dd/yyyy")' value="<?php if (isset($_POST['apply_dt'])) {
																								echo $_POST['apply_dt'];
																							} else if (isset($recb['apply_dt'])) {
																								echo $recb['apply_dt'];
																							} ?>">                                                              
																			</center>
																		</div>
																	</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group" style="margin-bottom:0px">
																			<center>
																				<label style="width:30%;text-align:left" for="lead_src">Source</label>
																				<input  class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="lead_src" id="lead_src"  value="<?php if (isset($_POST['lead_src'])) {
																								echo $_POST['lead_src'];
																							} else if (isset($recb['lead_src'])) {
																								echo $recb['lead_src'];
																							} ?>">
																			</center>
																		</div>
																	</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group" style="margin-bottom:0px">
																			<center>
																				<label style="width:30%;text-align:left" for="manager_id">Manager</label>
																				<input  class="form-control  my-form-control" style="display:inline-block;width:65%;background-color: #fff" type="text" name="manager_id" id="manager_id"  readonly value="<?php if (isset($_POST['manager_id'])) {
																								echo $_POST['manager_id'];
																							} else if (isset($recb['manager'])) {
																								echo $recb['manager'];
																							} ?>">
																				<select class="form-control  my-form-control" style="display:none;" name="manager_hidden_id" id="manager_hidden_id" class="" >
																					<option value=""></option>
																					<?php
																					$sql_pro = "select owner from admin_user where user_group='Agent' order by user_id";

																					$pro_res = mysql_query($sql_pro) or die(mysql_error());

																					while ($pro_rec = mysql_fetch_array($pro_res)) {
																						?>
																						<option value="<?php echo $pro_rec['owner']; ?>"><?php echo $pro_rec['owner']; ?></option>
																						<?php
																					}
																					?>
																				</select>
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group" style="margin-bottom:0px">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="user_id">Agent</label>
									                                			<select class="form-control  my-form-control" style="display:inline-block;width:65%" onchange="javascript:ChangeAgent();" name="user_id" id="user_id" class="" >
							                                                        <option value=""></option>
							                                                        <?php
							                                                        $sql_pro = "select user_id from admin_user where user_group='Agent' order by user_id";

							                                                        $pro_res = mysql_query($sql_pro) or die(mysql_error());

							                                                        while ($pro_rec = mysql_fetch_array($pro_res)) {
							                                                            ?>
							                                                            <option value="<?php echo $pro_rec['user_id']; ?>" <?php if ($pro_rec['user_id'] == $_POST['user_id']) {
							                                                                    echo "selected";
							                                                                } else if ($recb['agent'] == $pro_rec['user_id']) {
							                                                                    echo 'selected';
							                                                                } ?>>
							                                                                <?php echo $pro_rec['user_id']; ?>
							                                                            </option>
							                                                            <?php
							                                                        }
							                                                        ?>
							                                                    </select>                                					
							                                				</center>
																		</div>
							                                		</div>                                		
							                                	</div>
					                                		</div>
					  									</div>
					  									<div class="panel panel-info" id="person1_panel">
					  										 <div class="panel-heading">
													         	<h4 class="panel-title">
															        <a data-toggle="collapse" data-target="#collapsePerson1" 
															           href="#collapsePerson1" >Person1</a>
															    </h4>
													      	</div>
													      	<div id="collapsePerson1" class="panel-collapse collapse in">
													      		<div class="panel-body">
							  										<div class="row">
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p_fl_nm">Person1</label>
									                                			<input type="text" class="form-control  my-form-control" style="display:inline-block;width:65%" name="p_fl_nm" id="p_fl_nm"  value="<?php if (isset($_POST['p_fl_nm'])) {
							                                                                    echo $_POST['p_fl_nm'];
							                                                                } else if (isset($recb['p_fl_nm'])) {
							                                                                    echo $recb['p_fl_nm'];
							                                                                } ?>">
							                                				</center>
																		</div>
							                                		</div>
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p_ph1"><a href="javascript: ClicktoPhone(document.getElementById('p_ph1').value);">Phone1</a></label>
							                                                  	<input class="form-control  my-form-control" style="display:inline-block;width:65%;padding:0px" type="text" name="p_ph1" id="p_ph1"   value="<?php if (isset($_POST['p_ph1'])) {
							                                                                    $str = preg_replace("/[^0-9]*/s", "",$_POST['p_ph1']);
							                                                                    if ($str=='')
							                                                                        $_POST['p_ph1']='';
							                                                                    else
							                                                                    {
							                                                                        $_POST['p_ph1']='';
							                                                                        $_POST['p_ph1'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);
							                                                                    }
							                                                                    echo $_POST['p_ph1'];
							                                                                } else if (isset($recb['p_ph1'])) {
							                                                                    $str = preg_replace("/[^0-9]*/s", "",$recb['p_ph1']);
							                                                                     if ($str=='')
							                                                                        $recb['p_ph1']='';
							                                                                    else
							                                                                    {
							                                                                        $recb['p_ph1']='';
							                                                                        $recb['p_ph1'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);                                                                                        
							                                                                    }
							                                                                    echo $recb['p_ph1'];
							                                                                    } ?>">
							                                                  <!--   <div class="btn-group">
																				    <button type="button"  onclick="javascript: ClicktoCall(document.getElementById('p_ph1').value);" class="btn btn-success btn-xs" style="padding:1px">Call</button>
																				    <button type="button" onclick="javascript: ClicktoSMS(document.getElementById('p_ph1').value);"  class="btn btn-success btn-xs" style="padding:1px">Sms</button>
																				 </div>		-->					                                               		
							                                            <!--        <a href="javascript: ClicktoCall(document.getElementById('p_ph1').value);"><img src ="buyer_details_img/ClicktoCall.gif" style="width:25px; height:25px;" /></a>
							                                     				<a href="javascript: ClicktoSMS(document.getElementById('p_ph1').value);"><img src ="buyer_details_img/ClicktoSMS.gif" style="width:25px; height:25px;"></img></a>-->
							                                				</center>
																		</div>
							                                		</div>
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
							                                					<label style="width:30%;text-align:left" for="p_ph2"><a href="javascript: ClicktoPhone(document.getElementById('p_ph2').value);">Phone2</a></label>
									                                			
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%;padding:0px" type="text" name="p_ph2" id="p_ph2"   value="<?php if (isset($_POST['p_ph2'])) {
							                                                                    $str = preg_replace("/[^0-9]*/s", "",$_POST['p_ph2']);
							                                                                    if ($str=='')
							                                                                        $_POST['p_ph2']='';
							                                                                    else
							                                                                    {
							                                                                        $_POST['p_ph2']='';
							                                                                        $_POST['p_ph2'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);
							                                                                    }
							                                                                    echo $_POST['p_ph2'];
							                                                                } else if (isset($recb['p_ph2'])) {
							                                                                    $str = preg_replace("/[^0-9]*/s", "",$recb['p_ph2']);
							                                                                     if ($str=='')
							                                                                        $recb['p_ph2']='';
							                                                                    else
							                                                                    {
							                                                                        $recb['p_ph2']='';
							                                                                        $recb['p_ph2'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);                                                                                        
							                                                                    }
							                                                                    echo $recb['p_ph2'];
							                                                                    } ?>">
							                                                   <!-- <div class="btn-group">
																				    <button type="button"  onclick="javascript: ClicktoCall(document.getElementById('p_ph2').value);" class="btn btn-success btn-xs" style="padding:1px">Call</button>
																				    <button type="button" onclick="javascript: ClicktoSMS(document.getElementById('p_ph2').value);"  class="btn btn-success btn-xs" style="padding:1px">Sms</button>
																				 </div>-->
							                                                                    
							                                                  <!--  <a href="javascript: ClicktoCall(document.getElementById('p_ph2').value);"><img src ="buyer_details_img/ClicktoCall.gif" style="width:25px; height:25px;" /></a>
							                                     				<a href="javascript: ClicktoSMS(document.getElementById('p_ph2').value);"><img src ="buyer_details_img/ClicktoSMS.gif" style="width:25px; height:25px;"></img></a>-->
							                                				</center>
																		</div>
							                                		</div>
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="priority_opt">Priority</label>
																				<select class="form-control  my-form-control" style="display:inline-block;width:65%" name="priority_opt" id="priority_opt" size="1">
																					<option value="New" <?php 
																						if ($_POST['priority_opt'] == "New") {
																								echo 'selected';
																						} else if ($recb['priority_opt'] == "New") {
																							echo 'selected';
																						} ?>>New</option>
																					<option value="Opened Emails" <?php if ($_POST['priority_opt'] == "Opened Emails") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Opened Emails") {
																							echo 'selected';
																						} ?>>Opened Emails</option>
																						
																					<option value="Clickthroughs" <?php if ($_POST['priority_opt'] == "Clickthroughs") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Clickthroughs") {
																							echo 'selected';
																						} ?>>Clickthroughs</option>
																					<option value="Retry" 
																						<?php if ($_POST['priority_opt'] == "Retry") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Retry") {
																							echo 'selected';
																						} ?>>Retry</option>

																					<option value="Warm" <?php if ($_POST['priority_opt'] == "Warm") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Warm") {
																							echo 'selected';
																						} ?>>Warm</option>
																					<option value="Hot" <?php if ($_POST['priority_opt'] == "Hot") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Hot") {
																							echo 'selected';
																						} ?>>Hot</option>
																					<option value="Credit Check" <?php if ($_POST['priority_opt'] == "Credit Check") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Credit Check") {
																							echo 'selected';
																						}?>>Credit Check</option>
																					<option value="Bad Credit" <?php if ($_POST['priority_opt'] == "Bad Credit") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Bad Credit") {
																							echo 'selected';
																						}?>>Bad Credit</option>
																					<option value="Credit Repair" <?php if ($_POST['priority_opt'] == "Credit Repair") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Credit Repair") {
																							echo 'selected';
																						} ?>>Credit Repair</option>
																					<option value="Credit Ready" <?php if ($_POST['priority_opt'] == "Credit Ready") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Credit Ready") {
																							echo 'selected';
																						} ?>>Credit Ready</option>
																					<option value="Pre-Approved" <?php if ($_POST['priority_opt'] == "Pre-Approved") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Pre-Approved") {
																							echo 'selected';
																						} ?>>Pre-Approved</option>
																					<option value="Doc. Sent" <?php if ($_POST['priority_opt'] == "Doc. Sent") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Doc. Sent") {
																							echo 'selected';
																						} ?>>Doc. Sent</option>
																					<option value="Pending Funding" <?php if ($_POST['priority_opt'] == "Pending Funding") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Pending Funding") {
																							echo 'selected';
																						} ?>>Pending Funding</option>

																					<option value="Funded" <?php if ($_POST['priority_opt'] == "Funded") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Funded") {
																							echo 'selected';
																						} ?>>Funded</option>

																					<option value="Fee Pending" <?php if ($_POST['priority_opt'] == "Fee Pending") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Fee Pending") {
																							echo 'selected';
																						} ?>>Fee Pending</option>
																					<option value="Clients" <?php if ($_POST['priority_opt'] == "Clients") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Clients") {
																							echo 'selected';
																						} ?>>Clients</option>
																					<option value="Partners" <?php if ($_POST['priority_opt'] == "Partners") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Partners") {
																							echo 'selected';
																						} ?>>Partners</option>
																					<option value="Inactive" <?php if ($_POST['priority_opt'] == "Inactive") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Inactive") {
																							echo 'selected';
																						} ?>>Inactive</option>
																					<option value="Not Interested" <?php if ($_POST['priority_opt'] == "Not Interested") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Not Interested") {
																							echo 'selected';
																						} ?>>Not Interested</option>

																					<option value="Delete" <?php if ($_POST['priority_opt'] == "Delete") {
																							echo 'selected';
																						} else if ($recb['priority_opt'] == "Delete") {
																							echo 'selected';
																						} ?>>Delete</option>
																				</select>                                					
																			</center>
																		</div>
																	</div>			                      		
								                                	</div>  
																	<div class="row">
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p_eml1"><a href="javascript: ClicktoEmail(document.getElementById('p_eml1').value);">Email1</a></label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p_eml1" id="p_eml1"  value="<?php if (isset($_POST['p_eml1'])) {
							                                                            echo $_POST['p_eml1'];
							                                                        } else if (isset($recb['p_eml1'])) {
							                                                            echo $recb['p_eml1'];
							                                                        } ?>">  
							                                                   
							                                                    <!--<button type="button"  onclick="javascript: ClicktoEmail(document.getElementById('p_eml1').value);" class="btn btn-success btn-xs" style="padding:1px">Email</button>    -->
							                                                   
							                                                  <!--  <a href="javascript: ClicktoEmail(document.getElementById('p_eml1').value);" target="_blank">
							                                                    	<img src ="buyer_details_img/ClicktoEmail.gif" style="width:25px; height:25px;">
							                                                    </a>  -->
							                                				</center>
																		</div>
							                                		</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                		
									                                			<label style="width:30%;text-align:left" for="p_eml2"><a href="javascript: ClicktoEmail(document.getElementById('p_eml2').value);">Email2</a></label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p_eml2" id="p_eml2"  value="<?php if (isset($_POST['p_eml2'])) {
							                                                            echo $_POST['p_eml2'];
							                                                        } else if (isset($recb['p_eml2'])) {
							                                                            echo $recb['p_eml2'];
							                                                        } ?>">    
							                                                    
							                                                <!--    <button type="button" onclick="javascript: ClicktoEmail(document.getElementById('p_eml2').value);" class="btn btn-success btn-xs" style="padding:1px">Email</button>    -->
							                                                    
							                                                    
							                                                    <!--<a href="javascript: ClicktoEmail(document.getElementById('p_eml2').value);" target="_blank">
							                                                    	<img src ="buyer_details_img/ClicktoEmail.gif" style="width:25px; height:25px;">
							                                                    </a>  -->
							                                				</center>
																		</div>
							                                		</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:60%;text-align:left" for="bst_time_to_call"> Best time to Contact</label>
																				<select size="1" class="form-control  my-form-control" style="display:inline-block;width:35%"  name="bst_time_to_call" >
																					<option value="">Select Time</option>
																					<option <?php if ($recb['bst_time_to_call'] == '7AM') { ?> selected <?php } ?> value="7AM">7AM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '8AM') { ?> selected <?php } ?> value="8AM">8AM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '9AM') { ?> selected <?php } ?> value="9AM">9AM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '10AM') { ?> selected <?php } ?> value="10AM">10AM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '11AM') { ?> selected <?php } ?> value="11AM">11AM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '12PM') { ?> selected <?php } ?> value="12PM">12PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '1PM') { ?> selected <?php } ?> value="1PM">1PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '2PM') { ?> selected <?php } ?> value="2PM">2PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '3PM') { ?> selected <?php } ?> value="3PM">3PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '4PM') { ?> selected <?php } ?> value="4PM">4PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '5PM') { ?> selected <?php } ?> value="5PM">5PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '6PM') { ?> selected <?php } ?> value="6PM">6PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '7PM') { ?> selected <?php } ?> value="7PM">7PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '8PM') { ?> selected <?php } ?> value="8PM">8PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '9PM') { ?> selected <?php } ?> value="9PM">9PM</option>
																					<option <?php if ($recb['bst_time_to_call'] == '10PM') { ?> selected <?php } ?> value="10PM">10PM</option>
																				</select>
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:40%;text-align:left" for="funding_dt">Funding Time</label>		                                			
																				<input class="form-control  my-form-control" style="display:inline-block;width:55%" type="text" name="funding_dt" id="funding_dt2"  value="<?php if (isset($_POST['funding_dt'])and ($_POST['funding_dt']!='0000-00-00')) {
																					echo $_POST['funding_dt'];
																					} else if (isset($recb['funding_dt'])and ($recb['funding_dt']!='0000-00-00')) {
																						echo $recb['funding_dt'];
																					} ?>" onFocus='popUpCalendar(this, document.default_emplate.funding_dt, "mm/dd/yyyy")'>                                                                                                        
																			</center>
																		</div>
																	</div>       		
							                                	</div>   
																	<div class="row">
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p_dob">DOB</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" onFocus='popUpCalendar(this, document.default_emplate.p_dob, "mm/dd/yyyy")' name="p_dob" id="p_dob"  value="<?php if (isset($_POST['p_dob'])) {
																						echo $_POST['p_dob'];
																					} else if (isset($recb['p_dob'])) {
																						echo $recb['p_dob'];
																					} ?>">          					
																			</center>
																		</div>
																	</div>                                		
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p_ss">SS#</label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p_ss" id="p_ss"  value="<?php if (isset($_POST['p_ss'])) {
							                                                            echo $_POST['p_ss'];
							                                                        } else if (isset($recb['p_ss'])) {
							                                                            echo $recb['p_ss'];
							                                                        } ?>">        
							                                				</center>
																		</div>
							                                		</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:40%;text-align:left" for="p1_cred_usr">Credit Login</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:55%" type="text" name="p1_cred_usr" id="p1_cred_usr"  value="<?php if (isset($_POST['p1_cred_usr'])) {
																						echo $_POST['p1_cred_usr'];
																					} else if (isset($recb['p1_cred_usr'])) {
																						echo $recb['p1_cred_usr'];
																						} ?>">  
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p1_cred_pwd">Password</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" type="text" name="p1_cred_pwd" id="p1_cred_pwd" value="<?php if (isset($_POST['p1_cred_pwd'])) {
																						echo $_POST['p1_cred_pwd'];
																					} else if (isset($recb['p1_cred_pwd'])) {
																						echo $recb['p1_cred_pwd'];
																					} ?>">   					
																			</center>
																		</div>
																	</div>
							                                	</div> 
																	<div class="row">                                		
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
								                                			<div class="form-group">
								                                				<center>
										                                			<label style="width:30%;text-align:left" for="p_hm_addr">Address</label>
										                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p_hm_addr" id="p_hm_addr"  value="<?php if (isset($_POST['p_hm_addr'])) {
								                                                                echo $_POST['p_hm_addr'];
								                                                            } else if (isset($recb['p_hm_addr'])) {
								                                                                echo $recb['p_hm_addr'];
								                                                            } ?>">     
								                                				</center>
																			</div>
								                                		</div>
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
								                                			<div class="form-group">
								                                				<center>
										                                			<label style="width:30%;text-align:left" for="p_city">City</label>
										                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p_city" id="p_city"  value="<?php if (isset($_POST['p_city'])) {
								                                                                echo $_POST['p_city'];
								                                                            } else if (isset($recb['p_city'])) {
								                                                                echo $recb['p_city'];
								                                                            } ?>">
								                                				</center>
																			</div>
								                                		</div>
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
								                                			<div class="form-group">
								                                				<center>
										                                			<label style="width:40%;text-align:left" for="p_state">State</label>
										                                			<input class="form-control  my-form-control" style="display:inline-block;width:55%"type="text" name="p_state" id="p_state"  value="<?php if (isset($_POST['p_state'])) {
								                                                            echo $_POST['p_state'];
								                                                        } else if (isset($recb['p_state'])) {
								                                                            echo $recb['p_state'];
								                                                        } ?>">
								                                                </center>	                                                   
																			</div>
								                                		</div>                    
								                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
								                                			<div class="form-group">
								                                				<center>
										                                			<label style="width:30%;text-align:left" for="p_zip">Zip</label>
										                                			   <input class="form-control  my-form-control" style="display:inline-block;width:65%"type="text" name="p_zip" id="p_zip"  value="<?php if (isset($_POST['p_zip'])) {
								                                                            echo $_POST['p_zip'];
								                                                        } else if (isset($recb['p_zip'])) {
								                                                            echo $recb['p_zip'];
								                                                        } ?>">		
								                                				</center>
																			</div>
								                                		</div>                                		            		
								                                	</div>			                                	     
																</div>
															</div>														      				  										
					  									</div>
			<?php /*if ($_REQUEST['action'] != 'add') {
				if (isset($recb['p_psn2']) and ($recb['p_psn2'] !=""))
				{*/
				?>
														<div class="panel panel-info" id="person2_panel">
															<div class="panel-heading">
													         	<h4 class="panel-title">
															        <a data-toggle="collapse" data-target="#collapsePerson2" 
															           href="#collapsePerson2" class="collapsed">Person2</a>
															    </h4>
													      	</div>
													      	<div id="collapsePerson2" class="panel-collapse collapse">
													      		<div class="panel-body">
						  											<div class="row">
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																	<div class="form-group">
																		<center>
																			<label style="width:30%;text-align:left" for="p_psn2">Person2</label>
																			<input type="text" class="form-control  my-form-control" style="display:inline-block;width:65%" name="p_psn2" id="p_psn2"  value="<?php if (isset($_POST['p_psn2'])) {
																							echo $_POST['p_psn2'];
																						} else if (isset($recb['p_psn2'])) {
																							echo $recb['p_psn2'];
																						} ?>">
																		</center>
																	</div>
																</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																	<div class="form-group">
																		<center>
																			<label style="width:30%;text-align:left" for="p2_ph1"><a href="javascript: ClicktoPhone(document.getElementById('p2_ph1').value);">Phone</a></label>
																			<input class="form-control  my-form-control" style="display:inline-block;width:65%;padding:0px" type="text" name="p2_ph1" id="p2_ph1"   value="<?php if (isset($_POST['p2_ph1'])) {
																							$str = preg_replace("/[^0-9]*/s", "",$_POST['p2_ph1']);
																							if ($str=='')
																								$_POST['p2_ph1']='';
																							else
																							{
																								$_POST['p2_ph1']='';
																								$_POST['p2_ph1'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);
																							}
																							echo $_POST['p2_ph1'];
																						} else if (isset($recb['p2_ph1'])) {
																							$str = preg_replace("/[^0-9]*/s", "",$recb['p2_ph1']);
																							 if ($str=='')
																								$recb['p2_ph1']='';
																							else
																							{
																								$recb['p2_ph1']='';
																								$recb['p2_ph1'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);                                                                                        
																							}
																							echo $recb['p2_ph1'];
																							} ?>">
																			<!--<div class="btn-group">
																				<button type="button"  onclick="javascript: ClicktoCall(document.getElementById('p2_ph1').value);" class="btn btn-success btn-xs" style="padding:1px">Call</button>
																				<button type="button" onclick="javascript: ClicktoSMS(document.getElementById('p2_ph1').value);"  class="btn btn-success btn-xs" style="padding:1px">Sms</button>
																			</div>-->
																				 
																			<!--<a href="javascript: ClicktoCall(document.getElementById('p2_ph1').value);"><img src ="buyer_details_img/ClicktoCall.gif" style="width:25px; height:25px;" /></a>
																			<a href="javascript: ClicktoSMS(document.getElementById('p2_ph1').value);"><img src ="buyer_details_img/ClicktoSMS.gif" style="width:25px; height:25px;"></img></a>-->
																		</center>
																	</div>
																</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																	<div class="form-group">
																		<center>
																			<label style="width:30%;text-align:left" for="p2_eml"><a href="javascript: ClicktoEmail(document.getElementById('p2_eml').value);">Email</a></label>
																			
																			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p2_eml" id="p2_eml"  value="<?php if (isset($_POST['p2_eml'])) {
																					echo $_POST['p2_eml'];
																				} else if (isset($recb['p2_eml'])) {
																					echo $recb['p2_eml'];
																				} ?>">      
																			
																		<!--	<a href="javascript: ClicktoEmail(document.getElementById('p2_eml').value);" target="_blank">
																				<img src ="buyer_details_img/ClicktoEmail.gif" style="width:25px; height:25px;">
																			</a>  -->
																		</center>
																	</div>
																</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																	<div class="form-group">
																		<center>
																			<label style="width:40%;text-align:left" for="p2_relation">Relationship</label>
																			<input class="form-control  my-form-control" style="display:inline-block;width:55%" type="text" name="p2_relation" id="p2_relation"  value="<?php if (isset($_POST['p2_relation'])) {
																					echo $_POST['p2_relation'];
																				} else if (isset($recb['p2_relation'])) {
																					echo $recb['p2_relation'];
																				} ?>">                                                         
																		</center>
																	</div>
																</div>                  		
							                                	</div> 
									  								<div class="row">
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p2_dob">DOB</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" onFocus='popUpCalendar(this, document.default_emplate.p2_dob, "mm/dd/yyyy")' name="p2_dob" id="p2_dob"  value="<?php if (isset($_POST['p2_dob'])) {
																						echo $_POST['p2_dob'];
																					} else if (isset($recb['p2_dob'])) {
																						echo $recb['p2_dob'];
																					} ?>">          					
																			</center>
																		</div>
																	</div>                             
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p2_ss">SS#</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p2_ss" id="p2_ss"  value="<?php if (isset($_POST['p2_ss'])) {
																						echo $_POST['p2_ss'];
																					} else if (isset($recb['p2_ss'])) {
																						echo $recb['p2_ss'];
																					} ?>">        
																			</center>
																		</div>
																	</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:40%;text-align:left" for="p2_cred_usr">Credit Login</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:55%" type="text" name="p2_cred_usr" id="p2_cred_usr"  value="<?php if (isset($_POST['p2_cred_usr'])) {
																						echo $_POST['p2_cred_usr'];
																					} else if (isset($recb['p2_cred_usr'])) {
																						echo $recb['p2_cred_usr'];
																						} ?>">  
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p2_cred_pwd">Password</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" type="text" name="p2_cred_pwd" id="p2_cred_pwd" value="<?php if (isset($_POST['p2_cred_pwd'])) {
																						echo $_POST['p2_cred_pwd'];
																					} else if (isset($recb['p2_cred_pwd'])) {
																						echo $recb['p2_cred_pwd'];
																					} ?>">   					
																			</center>
																		</div>
																	</div>                       
							                                	</div>          
								                                	<div class="row">
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p2_hm_addr">Address</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p2_hm_addr" id="p2_hm_addr"  value="<?php if (isset($_POST['p2_hm_addr'])) {
																							echo $_POST['p2_hm_addr'];
																						} else if (isset($recb['p2_hm_addr'])) {
																							echo $recb['p2_hm_addr'];
																						} ?>">     
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p2_city">City</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p2_city" id="p2_city"  value="<?php if (isset($_POST['p2_city'])) {
																							echo $_POST['p2_city'];
																						} else if (isset($recb['p2_city'])) {
																							echo $recb['p2_city'];
																						} ?>">
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:40%;text-align:left" for="p2_state">State</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:55%"type="text" name="p2_state" id="p2_state"  value="<?php if (isset($_POST['p2_state'])) {
																						echo $_POST['p2_state'];
																					} else if (isset($recb['p2_state'])) {
																						echo $recb['p2_state'];
																					} ?>">	
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p2_zip">Zip</label>
																					<input class="form-control  my-form-control" style="display:inline-block;width:65%"type="text" name="p2_zip" id="p2_zip"  value="<?php if (isset($_POST['p2_zip'])) {
																						echo $_POST['p2_zip'];
																					} else if (isset($recb['p2_zip'])) {
																						echo $recb['p2_zip'];
																					} ?>">		
																			</center>
																		</div>
																	</div>
																	
																	  
							                                	</div>												
																</div>
															</div>	
					  									</div>
					  									<div class="panel panel-info" id="person3_panel">
					  										<div class="panel-heading">
													         	<h4 class="panel-title">
															        <a data-toggle="collapse" data-target="#collapsePerson3" 
															           href="#collapsePerson3" class="collapsed">Person3</a>
															    </h4>
													      	</div>
													      	<div id="collapsePerson3" class="panel-collapse collapse">
					  											<div class="panel-body">
					  											<div class="row">
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p_psn3">Person3</label>
																				<input type="text" class="form-control  my-form-control" style="display:inline-block;width:65%" name="p_psn3" id="p_psn3"  value="<?php if (isset($_POST['p_psn3'])) {
																								echo $_POST['p_psn3'];
																							} else if (isset($recb['p_psn3'])) {
																								echo $recb['p_psn3'];
																							} ?>">
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p3_ph1"><a href="javascript: ClicktoPhone(document.getElementById('p3_ph1').value);">Phone</a></label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%;padding:0px" type="text" name="p3_ph1" id="p3_ph1"   value="<?php if (isset($_POST['p3_ph1'])) {
																								$str = preg_replace("/[^0-9]*/s", "",$_POST['p3_ph1']);
																								if ($str=='')
																									$_POST['p3_ph1']='';
																								else
																								{
																									$_POST['p3_ph1']='';
																									$_POST['p3_ph1'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);
																								}
																								echo $_POST['p3_ph1'];
																							} else if (isset($recb['p3_ph1'])) {
																								$str = preg_replace("/[^0-9]*/s", "",$recb['p3_ph1']);
																								 if ($str=='')
																									$recb['p3_ph1']='';
																								else
																								{
																									$recb['p3_ph1']='';
																									$recb['p3_ph1'] = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);                                                                                        
																								}
																								echo $recb['p3_ph1'];
																								} ?>">
																				
																				<!--<div class="btn-group">
																					<button type="button"  onclick="javascript: ClicktoCall(document.getElementById('p3_ph1').value);" class="btn btn-success btn-xs" style="padding:1px">Call</button>
																					<button type="button" onclick="javascript: ClicktoSMS(document.getElementById('p3_ph1').value);"  class="btn btn-success btn-xs" style="padding:1px">Sms</button>
																				</div>-->
																				<!--<a href="javascript: ClicktoCall(document.getElementById('p3_ph1').value);"><img src ="buyer_details_img/ClicktoCall.gif" style="width:25px; height:25px;" /></a>
																				<a href="javascript: ClicktoSMS(document.getElementById('p3_ph1').value);"><img src ="buyer_details_img/ClicktoSMS.gif" style="width:25px; height:25px;"></img></a>-->
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				
																				<label style="width:30%;text-align:left" for="p3_eml"><a href="javascript: ClicktoEmail(document.getElementById('p3_eml').value);">Email</a></label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p3_eml" id="p3_eml"  value="<?php if (isset($_POST['p3_eml'])) {
																						echo $_POST['p3_eml'];
																					} else if (isset($recb['p3_eml'])) {
																						echo $recb['p3_eml'];
																					} ?>">      
																				
																				<!--<a href="javascript: ClicktoEmail(document.getElementById('p3_eml').value);" target="_blank">
																					<img src ="buyer_details_img/ClicktoEmail.gif" style="width:25px; height:25px;">
																				</a>  -->
																			</center>
																		</div>
																	</div>
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:40%;text-align:left" for="p3_relation">Relationship</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:55%" type="text" name="p3_relation" id="p3_relation"  value="<?php if (isset($_POST['p3_relation'])) {
																						echo $_POST['p3_relation'];
																					} else if (isset($recb['p3_relation'])) {
																						echo $recb['p3_relation'];
																					} ?>">                                                         
																			</center>
																		</div>
																	</div>
																</div>
					  											<div class="row">
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p3_dob">DOB</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" onFocus='popUpCalendar(this, document.default_emplate.p3_dob, "mm/dd/yyyy")' name="p3_dob" id="p3_dob"  value="<?php if (isset($_POST['p3_dob'])) {
																						echo $_POST['p3_dob'];
																					} else if (isset($recb['p3_dob'])) {
																						echo $recb['p3_dob'];
																					} ?>">          					
																			</center>
																		</div>
																	</div>    
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p3_ss">SS#</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p3_ss" id="p3_ss"  value="<?php if (isset($_POST['p3_ss'])) {
																						echo $_POST['p3_ss'];
																					} else if (isset($recb['p3_ss'])) {
																						echo $recb['p3_ss'];
																					} ?>">        
																			</center>
																		</div>
																	</div> 
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:40%;text-align:left" for="p3_cred_usr">Credit Login</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:55%" type="text" name="p3_cred_usr" id="p3_cred_usr"  value="<?php if (isset($_POST['p3_cred_usr'])) {
																						echo $_POST['p3_cred_usr'];
																					} else if (isset($recb['p3_cred_usr'])) {
																						echo $recb['p3_cred_usr'];
																						} ?>">  
																			</center>
																		</div>
																	</div> 
																	<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
																		<div class="form-group">
																			<center>
																				<label style="width:30%;text-align:left" for="p3_cred_pwd">Password</label>
																				<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" type="text" name="p3_cred_pwd" id="p3_cred_pwd" value="<?php if (isset($_POST['p3_cred_pwd'])) {
																						echo $_POST['p3_cred_pwd'];
																					} else if (isset($recb['p3_cred_pwd'])) {
																						echo $recb['p3_cred_pwd'];
																					} ?>">   					
																			</center>
																		</div>
																	</div>                                  
																</div>
							                                	<div class="row">                                		
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p3_hm_addr">Address</label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p3_hm_addr" id="p3_hm_addr"  value="<?php if (isset($_POST['p3_hm_addr'])) {
							                                                                echo $_POST['p3_hm_addr'];
							                                                            } else if (isset($recb['p3_hm_addr'])) {
							                                                                echo $recb['p3_hm_addr'];
							                                                            } ?>">     
							                                				</center>
																		</div>
							                                		</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p3_city">City</label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%" type="text" name="p3_city" id="p3_city"  value="<?php if (isset($_POST['p3_city'])) {
							                                                                echo $_POST['p3_city'];
							                                                            } else if (isset($recb['p3_city'])) {
							                                                                echo $recb['p3_city'];
							                                                            } ?>">
							                                				</center>
																		</div>
							                                		</div>
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:40%;text-align:left" for="p3_state">State</label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:55%"type="text" name="p3_state" id="p3_state"  value="<?php if (isset($_POST['p3_state'])) {
							                                                            echo $_POST['p3_state'];
							                                                        } else if (isset($recb['p3_state'])) {
							                                                            echo $recb['p3_state'];
							                                                        } ?>">
							                                                </center>	                                                   
																		</div>
							                                		</div>                    
							                                		<div class="col-sm-3" style="padding-left:0px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<center>
									                                			<label style="width:30%;text-align:left" for="p3_zip">Zip</label>
									                                			<input class="form-control  my-form-control" style="display:inline-block;width:65%"type="text" name="p3_zip" id="p3_zip"  value="<?php if (isset($_POST['p3_zip'])) {
							                                                            echo $_POST['p3_zip'];
							                                                        } else if (isset($recb['p3_zip'])) {
							                                                            echo $recb['p3_zip'];
							                                                        } ?>">		
							                                				</center>
																		</div>
							                                		</div>                                		            		
							                                	</div>       			                                	    
					  										</div>
															</div>
					  									</div>
			<?php	
				//}
			?>
					  									
					  									<div class="panel panel-default" id="remark_panel">
					  										<div class="panel-body">
					  											<div class="row">
							                                		<div class="col-sm-12" style="padding-left:5px;padding-right:0px;">
							                                			<div class="form-group">
							                                				<label for="remarks">Remarks:</label>
							                                				<textarea class="form-control my-textarea-control" style="padding:0px;background:#F9F8C2;" rows="2"  name="remarks" id="remarks" ><?php                                                                             		if (isset($_POST['remarks'])) {
							                                                        echo $_POST['remarks'];
							                                                    } else if (isset($recb['remarks'])) {
							                                                        echo $recb['remarks'];
							                                                    } ?></textarea>		                                			                                				
																		</div>
							                                		</div>
							                                		<div class="col-sm-9">
							                                		</div>
							                                	</div> 
							                                </div>
					  									</div>
					  									
				                                	</div>
			<?php if ($_REQUEST['action'] != 'add') { ?>    


													<div class="panel panel-info">
												    	<div class="panel-heading">Conversation</div>
												      	<div class="panel-body">
												      		<div class="table-responsive">         
												    			<table class="table table-striped my-table-font">
											    					<thead>
																		<tr >
																	        <th style="width:10%">Log Time</th>
																	        <th style="width:15%">Performed By</th>
																	        <th style="width:25%">Next Follow up</th>
																	        <th>Out come</th>													        
																	    </tr>
																    </thead>
																    <tbody>
																    <?php
					                                                	$seeavail = "select a.customer_id,b.auto_id,b.customer_id,b.log_time,b.log_subject,b.spoke_to,b.out_come,b.next_follow_up from customer_info a,conversation_log_info b where a.customer_id=b.customer_id and b.customer_id='" . $_GET['rid'] . "' order by auto_id desc";
					                                                    $seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");
					                                                    if (mysql_num_rows($seeres) == '0') {
					                                                ?>
					                                                	<tr>
					                                                    	<td colspan="4" align="center">
					                                                        	No Activity found
					                                                        </td>
					                                                    </tr>
					                                                <?php
					                                                	} else {
					                                                		while ($seerec = mysql_fetch_assoc($seeres)) {
					                                                        	if ($seerec['next_follow_up'] != "")
																				{
																					$follow_up = explode("-", $seerec['next_follow_up']);
																					$seerec['next_follow_up'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  			
																				}
					                                                    ?>
					                                                    		<tr >
					                                                            	<td style="width:10%"><?php if ($seerec['log_time'] != '') echo $seerec['log_time'];
					                                                                    	else echo "&nbsp;"; ?></td>
					                                                                <td style="width:15%"><?php if ($seerec['spoke_to'] != '') echo $seerec['spoke_to'];
					                                                                    	else echo "&nbsp;"; ?></td>
					                                                                <td style="width:15%"> <?php if (($seerec['next_follow_up'] != '') and ($seerec['next_follow_up'] != '00/00/0000')) echo $seerec['next_follow_up'];
					                                                                    	else echo "&nbsp;"; ?></td>
					                                                               	<td><?php if ($seerec['out_come'] != '') echo stripslashes($seerec['out_come']);
					                                                                    	else echo "&nbsp;"; ?></td>
					                                                            </tr> 
					                                                 <?php
					                                                   		}
					                                                   }
					                                                 ?>													     
																    </tbody>
											    				</table>
												    		</div>
															<div class="row">
																<div class="col-sm-6" >
						                                			<div class="form-group">
						                                				<label for="out_come">Outcome</label>		                                				
						                                                <textarea name="out_come" class="form-control my-textarea-control" style="padding:0px;" rows="5" ><?php if (isset($_POST['out_come'])) {
									                                               echo $_POST['out_come'];
									                                        } ?></textarea>                                			                                				
																	</div>
						                                		</div>
																<div class="col-sm-2" >
						                                			<div class="form-group">
						                                				<label  for="spoke_to">Performed By</label>
								                                		<input type="text" name="spoke_to" class="form-control  my-form-control"  id="spoke_to"  value="<?php if (isset($_POST['spoke_to'])) {
							                                                	echo $_POST['spoke_to'];} ?>">
							                                        </div>
						                                		</div>
						                                		<div class="col-sm-3" >
						                                			<div class="form-group">
						                                				<label  for="next_follow_up">Next Follow Up Date</label>
							                                			 <input type="text" name="next_follow_up"  class="form-control  my-form-control"  id="next_follow_up"  value="<?php if (isset($_POST['next_follow_up'])) {
						                                                        echo $_POST['next_follow_up'];
						                                                    } ?>" onFocus='popUpCalendar(this, document.default_emplate.next_follow_up, "mm/dd/yyyy")'>
																	</div>
						                                		</div>
						                                		<div class="col-sm-1"></div>
						                                	</div>
						                                </div>
												    </div>
				<?php } ?>                  
				<?php if ($_REQUEST['action'] != 'add') {
				    $change = "Change";
				} else {
				    $change = "Add";
				} ?>                           
												</div>   
												<div class="tab-pane fade" id="financial_tab">
													<h3>Financial Information</h3>				                                	
				                                	<div class="row" style="padding-bottom:5px;">
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-8 my-form-control-left-text">Legal Business Name</div>
				                                				<div class="col-sm-4" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control" name="b_leg_nm" id="b_leg_nm"  value="<?php if (isset($_POST['b_leg_nm'])) {
					                                                        echo $_POST['b_leg_nm'];
					                                                    } else if (isset($recb['b_leg_nm'])) {
					                                                        echo $recb['b_leg_nm'];
					                                                        } ?>">           
				                                				</div>
															</div>
				                                		</div>
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-7 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Doing Business As</div>
				                                				<div class="col-sm-5" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control" name="b_ind_typ" id="b_ind_typ"  value="<?php if (isset($_POST['b_ind_typ'])) {
				                                                            echo $_POST['b_ind_typ'];
				                                                        } else if (isset($recb['b_ind_typ'])) {
				                                                            echo $recb['b_ind_typ'];
				                                                            } ?>">      
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-7 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Physical Address</div>
				                                				<div class="col-sm-5" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control"    name="b_addr" id="b_addr" value="<?php if (isset($_POST['b_addr'])) {
						                                                    echo $_POST['b_addr'];
						                                                } else if (isset($recb['b_addr'])) {
						                                                    echo $recb['b_addr'];
						                                                    } ?>">          
				                                				</div>
															</div>
				                                		</div>                                		         		
				                                	</div> 
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5  my-form-control-left-text" style="padding-left:2px;padding-right:2px;">City</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control"  name="b_city" id="b_city" value="<?php if (isset($_POST['b_city'])) {
				                                                            echo $_POST['b_city'];
				                                                        } else if (isset($recb['b_city'])) {
				                                                            echo $recb['b_city'];
				                                                            } ?>">       
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">State</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control"   name="b_state" id="b_state" value="<?php if (isset($_POST['b_state'])) {
							                                                echo $_POST['b_state'];
							                                            } else if (isset($recb['b_state'])) {
							                                                echo $recb['b_state'];
							                                                } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Zip</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control"  name="b_zip" id="b_zip" value="<?php if (isset($_POST['b_zip'])) {
				                                                            echo $_POST['b_zip'];
				                                                        } else if (isset($recb['b_zip'])) {
				                                                            echo $recb['b_zip'];
				                                                            } ?>">           
				                                				</div>
															</div>
				                                		</div>       		
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Federal Tax ID</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text"  class="form-control my-form-control" name="b_fed_tax_id" id="b_fed_tax_id"  value="<?php if (isset($_POST['b_fed_tax_id'])) {
				                                                                        echo $_POST['b_fed_tax_id'];
				                                                                    } else if (isset($recb['b_fed_tax_id'])) {
				                                                                        echo $recb['b_fed_tax_id'];
				                                                                        } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Telephone No</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text"  class="form-control my-form-control" name="b_ph" id="b_ph"  value="<?php if (isset($_POST['b_ph'])) {
				                                                                        echo $_POST['b_ph'];
				                                                                    } else if (isset($recb['b_ph'])) {
				                                                                        echo $recb['b_ph'];
				                                                                        } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Own/Lease</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
																      <input type="radio"  value="Own" id="b_own_lease" <?php 
				                                			 			if (isset($_POST['b_own_lease']) and ($_POST['b_own_lease'] == "Own")) {
				                                                    		echo 'checked';
				                                                		} else if (isset($recb['b_own_lease']) and ($recb['b_own_lease'] == "Own")) {
				                                                    		echo 'checked';
				                                                    	}?> name="b_own_lease">Own                         					
																    </label>
				                                					<label class="radio-inline">
																      <input type="radio"  value="Lease" id="b_own_lease" <?php 
				                                			 			if (isset($_POST['b_own_lease']) and ($_POST['b_own_lease'] == "Lease")) {
				                                                    		echo 'checked';
				                                                		} else if (isset($recb['b_own_lease']) and ($recb['b_own_lease'] == "Lease")) {
				                                                    		echo 'checked';
				                                                    	}?> name="b_own_lease">Lease              					
																    </label>
				                         							
				                                				</div>
															</div>
				                                		</div>       		
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Facsimile No</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text"  class="form-control my-form-control" name="b_facs_no" id="b_facs_no"  value="<?php if (isset($_POST['b_facs_no'])) {
					                                                        echo $_POST['b_facs_no'];
					                                                    } else if (isset($recb['b_facs_no'])) {
					                                                        echo $recb['b_facs_no'];
					                                                        } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">E-Mail</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					 <input type="text"  class="form-control my-form-control" name="b_eml" id="b_eml"  value="<?php if (isset($_POST['b_eml'])) {
				                                                            echo $_POST['b_eml'];
				                                                        } else if (isset($recb['b_eml'])) {
				                                                            echo $recb['b_eml'];
				                                                            } ?>">            
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Accountant</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<input type="text" class="form-control my-form-control" name="b_acc" id="b_acc"  value="<?php if (isset($_POST['b_acc'])) {
					                                                        echo $_POST['b_acc'];
					                                                    } else if (isset($recb['b_acc'])) {
					                                                        echo $recb['b_acc'];
					                                                        } ?>">           
				                                				</div>
															</div>
				                                		</div>       		
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-3" >
				                                			<div class="form-group">      
				                                				<div class="col-sm-12" style="padding-left:2px;padding-right:2px;">Legal Entity Type</div>
				                                			</div>
				                                		</div>
				                                		<div class="col-sm-3" >
				                                			<div class="form-group">      
				                                				<div class="col-sm-12" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
					                                					<input type="radio" id="b_ent_typ"  name="b_ent_typ" 
					                                    			 		<?php 
					                                    			 			if (isset($_POST['b_ent_typ']) and ($_POST['b_ent_typ'] == "Corporation")) {
					                                                        		echo 'checked';
					                                                    		} else if (isset($recb['b_ent_typ']) and ($recb['b_ent_typ'] == "Corporation")) {
					                                                        		echo 'checked';
					                                                        }?> value="Corporation"/>Corporation
					                                                </label>
				                                				</div>
				                                			</div>
				                                		</div>
				                                		<div class="col-sm-6" >
				                                			<div class="form-group">      
				                                				<div class="col-sm-12" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
					                                					<input  type="radio" id="b_ent_typ"   name="b_ent_typ" 
					                             							<?php 
					                                    			 			if (isset($_POST['b_ent_typ']) and ($_POST['b_ent_typ'] == "Limited Liability Company")) {
					                                                        		echo 'checked';
					                                                    		} else if (isset($recb['b_ent_typ']) and ($recb['b_ent_typ'] == "Limited Liability Company")) {
					                                                        		echo 'checked';
					                                                        }?> value="Limited Liability Company"/>Limited Liability Company   					
					                                                </label>
				                                				</div>
				                                			</div>
				                                		</div>				                                		
				                                	</div>
				                                	<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-3" >
				                                			
				                                		</div>
				                                		<div class="col-sm-3" >
				                                			<div class="form-group">      
				                                				<div class="col-sm-12" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
					                                					<input  type="radio" id="b_ent_typ"  name="b_ent_typ"  
				                                 							<?php 
				                                        			 			if (isset($_POST['b_ent_typ']) and ($_POST['b_ent_typ'] == "General Partnership")) {
				                                                            		echo 'checked';
				                                                        		} else if (isset($recb['b_ent_typ']) and ($recb['b_ent_typ'] == "General Partnership")) {
				                                                            		echo 'checked';
				                                                            }?> value="General Partnership"/>General Partnership			
					                                                </label>
				                                				</div>
				                                			</div>
				                                		</div>
				                                		<div class="col-sm-3" >
				                                			<div class="form-group">      
				                                				<div class="col-sm-12" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
					                                					<input  type="radio" id="b_ent_typ"  name="b_ent_typ"  
				                                 							<?php 
				                                        			 			if (isset($_POST['b_ent_typ']) and ($_POST['b_ent_typ'] == "Limited Partnership")) {
				                                                            		echo 'checked';
				                                                        		} else if (isset($recb['b_ent_typ']) and ($recb['b_ent_typ'] == "Limited Partnership")) {
				                                                            		echo 'checked';
				                                                            }?>value="Limited Partnership"/>Limited Partnership               
					                                                </label>
				                                				</div>
				                                			</div>
				                                		</div>		
				                                		<div class="col-sm-3" >
				                                			<div class="form-group">      
				                                				<div class="col-sm-12" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
					                                					<input type="radio" id="b_ent_typ"  name="b_ent_typ" <?php 
				                                        			 			if (isset($_POST['b_ent_typ']) and ($_POST['b_ent_typ'] == "Sole Proprictorship")) {
				                                                            		echo 'checked';
				                                                        		} else if (isset($recb['b_ent_typ']) and ($recb['b_ent_typ'] == "Sole Proprictorship")) {
				                                                            		echo 'checked';
				                                                            }?> value="Sole Proprictorship"/>Sole Proprictorship     		
					                                                </label>
				                                				</div>
				                                			</div>
				                                		</div>				                                		
				                                	</div>
													<div class="row" style="padding-bottom:5px;">
				                                		<div class="col-sm-6" >
				                                			<div class="form-group">
				                                				<div class="col-sm-8 my-form-control-left-text" style="padding-left:2px;padding-right:2px;"># of years under the Current Management</div>
				                                				<div class="col-sm-4" style="padding-left:2px;padding-right:2px;">
				                                					 <input class="form-control-with-text my-form-control" name="b_ye_busi" id="b_ye_busi"  value="<?php if (isset($_POST['b_ye_busi'])) {
				                                                            echo $_POST['b_ye_busi'];
				                                                        } else if (isset($recb['b_ye_busi'])) {
				                                                            echo $recb['b_ye_busi'];
				                                                            } ?>"/>&nbsp;years</div>    												
															</div>
				                                		</div>
				                                		<div class="col-sm-6" >
				                                			<div class="form-group">
				                                				<div class="col-sm-7 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">State of Incorporation/Organization</div>
				                                				<div class="col-sm-5" style="padding-left:2px;padding-right:2px;">
				                                					<input class="form-control my-form-control" type="text" value="<?php if (isset($_POST['b_reg_state'])) {
					                                                        echo $_POST['b_reg_state'];
					                                                    } else if (isset($recb['b_reg_state'])) {
					                                                        echo $recb['b_reg_state'];
					                                                        } ?>">         
				                                				</div>
															</div>
				                                		</div>                                       		                 		         		
				                                	</div> 
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-5" >
				                                			<div class="form-group">
				                                				<div class="col-sm-8 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Type/Description of Business:</div>
				                                				<div class="col-sm-4" style="padding-left:2px;padding-right:2px;">
				                                					  <input class="form-control my-form-control" type="text"  name="b_type"  id="b_type" value="<?php if (isset($_POST['b_type'])) {
				                                                                        echo $_POST['b_type'];
				                                                                    } else if (isset($recb['b_type'])) {
				                                                                        echo $recb['b_type'];
				                                                                        } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-7" >
				                                			<div class="form-group">
				                                				<div class="col-sm-6 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Additional Location Address if Any</div>
				                                				<div class="col-sm-6" style="padding-left:2px;padding-right:2px;">
				                                					<input class="form-control my-form-control" type="text"  name="b_addition_addr"  id="b_addition_addr" value="<?php if (isset($_POST['b_addition_addr'])) {
				                                                                        echo $_POST['b_addition_addr'];
				                                                                    } else if (isset($recb['b_addition_addr'])) {
				                                                                        echo $recb['b_addition_addr'];
				                                                                        } ?>">                  
				                                				</div>
															</div>
				                                		</div>       
				                                			
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-7 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Landlord/Mortgage Ce</div>
				                                				<div class="col-sm-5" style="padding-left:2px;padding-right:2px;">
				                                					  <input class="form-control my-form-control" type="text"   name="b_landlord"  id="b_landlord" value="<?php if (isset($_POST['b_landlord'])) {
				                                                                        echo $_POST['b_landlord'];
				                                                                    } else if (isset($recb['b_landlord'])) {
				                                                                        echo $recb['b_landlord'];
				                                                                        } ?>">        
																</div>
															</div>
				                                		</div>       	
														<div class="col-sm-3" >
				                                			<div class="form-group">
				                                				<div class="col-sm-6 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Telephone No</div>
				                                				<div class="col-sm-6" style="padding-left:2px;padding-right:2px;">
				                                					  <input class="form-control my-form-control" type="text"   name="b_landlord_ph"  id="b_landlord_ph" value="<?php if (isset($_POST['b_landlord_ph'])) {
				                                                            echo $_POST['b_landlord_ph'];
				                                                        } else if (isset($recb['b_landlord_ph'])) {
				                                                            echo $recb['b_landlord_ph'];
				                                                            } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-5" >
				                                			<div class="form-group">
				                                				<div class="col-sm-4 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Current Term</div>
				                                				<div class="col-sm-2 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">From</div>
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;"><input class="form-control my-form-control" type="text"    name="b_landlord_fr" id="b_landlord_fr" value="<?php if (isset($_POST['b_landlord_fr'])) {
				                                                        echo $_POST['b_landlord_fr'];
				                                                    } else if (isset($recb['b_landlord_fr'])) {
				                                                        echo $recb['b_landlord_fr'];
				                                                        } ?>"></div>
				                                                <div class="col-sm-2 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">&nbsp;&nbsp;To</div>
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;"><input class="form-control my-form-control" type="text"    name="b_landlord_to" id="b_landlord_to" value="<?php if (isset($_POST['b_landlord_to'])) {
				                                                            echo $_POST['b_landlord_to'];
				                                                        } else if (isset($recb['b_landlord_to'])) {
				                                                            echo $recb['b_landlord_to'];
				                                                            } ?>">           		                                                              
				                                				</div>
															</div>
				                                		</div>                                       		
													</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Monthly Pmt $</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					  <input class="form-control my-form-control" type="text"    name="b_landlord_month_pmt" id="b_landlord_month_pmt" value="<?php if (isset($_POST['b_landlord_month_pmt'])) {
				                                                            echo $_POST['b_landlord_month_pmt'];
				                                                        } else if (isset($recb['b_landlord_month_pmt'])) {
				                                                            echo $recb['b_landlord_month_pmt'];
				                                                            } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-8" >
				                                			<div class="form-group">
				                                				<div class="col-sm-4 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Option to Renew</div>
				                                				<div class="col-sm-2 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">#of Options</div>
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;"><input class="form-control my-form-control"  type="text"    name="b_landlord_renew_opt" id="b_landlord_renew_opt" value="<?php if (isset($_POST['b_landlord_renew_opt'])) {
				                                                            echo $_POST['b_landlord_renew_opt'];
				                                                        } else if (isset($recb['b_landlord_renew_opt'])) {
				                                                            echo $recb['b_landlord_renew_opt'];
				                                                            } ?>"></div>       
				                                                <div class="col-sm-2 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">&nbsp;&nbsp;&nbsp;Years</div>                         				
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;"><input class="form-control my-form-control" type="text"     name="b_landlord_renew_ye" id="b_landlord_renew_ye" value="<?php if (isset($_POST['b_landlord_renew_ye'])) {
				                                                            echo $_POST['b_landlord_renew_ye'];
				                                                        } else if (isset($recb['b_landlord_renew_ye'])) {
				                                                            echo $recb['b_landlord_renew_ye'];
				                                                            } ?>"></div>
															</div>
				                                		</div>       
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Payment Current</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
																     <input type="radio" id="b_landlord_payment"  name="b_landlord_payment" <?php 
				                                    			 			if (isset($_POST['b_landlord_payment']) and ($_POST['b_landlord_payment'] == "Yes")) {
				                                                        		echo 'checked';
				                                                    		} else if (isset($recb['b_landlord_payment']) and ($recb['b_landlord_payment'] == "Yes")) {
				                                                        		echo 'checked';
				                                                        	}?> value="Yes"/>Yes             					
																    </label>
				                                					<label class="radio-inline">
																      <input  type="radio" id="b_landlord_payment"  name="b_landlord_payment" <?php 
				                                    			 			if (isset($_POST['b_landlord_payment']) and ($_POST['b_landlord_payment'] == "No")) {
				                                                        		echo 'checked';
				                                                    		} else if (isset($recb['b_landlord_payment']) and ($recb['b_landlord_payment'] == "No")) {
				                                                        		echo 'checked';
				                                                        	}?> value="No"/>No		
																    </label>
				                         							
				                                				</div>
															</div>
				                                		</div>      
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-7 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Approx Square Footage</div>
				                                				<div class="col-sm-5" style="padding-left:2px;padding-right:2px;">
				                                					 <input class="form-control my-form-control" type="text"    name="b_empl" id="b_empl" value="<?php if (isset($_POST['b_empl'])) {
				                                                            echo $_POST['b_empl'];
				                                                        } else if (isset($recb['b_empl'])) {
				                                                            echo $recb['b_empl'];
				                                                            } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;"># of Employees</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">
				                                					 <input class="form-control my-form-control" type="text"    name="b_empl" id="b_empl" value="<?php if (isset($_POST['b_empl'])) {
				                                                            echo $_POST['b_empl'];
				                                                        } else if (isset($recb['b_empl'])) {
				                                                            echo $recb['b_empl'];
				                                                            } ?>">           
				                                				</div>
															</div>
				                                		</div>       
				                                		 		
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-8" >
				                                			<div class="form-group">
				                                				<div class="col-sm-6 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Average Monthly Sales Info</div>
				                                				<div class="col-sm-2 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">#Cash/Check:</div>
				                                				<div class="col-sm-4" style="padding-left:2px;padding-right:2px;">$&nbsp;<input class="form-control-with-text my-form-control" type="text"  name="b_cash" id="b_cash" value="<?php if (isset($_POST['b_cash'])) {
							                                                                        echo $_POST['b_cash'];
							                                                                    } else if (isset($recb['b_cash'])) {
							                                                                        echo $recb['b_cash'];
							                                                                        } ?>">,000.00</div>
															</div>
				                                		</div>       
				                                		<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Amex</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">$&nbsp;<input class="form-control-with-text my-form-control"  type="text"  name="b_amex" id="b_amex" value="<?php if (isset($_POST['b_amex'])) {
				                                                                        echo $_POST['b_amex'];
				                                                                    } else if (isset($recb['b_amex'])) {
				                                                                        echo $recb['b_amex'];
				                                                                        } ?>">,000.00</div>                                				
															</div>
				                                		</div>       
				                                		
				                                	</div>
													<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-8" >
				                                			<div class="form-group">
				                                				<div class="col-sm-6" style="padding-left:2px;padding-right:2px;">(Round to the nearest thousands)</div>
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;">VS/MC:</div>
				                                				<div class="col-sm-4" style="padding-left:2px;padding-right:2px;">$&nbsp;<input  class="form-control-with-text my-form-control" type="text"   name="b_vs_mc" id="b_vs_mc" value="<?php if (isset($_POST['b_vs_mc'])) {
				                                                            echo $_POST['b_vs_mc'];
				                                                        } else if (isset($recb['b_vs_mc'])) {
				                                                            echo $recb['b_vs_mc'];
				                                                            } ?>">,000.00</div>                                 				                                				
															</div>
				                                		</div>       
														<div class="col-sm-4" >
				                                			<div class="form-group">
				                                				<div class="col-sm-5" style="padding-left:2px;padding-right:2px;">Other</div>
				                                				<div class="col-sm-7" style="padding-left:2px;padding-right:2px;">$&nbsp;<input class="form-control-with-text my-form-control"  type="text"  name="b_other" id="b_other" value="<?php if (isset($_POST['b_other'])) {
				                                                                        echo $_POST['b_other'];
				                                                                    } else if (isset($recb['b_other'])) {
				                                                                        echo $recb['b_other'];
				                                                                        } ?>">,000.00</div>                                				
															</div>
				                                		</div>				                                		 		
				                                	</div>
				                                	<div class="row"  style="padding-bottom:5px;">
														<div class="col-sm-6" >
				                                			<div class="form-group">
				                                				<div class="col-sm-8 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Is Your Business Seasonal? </div>
				                                				<div class="col-sm-4" style="padding-left:2px;padding-right:2px;">
				                                					<label class="radio-inline">
																      <input type="radio" id="b_is_season"  <?php 
						                                                    			 			if (isset($_POST['b_is_season']) and ($_POST['b_is_season'] == "Yes")) {
						                                                                        		echo 'checked';
						                                                                    		} else if (isset($recb['b_is_season']) and ($recb['b_is_season'] == "Yes")) {
						                                                                        		echo 'checked';
						                                                                        }?> name="b_is_season" value="Yes">Yes                  					
																    </label>
				                                					<label class="radio-inline">
																      <input type="radio" id="b_is_season"  name="b_is_season" <?php 
						                                                    			 			if (isset($_POST['b_is_season']) and ($_POST['b_is_season'] == "No")) {
						                                                                        		echo 'checked';
						                                                                    		} else if (isset($recb['b_is_season']) and ($recb['b_is_season'] == "No")) {
						                                                                        		echo 'checked';
						                                                                        }?> value="No">No            					
																    </label>
				                         							
				                                				</div>
															</div>
				                                		</div>    
														<div class="col-sm-6" >
				                                			<div class="form-group">
				                                				<div class="col-sm-7 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">Month High Season Begins and Ends</div>
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;">
				                                					 <input class="form-control my-form-control" type="text"    name="b_month_season_begin" id="b_month_season_begin" value="<?php if (isset($_POST['b_month_season_begin'])) {
				                                                            echo $_POST['b_month_season_begin'];
				                                                        } else if (isset($recb['b_month_season_begin'])) {
				                                                            echo $recb['b_month_season_begin'];
				                                                        } ?>">
				                                				</div>
				                                				<div class="col-sm-1 my-form-control-left-text" style="padding-left:2px;padding-right:2px;">&nbsp;&nbsp;to</div>
				                                				<div class="col-sm-2" style="padding-left:2px;padding-right:2px;">
				                                					<input class="form-control my-form-control" type="text"    name="b_month_season_to" id="b_month_season_to" value="<?php if (isset($_POST['b_month_season_to'])) {
				                                                            echo $_POST['b_month_season_to'];
				                                                        } else if (isset($recb['b_month_season_to'])) {
				                                                            echo $recb['b_month_season_to'];
				                                                        } ?>">         		    
				                                				</div>
															</div>
				                                		</div>                     		 		
				                                	</div>
				                                	
												</div>
				                            	<div class="tab-pane fade" id="funding_tab">
				                            		<h3>Funding Information</h3>				                                	
				                                	<div class="row" style="margin:5px">
														<div class="table-responsive">
															<table class="table" style="width:460px;margin-left:5px;margin-bottom:2px"  cellspacing="0" cellpadding="0" >
											    				<thead>
																	<tr>
																		<th style="padding:1px;width:70px;text-align:center">Card Name</th>
										                               	<th style="padding:1px;width:50px;text-align:center" >Card Number</th>
										                               	<th style="padding:1px;width:50px;text-align:center" >Expiration<br>Date</th>
										                               	<th style="padding:1px;width:30px;text-align:center" >Security<br>Code</th>
										                               	<th style="padding:1px;width:30px;text-align:center" >Approved<br>Limit</th>
										                               	<th style="padding:1px;width:30px;text-align:center" >Increased<br>Limit</th>
										                                <th style="padding:1px;width:30px;text-align:center" >Cash<br>Advance</th>
										                                <th style="padding:1px;width:70px;text-align:center" >Issuing Bank</th>
										                            	<th style="padding:1px;width:50px;text-align:center" >Bank Phone</th>
										                                <th style="padding:1px;width:50px;text-align:center" >Bank Website</th>
																	</tr>
																</thead>
																<tbody>
						<?php 
																   	 /* ------------------ Funding info  ----------------------------- */
																   	 if (($_REQUEST['action'] != 'add') and ($_GET['rid']!=""))
																   	 	$sql_sel = sprintf("select * from funding_info where customer_id = '%s'",$_GET['rid']);
																   	 else
																   	 	$sql_sel = sprintf("select * from funding_info where 1=0");
																	 $res_sel = mysql_query($sql_sel) or die(mysql_error());	
																	 $sum_appr_lim =0;
																	 $sum_incr_lim =0;
																	 $sum_cash_adv =0;
																	 $i=0;
																	 while (1) 
																	 {			
																	   $response = mysql_fetch_assoc($res_sel);
																	   if ($i>25 and $response == false)
																	   	break;
																	   if ($response!=false)
																	   {
																		    /* convert yyyy-mm-dd to mm/dd/yyyy*/
																		   if ((int)(preg_replace("/[^0-9]*/s", "",$response['expire_dt'])) == 0)
																			  $response['expire_dt']="";


																		   if ($response['expire_dt'] != "")
																		   {
																			  $follow_up = explode("-", $response['expire_dt']);
																		      $response['expire_dt'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  			
																		   }	
																		    $sum_appr_lim += (float) $response['appr_lim'];
																	 		$sum_incr_lim +=(float) $response['incr_lim'];
																	 		$sum_cash_adv +=(float) $response['cash_adv'];
																	   }	  	  	
																  	  
				?>
																   	  	<tr>
																	   	   	<td style="padding:1px;width:70px;" align="center">
																	   	  	    <input type="text" size="25" maxlength="30" name="fun_cd_name[]" id="fun_cd_name[]" value="<?php if ($response and (isset($response['cd_name'])))  echo $response['cd_name'];  else   echo ''; ?>"/>	                                                                                	
				                                                            </td>
				                                                            <td style="padding:1px;width:50px;" align="center">
																	   	  	   <input type="text"  size="10" maxlength="12" name="fun_cd_number[]" id="fun_cd_number[]" value="<?php 
																	   	  	 	if ($response and isset($response['cd_number'])) {
				                                                                	echo $response['cd_number'];
				                                                             	}else {
				                                                                        echo '';
				                                                                }?>"/>	                                                                       	
				                                                            </td>
				                                                            <td style="padding:1px;width:50px;" align="center">
										                                   	 	<input type="text" size="9" maxlength="12"  name="fun_expire_dt[]" id="fun_expire_dt[]" onFocus='popUpCalendar(this, this, "mm/dd/yyyy")' value="<?php 
																	   	  	 	if (($response) and isset($response['expire_dt'])) {
				                                                                	echo $response['expire_dt'];
				                                                             	}else {
				                                                                        echo '';
				                                                                }?>"/>	      
										                                 	</td>
																	   	  	<td style="padding:1px;width:30px;"  align="center"> 
																	   	 	 	 <input type="text"  size="4" name="fun_secu_code[]" id="fun_secu_code[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['secu_code'])) {
				                                                                    echo $response['secu_code'];
				                                                                 }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>
																	   	  	<td style="padding:1px;width:30px;" align="center"> 
																	   	 	 	 <input type="text"  size="7" maxlength="9" name="fun_appr_lim[]" id="fun_appr_lim[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['appr_lim']) and ($response['appr_lim']!=0)) {
				                                                                    echo $response['appr_lim'];
				                                                                 }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>																	   	  	
																			<td style="padding:1px;width:30px;" align="center"> 
																	   	 	 	 <input type="text"  size="7" maxlength="9" name="fun_incr_lim[]" id="fun_incr_lim[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['incr_lim'])and ($response['incr_lim']!=0)) {
				                                                                    echo $response['incr_lim'];
				                                                                 }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>				
																	   	  
																	   	  	<td style="padding:1px;width:30px;" align="center"> 
																	   	 	 	 <input type="text"  size="7" maxlength="9" name="fun_cash_adv[]" id="fun_cash_adv[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['cash_adv']) and ($response['cash_adv']!=0)) {
				                                                                    echo $response['cash_adv'];
				                                                                 }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>				
																	   	  	
																	   	  	<td style="padding:1px;width:70px"   align="center"> 
																	   	 	 	 <input type="text" size="25" maxlength="30"  name="fun_issu_bnk[]" id="fun_issu_bnk[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['issu_bnk'])) {
				                                                                    echo $response['issu_bnk'];
				                                                                 }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>
																	   	  	<td style="padding:1px;width:50px;" align="center"> 
																	   	 	 	 <input type="text" size="8" maxlength="10"  name="fun_bnk_ph[]" id="fun_bnk_ph[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['bnk_ph'])) {
				                                                                    echo $response['bnk_ph'];
				                                                                 }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>
																	   	  	<td style="padding:1px;width:50px" align="center"> 
																	   	 	 	 <input type="text" size="20" name="fun_bnk_web[]" id="fun_bnk_web[]" value="<?php 
																	   	  	 	 if (($response!=false) and isset($response['bnk_web'])) {
				                                                                    echo $response['bnk_web'];
				                                                                }else {
				                                                                        echo '';
				                                                                  }?>"/>	 
																	   	  	</td>																   	 
																	   	  	
																   	  	</tr>
																	  <?php
																	  	$i++;
																	  }
																  ?>	
																  <tr>
																		<td style="padding:1px;widtd:70px;text-align:center">Total</td>
										                               	<td style="padding:1px;width:50px;text-align:center" ></td>
										                               	<td style="padding:1px;width:50px;text-align:center" ></td>
										                               	<td style="padding:1px;width:30px;text-align:center" ></td>
										                               	<td style="padding:1px;width:30px;text-align:center" ><?php if ($sum_appr_lim==0) echo ''; else echo $sum_appr_lim;?></td>
										                               	<td style="padding:1px;width:30px;text-align:center" ><?php if ($sum_incr_lim==0) echo ''; else echo $sum_incr_lim;?></td>
										                                <td style="padding:1px;width:30px;text-align:center" ><?php if ($sum_cash_adv==0) echo ''; else echo $sum_cash_adv;?></td>
										                                <td style="padding:1px;width:70px;text-align:center" ></td>
										                            	<td style="padding:1px;width:50px;text-align:center" ></td>
										                                <td style="padding:1px;width:50px;text-align:center" ></td>
																</tr>
																</tbody>
															</table>
														
														</div>
													</div>
												</div>
												<div class="tab-pane fade" id="opportunity_tab">
													<h3>Opportunity Information</h3>				                                	
				                                	<div class="row" style="margin:5px">
														<div class="table-responsive">
															<table class="table" style="width:1168px;margin-bottom:2px">
											    				<thead>
																	<tr>
																		<th style="padding:1px;text-align:center" >Opportunity</th>
										                               	<th style="padding:1px;text-align:center">Yes/No</th>
										                               	<th style="padding:1px;text-align:center">Referal<br>CompanyName</th>
										                               	<th style="padding:1px;text-align:center">Referal<br>PersonName</th>
										                               	<th style="padding:1px;text-align:center">Phone 1</th>
										                               	<th style="padding:1px;text-align:center">Phone 2</th>
										                                <th style="padding:1px;text-align:center">Best Email</th>
										                                <th style="padding:1px;text-align:center">Fee Amount</th>
										                            	<th style="padding:1px;text-align:center">Date Paid</th>
										                                <th style="padding:1px;text-align:center">Notes</th>
																	</tr>
																</thead>
																<tbody>
																<?php 												

																   	 if (($_REQUEST['action'] != 'add') and ($_GET['rid']!=""))
																   	 	$sql_sel = sprintf("select * from opportunity_info where customer_id = '%s'",$_GET['rid']);
																   	 else
																   	 	$sql_sel = sprintf("select * from opportunity_info where 1=0");
																	 $res_sel = mysql_query($sql_sel) or die(mysql_error());	
																	
																	
																	 $i=0;
																	 while ($i<40) 
																	 {			
																	   $response = mysql_fetch_assoc($res_sel);
																	   if ($response!=false)
																	   {
																		    /* convert yyyy-mm-dd to mm/dd/yyyy*/
																		   if ((int)(preg_replace("/[^0-9]*/s", "",$response['date_paid'])) == 0)
																			  $response['date_paid']="";


																		   if ($response['date_paid'] != "")
																		   {
																			  $follow_up = explode("-", $response['date_paid']);
																		      $response['date_paid'] = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  			
																		   }	
																		   
																		   /* convert phone type */
																		   if (isset($response['phone1']))
																				$response['phone1'] = preg_replace("/[^0-9]*/s", "",$response['phone1']);
																		   if (isset($response['phone2']))
																				$response['phone2'] = preg_replace("/[^0-9]*/s", "",$response['phone2']);		
																	   }			  	  
																	?>
																   	  	<tr>
																	   	   	<td style="padding:1px;" align="center">
																	   	  	    <input type="text"  style="padding:0px; <?php if (isset($response) and isset($response['opportunity']) and $response['opportunity']!='') echo 'border:0px;';?>" name="op_opportunity[]" id="op_opportunity[]" value="<?php if ($response!=false){	if (isset($response['opportunity'])) { echo $response['opportunity']; } else  { echo ''; }	}else	echo $opportuntiy_name_ary[$i]; 	 	?>"/>
				                                                            </td>
				                                                            <td style="padding:1px;" align="center">
																	   	  	   <select size="1"  style="padding-left:0px;padding-right:0px;padding-bottom:4px;"  name="op_yes_no[]" id="op_yes_no[]">                                                                                    
																	   	  	    	<option <?php if (($response!=false) and $response['yes_no'] == '') { ?> selected <?php } ?> value=""></option>
				                                                                    <option <?php if (($response!=false) and $response['yes_no'] == 'Yes') { ?> selected <?php } ?> value="Yes">Yes</option>
				                                                                    <option <?php if (($response!=false) and $response['yes_no'] == 'No') { ?> selected <?php } ?> value="No">No</option>	                                                                                    
				                                                            	</select>                                                                  	
				                                                            </td>
				                                                            <td style="padding:1px;" align="center">
										                                   	 	<input type="text"  style="padding:0px"  name="op_ref_comp_nm[]" id="op_ref_comp_nm[]" value="<?php 
																		   	  	 	if (($response!=false) and isset($response['referal_company_name'])) {
				                                                                    	echo $response['referal_company_name'];
				                                                                 	}else {
				                                                                            echo '';
				                                                                    }?>"/>	       
										                                 	</td>
																	   	  	<td style="padding:1px;"  align="center"> 
																	   	 	 	<input type="text"   style="padding:0px" name="op_ref_pers_nm[]" id="op_ref_pers_nm[]" value="<?php 
																		   	  	 	 if (($response!=false) and isset($response['referal_person_name'])) {
				                                                                        echo $response['referal_person_name'];
				                                                                     }else {
				                                                                            echo '';
				                                                            		}?>"/>	 
																	   	  	</td>
																	   	  	<td style="padding:1px;" align="center"> 
																	   	  		<div class="form-group" style="margin-bottom:0px">
																		   	 	 
																					<?php 
					                                                            		if ((($response ==false) and ($opportuntiy_name_ary[$i] !="")) or ((isset($response['opportunity']) and $response['opportunity']!="") or (isset($_POST['opportunity']) and $_POST['opportunity']!="")))
					                                                            		{?>
					                                                            			<input type="text"  style="display:inline-block;width:70%;padding:0px" name="op_ph1[]" id="op_ph1[]" value="<?php 
																						   	  	 if (($response!=false) and isset($response['phone1'])) {
							                                                                       	echo $response['phone1'];
							                                                                     }else {
							                                                                     	echo '';
							                                                                     }?>"/>	 
							                                                                <div class="btn-group">
																							    <button type="button"  onclick="javascript: ClicktoCall((document.getElementsByName('op_ph1[]'))['<?php echo $i?>'].value);" class="btn btn-success btn-xs" style="padding:1px">Call</button>
																							    <button type="button" onclick="javascript: ClicktoSMS((document.getElementsByName('op_ph1[]'))['<?php echo $i?>'].value);"  class="btn btn-success btn-xs" style="padding:1px">Sms</button>
																							</div>	
																				 
					                                                            			<!--<a href="javascript: ClicktoCall((document.getElementsByName('op_ph1[]'))['<?php echo $i?>'].value);"><img src ="buyer_details_img/ClicktoCall.gif" style="width:25px; height:25px;" /></a>		
																							<a href="javascript: ClicktoSMS((document.getElementsByName('op_ph1[]'))['<?php echo $i?>'].value);"><img src ="buyer_details_img/ClicktoSMS.gif" style="width:25px; height:25px;"></img></a>-->
																					<?php
																						}else
																						{?>
																							<input type="text"  style="display:inline-block;padding:0px" name="op_ph1[]" id="op_ph1[]" value="<?php 
																						   	  	 if (($response!=false) and isset($response['phone1'])) {
							                                                                       	echo $response['phone1'];
							                                                                     }else {
							                                                                     	echo '';
							                                                                     }?>"/>	 
							                                                        <?php	
																						}
																					?>
																				</div>
																	   	  	</td>																	   	  	
																			<td style="padding:1px;" align="center"> 
																				<div class="form-group" style="margin-bottom:0px">
																		   	 	 	
					                                                                <?php 
					                                                            		if ((($response ==false) and ($opportuntiy_name_ary[$i] !="")) or ((isset($response['opportunity']) and $response['opportunity']!="") or (isset($_POST['opportunity']) and $_POST['opportunity']!="")))
					                                                            		{?>
					                                                            			<input type="text"  style="display:inline-block;width:70%;padding:0px" name="op_ph2[]" id="op_ph2[]" value="<?php 
																					   	  	 	 if (($response!=false) and isset($response['phone2'])) {
							                                                                        echo $response['phone2'];
							                                                                      } else  {
							                                                                            echo '';
							                                                                    }?>"/>	 
							                                                                <div class="btn-group">
																							    <button type="button"  onclick="javascript: ClicktoCall((document.getElementsByName('op_ph2[]'))['<?php echo $i?>'].value);" class="btn btn-success btn-xs" style="padding:1px">Call</button>
																							    <button type="button" onclick="javascript: ClicktoSMS((document.getElementsByName('op_ph2[]'))['<?php echo $i?>'].value);"  class="btn btn-success btn-xs" style="padding:1px">Sms</button>
																							</div>	
																							
					                                                            			<!--<a href="javascript: ClicktoCall((document.getElementsByName('op_ph2[]'))['<?php echo $i?>'].value);"><img src ="buyer_details_img/ClicktoCall.gif" style="width:25px; height:25px;" /></a>		
																							<a href="javascript: ClicktoSMS((document.getElementsByName('op_ph2[]'))['<?php echo $i?>'].value);"><img src ="buyer_details_img/ClicktoSMS.gif" style="width:25px; height:25px;"></img></a>-->
																						<?php
																						}else{?>
																							<input type="text"  style="display:inline-block;padding:0px" name="op_ph2[]" id="op_ph2[]" value="<?php 
																					   	  	 	 if (($response!=false) and isset($response['phone2'])) {
							                                                                        echo $response['phone2'];
							                                                                      } else  {
							                                                                            echo '';
							                                                                    }?>"/>	 
							                                                        <?php 	
																						}
																					?>
																				</div>
																	   	  	</td>				
																	   	  
																	   	  	<td style="padding:1px;" align="center"> 
																	   	  		<div class="form-group" style="margin-bottom:0px">
																		   	 		
								                                                    <?php 
					                                                            		if ((($response ==false) and ($opportuntiy_name_ary[$i] !="")) or ((isset($response['opportunity']) and $response['opportunity']!="") or (isset($_POST['opportunity']) and $_POST['opportunity']!="")))
					                                                            		{?>
					                                                            			<input type="text"  style="display:inline-block;width:80%;padding:0px" name="op_bst_eml[]" id="op_bst_eml[]" value="<?php 
																						   		if (($response!=false) and isset($response['best_email'])) {
										                                                        	echo $response['best_email'];
										                                                        }else{
										                                                        	echo '';
										                                                        }?>"/>	 
										                                                    
										                                        				<button type="button" onclick="javascript: ClicktoEmail((document.getElementsByName('op_bst_eml[]'))['<?php echo $i?>'].value);" class="btn btn-success btn-xs" style="padding:1px">Email</button>    
																								
																				
					                                                            		<!--	<a href="javascript: ClicktoEmail((document.getElementsByName('op_bst_eml[]'))['<?php echo $i?>'].value);"><img src ="buyer_details_img/ClicktoEmail.gif" style="width:25px; height:25px;" /></a>   -->
																					<?php
																						}else
																						{?>
																							<input type="text"  style="display:inline-block;padding:0px" name="op_bst_eml[]" id="op_bst_eml[]" value="<?php 
																						   		if (($response!=false) and isset($response['best_email'])) {
										                                                        	echo $response['best_email'];
										                                                        }else{
										                                                        	echo '';
										                                                        }?>"/>	 
										                                            <?php
																						}
																					?>  
																				</div>  
																	   	  	</td>				
																	   	  	
																	   	  	<td style="padding:1px;"   align="center"> 
																	   	 	 	<input type="text"  style="padding:0px"  name="op_fee_amt[]" id="op_fee_amt[]" value="<?php 
																		   	  	 	 if (($response!=false) and isset($response['fee_amount']) and ($response['fee_amount']!=0)) {
				                                                                        echo $response['fee_amount'];
				                                                                     }else{
				                                                                            echo '';
				                                                                     }?>"/>	       
																	   	  	</td>
																	   	  	<td style="padding:1px;" align="center"> 
																	   	 		<input type="text"  style="padding:0px" name="op_date_paid[]" id="op_date_paid[]" onFocus='popUpCalendar(this, this, "mm/dd/yyyy")'  value="<?php 
																		   	  	 	 if (($response!=false) and isset($response['date_paid'])) {
				                                                                        echo $response['date_paid'];
				                                                                     }else{
				                                                                        echo '';
				                                                                     }?>"/>	      
																	   	  	</td>
																	   	  	<td style="padding:1px;" align="center"> 
																	   	 	 	<input type="text"  style="padding:0px" name="op_notes[]" id="op_notes[]" value="<?php 
																		   	  		if (($response!=false) and isset($response['notes'])) {
				                                                                        echo $response['notes'];
				                                                                    }else {
				                                                                            echo '';
				                                                                    }?>"/>	 
																	   	  	</td>															   	  	
																   	  	</tr>
																	  <?php
																	  	$i++;
																	  }
																  ?>	
																</tbody>
															</table>									
														</div>
													</div>
												</div>
				                                <div class="tab-pane fade" id="log_tab">
				                                	<h3>History Information</h3>				                                	
				                                	<div class="panel panel-info">
												    	<div class="panel-heading">Call Log</div>
												      	<div class="panel-body">
												      		<?php 
															   	 /* ------------------ Call logs  ----------------------------- */
																$phone = '+1'.$_SESSION['google_voice_ph'];
																$cur_login_time = $_SESSION['cur_login_time'];
																 
																if (isset($_POST['p_ph1']))
																{
																	$tmp_p_ph1 = preg_replace("/[^0-9]*/s", "",$_POST['p_ph1']);
																	$tmp_p_ph2 = preg_replace("/[^0-9]*/s", "",$_POST['p_ph2']);
																	$tmp_p2_ph1 =preg_replace("/[^0-9]*/s", "",$_POST['p2_ph1']);
																	$tmp_p2_ph2 = preg_replace("/[^0-9]*/s", "",$_POST['p2_ph2']);
																	$tmp_p3_ph1 = preg_replace("/[^0-9]*/s", "",$_POST['p3_ph1']);
																	$tmp_p3_ph2 = preg_replace("/[^0-9]*/s", "",$_POST['p3_ph2']);
																}else{
																	$tmp_p_ph1 = preg_replace("/[^0-9]*/s", "",$recb['p_ph1']);
																	$tmp_p_ph2 = preg_replace("/[^0-9]*/s", "",$recb['p_ph2']);
																	$tmp_p2_ph1 = preg_replace("/[^0-9]*/s", "",$recb['p2_ph1']);
																	$tmp_p2_ph2 = preg_replace("/[^0-9]*/s", "",$recb['p2_ph2']);
																	$tmp_p3_ph1 = preg_replace("/[^0-9]*/s", "",$recb['p3_ph1']);
																	$tmp_p3_ph2 = preg_replace("/[^0-9]*/s", "",$recb['p3_ph2']);																			
																}
																if ($tmp_p_ph1 != "")
																	$tmp_p_ph1 = "+1".$tmp_p_ph1;
																if ($tmp_p_ph2 != "")
																	$tmp_p_ph2 = "+1".$tmp_p_ph2;
																if ($tmp_p2_ph1 != "")
																	$tmp_p2_ph1 = "+1".$tmp_p2_ph1;
																if ($tmp_p2_ph2 != "")
																	$tmp_p2_ph2 = "+1".$tmp_p2_ph2;
																if ($tmp_p3_ph1 != "")
																	$tmp_p3_ph1 = "+1".$tmp_p3_ph1;
																if ($tmp_p3_ph2 != "")
																	$tmp_p3_ph2 = "+1".$tmp_p3_ph2;
																
																
																// Have a call
																
																if ($_SESSION['user_group'] == "Admin")
																	$phone="%";
																
																$sql_sel="select * from call_log_info where  ((from_phone like ('".$phone."')) and (to_phone like ('".$tmp_p_ph1."') or to_phone like ('".$tmp_p_ph2."') or to_phone like ('".$tmp_p2_ph1."') or to_phone like ('".$tmp_p2_ph2."') or to_phone like ('".$tmp_p3_ph1."') or to_phone like ('".$tmp_p3_ph2."'))) or ((to_phone like ('".$phone."')) and (from_phone like ('".$tmp_p_ph1."') or from_phone like ('".$tmp_p_ph2."') or from_phone like ('".$tmp_p2_ph1."') or from_phone like ('".$tmp_p2_ph2."') or from_phone like ('".$tmp_p3_ph1."') or from_phone like ('".$tmp_p3_ph2."'))) order by start_time desc";
								
																 $res_sel = mysql_query($sql_sel) or die(mysql_error());	
																 $num_rows = mysql_num_rows($res_sel);
															?>   
												      		<div class="table-responsive" style="<?php if ($num_rows!=0) echo 'height:200px;';?> overflow:auto; overflow-y:scroll;margin-bottom: 0px;">         
												    			<table class="table table-striped my-table-font" style="margin-bottom: 0px;">
											    					<thead>
																		<tr >
																	        <th class="col-xs-1">No</th>
																	        <th class="col-xs-3">From</th>
																	        <th class="col-xs-3">To</th>
																	        <th class="col-xs-3">StartTime</th>
																	        <th class="col-xs-2">Conversation Time</th>													        
																	    </tr>
																    </thead>
																	<tbody>
																	<?php	
																		$i=0;
																		while ($response = mysql_fetch_assoc($res_sel)) 
																		{				  	  	
																	  	  	$i++;
																	  	  	if ($response['start_time'] > $_SESSION['last_logout']) {
																				if ((int)$response['call_flag'][$i] == 1) {
																					$style="style='min-height:50px;font-weight:bold;color:#0c00ea;'";
												                            	}else{
																					$style="style='min-height:50px;color:#0c00ea;'";	                                    
												                                }	
												                            }else
												                            	$style="style='min-height:50px;'";
																	?>		
																			<tr <?php echo $style;?>>
				                                                            	<td class="col-xs-1"><?php echo $i;?></td>
				                                                                <td class="col-xs-3"><?php echo $response['from_phone'];?></td>
				                                                                <td class="col-xs-3"><?php echo $response['to_phone'];?></td>
				                                                               	<td class="col-xs-3"><?php echo $response['start_time'];?></td>
				                                                               	<td class="col-xs-2"><?php echo $response['conv_time'];?></td>
					                                                        </tr> 
					                                                 <?php	                                                   		
					                                                	 }
					                                                 ?>													     
																    </tbody>
											    				</table>
												    		</div>											
						                                </div>
												    </div>
													<div class="panel panel-success">
												    	<div class="panel-heading">Text Log</div>
												      	<div class="panel-body">
												      		<?php 
															   	 /* ------------------ Text logs  ----------------------------- */
																$phone = '+1'.$_SESSION['google_voice_ph'];
																$cur_login_time = $_SESSION['cur_login_time'];
																
																if (isset($_POST['p_ph1']))
																{
																	$tmp_p_ph1 = "+1".preg_replace("/[^0-9]*/s", "",$_POST['p_ph1']);
																	$tmp_p_ph2 = "+1".preg_replace("/[^0-9]*/s", "",$_POST['p_ph2']);
																	$tmp_p2_ph1 ="+1".preg_replace("/[^0-9]*/s", "",$_POST['p2_ph1']);
																	$tmp_p2_ph2 = "+1".preg_replace("/[^0-9]*/s", "",$_POST['p2_ph2']);
																	$tmp_p3_ph1 = "+1".preg_replace("/[^0-9]*/s", "",$_POST['p3_ph1']);
																	$tmp_p3_ph2 = "+1".preg_replace("/[^0-9]*/s", "",$_POST['p3_ph2']);
																}else{
																	$tmp_p_ph1 = "+1".preg_replace("/[^0-9]*/s", "",$recb['p_ph1']);
																	$tmp_p_ph2 = "+1".preg_replace("/[^0-9]*/s", "",$recb['p_ph2']);
																	$tmp_p2_ph1 = "+1".preg_replace("/[^0-9]*/s", "",$recb['p2_ph1']);
																	$tmp_p2_ph2 = "+1".preg_replace("/[^0-9]*/s", "",$recb['p2_ph2']);
																	$tmp_p3_ph1 = "+1".preg_replace("/[^0-9]*/s", "",$recb['p3_ph1']);
																	$tmp_p3_ph2 = "+1".preg_replace("/[^0-9]*/s", "",$recb['p3_ph2']);
																	
																}
																
																
																if ($_SESSION['user_group'] == "Admin")
																	$phone="%";
																	
																/* Send SMS */																	
																$sql_sel="select * from sms_log_info where  ((from_phone like ('".$phone."')) and (to_phone like ('".$tmp_p_ph1."') or to_phone like ('".$tmp_p_ph2."') or to_phone like ('".$tmp_p2_ph1."') or to_phone like ('".$tmp_p2_ph2."') or to_phone like ('".$tmp_p3_ph1."') or to_phone like ('".$tmp_p3_ph2."'))) or ((to_phone like ('".$phone."')) and (from_phone like ('".$tmp_p_ph1."') or from_phone like ('".$tmp_p_ph2."') or from_phone like ('".$tmp_p2_ph1."') or from_phone like ('".$tmp_p2_ph2."') or from_phone like ('".$tmp_p3_ph1."') or from_phone like ('".$tmp_p3_ph2."'))) order by sms_time desc";
														
																$res_sel = mysql_query($sql_sel) or die(mysql_error());	
																$num_rows = mysql_num_rows($res_sel);
															?>
												      		<div class="table-responsive" style="<?php if ($num_rows!=0) echo 'height:200px;';?> overflow:auto; overflow-y:scroll;margin-bottom: 0px;">         
												    			<table class="table table-striped my-table-font" style="margin-bottom: 0px;">  
											    					<thead>
																		<tr >
																	        <th class="col-xs-1">No</th>
																	        <th class="col-xs-2">From</th>
																	        <th class="col-xs-2">To</th>
																	        <th class="col-xs-4">Content</th>
																	        <th class="col-xs-2">Time</th>
																	        <th class="col-xs-1">Res</th>
																	    </tr>
																    </thead>
																	<tbody>
															<?php	
																$i=0;
																while ($response = mysql_fetch_assoc($res_sel)) 																
															   	{					  	  	
															  	  	$i++;
															  	  	if ($response['to_phone']!=$phone)
															  	  		$response_phone=$response['to_phone'];
															  	  	else
															  	  		$response_phone=$response['from_phone'];
																 	 
									                                if ($response['sms_time'] > $_SESSION['last_logout']) {
																		if ((int)$response['mail_stat'][$i] == 1) {
																			$style="style='min-height:50px;font-weight:bold;color:#0c00ea;'";
										                            	}else{
																			$style="style='min-height:50px;color:#0c00ea;'";	                                    
										                                }	
										                            }else
										                            	$style="style='min-height:50px;'";
					    	
															?>		
																			<tr <?php echo $style;?>>
				                                                            	<td class="col-xs-1"><?php echo $i;?></td>
																		        <td class="col-xs-2"><?php echo $response['from_phone'];?></td>
																		        <td class="col-xs-2"><?php echo $response['to_phone'];?></td>
																		        <td class="col-xs-4"><?php echo $response['content'];?></td>
																		        <td class="col-xs-2"><?php echo $response['sms_time'];?></td>
																		        
																		         
																		        <td class="col-xs-1"><button type="button" onclick="javascript: ClicktoSMS('<?php echo $response_phone;?>');"  class="btn btn-success btn-xs" style="padding:1px">Sms</button></td>
					                                                        </tr> 
				                                             <?php	                                                   		
				                                            	 }
				                                             ?>													     
																    </tbody>
											    				</table>
												    		</div>											
						                                </div>
												    </div>
												    <div class="panel panel-info">
												    	<div class="panel-heading">Email Log</div>
												      	<div class="panel-body">
												      		<?php 
															   	/* ------------------ Email logs  ----------------------------- */
																$cur_login_time = $_SESSION['cur_login_time'];
																
																$agent_acc = $_SESSION['google_acc_nm'];
																if ($_SESSION['user_group'] == "Admin")
																	$agent_acc="%";
																/* sent email */																		
																if (isset($_POST['p_eml1']))
																{
																	$sql_sel="select * from mail_log_info where  ((mail_rcvr like ('".$_POST['p_eml1']."') or mail_rcvr like ('".$_POST['p_eml2']."') or mail_rcvr like ('".$_POST['p2_eml1']."') or mail_rcvr like ('".$_POST['p2_eml2']."') or mail_rcvr like ('".$_POST['p3_eml1']. "') or mail_rcvr like ('".$_POST['p3_eml2']. "')) and (from_address like ('".$agent_acc."')) or ((from_address like ('".$_POST['p_eml1']."') or from_address like ('".$_POST['p_eml2']."')) or from_address like ('".$_POST['p2_eml1']."') or from_address like ('".$_POST['p2_eml2']."') or from_address like ('".$_POST['p3_eml1']. "') or from_address like ('".$_POST['p3_eml2']. "')) and (mail_rcvr like ('".$agent_acc."'))) order by send_dt desc";
																}else if (isset($recb['p_eml1'])){
																	$sql_sel="select * from mail_log_info where  ((mail_rcvr like ('".$recb['p_eml1']."') or mail_rcvr like ('".$recb['p_eml2']."') or mail_rcvr like ('".$recb['p2_eml1']."') or mail_rcvr like ('".$recb['p2_eml2']."') or mail_rcvr like ('".$recb['p3_eml1']. "') or mail_rcvr like ('".$recb['p3_eml2']. "')) and (from_address like ('".$agent_acc."')) or ((from_address like ('".$recb['p_eml1']."') or from_address like ('".$recb['p_eml2']."')) or from_address like ('".$recb['p2_eml1']."') or from_address like ('".$recb['p2_eml2']."') or from_address like ('".$recb['p3_eml1']. "') or from_address like ('".$recb['p3_eml2']. "')) and (mail_rcvr like ('".$agent_acc."'))) order by send_dt desc";
																}else
																	$sql_sel="select * from mail_log_info where 1=0";
																	
																	
																$res_sel = mysql_query($sql_sel) or die(mysql_error());	
																$num_rows = mysql_num_rows($res_sel);
															?>
												      		<div class="table-responsive" style="<?php if ($num_rows!=0) echo 'height:200px;';?> overflow:auto; overflow-y:scroll;margin-bottom: 0px;">         
												    			<table class="table table-striped my-table-font" style="margin-bottom: 0px;">  
											    					<thead>
																		<tr style="font-size:16px">
																			<th class="col-sm-1">No</th>
																			<th class="col-sm-1">From</th>
																			<th class="col-sm-1">To</th>
																			<th class="col-sm-2">Subject</th>													        
																			<th class="col-sm-4">Content</th>		
																			<th class="col-sm-1">Time</th>		
																			<th class="col-sm-1">Opened</th>		
																			<th class="col-sm-1">Res</th>										
																	    </tr>
																    </thead>
																	<tbody>
															<?php	
																$i=0;
																while ($response = mysql_fetch_assoc($res_sel)) 																
															   	{					  	  	
															  	  	$i++;
															  	  	if ($response['mail_rcvr'] != $agent_acc)
																    	$response_address=$response['mail_rcvr'];
																    else
																    	$response_address=$response['from_address'];
																							    
																 	 
									                                if ($response['send_dt'] > $_SESSION['last_logout']) {
																		if ((int)$response['mail_stat'][$i] == 1) {
																			$style="style='font-weight:bold;color:#0c00ea;'";
										                            	}else{
																			$style="style='olor:#0c00ea;'";	                                    
										                                }	
										                            }else
										                            	$style="style=''";
					    	
															?>		
																			<tr <?php echo $style;?> >
				                                                            	<td class="col-sm-1"><?php echo $i;?></td>
																		        <td class="col-sm-1"><?php echo $response['from_address'];?></td>
																		        <td class="col-sm-1"><?php echo $response['mail_rcvr'];?></td>
																		        <td class="col-sm-2"><?php echo $response['mail_subject'];?></td>
																		        <td class="col-sm-4"><?php echo $response['mail_body'];?></td>
																		        <td class="col-sm-1"><?php echo $response['send_dt'];?></td>
																		        
																		        
																		        <td class="col-sm-1"><?php if ($response['mail_stat']==0)
																	        	{
																					if ($response['is_opened']==1)
																					{
																						echo "Yes";
																					}else
																					{
																						echo "No";
																					}												
																				}
																	        	?></td>
																		        <td class="col-sm-1"><button type="button" onclick="javascript: ClicktoEmail('<?php echo $response_address;?>');"  class="btn btn-success btn-xs" style="padding:1px">Email</button></td>
					                                                        </tr> 
					                                         <?php	                                                   		
					                                        	 }
					                                         ?>													     
																    </tbody>
											    				</table>
												    		</div>											
						                                </div>
												    </div>
												    <div class="panel panel-success">
												    	<div class="panel-heading">Priority History</div>
												      	<div class="panel-body">
												      		<?php 
															   	/* ------------------ Priority Change History  ----------------------------- */
																$cur_login_time = $_SESSION['cur_login_time'];
																
																$agent_login = $_SESSION['user_login'];
																if ($_SESSION['user_group'] == "Admin")
																	$agent_login="%";
																	
																$sql_sel="select * from priority_change_log_info where  (customer_id='" . $_GET['rid'] ."') and (agent_by like ('".$agent_login."'))";
																$res_sel = mysql_query($sql_sel) or die(mysql_error());	
																$num_rows = mysql_num_rows($res_sel);
															?>
												      		<div class="table-responsive" style="<?php if ($num_rows!=0) echo 'height:200px;';?> overflow:auto; overflow-y:scroll;margin-bottom: 0px;">         
												    			<table class="table table-striped my-table-font" style="margin-bottom: 0px;">  
											    					<thead>
																		<tr >
																	        <th class="col-xs-1" >No</th>
																	        <th class="col-xs-3" >Change By</th>
																	        <th class="col-xs-3" >Old Priority</th>
																	        <th class="col-xs-3" >New Priority</th>
																	        <th class="col-xs-2" >Changed Time</th>													        
																	    </tr>
																    </thead>
																	<tbody>
															<?php	
																$i=0;
																while ($response = mysql_fetch_assoc($res_sel)) 																
															   	{					  	  	
															  	  	$i++;
					    	
															?>		
																			<tr style="">
				                                                            	<td class="col-xs-1" ><?php echo $i;?></td>
																		        <td class="col-xs-3" ><?php echo $response['agent_by'];?></td>
																		        <td class="col-xs-3" ><?php echo $response['old_prio'];?></td>
																		        <td class="col-xs-3" ><?php echo $response['new_prio'];?></td>
																		        <td class="col-xs-2" ><?php echo $response['change_time'];?></td>		
						                                                    </tr> 
					                                         <?php	                                                   		
					                                        	 }
					                                         ?>													     
																    </tbody>
											    				</table>
												    		</div>											
						                                </div>
												    </div>
												     <div class="panel panel-info">
												    	<div class="panel-heading">Performance Log</div>
												      	<div class="panel-body">
												      		<?php 
															   /* ------------------ Performance Log  ----------------------------- */
																$agent_login = $_SESSION['user_login'];
																if ($_SESSION['user_group'] == "Admin")
																	$agent_login="%";
																	
																$sql_sel="select * from agent_log_info where  (agent like ('".$agent_login."')) order by log_in desc ";
																$res_sel = mysql_query($sql_sel) or die(mysql_error());	
																$num_rows = mysql_num_rows($res_sel);
															?>
												      		<div class="table-responsive" style="<?php if ($num_rows!=0) echo 'height:200px;';?> overflow:auto; overflow-y:scroll;margin-bottom: 0px;">         
												    			<table class="table table-striped my-table-font" style="margin-bottom: 0px;">  
											    					<thead>
																		<tr >
																	        <th style="width:8%;text-align:center" >Agent</th>
																	        <th style="width:20%;text-align:center" >Login Date</th>
																	        <th style="width:14%;text-align:center" >Duration in Minutes</th>
																	        <th style="width:15%;text-align:center" >Calls Made/Connected</th>
																	        <th style="width:10%;text-align:center" >Conversation Minutes</th>
																	        <th style="width:14%;text-align:center" >Emails Sent/Recevied</th>
																	        <th style="width:14%;text-align:center;">SMS Sent/Recevied</th>
																	        <th>Ratio</th>
																	    </tr>
																    </thead>
																	<tbody>
																<?php	
																	$i=0;
																	while ($response = mysql_fetch_assoc($res_sel)) 																
																   	{					  	  	
																  	  	$i++;
																  	  	$dur_mins = $response['dur_hour'] * 60 + $response['dur_min']+1;
																  	  	$dur_mins .= 'minutes';
																  	  	if ($response['call_made']=='')
																  	  		$response['call_made'] = 0;
																  	  	if ($response['call_conn']=='')
																  	  		$response['call_conn'] = 0;
																  	  	if ($response['conv_min']=='')
																  	  		$response['conv_min'] = 0;
																  	  	if ($response['eml_sent']=='')
																  	  		$response['eml_sent'] = 0;
																  	  	if ($response['eml_recv']=='')
																  	  		$response['eml_recv'] = 0;
																  	  	if ($response['sms_sent']=='')
																  	  		$response['sms_sent'] = 0;
																  	  	if ($response['sms_recv']=='')
																  	  		$response['sms_recv'] = 0;
																  	  	if ($response['ratio']=='')
																  	  		$response['ratio'] = 0;
						    	
																?>		
																			<tr style="">
				                                                            	<td style="width:8%" align="center"><?php echo $response['agent'];?></td>
																		        <td style="width:20%" align="center"><?php echo $response['log_in'];?></td>
																		        <td style="width:14%" align="center"><?php echo $dur_mins;?></td>
																		        <td style="width:15%" align="center">	<?php echo $response['call_made'].'/'.$response['call_conn'];?></td>
																		        <td style="width:10%" align="center"><?php echo $response['conv_min'];?></td>
																		        <td style="width:14%" align="center"><?php echo $response['eml_sent'].'/'.$response['eml_recv'];?></td>
																		        <td style="width:14%" align="center"><?php echo $response['sms_sent'].'/'.$response['sms_recv'];?></td>
																		        <td  align="center"><?php echo $response['ratio'];?></td>
																		    	
						                                                    </tr> 
				                                         <?php	                                                   		
				                                        	 }
				                                         ?>													     
																    </tbody>
											    				</table>
												    		</div>											
						                                </div>
												    </div>
				                                </div>                	
				                                <div>
				                                	<center>
				                                		<input type="submit" class="btn btn-primary btn-lg ladda-button" name="Submit" value="<?= $change ?>" class="button_medium" id="btnChangeSubmit">				                                		
													</center>
				                                </div>
											</div>
								       </form>
									</div>
							</div>
				   		</div>    		
            		</div>
            	</div>
			</div>
        </div>

		
		<!-- sms send modal dialog -->
        <div id="dialog_sms" class="modal fade" title="Send SMS" style="z-index:1000000002;display:none;">
        	<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Send SMS</h4>
			      	</div>
			      	<div class="modal-body">
			      		<div class="form-group">
				        	<label for="phone_numbers"><span class="glyphicon glyphicon-user"></span> Enter numbers</label>
				        	<input type="text" name="phone_numbers" id="phone_numbers" 
						      	    value="<?php
								  	 	  $p_ph1="";
								  	 	  $p_ph2="";
								  	 	  
								  	 	  if (isset($_POST['p_ph1'])) {
				                             $p_ph1 =$_POST['p_ph1'];
				                          } else if (isset($recb['p_ph1'])) {
				                              $p_ph1 =$recb['p_ph1'];
				                          } 
				                           if (isset($_POST['p_ph2'])) {
				                             $p_ph2 =$_POST['p_ph2'];
				                          } else if (isset($recb['p_ph2'])) {
				                              $p_ph2 =$recb['p_ph2'];
				                          } 
				                          
								  	 	  $default_email_to="";
								  	 	  
								  	 	  if ($p_ph1 != '') 
								  	 	  {
										  	$default_email_to =$p_ph1;	
										  	if ($p_ph2 !='') 
										  	{
												$default_email_to .= ";".$p_ph2;	
											}
										  }else if ($p_ph2 !='') 
										  {
											$default_email_to = $p_ph2;	
										  }
								  	 	  echo  $default_email_to; 
								  	 	  ?>"
								  class="form-control" placeholder="Enter Phone Numbers"/>				            
				        </div>
				        <div class="form-group">
				        	<label for="user_email_attach_file_name">Salutation</label>
				        	<input type="text" name="msg_sal" id="msg_sal"   value="Hello" class="form-control"/>
				        </div>
				        <div class="form-group">
				          	<label for="msg_body">Message</label>
				           	<textarea id="msg_body" name="msg_body" rows="5" cols="20" class="form-control"></textarea>				    
				        </div>			
			      	</div>
					<div class="modal-footer">
						<div class="col-xs-6">
			      			<button type="submit" onclick="javascript:sendSMS()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>			        	 
			      		</div>
			      		<div class="col-xs-6">
			      			<button type="submit" onclick="javascript:previewSMS()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Preview</button>			        	 
			      		
			      	</div>	
				</div>
			</div>
			</div>		 
		</div>

		<!-- SMS preview dialog -->
        <div id="dialog_preview_sms" class="modal fade" style="z-index:1000000003;display:none;" title="" >
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Preview SMS</h4>				        
			      	</div>
			      	<div class="modal-body" style="overflow:auto" id="sms_preview_div">
			      		
			      	</div>	
			      	<div class="modal-footer">
			      		<center>			      			
			      			<div class="col-xs-offset-4 col-xs-4">
			      				<button type="submit" onclick="javascript:sendSMS()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>
			      			</div>			      							      		
			      		</center>			      		
			      	</div>				     
				</div>
			</div>
		</div>
		
		<!-- click to phone (call,sms) modal dialog -->
        <div id="dialog_phone" class="modal fade" title="" style="display:none;">
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h2 class="modal-title"><center><label id='dialog_phone_number'></label></center></h2>				    	
			      	</div>
			      	<div class="modal-body">
			      		<div class="row">
			      			<div class="col-xs-6">
			      				<button type="submit" style="font-size:26px" onclick="javascript:ClicktoCall(document.getElementById('dialog_phone_number').innerHTML)" class="btn btn-default btn-primary btn-md btn-block"><!--<span class="glyphicon glyphicon-earphone"></span>-->&nbsp;Call</button>
			      			</div>
			      			<div class="col-xs-6">
			      				<button type="submit" style="font-size:26px" onclick="javascript:ClicktoSMS(document.getElementById('dialog_phone_number').innerHTML)" class="btn btn-default btn-primary btn-md btn-block"><!--<span class="glyphicon glyphicon-envelope"></span>-->&nbsp;Text</button>
			      			</div>
			      		</div>				        
			      	</div>			     
				</div>
			</div>
		</div>

		<!-- connecting dialog -->
        <div id="dialog_connecting" class="modal fade" title="" style="display:none;">
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Calling <label id='connecting_number'></label> ...</h4>				        
			      	</div>
			      	<div class="modal-body">
			      		<input type="hidden" name="connecting_number" id="connecting_number"/>
			      		<div>		      			
  							<h5 style="padding-left:5px">Please accept the incoming call to connect. It will take 15 seconds.</h5>
  						</div>	        
			      	</div>			     
				</div>
			</div>
		</div>
	
			<!-- email preview dialog -->
        <div id="dialog_preview_email" class="modal fade" style="z-index:1000000001" title="" style="display:none;">
        	<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Preview Email</h4>				        
			      	</div>
			      	<div class="modal-body" style="overflow:auto" id="email_preview_div">
			      		
			      	</div>	
			      	<div class="modal-footer">
			      		<center>			      			
			      			<div class="col-xs-offset-4 col-xs-4">
			      				<button type="submit" onclick="javascript:sendEmail()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>
			      			</div>			      							      		
			      		</center>			      		
			      	</div>				     
				</div>
			</div>
		</div>
	
		<!-- email send modal dialog -->
		<div id="dialog-email" class="modal fade" style="z-index:1000000000"  title="Send Email" style="display:none;">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Send Email</h4>
			      	</div>
			      	<div class="modal-body">
			        	<form enctype="multipart/form-data" method="post" id="SendEmailUpload">
			        		<div class="form-group">
				            	<label for="email_from"><span class="glyphicon glyphicon-user"></span> From</label>
				            	<input type="text" class="form-control" name="email_from" id="email_from" placeholder="Enter email" value="<?php if (isset($_SESSION['google_acc_nm'])) echo $_SESSION['google_acc_nm']; else echo ''; ?> " >				            	
				            </div>
            				<div class="form-group">
				            	<label for="email_to"><span class="glyphicon glyphicon-user"></span>To</label>
				            	<input type="text" name="email_to" id="email_to" 
							    	value="<?php
							  	 	  $p_eml1="";
							  	 	  $p_eml2="";
							  	 	  if (isset($_POST['p_eml1'])) {
			                             $p_eml1 =$_POST['p_eml1'];
			                          } else if (isset($recb['p_eml1'])) {
			                              $p_eml1 =$recb['p_eml1'];
			                          } 
			                           if (isset($_POST['p_eml2'])) {
			                             $p_eml2 =$_POST['p_eml2'];
			                          } else if (isset($recb['p_eml2'])) {
			                              $p_eml2 =$recb['p_eml2'];
			                          } 
			                          
							  	 	  $default_email_to="";
							  	 	  
							  	 	  if ($p_eml1 != '') 
							  	 	  {
									  	$default_email_to =$p_eml1;	
									  	if ($p_eml2 !='') 
									  	{
											$default_email_to .= ";".$p_eml2;	
										}
									  }else if ($p_eml2 !='') 
									  {
										$default_email_to = $p_eml2;	
									  }
							  	 	  echo  $default_email_to; 
							  	 	  ?>"
							  	 	 class="form-control" placeholder="Enter Email Address">				            	
				            </div>
            				<div class="form-group">
				            	<label for="email_subj">Subject</label>
				            	<input type="text" name="email_subj" id="email_subj" value="<?php echo $eml_subj;?>" class="form-control"/>				            	
				            </div>
							<div class="form-group">
				            	<label for="user_email_attach_file_name">Attach File</label>
				            	<input name="user_email_attach_file_name" type="file"  value="<?php echo $eml_att;?>" class="form-control"/>
				            </div>
				            <div class="form-group">
				            	<label for="user_email_attach_file_name">Salutation</label>
				            	<input type="text" name="email_sal" id="email_sal"   value="Hello" class="form-control"/>
				            </div>
				            <div class="form-group">
				            	<label for="email_body">Content</label>
				            	<textarea id="email_body" name="email_body" rows="5" cols="20" class="form-control"><?php echo $eml_cont;?></textarea>				    
				            </div>							
						</form>
			      	</div>
			      		
			      	<div class="modal-footer">
			      		<div class="row">
			      			<div class="col-xs-6">
			      				<button type="submit" onclick="javascript:sendEmail()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>			        	 
			      			</div>
			      			<div class="col-xs-6">
			      				<button type="submit" onclick="javascript:previewEmail()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Preview</button>			        	 
			      			</div>
			      		</div>	
			      	</div>					
				</div>
			</div>
		</div>	
	
		<!-- opened emails dialog -->
        <div id="dialog_opened_emails" class="modal fade" title="" style="display:none;">
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Opened Emails</h4>				        
			      	</div>
			      	<div class="modal-body" id="opened_emails">
			      		
			      	</div>			     
				</div>
			</div>
		</div>
		<!-- post idea modal dialog --> 
		<!--<div id="dialog_post_idea"  class="modal fade" title="Post Idea" style="display:none;">    
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Post idea</h4>
					</div>
					<div class="modal-body">
			      		<div class="form-group">
			      			<label for="idea_content"><span class="glyphicon glyphicon-user"></span>Please Input your idea!</label>
			      			<textarea id="idea_content" class="form-control my-form-control" cols="20" rows="5" style="background:#F9F8C2;"></textarea>
			      		</div>
			      	</div>
			      	<div class="modal-footer">
			      		<button type="submit" onclick="javascript:postIdea()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>Send</button>			        	 
			      	</div>			      	
				</div>
			</div>		
        </div>-->
		
        
    </body>
     
 	
 	<!-- for google hangout button -->
    <!--<link rel="canonical" href="http://www.example.com" />        
	<script src="https://apis.google.com/js/platform.js" async defer></script>-->
    <!------------------------------->
</html>