<?php
session_start();
ob_start();
if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
	header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");										
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">


<link href="css/css.css" rel="stylesheet" type="text/css"/>
</head>
<body>
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
<tr> 
          <td height="50px" align="center" class="title_blue">&nbsp;</td>
        </tr>
<tr><td valign="top" align="center">
		  <form method="post" enctype="multipart/form-data" name="RTEDemo">
		  <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_disp_out">
		  	<tr><td align="center">
			<table width="99%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td height="43"> <div align="center">&nbsp;&nbsp; Send 
                            Quotes </div></td>
                      </tr>
                      <tr> 
                        <td> </td>
                      </tr>
                      <tr> 
                        <td align="center" class="midbody_lebel"> <table width="66%">
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                1 Machine</td>
                              <td width="26%">
							  		<table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=1" class="link_txt2">Send Quote</a></td></tr></table>
							  </td>
                            </tr>
                            <tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                2 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=2" class="link_txt2">Send Quote</a></td></tr></table></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                3 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=3" class="link_txt2">Send Quote</a></td></tr></table></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                4 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=4" class="link_txt2">Send Quote</a></td></tr></table></a></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                5 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=5" class="link_txt2">Send Quote</a></td></tr></table></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                6 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=6" class="link_txt2">Send Quote</a></td></tr></table></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                7 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=7" class="link_txt2">Send Quote</a></td></tr></table></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%" class="sub_heading">Requested for 
                                8 Machines</td>
                              <td width="26%" class="midbody_lebel"><table class="button_big"><tr><td align="center"><a href="sendquote.php?mno=8" class="link_txt2">Send Quote</a></td></tr></table></td>
                            </tr>
							<tr> 
                              <td colspans="2" bgcolor="#999999" height="2px"></td>
                            </tr>
                            <tr> 
                              <td width="74%"> <div align="left"> </div></td>
                              <td width="26%" class="midbody_lebel"></td>
                            </tr>
                          </table></td>
                      </tr>
                      <tr> 
                        <td>&nbsp; </td>
                      </tr>
                      <tr> 
                        <td colspans="5" bgcolor="#666666" height="1px"></td>
                      </tr>
                      <tr> 
                        <td align="left"> </td>
                      </tr>
                    </table>
			</td></tr>
		  	   <?php
				if(isset($_GET['mno']))
				{	   
					    $quote="select a.f_nm fnm,a.l_nm lnm,a.p_eml1,a.machine_no,b.machine_no,b.file_nm flnm
						from customer_info a,quote_mast b
						where a.machine_no=b.machine_no
						and b.machine_no='".$_GET['mno']."'";
						//print($quote);
						$res_body=mysql_query($quote);					
						//********* start insert into table *******************
						while($qrec = mysql_fetch_assoc($res_body))
						{
									$mailsubject = 'Quote for Alpine yogurt & soft serve machines'; 
									$link='http://www.alpine360hp.com/prospects/quotes/'.$qrec['flnm'];
									$msg1 = 'To '.$qrec['fnm'].' '.$qrec['lnm'].', We are sending the quotation you requested for along with this mail. Hope to hear from you soon. Thanks.<br> <a href="'.$link.'"> Click to this link to have quotation<a>'; 							
									$v_receiver=$qrec['p_eml1'];
									$Fromname='Duc Nguyen';
									$Fromaddress='duc@cenpaco.com';										
									
									
				
												//if (mail($v_receiver, $mailsubject, $msg1,"<br> <img src='images/images.jpg' width='20px' height='20px'>".$link, "From:".$Fromname." <".$Fromaddress.">\nContent-Type: text/html; charset=iso-8859-1")) 
												if (mail($v_receiver, $mailsubject, $msg1, "From:".$Fromname." <".$Fromaddress.">\nContent-Type: text/html; charset=iso-8859-1")) 

													{ 
														//mail send message			
													}
													mysql_query("set time_zone='-7:00';");
									$sql_mail = "insert into mail_log
									(
									  mail_rcvr,
									  mail_subject,
									  mail_body,
									  send_dt,
									  from_nm,
									  from_address
									) 
									values
									 (
									 '".$v_receiver."',
									 '".$mailsubject."',
									 '".$msg1."',
									  sysdate(),
									 'Duc Nguyen',
									 'duc@cenpaco.com'
									 )";
									mysql_query($sql_mail) or die(mysql_error());	
									
					 }
							
						
					ob_clean();	
					header("Location: sendquote.php");
					exit();
			}
						
		
	// ********** end of outer IF
?>
            </table>
			</form> </td></tr>
			<tr><td height="14">
			
			</td></tr>
<!-- footer-->
		<tr> 
          <td>
            <?php include("footer.php");?>
          </td>
        </tr>
		<!-- footer end-->
</table>
</td></tr>
</table>
</body>
</html>
