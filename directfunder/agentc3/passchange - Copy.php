<?php
/****-------------------------------------------------------------------**************************	
		Purpose 	: 	Admin home page where the user will first get after login
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
          <td height="50px" class="up_menu"><div align="center" class="hyperlink"> 
              <?php 
include("admheadmenu.php");
?>
            </div></td>
        </tr>
        <!--header menu space end-->
        <tr>
          <td height="50px" align="center" class="title_blue">Change Password</td>
        </tr>
        <tr>
          <td height="473" valign="top" align="center">
		  <form name="default_emplate" id="default_emplate" method="post" enctype="multipart/form-data">
		  <table width="70%" border="0" cellspacing="0" cellpadding="0">
		  <tr> 
                        <td height="29" colspans="2"></td>
                      </tr>
              <tr>
                <td width="46%" height="40px" class="main_txt">Old Password :</td>
                <td width="54%">
				     <input type="password" name="oldpw" id="oldpw" class="textbox" value="<?php if(isset($_POST['oldpw'])){echo $_POST['oldpw'];}?>">
				</td>
              </tr>
              <tr>
                <td height="40px" class="main_txt">New Password :</td>
                <td>
                    <input type="password" name="newpw" id="newpw" class="textbox" value="<?php if(isset($_POST['newpw'])){echo $_POST['newpw'];}?>">
                  </td>
              </tr>
              <tr>
                <td height="40px" class="main_txt">Retype Password :</td>
                <td>
                    <input type="password" name="rpw" id="rpw" class="textbox" value="<?php if(isset($_POST['rpw'])){echo $_POST['rpw'];}?>">
                  </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="Confirm" value="Confirm" class="button_medium"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td class="alert_msg"><?php echo $msg;?></td>
              </tr>
            </table>
			</form> </td>
        </tr>
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
