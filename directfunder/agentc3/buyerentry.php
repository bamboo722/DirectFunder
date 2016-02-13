<?php

/****-------------------------------------------------------------------**************************	

		Purpose 	: 	Where Buyer information will be entered to the system

		Project 	:	Sales Lead DB	

	 	Developer 	: 	Kelvin Smith

	 	Create Date : 	27/04/2012     

****-------------------------------------------------------------------************************/

session_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name

{

	header("Location: index.php");//login in AdminLogin.php

}



require_once("includes/dbconnect.php");



/********************dynamic staff id creation start******************************/

				$sql_sel = "select max(customer_id) mxid from customer_info";

		    	$res_sel = mysql_query($sql_sel) or die(mysql_error()."11");

				$rec_sel = mysql_fetch_assoc($res_sel);

				if($rec_sel['mxid'] =='')

				{

					$recid = 'BY00000001';

				}

				

				else

				{

					

					$schlid = "select concat(substring(customer_id,1,2),lpad(max(convert(substring(customer_id,3),signed))+1,8,'0')) recid from customer_info";

					$schres = mysql_query($schlid) or die(mysql_error()."go select error");

					$schrec = mysql_fetch_assoc($schres);

					

					$recid = $schrec['recid'];

				}

/********************dynamic staff id creation end******************************/



if($_POST['Submit']=="Save")

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

  						//duplicate record checking

						if($_POST['p_ph1']=="")

						{

							$vph1='EMPTY';

						}

						else

						{

							$vph1=$_POST['p_ph1'];

						}

						if($_POST['p_ph2']=="")

						{

							$vph2='EMPTY';

						}

						else

						{

							$vph2=$_POST['p_ph2'];

						}

						if($_POST['p_eml1']=="")

						{

							$vemail1='EMPTY';

						}

						else

						{

							$vemail1=$_POST['p_eml1'];

						}

						if($_POST['email_2']=="")

						{

							$vemail2='EMPTY';

						}

						else

						{

							$vemail2=$_POST['email_2'];

						}

  						$sqldup = "select count(*) cnt from customer_info 

						where p_ph1 in ('".$vph1."','".$vph2."') 

						or p_ph2 in ('".$vph1."','".$vph2."')

						or p_eml1 in ('".$vemail1."','".$vemail2."')

						or email_2 in ('".$vemail1."','".$vemail2."')";

						//print($sqldup);

						$dupres = mysql_query($sqldup) or die(mysql_error()."go select error");

						$duprec = mysql_fetch_assoc($dupres);

						//duplicate record checking end

   						if($duprec['cnt']==0)

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

							 

								$sql_ins = "insert into customer_info

									(

									  customer_id,

									  apply_dt,

									  lead_src,

									  priority_opt,

									  f_nm,

									  l_nm,

									  b_leg_nm,

									  p_eml1,

									  email_2,

									  p_ph1,

									  p_ph2,

									  p_hm_addr,

									  p_city,

									  p_state,

									  p_zip,

									  machine_no,

									  total_amt,

									  invoice_dt,

									  invoice_number,

									  financing_opt,

									  financing_stat,

									  pay1_dt,

									  pay1_amt,

									  pay1_stat,

									  pay2_dt,

									  pay2_amt,

									  pay2_stat,

									  pay3_dt,

									  pay3_amt,

									  pay3_stat,

									  etd_dt,

									  freight_com_info,

									  shipping_method,

									  eta_dt,

									  funding_dt,

									  next_payment,

									  data_insert_by,

									  apply_dt,

									  agent

									) 

									values

									 (

									 '".$recid."',

									 '".clean($_POST['apply_dt'])."',

									 '".clean($_POST['lead_src'])."',

									 '".clean($_POST['priority_opt'])."',

									 '".clean($_POST['f_nm'])."',

									 '".clean($_POST['l_nm'])."',

									 '".clean($_POST['b_leg_nm'])."',

									 '".clean($_POST['p_eml1'])."',

									 '".clean($_POST['email_2'])."',

									 '".clean($_POST['p_ph1'])."',

									 '".clean($_POST['p_ph2'])."',

									 '".clean($_POST['p_hm_addr'])."',

									 '".clean($_POST['p_city'])."',

									 '".clean($_POST['p_state'])."',

									 '".clean($_POST['p_zip'])."',

									 '".clean($_POST['machine_no'])."',

									 '".clean($_POST['total_amt'])."',

									 '".clean($_POST['invoice_dt'])."',

									 '".clean($_POST['invoice_number'])."',

									 '".clean($_POST['financing_opt'])."',

									 '".clean($_POST['financing_stat'])."',

									 '".clean($_POST['pay1_dt'])."',

									 '".clean($_POST['pay1_amt'])."',

									 '".clean($_POST['pay1_stat'])."',

									 '".clean($_POST['pay2_dt'])."',

									 '".clean($_POST['pay2_amt'])."',

									 '".clean($_POST['pay2_stat'])."',

									 '".clean($_POST['pay3_dt'])."',

									 '".clean($_POST['pay3_amt'])."',

									 '".clean($_POST['pay3_stat'])."',

									 '".clean($_POST['etd_dt'])."',

									 '".clean($_POST['freight_com_info'])."',

									 '".clean($_POST['shipping_method'])."',

									 '".clean($_POST['eta_dt'])."',

									 '".clean($_POST['funding_dt'])."',

									 '".$amt."',

									 '".$_SESSION['user_login']."',

									 curdate(),

									 '".$_POST['user_id']."'

									 )";

									mysql_query($sql_ins) or die(mysql_error());

									//inserting staff into staff_mast table end		 

									header("Location: buyerinfo.php?rid=".$recid);

									exit();

						}

	}		

}			



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<title>Sales Lead DB</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/css.css" rel="stylesheet" type="text/css"/>

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

<tr>   <td height="50px" align="center" class="title_blue">Buyer Information 

            Entry </td></tr>

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

						<option value="New" <?php if($_POST['priority_opt']=="New"){echo 'selected';}?>>New</option>

						<option value="Retry" <?php if($_POST['priority_opt']=="Retry"){echo 'selected';}?>>Retry</option>

						<option value="Cold" <?php if($_POST['priority_opt']=="Cold"){echo 'selected';}?>>Cold</option>

						<option value="Warm" <?php if($_POST['priority_opt']=="Warm"){echo 'selected';}?>>Warm</option>

                        <option value="Hot" <?php if($_POST['priority_opt']=="Hot"){echo 'selected';}?>>Hot</option>

                        <option value="Ready" <?php if($_POST['priority_opt']=="Ready"){echo 'selected';}?> class="alert_msg">Ready</option>  

					    <option value="Customer" <?php if($_POST['priority_opt']=="Customer"){echo 'selected';}?>>Customer</option>

						<option value="Partners" <?php if($_POST['priority_opt']=="Partners"){echo 'selected';}?>>Partners</option>

						<option value="Bought Others" <?php if($_POST['priority_opt']=="Bought Others"){echo 'selected';}?>>Bought Others</option>

						<option value="Inactive" <?php if($_POST['priority_opt']=="Inactive"){echo 'selected';}?>>Inactive</option>

                        <option value="Delete" <?php if($_POST['priority_opt']=="Delete"){echo 'selected';}?>>Delete</option>

                          </select></td>

                        <td width="23%" height="20px" class="main_txt">Sale Rep. 

                          Name :</td>

                        <td width="27%"><select name="user_id" id="user_id" class="textbox_small">

                                        <option value=""></option>

                                        <?php 

							$sql_pro="select * from admin_user where user_group='Agent' order by user_id";

							$pro_res=mysql_query($sql_pro) or die(mysql_error());

									while($pro_rec=mysql_fetch_array($pro_res))

									{

									

									?>

                                        <option value="<?php  echo $pro_rec['user_id'];?>" <?php if($pro_rec['user_id']==$_POST['user_id']){ echo "selected";}?>> 

                                        <?php  echo $pro_rec['user_id'];?>

                                        </option>

                                        <?php  

							}

							?>

                                      </select></td>

                      </tr>

                      <tr> 

                        <td width="23%" height="20px" class="main_txt">Date :</td>

                        <td width="27%"><input type="text" name="apply_dt" id="apply_dt" class="textbox_small" onFocus='popUpCalendar(this,document.default_emplate.apply_dt,"mm-dd-yyyy")' value="<?php if(isset($_POST['apply_dt'])){echo $_POST['apply_dt'];}?>">

                          <strong><font color="#FF0000">*</font></strong> </td>

                        <td width="23%" height="20px" class="main_txt">Phone 1 

                          :</td>

                        <td width="27%"> <input type="text" name="p_ph1" id="p_ph1" class="textbox_small" value="<?php if(isset($_POST['p_ph1'])){echo $_POST['p_ph1'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td height="20px" class="main_txt">Lead Source :</td>

                        <td> <input type="text" name="lead_src" id="lead_src" class="textbox_small" value="<?php if(isset($_POST['lead_src'])){echo $_POST['lead_src'];}?>"> 

                        </td>

                        <td width="23%" height="20px" class="main_txt">Phone 2 

                          :</td>

                        <td width="27%"> <input type="text" name="p_ph2" id="p_ph2" class="textbox_small" value="<?php if(isset($_POST['p_ph2'])){echo $_POST['p_ph2'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td height="20px" class="main_txt">First Name :</td>

                        <td> <input type="text" name="f_nm" id="f_nm" class="textbox_small" value="<?php if(isset($_POST['f_nm'])){echo $_POST['f_nm'];}?>"> <strong><font color="#FF0000">*</font></strong> 

                        </td>

                        <td width="23%" height="20px" class="main_txt">Address 

                          :</td>

                        <td width="27%"> <input type="text" name="p_hm_addr" id="p_hm_addr" class="textbox_small" value="<?php if(isset($_POST['p_hm_addr'])){echo $_POST['p_hm_addr'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Last Name :</td>

                        <td><input type="text" name="l_nm" id="l_nm" class="textbox_small" value="<?php if(isset($_POST['l_nm'])){echo $_POST['l_nm'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">City :</td>

                        <td width="27%"> <input type="text" name="p_city" id="p_city" class="textbox_small" value="<?php if(isset($_POST['p_city'])){echo $_POST['p_city'];}?>"> 

                        </td>

                      </tr>

					  <tr> 

                        <td class="main_txt">Company Name :</td>

                        <td><input type="text" name="b_leg_nm" id="b_leg_nm" class="textbox_small" value="<?php if(isset($_POST['b_leg_nm'])){echo $_POST['b_leg_nm'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">State :</td>

                        <td width="27%"><input type="text" name="p_state" id="p_state" class="textbox_small" value="<?php if(isset($_POST['p_state'])){echo $_POST['p_state'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Email Address 1 :</td>

                        <td><input type="text" name="p_eml1" id="p_eml1" class="textbox_small" value="<?php if(isset($_POST['p_eml1'])){echo $_POST['p_eml1'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">Zip :</td>

                        <td width="27%"><input type="text" name="p_zip" id="p_zip2" class="textbox_small" value="<?php if(isset($_POST['p_zip'])){echo $_POST['p_zip'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Email Address 2 :</td>

                        <td><input type="text" name="email_2" id="email_2" class="textbox_small" value="<?php if(isset($_POST['email_2'])){echo $_POST['email_2'];}?>"></td>

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

                        <td><input type="text" name="machine_no" id="machine_no" class="textbox_small" value="<?php if(isset($_POST['machine_no'])){echo $_POST['machine_no'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">Invoice 

                          Date :</td>

                        <td width="27%"> <input type="text" name="invoice_dt" id="invoice_dt" class="textbox_small" onFocus='popUpCalendar(this,document.default_emplate.invoice_dt,"mm-dd-yyyy")' value="<?php if(isset($_POST['invoice_dt'])){echo $_POST['invoice_dt'];}?>"> 

                        </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Total Amount :</td>

                        <td><input type="text" name="total_amt" id="total_amt" class="textbox_small" value="<?php if(isset($_POST['total_amt'])){echo $_POST['total_amt'];}?>"></td>

                        <td width="23%" height="20px" class="main_txt">Invoice 

                          Number : </td>

                        <td width="27%"><input type="text" name="invoice_number" id="invoice_number" class="textbox_small" value="<?php if(isset($_POST['invoice_number'])){echo $_POST['invoice_number'];}?>"> 

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

                            <option value="Cash" <?php if($_POST['financing_opt']=="Cash"){echo 'selected';}?>>Cash</option>

                            <option value="Lease" <?php if($_POST['financing_opt']=="Lease"){echo 'selected';}?>>Lease</option>

							<option value="Lease2" <?php if($_POST['financing_opt']=="Lease2"){echo 'selected';}?>>Lease2</option>

							<option value="Lease3" <?php if($_POST['financing_opt']=="Lease3"){echo 'selected';}?>>Lease3</option>

							<option value="Lease4" <?php if($_POST['financing_opt']=="Lease4"){echo 'selected';}?>>Lease4</option>

                          </select></td>

                        <td width="23%" height="20px" class="main_txt">Financing 

                          Status :</td>

                        <td width="27%"> 

						<select name="financing_stat" size="1" class="textbox_small">

                            <option value="Applied" <?php if($_POST['financing_stat']=="Applied"){echo 'selected';}?>>Applied</option>

							<option value="Not Applied" <?php if($_POST['financing_stat']=="Not Applied"){echo 'selected';}?>>Not Applied</option>

                            <option value="Approved" <?php if($_POST['financing_stat']=="Approved"){echo 'selected';}?>>Approved</option>

                            <option value="Denied" <?php if($_POST['financing_stat']=="Denied"){echo 'selected';}?>>Denied</option>

                            <option value="Pending" <?php if($_POST['financing_stat']=="Pending"){echo 'selected';}?>>Pending 

                            Review</option>

                            <option value="Missing" <?php if($_POST['financing_stat']=="Missing"){echo 'selected';}?>>Missing 

                            Documents</option>

							<option value="Express Agreement" <?php if($_POST['financing_stat']=="Express Agreement"){echo 'selected';}?>>Express Agreement</option>

							<option value="Funded" <?php if($_POST['financing_stat']=="Funded"){echo 'selected';}?>>Funded</option>

							<option value="Final Docs Out" <?php if($_POST['financing_stat']=="Final Docs Out"){echo 'selected';}?>>Final Docs Out</option>

							<option value="Final Docs In" <?php if($_POST['financing_stat']=="Final Docs In"){echo 'selected';}?>>Final Docs In</option>

							<option value="Site Inspection" <?php if($_POST['financing_stat']=="Site Inspection"){echo 'selected';}?>>Site Inspection</option>

                          </select> </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Payment 1 Date :</td>

                        <td><input type="text" name="pay1_dt" id="pay1_dt" class="textbox_small" value="<?php if(isset($_POST['pay1_dt'])){echo $_POST['pay1_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay1_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Payment 

                          1 Amount :</td>

                        <td width="27%"> <input type="text" name="pay1_amt" id="pay1_amt" class="textbox_small" value="<?php if(isset($_POST['pay1_amt'])){echo $_POST['pay1_amt'];}?>"> 

                          <input type="checkbox" name="pay1_stat" value="T">

                          Paid </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Payment 2 Date :</td>

                        <td><input type="text" name="pay2_dt" id="pay2_dt" class="textbox_small" value="<?php if(isset($_POST['pay2_dt'])){echo $_POST['pay2_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay2_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Payment 

                          2 Amount :</td>

                        <td width="27%"> <input type="text" name="pay2_amt" id="pay2_amt" class="textbox_small" value="<?php if(isset($_POST['pay2_amt'])){echo $_POST['pay2_amt'];}?>"> 

                          <input type="checkbox" name="pay2_stat" value="T">

                          Paid </td>

                      </tr>

                      <tr> 

                        <td class="main_txt">Payment 3 Date :</td>

                        <td><input type="text" name="pay3_dt" id="pay3_dt" class="textbox_small" value="<?php if(isset($_POST['pay3_dt'])){echo $_POST['pay3_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.pay3_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Payment 

                          3 Amount :</td>

                        <td width="27%"> <input type="text" name="pay3_amt" id="pay3_amt" class="textbox_small" value="<?php if(isset($_POST['pay3_amt'])){echo $_POST['pay3_amt'];}?>"> 

                          <input type="checkbox" name="pay3_stat" value="T">

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

                        <td><input type="text" name="etd_dt" id="etd_dt" class="textbox_small" value="<?php if(isset($_POST['etd_dt'])){echo $_POST['etd_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.etd_dt,"mm-dd-yyyy")'></td>

                        <td width="23%" height="20px" class="main_txt">Shipping 

                          Method (air / sea) :</td>

                        <td width="27%"><select name="shipping_method" size="1" class="textbox_small">

							<option value="Sea" <?php if($_POST['shipping_method']=="Sea"){echo 'selected';}?>>Sea</option>

                            <option value="Air" <?php if($_POST['shipping_method']=="Air"){echo 'selected';}?>>Air</option>

                          </select> </td>

                      </tr>

                      <tr> 

                        <td class="main_txt" valign="top">ETA :</td>

                        <td valign="top"><input type="text" name="eta_dt" id="eta_dt" class="textbox_small" value="<?php if(isset($_POST['eta_dt'])){echo $_POST['eta_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.eta_dt,"mm-dd-yyyy")'> 

                        </td>

                        <td width="23%" height="20px" class="main_txt" valign="top">Freight 

                          Company Information :</td>

                        <td width="27%"><textarea name="freight_com_info"  rows="4"><?php if(isset($_POST['freight_com_info'])){echo $_POST['freight_com_info'];}?></textarea> 

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

                        <td><input type="text" name="funding_dt" id="funding_dt2" class="textbox_small" value="<?php if(isset($_POST['funding_dt'])){echo $_POST['funding_dt'];}?>" onFocus='popUpCalendar(this,document.default_emplate.funding_dt,"yyyy-mm-dd")'></td>

                        <td width="23%" height="20px" class="main_txt">&nbsp;</td>

                        <td width="27%">&nbsp; </td>

                      </tr>

                      <tr> 

                        <td colspans="2"><?php echo $msg;?> </td>

                        <td width="23%" height="20px" class="main_txt"><input type="submit" name="Submit" value="Save" class="button_medium"></td>

                        <td width="27%"> <strong><font color="#FF0000">*</font></strong> Mandatory Field.&nbsp; </td>

                      </tr>

                      <!-- text input area end-->

                    </table>

			</td></tr>

		  	   <tr><td>

			   

			   </td></tr>

            </table>

			</form> </td>

			</tr>

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

