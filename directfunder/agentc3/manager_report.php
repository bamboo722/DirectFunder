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

mysql_query("set time_zone='-7:00';");
$sql_time = "SELECT curdate() as now";
$sql_res = mysql_query($sql_time) or die(mysql_error());
$sql_rec = mysql_fetch_assoc($sql_res);
	
$to_dt = $sql_rec['now'];
$follow_up = explode("-", $to_dt);
$to_dt = $follow_up['1'] . '/' . $follow_up['2'] . '/' . $follow_up['0'];  		

$from_dt = '01/01/1900';
if (isset($_POST['to_dt']) and $_POST['to_dt'] != "")
	$to_dt =  trim($_POST['to_dt']);
if (isset($_POST['from_dt']) and $_POST['from_dt'] != "")
	$from_dt = trim($_POST['from_dt']);

// change date format from MM/DD/YYYY -> YYYY-MM-DD 
if (isset($to_dt))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$to_dt)) != 0)
	{
		$follow_up = explode("/", $to_dt);
        $to_dt = $follow_up['2'] . '-' . $follow_up['0'] . '-' . ($follow_up['1']+1);  		
	}		
}
if (isset($from_dt))
{
	if ((int)(preg_replace("/[^0-9]*/s", "",$from_dt)) != 0)
	{
		$follow_up = explode("/", $from_dt);
        $from_dt = $follow_up['2'] . '-' . $follow_up['0'] . '-' . ($follow_up['1']-1);  		
	}		
}

$agent_ary = array();
$i=0;
$google_phone = $_SESSION['tw_number'];
$tw_phone = $_SESSION['tw_number'];

//get Total, Average
$agent_ary['total']	= 0;
$agent_ary['average']	= 0;
	
// get all agents who are belong to manager 
$sql_sel = sprintf("select user_id,tw_number,google_acc_nm from admin_user where owner='%s'",$_SESSION['user_login']);
$sql_res = mysql_query($sql_sel) or die(mysql_error());
while($sql_rec = mysql_fetch_assoc($sql_res))
{
	$agent_ary[$i]['agent']	= $sql_rec['user_id'];
	
	if ($sql_rec['user_id']!="")	
		$filterAgent .= " or agent='".$sql_rec['user_id']."'";
	
	$google_phone = $sql_rec['tw_number'];
	$tw_phone = $sql_rec['tw_number'];


	// Call
	$sql_click_call = "SELECT count(*) as calls from call_log_info where ((from_phone like ('".$tw_phone."')) or (to_phone like ('".$tw_phone."')) and log_time > '".$from_dt."' and log_time < '".$to_dt."')";
	$sql_click_call_resul = mysql_query($sql_click_call) or die(mysql_error());
	$sql_click_call_ary = mysql_fetch_assoc($sql_click_call_resul);

	// SMS
	$sql_click_sms = "SELECT count(*) as sms from sms_log_info where ((from_phone like ('".$google_phone."')) or (to_phone like ('".$google_phone."')) and log_time > '".$from_dt."' and log_time < '".$to_dt."')";
	$sql_click_sms_resul = mysql_query($sql_click_sms) or die(mysql_error());
	$sql_click_sms_ary = mysql_fetch_assoc($sql_click_sms_resul);

	// Email
	
	$sql_click_email = "SELECT count(*) as email  from mail_log_info where ((from_address like ('".$sql_rec['google_acc_nm']."')) or (mail_rcvr like ('".$sql_rec['google_acc_nm']."')) and send_dt > '".$from_dt."' and send_dt < '".$to_dt."') ";
	$sql_click_email_resul = mysql_query($sql_click_email) or die(mysql_error());
	$sql_click_email_ary = mysql_fetch_assoc($sql_click_email_resul);
	
	if (isset($sql_click_call_ary['calls']) and $sql_click_call_ary['calls']!="")
		$agent_ary[$i]['calls']	= (int)$sql_click_call_ary['calls'];
	else
		$agent_ary[$i]['calls']	= 0;
	if (isset($sql_click_email_ary['email']) and $sql_click_email_ary['email']!="")
		$agent_ary[$i]['email']	= (int)$sql_click_email_ary['email'];
	else
		$agent_ary[$i]['email']	= 0;
	if (isset($sql_click_sms_ary['sms']) and $sql_click_sms_ary['sms']!="")
		$agent_ary[$i]['sms']	= (int)$sql_click_sms_ary['sms'];
	else
		$agent_ary[$i]['sms']	= 0;
				
	// get statistics for every agent 
	$sql_stat_sel = sprintf("SELECT SUM(CASE WHEN priority_opt = 'New' THEN 1 ELSE 0 END) As newLeads,SUM(CASE WHEN priority_opt = 'Retry' THEN 1 ELSE 0 END) As retryLeads,SUM(CASE WHEN (apply_dt = now() ) THEN 1 ELSE 0 END) As today_task, SUM(CASE WHEN (priority_opt = 'Hot' ) THEN 1 ELSE 0 END) As hotLeads,SUM(CASE WHEN priority_opt = 'Warm' THEN 1 ELSE 0 END) As warmLeads,SUM(CASE WHEN priority_opt = 'Credit Check' THEN 1 ELSE 0 END) As credit_checks,SUM(CASE WHEN priority_opt = 'Credit Repair' THEN 1 ELSE 0 END) As credit_repairs,SUM(CASE WHEN priority_opt = 'Credit Ready' THEN 1 ELSE 0 END) As credit_ready,SUM(CASE WHEN priority_opt = 'Doc. Sent' THEN 1 ELSE 0 END) As doc_sents,SUM(CASE WHEN priority_opt = 'Funded' THEN 1 ELSE 0 END) As fundedLeads, SUM(CASE WHEN priority_opt = 'Pending Funding' THEN 1 ELSE 0 END) As pending_fundings, SUM(CASE WHEN priority_opt = 'Fee Pending' THEN 1 ELSE 0 END) As fee_pendings, SUM(CASE WHEN priority_opt = 'Pre-approved' THEN 1 ELSE 0 END) As pre_approveds,SUM(CASE WHEN priority_opt = 'Funded' THEN 1 ELSE 0 END) As fundedLeads, SUM(CASE WHEN priority_opt = 'Clients' THEN 1 ELSE 0 END) As clients,SUM(CASE WHEN priority_opt = 'Fee Pending' THEN 1 ELSE 0 END) As fee_pending,SUM(CASE WHEN priority_opt = 'Clickthroughs' THEN 1 ELSE 0 END) As clickthroughs,SUM(CASE WHEN priority_opt = 'Opened Emails' THEN 1 ELSE 0 END) As opened_emails,SUM(CASE WHEN is_opportunity_yes = 1 THEN 1 ELSE 0 END) As other_opportunity from customer_info WHERE agent='%s'",$agent_ary[$i]['agent']);
	$sql_stat_res = mysql_query($sql_stat_sel) or die(mysql_error());
	$sql_stat_rec = mysql_fetch_assoc($sql_stat_res);
	
	if (isset($sql_stat_rec))
	{
		$agent_ary[$i]['newLeads']	= (int)$sql_stat_rec['newLeads'];
		$agent_ary[$i]['opened_emails']	= (int)$sql_stat_rec['opened_emails'];
		$agent_ary[$i]['clickthroughs']	= (int)$sql_stat_rec['clickthroughs'];
		$agent_ary[$i]['retryLeads']	= (int)$sql_stat_rec['retryLeads'];
		$agent_ary[$i]['followUpCount']	= (int)$sql_stat_rec['followUpCount'];
		$agent_ary[$i]['past_due']	= (int)$sql_stat_rec['past_due'];
		$agent_ary[$i]['delinquent']	= (int)$sql_stat_rec['delinquent'];
		$agent_ary[$i]['no_follow_up_date']	= (int)$sql_stat_rec['no_follow_up_date'];
		$agent_ary[$i]['warmLeads']	= (int)$sql_stat_rec['warmLeads'];
		$agent_ary[$i]['hotLeads']	= (int)$sql_stat_rec['hotLeads'];
		$agent_ary[$i]['credit_checks']	= (int)$sql_stat_rec['credit_checks'];
		$agent_ary[$i]['credit_repairs']	= (int)$sql_stat_rec['credit_repairs'];
		
		
		$agent_ary[$i]['credit_ready']	= (int)$sql_stat_rec['credit_ready'];
		$agent_ary[$i]['pre_approveds']	= (int)$sql_stat_rec['pre_approveds'];
		$agent_ary[$i]['doc_sents']	= (int)$sql_stat_rec['doc_sents'];
		$agent_ary[$i]['pending_fundings']	= (int)$sql_stat_rec['pending_fundings'];
		$agent_ary[$i]['fundedLeads']	= (int)$sql_stat_rec['fundedLeads'];
		$agent_ary[$i]['thirty_day_funding']	= (int)$sql_stat_rec['thirty_day_funding'];
		$agent_ary[$i]['sixty_day_funding']	= (int)$sql_stat_rec['sixty_day_funding'];
		$agent_ary[$i]['sixty_ninety_day_fundings']	= (int)$sql_stat_rec['sixty_ninety_day_fundings'];
		$agent_ary[$i]['clients']	= (int)$sql_stat_rec['clients'];
		$agent_ary[$i]['other_opportunity']	= (int)$sql_stat_rec['other_opportunity'];
		$agent_ary[$i]['fee_pending']	= (int)$sql_stat_rec['fee_pending'];
		
		
	}		
	// get performance info for every agent 
	$sql_sum = sprintf("select sum(dur_hour*3600+dur_min*60+dur_sec) as duration,sum(conv_min) as conv_min, sum(call_made) as call_made,sum(call_conn) as call_conn,sum(sms_sent) as sms_sent, sum(sms_recv) as sms_recv,sum(eml_sent) as eml_sent, sum(eml_recv) as eml_recv,sum(ratio) as ratio from agent_log_info where agent='%s' and log_in>'%s' and log_in<'%s'",$agent_ary[$i]['agent'],$from_dt,$to_dt);
	$result_sum = mysql_query($sql_sum) or die(mysql_error());
	$sum_array = mysql_fetch_assoc($result_sum);
	$agent_ary[$i]['duration']	= (int)$sum_array['duration'];
	$agent_ary[$i]['conv_min']	= (int)$sum_array['conv_min'];
	$agent_ary[$i]['call_made']	= (int)$sum_array['call_made'];
	$agent_ary[$i]['call_conn']	= (int)$sum_array['call_conn'];
	$agent_ary[$i]['sms_sent']	= (int)$sum_array['sms_sent'];
	$agent_ary[$i]['sms_recv']	= (int)$sum_array['sms_recv'];
	$agent_ary[$i]['eml_sent']	= (int)$sum_array['eml_sent'];
	$agent_ary[$i]['eml_recv']	= (int)$sum_array['eml_recv'];
	if ((int)($agent_ary[$i]['duration']/60) == 0)
		$agent_ary[$i]['ratio']=0;
	else
		$agent_ary[$i]['ratio']=sprintf("%.1f",(float)($agent_ary[$i]['conv_min']*100/((int)($agent_ary[$i]['duration']/60))));	
	
	// get Revenue, Total, Average
	
	// get sum of fee amounts under opportunity tab of customer info for each agent
	$agent_ary[$i]['revenue'] = 0;
			
	$sql_sel_fees = sprintf("select sum(fee_amount) as fee_amounts from opportunity_info,(select customer_id from customer_info where agent='%s') as selected_customer_info where (opportunity_info.customer_id = selected_customer_info.customer_id) and  date_paid>'%s' and date_paid<'%s'",$agent_ary[$i]['agent'],$from_dt,$to_dt);
	$sql_res_fees = mysql_query($sql_sel_fees) or die(mysql_error());
	$sql_rec_fees = mysql_fetch_assoc($sql_res_fees);
	if (isset($sql_rec_fees) and  $sql_rec_fees['fee_amounts']!=NULL)
		$agent_ary[$i]['revenue'] += $sql_rec_fees['fee_amounts'];		
		
	
	$agent_ary['total']+=$agent_ary[$i]['revenue'];	
	$i++;
}
if ($i != 0)
	$agent_ary['average'] = round($agent_ary['total']/$i,2);



    

$top_duration=$agent_ary[0]['duration'];
$bottom_duration =$agent_ary[0]['duration'];
$top_conv_min=$agent_ary[0]['conv_min'];
$bottom_conv_min =$agent_ary[0]['conv_min'];
$top_ratio=$agent_ary[0]['ratio'];
$bottom_ratio=$agent_ary[0]['ratio'];
for ($i=0;$i<10;$i++)
{
	if ($agent_ary[$i]['duration']!=null and $top_duration < $agent_ary[$i]['duration'])
		$top_duration = $agent_ary[$i]['duration'];
	if ($agent_ary[$i]['conv_min']!=null and $top_conv_min < $agent_ary[$i]['conv_min'])
		$top_conv_min = $agent_ary[$i]['conv_min'];
	if ($agent_ary[$i]['ratio']!=null and $top_ratio < $agent_ary[$i]['ratio'])
		$top_ratio = $agent_ary[$i]['ratio'];
	
	if ($agent_ary[$i]['duration']!=null and $bottom_duration > $agent_ary[$i]['duration'])
		$bottom_duration = $agent_ary[$i]['duration'];
	if ($agent_ary[$i]['conv_min']!=null and $bottom_conv_min > $agent_ary[$i]['conv_min'])
		$bottom_conv_min = $agent_ary[$i]['conv_min'];
	if ($agent_ary[$i]['ratio']!=null and $bottom_ratio > $agent_ary[$i]['ratio'])
		$bottom_ratio = $agent_ary[$i]['ratio'];		
}

// get Report 2;
// Highest Approved Limit Cards
$approved_card_ary = array();
$cd_cnt=0;
$sql_sel = sprintf("select sum(appr_lim) as appr_sum_lim,cd_name from funding_info,(select customer_id from customer_info,(select user_id from admin_user where owner='%s') as selected_user_id where customer_info.agent = selected_user_id.user_id) as selected_customer_info where funding_info.customer_id=selected_customer_info.customer_id group by cd_name order by appr_sum_lim desc",$_SESSION['user_login']);
$sql_res = mysql_query($sql_sel) or die(mysql_error());
while($sql_rec = mysql_fetch_assoc($sql_res))
{
	$approved_card_ary[$cd_cnt]['cd_name']	= $sql_rec['cd_name'];
	$cd_cnt++;
}

// Best Opportunity
$best_opp_ary = array();
$opp_cnt=0;
$sql_sel = sprintf("select sum(case when yes_no='yes' THEN 1 ELSE 0 END) as yes_nos,opportunity from opportunity_info,(select customer_id from customer_info,(select user_id from admin_user where owner='%s') as selected_user_id where customer_info.agent = selected_user_id.user_id) as selected_customer_info where opportunity_info.customer_id=selected_customer_info.customer_id group by opportunity order by yes_nos desc",$_SESSION['user_login']);
$sql_res = mysql_query($sql_sel) or die(mysql_error());
while($sql_rec = mysql_fetch_assoc($sql_res))
{
	if (trim($sql_rec['opportunity']) != "" and $sql_rec['yes_nos'] >0)
	{
		$best_opp_ary[$opp_cnt]['opportunity']	= $sql_rec['opportunity'];
		$opp_cnt++;		
	}
}

// Best Source

$best_src_ary = array();
$src_cnt=0;
$sql_sel = sprintf("select lead_src,count(*) as source_cnt from customer_info,(select user_id from admin_user where owner='%s') as selected_user_id where customer_info.agent = selected_user_id.user_id group by lead_src order by source_cnt desc",$_SESSION['user_login']);
$sql_res = mysql_query($sql_sel) or die(mysql_error());
while($sql_rec = mysql_fetch_assoc($sql_res))
{
	if (trim($sql_rec['lead_src']) != "")
	{
		$best_src_ary[$src_cnt]['lead_src']	= $sql_rec['lead_src'];
		$src_cnt++;		
	}
}
$max_row_cnt=max($opp_cnt,$cd_cnt,$src_cnt);
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
			    width:11% !important;
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

			/* form control without padding and low height */
			.my-form-control {
			    padding:0px !important;
			    height:34px !important;
			    font-size:18px !important;
			    font-weight:300 !important;
			    color:black !important;
			}
			.my-form-control-left-text {
				margin-top:3px !important;
				padding-left:2px !important;
				padding-right:2px !important;
			    
			}
			
			.my-row{
				margin-right:1px; 
    			margin-left: 1px; 
			}
			/* grid table padding */
			.my-grid-table{
				padding:1px;
				text-align:center;
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

			
			/* Kelvin */
			@media (min-width: 1100px) {
			  .container {
			    width: 1070px;
			  }
			}			
		
			/*************/
			
			/****************************************************************/
        </style>
    </head>
    <body>
        <script type="text/javascript" src="popcalendar.js"></script>
                
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
		                    
		                    <!-- History : all histories of call, email, sms -->
					      	<li class="active"><a href="#">HISTORY<span class="sr-only">(current)</span></a></li>
					        
			                <li >
				            	<a href="javascript:GetCallHistory();" style="padding:0px"><p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Call</p></a>
			                </li>
			                <li >
				            	<a href="javascript:GetEmailHistory();" style="padding:0px"><p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Email</p></a>
			                </li>
			                <li >
				            	<a href="javascript:GetSMSHistory();" style="padding:0px"><p style="padding: 10px 15px;line-height: 20px;margin-bottom: 0px">Sms</p></a>
			                </li>
							
							<!-- Click to : ClicktoCall, ClicktoSMS, ClicktoEmail -->
							
							<li class="active"><a href="#">Group Texts and Emails<span class="sr-only">(current)</span></a></li>
							<form class="form"  class="navbar-form navbar-left" style="padding:10px 15px 10px 15px;margin:5px 0px 5px 0px" method="post" action="searchbuyer.php">
								<div class="form-group my-form-group">
									<!--<button type="button" class="btn btn-success btn-md my-sms-button" id="phone_call_btn_mobile">Call<span id="call_new_span_mobile" style="visibility:hidden;color:white !important;background-color:red !important" class="badge"></span></button>			    		-->
									<button type="button" class="btn btn-success btn-sm my-sms-button" id="send_sms_btn_mobile">SMS<span id="sms_news_span_mobile" style="visibility:hidden;color:white !important;background-color:red !important" class="badge"></span></button>
									<button type="button" class="btn btn-success btn-sm my-sms-button" id="send_eml_btn_mobile">Email<span id="email_new_span_mobile" style="visibility:hidden;color:white !important;background-color:red !important" class="badge"></span></button>
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
		
		<h1>&nbsp;</h1>
		<!-- main content -->
		<div class="container" style="margin:1px;padding:0px;width:99%">
			
			<!-- title -->		
			<div class="row my-row">				
				<center><h1 style="margin-top:0px">Manager Report</h1></center>				
			</div>	
			<br>
			<!-- report tab panel -->
   			<div class="row my-row">
   				<div class="panel panel-success">
   					
   					<!-- panel heading -->	
					<div class="panel-heading">
					</div>

					<!-- Tabs -->
					<div class="panel-body" style="padding-left:2px;padding-right:2px">
						<form name="default_emplate" id="default_emplate" method="post" enctype="multipart/form-data">

                          	<!-- Nav tabs -->
                            <ul class="nav nav-tabs desktop-my-menu" style="font-size:14px">
							 	<li class="active"><a href="#report1_tab" data-toggle="tab">Report1</a></li>
							    <li class=""><a href="#report2_tab" data-toggle="tab">Report2</a></li>							    
							 </ul>
							<ul class="nav nav-tabs mobile-my-menu" style="font-size:16px !important;">
							 	<li class="active"><a href="#report1_tab" data-toggle="tab" style="padding:6px">Report1</a></li>
							    <li class=""><a href="#report2_tab" data-toggle="tab"  style="padding:6px">Report2</a></li>							    
							 </ul>

							<!-- Tab panes -->
                            <div class="tab-content">
                               	<div class="tab-pane fade in active" id="report1_tab">
                            		<!--<h3>Report1</h3>-->
                            		<h5></h5>
                            		<div class="row my-row">                            			
                            			<div class="col-xs-5 col-sm-offset-2 col-sm-3" style="padding:0px">
                            				<div class="form-group" >
												<center>
													<label style="width:30%;text-align:left" for="from_dt">From</label>
													<input name="from_dt" id="from_dt" class="form-control  my-form-control" style="display:inline-block;width:65%" type="text"   onFocus='popUpCalendar(this, document.default_emplate.from_dt, "mm/dd/yyyy")'  value=" <?php if (isset($_POST['from_dt'])) echo $_POST['from_dt']; ?>">
												</center>
											</div>
                            			</div>
                            			<div class="col-xs-5 col-sm-3" style="padding:0px">
                            				<div class="form-group" >
                            					<center>
													<label style="width:13%;text-align:left" for="to_dt">To</label>
													<input name="to_dt" id="to_dt" class="form-control  my-form-control" style="display:inline-block;width:65%" type="text"   onFocus='popUpCalendar(this, document.default_emplate.to_dt, "mm/dd/yyyy")'  value=" <?php if (isset($_POST['to_dt'])) echo $_POST['to_dt']; ?>">
												</center>												
											</div>
                            			</div>
                            			<div class="col-xs-2 col-sm-2" style="padding:0px">
                            				<div class="form-group" >
						                		<button class="form-control btn btn-primary btn-xs" type="submit" name="Submit" value="Report">Report</button>                
						              		</div>
                            			</div>
                            			<div>
                            				<div class="form-group" >
						                		<input type="hidden"  value="Report"/>
						              		</div>
                            			</div>                            			
                            		</div>
                                	<div class="row my-row">
										<div class="table-responsive" style="margin:2px;padding:2px;width:99%">
											<table class="table table-striped" style="margin:5px" width="100%">
							    				<thead>
													<tr>
														<th >Variables</th>
													<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['agent']) and $agent_ary[$i]['agent']!="")
															{
															?>
																<th  ><?php echo $agent_ary[$i]['agent'];?></th>
															<?php	
															}else
															{															
															?>
																<th ></th>
															<?php
															}
														}
													?>						                               
						                            <tr/>
												</thead>
												<tbody>
													<tr style="font-weight:bold;background-color:#ddd">
														<td style="width:15%">WorkSpace</td>
														<td  colspan='10'></td>
													</tr>
													<tr >
														<td style="width:15%">Calls</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['calls']) and $agent_ary[$i]['calls']!="")
															{
															?>
																<td  style="width:8%"><?php echo $agent_ary[$i]['calls'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Email</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['email']) and $agent_ary[$i]['email']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['email'];?></td>
															<?php	
															}else
															{															
															?>
																<td></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">SMS</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['sms']) and $agent_ary[$i]['sms']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['sms'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Task</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['followUpCount']) and $agent_ary[$i]['followUpCount']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['followUpCount'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Past Due</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['past_due']) and $agent_ary[$i]['past_due']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['past_due'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Delinquent</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['delinquent']) and $agent_ary[$i]['delinquent']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['delinquent'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">No FollowUp Date</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['no_follow_up_date']) and $agent_ary[$i]['no_follow_up_date']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['no_follow_up_date'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>

													<tr style="font-weight:bold;background-color:#ddd">
														<td style="width:15%">Sales</td>
														<td  colspan='10'></td>
													</tr>
													<tr>
														<td style="width:15%">New Leads</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['newLeads']) and $agent_ary[$i]['newLeads']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['newLeads'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Opened Emails</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['opened_emails']) and $agent_ary[$i]['opened_emails']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['opened_emails'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Clickthroughs</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['clickthroughs']) and $agent_ary[$i]['clickthroughs']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['clickthroughs'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Retry</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['retryLeads']) and $agent_ary[$i]['retryLeads']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['retryLeads'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Hot</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['hotLeads']) and $agent_ary[$i]['hotLeads']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['hotLeads'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Warm</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['warmLeads']) and $agent_ary[$i]['warmLeads']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['warmLeads'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Credit Check</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['credit_checks']) and $agent_ary[$i]['credit_checks']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['credit_checks'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Credit Repair</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['credit_repairs']) and $agent_ary[$i]['credit_repairs']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['credit_repairs'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Credit Ready</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['credit_ready']) and $agent_ary[$i]['credit_ready']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['credit_ready'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Other Opportunity</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['other_opportunity']) and $agent_ary[$i]['other_opportunity']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['other_opportunity'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													
													<tr style="font-weight:bold;background-color:#ddd">
														<td style="width:15%">STATISTIC</td>
														<td  colspan='10'></td>
													</tr>
													<tr>
														<td style="width:15%">Pre-approved</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['pre_approveds']) and $agent_ary[$i]['pre_approveds']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['pre_approveds'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Doc. Sent</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['doc_sents']) and $agent_ary[$i]['doc_sents']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['doc_sents'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Pending Funding</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['pending_fundings']) and $agent_ary[$i]['pending_fundings']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['pending_fundings'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Funded</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['fundedLeads']) and $agent_ary[$i]['fundedLeads']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['fundedLeads'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Fee Pending</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['fee_pending']) and $agent_ary[$i]['fee_pending']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['fee_pending'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">30 day funding</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['thirty_day_funding']) and $agent_ary[$i]['thirty_day_funding']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['thirty_day_funding'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">60 day funding</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['sixty_day_funding']) and $agent_ary[$i]['sixty_day_funding']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['sixty_day_funding'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">90 day funding</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['sixty_ninety_day_fundings']) and $agent_ary[$i]['sixty_ninety_day_fundings']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['sixty_ninety_day_fundings'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>
													<tr>
														<td style="width:15%">Clients</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['clients']) and $agent_ary[$i]['clients']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['clients'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	

													<tr style="font-weight:bold;background-color:#ddd">
														<td style="width:15%">PERFORMANCE</td>
														<td  colspan='10'></td>
													</tr>	
													<tr>
														<td style="width:15%">Duration</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['duration']) and $agent_ary[$i]['duration']!="")
															{
															?>
																<td  <?php 
																if ($agent_ary[$i]['duration']==$top_duration) 
																	echo "style='color:green'"; 
																else if ($agent_ary[$i]['duration']==$bottom_duration) 
																	echo "style='color:red'"; 
																?>> <?php echo (int)($agent_ary[$i]['duration']/3600).':'.(int)(($agent_ary[$i]['duration']%3600)/60).':'.(int)($agent_ary[$i]['duration']%60);?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">Phone Minutes</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['conv_min']) and $agent_ary[$i]['conv_min']!="")
															{
															?>
																<td  <?php 
																if ($agent_ary[$i]['conv_min']==$top_conv_min) 
																	echo "style='color:green'"; 
																else if ($agent_ary[$i]['conv_min']==$bottom_conv_min) 
																	echo "style='color:red'"; 
																?>><?php echo $agent_ary[$i]['conv_min'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">Phone Calls/Connected</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['call_made']) and isset($agent_ary[$i]['call_conn']) and ($agent_ary[$i]['call_made']!=""  or $agent_ary[$i]['call_conn']!=""))
															{
															?>
																<td  ><?php echo $agent_ary[$i]['call_made'].'/'.$agent_ary[$i]['call_conn'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">SMS Sent/Received</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['sms_sent']) and isset($agent_ary[$i]['sms_recv']) and ($agent_ary[$i]['sms_sent']!="" or $agent_ary[$i]['sms_recv']!=""))
															{
															?>
																<td  ><?php echo $agent_ary[$i]['sms_sent'].'/'.$agent_ary[$i]['sms_recv'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">Email Sent/Received</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['eml_sent']) and isset($agent_ary[$i]['eml_recv']) and ($agent_ary[$i]['eml_sent']!=""  or $agent_ary[$i]['eml_recv']!=""))
															{
															?>
																<td  ><?php echo $agent_ary[$i]['eml_sent'].'/'.$agent_ary[$i]['eml_recv'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">Ratio</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['ratio']) and $agent_ary[$i]['ratio']!="")
															{
															?>
																<td  <?php 
																if ($agent_ary[$i]['ratio']==$top_ratio) 
																	echo "style='color:green'"; 
																else if ($agent_ary[$i]['ratio']==$bottom_ratio) 
																	echo "style='color:red'"; 
																?> ><?php echo $agent_ary[$i]['ratio'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">Revenue</td>
														<?php
														for ($i=0;$i<10;$i++)
														{
															if (isset($agent_ary[$i]['revenue']) and $agent_ary[$i]['revenue']!="")
															{
															?>
																<td  ><?php echo $agent_ary[$i]['revenue'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														}
														?>														
													</tr>	
													<tr>
														<td style="width:15%">Total</td>
														<?php
														
															if (isset($agent_ary['total']) and $agent_ary['total']!="")
															{
															?>
																<td  ><?php echo $agent_ary['total'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}
														
														?>	
														<td colspan='9' ></td>													
													</tr>	
													<tr>
														<td style="width:15%">Average</td>
														<?php														
															if (isset($agent_ary['average']) and $agent_ary['average']!="")
															{
															?>
																<td  ><?php echo $agent_ary['average'];?></td>
															<?php	
															}else
															{															
															?>
																<td style="width:8%"></td>
															<?php
															}														
														?>	
														<td colspan='9' ></td>
													</tr>						
												</tbody>
											</table>										
										</div>
									</div>
								</div>	
								<div class="tab-pane fade" id="report2_tab">
                            		<!--<h3>Report2</h3>-->
                            		<h5></h5>
                            		<div class="row my-row">
										<div class="table-responsive" style="margin:1px;padding:1px;">
											<table class="table table-striped " style="margin:10px" width="100%">
							    				<thead>
													<tr>
														<th>Highest Approved Limit Cards</th>
														<th>Best Opportunity</th>
														<th>Best Source</th>                 
						                            <tr/>
												</thead>
												<tbody>															
													<?php		
													for ($i=0;$i<$max_row_cnt;$i++)												
													{
													?>
														<tr>
															<!-- Highest Approved Limit Cards -->
															<?php	
															if ($i<$cd_cnt and ($approved_card_ary[$i]) and $approved_card_ary[$i]['cd_name']!="")
															{
															?>
																<td><?php echo $approved_card_ary[$i]['cd_name'];?></td>
															<?php	
															}else
															{
															?>
																<td ></td>
															<?php															
															}
															?>
															
															<!-- Best Opportunity -->
															<?php	
															if ($i<$opp_cnt and ($best_opp_ary[$i]) and $best_opp_ary[$i]['opportunity']!="")
															{
															?>
																<td><?php echo $best_opp_ary[$i]['opportunity'];?></td>
															<?php	
															}else
															{
															?>
																<td ></td>
															<?php															
															}
															?>
															
															<!-- Best Source -->
															<?php	
															if ($i<$src_cnt and ($best_src_ary[$i]) and $best_src_ary[$i]['lead_src']!="")
															{
															?>
																<td><?php echo $best_src_ary[$i]['lead_src'];?></td>
															<?php	
															}else
															{
															?>
																<td ></td>
															<?php															
															}
															?>
															
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
				       </form>
					</div>
				</div>
	   		</div>    		
        
        </div>
                
    </body>
 	
</html>