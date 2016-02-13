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
$slno="select count(mail_source) cnt from draft_mail_mast where mail_source='".$_POST['source']."'";
$sres=mysql_query($slno);
$srec=mysql_fetch_assoc($sres);

if($_POST['Save']=="Save")
{
	if($srec['cnt']==0)
	{
        $adm_dtl="INSERT INTO draft_mail_mast
	    (
		mail_source,
		mail_body,
		draft_dt
		)
		VALUES
		(
		'".sql_quote($_POST['source'])."',
		'".sql_quote($_POST['rte1'])."',
		 curdate()
		)";
  		mysql_query($adm_dtl);
	}
	else
	{
		$text_upd="update draft_mail_mast set 
		mail_source='".sql_quote($_POST['source'])."',
		mail_body='".sql_quote($_POST['rte1'])."',
	    draft_dt= curdate()
		where mail_source='".$_POST['source']."'";
		mysql_query($text_upd);
	}
		header("Location: draftmail.php");
		exit();
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
document.all.RTEDemo.action="draftmail.php"; 
document.all.RTEDemo.submit(); 
} 
else 
{ 
document.RTEDemo.action="draftmail.php"; 
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
                              <td width="74%">
							  <select name="source" id="source" class="textbox" onChange="memsubmit()">
							  <option value="">Select Source</option>
                                  <?php
									$sql_source="select distinct data_insert_by from customer_info";
									$res_source=mysql_query($sql_source);
									while($v_res_source=mysql_fetch_assoc($res_source))
									{
								  ?>
												  <option value="<?php echo $v_res_source['data_insert_by'];?>" <?php  if($_POST['source']==$v_res_source['data_insert_by']){echo "selected";}?>>
												  <?php echo $v_res_source['data_insert_by'];?>
												  </option>
								<?php
									}
								?>
                                </select>
                              </td>
                              <td width="26%" class="midbody_lebel">&nbsp;</td>
                            </tr>
                            <tr> 
                              <td width="74%">
							  <div align="left">
							  <?php
							   if(isset($_POST['source']))
							   {
							   		$sql_sel="select mail_body from draft_mail_mast where mail_source='".$_POST['source']."'"; 
									$res=mysql_query($sql_sel);
							   		$v_res=mysql_fetch_assoc($res);
							   }
							   
											
									//format content for preloading
									if (isset($_POST["rte1"])) 
									{
										$content = $v_res['mail_body'];
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
						<!--<table width="50%">
			<?php
			$slno="select * from draft_mail_mast";
			$sres=mysql_query($slno);
			while($srec=mysql_fetch_assoc($sres))
			{
			?>
			<tr>
			<td ><a href="draftmail.php?aid=<?php echo $srec['mail_source'];?>" class="link_txt2">Edit <?php echo $srec['mail_source'];?> Mail Draft</a></td>
			<td></td>
			</tr>
			<?php
			}
			?>
			</table>-->
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
