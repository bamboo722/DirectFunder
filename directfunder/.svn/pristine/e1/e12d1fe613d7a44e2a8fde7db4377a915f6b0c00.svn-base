<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
$response = array();

$response['eml_templ_att'] ="";
$response['eml_templ_subj'] ="";
$response['eml_templ_cont'] ="";
$response['eml_templ_white'] ="";


/* ------------------ Email Template Load ----------------------------- */
$sql_eml_templ = sprintf("select eml_templ_subj,eml_templ_cont,eml_templ_att,eml_templ_white from profile_info where user_username='%s'",$_SESSION['user_login']);
$resb_eml = mysql_query($sql_eml_templ) or die(mysql_error());	

if( mysql_num_rows($resb_eml) > 0) 
{
	$recb_eml = mysql_fetch_assoc($resb_eml);
	$response['eml_templ_att'] = $recb_eml['eml_templ_att'];
	$response['eml_templ_subj'] = $recb_eml['eml_templ_subj'];
	$response['eml_templ_cont'] = $recb_eml['eml_templ_cont'];
	$response['eml_templ_white'] = $recb_eml['eml_templ_white'];
	
}
/* --------------------------------------------------------------------- */


if ($response['status'] == "" )
	$response['status'] = 'Success';		

echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
/*<html>
	<head>
		<title>Sales Lead DB</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="css/css.css" rel="stylesheet" type="text/css"/>
	 
	    <link href="css/wcss.css" rel="stylesheet" type="text/css" />
	    <link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8">
	 
	    <link href="css/layout.css" rel="stylesheet" type="text/css"/>
	    <link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"/>
	    <script src="https://jqueryui.com/wp-includes/js/wp-emoji-release.min.js?ver=4.2.1" type="text/javascript"></script>
	    <script src="jss/jquery-1.7.1.min.js" type="text/javascript"></script>
	    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
	    <script src="jss/jquery.hashchange.min.js" type="text/javascript"></script>
	    <script src="jss/jquery.easytabs.min.js" type="text/javascript"></script>
	<head>
	<body>
   	   <div id="dialog_email_news" title="Email News" style="text-align: center;">
		<p class="validateTips">Email News</p>
			<form>
				<div class="dashbord-task-content" >
                    <div style="width: 100%; font-size:12px;">
                         <table style="width:100%;">
                             <tr style="background-image:url('./buyer_details_img/task-bg.png');background-repeat:repeat-x;">
                                 <td style="width:5%;border-right:solid 1px #328eba;" align="center">
                                   No
                                 </td>
                                 <td style="width:10%;border-right:solid 1px #328eba;" align="center">
                                   From
                                 </td>
                                 <td style="width:15%;border-right:solid 1px #328eba;" align="center">
                                   Subject
                                 </td>
                                 <td style="width:60%;border-right:solid 1px #328eba;" align="center">
                                   Content
                                 </td>
                                 <td style="order-right:solid 1px #328eba;" align="center"> 
                                   Time
                                 </td>                               
                                 
                               </tr>
                         </table>
                     </div>
                        
                <div id='div_sms_dlg_content' style="overflow: auto; font-size:12px;">
                	 <table style='width:100%;border:solid 1px #328eba;font-size:12px;'>
                 		<?php 
						   	  for ($i=0;$i<count($response['row_id']);$i++)
						  	  {					  	  	
						  	  
							  ?>
						   	  	<tr style="min-height:50px">
							   	     
							   	  	<td style="width:5%;" align="center"> 
							   	  		<?php echo $response['row_id'][$i];?>
							   	  	</td>
							   	  	<td style="width:10%;" align="center">
							   	  		 <?php echo $response['from_nm'][$i];?>							   	  	 	
							   	  	</td>
							   	  	<td style="width:15%;border-right:solid 1px #328eba;" align="center">
                                   	 	<?php echo $response['subject'][$i];?>							   	  		
                                 	</td>
							   	  	<td style="width:60%;" align="center"> 
							   	 	 	<?php echo $response['content'][$i];?>							   	  		
							   	  	</td>
							   	  	<td align="center"> 
							   	  		<?php echo $response['recv_dt'][$i];?>							   	  		
							   	  	</td>							   	 
						   	  	</tr>
							  <?php
							  }
						  ?>	
					  </table>
                </div>       					
			</form>
		</div>    
	</body>
</html-->*/
?>