<?php
session_start();
ob_start();
if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
	header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");

function rteSafe($strText)
{
	//returns safe code for preloading in the RTE
	$tmpString = $strText;
	
	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);
	
	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
	//$tmpString = str_replace("\"", "\"", $tmpString);
	
	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);
	return $tmpString;
}

$v_customer=array();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="cbrte/html2xhtml.js"></script>
<script language="JavaScript" type="text/javascript" src="cbrte/richtext_compressed.js"></script>
<script type="text/javascript">

function cat_check() 
{	
	var buffer=document.RTEDemo.mailField.value;
	document.RTEDemo.mailField.value='';
	for(var k=0; k < document.RTEDemo.mailsend.length; k++)
	{	
			if ( document.RTEDemo.mailsend[k].checked )
			 	{
						document.RTEDemo.mailField.value +=document.RTEDemo.mailsend[k].value+',';
				}		
	}	
}

function submitForm() 
{
	//make sure hidden and iframe values are in sync for all rtes before submitting form
	updateRTEs();
	
	return true;
}

initRTE("cbrte/images/", "cbrte/", "", true);
		//-->
<!--
//build new richTextEditor

var rte1 = new richTextEditor('rte1');
rte1.width = 800;
rte1.height = 400;
<?php
//format content for preloading
if (isset($_POST["rte1"])) 
{
	$content = $_POST["rte1"];
	$content = rteSafe($content);
	//$msgsub="Please enter Subject";
} 
else 
{
	//retrieve posted value
	$content = rteSafe($_POST["rte1"]);
}
?>

//rte1.toggleSrc = false;

//-->
 
</script>
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
          <td height="50px" align="center" class="title_blue">Send Mail</td>
        </tr>
<tr><td valign="top" align="center">
		  <form method="post" enctype="multipart/form-data" name="RTEDemo" onsubmit="return submitForm();">
		  <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_disp_out">
		  	<tr><td align="center">
			<table width="99%" border="0" cellspacing="0" cellpadding="0">
					<tr> 
                        <td height="43"> &nbsp;&nbsp; </td>
                      </tr>
					  <tr> 
                        <td> &nbsp;&nbsp; </td>
                      </tr>
					  <tr> 
                        <td>
						</td>
                      </tr>
					  <tr> 
                        <td></td>
                      </tr>
                      <tr> 
                        <td align="center" class="midbody_lebel"> <table width="66%">
                            <tr> 
                              <td width="74%">Subject : <input type="text" name="subject" class="textbox"></td>
                              <td width="26%" class="midbody_lebel">&nbsp;</td>
                            </tr>
                            <tr> 
                              <td width="74%">
							  <div align="left">
							  <?php		
							   $body="select a.customer_id,a.machine_no,a.p_eml1 eml,b.machine_no,b.quote_mail_draft 
										from customer_info a, quote_draft b
										where a.customer_id='".$_GET['rid']."'
										and a.machine_no=b.machine_no"; 
							   //print($body);
							   $res_body=mysql_query($body);
							   $v_body=mysql_fetch_assoc($res_body);
							   $veml=$v_body['eml'];
							   //print($v_body['mail_body']);
											
									//format content for preloading
									
									
										$content = $v_body['quote_mail_draft'];
										$content = rteSafe($content);
									
									
								?> 
							  <script language="JavaScript" type="text/javascript">
							  rte1.html = '<?php echo $content;?>';
							  rte1.build();
							  </script>
								</div>
							  </td>
                              <td width="26%" class="midbody_lebel"><input type="submit" name="Send" value="Send" class="button_medium"></td>
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
                        <td align="left">
						</td>
                      </tr>
                    </table>
			</td></tr>
		  	   <?php					
						//********* start insert into table *******************
						
						if ($_POST['Send']=="Send")
						{
									$mailsubject = $_POST['subject']; 
									
									//msg body generation start
									
									$msg1 = addslashes($_POST['rte1']);
									
									$fp = fopen("template/10515175961.html", 'r');
									while(!feof($fp)) 
										{ //Do until out of lines
											$line = $line.fgets($fp, 5000); //Get line
											
										}
									$tpl1= substr($line,0,strpos($line,"--BODY--"));
									
									$tpl2= substr($line,strpos($line,"--BODY--")+8);
									$msg1=$tpl1.$msg1.$tpl2;
									
									
									
									
									
									
									//msg body generation end
									
																
									$v_receiver = $veml;
									$Fromname='Duc Nguyen';
									$Fromaddress='duc@cenpaco.com';										
									//print('mail id ='.$v_receiver.' msage body='.$msg1.' subject body='.$mailsubject.'from='.$Fromname);
				
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
						
					ob_clean();	
					header("Location: searchbuyer.php");
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
