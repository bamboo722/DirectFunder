<?php
session_start();
if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
	header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

$sql = "select * from admin_user where user_id='".$_GET['uid']."'";
$res = mysql_query($sql) or die(mysql_error()."777");
$rec = mysql_fetch_assoc($res);

if($_POST['Change'] == "Change" )
{
	if($_POST['pw'] == "")
	{
		$msg="Please enter a password.";
	}	
	elseif($_POST['pw'] != $_POST['rpw'])
	{
		$msg="The passwords you entered did not match.";
	}
	else
	{			
		$upd_crdl="update admin_user set 
		e_mail='".clean($_POST['emailid'])."',
		password='".clean($_POST['pw'])."'
		where user_id='".$_GET['uid']."'";
		//print($upd_crdl);
		mysql_query($upd_crdl) or die(mysql_error());
		header("Location: changecredential.php?uid=".$_GET['uid']);
		exit();
	}	
}
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
			    width:14%;
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
        
    </head>
    <body>
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
		
		<div class="container">
			<br>
			<div class="row">
				<div class="col-sm-12">
					<center>
						<h1>Change Credential</h1>
						<br>
					</center>
				</div>
			</div>
		    <div class='row'>
		    	<div class='col-sm-4 col-md-offset-4'>
		          <form name="default_emplate" id="default_emplate" method="post" enctype="multipart/form-data" onsubmit="return stepcheck();">
		          	<div class='form-row'>
		              <div class='col-xs-12 form-group' style="margin-left:0px;margin-right:0px">
		                <label class='col-xs-6 control-label'>User Group : </label>
		                <label class='col-xs-6 control-label' style="color:green"><?php echo $rec['user_group'];?></label>		                
		              </div>
		            </div>
		            
		          	<div class='form-row'>
		              <div class='col-xs-12 form-group' style="margin-left:0px;margin-right:0px">
		                <label class='col-xs-6 control-label'>User Id : </label>
		                <label class='col-xs-6 control-label' style="color:green"><?php echo $rec['user_id'];?></label>		                		                
		              </div>
		  			</div>		  			
		  			
		  			<div class='form-row'>
		              <div class='col-xs-12 form-group' style="margin-left:0px;margin-right:0px">
		                <label class='control-label'>User Email</label>
		                <input name="emailid" type="email" id="emailid"  class='form-control'  value="<?php if(isset($_POST['emailid'])){echo $_POST['emailid'];}else{echo $rec['e_mail'];}?>">
		     		  </div>
		            </div>
		  
		  
		            <div class='form-row'>
		              <div class='col-xs-12 form-group' style="margin-left:0px;margin-right:0px">
		                <label class='control-label'>New password</label>
		                <input name="pw" autocomplete='off' type="password" id="pw" class='form-control' value="<?php if(isset($_POST['pw'])){echo $_POST['pw'];}?>">            
		              </div>
		            </div>
		            
		            <div class='form-row'>
		              <div class='col-xs-12 form-group' style="margin-left:0px;margin-right:0px">
		                <label class='control-label'>Confirm password</label>
		                <input name="rpw" autocomplete='off' type="password" id="rpw" class="form-control" value="<?php if(isset($_POST['rpw'])){echo $_POST['rpw'];}?>">
		                
		              </div>
		            </div>    
		           
		            <div class='form-row'>
		              <div class='col-xs-12 form-group' style="margin-left:0px;margin-right:0px">
		                <button class='form-control btn btn-primary submit-button' type='submit' name="Change" value="Change">Change</button>                
		              </div>
		            </div>
		          </form>
		        </div>
		    </div>
			<div class="row">
				<div class="col-sm-12">
					<center>
						<h6 style="color:blue;"><?php echo $msg;?></h6>
						<br>
					</center>
				</div>
			</div>
		</div>		
				
    </body>
</html>
