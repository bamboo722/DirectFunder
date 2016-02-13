<?php
session_start();
if(!isset($_SESSION['user_login']) ||$_SESSION['user_login']=="")//session store admin name
{
	header("Location: adminlogin.php");//login in AdminLogin.php
}
require_once("includes/dbconnect.php");
function findexts($filename)
{
$filename=strtolower($filename);
$exts=split("[/\\.]",$filename);
$n = count($exts)-1;
$exts = $exts[$n];
return $exts;
}
//upload for profile pic//
if($_FILES["file"]["name"]!="")
{
		$ran= rand();
		$ran=$ran.".";
		$ext= findexts($_FILES["file"]["name"]);
		$fname=$ran.$ext;
		$target="quotes/".$fname;
}
else
{
	$fname="";
}
if($_POST['Add'] == "Add")
{
		 			$sql_ins_more="insert into quote_mast
					(
					machine_no,
					file_nm
					)
					values
					(
					  '".$_POST['mno']."',
					  '".$fname."'
					 )";
					 mysql_query($sql_ins_more) or die(mysql_error()."error");
					 move_uploaded_file($_FILES["file"]["tmp_name"], $target);
					 header("Location: quoteupld.php");
					 exit();
}
?>
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/css.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<table width="600" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td bgcolor="#FFFFFF"  width="85%" valign="top"> 
	
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
   
        <tr> 
          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="26"  align="center"> 
				
				</td>
              </tr>
			  <tr> 
                <td height="26"  align="middle"> 
				<form name="default_emplate" id="default_emplate" method="post"  enctype="multipart/form-data">
				    <table width="600" cellpadding="0" cellspacing="0" bgcolor="#0066FF">
                      <tr> 
                        <td width="26" height="40px">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="300" class="heading_title"><div align="center">Upload 
                            File </div></td>
                        <td width="19" height="40px">&nbsp;</td>
                        <td width="153"></td>
                      </tr>
					  <tr> 
                        <td height="40px">&nbsp;</td>
                        <td width="100" class="whitetxt" valign="top">&nbsp;</td>
                        <td width="300"><input type="text" name="mno"></td>
                        <td width="19" height="40px">&nbsp;</td>
                        <td width="153" class="heading_black">&nbsp; </td>
                      </tr>
                      <tr> 
                        <td height="40px">&nbsp;</td>
                        <td width="100" class="whitetxt" valign="top">&nbsp;</td>
                        <td width="300"><input type="file" class="oval_text2" name="file" id="file"/></td>
                        <td width="19" height="40px">&nbsp;</td>
                        <td width="153" class="heading_black"><input type="submit" name="Add" value="Add" class="button_blue">
                        </td>
                      </tr>
                      <tr> 
                        <td height="40px">&nbsp;</td>
                        <td width="100" class="heading_black">&nbsp;</td>
                        <td width="300"> </td>
                        <td width="19" height="40px">&nbsp;</td>
                        <td width="153" class="heading_black">
                          <!--<input type="button" name="Exit2" value="Exit" onClick="winback()" class="button_blue" >-->
                        </td>
                      </tr>
                    </table>
                    

						
				</form>
				</td>
              </tr>
			  <tr>
			  <td>
			 
				</td>
				
			
				<tr><td>
				
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
						 <td width="14%" valign="middle" class="title_blue">Date</td>
						 <td width="86%" valign="middle" class="title_blue">Files</td>
                </tr>
				<?php
				$user_fetch = "select * from quote_mast";
				$res_user = mysql_query($user_fetch) or die(mysql_error()."11");
				while($rec_user = mysql_fetch_assoc($res_user))
				{
				?>
				<tr>
						 <td class="smalltext_black_2" height="20"><?php echo $rec_user['machine_no'];?></td>
						 <td class="smalltext_black_2"><?php echo $rec_user['file_nm'];?></td>
                </tr>
				<?php
				}
				?>
			  	</table>
			  </td>
			  </tr>
			  
              <tr> 
                <td  align="center" valign="top">
                        </table></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>

</body>
</html>
