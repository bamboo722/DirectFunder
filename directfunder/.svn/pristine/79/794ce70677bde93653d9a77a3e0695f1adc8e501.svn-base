<?php
/****-------------------------------------------------------------------**************************	
		Purpose 	: 	Where user can search the buyer detail
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

if(isset($_GET['set']))
{
			  $sql_mail = "update mail_log set
			  mail_stat='FLAG'
			  where auto_no='".$_GET['set']."'";
			  mysql_query($sql_mail) or die(mysql_error());
			  //inserting staff into staff_mast table end		 
			  header("Location: sentmail.php");
			  exit();
}
if(isset($_GET['uset']))
{
			  $sql_mail2 = "update mail_log set
			  mail_stat='UNFLAG'
			  where auto_no='".$_GET['uset']."'";
			  mysql_query($sql_mail2) or die(mysql_error());
			  //inserting staff into staff_mast table end		 
			  header("Location: sentmail.php");
			  exit();
}

$sql_first="select * from mail_log";
//print($sql_cnt);
$res_sel = mysql_query($sql_first) or die(mysql_error()."11111");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript">
function memsubmit() 
{ 
if (document.all) 
{ 
document.all.default_emplate.action="searchbuyer.php"; 
document.all.default_emplate.submit(); 
} 
else 
{ 
document.default_emplate.action="searchbuyer.php"; 
document.default_emplate.submit(); 
}
}
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
          <td height="50px" align="center" class="title_blue">List of Sent Mails</td>
        </tr>
<tr><td valign="top" align="center">
		  <form name="default_emplate" id="default_emplate" method="post" enctype="multipart/form-data">
		  <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_disp_out">
		  	<tr><td align="center">
			<table width="99%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td align="center" class="midbody_lebel">&nbsp; </td>
                      </tr>
                      <tr> 
                        <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr> 
                              <td class="midbody_text3" width="3"></td>
                              <td class="midbody_text3" width="152"> 
                                <!--Search By 
                                Priority : </td>
                              <td class="midbody_text3" width="130">
							  <select name="priority" class="midbody_text3" onChange="memsubmit()">
							 <option value="" >All Priority</option>
						    <option value="New" <?php if($_POST['priority']=="New"){echo 'selected';}?>>New</option>
                            <option value="Hot" <?php if($_POST['priority']=="Hot"){echo 'selected';}?>>Hot</option>
                            <option value="Warm" <?php if($_POST['priority']=="Warm"){echo 'selected';}?>>Warm</option>
                            <option value="Cold" <?php if($_POST['priority']=="Cold"){echo 'selected';}?>>Cold</option>
                            <option value="Retry" <?php if($_POST['priority']=="Retry"){echo 'selected';}?>>Retry</option>
                            <option value="Delete" <?php if($_POST['priority']=="Delete"){echo 'selected';}?>>Delete</option>
                          </select>-->
                              </td>
                              <td class="midbody_text3" width="317"><div align="right">Total 
                                  Mail :</div></td>
                              <td width="237" class="midbody_text4">&nbsp;<?php echo mysql_num_rows($res_sel);?> 
                              </td>
                              <td width="178" class="midbody_text4"> 
                               
                              </td>
                            </tr>
                          </table></td>
                      </tr>
                      <tr> 
                        <td colspans="5" bgcolor="#666666" height="1px"></td>
                      </tr>
                      <tr> 
                        <td> <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="E5E5E5">
                            <?php 
					  	
						
						if(mysql_num_rows($res_sel)=='0')
						{
						?>
                            <tr> 
                              <td colspans="4" class="midbody_text1" align="center">Sorry, 
                                No Record Found.</td>
                            </tr>
                            <?php
						}
						else
						{
						 while($seerec = mysql_fetch_assoc($res_sel))
							{
							
						 
							?>
                            <tr> 
                              <td class="midbody_text1" width="61" align="center">
							  <?php
							  if($seerec['mail_stat']!='FLAG')
							  {
							  ?>
							  <a href="sentmail.php?set=<?php echo $seerec['auto_no'];?>"><img src="images/tranflag.jpg" height="20px" width="20px" border="0"></a>
							  <?php
							  }
							  else
							  {
							  ?>
							  <a href="sentmail.php?uset=<?php echo $seerec['auto_no'];?>"><img src="images/flag.jpg" height="35px" width="35px" border="0"></a>
							  <?php
							  }
							  ?>
							  </td>
                              <td class="midbody_text1" width="210">To : <?php echo $seerec['mail_rcvr'];?></td>
                              <td class="midbody_text1" width="503"><a href="sentmaildtl.php?mid=<?php echo $seerec['auto_no'];?>"><?php echo $seerec['mail_subject'];?></a></td>
                              <td class="midbody_text1" width="115"><?php echo $seerec['send_dt'];?></td>
                            
                              
                              
                             
                            </tr>
                            <?php
					  }
					 }
					 ?>
                          </table></td>
                      </tr>
                    </table>
			</td></tr>
		  	   
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
