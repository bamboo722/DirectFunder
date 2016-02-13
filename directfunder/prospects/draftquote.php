<?php
session_start();
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

//insert or update to the draft master table


if($_POST['Save']=="Save")
{
	$slno="select count(machine_no) cnt from quote_draft where machine_no=".$_POST['machine'];
	$sres=mysql_query($slno);
	$srec=mysql_fetch_assoc($sres);
	if($srec['cnt']==0)
	{
        $adm_dtl="INSERT INTO quote_draft
	    (
		machine_no,
		quote_mail_draft
		)
		VALUES
		(
		".sql_quote($_POST['machine']).",
		'".sql_quote($_POST['rte1'])."'
		)";
  		mysql_query($adm_dtl);
		//print($adm_dtl);
	}
	else
	{
		$text_upd="update quote_draft set 
		machine_no=".sql_quote($_POST['machine']).",
		quote_mail_draft='".sql_quote($_POST['rte1'])."'
		where machine_no=".$_POST['machine'];
		mysql_query($text_upd);
	}
		
}
//insert or update to the draft master table end
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="cbrte/html2xhtml.js"></script>
<script language="JavaScript" type="text/javascript" src="cbrte/richtext_compressed.js"></script>
<script type="text/javascript">

function memsubmit() 
{ 
if (document.all) 
{ 
document.all.RTEDemo.action="draftquote.php"; 
document.all.RTEDemo.submit(); 
} 
else 
{ 
document.RTEDemo.action="draftquote.php"; 
document.RTEDemo.submit(); 
} 
}

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
          <td height="50px" align="center" class="title_blue">Draft Mail</td>
        </tr>
<tr><td valign="top" align="center">
		  <form method="post" enctype="multipart/form-data" name="RTEDemo" onsubmit="return submitForm();">
		  <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_disp_out">
		  	<tr><td align="center">
			<table width="99%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td align="center" class="midbody_lebel"> <table width="66%">
                            <tr> 
                              <td width="74%"> <select name="machine"  class="textbox" size="1" onChange="memsubmit()">
							  	  <option value="">Select No of Machines</option>
                                  <option value="1" <?php if($_POST['machine']==1){echo "selected";}?>>1</option>
                                  <option value="2" <?php if($_POST['machine']==2){echo "selected";}?>>2</option>
                                  <option value="3" <?php if($_POST['machine']==3){echo "selected";}?>>3</option>
                                  <option value="4" <?php if($_POST['machine']==4){echo "selected";}?>>4</option>
                                  <option value="5" <?php if($_POST['machine']==5){echo "selected";}?>>5</option>
                                  <option value="6" <?php if($_POST['machine']==6){echo "selected";}?>>6</option>
                                  <option value="7" <?php if($_POST['machine']==7){echo "selected";}?>>7</option>
                                  <option value="8" <?php if($_POST['machine']==8){echo "selected";}?>>8</option>
                                </select> </td>
                              <td width="26%" class="midbody_lebel">&nbsp;</td>
                            </tr>
                            <tr> 
                              <td width="74%">
							  <div align="left">
							  <?php
							   if(isset($_POST['machine']))
							   {
							   		$sql_sel="select quote_mail_draft from quote_draft where machine_no=".$_POST['machine']; 
									//print($sql_sel);
									$res=mysql_query($sql_sel);
							   		$v_res=mysql_fetch_assoc($res);
							   }
							   
											
									//format content for preloading
									if (isset($_POST["rte1"])) 
									{
										$content = $v_res['quote_mail_draft'];
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
                        <td>
						<table bgcolor="#f8f8f8" bordercolor="#000000" bordercolordark="#000033"> </table>
						</td>
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
