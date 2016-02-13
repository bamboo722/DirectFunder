<?php
/* * **-------------------------------------------------------------------**************************    

  Purpose 	: 	Where user can search the buyer detail

  Project 	:	Sales Lead DB

  Developer 	: 	Kelvin 

  Create Date : 	30/11/2015

 * ***-------------------------------------------------------------------*********************** */
//phpinfo();
@session_start();	
if (! isset($_COOKIE['cookie_login']) and !isset($_SESSION['user_login'])) {//session store admin name
    header("Location: index.php"); //login in AdminLogin.php
}
/*function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}*/

require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");	// Email
require_once("includes/GeeVee/GeeVeeAPI.php");		// Google Voice sms and call
             		
$_SESSION['firstRecords'] = '';

//Pagination 
function pagination($adjacents, $targetpage, $total_pages, $limit, $page, $extra_parameters) {
 
    if ($page)
        $start = ($page - 1) * $limit;    //first item to display on this page
    else
        $start = 0;

    if ($page == 0)
        $page = 1;     //if no page var is given, default to 1.
    $prev = $page - 1;       //previous page is page - 1
    $next = $page + 1;       //next page is page + 1
    $lastpage = ceil($total_pages / $limit);  //lastpage is = total pages / items per page, rounded up.
    $lpm1 = $lastpage - 1;
    $pagination = "";
    if ($lastpage > 0) {
        $pagination.= "<div class=\"pagination\">";
        //previous button
        if ($page > 1)
            $pagination.= "<a href=\"$targetpage?page=$prev" . $extra_parameters . "\">«prev</a>";
        else
            $pagination.= "<span class=\"disabled\">«prev</span>";

        //pages	
        if ($lastpage < 7 + ($adjacents * 2)) { //not enough pages to bother breaking it up
            for ($counter = 1; $counter <= $lastpage; $counter++) {

                if ($counter == $page)
                    $pagination.= "<span class=\"current\">$counter</span>";
                else
                    $pagination.= "<a href=\"$targetpage?page=$counter" . $extra_parameters . "\">$counter</a>";
            }
        }
        elseif ($lastpage > 5 + ($adjacents * 2)) { //enough pages to hide some
            //close to beginning; only hide later pages
            if ($page < 1 + ($adjacents * 2)) {

                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"$targetpage?page=$counter" . $extra_parameters . "\">$counter</a>";
                }
                $pagination.= "...";
                $pagination.= "<a href=\"$targetpage?page=$lpm1" . $extra_parameters . "\">$lpm1</a>";
                $pagination.= "<a href=\"$targetpage?page=$lastpage\"" . $extra_parameters . ">$lastpage</a>";
            }
            //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                $pagination.= "<a href=\"$targetpage?page=1\"" . $extra_parameters . ">1</a>";
                $pagination.= "<a href=\"$targetpage?page=2\"" . $extra_parameters . ">2</a>";
                $pagination.= "...";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"$targetpage?page=$counter\"" . $extra_parameters . ">$counter</a>";
                }
                $pagination.= "...";
                $pagination.= "<a href=\"$targetpage?page=$lpm1" . $extra_parameters . "\">$lpm1</a>";
                $pagination.= "<a href=\"$targetpage?page=$lastpage\"" . $extra_parameters . ">$lastpage</a>";
            }
            //close to end; only hide early pages
            else {

                $pagination.= "<a href=\"$targetpage?page=1\"" . $extra_parameters . ">1</a>";
                $pagination.= "<a href=\"$targetpage?page=2\"" . $extra_parameters . ">2</a>";
                $pagination.= "...";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"$targetpage?page=$counter" . $extra_parameters . "\">$counter</a>";
                }
            }
        }
        //next button
        if ($page < $counter - 1)
            $pagination.= "<a href=\"$targetpage?page=$next" . $extra_parameters . "\">next»</a>";
        else
            $pagination.= "<span class=\"disabled\">next»</span>";
        $pagination.= "</div>\n";
    }

    return $pagination;
}

if (isset($_POST['Add']) && $_POST['Add'] == "Add Buyer") /* modified 20151119 */ {

    // header("Location: buyerentry.php");
    header("Location: buyerinfo2.php?action=add");

    exit();
}

// get history of call, sms, email 
if ($_SESSION['is_login_now'])
{
	//header("Location: getNotification.php");
	$_SESSION['is_login_now']= 0;	
}
 
 /* last select all status */
$old_selected = 1;

$extra_parameters = '';

if (isset($_POST['val']) && isset($_POST['Submit'])) {
    $_SESSION['hdnTodaysFolloups'] = '';
    $_SESSION['hdnSevenDayOverdue'] = '';
    $_SESSION['hdnThirtyDayOverdue'] = '';
    $_SESSION['hdnBuyingTimeThirty'] = '';
    $_SESSION['hdnBuyingTimeSixty'] = '';
    $_SESSION['hdnBuyingTimeNinety'] = '';
    $_SESSION['hdnBuyingTimeEighty'] = '';
    $_SESSION['is_opportunity'] = '';
    
    $_SESSION['no_follow_up_date_cnt'] = '';
    
    
}

if (isset($_POST['val']) && $_POST['val'] != '') {
    $_SESSION['hdnTodaysFolloups'] = '';
    $_SESSION['hdnSevenDayOverdue'] = '';
    $_SESSION['hdnThirtyDayOverdue'] = '';
    $_SESSION['hdnBuyingTimeThirty'] = '';
    $_SESSION['hdnBuyingTimeSixty'] = '';
    $_SESSION['hdnBuyingTimeNinety'] = '';
    $_SESSION['hdnBuyingTimeEighty'] = '';
     $_SESSION['is_opportunity'] = '';
     $_SESSION['no_follow_up_date_cnt'] = '';
}

if (isset($_POST['field_nm']))
    $_SESSION['field_nm'] = $_POST['field_nm'];
$_POST['field_nm'] = $_SESSION['field_nm'];

if (isset($_POST['val']))
    $_SESSION['val'] = $_POST['val'];
$_POST['val'] = $_SESSION['val'];

/* initial search user option. It's To Do */
/*if (isset($_SESSION['search_manager']))
	$_POST['search_manager'] = $_SESSION['search_manager'];
if (isset($_SESSION['search_agent']))
	$_POST['search_agent'] = $_SESSION['search_agent'];
if (isset($_SESSION['search_customer']))
	$_POST['search_customer'] = $_SESSION['search_customer']; 
$_SESSION['search_manager']=NULL;
$_SESSION['search_agent']=NULL;
$_SESSION['search_customer']=NULL;*/

$_POST['search_manager'] = '';
$_POST['search_agent'] = '';
$_POST['search_customer'] = 'search_customer';

if (isset($_POST['hdnTodaysFolloups']) and $_POST['hdnTodaysFolloups'] == 1)
    $_SESSION['hdnTodaysFolloups'] = $_POST['hdnTodaysFolloups'];
/* else
  $_SESSION['hdnTodaysFolloups'] = ''; */

$_POST['hdnTodaysFolloups'] = $_SESSION['hdnTodaysFolloups'];

if (isset($_POST['hdnSevenDayOverdue']) and $_POST['hdnSevenDayOverdue'] == 1)
    $_SESSION['hdnSevenDayOverdue'] = $_POST['hdnSevenDayOverdue'];
/* else
  $_SESSION['hdnSevenDayOverdue'] = ''; */

$_POST['hdnSevenDayOverdue'] = $_SESSION['hdnSevenDayOverdue'];

if (isset($_POST['hdnThirtyDayOverdue']) and $_POST['hdnThirtyDayOverdue'] == 1)
    $_SESSION['hdnThirtyDayOverdue'] = $_POST['hdnThirtyDayOverdue'];
/* else
  $_SESSION['hdnThirtyDayOverdue'] = ''; */

$_POST['hdnThirtyDayOverdue'] = $_SESSION['hdnThirtyDayOverdue'];

if (isset($_POST['no_follow_up_date']) and $_POST['no_follow_up_date'] == 1)
    $_SESSION['no_follow_up_date_cnt'] = $_POST['no_follow_up_date'];
/* else
  $_SESSION['no_follow_up_date'] = ''; */

$_POST['no_follow_up_date'] = $_SESSION['no_follow_up_date_cnt'];

if (isset($_POST['hdnBuyingTimeThirty']) and $_POST['hdnBuyingTimeThirty'] == 1)
    $_SESSION['hdnBuyingTimeThirty'] = $_POST['hdnBuyingTimeThirty'];
/* else
  $_SESSION['hdnBuyingTimeThirty'] = ''; */

$_POST['hdnBuyingTimeThirty'] = $_SESSION['hdnBuyingTimeThirty'];

if (isset($_POST['hdnBuyingTimeSixty']) and $_POST['hdnBuyingTimeSixty'] == 1)
    $_SESSION['hdnBuyingTimeSixty'] = $_POST['hdnBuyingTimeSixty'];
/* else
  $_SESSION['hdnBuyingTimeSixty'] = ''; */

$_POST['hdnBuyingTimeSixty'] = $_SESSION['hdnBuyingTimeSixty'];

if (isset($_POST['hdnBuyingTimeNinety']) and $_POST['hdnBuyingTimeNinety'] == 1)
    $_SESSION['hdnBuyingTimeNinety'] = $_POST['hdnBuyingTimeNinety'];
/* else
  $_SESSION['hdnBuyingTimeNinety'] = ''; */

$_POST['hdnBuyingTimeNinety'] = $_SESSION['hdnBuyingTimeNinety'];

if (isset($_POST['is_opportunity']) and $_POST['is_opportunity'] == 1)
    $_SESSION['is_opportunity'] = $_POST['is_opportunity'];

$_POST['is_opportunity'] = $_SESSION['is_opportunity'];

if (isset($_POST['hdnBuyingTimeEighty']) and $_POST['hdnBuyingTimeEighty'] == 1)
    $_SESSION['hdnBuyingTimeEighty'] = $_POST['hdnBuyingTimeEighty'];

$_POST['hdnBuyingTimeEighty'] = $_SESSION['hdnBuyingTimeEighty'];

if (isset($_POST['hdnVolume_Buyers']) and $_POST['hdnVolume_Buyers'] == 1)
    $_SESSION['hdnVolume_Buyers'] = $_POST['hdnVolume_Buyers'];

$_POST['hdnVolume_Buyers'] = $_SESSION['hdnVolume_Buyers'];

if ($_POST['Reset'] == "Reset" or $_GET['rst'] == 1) {
    unset($_SESSION['hdnTodaysFolloups']);
    unset($_SESSION['hdnSevenDayOverdue']);
    unset($_SESSION['hdnThirtyDayOverdue']);
    unset($_SESSION['no_follow_up_date_cnt']);
    
    unset($_SESSION['hdnBuyingTimeThirty']);
    unset($_SESSION['hdnBuyingTimeSixty']);
    unset($_SESSION['hdnBuyingTimeNinety']);
    unset($_SESSION['is_opportunity']);
    
    unset($_SESSION['hdnBuyingTimeEighty']);
    unset($_SESSION['hdnVolume_Buyers']);
    unset($_SESSION['val']);
    unset($_SESSION['field_nm']);
    unset($_SESSION['search_manager']);
    unset($_SESSION['search_agent']);
    unset($_SESSION['search_customer']);
    
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
    
    $_SESSION['val'] = '';
    $_SESSION['field_nm'] = '';
    header("Location: searchbuyer.php");
}

$join = 'where';
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


if ($_POST['no_follow_up_date'] == 1) {
$filter = " AND ((follow_up is NULL) OR (follow_up = '0000-00-00')) AND priority_opt!='Clients' AND priority_opt!='Partners'  AND priority_opt!='Inactive'  AND priority_opt!='Not Interested'";
}

if ($_POST['hdnBuyingTimeThirty'] == 1) {
    $filter = " AND funding_dt >= DATE( NOW( ) ) AND funding_dt < DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <> '0000-00-00'  AND priority_opt!='Delete'";
}

if ($_POST['hdnBuyingTimeSixty'] == 1) {
    $filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <> '0000-00-00'  AND priority_opt!='Delete'";
}

if ($_POST['hdnBuyingTimeNinety'] == 1) {
    $filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <> '0000-00-00'  AND priority_opt!='Delete'";
}


if ($_POST['is_opportunity'] == 1) {
    $filter = " AND is_opportunity_yes =1  AND priority_opt!='Delete'";
}

if ($_POST['hdnBuyingTimeEighty'] == 1) {
    $filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 180 DAY) AND funding_dt <> '0000-00-00'  AND priority_opt!='Delete' ";
}

if ($_SESSION['hdnVolume_Buyers'] == 1) {
    $filter = " AND equipment_volume_buyers = 1  AND priority_opt!='Delete'";
}


// determine condition of selecting users
$sql_user_part="";
if ((isset($_GET['stat']) or isset($_GET['back'])) and $_POST['Submit'] != "Search") 
{
    $sql_user_part = $_SESSION['sql_condtion1'];
}else if ($_SESSION['user_group'] == "Admin")  // if the user is Admin, then he can see all managers, agents and customers.
{
	$sql_user_part = "select count(*) as totalCount from admin_user where 1=0";
	
	if (isset($_POST['search_manager']) and $_POST['search_manager'] != '') {
        $sql_user_part .= " or user_group='Manager'";
    }
    if (isset($_POST['search_agent']) and $_POST['search_agent'] != '') {
        $sql_user_part .= " or user_group='Agent'";
    }
	
}else if ($_SESSION['user_group'] == "Manager")  // if the user is manager, then he can see all managers, agents and customers.
{
	$sql_user_part = "select count(*) as totalCount from admin_user where 1=0";
	
	if (isset($_POST['search_agent']) and $_POST['search_agent'] != '') {
        $sql_user_part .= " or (user_group='Agent' and owner='".$_SESSION['user_login']."')";
    }    
	
}else if ($_SESSION['user_group'] == "Agent")  // if the user is manager, then he can see all managers, agents and customers.
{
	$sql_user_part = "select count(*) as totalCount  from admin_user where 1=0";
}

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


// determine condition of selecting customers
$sql_first = "select count(customer_id) as totalCount from customer_info where priority_opt!='Delete' $filter ";
$sqlpart="";
$_POST['val'] = trim($_POST['val']);
$_POST['field_nm'] = trim($_POST['field_nm']);

$_tmp_dt = $_POST['val'];

if ((int)(preg_replace("/[^0-9]*/s", "",$_tmp_dt)) == 0)
	$_tmp_dt=NULL;
if (isset($_tmp_dt))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$_tmp_dt)) != 0)
	{
		$follow_up = explode("/", $_tmp_dt);
		if (count($follow_up)==3)
        	$_tmp_dt = $follow_up['2'] . '-' . $follow_up['0'] . '-' . $follow_up['1'];  		
	}		
}
				
if ((isset($_GET['stat']) or isset($_GET['back'])) and $_POST['Submit'] != "Search") 
{
    $sqlpart = $_SESSION['sql_condtion1'];
} else
{ 
	switch($_POST['field_nm'])
	{
		case "":
			if (isset($_tmp_dt) and $_tmp_dt != "")
				$sqlpart = $sqlpart . " and ((lead_src like ('%" . $_POST['val'] . "%'))" ." or ((p_fl_nm like ('%" . $_POST['val'] . "%')) or (p_psn2 like ('%" . $_POST['val'] . "%')) or (p_psn3 like ('%" . $_POST['val'] . "%')))" . " or ((p_ph1 like ('%" . $_POST['val'] . "%')) or (p_ph2 like ('%" . $_POST['val'] . "%')) or (p2_ph1 like ('%" . $_POST['val'] . "%')) or (p3_ph1 like ('%" . $_POST['val'] . "%')))". " or ((p_eml1 like ('%" . $_POST['val'] . "%')) or (p_eml2 like ('%" . $_POST['val'] . "%')) or (p2_eml like ('%" . $_POST['val'] . "%')) or (p3_eml like ('%" . $_POST['val'] . "%')))" . " or (priority_opt like ('%" . $_POST['val'] . "%'))". " or ( cust_upd_dt like '%". $_tmp_dt."%')"." or (apply_dt like '%". $_tmp_dt."%')". " or (funding_dt  like '%". $_tmp_dt."%')". " or ((p_dob  like '%". $_tmp_dt."%') or (p2_dob  like '%". $_tmp_dt."%') or (p3_dob  like '%". $_tmp_dt."%'))". " or ((p_ss like ('%" . $_POST['val'] . "%')) or (p2_ss like ('%" . $_POST['val'] . "%')) or (p3_ss like ('%" . $_POST['val'] . "%')))". " or ((b_cred_usr like ('%" . $_POST['val'] . "%')) or (p1_cred_usr like ('%" . $_POST['val'] . "%')) or (p2_cred_usr like ('%" . $_POST['val'] . "%')) or (p3_cred_usr like ('%" . $_POST['val'] . "%')))"  . " or ((b_cred_pwd like ('%" . $_POST['val'] . "%')) or (p1_cred_pwd like ('%" . $_POST['val'] . "%')) or (p2_cred_pwd like ('%" . $_POST['val'] . "%')) or (p3_cred_pwd like ('%" . $_POST['val'] . "%')))". " or ((p_city like ('%" . $_POST['val'] . "%')) or (p2_city like ('%" . $_POST['val'] . "%')) or (p3_city like ('%" . $_POST['val'] . "%')) or (b_city like ('%" . $_POST['val'] . "%')))". " or ((p_hm_addr like ('%" . $_POST['val'] . "%')) or (p2_hm_addr like ('%" . $_POST['val'] . "%')) or (p3_hm_addr like ('%" . $_POST['val'] . "%')) or (p3_eml like ('%" . $_POST['val'] . "%')))". " or ((p_zip like ('%" . $_POST['val'] . "%')) or (p2_zip like ('%" . $_POST['val'] . "%')) or (p3_zip like ('%" . $_POST['val'] . "%')) or (b_zip like ('%" . $_POST['val'] . "%')))". " or b_leg_nm like ('%" . $_POST['val'] . "%'))" ;
			else
				$sqlpart = $sqlpart . " and ((lead_src like ('%" . $_POST['val'] . "%'))" ." or ((p_fl_nm like ('%" . $_POST['val'] . "%')) or (p_psn2 like ('%" . $_POST['val'] . "%')) or (p_psn3 like ('%" . $_POST['val'] . "%')))" . " or ((p_ph1 like ('%" . $_POST['val'] . "%')) or (p_ph2 like ('%" . $_POST['val'] . "%')) or (p2_ph1 like ('%" . $_POST['val'] . "%')) or (p3_ph1 like ('%" . $_POST['val'] . "%')))". " or ((p_eml1 like ('%" . $_POST['val'] . "%')) or (p_eml2 like ('%" . $_POST['val'] . "%')) or (p2_eml like ('%" . $_POST['val'] . "%')) or (p3_eml like ('%" . $_POST['val'] . "%')))" . " or (priority_opt like ('%" . $_POST['val'] . "%'))". " or ((p_ss like ('%" . $_POST['val'] . "%')) or (p2_ss like ('%" . $_POST['val'] . "%')) or (p3_ss like ('%" . $_POST['val'] . "%')))". " or ((b_cred_usr like ('%" . $_POST['val'] . "%')) or (p1_cred_usr like ('%" . $_POST['val'] . "%')) or (p2_cred_usr like ('%" . $_POST['val'] . "%')) or (p3_cred_usr like ('%" . $_POST['val'] . "%')))"  . " or ((b_cred_pwd like ('%" . $_POST['val'] . "%')) or (p1_cred_pwd like ('%" . $_POST['val'] . "%')) or (p2_cred_pwd like ('%" . $_POST['val'] . "%')) or (p3_cred_pwd like ('%" . $_POST['val'] . "%')))". " or ((p_city like ('%" . $_POST['val'] . "%')) or (p2_city like ('%" . $_POST['val'] . "%')) or (p3_city like ('%" . $_POST['val'] . "%')) or (b_city like ('%" . $_POST['val'] . "%')))". " or ((p_hm_addr like ('%" . $_POST['val'] . "%')) or (p2_hm_addr like ('%" . $_POST['val'] . "%')) or (p3_hm_addr like ('%" . $_POST['val'] . "%')) or (p3_eml like ('%" . $_POST['val'] . "%')))". " or ((p_zip like ('%" . $_POST['val'] . "%')) or (p2_zip like ('%" . $_POST['val'] . "%')) or (p3_zip like ('%" . $_POST['val'] . "%')) or (b_zip like ('%" . $_POST['val'] . "%')))". " or b_leg_nm like ('%" . $_POST['val'] . "%'))" ;
			break;
		case "lead_src":
			$sqlpart = $sqlpart . " and (lead_src like ('%" . $_POST['val'] . "%'))";
			break;
		case "p_fl_nm":
			$sqlpart = $sqlpart . " and ((p_fl_nm like ('%" . $_POST['val'] . "%')) or (p_psn2 like ('%" . $_POST['val'] . "%')) or (p_psn3 like ('%" . $_POST['val'] . "%')))";
			break;
		case "p_ph":
			$sqlpart = $sqlpart . " and ((p_ph1 like ('%" . $_POST['val'] . "%')) or (p_ph2 like ('%" . $_POST['val'] . "%')) or (p2_ph1 like ('%" . $_POST['val'] . "%')) or (p3_ph1 like ('%" . $_POST['val'] . "%')))";
			break;
		case "p_eml":
			$sqlpart = $sqlpart . " and ((p_eml1 like ('%" . $_POST['val'] . "%')) or (p_eml2 like ('%" . $_POST['val'] . "%')) or (p2_eml like ('%" . $_POST['val'] . "%')) or (p3_eml like ('%" . $_POST['val'] . "%')))";
			break;
		case "priority_opt":
			$sqlpart = $sqlpart . " and priority_opt like ('%" . $_POST['val'] . "%')";
			break;			
		case "out_come":
			$join = ", (select customer_id from conversation_log_info where out_come like ('%".$_POST['val']."%')) as conversations where customer_info.customer_id = conversations.customer_id and ";
			break;			
		case "cust_upd_dt":
			$sqlpart = $sqlpart . " and cust_upd_dt like '%". $_tmp_dt."%'";
			break;
		case "apply_dt":
			$sqlpart = $sqlpart . " and apply_dt like '%". $_tmp_dt."%'";
			break;
		case "funding_dt":
			$sqlpart = $sqlpart . " and funding_dt  like '%". $_tmp_dt."%'";
			break;
		case "dob":
			$sqlpart = $sqlpart . " and ((p_dob  like %'". $_tmp_dt."%') or (p2_dob  like '%". $_tmp_dt."%') or (p3_dob  like '%". $_tmp_dt."%'))";
			break;
		case "ss":
			$sqlpart = $sqlpart . " and ((p_ss like ('%" . $_POST['val'] . "%')) or (p2_ss like ('%" . $_POST['val'] . "%')) or (p3_ss like ('%" . $_POST['val'] . "%')))";
			break;		
		case "Login":
			$sqlpart = $sqlpart . " and ((b_cred_usr like ('%" . $_POST['val'] . "%')) or (p1_cred_usr like ('%" . $_POST['val'] . "%')) or (p2_cred_usr like ('%" . $_POST['val'] . "%')) or (p3_cred_usr like ('%" . $_POST['val'] . "%')))";			
			break;
		case "Password":
			$sqlpart = $sqlpart . " and ((b_cred_pwd like ('%" . $_POST['val'] . "%')) or (p1_cred_pwd like ('%" . $_POST['val'] . "%')) or (p2_cred_pwd like ('%" . $_POST['val'] . "%')) or (p3_cred_pwd like ('%" . $_POST['val'] . "%')))";
			break;		
		case "hm_addr":
			$sqlpart = $sqlpart . " and ((p_hm_addr like ('%" . $_POST['val'] . "%')) or (p2_hm_addr like ('%" . $_POST['val'] . "%')) or (p3_hm_addr like ('%" . $_POST['val'] . "%')) or (p3_eml like ('%" . $_POST['val'] . "%')))";
			break;	
		case "city":
			$sqlpart = $sqlpart . " and ((p_city like ('%" . $_POST['val'] . "%')) or (p2_city like ('%" . $_POST['val'] . "%')) or (p3_city like ('%" . $_POST['val'] . "%')) or (b_city like ('%" . $_POST['val'] . "%')))";
			break;
		case "zip":
			$sqlpart = $sqlpart . " and ((p_zip like ('%" . $_POST['val'] . "%')) or (p2_zip like ('%" . $_POST['val'] . "%')) or (p3_zip like ('%" . $_POST['val'] . "%')) or (b_zip like ('%" . $_POST['val'] . "%')))";
			break;
			
		case "cd_name":
			$join = ", (select distinct customer_id from funding_info where cd_name like ('%".$_POST['val']."%')) as fundings where customer_info.customer_id = fundings.customer_id and ";			
			break;
		case "cd_number":
			$join = ", (select distinct customer_id from funding_info where cd_number like ('%".$_POST['val']."%')) as fundings where customer_info.customer_id = fundings.customer_id and ";			
			break;	
		case "issu_bnk":
			$join = ", (select distinct customer_id from funding_info where issu_bnk like ('%".$_POST['val']."%')) as fundings where customer_info.customer_id = fundings.customer_id and ";			
			break;
		case "opportunity":		
			$join = ", (select distinct customer_id from opportunity_info where opportunity like ('%".$_POST['val']."%')) as opportunities where customer_info.customer_id = opportunities.customer_id and ";
			break;
		case "referal_company_name":
			$join = ", (select distinct customer_id from opportunity_info where referal_company_name like ('%".$_POST['val']."%')) as opportunities where customer_info.customer_id = opportunities.customer_id and ";
			break;
		case "referal_person_name":
			$join = ", (select distinct customer_id from opportunity_info where referal_person_name like ('%".$_POST['val']."%')) as opportunities where customer_info.customer_id = opportunities.customer_id and ";
			break;
		case "fee_amount":
			$join = ", (select distinct customer_id from opportunity_info where fee_amount like ('%".$_POST['val']."%')) as opportunities where customer_info.customer_id = opportunities.customer_id and ";
			break;
		case "b_leg_nm":
			$sqlpart = $sqlpart . " and b_leg_nm like ('%" . $_POST['val'] . "%')";
			break;
		default:
			break;
			
	}
    
    if ($_SESSION['user_group'] == "Admin") { // if the user is Admin, then he can see all managers, agents and customers.
	    if (isset($_POST['search_customer']) and $_POST['search_customer'] != '') {
	        
		}else
		{
			$sqlpart = $sqlpart . " and 1=0"; // don't show 
		}		
	} else if ($_SESSION['user_group'] == "Finance") 
	{   
	    $sqlpart = $sqlpart . " and financing_opt='Lease'";
	} else if ($_SESSION['user_group'] == "Lease2") 
	{
	    $sqlpart = $sqlpart . " and financing_opt='Lease2'";
	} else if ($_SESSION['user_group'] == "Lease3") 
	{
		$sqlpart = $sqlpart . " and financing_opt='Lease3'";
	} else if ($_SESSION['user_group'] == "Lease4") 
	{
		$sqlpart = $sqlpart . " and financing_opt='Lease4'";
	} else if ($_SESSION['user_group'] == "Manager")
	{ 
  
	    if (isset($_POST['search_customer']) and $_POST['search_customer'] != '') {
	        //$sqlpart = $sqlpart . " and ("; 
	    }else
	    {
			 $sqlpart = $sqlpart . " and 1=0"; // don't show 
		}    
		
		/* seach all customers that are assigned agents who are participated into manager */
		$sql_sub_sel="select user_id from admin_user where user_group='Agent' and owner='".$_SESSION['user_login']."'";
		$res=mysql_query($sql_sub_sel) or die(mysql_error()."11");
		$is_first=1;
		$sql_agent_cond = " (";
		while ($agent_res = mysql_fetch_assoc($res)) {
			if ($is_first)
				$sql_agent_cond = $sql_agent_cond . "agent='". $agent_res['user_id'] . "'";
			else
				$sql_agent_cond = $sql_agent_cond . " or agent='". $agent_res['user_id'] . "'";
			$is_first = 0;
		}
		$sql_agent_cond .= ")";
		if ($sql_agent_cond != "()")
		{
			$sqlpart = $sqlpart . " and" . $sql_agent_cond;
		}	
    //$sqlpart = $sqlpart . " and agent='" . $_SESSION['user_login'] . "'";
	} else if ($_SESSION['user_group'] == "Agent") 
																															{ // if the user is manager, then he can see particpated customers.
   	    if (isset($_POST['search_customer']) and $_POST['search_customer'] != '') {
	        
	    }else
	    {
			 $sqlpart = $sqlpart . " and 1=0"; // don't show 
		}    

	    $sqlpart = $sqlpart . " and agent='" . $_SESSION['user_login'] . "'";
	}
}


$sql_cnt = $sql_first . $sqlpart;

$order = " order by auto_id desc";

if ($_GET['name'] == 'd') {
    $extra_parameters .= "&name=d";
    $order = " order by p_fl_nm desc";
}

if ($_GET['name'] == 'a') {
    $extra_parameters .= "&name=a";
    $order = " order by p_fl_nm asc";
}

if ($_GET['agent'] == 'd') {
    $extra_parameters .= "&agent=d";
    $order = " order by agent desc";
}

if ($_GET['agent'] == 'a') {
    $extra_parameters .= "&agent=a";
    $order = " order by agent asc";
}

if ($_GET['last_mod'] == 'd') {
    $extra_parameters .= "&last_mod=d";
    $order = " order by cust_upd_dt desc";
}

if ($_GET['last_mod'] == 'a') {
    $extra_parameters .= "&last_mod=a";
    $order = " order by cust_upd_dt asc";
}

if ($_GET['priority'] == 'd') {
    $extra_parameters .= "&priority=d";
    $order = " order by priority_opt desc";
}

if ($_GET['priority'] == 'a') {
    $extra_parameters .= "&priority=a";
    $order = " order by priority_opt asc";
}

if ($_GET['stdt'] == 'd') {
    $extra_parameters .= "&stdt=d";
    $order = " order by str_to_date(apply_dt,'%m-%d-%Y') desc";
}

if ($_GET['stdt'] == 'a') {
    $extra_parameters .= "&stdt=a";
    $order = " order by str_to_date(apply_dt,'%m-%d-%Y') asc";
}




if ($_GET['follow'] == 'd') {
    $extra_parameters .= "&follow=d";

    $order = " order by follow_up desc";
}

if ($_GET['follow'] == 'a') {
    $extra_parameters .= "&follow=a";
    $order = " order by follow_up asc";
}



//$sql = $sql_first.$sqlpart.$order;
$sql = $sql_first . $sqlpart;
   
$res_sel = mysql_query($sql) or die(mysql_error() . "11111");
$customer_count_array = mysql_fetch_array($res_sel);
$noofrecords = $customer_count_array['totalCount'];

$res_sel = mysql_query($sql_user_part) or die(mysql_error() . "11111");
$customer_count_array = mysql_fetch_array($res_sel);

$noofrecords += $customer_count_array['totalCount'];
$customer_count_array['totalCount']=$noofrecords;
if (isset($_POST['display_recs']) and ($_POST['display_recs']!=""))
{
	if (trim($_POST['display_recs']) == 'All')
		$pagesize = $noofrecords;
	else
		$pagesize = $_POST['display_recs'];
}
if ($pagesize==0)
	$pagesize = 50;
//$pagesize = 20;
$total_pages = ceil($noofrecords / $pagesize);

if (empty($_GET['page'])) {
    $page = 1;
} elseif (isset($_GET['page'])) {
    $page = $_GET['page'];
}
$_SESSION[page] = $page;
$offset = (($page - 1) * $pagesize);
////////////////////////////////

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
	$filterAgent = " and ( from_nm='" . $_SESSION['user_login'] . "'";
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
<!DOCTYPE HTML>
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
  		<!-- <script src="js/jquery.js" type="text/javascript" async defer></script>  -->
  		
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
			var auto_interval;
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
						//alert("oops");
					}
				});			
			}
			 
			function memsubmit()
            {
                if (document.all)
                {
                    document.all.default_emplate.action = "searchbuyer.php";
                    document.all.default_emplate.submit();
                } else
                {
                    document.default_emplate.action = "searchbuyer.php";
                    document.default_emplate.submit();
                }
            }
            //$(".loading").removeClass("loading");
		    function submitForm()
            {
                $("#btnChangeSubmit").click();
            }
            
           	$(document).ready(function () {
           		var auto_interval;
				setInterval(startTime_News,1000*60*5);
				setInterval(startTime_Dur, 1000);
				
				//setInterval(getAgent_Activity, 1000*60*15);
				
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
				
				function getAgent_Activity(){
					console.log("every 15 minutes");
				    // check agent event every 15 minutes 
			    	if (document.getElementById("no_event").value==1)
			    	{
						window.location.href="adminlogout.php";	     	
					}else
						document.getElementById("no_event").value=1;
					
				}
				/* get email status that is opened by recepient */
	       	
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
	
				/*================================ Voice call and sms =========================================*/
            	function checkRegexp( o, regexp, n ) {
			      if ( !( regexp.test( o.val() ) ) ) {
			        o.addClass( "ui-state-error" );
		
			        return false;
			      } else {
			        return true;
			      }
			    }
			    
			  
				
				function OnAutoDial(){
					console.log("Auto Dial is process on : async");
					
					
					auto_request=$.ajax({
						type:"POST",
						dataType : "json",
						url : "autodial.php",										
						success : function(res){						
							console.log("auto_dial : succes;");
							console.log(res);
							if (res.calling_phone==null)
							{
								document.getElementById("calling_person").innerHTML = "";
								document.getElementById("calling_person_mobile").innerHTML = "";
								
								setTimeout(OnAutoDial,10);		
							}else
							{
								var calling_info="";
								calling_info = "<a href='buyerinfo2.php?rid="+res.customer_id+"'>"+res.p_fl_nm+"<br>"+res.calling_phone+"</a>";					
								document.getElementById("calling_person").innerHTML = calling_info;			
								document.getElementById("calling_person_mobile").innerHTML = calling_info;							
								setTimeout(OnAutoDial,30*1000);		
							}
						},
						error:function(res)
						{
							console.log("auto_dial : fail;");
							console.log(res);							
						}
					});	
				}
				
				
				$("#auto_dialer_start").on("click",function(){
					console.log("AutoDialStart");
					 
					var chk_ary = document.getElementsByName('sel_customers');
					var customer_id_ary='';
					
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							customer_id_ary += str_ary[2]+';';							
						}	
					}
					
					var data = {"customers":customer_id_ary};
					console.log(data);
					/* make auto dial list */
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "setautodiallist.php",										
						data : data,
						success : function(res){						
							console.log("auto_dial : succes;");
							console.log(res);
							
							//auto_interval = setTimeout(OnAutoDial,30*1000);					
							OnAutoDial();	
						},
						error:function(res)
						{
							console.log("auto_dial : fail;");
							console.log(res);
							
						}
					});	
				});
				
				$("#auto_dialer_start_mobile").on("click",function(){
					console.log("AutoDialStart_Mobile");
					 
					var chk_ary = document.getElementsByName('sel_customers');
					var customer_id_ary='';
					
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							customer_id_ary += str_ary[2]+';';							
						}	
					}
					
					var data = {"customers":customer_id_ary};
					/* make auto dial list */
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "setautodiallist.php",										
						data : data,
						success : function(res){						
							console.log("auto_dial : succes;");
							console.log(res);
							
							auto_interval = setInterval(OnAutoDial,2*60*1000);					
							OnAutoDial();	
						},
						error:function(res)
						{
							console.log("auto_dial : fail;");
							console.log(res);
							
						}
					});	
				});
				
				$("#auto_dialer_stop").on("click",function(){
					console.log("AutoDialStop");
				
					clearInterval(auto_interval);			
				});
				$("#auto_dialer_stop_mobile").on("click",function(){
					console.log("AutoDialStop");
				
					clearInterval(auto_interval);			
				});
				
				$("#auto_dialer_restart_mobile").on("click",function(){
					console.log("AutoDialRestart_Mobile");
				
					clearInterval(auto_interval);	
					
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "autodialrestart.php",						
						success : function(res){						
							console.log("autodialrestart : succes;");
							console.log(res);
							auto_interval = setInterval(OnAutoDial,30*1000);
							
						},
						error:function(res)
						{
							console.log("autodialrestart : fail;");
							console.log(res);								
						}
					});	
				});

				$("#auto_dialer_restart").on("click",function(){
					console.log("AutoDialRestart");
				
					clearInterval(auto_interval);	
					
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "autodialrestart.php",						
						success : function(res){						
							console.log("autodialrestart : succes;");
							console.log(res);
							auto_interval = setInterval(OnAutoDial,30*1000);
							
						},
						error:function(res)
						{
							console.log("autodialrestart : fail;");
							console.log(res);								
						}
					});	
				});

				/*$("#phone_call_btn").on("click", function() {	
					console.log('phone_call click to call');
				  	var chk_ary = document.getElementsByName('sel_customers');
					var phone_ary='';
					var phone='';
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							phone_ary = str_ary[1];	
							phone = phone_ary.split(':');
							break;							
						}	
					}
					console.log(phone);
					var addr = phone.substring(0,phone.length);
					console.log(addr);
					addr = addr.substr(0,3)+ '-'+ addr.substr(3,3)+ '-'+ addr.substr(6,4);
					
					document.getElementById("dialog_phone_number").innerHTML = addr;
        			$("#dialog_phone").modal();        		
					
				});			
				
				$("#phone_call_btn_mobile").on("click", function(){
					var chk_ary = document.getElementsByName('sel_customers');
					var phone_ary='';
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							phone_ary = str_ary[1];		
							phone = phone_ary.split(':');
							break;							
						}	
					}
					var addr = phone.substring(0,phone.length);
					console.log(addr);
					addr = addr.substr(0,3)+ '-'+ addr.substr(3,3)+ '-'+ addr.substr(6,4);
					
					document.getElementById("dialog_phone_number").innerHTML = addr;
        			$("#dialog_phone").modal();        		
				});
				*/
				$("#send_sms_btn").on("click", function() {	
					
				  	var chk_ary = document.getElementsByName('sel_customers');
					var phone_ary='';
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							var customer_phone_ary = str_ary[1].split(':');
							for (j=0;j<customer_phone_ary.length;j++)
							{
								if (customer_phone_ary[j]!="")
									phone_ary += customer_phone_ary[j]+';';			
							}
						}	
					}
					
					//$("#dialog_phone").modal('hide');
					document.getElementById("phone_numbers").value = phone_ary.substring(0,phone_ary.length-1);
					
				  	$("#dialog_sms").modal();							
				});				
				
				$("#send_sms_btn_mobile").on("click", function() {	
				  
				 	var chk_ary = document.getElementsByName('sel_customers');
					var phone_ary='';
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							var customer_phone_ary = str_ary[1].split(':');
							for (j=0;j<customer_phone_ary.length;j++)
							{
								if (customer_phone_ary[j]!="")
									phone_ary += customer_phone_ary[j]+';';			
							}						
						}						
							
					}
					
					//$("#dialog_phone").modal('hide');
					document.getElementById("phone_numbers").value = phone_ary.substring(0,phone_ary.length-1);
					
				  	$("#dialog_sms").modal();										
				});
				/*================================ Email =========================================*/
						   
				$("#send_eml_btn").on("click", function() {		
					var chk_ary = document.getElementsByName('sel_customers');
					console.log(chk_ary.length);
					var email_ary='';
					
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							console.log(str);
							var str_ary = str.split(';');
							console.log(str_ary);
							var customer_email_ary = str_ary[0].split(':');
							for (j=0;j<customer_email_ary.length;j++)
							{
								if (customer_email_ary[j]!="")
									email_ary += customer_email_ary[j]+';';			
							}
													
						}						
							
					}
					
					document.getElementById("email_to").value = email_ary.substring(0,email_ary.length-1);			
					
					$("#dialog-email").modal();
				});
				
				$("#send_eml_btn_mobile").on("click", function() {					
					
					var chk_ary = document.getElementsByName('sel_customers');
					var email_ary='';
					
					for (var i=0;i<chk_ary.length;i++)
					{
						if (chk_ary[i].checked)
						{
							var str = chk_ary[i].value;
							var str_ary = str.split(';');
							var customer_email_ary = str_ary[0].split(':');
							for (j=0;j<customer_email_ary.length;j++)
							{
								if (customer_email_ary[j]!="")
									email_ary += customer_email_ary[j]+';';			
							}				
						}						
							
					}
					
					document.getElementById("email_to").value = email_ary.substring(0,email_ary.length-1);		
					$("#dialog-email").modal();
				});
								
            });       		
       		
       		
       		  
       		/* phone call */
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
	        	
		            var data = {"call_to":addr, "call_type" :"manual_call"};
					console.log(data);
					
					//alert(data);
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "clicktocall.php",
						data : data,
						success : function(res){						
							console.log(res);
							if (res.status == 'Error')
								alert("Phone call is failed");
							else{
								setTimeout(function(){$("#dialog_connecting").modal('hide');},5*1000);											
										
							}
						},
						error:function(res)
						{
							console.log("fail");
							console.log(res);
						//	alert("Phone call is failed");
						}
					});			
				}				
	        }      
	        
	        /* send sms */
	        function ClicktoSMS(addr)
            {
            	console.log("ClicktoSMS");
            	
        		document.getElementById("phone_numbers").value = addr;
        		
            	$("#dialog_phone").modal('hide');
            	
            	document.getElementById("msg_body").innerHTML ='Hello, '+document.getElementById('p_fl_nm').value+'\n';
            	$("#dialog_sms").modal();
				
				
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
			  
       		function sendSMS(){
			   	var valid = true;
			//	valid = valid && checkRegexp(sms_to, /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/, "Phone address only allow : 0-9" );
			
 				if (valid){
					var data = {
						"sms_to":document.getElementById('phone_numbers').value,
						"sms_sal":document.getElementById('msg_sal').value,
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
							//document.getElementById("sms_div").innerHTML = res.sms_sent+"/"+res.sms_recv;	
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
			   
			/* click to call, smsm */
			function ClicktoPhone(ph_num,p_fl_nm)
			{
				console.log("ClicktoPhone");
				document.getElementById("p_fl_nm").value = p_fl_nm;
        		document.getElementById("dialog_phone_number").innerHTML = ph_num;
        		$("#dialog_phone").modal();
			}
			function Select_all_records()
			{
				console.log("Selected all records");
				sel_stat = document.getElementById('selected_status_val').value;
				
				var chk_ary = document.getElementsByName('sel_customers');
				for (var i=0;i<chk_ary.length;i++)
				{
					chk_ary[i].checked = 1-sel_stat;							
				}
				document.getElementById('selected_status_val').value = 1-sel_stat;
				
			}
            function setFilterOnLeads(field, value,real_value)
            {
            	/* Set current session statistics value with value */
            	console.log("setFilterOnLeads");
            	console.log("field :"+field);
            	console.log("value : "+real_value);
            	var data = {"stat_name":value,
            			"stat_val":real_value};
            	
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "setstatisticsvalue.php",	
					data : data,					
					success : function(res)
					{	
						console.log("succ");
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
            	console.log("setFilterOnLeadsOverdue");
            	console.log("field :"+field);
            	console.log("value : "+real_value);
            	var data = {"stat_name":field,
            			"stat_val":real_value};            	
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "setstatisticsvalue.php",	
					data : data,					
					success : function(res)
					{	
						console.log("succ");
					},
					error:function(res)
					{
						console.log("fail" + res);
												
					}
				});				
				
				if (field == 'call')
				{
					console.log('field-call');
					window.open("getNotification_call.php");	
					
				}else if (field == 'email')
				{
					console.log('field-email');
					window.open("getNotification_email.php");	
					
				}else if (field == 'sms')
				{
					console.log('field-sms');
					window.open("getNotification_sms.php");	
				}else
				{
					$("#field_nm").val('');
	                $("#val").val('');
	                $("#" + field).val(value);
	                $("#btnSearch").click();
				}
              
            }
			
			function ChangeSearchValue()
			{
				document.getElementById('val').value = document.getElementById('search_value').value;
				console.log(document.getElementById('val').value);
				$('#btnSearch').click();
			}
			
			/**
			* Change records count per pagge
			*/
			function ChangeDisplayCnt()
			{
				///display_recs
				//	document.getElementById('page_rec').value = document.getElementById('display_recs').value;
				 $("#btnPage").click();
				
				//alert("hello!");
			}
			/**
			* Get Agent Event
			*/
			function MyMouseDown()
			{	
				document.getElementById("no_event").value = 0;
			}
			
			
        </script>
       
    </head>
    <body onmousedown="MyMouseDown()">
    	
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

						<?php
						if ($_SESSION['user_group'] == "Admin") {
						?>
		                  
						<?php
						}
						?>
					</ul>
					<ul class="nav navbar-nav navbar-right" style="margin-top:0px;">									   	
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
					        <!-- Sales : new leads, opened emails, clickthroughs, retry, hot, warm, credit check, credit repair-->
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
					      	<!-- Statistics : Doc.Sent, Pending Funding, Funded, Fee Pending, 30 day funding, 60 day funding, 90 day funding, Clients, Other opportunity -->
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
							<!--<li class="active"><a href="#">Auto Dialer ...<span class="sr-only">(current)</span></a></li>
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
						    		
							</form> -->				
						
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
  			<br>
  			<div id="my-main-content">
  				<!-- Last Login, Duration, ... -->
				<div class="row">					
					<center>
						<div id="tab-container" class="head-box-layer" style="line-height : 1.2;">
		                    <ul class="etabs">                                
		                      	<div> 
		                            <table class="head-box desktop-my-menu" style="background-color: #ca6497;">
		                            	<tr align="center">
		                                  <td>Last Login</td>
		                                </tr>
		                                <tr align="center">
		                             	   <td><?php if (isset($_SESSION['last_logout'])) {
		                                            echo $_SESSION['last_logout'];
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
		                                     <td ><div id="dur_div"><?php if (isset($_SESSION['dur_info'])) {
		                                            echo $_SESSION['dur_info'];
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
		                                    <td >
		                                    	<div id="ratio_div"><?php if (isset($_SESSION['ratio'])) {
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
				
				
        		<!--Search Box-->
        		<br>
        		<div class="row">
					<div class="col-md-offset-3 col-md-6 col-md-offset-3">
						<div class="input-group" id="adv-search" style="display: inline">
			                <input type="text" class="form-control" id="search_value" style="width:70%;height:34px !important" placeholder="Search for" value="<?php
									if (isset($_POST['val'])) {
	                                    echo $_POST['val'];
	                                }?>"/>
			                <div class="input-group-btn">
			                    <div class="btn-group" style="height:34px">
			                    	<button type="button" name="Submit" style="height:34px" onclick="ChangeSearchValue();" class="btn btn-primary"  ><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
			                        <div class="dropdown dropdown-lg">
			                            <button type="button" class="btn btn-default dropdown-toggle" style="height:34px" data-toggle="dropdown" ><span class="caret"></span></button>
			                            <div class="dropdown-menu dropdown-menu-right">
			                                <form class="form-horizontal" id="search_form" method="post" style="padding:10px" action="searchbuyer.php" >
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
					                            <input type="hidden" name="no_follow_up_date" value="0" id="no_follow_up_date" />
					                            <input type="hidden" name="is_opportunity" value="0" id="is_opportunity" />
					                            
					                            <input type="hidden" name="hdnBuyingTimeThirty" value="0" id="hdnBuyingTimeThirty" />
					                            <input type="hidden" name="hdnBuyingTimeSixty" value="0" id="hdnBuyingTimeSixty" />
					                            <input type="hidden" name="hdnBuyingTimeNinety" value="0" id="hdnBuyingTimeNinety" />
					                            <input type="hidden" name="hdnBuyingTimeEighty" value="0" id="hdnBuyingTimeEighty" />
					                            <input type="hidden" name="hdnVolume_Buyers" value="0" id="hdnVolume_Buyers" />
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
			    
				<div class="row">
					<div class="col-md-12 ">
						<table class="table" style="border:0px;margin:20px 0px 0px 0px">
							<thead>
								<tr >
									<td class="col-md-12" align="center" style="padding-right:0px;font-size:20px">Total Records&nbsp;:&nbsp;<?php  echo $noofrecords;?></td>										
								</tr>	
							</thead>
	  					</table>				
					</div>					
			    </div>
			    
			    <div class="row">
					<div class="col-md-12 ">
						<form class="form-horizontal" method="post" action="searchbuyer.php" >
							<table class="table" style="border:0px;margin:0px">
								<thead>
									<tr >
										
										<td class="col-md-9" align="right" style="padding:8px 0px 0px 0px"><button type="button" class="btn btn-success btn-sm " id="send_sms_btn">SMS<span id="sms_news_span" style="visibility:hidden;" class="badge"></span></button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-success btn-sm " id="send_eml_btn">Email<span id="email_new_span" style="visibility:hidden;" class="badge"></span></button></td>
										<td class="col-md-2" align="right" style="padding:13px 0px 0px 0px">Display by :&nbsp;</td>
										<td class="col-md-1" style="padding:8px 0px 0px 0px" >
				                            <select class="form-control" onchange="ChangeDisplayCnt();" id="display_recs" name="display_recs">
													<option value="50" <?php if($_POST['display_recs']=="50"){echo 'selected';}?>>50</option>
				                                    <option value="100" <?php if($_POST['display_recs']=="100"){echo 'selected';}?>>100</option>
				                                    <option value="200" <?php
					                                    if ($_POST['display_recs'] == "200") {
					                                        echo 'selected';
					                                    }
					                                    ?>>200</option>
				                                    <option value="300" <?php
					                                    if ($_POST['display_recs'] == "300") {
					                                        echo 'selected';
					                                    }
					                                    ?>>300</option>
				                                    <option value="400" <?php
				                                        if ($_POST['display_recs'] == "400") {
				                                            echo 'selected';
				                                        }
				                                        ?>>400</option>
				                                    <option value="500" <?php
						                                if ($_POST['display_recs'] == "500") {
						                                    echo 'selected';
						                                }
						                                ?>>500</option>
				                                    <option value="750" <?php 
				                                        if ($_POST['display_recs'] == "750") {
						                                    echo 'selected';
						                                }
						                                ?>>750</option>
				                                    <option value="1000" <?php
					                                    if ($_POST['display_recs'] == "1000") {
					                                        echo 'selected';
					                                    }
					                                    ?>>1000</option>
				                                    <option value="All" <?php
					                                    if ($_POST['display_recs'] == "All") {
					                                        echo 'selected';
					                                    }
					                                    ?>>All</option>				                                    
				                                </select>	
											<button type="submit" style="display:none" class="btn btn-primary" name="Submit" id="btnPage" value="btnPage"></button> 
				                        </td>
									</tr>	
								</thead>
		  					</table>				
	  					</form>
					</div>					
			    </div>
			    
			    
			    
				<div id="my-main-content-left">
					<!-- right navigation bar -->
		           	<div class="my-switch-vscroll desktop-my-menu">
						<!--<div class="switch" style="border-radius:30px;padding:10px 10px;"><span class="glyphicon glyphicon-th-list"></span></div>-->
						<div class="row" style="padding-top:2px;margin-left:2px;margin-right:2px;">
							<div class="col-xs-12" style="padding:1px">								
								<form class="form"  style="margin:2px 2px 2px 4px;" method="post" action="searchbuyer.php">
									<input type="hidden" name="hdnTodaysFolloups" value="0" id="hdnTodaysFolloups" />
		                            <input type="hidden" name="hdnSevenDayOverdue" value="0" id="hdnSevenDayOverdue" />
		                            <input type="hidden" name="hdnThirtyDayOverdue" value="0" id="hdnThirtyDayOverdue" />
		                            <input type="hidden" name="no_follow_up_date" value="0" id="no_follow_up_date" />
		                            <input type="hidden" name="is_opportunity" value="0" id="is_opportunity" />
		                            <input type="hidden" name="hdnBuyingTimeThirty" value="0" id="hdnBuyingTimeThirty" />
		                            <input type="hidden" name="hdnBuyingTimeSixty" value="0" id="hdnBuyingTimeSixty" />
		                            <input type="hidden" name="hdnBuyingTimeNinety" value="0" id="hdnBuyingTimeNinety" />
		                            <input type="hidden" name="hdnBuyingTimeEighty" value="0" id="hdnBuyingTimeEighty" />
		                            <input type="hidden" name="hdnVolume_Buyers" value="0" id="hdnVolume_Buyers" />		
		                            <input type="hidden" name="cur_login_time" value="<?php echo $_SESSION['cur_login_time']; ?>" id="cur_login_time" />                            
		                           
		                            <div class="panel panel-default" style="margin-bottom:5px">
		                            <!--	<div class="panel-heading">Group Texts and Emails</div>
									    <div class="panel-body">
									    	<div class="form-group my-form-group">
									    		<center>
													
													<button type="button" class="btn btn-success btn-sm " id="send_sms_btn">SMS<span id="sms_news_span" style="visibility:hidden;" class="badge"></span></button>
													<button type="button" class="btn btn-success btn-sm " id="send_eml_btn">Email<span id="email_new_span" style="visibility:hidden;" class="badge"></span></button>									    			
									    		</center>
									    	</div>											    						    	
									  	</div>-->
									  	
		                            	
									  <!--	<div class="panel-heading">Auto Dialer </div>
									    <div class="panel-body">
									    	<div class="form-group">
									    		<button type="button" class="btn btn-success btn-md my-sms-button" id="auto_dialer_start">Start</button>			    		
												<button type="button" class="btn btn-success btn-sm my-sms-button" id="auto_dialer_stop">Stop</button>
												<button type="button" class="btn btn-success btn-sm my-sms-button" id="auto_dialer_restart">Restart</button>												
									    	</div>											    						    	
									    	<div class="form-group" style="margin:0px">
												<label style=text-align:left;font-weight:100;" >Calling : <br> </label>
											</div>
											<div class="form-group">
												<div class="col-sm-12">
													<center>
														<span id="calling_person"></span>		
													</center>
												</div>												
									    	</div>
									  	</div>-->
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
            	<div id="my-main-content-right">
            		<div id="my-main-content-right-content">
            			<!-- search result -->
						<div class="row"  style="margin-left:-5px;margin-right:-5px">
							<br>				
								
							<input type="hidden" name="p_fl_nm" id="p_fl_nm" />		
			  				<div class="table-responsive">    
			  					<table class="table table-striped my-main-content-right-table my-table-font" style="margin:0px;text-align:center;" cellpadding="0" cellspacing="0">
			  						<thead>
			      						<tr>
			      							<th style="padding:1px;text-align:center">                                                                    
				                              	<input type="hidden" name="selected_status_val" value="0" id="selected_status_val" />
				                               	<a id="selectall_btn" href="javascript: Select_all_records();">Select</a>
				                              
				                            </th>

				                            <th style="padding:1px;text-align:center"> 
				                                <?php
				                                if ($_GET['name'] == 'd') {
				                                ?>
				                                	<a href="searchbuyer.php?name=a">Name <img src="images/arrow_down.gif"> </a>
				                                <?php
												} else if ($_GET['name'] == 'a') {
												?>
													<a href="searchbuyer.php?name=d">Name <img src="images/arrow_up.gif"> </a>
												<?php
												} else {
												?>
													<a href="searchbuyer.php?name=d">Name </a>
												<?php
												}
												?>
				                            </th>
				                            <th style="padding:1px;text-align:center">Conversation</th>
				                            <th style="padding:1px;text-align:center">Phone</th>
				                            <th style="padding:1px;text-align:center">Email</th>
				                            <th style="padding:1px;text-align:center">
												<?php
				                            	if ($_GET['priority'] == 'd') {
				                                ?>
				                                	<a href="searchbuyer.php?priority=a">Priority <img src="images/arrow_down.gif"> </a>
				                                <?php
					                            } else if ($_GET['priority'] == 'a') {
				                                ?>
				                                	<a href="searchbuyer.php?priority=d">Priority <img src="images/arrow_up.gif"> </a>
												<?php
				                                } else {
				                                ?>
				                                	<a href="searchbuyer.php?priority=d">Priority </a>
				                                <?php
				                                }
				                                ?>
				                            </th>

				                            <th style="padding:1px;text-align:center">
					                            <?php
					                            if ($_GET['follow'] == 'd') {
				                                ?>
				                                	<a href="searchbuyer.php?follow=a">Follow Up <img src="images/arrow_down.gif"> </a>
				                                <?php
					                            } else if ($_GET['follow'] == 'a') {
					                            ?>
					                            	<a href="searchbuyer.php?follow=d">Follow Up <img src="images/arrow_up.gif"> </a>
					                            <?php
					                            } else {
					                            ?>
					                            	<a href="searchbuyer.php?follow=d">Follow Ups </a>
					                            <?php
					                            }
					                            ?>
					                        </th>

				                            
											
				                            <th style="padding:1px;text-align:center">
				                            	<?php
				                                if ($_GET['agent'] == 'd') {
				                                ?>
				                                	<a href="searchbuyer.php?agent=a">Owner<img src="images/arrow_down.gif"> </a>
				                                <?php
				                                } else if ($_GET['agent'] == 'a') {
				                                ?>
				                                	<a href="searchbuyer.php?agent=d">Owner<img src="images/arrow_up.gif"> </a>
				                                <?php
				                                } else {
				                                ?>
				                                	<a href="searchbuyer.php?agent=d">Owner</a>
				                                <?php
				                                }
				                                ?>
				                            </th>
				                            
				                            <th style="padding:1px;text-align:center">
				                             	<?php
				                                if ($_GET['last_mod'] == 'd') {
				                                ?>
				                                	<a href="searchbuyer.php?last_mod=a">Last Upd<img src="images/arrow_down.gif"> </a>
												<?php
				                                }else if ($_GET['last_mod'] == 'a') {
				                                ?>
				                                	<a href="searchbuyer.php?last_mod=d">Last Upd<img src="images/arrow_up.gif"> </a>
												<?php
				                                } else {
				                                ?>
													<a href="searchbuyer.php?last_mod=d">Last Upd</a>
												<?php
				                                }
				                            	?>
				                            </th>
				                            
				                            <th style="padding:1px;text-align:center">
				                                <?php
				                                if ($_GET['stdt'] == 'd') {
				                                ?>
				                                    <a href="searchbuyer.php?stdt=a">Start Date <img src="images/arrow_down.gif"> </a>
												<?php
				                                } else if ($_GET['stdt'] == 'a') {
				                                ?>
				                                	<a href="searchbuyer.php?stdt=d">Start Date <img src="images/arrow_up.gif"> </a>
				                                <?php
				                                } else {
				                                ?>
				                                	<a href="searchbuyer.php?stdt=d">Start Date </a>
				                                <?php
				                                }
				                            ?>
				                            </th>	                            
			      						</tr>
			      					</thead>
			      					<tbody>
			      						<?php
					                    if ($customer_count_array['totalCount'] == 0) 	
					                    {
			                        	?>
			                        		<tr> <td colspan="10">Sorry, No Record Found.</td></tr>
			                        	<?php
					                    } else 
					                    {
											//duplicate record flagging

					                       /* $sqldup = "select * from (select p_ph1,count(p_ph1) cnt from customer_info group by p_ph1) a where a.cnt>1 and length(p_ph1)>1";
					                        $sqldup2 = "select * from (select p_ph2,count(p_ph2) cnt from customer_info group by p_ph2) a where a.cnt>1 and length(p_ph2)>1";
					                        $sqldup3 = "select * from (select p_eml1,count(p_eml1) cnt from customer_info group by p_eml1) a where a.cnt>1 and length(p_eml1)>1";*/
					                       // $sqldup4 = "select * from (select email_2,count(email_2) cnt from customer_info group by email_2) a where a.cnt>1 and length(email_2)>1";

					                        $sql_first_select = "select customer_info.* from customer_info $join customer_info.priority_opt!='Delete' $filter ";
					                        $sql_select = $sql_first_select . $sqlpart . $order . " limit $offset,$pagesize";
					                      
					                        $result = mysql_query($sql_select) or die(mysql_error());
					                        //$_SESSION['sql_query'] = "select auto_id, customer_id from customer_info $join where priority_opt!='Delete' $filter ".$sqlpart;
					                        $_SESSION['sql_order'] = $sqlpart . $filter . $order;
					                        
					                        //while($seerec = mysql_fetch_assoc($res_sel))
					                       // $firstRecordsCount = 0;
					                        while ($seerec = mysql_fetch_assoc($result))
					                        {
					                        	/*$firstRecordsCount = $firstRecordsCount + 1;
			                                    $txtcolr = '';
			                                    $phno = $seerec['p_ph1'];*/
			                                    //$phno= preg_replace("/[^0-9]*/s", "",$phno);
			                                    //$dupres = mysql_query($sqldup) or die(mysql_error() . "go select error");

			                                   // while ($duprec = mysql_fetch_assoc($dupres)) {
			                                   ///    $duprec['a.p_ph1'] = preg_replace("/[^0-9]*/s", "",$duprec['a.p_ph1']);
			                                   /*     if ($duprec['a.p_ph1'] == $phno) {
			                                            $txtcolr = '#FF0000';
			                                        }
			                                    }

			                                    $phno2 = $seerec['p_ph2'];*/
			                                    //$phno2= preg_replace("/[^0-9]*/s", "", $phno2);
			                                    //$dupres2 = mysql_query($sqldup2) or die(mysql_error() . "go select error");

			                                   // while ($duprec2 = mysql_fetch_assoc($dupres2)) {
			                                  //      $duprec2['a.p_ph2'] = preg_replace("/[^0-9]*/s", "",$duprec2['a.p_ph2']);
			                                   /*     if ($duprec2['a.p_ph2'] == $phno2) {
			                                            $txtcolr = '#FF0000';
			                                        }
			                                    }

			                                    $email1 = $seerec['p_eml1'];
			                                    $dupres3 = mysql_query($sqldup3) or die(mysql_error() . "go select error");
			                                    while ($duprec3 = mysql_fetch_assoc($dupres3)) {

			                                        if ($duprec3['a.p_eml1'] == $email1) {

			                                            $txtcolr = '#FF0000';
			                                        }
			                                    }            */                      
			                                   

			                                    $i = $i + 1;
			                                    if ($firstRecordsCount < 15) {
			                                        $_SESSION['firstRecords'] .= '<div style="width:30%;float:left;background:' . $col . ';">' . $seerec['auto_id'] . '</div><div style="background:' . $col . ';width:70%;float:left;"><a href="buyerinfo2.php?rid=' . $seerec['customer_id'] . '">' . $seerec['p_fl_nm'] . '</a></div>';
			                                    }
			                                    ?>

			                                    <tr>
			                                    	<td ><input type="checkbox" id="sel_customers" name="sel_customers" value="<?php echo $seerec['p_eml1'].':'.$seerec['p_eml2'].':'.$seerec['p2_eml'].':'.$seerec['p3_eml'].';'.$seerec['p_ph1'].':'.$seerec['p_ph2'].':'.$seerec['p2_ph1'].':'.$seerec['p2_ph2'].':'.$seerec['p3_ph1'].':'.$seerec['p3_ph2'].';'.$seerec['customer_id']; ?>"></td>

			                                        <td ><label><a href="buyerinfo2.php?rid=<?php echo $seerec['customer_id']; ?>"><?php echo $seerec['p_fl_nm']; ?></a></label></td>
			                                        <td>
			                                           <?php
			                                                $comm_sql = "select substring(out_come,1,27) out_come from conversation_log_info where customer_id='" . $seerec['customer_id'] . "' order by auto_id desc limit 0,1";
															$comm_res = mysql_query($comm_sql) or die(mysql_error() . "11111");
			                                                $comm_rec = mysql_fetch_assoc($comm_res);
			                                                if ($comm_rec['out_come'] != '') {
			                                                    echo stripslashes($comm_rec['out_come']) . "..";
			                                                }
			                                           ?>  
			                                        </td>
			                                          <td >
			                                        
			                                            <?php 
			                                              $str = preg_replace("/[^0-9]*/s", "", $seerec['p_ph1']);
			                                               $show_ph1='';
			                                              if ($str != '')
			                                              {
			                                                $show_ph1='';
			                                                $show_ph1 = substr($str,0,3). '-'. substr($str,3,3). '-'. substr($str,6,4);
			                                                
			                                              }
			                                            ?>
			                                            <a href="javascript: ClicktoPhone('<?php echo $show_ph1;?>','<?php echo $seerec['p_fl_nm']; ?>');"><?php echo $show_ph1;?></a>
			                                        </td>
													<td >
			                                            <?php
			                                                $comm_sql = "select substring(p_eml1,1,6) p_eml1 from customer_info where customer_id='" . $seerec['customer_id'] . "' order by auto_id desc limit 0,1";

			                                                $comm_res = mysql_query($comm_sql) or die(mysql_error() . "11111");

			                                                $comm_rec = mysql_fetch_assoc($comm_res);

			                                                if ($comm_rec['p_eml1'] != '') {

			                                                    echo stripslashes($comm_rec['p_eml1']) . "..";
			                                                }
			                                             ?> 
			                                        </td>
			                                        <?php
			                                        if ($seerec['priority_opt'] == 'Ready'){
			                                    	?>
			                                            <td ><?php echo $seerec['priority_opt']; ?></td>
													<?php
													} else {
													?>
			                                            <td ><?php echo $seerec['priority_opt']; ?></td>
													<?php
													}
													?>
			                                        <td><?php if ($seerec['follow_up'] != '0000-00-00') echo $seerec['follow_up']; ?></td>
			                                      
			                                        
			                                       
			                                        <td><?php echo $seerec['agent']; ?></td> 
			                                        <td>
			                                            <?php
			                                                if (($seerec['cust_upd_dt'] != '') and ($seerec['cust_upd_dt'] != '0000-00-00'))
			                                                    echo $seerec['cust_upd_dt']; 
			                                            ?>
			                                        </td>                                                                           
			                                        <td>
			                                            <?php
			                                                if (($seerec['apply_dt'] != '') and ($seerec['apply_dt'] != '0000-00-00'))
			                                                    echo $seerec['apply_dt']; 
			                                            ?>                                                                                
			                                        </td>
			                                    </tr>

											<?php        															
											}
											?>
					                        <!--tr><td colspan="10" align="center"><?php echo pagination(1, 'searchbuyer.php', $noofrecords, $pagesize, $page, $extra_parameters);?></td></tr-->
										<?php
										}
										?>			
			      					</tbody>
			  					</table>
			  				</div>
			  				<div class="row">
			  					<ul class="pager">
			  						<?php echo pagination(1, 'searchbuyer.php', $noofrecords, $pagesize, $page, $extra_parameters);?>
			  					</ul>  				
			  				</div>
			            </div>
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
		
		
		<!-- email send modal dialog -->
		<div id="dialog-email" class="modal fade" style="z-index:1000000000;display:none;"  title="Send Email">
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
		<!-- email preview dialog -->
        <div id="dialog_preview_email" class="modal fade" style="z-index:1000000001;display:none;" title="">
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
    </body>
   <!-- for google hangout button -->
    <!--<link rel="canonical" href="http://www.example.com" />        
	<script src="https://apis.google.com/js/platform.js" async defer></script>-->
    <!------------------------------->
</html>

		