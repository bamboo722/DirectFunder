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
<table width="70%">
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
                        <td><select name="source" id="source" class="textbox">
                            <?php
									$sql_source="select distinct data_insert_by from customer_info";
									$res_source=mysql_query($sql_source);
									while($v_res_source=mysql_fetch_assoc($res_source))
									{
								  ?>
                            <option value="<?php echo $v_res_source['data_insert_by'];?>" <?php if($_GET['aid']==$v_res_source['data_insert_by']){echo "selected";} else if($_POST['source']==$v_res_source['data_insert_by']){echo "selected";}?>><?php echo $v_res_source['data_insert_by'];?></option>
                            <?php
									}
								?>
                          </select>
                          &nbsp;&nbsp;
						<input type="submit" name="Submit" value="Show" class="button"></td>
                      </tr>
					  <tr> 
                        <td><div style="overflow: auto;height: 160px; width: 500px;"> 
                      <table width="100%">
                
				<?php
				 if($_POST['Submit']=="Show")
				 { 
				    if($_POST['source']=="admin")
					{
						$sql_sel="select p_eml1 from customer_info where data_insert_by='admin' and p_eml1!=''";
					}
					else if($_POST['source']=="Financing")
					{
						$sql_sel="select p_eml1 from customer_info where data_insert_by='Financing' and p_eml1!=''";
					}
					else if($_POST['source']=="Quotes")
					{
						$sql_sel="select p_eml1 from customer_info where data_insert_by='Quotes' and p_eml1!=''";
					}
					else if($_POST['source']=="Manual")
					{
						$sql_sel="select p_eml1 from customer_info where data_insert_by='Manual' and p_eml1!=''";
					}
					$res=mysql_query($sql_sel);
					//$i=0;
					$customer_mail="";
					while($v_res=mysql_fetch_assoc($res))
					{
							echo "<tr><td width='20%'></td>";
							printf(
								"<td><input type=\"checkbox\" name=\"mailsend\" value=\"%s\" checked onclick=\"cat_check()\" /></td>\n",
								$v_res['cli_email']
								);					
					  ?>

                        <td height="21" align="left"><?php echo $v_res['p_eml1'];?></td>
                        <td height="21"></td>
                        <?php
							$customer_mail=$v_res['p_eml1'].','.$customer_mail;
							echo "</tr>";
				}
				}	// end while
				?>
                      </table>
                    </div></td>
                      </tr>
                      <tr> 
                        <td align="center" class="midbody_lebel"> <table width="66%">
                            <tr> 
                              <td width="74%">&nbsp; </td>
                              <td width="26%" class="midbody_lebel">&nbsp;</td>
                            </tr>
                            <tr> 
                              <td width="74%">
							  <div align="left">
							  <?php		
							   $body="select mail_body from draft_mail_mast where mail_source='".$_POST['source']."'"; 
							   //print($body);
							   $res_body=mysql_query($body);
							   $v_body=mysql_fetch_assoc($res_body);
							   //print($v_body['mail_body']);
											
									//format content for preloading
									if (!(isset($_POST["rte1"]))) 
									{
										$content = $v_body['mail_body'];
										$content = rteSafe($content);
									} 
									else 
									{
										//retrieve posted value
										$content = rteSafe($_POST["rte1"]);
									}
							
								?> 
							  <script language="JavaScript" type="text/javascript">
							  rte1.html = '<?php echo $content;?>';
							  rte1.build();
							  </script>
								</div>
							  </td>
                              <td width="26%" class="midbody_lebel"><input type="submit" name="Save" value="Save" class="button_medium"></td>
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
