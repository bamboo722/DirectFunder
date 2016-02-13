<?php
session_start();
ob_start();
if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
	header("Location: index.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");


if($_POST['Send'] == "Forward")
{
  header("Location: forwardmail.php?mid=".$_GET['mid']);
  exit();
}

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
rte1.width = 700;
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
          <td height="50px" align="center" class="title_blue">Sent Mail</td>
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
                              <td width="74%"><input type="hidden" name="mailField" size="50" value="<?php echo $customer_mail; ?>"></td>
                              <td width="26%" class="midbody_lebel">&nbsp;</td>
                            </tr>
                            <tr> 
                              <td width="74%">
							  <div align="left">
							  <?php		
							   $body="select mail_body from mail_log where auto_no='".$_GET['mid']."'"; 
							   //print($body);
							   $res_body=mysql_query($body);
							   $v_body=mysql_fetch_assoc($res_body);
							   //print($v_body['mail_body']);
											
									//format content for preloading
									
									
										$content = $v_body['mail_body'];
										$content = rteSafe($content);
									
									
								?> 
							  <script language="JavaScript" type="text/javascript">
							  rte1.html = '<?php echo $content;?>';
							  rte1.build();
							  </script>
								</div>
							  </td>
                              <td width="26%" class="midbody_lebel"><input type="submit" name="Send" value="Forward" class="button_medium"></td>
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
			if($_POST['mailField']=="")
			{
				$v_customer=explode(",", $customer_mail);
			}
			else
			{
				$v_customer=explode(",", $_POST['mailField']);
			}
			
											
											
						//********* start insert into table *******************
						
						if ($_POST['Send']=="Send")
						{
									$mailsubject = 'Confirmation Mail from Alpine360hp.com'; 
									$msg1 = $_POST['rte1']; 
									$v_receiver="";							
									$v_receiver=$_POST['mailField'];
									$link="";
									$Fromname='Admin';
									$Fromaddress='Admin';										
									$m=0; 
									
									while ($v_customer[$m]!="")
									{
									//print('mail id ='.$v_customer[$m].' msage body='.$msg1.' subject body='.$mailsubject.'from='.$Fromname);
	
										if (mail($v_customer[$m], $mailsubject, $msg1, "From:".$Fromname." <".$Fromaddress.">\nContent-Type: text/html; charset=iso-8859-1")) 
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
											 '".$v_customer[$m]."',
											 'Mail from Alpine360hp Administrator',
											 '".$_POST['rte1']."',
											  sysdate(),
											 'Administrator',
											 'duc@cenpaco.com'
											 )";
										mysql_query($sql_mail) or die(mysql_error());	
										$m=$m+1;
									}
							
						
					ob_clean();	
					header("Location: sendmail.php");
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
