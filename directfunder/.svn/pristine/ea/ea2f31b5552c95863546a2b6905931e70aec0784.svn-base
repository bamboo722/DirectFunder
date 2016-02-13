<?php
/****-------------------------------------------------------------------**************************	
		Purpose 	: 	This page will act as the login to the system
		Project 	:	Sales Lead DB	
	 	Developer 	: 	Kelvin Smith
	 	Create Date : 	27/04/2012     
****-------------------------------------------------------------------************************/
//phpinfo();
@session_start();

require_once("includes/dbconnect.php");


/**
* set timezone to Arizona
*/


//$result = mysql_query($sql_select) or die(mysql_error());
//

/* reconstruct customer_info table by reformatting phone numbers */
//$sql_select = "select auto_id,p_ph1,p_ph2,p2_ph1,p2_ph2,p3_ph1,p3_ph2 from customer_info";
//$result = mysql_query($sql_select) or die(mysql_error());
//while ($seerec = mysql_fetch_assoc($result)) {
//	$new_p_ph1 = preg_replace("/[^0-9]*/s", "",$seerec['p_ph1']);
//	$new_p_ph2 = preg_replace("/[^0-9]*/s", "",$seerec['p_ph2']);
//	$new_p2_ph1 = preg_replace("/[^0-9]*/s", "",$seerec['p2_ph1']);
//	$new_p2_ph2 = preg_replace("/[^0-9]*/s", "",$seerec['p2_ph2']);
//	$new_p3_ph1 = preg_replace("/[^0-9]*/s", "",$seerec['p3_ph1']);
//	$new_p3_ph2 = preg_replace("/[^0-9]*/s", "",$seerec['p3_ph2']);
//	//$sql_update = sprintf("update customer_info set p_ph1='%s',p_ph1='%s',p_ph2='%s'",$new_p_ph1,$new_p_ph1,$new_p_ph1);
//	$sql_update = sprintf("update customer_info set p_ph1='%s',p_ph2='%s',p2_ph1='%s',p2_ph2='%s',p3_ph1='%s',p3_ph2='%s' where auto_id='%d'",$new_p_ph1,$new_p_ph2,$new_p2_ph1,$new_p2_ph2,$new_p3_ph1,$new_p3_ph2,$seerec['auto_id']);
//	mysql_query($sql_update) or die(mysql_error());
//}

/**
* init auto_dial_info table
**/
/*  $sql_del = "truncate table auto_dial_info";
  $result = mysql_query($sql_del) or die(mysql_error());
  $sql_sel = "select user_id from admin_user";
  
  $sql_res = mysql_query($sql_sel) or die(mysql_error());

  $row_id=0;
  if (mysql_num_rows($sql_res) == '0'){	

  }else 
  {
	while ($sql_rec = mysql_fetch_assoc($sql_res)) 
	{
		$sql_ins = sprintf("insert into auto_dial_info (user_id,customer_id,ph_1,ph_2,duration,is_called,called_number) select '%s',customer_id,p_ph1,p_ph2,'%d','%d',%d from customer_info where agent='%s'",$sql_rec['user_id'],0,0,0,$sql_rec['user_id']);
		mysql_query($sql_ins) or die(mysql_error());
  	}
  } */
  
 /**
* init auto_dial_status_info table
**/
  $sql_del = "truncate table auto_dial_status_info";
  $result = mysql_query($sql_del) or die(mysql_error());
  $sql_sel = "select user_id from admin_user";
  
  $sql_res = mysql_query($sql_sel) or die(mysql_error());

  $row_id=0;
  if (mysql_num_rows($sql_res) == '0'){	

  }else 
  {
	while ($sql_rec = mysql_fetch_assoc($sql_res)) 
	{
		$sql_ins = sprintf("insert into auto_dial_status_info (user_id,auto_status) values ('%s','%d')",$sql_rec['user_id'],0);
		mysql_query($sql_ins) or die(mysql_error());
  	}
  } 
  
 
/* ------------------------------------------------------------- */


if(isset($_COOKIE['cookie_login']) and $_COOKIE['cookie_login']!="")//session store admin name, modified 20151119
{
	$_SESSION['user_login']=$_COOKIE['cookie_login'];
	$_SESSION['user_password']=$_COOKIE['cookie_password'];
	
	header("Location: adminhome.php");//login in AdminLogin.php
}
if(isset($_POST['Submit']) and $_POST['Submit']=='Log in') // modified 20151119
{ 
		$login=trim($_POST['name']);
		$password=$_POST['pw'];
		
		//selecting associated data from admin_user table where user id is sign id id
		$sql="select user_group,password,owner,google_voice_ph,google_acc_nm,google_acc_pwd,tw_account_sid,tw_auth_token,tw_app_sid,tw_number,eml_marketing_user_eml,eml_marketing_firstname,eml_marketing_lastname,eml_marketing_apikey from admin_user where user_id='".$login."'";  
		$res=mysql_query($sql) or die(mysql_error()."11");
		$array_pass_check=mysql_fetch_assoc($res);
		$group=$array_pass_check['user_group'];
		$owner=$array_pass_check['owner'];
		
        $google_voice_ph = trim($array_pass_check['google_voice_ph']);
        $google_acc_nm = trim($array_pass_check['google_acc_nm']);
        $google_acc_pwd = trim($array_pass_check['google_acc_pwd']);
       
        
        // Twilio Info for Call, SMS
        $tw_account_sid = trim($array_pass_check['tw_account_sid']);
        $tw_auth_token = trim($array_pass_check['tw_auth_token']);
        $tw_app_sid = trim($array_pass_check['tw_app_sid']);
		$tw_number = trim($array_pass_check['tw_number']);
		
		// Email Marketing Info
		$eml_marketing_user_eml = trim($array_pass_check['eml_marketing_user_eml']);
        $eml_marketing_firstname = trim($array_pass_check['eml_marketing_firstname']);
        $eml_marketing_lastname = trim($array_pass_check['eml_marketing_lastname']);
		$eml_marketing_apikey = trim($array_pass_check['eml_marketing_apikey']);
		
		
		//selecting associated data from admin_user table where user id is sign id id end
		//checking whether the user id and password fields are empty 
		if($login=="")
			$msg="Name is empty!";
		else if($password=="")
			$msg="Password is empty!";
		//checking whether the user id and password fields are empty  end
		//matching password
		else if($array_pass_check['password']==$password)
		{	
			if($_POST['cookie']=='Y')
			{
				setcookie("cookie_login",$login,time()+31536000);
				setcookie("cookie_password",$password,time()+31536000);
				setcookie("cookie_group",$group,time()+31536000);
			}
			
			/* check if current user is using now */
			$sql="select cur_login_time  from admin_user where user_id='".$login."'";  
			$res=mysql_query($sql) or die(mysql_error()."11");
			$res_rec=mysql_fetch_assoc($res);
			//if (($res_rec['cur_login_time'] == null) || ($res_rec['cur_login_time'] == "0000-00-00 00:00:00"))
			{
					/* set user info into Session variable */
				
				$_SESSION['user_login']=$login;//creating session level user name for future use
				$_SESSION['user_password']=$password;//creating session level password for future use
				$_SESSION['user_group']=$group;//creating session level password for future use
				$_SESSION['user_owner']=$owner;//user owner, ex : agent->manager, manager->admin
				
				$_SESSION['google_voice_ph']=$google_voice_ph;//creating session level user google voice phone number for future use
				$_SESSION['google_acc_nm']=$google_acc_nm;//creating session level user google account name for future use
				$_SESSION['google_acc_pwd']=$google_acc_pwd;//creating session level user google account password for future use
                
                
                // Twilio info
				$_SESSION['tw_account_sid']=$tw_account_sid;// twilio account sid
				$_SESSION['tw_auth_token']=$tw_auth_token;//twilio auth token
				$_SESSION['tw_app_sid']=$tw_app_sid;//twilio app sid
                $_SESSION['tw_number']=$tw_number;  //twilio number
                $_SESSION['tw_token']=""; //twilio token
                
                // Email Marketing info
				$_SESSION['eml_marketing_user_eml']=$eml_marketing_user_eml;
				$_SESSION['eml_marketing_firstname']=$eml_marketing_firstname;
				$_SESSION['eml_marketing_lastname']=$eml_marketing_lastname;
                $_SESSION['eml_marketing_apikey']=$eml_marketing_apikey; 
               
                
         		$_SESSION['admin_login_time']=time();//creating session level user name for future use
         		
         		/*---- Search user option ----- */
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
				}else if ($_SESSION['user_group'] == "Agent")
			    {
			    	$_SESSION['search_customer']='search_customer';
				}            		
         		
         		/* it is true only if it is login now*/
         		$_SESSION['is_login_now']=1;
         		
         		/* set auto dial status 0 */
         		$sql_set_auto = sprintf("update auto_dial_status_info set auto_status=0 where user_id='%s'",$login);
				mysql_query($sql_set_auto) or die(mysql_error());
				$_SESSION['auto_dial_status']=0;
				
				/* set auto dial info 0 */
				$sql_upd = sprintf("update auto_dial_info set duration='%d',is_called='%d',called_number='%d' where user_id='%s'",0,0,0,$_SESSION['user_login']);
				mysql_query($sql_upd) or die(mysql_error());	

				
         		/*---- Last Login, Duration, Calls Made/Connected, Conversation Minutes, Email Sent/Receive, SMS Sent/Receive, Ratio ----*/
         		
         		/*----- Current Login Time ------ */
         		mysql_query("set time_zone='-7:00';");
         		$sql=sprintf("update admin_user set cur_login_time =sysdate() where user_id='%s'",$login);
				$res=mysql_query($sql) or die(mysql_error()."11");
         		
         		$sql="select cur_login_time  from admin_user where user_id='".$login."'";  
				$res=mysql_query($sql) or die(mysql_error()."11");
				$res_rec=mysql_fetch_assoc($res);
				$_SESSION['cur_login_time']=$res_rec['cur_login_time']; // current login time
				$_SESSION['last_post_time']=$res_rec['cur_login_time'];	// lastest time that connect post_ideas
         		$_SESSION['last_login'] = $res_rec['cur_login_time'];	// lastest time that connect post_ideas
         		$_SESSION['last_logout']=$_SESSION['last_login'];// current login time
         		
         		$_SESSION['duration']='00:00:00';
				$_SESSION['calls_made']=0;
				$_SESSION['calls_con']=0;
								
				$_SESSION['conv_minutes'] = 0;
				$_SESSION['email_sent']=0;
				$_SESSION['email_recv']=0;
					
				$_SESSION['sms_sent']=0;
				$_SESSION['sms_recv']=0;	
				$_SESSION['ratio']=0;	
				
				/* Statistics Value */
				$sql_sel="select * from agent_log_info where  (agent like ('".$login."')) order by auto_id desc limit 1";
				$sql_resul = mysql_query($sql_sel);
				
				$result_array = mysql_fetch_assoc($sql_resul);

				$_SESSION['no_event'] = 1;
				$_SESSION['last_real_logout']=$result_array['log_out'];	
				$_SESSION['newLeads']=$result_array['newLeads'];
				$_SESSION['opened_emails']=$result_array['opened_emails'];
				$_SESSION['clickthroughs']=$result_array['clickthroughs'];
				$_SESSION['retryLeads']=$result_array['retryLeads'];
				$_SESSION['followUpCount']=$result_array['followUpCount'];
				$_SESSION['past_due']=$result_array['past_due'];
				$_SESSION['delinquent']=$result_array['delinquent'];
				$_SESSION['hotLeads']=$result_array['hotLeads'];
				$_SESSION['warmLeads']=$result_array['warmLeads'];
				$_SESSION['credit_checks']=$result_array['credit_checks'];
				$_SESSION['credit_repairs']=$result_array['credit_repairs'];
				$_SESSION['credit_ready']=$result_array['credit_ready'];
				$_SESSION['pre_approveds']=$result_array['pre_approveds'];
				$_SESSION['doc_sents']=$result_array['doc_sents'];
				$_SESSION['pending_fundings']=$result_array['pending_fundings'];
				$_SESSION['fundedLeads']=$result_array['fundedLeads'];
				$_SESSION['fee_pending']=$result_array['fee_pending'];
				$_SESSION['thirty_day_funding']=$result_array['thirty_day_funding'];
				
				$_SESSION['sixty_day_funding']=$result_array['sixty_day_funding'];
				$_SESSION['sixty_ninety_day_fundings']=$result_array['sixty_ninety_day_fundings'];
				$_SESSION['clients']=$result_array['clients'];
				$_SESSION['other_opportunity']=$result_array['other_opportunity'];
				
				$_SESSION['call']=$result_array['calls'];
				$_SESSION['email']=$result_array['email'];
				$_SESSION['sms']=$result_array['sms'];
				
				$_SESSION['no_follow_up_date']=$result_array['no_follow_up_date'];
				
				if ($result_array['newLeads'] == NULL)
					$_SESSION['newLeads'] = 0;
				if ($result_array['opened_emails'] == NULL)
					$_SESSION['opened_emails'] = 0;
				if ($result_array['clickthroughs'] == NULL)
					$_SESSION['clickthroughs'] = 0;
				if ($result_array['retryLeads'] == NULL)
					$_SESSION['retryLeads'] = 0;
				if ($result_array['followUpCount'] == NULL)
					$_SESSION['followUpCount'] = 0;
				if ($result_array['past_due'] == NULL)
					$_SESSION['past_due'] = 0;
				if ($result_array['delinquent'] == NULL)
					$_SESSION['delinquent'] = 0;	
				if ($result_array['hotLeads'] == NULL)
					$_SESSION['hotLeads'] = 0;
				if ($result_array['warmLeads'] == NULL)
					$_SESSION['warmLeads'] = 0;
				if ($result_array['credit_checks'] == NULL)
					$_SESSION['credit_checks'] = 0;
				if ($result_array['credit_repairs'] == NULL)
					$_SESSION['credit_repairs'] = 0;
				if ($result_array['credit_ready'] == NULL)
					$_SESSION['credit_ready'] = 0;
				if ($result_array['pre_approveds'] == NULL)
					$_SESSION['pre_approveds'] = 0;	
				if ($result_array['doc_sents'] == NULL)
					$_SESSION['doc_sents'] = 0;
				if ($result_array['pending_fundings'] == NULL)
					$_SESSION['pending_fundings'] = 0;
				if ($result_array['fundedLeads'] == NULL)
					$_SESSION['fundedLeads'] = 0;
				if ($result_array['fee_pending'] == NULL)
					$_SESSION['fee_pending'] = 0;
				if ($result_array['thirty_day_funding'] == NULL)
					$_SESSION['thirty_day_funding'] = 0;
					
				if ($result_array['sixty_day_funding'] == NULL)
					$_SESSION['sixty_day_funding'] = 0;
				if ($result_array['sixty_ninety_day_fundings'] == NULL)
					$_SESSION['sixty_ninety_day_fundings'] = 0;
				if ($result_array['clients'] == NULL)
					$_SESSION['clients'] = 0;
				if ($result_array['other_opportunity'] == NULL)
					$_SESSION['other_opportunity'] = 0;
				
				/*if ($result_array['calls'] == NULL)
					$_SESSION['call'] = 0;
				if ($result_array['email'] == NULL)
					$_SESSION['email'] = 0;
				if ($result_array['sms'] == NULL)
					$_SESSION['sms'] = 0;							*/
					
			
				// Call, Email, Sms while agent was logged out
				$_SESSION['email'] =-1;
				$_SESSION['logout_email_get_time'] =0;
				$_SESSION['call'] =-1;
				$_SESSION['logout_call_get_time'] =0;
				$_SESSION['sms'] =-1;
				$_SESSION['logout_sms_get_time'] =0;
				
                //directing user to the home page
                                
				header("Location: searchbuyer.php");
				exit();
			}/*else
			{
				$msg="Current User ID is using now!";
			}*/		
		}
		else
		{
				$msg="Wrong User ID or Password!";
		}			
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Sales Lead DB</title>
		<meta charset="utf-8">
	    <meta http-equiv="Content-Type" content="text/html;" content-encoding="gzip" accept-encoding="gzip,deflat">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		 <!-- Bootstrap  -->
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        
        <style type="text/css">
        	/*************** New CSS - Kelvin Smith ***************************/
			.badge {
			  display: inline-block;
			  min-width: 10px;
			  padding: 3px 7px;
			  font-size: 12px;
			  font-weight: bold;
			  line-height: 1;
			  color: #fff;
			  text-align: center;
			  white-space: nowrap;
			  vertical-align: middle;
			  background-color: red !important; /* Kelvin */
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
			  
			  .my-login-box{
				border :8px solid rgb(204,219,226); 
				padding:0px;
				background:rgb(251,252,255);
				border-radius: 12px;
			  }
			   @media (max-width: 767px) {
			   	  body {
					  padding-top : 15px !important;
				  }
				  .main {
				    max-width: 520px;
				    margin: 0 auto;
					
					padding-left : 5px;
					padding-right : 5px;
				  } 	
			   	}
			  @media (min-width: 767px) {
				  .main {
				    max-width: 520px;
				    margin: 0 auto;
					padding-top : 100px;
				  } 	
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
			    width:11%;
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
			  
			  .desktop-my-menu {
			    display:auto !important;
			  }
			}
			@media (max-width: 768px) {
			   .mobile-my-menu  {
			    display:auto !important;
			    font-size:18px !important;
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
			  	font-weight:100;
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
			    font-weight:100 !important;
			    color:black !important;
			}
			.my-form-control-left-text {
				margin-top:3px !important;
				padding-left:2px !important;
				padding-right:2px !important;
			    
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
			
			.dropdown.dropdown-lg .dropdown-menu {
			    margin-top: -1px;
			    padding: 6px 20px;
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
			/** Kelvin **/
			@media (min-width: 1100px) {
			  .container {
			    width: 1070px;
			  }
			}
			/*************/
			/* auto panel hide */
			/*.panel-heading a:after {
			    font-family:'Glyphicons Halflings';
			    content:'\e114';
			    float: right;
			    color: grey;
			}
			.panel-heading a.collapsed:after {
			    content:"\e080";
			}*/
			/****************************************************************/
        </style>
        
    	<!--link href="css/main.css" rel="stylesheet"-->
		<!--link href="css/css.css" rel="stylesheet" type="text/css"/-->
	</head>
	<body>
		<div class="container">
		    <div class="row">
		       <div class="main">		       		
		          	<div class="col-xs-12 my-login-box" >		          		
		          		<form class="form col-xs-12 center-block" name="login" method="post" style="padding: 15px">
		          		    <a href="#"><img class="img-responsive"  src="images/logo_btn4.gif"></a>								
		          			<h3 style="color:#65aeee;">Log In</a></h3>							
				          	<div class="row">
				             	<div class="col-xs-12" style="z-index: 9;">
		    						<div class="form-group">
					                  <label for="name">Username</label>
					                  <input type="text" class="form-control" id="name"  name="name"   placeholder="Enter User ID">
					              	</div>
					        	</div>
					        </div>
					        <div class="row">
				             	<div class="col-xs-12" style="z-index: 9;">
		    						<div class="form-group">
					                  <label for="pw">Password</label>			                  
					                  <input type="password" class="form-control" id="pw" name="pw"  placeholder="Enter Password">			                  
					        	    </div>
					        	</div>
					        </div>
					        <div class="row">
				             	<div class="col-xs-12" style="z-index: 9;">
		    						<div class="form-group">
		    							<center>
		    								<input type="submit" class="btn btn-primary btn-lg ladda-button" data-style="zoom-in"  name="Submit" value="Log in">			                  
		    							</center>
					        	    </div>
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
			          	</form>
			        </div>	
		    	</div>
			</div>
		</div>
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	    
	</body>
</html>
