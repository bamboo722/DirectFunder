<?php

/****-------------------------------------------------------------------**************************    

		Purpose 	: 	Buyer Information Detail Page

		Project 	:	Sales Lead DB	

	 	Developer 	: 	Kelvin Smith

	 	Create Date : 	12/17/2015     

****-------------------------------------------------------------------************************/
session_start();
if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
	header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

if($_POST['Quote'] == "Quote")
{
			  header("Location: sendindivquote.php?rid=".$_GET['rid']);
			  exit();
}

//echo $_SESSION['sql_query'];
//echo $_SESSION['sql_order'];
// $_SESSION['val']
// $_SESSION['field_nm']
// $_SESSION['hdnTodaysFolloups']
// $_SESSION['hdnSevenDayOverdue']
// $_SESSION['hdnThirtyDayOverdue']

$filter = '';

if($_SESSION['hdnTodaysFolloups']==1)
	  {
		$filter = " AND follow_up <= '".date("Y-m-d")."' AND follow_up >= DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) AND follow_up <> '0000-00-00' ";	
	  }
	  
	  if($_SESSION['hdnSevenDayOverdue']==1)
	  {
		$filter = " AND follow_up BETWEEN DATE_SUB(DATE(NOW()), INTERVAL 2 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) AND follow_up <> '0000-00-00' ";	
	  }
	  
	  if($_SESSION['hdnThirtyDayOverdue']==1)
	  {
		$filter = " AND follow_up <= DATE_SUB(DATE(NOW()), INTERVAL 8 DAY) AND follow_up <> '0000-00-00' ";	
	  }
	  
	  if($_SESSION['hdnBuyingTimeThirty']==1)
	  {
		$filter = " AND funding_dt >= DATE( NOW( ) ) AND funding_dt < DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <> '0000-00-00' ";	
	  }
	  
	  if($_SESSION['hdnBuyingTimeSixty']==1)
	  {
		$filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <> '0000-00-00' ";	
	  }
	  
	  if($_SESSION['hdnBuyingTimeNinety']==1)
	  {
		$filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <> '0000-00-00' ";	
	  }
	  
	  if($_SESSION['hdnBuyingTimeEighty']==1)
	  {
		$filter = " AND funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 180 DAY) AND funding_dt <> '0000-00-00' ";	
	  }

if($_POST['priority_opt'] == "Delete")
{
			  $sql_del = "update customer_info set

			  priority_opt='Delete'

			  where customer_id='".$_GET['rid']."'";

			  mysql_query($sql_del) or die(mysql_error());

			  //inserting staff into staff_mast table end		 

			  header("Location: searchbuyer.php");

			  exit();

}



if($_POST['Cancel'] == "Cancel")

{

  header("Location: buyerinfo1.php?rid=".$_GET['rid']."&st=O");

  exit();

}

if($_POST['Done'] == "Done")

{

  header("Location: searchbuyer.php");

  exit();

}



/********************buyer information select start******************************/

				$buyer = "select * from customer_info where customer_id='".$_GET['rid']."'";
		    	$resb = mysql_query($buyer) or die(mysql_error()."11");
				$recb = mysql_fetch_assoc($resb);
				
				// set the i variable to 0 to initialize row counter in the resultset
				mysql_query("SET @i = -1; ") or die(mysql_error());
				
				// get the row count (offset) of the customer_id
				$sql_offset_position = "SELECT POSITION FROM (
				SELECT customer_id, @i:=@i+1 AS POSITION
				FROM customer_info 
				WHERE priority_opt!='Delete' ".$_SESSION['sql_order']."  
			 ) t WHERE customer_id = '".$recb['customer_id']."'; ";
			    $result_offset_position = mysql_query($sql_offset_position) or die(mysql_error());
				$row_offset_position = mysql_fetch_assoc($result_offset_position);
				
				if($row_offset_position['POSITION']>0)
				{
					$sql_previous = "SELECT customer_id FROM customer_info WHERE priority_opt!='Delete' ".$_SESSION['sql_order']." LIMIT ".($row_offset_position['POSITION']-1).",1 ";
					$result_previous = mysql_query($sql_previous) or die(mysql_error());
					$row_previous = mysql_fetch_assoc($result_previous);
				}	
				
				$sql_next = "SELECT customer_id FROM customer_info WHERE priority_opt!='Delete' ".$_SESSION['sql_order']." LIMIT ".($row_offset_position['POSITION']+1).",1 ";
				$result_next = mysql_query($sql_next) or die(mysql_error());
				$row_next = mysql_fetch_assoc($result_next);
				
				$sql_record_count = "SELECT count(*) as totalCount from customer_info WHERE priority_opt!='Delete' ".$_SESSION['sql_order'];
				$result_record_count = mysql_query($sql_record_count) or die(mysql_error());
				$row_record_count = mysql_fetch_assoc($result_record_count);
/********************buyer information select end******************************/

if($_GET['ed']=="T")

{ 

				$log = "select * from conversation_log_info where auto_id='".$_GET['aid']."'";

		    	$resl = mysql_query($log) or die(mysql_error()."11");

				$recl = mysql_fetch_assoc($resl);

}

if($_GET['ed']=="edit")

{ 

				$log = "select * from issues_log_info where auto_id='".$_GET['aid']."'";

		    	$resIssues = mysql_query($log) or die(mysql_error()."11");

				$recIssues = mysql_fetch_assoc($resIssues);

}

if($_POST['Submit']=="Change")

{ 

	if($_POST['priority_opt'] == "Delete")

	{
				  $sql_del = "update customer_info set

				  priority_opt='Delete'

				  where customer_id='".$_GET['rid']."'";

				  mysql_query($sql_del) or die(mysql_error());

				  //inserting staff into staff_mast table end		 

				  header("Location: searchbuyer.php");

				  exit();

	}

	else

	{

		  //checking whether nessary fields are empty 

		  if($_POST['apply_dt']=="")

		  {

		   $msg="Please Enter Date.";

		  }

		  elseif($_POST['f_nm']=="")

		  {

		   $msg="Please Enter First Name.";

		  } 

		  //checking whether nessary fields are empty end

		  else

		  { 

		  

		  				$amt=$_POST['pay1_dt'];

						 if($_POST['pay1_stat']=='T')

						 {

						 	$amt=$_POST['pay2_dt'];

						 }

						  if($_POST['pay2_stat']=='T')

						 {

						 	$amt=$_POST['pay3_dt'];

						 }

						  if($_POST['pay3_stat']=='T')

						 {

						 	$amt='All Paid';

						 }
				
				if(!empty($_POST['need_other_equipment']))
				{
					$need_other_equipment = implode(",",$_POST['need_other_equipment']);
					
				}else
				{
					$need_other_equipment = '';
				}
				
				if($_POST['is_kiosk']!=1)
				  $_POST['is_kiosk'] = 0; 
				
				$sql_upd = "update customer_info set 
					  apply_dt='".$_POST['apply_dt']."',
					  lead_src='".$_POST['lead_src']."',
					  priority_opt='".$_POST['priority_opt']."',
					  f_nm='".$_POST['f_nm']."',
					  l_nm='".$_POST['l_nm']."',
					  b_leg_nm='".$_POST['b_leg_nm']."',
					  p_eml1='".$_POST['p_eml1']."',
					  email_2='".$_POST['email_2']."',
					  p_ph1='".$_POST['p_ph1']."',
					  p_ph2='".$_POST['p_ph2']."',
					  p_hm_addr='".$_POST['p_hm_addr']."',
					  p_city='".$_POST['p_city']."',
					  p_state='".$_POST['p_state']."',
					  p_zip='".$_POST['p_zip']."',
					  p_state = '".$_POST['p_state']."',
					  machine_no='".$_POST['machine_no']."',
					  total_amt='".$_POST['total_amt']."',
					  invoice_dt='".$_POST['invoice_dt']."',
					  invoice_number='".$_POST['invoice_number']."',
					  financing_opt='".$_POST['financing_opt']."',
					  financing_stat='".$_POST['financing_stat']."',
					  pay1_dt='".$_POST['pay1_dt']."',
					  pay1_amt='".$_POST['pay1_amt']."',
					  pay1_stat='".$_POST['pay1_stat']."',
					  pay2_dt='".$_POST['pay2_dt']."',
					  pay2_amt='".$_POST['pay2_amt']."',
					  pay2_stat='".$_POST['pay2_stat']."',
					  pay3_dt='".$_POST['pay3_dt']."',
					  pay3_amt='".$_POST['pay3_amt']."',
					  pay3_stat='".$_POST['pay3_stat']."',
					  pay4_dt='".$_POST['pay4_dt']."',
					  pay4_amt='".$_POST['pay4_amt']."',
					  pay4_stat='".$_POST['pay4_stat']."',
					  pay5_dt='".$_POST['pay5_dt']."',
					  pay5_amt='".$_POST['pay5_amt']."',
					  pay5_stat='".$_POST['pay5_stat']."',
					  pay6_dt='".$_POST['pay6_dt']."',
					  pay6_amt='".$_POST['pay6_amt']."',
					  pay6_stat='".$_POST['pay6_stat']."',
					  pay7_dt='".$_POST['pay7_dt']."',
					  pay7_amt='".$_POST['pay7_amt']."',
					  pay7_stat='".$_POST['pay7_stat']."',
					  pay8_dt='".$_POST['pay8_dt']."',
					  pay8_amt='".$_POST['pay8_amt']."',
					  pay8_stat='".$_POST['pay8_stat']."',
					  pay9_dt='".$_POST['pay9_dt']."',
					  pay9_amt='".$_POST['pay9_amt']."',
					  pay9_stat='".$_POST['pay9_stat']."',
					  pay10_dt='".$_POST['pay10_dt']."',
					  pay10_amt='".$_POST['pay10_amt']."',
					  pay10_stat='".$_POST['pay10_stat']."',
					  pay11_dt='".$_POST['pay11_dt']."',
					  pay11_amt='".$_POST['pay11_amt']."',
					  pay11_stat='".$_POST['pay11_stat']."',
					  pay12_dt='".$_POST['pay12_dt']."',
					  pay12_amt='".$_POST['pay12_amt']."',
					  pay12_stat='".$_POST['pay12_stat']."',
					  etd_dt='".$_POST['etd_dt']."',
					  freight_com_info='".$_POST['freight_com_info']."',
					  shipping_method='".$_POST['shipping_method']."',
					  eta_dt='".$_POST['eta_dt']."',
					  funding_dt='".$_POST['funding_dt']."',
					  next_payment='".$amt."',
					  cust_upd_by='".$_SESSION['user_login']."',
					  cust_upd_dt= curdate(),
					  agent = '".$_POST['user_id']."',
					  machine_types = '".$_POST['machine_types']."',
					  condenser_type = '".$_POST['condenser_type']."',
					  equipment_options = '".$_POST['equipment_options']."',
					  equipment_remark = '".$_POST['equipment_remark']."',
					  existing_new = '".$_POST['existing_new']."',
					  need_free_logo_design = '".$_POST['need_free_logo_design']."',
					  need_free_floor_plan = '".$_POST['need_free_floor_plan']."',
					  need_other_equipment = '".$need_other_equipment."',
					  is_kiosk = '".$_POST['is_kiosk']."',
					  ss_com_name = '".$_POST['ss_com_name']."',
					  ss_contact_person = '".$_POST['ss_contact_person']."',
					  b_ph = '".$_POST['b_ph']."',
					  ss_contact_email = '".$_POST['ss_contact_email']."',
					  ss_technician = '".$_POST['ss_technician']."',
					  ss_tech_phone = '".$_POST['ss_tech_phone']."',
					  ss_tech_email = '".$_POST['ss_tech_email']."',
					  b_fax = '".$_POST['b_fax']."',
					  b_wb_site = '".$_POST['b_wb_site']."',
					  b_addr = '".$_POST['b_addr']."',
					  b_city = '".$_POST['b_city']."',
					  b_state = '".$_POST['b_state']."',
					  b_zip = '".$_POST['b_zip']."',
					  ship_com_name = '".$_POST['ship_com_name']."', 
					  ship_com_phone = '".$_POST['ship_com_phone']."', 
					  ship_con_person = '".$_POST['ship_con_person']."', 
					  ship_ph = '".$_POST['ship_ph']."', 
					  ship_driver_name = '".$_POST['ship_driver_name']."', 
					  ship_driver_ph = '".$_POST['ship_driver_ph']."', 
					  ship_email1 = '".$_POST['ship_email1']."', 
					  ship_email2 = '".$_POST['ship_email2']."', 
					  ship_web = '".$_POST['ship_web']."', 
					  ship_addr = '".$_POST['ship_addr']."', 
					  ship_city = '".$_POST['ship_city']."', 
					  ship_state = '".$_POST['ship_state']."', 
					  ship_zipcode = '".$_POST['ship_zipcode']."', 
					  ship_pickup_date = '".$_POST['ship_pickup_date']."', 
					  ship_pickup_time = '".$_POST['ship_pickup_time']."', 
					  ship_estimated_delivery_date = '".$_POST['ship_estimated_delivery_date']."', 
					  ship_estimated_delivery_time = '".$_POST['ship_estimated_delivery_time']."', 
					  ship_ship_charge = '".$_POST['ship_ship_charge']."', 
					  ship_inv_no = '".$_POST['ship_inv_no']."' 
					  where customer_id ='".$_GET['rid']."'";

					mysql_query($sql_upd) or die(mysql_error());
					
					/*Save conversion log if subject is set*/
					
					if(isset($_POST['log_subject']) and $_POST['log_subject']!='')
					{
						$follow_up = $_POST['next_follow_up'];
						$follow_up = explode("-",$follow_up);
						$follow_up = $follow_up['2'].'-'.$follow_up['0'].'-'.$follow_up['1'];
						mysql_query("set time_zone='-7:00';");
						$sql_ins = "insert into conversation_log_info
								(
								  customer_id,
								  log_time,
								  log_subject,
								  out_come,
								  spoke_to,
								  next_follow_up,
								  record_by,
								  record_dt
								) 
								values
								 (
								 '".$_GET['rid']."',
								 sysdate(),
								 '".clean($_POST['log_subject'])."',
								 '".clean($_POST['out_come'])."',
								 '".clean($_POST['spoke_to'])."',
								 '".clean($follow_up)."',
								 '".$_SESSION['user_login']."',
								 curdate()
								 )";
					
								mysql_query($sql_ins) or die(mysql_error());
				}	
			 
			  $follow_up = $_POST['next_follow_up'];
			  $follow_up = explode("-",$follow_up);
			  $follow_up = $follow_up['2'].'-'.$follow_up['0'].'-'.$follow_up['1'];				
			
			/*update follow up date of buyer mast table*/

			  $mast_upd = "update customer_info set

			  follow_up='".clean($follow_up)."'

			  where customer_id='".$_GET['rid']."'";

			  mysql_query($mast_upd) or die(mysql_error());
					
					// log_subject

					//inserting staff into staff_mast table end		 

					header("Location: buyerinfo1.php?rid=".$_GET['rid']);

					exit();

			}

	}		

}

for($i=1;$i<=13;$i++)
{
	if(isset($_POST['btnSubmitCheckList_'.$i]))
	{
		$date_sent = $_POST['date_'.$i];
		$date_sent = explode("-",$date_sent);
		$date_sent = $date_sent['2'].'-'.$date_sent['0'].'-'.$date_sent['1'];

		$sql_ins = "insert into checklist
			(
			  customer_id,
			  `topic`,
			  `sent_date`,
			  `sent_by`
			) 
			values
			 (
			 '".$_GET['rid']."',
			 ".$i.",
			 '".clean($date_sent)."',
			 '".clean($_POST['sent_by_'.$i])."'
			 )";

			mysql_query($sql_ins) or die(mysql_error());
	}
}

if($_POST['AddIssue']=="Save Issue Log")
{
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
			 '".$_GET['rid']."',
			 CURRENT_TIMESTAMP(),
			 '".clean($_POST['issue'])."',
			 '".clean($_POST['outcome'])."',
			 '".clean($_POST['performed_by'])."'
			 )";

			mysql_query($sql_ins) or die(mysql_error());
}			

if($_POST['Add']=="Save Call Log")
{ 

$follow_up = $_POST['next_follow_up'];
$follow_up = explode("-",$follow_up);
$follow_up = $follow_up['2'].'-'.$follow_up['0'].'-'.$follow_up['1'];
mysql_query("set time_zone='-7:00';");
$sql_ins = "insert into conversation_log_info
			(
			  customer_id,
			  log_time,
			  log_subject,
			  out_come,
			  spoke_to,
			  next_follow_up,
			  record_by,
			  record_dt
			) 
			values
			 (
			 '".$_GET['rid']."',
			 sysdate(),
			 '".clean($_POST['log_subject'])."',
			 '".clean($_POST['out_come'])."',
			 '".clean($_POST['spoke_to'])."',
			 '".clean($follow_up)."',
			 '".$_SESSION['user_login']."',
			 curdate()
			 )";

			mysql_query($sql_ins) or die(mysql_error());
			
			/*update follow up date of buyer mast table*/

			  $mast_upd = "update customer_info set

			  follow_up='".clean($_POST['next_follow_up'])."'

			  where customer_id='".$_GET['rid']."'";

			  mysql_query($mast_upd) or die(mysql_error());

			/*update follow up date of buyer mast table end */

			

			//inserting staff into staff_mast table end		 

			header("Location: buyerinfo1.php?rid=".$_GET['rid']);

			exit();

}

if($_POST['EditIssue']=="Edit Issue Log")
{ 
	$log_upd = "update issues_log_info set
			  outcome='".$_POST['outcome']."',
			  performed_by='".$_POST['performed_by']."',
			  issue='".$_POST['issue']."' 
			  where auto_id='".$_GET['aid']."'";

			mysql_query($log_upd) or die(mysql_error());

			//inserting staff into staff_mast table end		 

			header("Location: buyerinfo1.php?rid=".$_GET['rid']."&st=O");

			exit();
}


if($_POST['Edit']=="Edit Log")
{ 
		$follow_up = $_POST['next_follow_up'];
		$follow_up = explode("-",$follow_up);
		$follow_up = $follow_up['2'].'-'.$follow_up['0'].'-'.$follow_up['1'];
		mysql_query("set time_zone='-7:00';");
		$log_upd = "update conversation_log_info set

			  log_time=sysdate(),
			  log_subject='".$_POST['log_subject']."',
			  out_come='".$_POST['out_come']."',
			  spoke_to='".$_POST['spoke_to']."',
			  next_follow_up='".$follow_up."',
			  update_by='".$_SESSION['user_login']."',
			  update_dt=curdate()
			  where auto_id='".$_GET['aid']."'";

			mysql_query($log_upd) or die(mysql_error());

			//inserting staff into staff_mast table end		 

			header("Location: buyerinfo1.php?rid=".$_GET['rid']."&st=O");

			exit();

}

$filterAgent = '';

if($_SESSION['user_group']=='Agent')
$filterAgent = " and agent='".$_SESSION['user_login']."'";

$sql_follow_up = "SELECT count(distinct customer_id) as followUpCount from customer_info where follow_up <= '".date("Y-m-d")."' AND follow_up >= DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) AND priority_opt!='Delete' AND  follow_up <> '0000-00-00' $filterAgent ";
$result_followup = mysql_query($sql_follow_up) or die(mysql_error());
$followup_count_array = mysql_fetch_assoc($result_followup);

$sql_seven_overdue = "SELECT count(distinct customer_id) as overdueCount from customer_info where follow_up BETWEEN DATE_SUB(DATE(NOW()), INTERVAL 2 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) AND follow_up <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_seven_overdue = mysql_query($sql_seven_overdue) or die(mysql_error());
$sevenoverdue_count_array = mysql_fetch_assoc($result_seven_overdue);

$sql_thirty_overdue = "SELECT count(distinct customer_id) as overdueCount from customer_info where follow_up <= DATE_SUB(DATE(NOW()), INTERVAL 8 DAY) AND follow_up <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_thirty_overdue = mysql_query($sql_thirty_overdue) or die(mysql_error());
$thirty_count_array = mysql_fetch_assoc($result_thirty_overdue);

// Funding Time within 30 days
$sql_buying_time_thirty = "SELECT count(distinct customer_id) as dueCount from customer_info where funding_dt >= DATE( NOW( ) ) AND funding_dt < DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_thirty = mysql_query($sql_buying_time_thirty) or die(mysql_error());
$buying_time_thirty_count_array = mysql_fetch_assoc($result_buying_time_thirty);

// Funding Time within 31-60 days
$sql_buying_time_thirty_sixty = "SELECT count(distinct customer_id) as dueCount from customer_info where funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 30 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_thirty_sixty = mysql_query($sql_buying_time_thirty_sixty) or die(mysql_error());
$buying_time_thirty_sixty_count_array = mysql_fetch_assoc($result_buying_time_thirty_sixty);

// Funding Time within 61-90 days
$sql_buying_time_sixty_ninety = "SELECT count(distinct customer_id) as dueCount from customer_info where funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 60 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_sixty_ninety = mysql_query($sql_buying_time_sixty_ninety) or die(mysql_error());
$buying_time_sixty_ninety_count_array = mysql_fetch_assoc($result_buying_time_sixty_ninety);

// Funding Time within 91-180 days
$sql_buying_time_ninety_eighty = "SELECT count(distinct customer_id) as dueCount from customer_info where funding_dt > DATE_ADD(DATE(NOW()), INTERVAL 90 DAY) AND funding_dt <=  DATE_ADD(DATE(NOW()), INTERVAL 180 DAY) AND funding_dt <> '0000-00-00' AND priority_opt!='Delete' $filterAgent ";
$result_buying_time_ninety_eighty = mysql_query($sql_buying_time_ninety_eighty) or die(mysql_error());
$buying_time_ninety_eighty_count_array = mysql_fetch_assoc($result_buying_time_ninety_eighty);

//$sql_sum = "SELECT SUM(CASE WHEN priority_opt = 'Retry 3' THEN 1 ELSE 0 END) As retry3Leads, SUM(CASE WHEN priority_opt = 'Retry 2' THEN 1 ELSE 0 END) As retry2Leads, SUM(CASE WHEN priority_opt = 'New' THEN 1 ELSE 0 END) As newLeads,SUM(CASE WHEN priority_opt = 'Retry' THEN 1 ELSE 0 END) As retryLeads,SUM(CASE WHEN priority_opt = 'Ready' THEN 1 ELSE 0 END) As readyLeads, SUM(CASE WHEN (priority_opt = 'Hot' ) THEN 1 ELSE 0 END) As hotLeads,SUM(CASE WHEN priority_opt = 'Warm' THEN 1 ELSE 0 END) As warmLeads,SUM(CASE WHEN priority_opt = 'Cold' THEN 1 ELSE 0 END) As coldLeads, SUM(CASE WHEN priority_opt = 'Customer' THEN 1 ELSE 0 END) As customers, SUM(CASE WHEN priority_opt = 'Must Close' THEN 1 ELSE 0 END) As mustClose,SUM(CASE WHEN financing_opt = 'Lease' THEN 1 ELSE 0 END) As financing, SUM(CASE WHEN priority_opt = 'Retry' THEN 1 ELSE 0 END) As retryLeads from customer_info WHERE 1 $filterAgent ";
$sql_sum = "SELECT SUM(CASE WHEN priority_opt = 'New' THEN 1 ELSE 0 END) As newLeads,SUM(CASE WHEN priority_opt = 'Retry' THEN 1 ELSE 0 END) As retryLeads,SUM(CASE WHEN (apply_dt = now() ) THEN 1 ELSE 0 END) As today_task, SUM(CASE WHEN (priority_opt = 'Hot' ) THEN 1 ELSE 0 END) As hotLeads,SUM(CASE WHEN priority_opt = 'Warm' THEN 1 ELSE 0 END) As warmLeads,SUM(CASE WHEN priority_opt = 'Credit Check' THEN 1 ELSE 0 END) As credit_checks,SUM(CASE WHEN priority_opt = 'Credit Repair' THEN 1 ELSE 0 END) As credit_repairs,SUM(CASE WHEN priority_opt = 'Doc. Sent' THEN 1 ELSE 0 END) As doc_sents,SUM(CASE WHEN priority_opt = 'Funded' THEN 1 ELSE 0 END) As fundedLeads, SUM(CASE WHEN priority_opt = 'Pending Funding' THEN 1 ELSE 0 END) As pending_fundings, SUM(CASE WHEN priority_opt = 'Fee Pending' THEN 1 ELSE 0 END) As fee_pendings, SUM(CASE WHEN priority_opt = 'Pre-approved' THEN 1 ELSE 0 END) As pre_approveds,SUM(CASE WHEN priority_opt = 'Funded' THEN 1 ELSE 0 END) As fundedLeads, SUM(CASE WHEN priority_opt = 'Clients' THEN 1 ELSE 0 END) As clients,SUM(CASE WHEN priority_opt = 'Fee Pending' THEN 1 ELSE 0 END) As fee_pending from customer_info WHERE 1 $filterAgent ";
$result_sum = mysql_query($sql_sum) or die(mysql_error());
$sum_array = mysql_fetch_assoc($result_sum);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/css.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.threecolumns{ width:30%; float:left; line-height:12px; }
.twocolumns{ width:49%; float:left; text-align:left; font-size:12px; font-family:Tahoma; margin-left:2px; }
h5{ margin:0px; text-align:left; }
.button_medium{ font-size:14px !important; }
.textbox_small{ width:120px !important; }
.textbox{ margin-bottom:5px; }
.main_txt{ font-size:10px !important; }
.etabs { margin: 0; padding: 0; }
    .tab { display: inline-block; zoom:1; *display:inline; background: #eee; border: solid 1px #999; border-bottom: none; -moz-border-radius: 4px 4px 0 0; -webkit-border-radius: 4px 4px 0 0; }
    .tab a { font-size: 14px; line-height: 2em; display: block; padding: 0 10px; outline: none; }
    .tab a:hover { text-decoration: underline; }
    .tab.active { background: #fff; padding-top: 6px; position: relative; top: 1px; border-color: #666; }
    .tab a.active { font-weight: bold; }
    .tab-container .panel-container { background: #fff; border: solid #29216D 2px; padding: 0px; -moz-border-radius: 0 4px 4px 4px; -webkit-border-radius: 0 4px 4px 4px; }
    .panel-container { margin-bottom: 10px; float:left; }
	.slide-out-div p{ margin:0px; }
	.slide-out-div .head{ background:#eeeeee; margin:-10px -10px 5px; padding-left:5px; font-weight:bold; }
	.sectiontitle{ padding:0px; margin:5px; text-align:left; }
</style> 

<script type="text/javascript">

function wopen(url, name, w, h)

{

// Fudge factors for window decoration space.

 // In my tests these work well on all platforms & browsers.

w += 120;

h += 80;

 var win = window.open(url,  name,   'width=' + w + ', height=' + h + ', location=no, menubar=no, status=no, toolbar=no, scrollbars=no, resizable=no, left=300');

}	



function wopen2(url, name, w, h)

{

// Fudge factors for window decoration space.

 // In my tests these work well on all platforms & browsers.

w += 120;

h += 80;

 var win = window.open(url,  name,   'width=' + w + ', height=' + h + ', location=no, menubar=no, status=no, toolbar=no, scrollbars=no, resizable=no, left=300');

}			

function setFilterOnLeads(field, value)
{
	$("#field_nm").val(field);
	$("#val").val(value);
	$("#btnSearch").click();
}

function setFilterOnLeadsOverdue(field, value)
{
	$("#field_nm").val('');
	$("#val").val('');
	$("#"+field).val(value);
	$("#btnSearch").click();
}
</script>
<link href="css/css.css" rel="stylesheet" type="text/css"/>
<link href="css/layout.css" rel="stylesheet" type="text/css"/>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>-->
<script src="jss/jquery-1.7.1.min.js" type="text/javascript"></script> 
<script src="jss/jquery.hashchange.min.js" type="text/javascript"></script>
<script src="jss/jquery.easytabs.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready( function() {
      $('#tab-container').easytabs();
	  $("#ship_pickup_time").timePicker();
	  $("#ship_estimated_delivery_time").timePicker();
    });
  </script>
<style type="text/css">
/*----------------Pagination ----------------------------------*/
div.pagination
{
	padding: 3px;
	margin: 3px;
}

/*div.pagination a
{
	padding: 2px 5px 2px 5px;
	margin: 2px;
	border: 1px solid #AAAADD;
	
	text-decoration: none; 
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
}*/
/*div.pagination span.disabled
{
	padding: 2px 5px 2px 5px;
	margin: 2px;
	border: 1px solid #970A00;
	color: #970A00;
}*/
</style>
<style type="text/css">
.slide-out-div {
padding: 10px;
width: 150px;
background: #FFFFFF;
border: #29216d 2px solid;
z-index:999;
} 

.button {
    background-color: #006699;
    border: 0 solid #A0A0A0;
    border-radius: 4px;
    color: #FFFFFF;
    font-family: calibri,verdana,tahoma,arial;
    font-size: 12px;
    font-weight: bold;
    height: 35px;
    text-decoration: none;
    width: 50px;
	margin-top:5px;
}
</style>
</head>
<body>
<script language="JavaScript" src="popcalendar.js"></script>
<style type="text/css" media="all">@import "timePicker/timePicker.css";</style>
<script type="text/javascript" src="timePicker/jquery.timePicker.js"></script>

<table width="100%">
  <tr>
    <td width="12%" valign="top">
    
    
    <form name="frmSearch" method="post" action="searchbuyer.php">
    
    <input style="width:175px;" type="text" name="val" id="val" value="<?php if(isset($_POST['val'])){echo $_POST['val'];}?>"  class="textbox">
    
<select id="field_nm" name="field_nm" size="1" class="textbox" style="width:175px;">

                                  <option value=""></option>

                                  <option value="f_nm" <?php if($_POST['field_nm']=="f_nm"){echo 'selected';}?>>First 

                                  Name</option>

                                  <option value="l_nm" <?php if($_POST['field_nm']=="l_nm"){echo 'selected';}?>>Last 

                                  Name</option>

                                  <option value="apply_dt" <?php if($_POST['field_nm']=="apply_dt"){echo 'selected';}?>>Start 

                                  Date (MM-DD-YYYY)</option>

                                  <option value="priority_opt" <?php if($_POST['field_nm']=="priority_opt"){echo 'selected';}?>>Priority Option</option>

								    <option value="lead_src" <?php if($_POST['field_nm']=="lead_src"){echo 'selected';}?>>Lead Source</option>

                                  <option value="machine_no" <?php if($_POST['field_nm']=="machine_no"){echo 'selected';}?>>Number of Machine</option>

								  <option value="p_eml1" <?php if($_POST['field_nm']=="p_eml1"){echo 'selected';}?>>Email Address 1</option>

								   <option value="email_2" <?php if($_POST['field_nm']=="email_2"){echo 'selected';}?>>Email Address 2</option>

								   <option value="p_ph1" <?php if($_POST['field_nm']=="p_ph1"){echo 'selected';}?>>Phone 1</option>

								    <option value="p_ph2" <?php if($_POST['field_nm']=="p_ph2"){echo 'selected';}?>>Phone 2</option>

									 <option value="p_hm_addr" <?php if($_POST['field_nm']=="p_hm_addr"){echo 'selected';}?>>Buyer Address</option>

									  <option value="p_city" <?php if($_POST['field_nm']=="p_city"){echo 'selected';}?>>Buyer City</option>

									 <option value="p_zip" <?php if($_POST['field_nm']=="p_zip"){echo 'selected';}?>>Buyer ZIP</option>

									  <option value="invoice_dt" <?php if($_POST['field_nm']=="invoice_dt"){echo 'selected';}?>>Invoice Date (MM-DD-YYYY)</option>

									   <option value="invoice_number" <?php if($_POST['field_nm']=="invoice_number"){echo 'selected';}?>>Invoice Number</option>

									    <option value="financing_opt" <?php if($_POST['field_nm']=="financing_opt"){echo 'selected';}?>>Financing Option</option>

										 <option value="financing_stat" <?php if($_POST['field_nm']=="financing_stat"){echo 'selected';}?>>Financing Status</option>

									<option value="etd_dt" <?php if($_POST['field_nm']=="etd_dt"){echo 'selected';}?>>ETD (MM-DD-YYYY)</option>

										<option value="eta_dt" <?php if($_POST['field_nm']=="eta_dt"){echo 'selected';}?>>ETA (MM-DD-YYYY)</option>

									<option value="shipping_method" <?php if($_POST['field_nm']=="shipping_method"){echo 'selected';}?>>Shipping Way</option>

								<option value="funding_dt" <?php if($_POST['field_nm']=="funding_dt"){echo 'selected';}?>>Funding Time(MM-DD-YYYY)</option>



                                </select>
                                
                               <input id="btnSearch" type="submit" name="Submit" value="Search" class="button"> 

                           <input type="submit" name="Reset" value="Reset" class="button">

                          <input type="submit" name="Add" value="Add Buyer" class="button" style="width:60px;">

                        

<input type="hidden" name="hdnTodaysFolloups" value="0" id="hdnTodaysFolloups" />
<input type="hidden" name="hdnSevenDayOverdue" value="0" id="hdnSevenDayOverdue" />
<input type="hidden" name="hdnThirtyDayOverdue" value="0" id="hdnThirtyDayOverdue" />
<input type="hidden" name="hdnBuyingTimeThirty" value="0" id="hdnBuyingTimeThirty" />
<input type="hidden" name="hdnBuyingTimeSixty" value="0" id="hdnBuyingTimeSixty" />
<input type="hidden" name="hdnBuyingTimeNinety" value="0" id="hdnBuyingTimeNinety" />
<input type="hidden" name="hdnBuyingTimeEighty" value="0" id="hdnBuyingTimeEighty" />
</form>
    
    <div class="slide-out-div">
  	     <a href="http://link-for-non-js-users" class="handle" style="background: url(&quot;img/contact_tab.gif&quot;) no-repeat scroll 0% 0% transparent; width: 40px; height: 122px; display: block; text-indent: -99999px; outline: medium none; position: absolute; top: 0px; right: -40px;">Content</a>
        
        <p class="head">Statistics</p>
          <p>New Leads:
<?php if ($sum_array['newLeads'] > 0) {?>
                                                <a href="#" onClick="setFilterOnLeads('priority_opt', 'New'); return false;">
    <?= $sum_array['newLeads'] ?>
                                                </a>
<?php } else { ?>
    <?= $sum_array['newLeads'] ?>
<?php } ?>
                                        </p>
                                        
                                         <p>Retry :
                                            <?php if ($sum_array['retryLeads'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Retry');
                                                                return false;">
                                                <?= $sum_array['retryLeads'] ?>
                                                    </a>
                                            <?php } else { ?>
                                                <?= $sum_array['retryLeads'] ?>
                                            <?php } ?>
                                            </p>
                                            
                                            <p>Today's Task:
                                                <?php if ($followup_count_array['followUpCount'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeadsOverdue('hdnTodaysFolloups', '1');
                                                                return false;">
                                                    <?= $followup_count_array['followUpCount'] ?>
                                                    </a>
                                                   <?php } else { ?>
                                                           <?= $followup_count_array['followUpCount'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p>Past Due:
                                                <?php if ($sevenoverdue_count_array['overdueCount'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeadsOverdue('hdnSevenDayOverdue', '1');
                                                                return false;">
                                                    <?= $sevenoverdue_count_array['overdueCount'] ?>
                                                    </a>
                                                   <?php } else { ?>
                                                           <?= $sevenoverdue_count_array['overdueCount'] ?>
                                                <?php } ?>
                                            </p>

                                            <p>Delinquent:
<?php if ($thirty_count_array['overdueCount'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeadsOverdue('hdnThirtyDayOverdue', '1');
                                                                return false;">
                                                       <?= $thirty_count_array['overdueCount'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $thirty_count_array['overdueCount'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p>Hot:
<?php if ($sum_array['hotLeads'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Hot');
                                                                return false;">
                                                       <?= $sum_array['hotLeads'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['hotLeads'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p> Warm:
 <?php if ($sum_array['warmLeads'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Warm');
                                                                return false;">
                                                       <?= $sum_array['warmLeads'] ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?= $sum_array['warmLeads'] ?>
                                                <?php } ?>
                                            </p>

                                            <p>Credit Check:
<?php if ($sum_array['credit_checks'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Credit Check');
                                                                return false;">
                                                       <?= $sum_array['credit_checks'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['credit_checks'] ?>
                                                <?php } ?>
                                            </p>

                                            <p>Credit Repair:
<?php if ($sum_array['credit_repairs'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Credit Repair');
                                                                return false;">
                                                       <?= $sum_array['credit_repairs'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['credit_repairs'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p>Pre-approved:
<?php if ($sum_array['pre_approveds'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Pre-approved');
                                                                return false;">
                                                       <?= $sum_array['pre_approveds'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['pre_approveds'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p>Doc. Sent:
<?php if ($sum_array['doc_sents'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Doc. Sent');
                                                                return false;">
                                                       <?= $sum_array['doc_sents'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['doc_sents'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                           <p>Pending Funding :
<?php if ($sum_array['pending_fundings'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Pending Funding');
                                                                return false;">
                                                       <?= $sum_array['pending_fundings'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['pending_fundings'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p>Funded :
<?php if ($sum_array['fundedLeads'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Funded');
                                                                return false;">
                                                       <?= $sum_array['fundedLeads'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['fundedLeads'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                            <p>Fee Pending :
<?php if ($sum_array['fee_pending'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Fee Pending');
                                                                return false;">
                                                       <?= $sum_array['fee_pending'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['fee_pending'] ?>
                                                <?php } ?>
                                            </p>
                                            
                                         
                                          
                                          
                                            <p>30 day funding :
<?php if ($buying_time_thirty_count_array['dueCount'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeThirty', '1');
                                                                return false;">
                                                <?= $buying_time_thirty_count_array['dueCount'] ?>
                                                    </a>
                                            <?php } else { ?>
                                                <?= $buying_time_thirty_count_array['dueCount'] ?>
                                            <?php } ?>
                                            </p>
                                            
                                            <p>60 day funding:
                                            <?php if ($buying_time_thirty_sixty_count_array['dueCount'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeSixty', '1');
                                                                return false;">
    <?= $buying_time_thirty_sixty_count_array['dueCount'] ?>
                                                    </a>
<?php } else { ?>
    <?= $buying_time_thirty_sixty_count_array['dueCount'] ?>
<?php } ?>
                                            </p>
                                            
                                            <p>90 day funding:
<?php if ($buying_time_sixty_ninety_count_array['dueCount'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeNinety', '1');
                                                                return false;">
    <?= $buying_time_sixty_ninety_count_array['dueCount'] ?>
                                                    </a>
<?php } else { ?>
    <?= $buying_time_sixty_ninety_count_array['dueCount'] ?>
<?php } ?>
                                            </p>
                                            
                                            <p>Clients:
<?php if ($sum_array['clients'] > 0) { ?>
                                                    <a href="#" onClick="setFilterOnLeads('priority_opt', 'Clients');
                                                                return false;">
                                                       <?= $sum_array['clients'] ?>
                                                    </a>
                                                       <?php } else { ?>
                                                    <?= $sum_array['clients'] ?>
                                                <?php } ?>
                                            </p>
                                            
        
       <?php /* <p>Must Close:	  <?php if ($sum_array['mustClose']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Must Close'); return false;"><?=$sum_array['mustClose']?></a><?php }else{ ?><?=$sum_array['mustClose']?><?php } ?></p> 
        <p>New Leads:	<?php if ($sum_array['newLeads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','New'); return false;"><?=$sum_array['newLeads']?></a><?php }else{ ?><?=$sum_array['newLeads']?><?php } ?></p>
        <p>Today's follow ups:	<?php if ($followup_count_array['followUpCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnTodaysFolloups','1'); return false;"><?=$followup_count_array['followUpCount']?></a><?php }else{ ?><?=$followup_count_array['followUpCount']?><?php } ?></p>
        <p>Past Due:	<?php if ($sevenoverdue_count_array['overdueCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnSevenDayOverdue','1'); return false;"><?=$sevenoverdue_count_array['overdueCount']?></a><?php }else{ ?><?=$sevenoverdue_count_array['overdueCount']?><?php } ?></p>
        <p>Delinquent:	<?php if ($thirty_count_array['overdueCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnThirtyDayOverdue','1'); return false;"><?=$thirty_count_array['overdueCount']?></a><?php }else{ ?><?=$thirty_count_array['overdueCount']?><?php } ?></p>
        <p>Ready Leads:	<?php if ($sum_array['readyLeads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Ready'); return false;"><?=$sum_array['readyLeads']?></a><?php }else{ ?><?=$sum_array['readyLeads']?><?php } ?></p>
        <p>Hot Leads:	<?php if ($sum_array['hotLeads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Hot'); return false;"><?=$sum_array['hotLeads']?></a><?php }else{ ?><?=$sum_array['hotLeads']?><?php } ?></p>
        <p>Retry Leads:	<?php if ($sum_array['retryLeads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Retry'); return false;"><?=$sum_array['retryLeads']?></a><?php }else{ ?><?=$sum_array['retryLeads']?><?php } ?></p>
                <p>Retry 2 Leads:	<?php if ($sum_array['retry2Leads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Retry 2'); return false;"><?=$sum_array['retry2Leads']?></a><?php }else{ ?><?=$sum_array['retry2Leads']?><?php } ?></p>

        <p>Retry 3 Leads:	<?php if ($sum_array['retry3Leads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Retry 3'); return false;"><?=$sum_array['retry3Leads']?></a><?php }else{ ?><?=$sum_array['retry3Leads']?><?php } ?></p>
        <p>Warm Leads:	<?php if ($sum_array['warmLeads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Warm'); return false;"><?=$sum_array['warmLeads']?></a><?php }else{ ?><?=$sum_array['warmLeads']?><?php } ?></p>
        <p>Cold Leads:	<?php if ($sum_array['coldLeads']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Cold'); return false;"><?=$sum_array['coldLeads']?></a><?php }else{ ?><?=$sum_array['coldLeads']?><?php } ?></p>
        <p>Customers:	<?php if ($sum_array['customers']>0){ ?><a href="#" onClick="setFilterOnLeads('priority_opt','Customer'); return false;"><?=$sum_array['customers']?></a><?php }else{ ?><?=$sum_array['customers']?><?php } ?></p>
        <p>Financing:	  <?php if ($sum_array['financing']>0){ ?><a href="#" onClick="setFilterOnLeads('financing_opt','Lease'); return false;"><?=$sum_array['financing']?></a><?php }else{ ?><?=$sum_array['financing']?><?php } ?></p>
        <p>30 day Funding Time: <?php if ($buying_time_thirty_count_array['dueCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeThirty','1'); return false;"><?=$buying_time_thirty_count_array['dueCount']?></a><?php }else{ ?><?=$buying_time_thirty_count_array['dueCount']?><?php } ?></p>
        <p>60 day Funding Time: <?php if ($buying_time_thirty_sixty_count_array['dueCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeSixty','1'); return false;"><?=$buying_time_thirty_sixty_count_array['dueCount']?></a><?php }else{ ?><?=$buying_time_thirty_sixty_count_array['dueCount']?><?php } ?></p>
        <p>90 day Funding Time: <?php if ($buying_time_sixty_ninety_count_array['dueCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeNinety','1'); return false;"><?=$buying_time_sixty_ninety_count_array['dueCount']?></a><?php }else{ ?><?=$buying_time_sixty_ninety_count_array['dueCount']?><?php } ?></p>
        <p>180 day Funding Time: <?php if ($buying_time_ninety_eighty_count_array['dueCount']>0){ ?><a href="#" onClick="setFilterOnLeadsOverdue('hdnBuyingTimeEighty','1'); return false;"><?=$buying_time_ninety_eighty_count_array['dueCount']?></a><?php }else{ ?><?=$buying_time_ninety_eighty_count_array['dueCount']?><?php } ?></p>
         */?>
        
    </div>
    
    <div style="width:95%;border:1px solid #CCCCCC; height:350px; padding-left:5px; margin-top:5px;">
    
    <?=$_SESSION['firstRecords']?> 
   
    </div>
    
    </td>
    <td align="right"><table width="100%">
        <!--heading space-->
        <tr>
        <?php /*?>  <td height="100px"><?php include("header.php");?>
          </td>
        </tr><?php */?>
        <!--heading space end-->
        <!--header menu space-->
        <tr>
          <td height="50px" class="up_menu"><div align="center" class="hyperlink">
              <?php 

include("admheadmenu.php");

?>
            </div></td>
        </tr>
        <!--header menu space end-->
        
        <tr>
          <td height="473" valign="top" align="center"><form name="default_emplate" id="default_emplate" method="post"  enctype="multipart/form-data">
          
          <div style="float:left; width:100%;">
          
          <div class="threecolumns">
          
          	<div class="twocolumns">Priority: <select name="priority_opt" size="1" class="textbox_small">
                            <option value="New" <?php if($_POST['priority_opt']=="New"){echo 'selected';}else if($recb['priority_opt']=="New"){echo 'selected';}?>>New</option>
                            <option value="Retry" <?php if($_POST['priority_opt']=="Retry"){echo 'selected';}else if($recb['priority_opt']=="Retry"){echo 'selected';}?>>Retry</option>
                             <option value="Retry 2" <?php if($_POST['priority_opt']=="Retry 2"){echo 'selected';}else if($recb['priority_opt']=="Retry 2"){echo 'selected';}?>>Retry 2</option>
                              <option value="Retry 3" <?php if($_POST['priority_opt']=="Retry 3"){echo 'selected';}else if($recb['priority_opt']=="Retry 3"){echo 'selected';}?>>Retry 3</option>
                            <option value="Cold" <?php if($_POST['priority_opt']=="Cold"){echo 'selected';}else if($recb['priority_opt']=="Cold"){echo 'selected';}?>>Cold</option>
                            <option value="Warm" <?php if($_POST['priority_opt']=="Warm"){echo 'selected';}else if($recb['priority_opt']=="Warm"){echo 'selected';}?>>Warm</option>
                            <option value="Hot" <?php if($_POST['priority_opt']=="Hot"){echo 'selected';}else if($recb['priority_opt']=="Hot"){echo 'selected';}?>>Hot</option>
                            <option value="Ready" <?php if($_POST['priority_opt']=="Ready"){echo 'selected';}else if($recb['priority_opt']=="Ready"){echo 'selected';}?> class="alert_msg">Ready</option>
                            <option value="Customer" <?php if($_POST['priority_opt']=="Customer"){echo 'selected';}else if($recb['priority_opt']=="Customer"){echo 'selected';}?>>Customer</option>
                            <option value="Partners" <?php if($_POST['priority_opt']=="Partners"){echo 'selected';}else if($recb['priority_opt']=="Partners"){echo 'selected';}?>>Partners</option>
                            <option value="Bought Others" <?php if($_POST['priority_opt']=="Bought Others"){echo 'selected';}else if($recb['priority_opt']=="Bought Others"){echo 'selected';}?>>Bought Others</option>
                            <option value="Inactive" <?php if($_POST['priority_opt']=="Inactive"){echo 'selected';}else if($recb['priority_opt']=="Inactive"){echo 'selected';}?>>Inactive</option>
                            <option value="Delete" <?php if($_POST['priority_opt']=="Delete"){echo 'selected';}else if($recb['priority_opt']=="Delete"){echo 'selected';}?>>Delete</option>
                            <option value="Must Close" <?php if($_POST['priority_opt']=="Must Close"){echo 'selected';}else if($recb['priority_opt']=="Must Close"){echo 'selected';}?>>Must Close</option>
                          </select></div>
            
            <div class="twocolumns">Date: <input type="text" name="apply_dt" id="apply_dt" class="textbox_small" onFocus='popUpCalendar(this,document.default_emplate.apply_dt,"mm-dd-yyyy")' value="<?php if(isset($_POST['apply_dt'])){echo $_POST['apply_dt'];}else if(isset($recb['apply_dt'])){echo $recb['apply_dt'];}?>"></div>
          
          </div>
          
          <div class="threecolumns">
          
          	<div class="twocolumns">Source: <input type="text" name="lead_src" id="lead_src" class="textbox_small" value="<?php if(isset($_POST['lead_src'])){echo $_POST['lead_src'];}else if(isset($recb['lead_src'])){echo $recb['lead_src'];}?>"></div>
            
            <div class="twocolumns">Agent: <select name="user_id" id="user_id" class="textbox_small">
                            <option value=""></option>
                            <?php 

							$sql_pro="select * from admin_user where user_group='Agent' order by user_id";

							$pro_res=mysql_query($sql_pro) or die(mysql_error());

									while($pro_rec=mysql_fetch_array($pro_res))

									{

									

									?>
                            <option value="<?php  echo $pro_rec['user_id'];?>" <?php if($pro_rec['user_id']==$_POST['user_id']){ echo "selected";}else if($recb['agent']==$pro_rec['user_id']){echo 'selected';}?>>
                            <?php  echo $pro_rec['user_id'];?>
                            </option>
                            <?php  

							}

							?>
                          </select></div>
          
          </div>
          
          <div class="threecolumns" style="width:39%;">
          
          	
            
            <div class="twocolumns">Funding Time: <input type="text" name="funding_dt" id="funding_dt2" class="textbox_small" value="<?php if(isset($_POST['funding_dt'])){echo $_POST['funding_dt'];}else if(isset($recb['funding_dt'])){echo $recb['funding_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.funding_dt,"yyyy-mm-dd")'></div>
            
            <div class="pagination" style="float:left;">
            
            <?php if($row_offset_position['POSITION']>0){ ?>
            <a href="buyerinfo1.php?rid=<?=$row_previous['customer_id']?>"><img border="0" src="images/prev.png" /></a>
            <?php }else{ ?>
            <span class="disabled"><img border="0" src="images/prev.png" /></span>
            <?php } ?>
            
            <?php if( $row_offset_position['POSITION'] < ($row_record_count['totalCount']-1)){ ?>
            <a href="buyerinfo1.php?rid=<?=$row_next['customer_id']?>"><img border="0" src="images/next.png" /></a>
            <?php }else{ ?>
            <span class="disabled"><img border="0" src="images/next.png" /></span>
            <?php } ?>
           	
           </div>
          
          </div>
          
          </div>
          
          <div id="tab-container" class='tab-container'>
 <ul class='etabs'>
   <li class='tab'><a href="#tabs1-html">Lead, Con Log, Equipment</a></li>
   <li class='tab'><a href="#tabs2-html">After Sale Service</a></li>
   <li class='tab'><a href="#tabs3-html">Shipping Info</a></li>
   <li class='tab'><a href="#tabs4-html">Finance</a></li>
   <li class='tab'><a href="#tabs5-html">Checklist</a></li>
 </ul>
 <div class='panel-container table_disp_out'>
  <div id="tabs1-html">
<div class="threecolumns" style="width:100%;">
                      <div style="width:100%; float:left; padding:0px;border-bottom:2px dotted;">
                        <h4 class="sectiontitle">Contact Information</h4> 
                        
                        <div class="threecolumns" style="width:25%; height:35px;">
                         <div style="width:30%; float:left; " >First Name:</div>
                         <div style="width:70%; float:left;" ><input type="text" name="f_nm" id="f_nm" class="textbox_small" value="<?php if(isset($_POST['f_nm'])){echo $_POST['f_nm'];}else if(isset($recb['f_nm'])){echo $recb['f_nm'];}?>"></div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%;height:35px;">
                        
                         <div style="width:30%; float:left;">Last Name:</div>
                         <div style="width:70%; float:left;"> <input type="text" name="l_nm" id="l_nm" class="textbox_small" value="<?php if(isset($_POST['l_nm'])){echo $_POST['l_nm'];}else if(isset($recb['l_nm'])){echo $recb['l_nm'];}?>">
                        </div>
                        
                        </div>
                       
                        <div class="threecolumns" style="width:25%;height:35px;">
                        
                         <div style="width:30%; float:left;">Phone 1:</div>
                          
                          <div style="width:70%; float:left;"><input type="text" name="p_ph1" id="p_ph1" class="textbox_small" value="<?php if(isset($_POST['p_ph1'])){echo $_POST['p_ph1'];}else if(isset($recb['p_ph1'])){echo $recb['p_ph1'];}?>">
                        </div>
                        
                        </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                        <div style="width:30%; float:left;">Phone 2:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_ph2" id="p_ph2" class="textbox_small" value="<?php if(isset($_POST['p_ph2'])){echo $_POST['p_ph2'];}else if(isset($recb['p_ph2'])){echo $recb['p_ph2'];}?>"></div>
                        </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                       
                       	<div style="width:30%; float:left;">Email 1:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_eml1" id="p_eml1" class="textbox_small" value="<?php if(isset($_POST['p_eml1'])){echo $_POST['p_eml1'];}else if(isset($recb['p_eml1'])){echo $recb['p_eml1'];}?>">
                        </div>
                       
                       </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                        	
                            <div style="width:30%; float:left;">Email 2:</div>
                          <div style="width:70%; float:left;"><input type="text" name="email_2" id="email_2" class="textbox_small" value="<?php if(isset($_POST['email_2'])){echo $_POST['email_2'];}else if(isset($recb['email_2'])){echo $recb['email_2'];}?>">
                        </div>
                            
                        </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                        
                        <div style="width:30%; float:left;">Company's name:</div>
                         <div style="width:70%; float:left;"> <input type="text" name="b_leg_nm" id="b_leg_nm" class="textbox_small" value="<?php if(isset($_POST['b_leg_nm'])){echo $_POST['b_leg_nm'];}else if(isset($recb['b_leg_nm'])){echo $recb['b_leg_nm'];}?>">
                        </div>
                        
                        </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                        	
                            <div style="width:30%; float:left;">Shipping Address:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_hm_addr" id="p_hm_addr" class="textbox_small" value="<?php if(isset($_POST['p_hm_addr'])){echo $_POST['p_hm_addr'];}else if(isset($recb['p_hm_addr'])){echo $recb['p_hm_addr'];}?>">
                        </div>
                            
                        </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                        	
                              <div style="width:30%; float:left;">City:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_city" id="p_city" class="textbox_small" value="<?php if(isset($_POST['p_city'])){echo $_POST['p_city'];}else if(isset($recb['p_city'])){echo $recb['p_city'];}?>">
                        </div>
                            
                        </div>
                        <div class="threecolumns" style="width:25%;height:35px;">
                        	
                            <div style="width:30%; float:left;">State:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_state" id="p_state" class="textbox_small" value="<?php if(isset($_POST['p_state'])){echo $_POST['p_state'];}else if(isset($recb['p_state'])){echo $recb['p_state'];}?>">
                        </div>
                        
                        </div>
                        <div class="threecolumns" style="width:25%; height:35px;">
                        	
                            <div style="width:30%; float:left;">Zip:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_zip" id="p_zip2" class="textbox_small" value="<?php if(isset($_POST['p_zip'])){echo $_POST['p_zip'];}else if(isset($recb['p_zip'])){echo $recb['p_zip'];}?>">
                        </div>
                            
                        </div>
                        <div class="threecolumns" style="width:25%; height:35px;">
                        	
                            <div style="width:30%; float:left;">Country:</div>
                          <div style="width:70%; float:left;"><input type="text" name="p_state" id="p_state" class="textbox_small" value="<?php if(isset($_POST['p_state'])){echo $_POST['p_state'];}else if(isset($recb['p_state'])){echo $recb['p_state'];}?>">
                        </div>
                            
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:35px;">
                        	
                            <div style="width:30%; float:left;">Best time to contact:</div>
                          <div style="width:70%; float:left;">
                          <select name="bst_time_to_call">
                          <option value="">Select Time</option>
                          <option <?php if($recb['bst_time_to_call']=='7AM'){ ?> selected <?php } ?> value="7AM">7AM</option><option <?php if($recb['bst_time_to_call']=='8AM'){ ?> selected <?php } ?> value="8AM">8AM</option><option <?php if($recb['bst_time_to_call']=='9AM'){ ?> selected <?php } ?> value="9AM">9AM</option><option <?php if($recb['bst_time_to_call']=='10AM'){ ?> selected <?php } ?> value="10AM">10AM</option><option <?php if($recb['bst_time_to_call']=='11AM'){ ?> selected <?php } ?> value="11AM">11AM</option><option <?php if($recb['bst_time_to_call']=='12PM'){ ?> selected <?php } ?> value="12PM">12PM</option><option <?php if($recb['bst_time_to_call']=='1PM'){ ?> selected <?php } ?> value="1PM">1PM</option><option <?php if($recb['bst_time_to_call']=='2PM'){ ?> selected <?php } ?> value="2PM">2PM</option><option <?php if($recb['bst_time_to_call']=='3PM'){ ?> selected <?php } ?> value="3PM">3PM</option><option <?php if($recb['bst_time_to_call']=='4PM'){ ?> selected <?php } ?> value="4PM">4PM</option><option <?php if($recb['bst_time_to_call']=='5PM'){ ?> selected <?php } ?> value="5PM">5PM</option><option <?php if($recb['bst_time_to_call']=='6PM'){ ?> selected <?php } ?> value="6PM">6PM</option><option value="7PM" <?php if($recb['bst_time_to_call']=='7PM'){ ?> selected <?php } ?>>7PM</option><option <?php if($recb['bst_time_to_call']=='8PM'){ ?> selected <?php } ?> value="8PM">8PM</option><option <?php if($recb['bst_time_to_call']=='9PM'){ ?> selected <?php } ?> value="9PM">9PM</option><option <?php if($recb['bst_time_to_call']=='10PM'){ ?> selected <?php } ?> value="10PM">10PM</option>
                          </select>
                        </div>
                            
                        </div>
                        
                        </div> 
                       
                      </div>

<div class="threecolumns" style="width:100%;">
                      <div style="width:100%; float:left; border-bottom:2px dotted; padding:0px;">
                        <h4 class="sectiontitle">Equipment Information</h4>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        <div style="width:30%; float:left;"># of Machines:</div>
                        
                        <div style="width:70%; float:left;"> <input type="text" name="machine_no" id="machine_no" class="textbox_small" value="<?php if(isset($_POST['machine_no'])){echo $_POST['machine_no'];}else if(isset($recb['machine_no'])){echo $recb['machine_no'];}?>"><!-- <input type="submit" name="Quote" value="Quote" class="button_small">--></div>
                        </div>
                        
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        <div style="width:30%; float:left;">Equipment Type:</div>
                        <div style="width:70%; float:left;">
                          <select name="machine_types">
                            <option value="0">Select Type</option>
                            <option value="1" <?php if($recb['machine_types']==1){ ?> selected<?php } ?> >Countertop</option>
                            <option value="2" <?php if($recb['machine_types']==2){ ?> selected<?php } ?>>115V Electrical</option>
                            <option value="3" <?php if($recb['machine_types']==3){ ?> selected<?php } ?>>220V Electrical</option>
                          </select>
                        </div>
                        </div>
                        
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Condenser:</div>
                            <div style="width:70%; float:left;">
                          <select name="condenser_type">
                            <option value="0">Select Condenser</option>
                            <option value="1" <?php if($recb['condenser_type']==1){ ?> selected<?php } ?>>Air Cooled</option>
                            <option value="2" <?php if($recb['condenser_type']==2){ ?> selected<?php } ?>>Water Cooled</option>
                          </select>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:65px;">
                        	<div style="width:40%; float:left;"> Existing or New Store?</div>
                          <div style="width:60%; float:left; text-align:left;"><select name="existing_new">
                            <option value="0">Select Store</option>
                            <option value="1" <?php if($recb['existing_new']==1){ ?> selected<?php } ?>>Existing</option>
                            <option value="2" <?php if($recb['existing_new']==2){ ?> selected<?php } ?>>New</option>
                          </select>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:60%; height:50px;">
                        	<div style="width:20%; float:left;">Options:</div>
                          <div style="width:80%; float:left; text-align:left;">
                          <input type="checkbox" value="1" name="equipment_options[]" <?php if( strpos($recb['equipment_options'],1)!==false ){ ?> checked <?php } ?> />
                          &nbsp;Agitator&nbsp;
                          <input type="checkbox" value="2" name="equipment_options[]" <?php if( strpos($recb['equipment_options'],2)!==false ){ ?> checked<?php } ?> />
                          Clear Door&nbsp;
                          <input type="checkbox" value="3" name="equipment_options[]" <?php if( strpos($recb['equipment_options'],3)!==false ){ ?> checked<?php } ?> />
                          &nbsp;Back Control&nbsp;
                          <input type="checkbox" value="4" name="equipment_options[]" <?php if( strpos($recb['equipment_options'],4)!==false ){ ?> checked<?php } ?> />
                          &nbsp;Custom Graphic&nbsp;
                          <input type="checkbox" value="5" name="equipment_options[]" <?php if( strpos($recb['equipment_options'],5)!==false ){ ?> checked<?php } ?> />
                          &nbsp;Wall Trim<br />
                          &nbsp;
                          <input type="checkbox" value="1" name="need_other_equipment[]" <?php if( strpos($recb['need_other_equipment'],1)!==false ){ ?> checked <?php } ?> />
                          &nbsp;Topping Bar&nbsp;
                          <input type="checkbox" value="2" name="need_other_equipment[]" <?php if( strpos($recb['need_other_equipment'],2)!==false ){ ?> checked <?php } ?> />
                          Refrigerators&nbsp;
                          <input type="checkbox" value="3" name="need_other_equipment[]" <?php if( strpos($recb['need_other_equipment'],3)!==false ){ ?> checked <?php } ?> />
                          POS System &nbsp;
                          <input type="checkbox" value="1" name="need_free_logo_design" <?php if( $recb['need_free_logo_design'] == 1 ){ ?> checked <?php } ?> />
                          Logo &nbsp;
                          <input type="checkbox" value="1" name="need_free_floor_plan" <?php if( $recb['need_free_floor_plan'] == 1 ){ ?> checked <?php } ?> />
                          Floor Plan &nbsp;
                          <input type="checkbox" value="1" name="is_kiosk" <?php if( $recb['is_kiosk'] == 1 ){ ?> checked <?php } ?> />
                          Kiosk &nbsp;
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:40%; height:65px;">
                        	<div style="width:30%; float:left;">Remark:</div>
                          <div style="width:70%; float:left;">
                          <textarea name="equipment_remark"><?=$recb['equipment_remark']?></textarea>
                          
                        </div>
                        </div>
                        
                        <?php /*?><div class="threecolumns" style="width:30%; height:65px;">
                        	<div style="width:30%; float:left;"> What other equipment do you need?</div>
                          <div style="width:70%; float:left; text-align:left;">
                        </div>
                        </div><?php */?>
                        
                        
                        
                        
                        <?php /*?><div class="threecolumns" style="width:40%; height:65px;">
                        	<div style="width:40%; float:left;"> Do you need help with our FREE logo design?</div>
                          <div style="width:60%; float:left; text-align:left;"><select name="need_free_logo_design">
                            <option value="0">Select</option>
                            <option value="1" <?php if($recb['need_free_logo_design']==1){ ?> selected<?php } ?>>Yes</option>
                            <option value="2" <?php if($recb['need_free_logo_design']==2){ ?> selected<?php } ?>>No</option>
                          </select>
                        </div>
                        </div><?php */?>
                        
                        <?php /*?><div class="threecolumns" style="width:40%; height:50px;">
                        	<div style="width:50%; float:left;"> Do you need help with our FREE Floor Plan Design?</div>
                          <div style="width:25%; float:left; text-align:left;"><select name="need_free_floor_plan">
                            <option value="0">Select</option>
                            <option value="1" <?php if($recb['need_free_floor_plan']==1){ ?> selected<?php } ?>>Yes</option>
                            <option value="2" <?php if($recb['need_free_floor_plan']==2){ ?> selected<?php } ?>>No</option>
                          </select>
                        </div>
                        </div><?php */?>
                        
                      </div>
                    </div>
                    
<div class="threecolumns" style="width:100%;">
                      <div style="width:100%; float:left; padding:0px; border-bottom:2px dotted;">
                        <h4 class="sectiontitle">Conversation Log</h4>
                        <?php

						if($_GET['ed']=="T")

						{

						?>
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Subject:</div>
                            <div style="width:70%; float:left;">
                          <input type="text" name="log_subject" id="log_subject" class="textbox_small" value="<?php if(isset($_POST['log_subject'])){echo $_POST['log_subject'];}else if(isset($recl['log_subject'])){echo $recl['log_subject'];}?>">
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                       <div style="width:30%; float:left;">Outcome:</div>
                         <div style="width:70%; float:left;"> <textarea name="out_come"><?php if(isset($_POST['out_come'])){echo $_POST['out_come'];}else if(isset($recl['out_come'])){echo $recl['out_come'];}?></textarea>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Spoke To:</div>
                          <div style="width:70%; float:left;"><input type="text" name="spoke_to" id="spoke_to" class="textbox_small" value="<?php if(isset($_POST['spoke_to'])){echo $_POST['spoke_to'];}else if(isset($recl['spoke_to'])){echo $recl['spoke_to'];}?>">
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Next Follow Up Date:</div>
                          <div style="width:70%; float:left;"><input readonly="true" type="text" name="next_follow_up" id="next_follow_up" class="textbox_small" value="<?php if(isset($_POST['next_follow_up'])){echo $_POST['next_follow_up'];}else if(isset($recl['next_follow_up'])){  $follow_up = explode("-",$recl['next_follow_up']); echo $follow_up[1].'-'.$follow_up[2].'-'.$follow_up[0];}?>" >
                        </div>
                        </div>
                        
                        <div class="twocolumns">
                          <div align="right"><a href="sendmailsinglecust.php?bid=<?php echo $recb['customer_id'];?>" onClick="wopen('sendmailsinglecust.php?uid=<?php echo $recb['customer_id'];?>', 'popup', 500, 500); return false;" target="popup"> </a></div>
                        </div>
                        <div class="twocolumns">
                          <input type="submit" name="Edit" value="Edit Log" class="button_big">
                          <input type="submit" name="Cancel" value="Cancel" class="button_medium">
                        </div>
                        <?php

						}

						else

						{

						?>
                        <div style="width:100%;">
                          <table width="100%">
                            <tr>
                              <td width="120" height="21" bgcolor="#CCCCCC" class="main_txt">Log 
                                
                                Time </td>
                              <td width="120" height="21" bgcolor="#CCCCCC" class="main_txt">Subject</td>
                              <td width="110" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;&nbsp;&nbsp;Performed 
                                
                                By </td>
                              <td width="80" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;&nbsp;Next 
                                
                                Follow up</td>
                              <td width="250" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;Outcome</td>
                              <td width="60" height="21" bgcolor="#CCCCCC" class="main_txt"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Option</div></td>
                            </tr>
                            <tr>
                              <td colspans="6"><div style="overflow: auto;height: 100px; width: 100%;">
                                  <table width="100%" height="26">
                                    <?php 

					  

					    $seeavail="select a.customer_id,b.auto_id,b.customer_id,b.log_time,b.log_subject,b.spoke_to,b.out_come,b.next_follow_up

						from customer_info a,conversation_log_info b

						where a.customer_id=b.customer_id

						and b.customer_id='".$_GET['rid']."'

						order by auto_id desc";

						$seeres=mysql_query($seeavail) or die(mysql_error()."go select error");

						

						if(mysql_num_rows($seeres)=='0')

						{

						?>
                                    <tr>
                                      <td colspans="6" class="msg" align="center">No 
                                        
                                        Activity found</td>
                                    </tr>
                                    <?php

						 }

						 else

						 {

						while($seerec = mysql_fetch_assoc($seeres))

							{

							?>
                                    <tr>
                                      <td width="120" class="smalltext_grey" valign="top"><?php echo $seerec['log_time'];?></td>
                                      <td width="120" class="smalltext_grey" valign="top"><?php echo $seerec['log_subject'];?></td>
                                      <td width="110" class="smalltext_grey" valign="top"><?php echo $seerec['spoke_to'];?></td>
                                      <td width="80" class="smalltext_grey" valign="top"><?php echo $seerec['next_follow_up'];?></td>
                                      <td width="250" class="" valign="top"><?php echo $seerec['out_come'];?></td>
                                      <td width="60" class="main_txt" valign="top"><div align="center"><a href="buyerinfo1.php?rid=<?php echo $_GET['rid'];?>&aid=<?php echo $seerec['auto_id'];?>&ed=T" class="main_txt">Edit</a> &nbsp;&nbsp;</div></td>
                                    </tr>
                                    <?php 

							

							}

						}		

					  ?>
                                  </table>
                                </div></td>
                            </tr>
                          </table>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Subject:</div>
                          <div style="float:left; width:70%;"><input type="text" name="log_subject" id="log_subject" class="textbox_small" value="<?php if(isset($_POST['log_subject'])){echo $_POST['log_subject'];}?>">
                        </div>
                        </div>
                        
                         <div class="threecolumns" style="width:75%; height:50px;">
                         	<div style="float:left; width:30%;">Outcome:</div>
                          <div style="float:left; width:70%;"><textarea name="out_come" cols="50" style="background:#F9F8C2;"><?php if(isset($_POST['out_come'])){echo $_POST['out_come'];}?></textarea>
                        </div>
                         </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Performed By:</div>
                          <div style="float:left; width:70%;"><input type="text" name="spoke_to" id="spoke_to" class="textbox_small" value="<?php if(isset($_POST['spoke_to'])){echo $_POST['spoke_to'];}?>">
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Next Follow Up Date:</div>
                          <div style="float:left; width:70%;"><input type="text" name="next_follow_up" id="next_follow_up" class="textbox_small" value="<?php if(isset($_POST['next_follow_up'])){echo $_POST['next_follow_up'];}?>" onFocus='popUpCalendar(this,document.default_emplate.next_follow_up,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        
                        <?php /*?><div style="margin-top:5px; float:left; margin-right:5px; margin-left:5px; margin-bottom:5px;">
                          <input style="width:100px;" type="submit" name="Add" value="Save Call Log" class="button_medium">
                        </div>
                        <div style="margin-top:5px;float:left; margin-right:5px;">
                          <input type="submit" name="Done" value="Done" class="button_medium">
                        </div><?php */?>
                        <?php

						}

						?>
                      </div>
                    </div>                    
                                        


  </div>
   <div id="tabs2-html">
   	
    <div class="threecolumns" style="width:100%;"> 
            <div style="width:100%; float:left; border-bottom:2px dotted; padding:0px;">
           <h5>After Sale Service</h5>
          <?php /*?><div class="twocolumns">Repair company information <textarea cols="17" name="sale_service_repair_company_info"><?=$recb['sale_service_repair_company_info']?></textarea></div><?php */?>
          
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Company Name:</div>
                          <div style="float:left; width:70%;"><input type="text" name="ss_com_name" id="ss_com_name" class="textbox_small" value="<?=$recb['ss_com_name']?>" />
                        </div>
                        </div>
          
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Contact Person:</div>
                          <div style="float:left; width:70%;"><input type="text" name="ss_contact_person" id="ss_contact_person" class="textbox_small" value="<?=$recb['ss_contact_person']?>" />
                        </div>
                        </div>
          
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Contact Phone:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_ph" id="b_ph" class="textbox_small" value="<?=$recb['b_ph']?>" />
                        </div>
                        </div> 
                        
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Contact Email:</div>
                          <div style="float:left; width:70%;"><input type="text" name="ss_contact_email" id="ss_contact_email" class="textbox_small" value="<?=$recb['ss_contact_email']?>" />
                        </div>
                        </div> 
                        
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Technician:</div>
                          <div style="float:left; width:70%;"><input type="text" name="ss_technician" id="ss_technician" class="textbox_small" value="<?=$recb['ss_technician']?>" />
                        </div>
                        </div> 
                        
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Technician Phone:</div>
                          <div style="float:left; width:70%;"><input type="text" name="ss_tech_phone" id="ss_tech_phone" class="textbox_small" value="<?=$recb['ss_tech_phone']?>" />
                        </div>
                        </div>  
                        
          <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Technician Email:</div>
                          <div style="float:left; width:70%;"><input type="text" name="ss_tech_email" id="ss_tech_email" class="textbox_small" value="<?=$recb['ss_tech_email']?>" />
                        </div>
                        </div>  
                        
           <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Fax:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_fax" id="b_fax" class="textbox_small" value="<?=$recb['b_fax']?>" />
                        </div>
                        </div>  
                        
            <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Url:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_wb_site" id="b_wb_site" class="textbox_small" value="<?=$recb['b_wb_site']?>" />
                        </div>
                        </div>
                        
           <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Address:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_addr" id="b_addr" class="textbox_small" value="<?=$recb['b_addr']?>" />
                        </div>
                        </div>    
           
           <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">City:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_city" id="b_city" class="textbox_small" value="<?=$recb['b_city']?>" />
                        </div>
                        </div>
                        
            <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">State:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_state" id="b_state" class="textbox_small" value="<?=$recb['b_state']?>" />
                        </div>
                        </div>                                                                                                                                      
          
            <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Zip Code:</div>
                          <div style="float:left; width:70%;"><input type="text" name="b_zip" id="b_zip" class="textbox_small" value="<?=$recb['b_zip']?>" />
                        </div>
                        </div>
            
           </div>
           
           
          </div>
    
    <div class="threecolumns" style="width:100%;">
                      <div style="width:100%; float:left; padding:0px;">
                        <h4 class="sectiontitle">Issues Log</h4>
                        <?php

						if($_GET['ed']=="edit")

						{

						?>
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Issue:</div>
                            <div style="width:70%; float:left;">
                          <input type="text" name="issue" id="issue" class="textbox_small" value="<?php if(isset($_POST['issue'])){echo $_POST['issue'];}else if(isset($recIssues['issue'])){echo $recIssues['issue'];}?>">
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                       <div style="width:30%; float:left;">Outcome:</div>
                         <div style="width:70%; float:left;"> <textarea name="outcome"><?php if(isset($_POST['outcome'])){echo $_POST['outcome'];}else if(isset($recIssues['outcome'])){echo $recIssues['outcome'];}?></textarea>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Performed by:</div>
                          <div style="width:70%; float:left;"><input type="text" name="performed_by" id="performed_by" class="textbox_small" value="<?php if(isset($_POST['performed_by'])){echo $_POST['performed_by'];}else if(isset($recIssues['performed_by'])){echo $recIssues['performed_by'];}?>">
                        </div>
                        </div>
                        
                        
                        <div class="twocolumns">
                          <input type="submit" name="EditIssue" value="Edit Issue Log" class="button_big">
                          <input type="submit" name="CancelIssue" value="Cancel" class="button_medium">
                        </div>
                        <?php

						}

						else

						{

						?>
                        <div style="width:100%;">
                          <table width="100%">
                            <tr>
                              <td width="250" height="21" bgcolor="#CCCCCC" class="main_txt">Log 
                                
                                Time </td>
                              <td width="250" height="21" bgcolor="#CCCCCC" class="main_txt">Issue</td>
                              <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;&nbsp;&nbsp;Performed 
                                
                                By </td>
                              
                              <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;Outcome</td>
                              <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Option</div></td>
                            </tr>
                            <tr>
                              <td colspans="6"><div style="overflow: auto;height: 100px; width: 100%;">
                                  <table width="100%" height="26">
                                    <?php 

					  

					    $seeavail="select a.customer_id,b.auto_id,b.log_time,b.issue,b.performed_by,b.outcome 

						from customer_info a,issues_log_info b

						where a.customer_id=b.customer_id

						and b.customer_id='".$_GET['rid']."'

						order by auto_id desc";

						$seeres=mysql_query($seeavail) or die(mysql_error()."go select error");

						

						if(mysql_num_rows($seeres)=='0')

						{

						?>
                                    <tr>
                                      <td colspans="5" class="msg" align="center">No 
                                        
                                        Activity found</td>
                                    </tr>
                                    <?php

						 }

						 else

						 {

						while($seerec = mysql_fetch_assoc($seeres))

							{

							?>
                                    <tr>
                                      <td width="151"150px"" class="smalltext_grey" valign="top"><?php echo $seerec['log_time'];?></td>
                                      <td width="151" class="smalltext_grey" valign="top"><?php echo $seerec['issue'];?></td>
                                      <td width="151" class="smalltext_grey" valign="top"><?php echo $seerec['performed_by'];?></td>
                                      <td width="140" class="smalltext_grey" valign="top"><?php echo $seerec['outcome'];?></td>
                                      <td width="149"150px""  class="main_txt" valign="top"><div align="center"><a href="buyerinfo1.php?rid=<?php echo $_GET['rid'];?>&aid=<?php echo $seerec['auto_id'];?>&ed=edit#tabs2-html" class="main_txt">Edit</a> &nbsp;&nbsp;</div></td>
                                    </tr>
                                    <?php 

							

							}

						}		

					  ?>
                                  </table>
                                </div></td>
                            </tr>
                          </table>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Issue:</div>
                          <div style="float:left; width:70%;"><input type="text" name="issue" id="issue" class="textbox_small" value="">
                        </div>
                        </div>
                        
                         <div class="threecolumns" style="width:75%; height:50px;">
                         	<div style="float:left; width:30%;">Outcome:</div>
                          <div style="float:left; width:70%;"><textarea name="outcome"></textarea>
                        </div>
                         </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="float:left; width:30%;">Performed By:</div>
                          <div style="float:left; width:70%;"><input type="text" name="performed_by" id="performed_by" class="textbox_small" value="">
                        </div>
                        </div>
                        
                        <div style="margin-top:5px; float:left; margin-right:5px; margin-left:5px; margin-bottom:5px;">
                          <input style="width:100px;" type="submit" name="AddIssue" value="Save Issue Log" class="button_medium">
                        </div>
                        <div style="margin-top:5px;float:left; margin-right:5px;">
                          <input type="submit" name="DoneIssue" value="Done" class="button_medium">
                        </div>
                        <?php

						}

						?>
                      </div>
                    </div>      
    
    
    
  </div>
  <div id="tabs3-html">
   <div style="width:100%; float:left;">
   
   <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Company Name:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_com_name" id="ship_com_name" class="textbox_small" value="<?=$recb['ship_com_name']?>">
                        </div>
                        
                        </div>
                        
   <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Company Phone:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_com_phone" id="ship_com_phone" class="textbox_small" value="<?=$recb['ship_com_phone']?>">
                        </div>
                        
                        </div>
                        
   <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Contact Person:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_con_person" id="ship_con_person" class="textbox_small" value="<?=$recb['ship_con_person']?>">
                        </div>
                        
                        </div>
                        
   <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Phone:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_ph" id="ship_ph" class="textbox_small" value="<?=$recb['ship_ph']?>">
                        </div>
                        
                        </div> 
                        
   <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Driver Name:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_driver_name" id="ship_driver_name" class="textbox_small" value="<?=$recb['ship_driver_name']?>">
                        </div>
                        
                        </div>
   
   <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Driver's Phone:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_driver_ph" id="ship_driver_ph" class="textbox_small" value="<?=$recb['ship_driver_ph']?>">
                        </div>
                        
                        </div>
      
    <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Email 1:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_email1" id="ship_email1" class="textbox_small" value="<?=$recb['ship_email1']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Email 2:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_email1" id="ship_email1" class="textbox_small" value="<?=$recb['ship_email2']?>">
                        </div>
                        
                        </div> 
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Web:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_web" id="ship_web" class="textbox_small" value="<?=$recb['ship_web']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Address:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_addr" id="ship_addr" class="textbox_small" value="<?=$recb['ship_addr']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >City:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_city" id="ship_city" class="textbox_small" value="<?=$recb['ship_city']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >State: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_state" id="ship_state" class="textbox_small" value="<?=$recb['ship_state']?>">
                        </div>
                        
                        </div> 
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Zipcode: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_zipcode" id="ship_zipcode" class="textbox_small" value="<?=$recb['ship_zipcode']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        <?php if($recb['ship_pickup_date']=='0000-00-00'){ $recb['ship_pickup_date']=''; } ?>
                        <div style="width:30%; float:left;" >Pick up date: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_pickup_date" id="ship_pickup_date" class="textbox_small" value="<?=$recb['ship_pickup_date']?>" onFocus='popUpCalendar(this,document.default_emplate.ship_pickup_date,"yyyy-mm-dd")'>
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Pick up time: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_pickup_time" id="ship_pickup_time" class="textbox_small" value="<?=$recb['ship_pickup_time']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        <?php if($recb['ship_estimated_delivery_date']=='0000-00-00'){ $recb['ship_estimated_delivery_date']=''; } ?>
                        <div style="width:30%; float:left;" >Estimated Delivery Date: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_estimated_delivery_date" id="ship_estimated_delivery_date" class="textbox_small" value="<?=$recb['ship_estimated_delivery_date']?>" onFocus='popUpCalendar(this,document.default_emplate.ship_estimated_delivery_date,"yyyy-mm-dd")'>
                        </div>
                        
                        </div>     
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Estimated Delivery Time: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_estimated_delivery_time" id="ship_estimated_delivery_time" class="textbox_small" value="<?=$recb['ship_estimated_delivery_time']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Shipping Charge: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_ship_charge" id="ship_ship_charge" class="textbox_small" value="<?=$recb['ship_ship_charge']?>">
                        </div>
                        
                        </div>
                        
     <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Invoice Number: </div>
                          <div style="width:70%; float:left;" ><input type="text" name="ship_inv_no" id="ship_inv_no" class="textbox_small" value="<?=$recb['ship_inv_no']?>">
                        </div>
                        
                        </div>
                        
     
   
   <div class="threecolumns">ETD:
                          <input type="text" name="etd_dt" id="etd_dt" class="textbox_small" value="<?php if(isset($_POST['etd_dt'])){echo $_POST['etd_dt'];}else if(isset($recb['etd_dt'])){echo $recb['etd_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.etd_dt,"mm-dd-yyyy")'>
                        </div><div class="threecolumns">ETA:
                          <input type="text" name="eta_dt" id="eta_dt" class="textbox_small" value="<?php if(isset($_POST['eta_dt'])){echo $_POST['eta_dt'];}else if(isset($recb['eta_dt'])){echo $recb['eta_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.eta_dt,"mm-dd-yyyy")'>
                        </div><div class="threecolumns">Frieght Company Information:
                          <textarea name="freight_com_info"  rows="4"><?php if(isset($_POST['freight_com_info'])){echo $_POST['freight_com_info'];}else if(isset($recb['freight_com_info'])){echo $recb['freight_com_info'];}?>
</textarea>
                        </div>
   </div>
 </div>
 <div id="tabs4-html">
 <div class="threecolumns" style="width:100%;">
                      <div style="width:100%; float:left; border-bottom:2px dotted; padding:0px;">
                        <h4 class="sectiontitle">Finance</h4>
                        
                        <div class="threecolumns" style="width:25%; height:40px;">
                        
                        <div style="width:30%; float:left;" >Invoice Date:</div>
                          <div style="width:70%; float:left;" ><input type="text" name="invoice_dt" id="invoice_dt" class="textbox_small" onFocus='popUpCalendar(this,document.default_emplate.invoice_dt,"mm-dd-yyyy")' value="<?php if(isset($_POST['invoice_dt'])){echo $_POST['invoice_dt'];}else if(isset($recb['invoice_dt'])){echo $recb['invoice_dt'];}?>">
                        </div>
                        
                        </div>
                        
                        
                        <div class="threecolumns" style="width:25%; height:40px;">
                         <div style="width:30%; float:left;">Total Amount:</div>
                         <div style="width:70%; float:left;"> <input type="text" name="total_amt" id="total_amt" class="textbox_small" value="<?php if(isset($_POST['total_amt'])){echo $_POST['total_amt'];}else if(isset($recb['total_amt'])){echo $recb['total_amt'];}?>">
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:40px;">
                        	
                           <div style="width:30%; float:left;">Invoice #:</div>
                          <div style="width:70%; float:left;"><input type="text" name="invoice_number" id="invoice_number" class="textbox_small" value="<?php if(isset($_POST['invoice_number'])){echo $_POST['invoice_number'];}else if(isset($recb['invoice_number'])){echo $recb['invoice_number'];}?>">
                        </div>
                        
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:40px;">
                        	
                            <div style="width:30%; float:left;">Type:</div>
                          <div style="width:70%; float:left;"><select name="financing_opt" size="1" class="textbox_small">
                            <option value="Cash" <?php if($_POST['financing_opt']=="Cash"){echo 'selected';}else if($recb['financing_opt']=="Cash"){echo 'selected';}?>>Cash</option>
                            <option value="Lease" <?php if($_POST['financing_opt']=="Lease"){echo 'selected';}else if($recb['financing_opt']=="Lease"){echo 'selected';}?>>Lease</option>
                            <option value="Lease2" <?php if($_POST['financing_opt']=="Lease2"){echo 'selected';}else if($recb['financing_opt']=="Lease2"){echo 'selected';}?>>Lease2</option>
                            <option value="Lease3" <?php if($_POST['financing_opt']=="Lease3"){echo 'selected';}else if($recb['financing_opt']=="Lease3"){echo 'selected';}?>>Lease3</option>
                            <option value="Lease4" <?php if($_POST['financing_opt']=="Lease4"){echo 'selected';}else if($recb['financing_opt']=="Lease4"){echo 'selected';}?>>Lease4</option>
                          </select>
                        </div>
                            
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Status:</div>
                          <div style="width:70%; float:left;"><select name="financing_stat" size="1" class="textbox_small">
                            <option value="Applied" <?php if($_POST['financing_stat']=="Applied"){echo 'selected';}else if($recb['financing_stat']=="Applied"){echo 'selected';}?>>Applied</option>
                            <option value="Not Applied" <?php if($_POST['financing_stat']=="Not Applied"){echo 'selected';}else if($recb['financing_stat']=="Not Applied"){echo 'selected';}?>>Not Applied</option>
                            <option value="Approved" <?php if($_POST['financing_stat']=="Approved"){echo 'selected';}else if($recb['financing_stat']=="Approved"){echo 'selected';}?>>Approved</option>
                            <option value="Denied" <?php if($_POST['financing_stat']=="Denied"){echo 'selected';}else if($recb['financing_stat']=="Denied"){echo 'selected';}?>>Denied</option>
                            <option value="Pending" <?php if($_POST['financing_stat']=="Pending"){echo 'selected';}else if($recb['financing_stat']=="Pending"){echo 'selected';}?>>Pending Review</option>
                            <option value="Missing" <?php if($_POST['financing_stat']=="Missing"){echo 'selected';}else if($recb['financing_stat']=="Missing"){echo 'selected';}?>>Missing Documents</option>
                            <option value="Express Agreement" <?php if($_POST['financing_stat']=="Express Agreement"){echo 'selected';}else if($recb['financing_stat']=="Express Agreement"){echo 'selected';}?>>Express Agreement</option>
                            <option value="Funded" <?php if($_POST['financing_stat']=="Funded"){echo 'selected';}else if($recb['financing_stat']=="Funded"){echo 'selected';}?>>Funded</option>
                            <option value="Final Docs Out" <?php if($_POST['financing_stat']=="Final Docs Out"){echo 'selected';}else if($recb['financing_stat']=="Final Docs Out"){echo 'selected';}?>>Final Docs Out</option>
                            <option value="Final Docs In" <?php if($_POST['financing_stat']=="Final Docs In"){echo 'selected';}else if($recb['financing_stat']=="Final Docs In"){echo 'selected';}?>>Final Docs In</option>
                            <option value="Site Inspection" <?php if($_POST['financing_stat']=="Site Inspection"){echo 'selected';}else if($recb['financing_stat']=="Site Inspection"){echo 'selected';}?>>Site Inspection</option>
                          </select>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	
                            <div style="width:30%; float:left;">Shipping Method<!-- (air / sea)-->:</div>
                          <div style="width:70%; float:left;"><select name="shipping_method" size="1" class="textbox_small">
                            <option value="Sea" <?php if($_POST['shipping_method']=="Sea"){echo 'selected';}else if($recb['shipping_method']=="Sea"){echo 'selected';}?>>Sea</option>
                            <option value="Air" <?php if($_POST['shipping_method']=="Air"){echo 'selected';}else if($recb['shipping_method']=="Air"){echo 'selected';}?>>Air</option>
                          </select>
                        </div>
                            
                        </div>
                        
                         <div class="threecolumns" style="width:25%; height:50px;">
                         	<div style="width:30%; float:left;">Payment 1 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay1_dt" id="pay1_dt" class="textbox_small" value="<?php if(isset($_POST['pay1_dt'])){echo $_POST['pay1_dt'];}else if(isset($recb['pay1_dt'])){echo $recb['pay1_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay1_dt,"mm-dd-yyyy")'>
                        </div>
                         </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 1 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay1_amt" id="pay1_amt" class="textbox_small" value="<?php if(isset($_POST['pay1_amt'])){echo $_POST['pay1_amt'];}else if(isset($recb['pay1_amt'])){echo $recb['pay1_amt'];}?>">
                          <br />
                          <input type="checkbox" name="pay1_stat" value="T" <?php if($_POST['pay1_stat']=="T"){echo 'checked';}else if($recb['pay1_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 2 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay2_dt" id="pay2_dt" class="textbox_small" value="<?php if(isset($_POST['pay2_dt'])){echo $_POST['pay2_dt'];}else if(isset($recb['pay2_dt'])){echo $recb['pay2_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay2_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 2 Amount:</div>
                          <div style="width:70%; float:left;text-align:left;"><input type="text" name="pay2_amt" id="pay2_amt" class="textbox_small" value="<?php if(isset($_POST['pay2_amt'])){echo $_POST['pay2_amt'];}else if(isset($recb['pay2_amt'])){echo $recb['pay2_amt'];}?>">
                          <br />
                          <input type="checkbox" name="pay2_stat" value="T" <?php if($_POST['pay2_stat']=="T"){echo 'checked';}else if($recb['pay2_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 3 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay3_dt" id="pay3_dt" class="textbox_small" value="<?php if(isset($_POST['pay3_dt'])){echo $_POST['pay3_dt'];}else if(isset($recb['pay3_dt'])){echo $recb['pay3_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay3_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 3 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay3_amt" id="pay3_amt" class="textbox_small" value="<?php if(isset($_POST['pay3_amt'])){echo $_POST['pay3_amt'];}else if(isset($recb['pay3_amt'])){echo $recb['pay3_amt'];}?>">
                          <br /><input type="checkbox" name="pay3_stat" value="T" <?php if($_POST['pay3_stat']=="T"){echo 'checked';}else if($recb['pay3_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 4 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay4_dt" id="pay4_dt" class="textbox_small" value="<?php if(isset($_POST['pay4_dt'])){echo $_POST['pay4_dt'];}else if(isset($recb['pay4_dt'])){echo $recb['pay4_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay4_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 4 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay4_amt" id="pay4_amt" class="textbox_small" value="<?php if(isset($_POST['pay4_amt'])){echo $_POST['pay4_amt'];}else if(isset($recb['pay4_amt'])){echo $recb['pay4_amt'];}?>">
                          <br /><input type="checkbox" name="pay4_stat" value="T" <?php if($_POST['pay4_stat']=="T"){echo 'checked';}else if($recb['pay4_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 5 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay5_dt" id="pay5_dt" class="textbox_small" value="<?php if(isset($_POST['pay5_dt'])){echo $_POST['pay5_dt'];}else if(isset($recb['pay5_dt'])){echo $recb['pay5_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay5_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 5 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay5_amt" id="pay5_amt" class="textbox_small" value="<?php if(isset($_POST['pay5_amt'])){echo $_POST['pay5_amt'];}else if(isset($recb['pay5_amt'])){echo $recb['pay5_amt'];}?>">
                          <br /><input type="checkbox" name="pay5_stat" value="T" <?php if($_POST['pay5_stat']=="T"){echo 'checked';}else if($recb['pay5_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 6 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay6_dt" id="pay6_dt" class="textbox_small" value="<?php if(isset($_POST['pay6_dt'])){echo $_POST['pay6_dt'];}else if(isset($recb['pay6_dt'])){echo $recb['pay6_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay6_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 6 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay6_amt" id="pay6_amt" class="textbox_small" value="<?php if(isset($_POST['pay6_amt'])){echo $_POST['pay6_amt'];}else if(isset($recb['pay6_amt'])){echo $recb['pay6_amt'];}?>">
                          <br /><input type="checkbox" name="pay6_stat" value="T" <?php if($_POST['pay6_stat']=="T"){echo 'checked';}else if($recb['pay6_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 7 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay7_dt" id="pay7_dt" class="textbox_small" value="<?php if(isset($_POST['pay7_dt'])){echo $_POST['pay7_dt'];}else if(isset($recb['pay7_dt'])){echo $recb['pay7_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay7_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 7 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay7_amt" id="pay7_amt" class="textbox_small" value="<?php if(isset($_POST['pay7_amt'])){echo $_POST['pay7_amt'];}else if(isset($recb['pay7_amt'])){echo $recb['pay7_amt'];}?>">
                          <br /><input type="checkbox" name="pay7_stat" value="T" <?php if($_POST['pay7_stat']=="T"){echo 'checked';}else if($recb['pay7_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 8 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay8_dt" id="pay8_dt" class="textbox_small" value="<?php if(isset($_POST['pay8_dt'])){echo $_POST['pay8_dt'];}else if(isset($recb['pay8_dt'])){echo $recb['pay8_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay8_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 8 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay8_amt" id="pay8_amt" class="textbox_small" value="<?php if(isset($_POST['pay8_amt'])){echo $_POST['pay8_amt'];}else if(isset($recb['pay8_amt'])){echo $recb['pay8_amt'];}?>">
                          <br /><input type="checkbox" name="pay8_stat" value="T" <?php if($_POST['pay8_stat']=="T"){echo 'checked';}else if($recb['pay8_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 9 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay9_dt" id="pay9_dt" class="textbox_small" value="<?php if(isset($_POST['pay9_dt'])){echo $_POST['pay9_dt'];}else if(isset($recb['pay9_dt'])){echo $recb['pay9_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay9_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 9 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay9_amt" id="pay9_amt" class="textbox_small" value="<?php if(isset($_POST['pay9_amt'])){echo $_POST['pay9_amt'];}else if(isset($recb['pay9_amt'])){echo $recb['pay9_amt'];}?>">
                          <br /><input type="checkbox" name="pay9_stat" value="T" <?php if($_POST['pay9_stat']=="T"){echo 'checked';}else if($recb['pay9_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 10 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay10_dt" id="pay10_dt" class="textbox_small" value="<?php if(isset($_POST['pay10_dt'])){echo $_POST['pay10_dt'];}else if(isset($recb['pay10_dt'])){echo $recb['pay10_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay10_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 10 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay10_amt" id="pay10_amt" class="textbox_small" value="<?php if(isset($_POST['pay10_amt'])){echo $_POST['pay10_amt'];}else if(isset($recb['pay10_amt'])){echo $recb['pay10_amt'];}?>">
                          <br /><input type="checkbox" name="pay10_stat" value="T" <?php if($_POST['pay10_stat']=="T"){echo 'checked';}else if($recb['pay10_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 11 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay11_dt" id="pay11_dt" class="textbox_small" value="<?php if(isset($_POST['pay11_dt'])){echo $_POST['pay11_dt'];}else if(isset($recb['pay11_dt'])){echo $recb['pay11_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay11_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 11 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay11_amt" id="pay11_amt" class="textbox_small" value="<?php if(isset($_POST['pay11_amt'])){echo $_POST['pay11_amt'];}else if(isset($recb['pay11_amt'])){echo $recb['pay11_amt'];}?>">
                          <br /><input type="checkbox" name="pay11_stat" value="T" <?php if($_POST['pay11_stat']=="T"){echo 'checked';}else if($recb['pay11_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 12 Date:</div>
                          <div style="width:70%; float:left;"><input type="text" name="pay12_dt" id="pay12_dt" class="textbox_small" value="<?php if(isset($_POST['pay12_dt'])){echo $_POST['pay12_dt'];}else if(isset($recb['pay12_dt'])){echo $recb['pay12_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay12_dt,"mm-dd-yyyy")'>
                        </div>
                        </div>
                        
                        <div class="threecolumns" style="width:25%; height:50px;">
                        	<div style="width:30%; float:left;">Payment 12 Amount:</div>
                          <div style="width:70%; float:left; text-align:left;"><input type="text" name="pay12_amt" id="pay12_amt" class="textbox_small" value="<?php if(isset($_POST['pay12_amt'])){echo $_POST['pay12_amt'];}else if(isset($recb['pay12_amt'])){echo $recb['pay12_amt'];}?>">
                          <br /><input type="checkbox" name="pay12_stat" value="T" <?php if($_POST['pay12_stat']=="T"){echo 'checked';}else if($recb['pay12_stat']=="T"){echo 'checked';}?>>
                          Paid </div>
                        </div>
                     
                      </div>
                    </div>
 </div>
 <div id="tabs5-html">
 <div style="width:100%; float:left; border-top:2px dotted; padding:0px;">
           <h5>Checklist</h5>
          
          <div style="width:40%; float:left; text-align:left;"><b>Topic</b></div><div style="width:20%; float:left; text-align:left;"><b>Date</b></div><div style="width:20%; float:left;text-align:left;"><b>By</b></div>
           
           <?php  
		   
		   $sql_checklist = "SELECT * from checklist c where c.customer_id = '".$_GET['rid']."' ORDER BY c.sent_date DESC";
		   $result_checklist = mysql_query($sql_checklist) or die(mysql_error());
		   
		   ?>
           
           <div style="width:100%; float:left; text-align:left; height:200px; overflow:auto;">
           
           <?php while($row_checklist = mysql_fetch_assoc($result_checklist) ){ ?>
           
           <div style="width:40%; float:left; text-align:left;">
           
           <?php 
		   
		   if($row_checklist['topic']==1)
		   	 echo "Introduction Email";
		   else if($row_checklist['topic']==2)
		   	 echo "Quote Sent";
		   else if($row_checklist['topic']==3)
		   	 echo "Quote Review";
		   else if($row_checklist['topic']==4)
		   	 echo "Purchase Order Sent";
		   else if($row_checklist['topic']==5)
		   	 echo "Payment Confirmation";
		   else if($row_checklist['topic']==6)
		   	 echo "Artwork Received";
		   else if($row_checklist['topic']==7)
		   	 echo "Local Technician assigned";
		   else if($row_checklist['topic']==8)
		   	 echo "Operating Manual";
		   else if($row_checklist['topic']==9)
		   	 echo "Electrical Diagram";
		   else if($row_checklist['topic']==10)
		   	 echo "Delivery Confirmation";
		   else if($row_checklist['topic']==11)
		   	 echo "Balance Due Request";
	       else if($row_checklist['topic']==12)
		   	 echo "Balance Paid";		 
		   else if($row_checklist['topic']==13)
		   	 echo "Training Date Scheduled";	  	 	 
			 	 	 	 
		   ?>
           
           </div><div style="width:20%; float:left;"><?=$row_checklist['sent_date']?></div><div style="width:20%; float:left;"><?=$row_checklist['sent_by']?></div><div style="width:20%; float:left;"></div>
           <?php } ?>
           </div>
           
           <div style="width:40%; float:left; text-align:left;">Introduction Email</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_1,"mm-dd-yyyy")' name="date_1" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_1" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_1" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Quote Sent</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_2,"mm-dd-yyyy")' name="date_2" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_2" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_2" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Quote Review</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_3,"mm-dd-yyyy")' name="date_3" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_3" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_3" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Purchase Order Sent</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_4,"mm-dd-yyyy")' name="date_4" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_4" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_4" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Payment Confirmation</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_5,"mm-dd-yyyy")' name="date_5" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_5" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_5" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Artwork Received</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_6,"mm-dd-yyyy")' name="date_6" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_6" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_6" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Local Technician assigned</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_7,"mm-dd-yyyy")' name="date_7" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_7" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_7" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Operating Manual</div><div style="width:20%; float:left;"><input type="text" onFocus='popUpCalendar(this,document.default_emplate.date_8,"mm-dd-yyyy")' name="date_8" /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_8" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_8" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Electrical Diagram</div><div style="width:20%; float:left;"><input type="text" name="date_9" onFocus='popUpCalendar(this,document.default_emplate.date_9,"mm-dd-yyyy")' /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_9" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_9" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Delivery Confirmation</div><div style="width:20%; float:left;"><input type="text" name="date_10" onFocus='popUpCalendar(this,document.default_emplate.date_10,"mm-dd-yyyy")' /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_10" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_10" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Balance Due Request</div><div style="width:20%; float:left;"><input type="text" name="date_11" onFocus='popUpCalendar(this,document.default_emplate.date_11,"mm-dd-yyyy")' /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_11" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_11" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Balance Paid</div><div style="width:20%; float:left;"><input type="text" name="date_12" onFocus='popUpCalendar(this,document.default_emplate.date_12,"mm-dd-yyyy")' /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_12" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_12" value="Save" /></div>
           <div style="width:40%; float:left; text-align:left;">Training Date Scheduled</div><div style="width:20%; float:left;"><input type="text" name="date_13" onFocus='popUpCalendar(this,document.default_emplate.date_13,"mm-dd-yyyy")' /></div><div style="width:20%; float:left;"><input type="text" name="sent_by_13" /></div><div style="width:20%; float:left;"><input type="submit" name="btnSubmitCheckList_13" value="Save" /></div>
           </div>
 </div>
 </div>
</div>
          
          
          
          
          
          
          
          
           
           
           
            
          
          
           
           
           
           <div style="width:100%; text-align:left; float:left; margin-top:10px; margin-left:5px; padding:0px;">
                            <input type="submit" name="Submit" value="Change" class="button_medium">
                         <a href="addnote.php?uid=<?php echo $recb['customer_id'];?>" onClick="wopen('addnote.php?uid=<?php echo $recb['customer_id'];?>', 'popup', 500, 500); return false;" target="popup">
                          <input type="button" name="Upload" value="Upload File" class="button_medium">
                          </a>   
                          
           <?php /*?> <a href="sendmailsinglecust.php?bid=<?php echo $recb['customer_id'];?>" onClick="wopen2('sendmailsinglecust.php?bid=<?php echo $recb['customer_id'];?>', 'popup', 700, 550); return false;" target="popup">
                          <input type="button" name="Mail" value="Send Mail" class="button_medium">
                          </a>     <?php */?>          
           
           
           </div>
         
           
           	<div class="pagination" style="float:left;">
            
            <?php if($row_offset_position['POSITION']>0){ ?>
            <a href="buyerinfo1.php?rid=<?=$row_previous['customer_id']?>"><img border="0" src="images/prev.png" /></a>
            <?php }else{ ?>
            <span class="disabled"><img border="0" src="images/prev.png" /></span>
            <?php } ?>
            
            <?php if( $row_offset_position['POSITION'] < ($row_record_count['totalCount']-1)){ ?>
            <a href="buyerinfo1.php?rid=<?=$row_next['customer_id']?>"><img border="0" src="images/next.png" /></a>
            <?php }else{ ?>
            <span class="disabled"><img border="0" src="images/next.png" /></span>
            <?php } ?>
           	
           </div>
           <div style="width:100%; float:left;"><?php echo $msg;?></div>
           
           
           
          
             
            </form></td>
        </tr>
        
        <!-- footer-->
       <?php /*?> <tr>
          <td><?php include("footer.php");?></td>
        </tr><?php */?>
        <!-- footer end-->
      </table></td>
  </tr>
</table>
</body>
</html>
