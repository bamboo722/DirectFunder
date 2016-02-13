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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sales Lead DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="cbrte/html2xhtml.js"></script>
<script language="JavaScript" type="text/javascript" src="cbrte/richtext_compressed.js"></script>
<script type="text/javascript">

    function submitForm() 
    {
        //make sure hidden and iframe values are in sync for all rtes before submitting form
        updateRTEs();
        return true;
    }

    initRTE("cbrte/images/", "cbrte/", "", true);

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

</script>
<link href="css/css.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<table width="60%">
 <form method="post" enctype="multipart/form-data" name="RTEDemo" onsubmit="return submitForm();">
<!--header menu space end-->
    <tr> 
        <td height="50px" align="center" class="title_blue" colspans="2">Send Mail</td>
    </tr>
    <tr> 
        <td align="right"><input type='submit' value = "Send" name='Send' class = "button">&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;
            <input type='button' value = "Back" name='Back' onclick="window.close();">
        </td> 
    </tr>
    <tr>
        <td valign="top" align="center" colspans='2'>
	  
            <table width="99%" border="0" cellspacing="0" cellpadding="0" >
              <tr>
                  <td align="center" class="midbody_lebel">
                      <table width="66%">
                          <tr> 
                              <td >
                                   <div align="left">
                                     
                                      <script language="JavaScript" type="text/javascript">
                                        rte1.html = '<?php echo $content;?>';
                                        rte1.build();
                                      </script>
                                   </div>
                               </td>       

                           </tr>

                      </table>
                  </td>
               </tr>
             </table>           
        </td>   
    </tr>
  <?php
            //***** start insert into table *******************
           
            if ($_POST['Send']=="Send")
            {
                $v_receiver="";			
                if (isset($_REQUEST['addr']))
                {
                    $v_receiver=$_REQUEST['addr'];		
                }
                $msg1 = $_POST['rte1']; 

                $link="";
                $Fromname='Duc Nguyen';
                $Fromaddress='duc@cenpaco.com';										
                //$Fromaddress='dariuz618@hotmail.com';										
                $mailsubject = 'Hello from Duc Nguyen';							
                if ($v_receiver!="")
                {
            
                    if (mail($v_receiver, $mailsubject, $msg1, "From:".$Fromname." <".$Fromaddress.">\nContent-Type: text/html; charset=iso-8859-1")) 
                    { 
                      echo "<script type='text/javascript'>alert('Successed!')</script>";
                    }
                    mysql_query("set time_zone='-7:00';");
                    $sql_mail = "insert into mail_log(mail_rcvr, mail_subject,mail_body,send_dt,from_nm,from_address) values ('".$v_receiver."','".$mailsubject."','".$_POST['rte1']."',sysdate(),'Duc Nguyen', 'duc@cenpaco.com')";
                    mysql_query($sql_mail) or die(mysql_error());	

                 }                 
            }	        
            // ********** end of outer IF
           ?>
    </form>  
</table>		<!-- footer end-->
</body>
</html>
