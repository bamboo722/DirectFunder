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

  header("Location: buyerinfo.php?rid=".$_GET['rid']."&st=O");

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

				

/********************buyer information select end******************************/

if($_GET['ed']=="T")

{ 

				$log = "select * from conversation_log_info where auto_id='".$_GET['aid']."'";

		    	$resl = mysql_query($log) or die(mysql_error()."11");

				$recl = mysql_fetch_assoc($resl);

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

					  etd_dt='".$_POST['etd_dt']."',

					  freight_com_info='".$_POST['freight_com_info']."',

					  shipping_method='".$_POST['shipping_method']."',

					  eta_dt='".$_POST['eta_dt']."',

					  funding_dt='".$_POST['funding_dt']."',

					  next_payment='".$amt."',

					  cust_upd_by='".$_SESSION['user_login']."',

					  cust_upd_dt= curdate(),

					  agent='".$_POST['user_id']."'

					  where customer_id='".$_GET['rid']."'";

					mysql_query($sql_upd) or die(mysql_error());

					//inserting staff into staff_mast table end		 

					header("Location: buyerinfo.php?rid=".$_GET['rid']);

					exit();

			}

	}		

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

			header("Location: buyerinfo.php?rid=".$_GET['rid']);

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

			header("Location: buyerinfo.php?rid=".$_GET['rid']."&st=O");

			exit();

}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<title>Sales Lead DB</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/css.css" rel="stylesheet" type="text/css"/>

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

</script>





</head>

<body>

<script language="JavaScript" src="popcalendar.js"></script>

<table width="100%">

<tr><td align="center">

<table width="90%">

<!--heading space-->

        <tr> 

          <td height="100px"> 

            <?php include("header.php");?>

          </td>

        </tr>

        <!--heading space end-->

<!--header menu space-->

<tr>

          <td height="50px" class="up_menu"><div align="center" class="hyperlink"> <?php 

include("admheadmenu.php");

?></div></td>

        </tr>

<!--header menu space end-->

<tr><td height="50px"></td></tr>

<tr><td height="473" valign="top" align="center">

		  <form name="default_emplate" id="default_emplate" method="post"  enctype="multipart/form-data">

		  <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_disp_out">

		  	<tr><td align="center" valign="middle">

			<table class="table_disp_in" width="99%">

                      <!-- main text input table-->

                      <tr> 

                        <td height="29" colspans="4" class="alert_msg"><?php echo $msg;?></td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Priority : </td>

                        <td> <select name="priority_opt" size="1" class="textbox_small">

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
                            

                          </select></td>

                        <td width="23%" height="20px" class="main_txt"><input type="submit" name="Submit" value="Change" class="button_medium"></td>

                        <td width="27%">&nbsp; </td>

                      </tr>

					  <tr> 

                        <td class="main_txt">Sale Rep. Name : </td>

                        <td> <select name="user_id" id="user_id" class="textbox_small">

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

                          </select></td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td width="23%" height="20px" class="main_txt">Date :</td>

                        <td width="27%"><input type="text" name="apply_dt" id="apply_dt" class="textbox_small" onFocus='popUpCalendar(this,document.default_emplate.apply_dt,"mm-dd-yyyy")' value="<?php if(isset($_POST['apply_dt'])){echo $_POST['apply_dt'];}else if(isset($recb['apply_dt'])){echo $recb['apply_dt'];}?>"> 

                        </td>

                        <td width="23%" height="20px" class="main_txt">Phone 1 

                          :</td>

                        <td width="27%"> <input type="text" name="p_ph1" id="p_ph1" class="textbox_small" value="<?php if(isset($_POST['p_ph1'])){echo $_POST['p_ph1'];}else if(isset($recb['p_ph1'])){echo $recb['p_ph1'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td height="20px" class="main_txt">Lead Source :</td>

                        <td> <input type="text" name="lead_src" id="lead_src" class="textbox_small" value="<?php if(isset($_POST['lead_src'])){echo $_POST['lead_src'];}else if(isset($recb['lead_src'])){echo $recb['lead_src'];}?>"> 

                        </td>

                        <td width="23%" height="20px" class="main_txt">Phone 2 

                          :</td>

                        <td width="27%"> <input type="text" name="p_ph2" id="p_ph2" class="textbox_small" value="<?php if(isset($_POST['p_ph2'])){echo $_POST['p_ph2'];}else if(isset($recb['p_ph2'])){echo $recb['p_ph2'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td height="20px" class="main_txt">First Name :</td>

                        <td> <input type="text" name="f_nm" id="f_nm" class="textbox_small" value="<?php if(isset($_POST['f_nm'])){echo $_POST['f_nm'];}else if(isset($recb['f_nm'])){echo $recb['f_nm'];}?>"> 

                        </td>

                        <td width="23%" height="20px" class="main_txt">Address 

                          :</td>

                        <td width="27%"> <input type="text" name="p_hm_addr" id="p_hm_addr" class="textbox_small" value="<?php if(isset($_POST['p_hm_addr'])){echo $_POST['p_hm_addr'];}else if(isset($recb['p_hm_addr'])){echo $recb['p_hm_addr'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Last Name :</td>

                        <td><input type="text" name="l_nm" id="l_nm" class="textbox_small" value="<?php if(isset($_POST['l_nm'])){echo $_POST['l_nm'];}else if(isset($recb['l_nm'])){echo $recb['l_nm'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">City :</td>

                        <td width="27%"> <input type="text" name="p_city" id="p_city" class="textbox_small" value="<?php if(isset($_POST['p_city'])){echo $_POST['p_city'];}else if(isset($recb['p_city'])){echo $recb['p_city'];}?>"> 

                        </td>

                      </tr>

					  <tr> 

                        <td class="main_txt">Company Name :</td>

                        <td><input type="text" name="b_leg_nm" id="b_leg_nm" class="textbox_small" value="<?php if(isset($_POST['b_leg_nm'])){echo $_POST['b_leg_nm'];}else if(isset($recb['b_leg_nm'])){echo $recb['b_leg_nm'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">State :</td>

                        <td width="27%"><input type="text" name="p_state" id="p_state" class="textbox_small" value="<?php if(isset($_POST['p_state'])){echo $_POST['p_state'];}else if(isset($recb['p_state'])){echo $recb['p_state'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Email Address 1 :</td>

                        <td><input type="text" name="p_eml1" id="p_eml1" class="textbox_small" value="<?php if(isset($_POST['p_eml1'])){echo $_POST['p_eml1'];}else if(isset($recb['p_eml1'])){echo $recb['p_eml1'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">Zip :</td>

                        <td width="27%"><input type="text" name="p_zip" id="p_zip2" class="textbox_small" value="<?php if(isset($_POST['p_zip'])){echo $_POST['p_zip'];}else if(isset($recb['p_zip'])){echo $recb['p_zip'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Email Address 2 :</td>

                        <td><input type="text" name="email_2" id="email_2" class="textbox_small" value="<?php if(isset($_POST['email_2'])){echo $_POST['email_2'];}else if(isset($recb['email_2'])){echo $recb['email_2'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">&nbsp;</td>

                        <td>&nbsp;</td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td class="main_txt"># of Machines :</td>

                        <td><input type="text" name="machine_no" id="machine_no" class="textbox_small" value="<?php if(isset($_POST['machine_no'])){echo $_POST['machine_no'];}else if(isset($recb['machine_no'])){echo $recb['machine_no'];}?>">

                          <input type="submit" name="Quote" value="Quote" class="button_small"></td>

                        <td width="23%" height="20px" class="main_txt">Invoice 

                          Date :</td>

                        <td width="27%"> <input type="text" name="invoice_dt" id="invoice_dt" class="textbox_small" onFocus='popUpCalendar(this,document.default_emplate.invoice_dt,"mm-dd-yyyy")' value="<?php if(isset($_POST['invoice_dt'])){echo $_POST['invoice_dt'];}else if(isset($recb['invoice_dt'])){echo $recb['invoice_dt'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Total Amount :</td>

                        <td><input type="text" name="total_amt" id="total_amt" class="textbox_small" value="<?php if(isset($_POST['total_amt'])){echo $_POST['total_amt'];}else if(isset($recb['total_amt'])){echo $recb['total_amt'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">Invoice 

                          Number : </td>

                        <td width="27%"><input type="text" name="invoice_number" id="invoice_number" class="textbox_small" value="<?php if(isset($_POST['invoice_number'])){echo $_POST['invoice_number'];}else if(isset($recb['invoice_number'])){echo $recb['invoice_number'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">&nbsp;</td>

                        <td>&nbsp;</td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Financing (Cash - Lease) :</td>

                        <td> <select name="financing_opt" size="1" class="textbox_small">

                            <option value="Cash" <?php if($_POST['financing_opt']=="Cash"){echo 'selected';}else if($recb['financing_opt']=="Cash"){echo 'selected';}?>>Cash</option>

                            <option value="Lease" <?php if($_POST['financing_opt']=="Lease"){echo 'selected';}else if($recb['financing_opt']=="Lease"){echo 'selected';}?>>Lease</option>

							<option value="Lease2" <?php if($_POST['financing_opt']=="Lease2"){echo 'selected';}else if($recb['financing_opt']=="Lease2"){echo 'selected';}?>>Lease2</option>

							<option value="Lease3" <?php if($_POST['financing_opt']=="Lease3"){echo 'selected';}else if($recb['financing_opt']=="Lease3"){echo 'selected';}?>>Lease3</option>

							<option value="Lease4" <?php if($_POST['financing_opt']=="Lease4"){echo 'selected';}else if($recb['financing_opt']=="Lease4"){echo 'selected';}?>>Lease4</option>

                          </select></td>

                        <td width="23%" height="20px" class="main_txt">Financing 

                          Status :</td>

                        <td width="27%"> <select name="financing_stat" size="1" class="textbox_small">

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

						  </select> </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Payment 1 Date :</td>

                        <td><input type="text" name="pay1_dt" id="pay1_dt" class="textbox_small" value="<?php if(isset($_POST['pay1_dt'])){echo $_POST['pay1_dt'];}else if(isset($recb['pay1_dt'])){echo $recb['pay1_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay1_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Payment 

                          1 Amount :</td>

                        <td width="27%"> <input type="text" name="pay1_amt" id="pay1_amt" class="textbox_small" value="<?php if(isset($_POST['pay1_amt'])){echo $_POST['pay1_amt'];}else if(isset($recb['pay1_amt'])){echo $recb['pay1_amt'];}?>">

                          <input type="checkbox" name="pay1_stat" value="T" <?php if($_POST['pay1_stat']=="T"){echo 'checked';}else if($recb['pay1_stat']=="T"){echo 'checked';}?>>

                          Paid </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Payment 2 Date :</td>

                        <td><input type="text" name="pay2_dt" id="pay2_dt" class="textbox_small" value="<?php if(isset($_POST['pay2_dt'])){echo $_POST['pay2_dt'];}else if(isset($recb['pay2_dt'])){echo $recb['pay2_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay2_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Payment 

                          2 Amount :</td>

                        <td width="27%"> <input type="text" name="pay2_amt" id="pay2_amt" class="textbox_small" value="<?php if(isset($_POST['pay2_amt'])){echo $_POST['pay2_amt'];}else if(isset($recb['pay2_amt'])){echo $recb['pay2_amt'];}?>">

                          <input type="checkbox" name="pay2_stat" value="T" <?php if($_POST['pay2_stat']=="T"){echo 'checked';}else if($recb['pay2_stat']=="T"){echo 'checked';}?>>

                          Paid </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Payment 3 Date :</td>

                        <td><input type="text" name="pay3_dt" id="pay3_dt" class="textbox_small" value="<?php if(isset($_POST['pay3_dt'])){echo $_POST['pay3_dt'];}else if(isset($recb['pay3_dt'])){echo $recb['pay3_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay3_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Payment 

                          3 Amount :</td>

                        <td width="27%"> <input type="text" name="pay3_amt" id="pay3_amt" class="textbox_small" value="<?php if(isset($_POST['pay3_amt'])){echo $_POST['pay3_amt'];}else if(isset($recb['pay3_amt'])){echo $recb['pay3_amt'];}?>">

                          <input type="checkbox" name="pay3_stat" value="T" <?php if($_POST['pay3_stat']=="T"){echo 'checked';}else if($recb['pay3_stat']=="T"){echo 'checked';}?>>

                          Paid </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">&nbsp;</td>

                        <td>&nbsp;</td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">ETD :</td>

                        <td><input type="text" name="etd_dt" id="etd_dt" class="textbox_small" value="<?php if(isset($_POST['etd_dt'])){echo $_POST['etd_dt'];}else if(isset($recb['etd_dt'])){echo $recb['etd_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.etd_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Shipping 

                          Method (air / sea) :</td>

                        <td width="27%"><select name="shipping_method" size="1" class="textbox_small">

							<option value="Sea" <?php if($_POST['shipping_method']=="Sea"){echo 'selected';}else if($recb['shipping_method']=="Sea"){echo 'selected';}?>>Sea</option>

                            <option value="Air" <?php if($_POST['shipping_method']=="Air"){echo 'selected';}else if($recb['shipping_method']=="Air"){echo 'selected';}?>>Air</option>

                            

                          </select> </td>

                      </tr>

                      <tr> 

                        <td class="main_txt" valign="top">ETA :</td>

                        <td valign="top"><input type="text" name="eta_dt" id="eta_dt" class="textbox_small" value="<?php if(isset($_POST['eta_dt'])){echo $_POST['eta_dt'];}else if(isset($recb['eta_dt'])){echo $recb['eta_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.eta_dt,"mm-dd-yyyy")'> 

                        </td>

                        <td width="23%" height="20px" class="main_txt" valign="top">Freight 

                          Company Information :</td>

                        <td width="27%">

                          <textarea name="freight_com_info"  rows="4"><?php if(isset($_POST['freight_com_info'])){echo $_POST['freight_com_info'];}else if(isset($recb['freight_com_info'])){echo $recb['freight_com_info'];}?></textarea> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">&nbsp;</td>

                        <td>&nbsp;</td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Delivery Date :</td>

                        <td><input type="text" name="funding_dt" id="funding_dt2" class="textbox_small" value="<?php if(isset($_POST['funding_dt'])){echo $_POST['funding_dt'];}else if(isset($recb['funding_dt'])){echo $recb['funding_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.funding_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td colspans="2"><?php echo $msg;?> <div align="right">

                            <input type="submit" name="Submit" value="Change" class="button_medium">

                          </div></td>

                        <td width="23%" height="20px" class="main_txt">

						<a href="addnote.php?uid=<?php echo $recb['customer_id'];?>" onClick="wopen('addnote.php?uid=<?php echo $recb['customer_id'];?>', 'popup', 500, 500); return false;" target="popup">

						<input type="button" name="Upload" value="Upload File" class="button_big">

						</a>

						</td>

                        <td width="27%"><a href="sendmailsinglecust.php?bid=<?php echo $recb['customer_id'];?>" onClick="wopen2('sendmailsinglecust.php?bid=<?php echo $recb['customer_id'];?>', 'popup', 700, 550); return false;" target="popup">

                          <input type="button" name="Mail" value="Send Mail" class="button_big">

                          </a> </td>

                      </tr>

                      <!-- text input area end-->

                    </table>

			</td></tr>

			<!--tab window -->

		  	   <tr><td>

			   		<table width="100%">

						<tr><td>

						<table width="100%" bgcolor="#e5e5e5">

						<tr bgcolor="#e5e5e5">

						<td><a href="buyerinfo.php?rid=<?php echo $_GET['rid'];?>&st=O" class="title_green">Conversation Log</a></td>

						<td><div align="right">&nbsp;&nbsp;</div></td>

						</tr>

						</table>

						</td></tr>

						<?php

						if($_GET['ed']=="T")

						{

						?>

						<tr><td>

						<!-- conversation log entry-->

						<table width="100%">

						<tr>

						<td width="36%" class="smalltxt_black">Subject </td>

						<td width="64%" class="smalltext_grey"><input type="text" name="log_subject" id="log_subject" class="textbox_small" value="<?php if(isset($_POST['log_subject'])){echo $_POST['log_subject'];}else if(isset($recl['log_subject'])){echo $recl['log_subject'];}?>"></td>

						</tr>

						<tr>

						      <td width="36%" class="smalltxt_black" valign="top">Outcome</td>

						<td width="64%" class="smalltext_grey">

                                <textarea name="out_come"><?php if(isset($_POST['out_come'])){echo $_POST['out_come'];}else if(isset($recl['out_come'])){echo $recl['out_come'];}?></textarea></td>

						</tr>

						<tr>

						<td class="smalltxt_black">Spoke To </td>

						<td class="smalltext_grey"><input type="text" name="spoke_to" id="spoke_to" class="textbox_small" value="<?php if(isset($_POST['spoke_to'])){echo $_POST['spoke_to'];}else if(isset($recl['spoke_to'])){echo $recl['spoke_to'];}?>"></td>

						</tr>

						<tr>

						<td class="smalltxt_black">Next Follow up Date  </td>

						<td class="smalltext_grey"><input readonly="true" type="text" name="next_follow_up" id="next_follow_up" class="textbox_small" value="<?php if(isset($_POST['next_follow_up'])){echo $_POST['next_follow_up'];}else if(isset($recl['next_follow_up'])){  $follow_up = explode("-",$recl['next_follow_up']); echo $follow_up[1].'-'.$follow_up[2].'-'.$follow_up[0];}?>" ></td>

						</tr>

						<tr>

						      <td class="smalltxt_black"><div align="right"><a href="sendmailsinglecust.php?bid=<?php echo $recb['customer_id'];?>" onClick="wopen('sendmailsinglecust.php?uid=<?php echo $recb['customer_id'];?>', 'popup', 500, 500); return false;" target="popup"> 

                                  </a></div></td>

						      <td class="smalltext_grey"><input type="submit" name="Edit" value="Edit Log" class="button_big">

                                <input type="submit" name="Cancel" value="Cancel" class="button_medium"></td>

						</tr>

						</table>

						<!-- conversation log entry end-->

						</td></tr>

						<?php

						}

						else

						{

						?>

						<tr><td>

						<!-- log display -->

								<table width="100%">

						<tr> 

                              

                      

             

                       

                              <td width="250" height="21" bgcolor="#CCCCCC" class="main_txt">Log 

                                Time </td>

                              <td width="250" height="21" bgcolor="#CCCCCC" class="main_txt">Subject</td>

			                  <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;&nbsp;&nbsp;Performed 

                                By </td>

								

                              <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;&nbsp;Next 

                                Follow up</td>

								

                              <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt">&nbsp;Outcome</td>

              					

                              <td width="214" height="21" bgcolor="#CCCCCC" class="main_txt"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Option</div></td>

					

                      </tr>

					  <tr><td colspans="6">

					  

					   <div style="overflow: auto;height: 200px; width: 100%;">

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

					 <td width="151"150px"" class="smalltext_grey" valign="top"><?php echo $seerec['log_time'];?></td>

						<td width="151" class="smalltext_grey" valign="top"><?php echo $seerec['log_subject'];?></td>

						<td width="151" class="smalltext_grey" valign="top"><?php echo $seerec['spoke_to'];?></td>

						<td width="110" class="smalltext_grey" valign="top"><?php echo $seerec['next_follow_up'];?></td>

						<td width="140" class="smalltext_grey" valign="top"><?php echo $seerec['out_come'];?></td>

						

                                      <td width="149"150px""  class="main_txt" valign="top"><div align="center"><a href="buyerinfo.php?rid=<?php echo $_GET['rid'];?>&aid=<?php echo $seerec['auto_id'];?>&ed=T" class="main_txt">Edit</a> 

                                          &nbsp;&nbsp;</div></td>        

                              

					   </tr>

					   

					  <?php 

							

							}

						}		

					  ?>

					 </table>

					 </div>

					  </td></tr>

						

						</table>

						<!-- log display end-->

						</td></tr>

						

						

						<tr><td>

						<!-- conversation log entry-->

						<table width="100%">

						<tr>

						<td width="36%" class="smalltxt_black">Subject </td>

						<td width="64%" class="smalltext_grey"><input type="text" name="log_subject" id="log_subject" class="textbox_small" value="<?php if(isset($_POST['log_subject'])){echo $_POST['log_subject'];}?>"></td>

						</tr>

						<tr>

						      <td width="36%" class="smalltxt_black" valign="top">Outcome</td>

						<td width="64%">

                                <textarea name="out_come"><?php if(isset($_POST['out_come'])){echo $_POST['out_come'];}?></textarea></td>

						</tr>

						<tr>

						      <td class="smalltxt_black">Performed By</td>

						<td class="smalltext_grey"><input type="text" name="spoke_to" id="spoke_to" class="textbox_small" value="<?php if(isset($_POST['spoke_to'])){echo $_POST['spoke_to'];}?>"></td>

						</tr>

						<tr>

						<td class="smalltxt_black">Next Follow up Date  </td>

						<td class="smalltext_grey"><input type="text" name="next_follow_up" id="next_follow_up" class="textbox_small" value="<?php if(isset($_POST['next_follow_up'])){echo $_POST['next_follow_up'];}?>" onFocus='popUpCalendar(this,document.default_emplate.next_follow_up,"mm-dd-yyyy")'></td>

						</tr>

						<tr>

						      <td class="smalltxt_black">&nbsp;</td>

						      <td class="smalltext_grey"><input type="submit" name="Add" value="Save Call Log" class="button_big">

                                <input type="submit" name="Done" value="Done" class="button_medium"></td>

						</tr>

						</table>

						<!-- conversation log entry end-->

						</td></tr>

						<?php

						}

						?>

					</table>

			   </td></tr>

			 <!--tab window end-->

            </table>

			</form> </td></tr>

<tr><td height="14"></td></tr>

<!-- footer-->

		<tr> 

          <td><?php include("footer.php");?></td>

        </tr>

		<!-- footer end-->

</table>



</td></tr>

</table>

</body>

</html>

