<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");	// Email

$response = array();

$response['status']="";
$response['sms_news']=0;
$response['email_news']=0;
$response['call_news']=0;
$agent_name = $_SESSION['user_login'];

$sql_mail = "select count(*) as news_cnt from mail_log_info where mail_stat=1";
$sql_res = mysql_query($sql_mail) or die(mysql_error());	
$sql_result = mysql_fetch_assoc($sql_res);
$response['email_news']=$sql_result['news_cnt'];


$response['row_id'] = array();
$response['response_address'] = array();
$response['from_address'] = array();
$response['subject'] = array();
$response['content'] = array();
$response['recv_dt'] = array();
$response['mail_st'] = array();
$cur_login_time = $_SESSION['cur_login_time'];

// get email history since last log out time 
$seeavail="select * from mail_log_info where  ((from_address ='".$_SESSION['google_acc_nm']."')or (mail_rcvr like ('".$_SESSION['google_acc_nm']."'))) order by send_dt desc";
$seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");
$row_id=0;
if (mysql_num_rows($seeres) == '0'){	

}else 
{
   while ($seerec = mysql_fetch_assoc($seeres)) {
      $response['row_id'][$row_id]=	$row_id+1;
    
    
      	
      if ($seerec['from_address'] != '') 
      	$response['from_address'][$row_id]=$seerec['from_address'];
      else 
      	$response['from_address'][$row_id]="&nbsp;";
      	
      if ($seerec['mail_rcvr'] != '') 
      	$response['mail_rcvr'][$row_id]=$seerec['mail_rcvr'];
      else 
      	$response['mail_rcvr'][$row_id]="&nbsp;";
    
      if ($seerec['mail_rcvr'] != $_SESSION['google_acc_nm']) 
      	$response['response_address'][$row_id]=$seerec['mail_rcvr'];
      else 
      	$response['response_address'][$row_id]=$seerec['from_address'];
      	
      if ($seerec['mail_subject'] != '') 
      	$response['mail_subject'][$row_id]=$seerec['mail_subject'];
      else 
      	$response['mail_subject'][$row_id]="&nbsp;";
      	
      
      if ($seerec['mail_body'] != '') 
      	$response['mail_body'][$row_id]= $seerec['mail_body'];
      else 
         $response['mail_body'][$row_id]="&nbsp;";
         
      if ($seerec['send_dt'] != '') 
      	$response['send_dt'][$row_id]=$seerec['send_dt'];
      else 
      	$response['send_dt'][$row_id]="&nbsp;";
         
      if ($seerec['is_opened'] != '') 
      	$response['is_opened'][$row_id]=$seerec['is_opened'];
      else 
      	$response['is_opened'][$row_id]="&nbsp;";
      
      if ($seerec['mail_stat'] != '') 
      	$response['mail_stat'][$row_id]=$seerec['mail_stat'];
      else 
      	$response['mail_stat'][$row_id]="&nbsp;";
      	
      $response['mail_st'][$row_id]=(int)$seerec['mail_stat'];
      
      
      $row_id++;
   }   
}
//$response['email_news']=count($response['row_id']);                      
/* mark unread email as read email */
$seeavail = "update mail_log_info set mail_stat= 2 where mail_stat=1";
$seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");

/* ------------------ Email Template Load ----------------------------- */
$sql_eml_templ = sprintf("select eml_templ_subj,eml_templ_cont,eml_templ_att,eml_templ_white from profile_info where user_username='%s'",$_SESSION['user_login']);
$resb_eml = mysql_query($sql_eml_templ) or die(mysql_error());	

if( mysql_num_rows($resb_eml) > 0) 
{
	$recb_eml = mysql_fetch_assoc($resb_eml);
	$eml_att = $recb_eml['eml_templ_att'];
	$eml_subj = $recb_eml['eml_templ_subj'];
	$eml_cont = $recb_eml['eml_templ_cont'];
	$eml_white = $recb_eml['eml_templ_white'];
	
}
/* --------------------------------------------------------------------- */

//Email Sent
$sql="select count(mail_stat) as email_sent from mail_log_info where  from_address ='".$_SESSION['google_acc_nm']."' and send_dt > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['email_sent']=$res_rec['email_sent'];

					
//Email Receive
$sql="select count(mail_stat) as email_recv from mail_log_info where  mail_rcvr ='".$_SESSION['google_acc_nm']."' and send_dt > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['email_recv']=$res_rec['email_recv'];



//echo json_encode($response);


//Function to check if the request is an AJAX request
/*function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Sales Lead DB</title>
        
        <!-- utf8 setting -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    
	    <!-- Bootstrap -->
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
      
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
 		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		
	    <script type="text/javascript">
	    	/* Email Preview */
			function previewEmail()
       		{
       			console.log("previewEmail");
				var form = document.getElementById('SendEmailUpload');
				var formData = new FormData(form);
       			
			    $.ajax({
			        url: 'previewEmail.php',
			        data: formData,
			        dataType : "json",
			        processData: false,
  					contentType: false,
			        type: 'POST',
			        success: function ( res ) {
			        	console.log("Success");
			        	console.log(res);
			        	if (res.status == "Success")
			        	{
			        		if (res.email_body != "")
			        		{
			        			console.log(res.email_body);
								document.getElementById("email_preview_div").innerHTML =res.email_body;								
          						$('#dialog_preview_email').modal();            					
							}							
						}							
			        }
			    });				   
			}			   
	    	function sendEmail()
       		{
			   	var valid = true;	
			   	console.log("sendemail");
			 	var form = document.getElementById('SendEmailUpload');
				var formData = new FormData(form);
				var xhr = new XMLHttpRequest();
				// Add any event handlers here...
				xhr.open('POST', 'sendemail.php', true);
				xhr.send(formData);

			 	console.log(formData);
		    	$("#dialog_email").modal('hide');
		  		$('#dialog_preview_email').modal('hide');      
		  		setTimeout(getEmailStatus,30*1000);
				return valid;						
			}
			
		    function ClicktoEmail(addr)
            {
            	console.log('ClicktoEMAIL');
            	document.getElementById("email_to").value = addr;            	
            	$("#dialog_email").modal();				
			}
	    </script>
	</head>
	<body>
		<form name="frmSearch" method="post" action="getHistory_email.php">
		<div class="container">
			<h1>&nbsp;</h1>
	   		<div class="row">
	   			<div class="panel panel-primary">
					<div class="panel-heading"><center><h3>Email History</h3></center></div>
					<div class="panel-body">
			      		<div class="table-responsive" style="height:600px; overflow:auto; overflow-y:scroll;">    
			    			<table class="table table-striped">  
		    					<thead>
									<tr style="font-size:16px">
										<th class="col-sm-1">No</th>
										<th class="col-sm-1">From</th>
										<th class="col-sm-1">To</th>
										<th class="col-sm-2">Subject</th>													        
										<th class="col-sm-4">Content</th>		
										<th class="col-sm-1">Time</th>		
										<th class="col-sm-1">Opened</th>		
										<th class="col-sm-1">Res</th>										
								    </tr>
							    </thead>
								<tbody>
								<?php	
								  for ($i=0;$i<count($response['row_id']);$i++)
							  	  {					  	  	
							  	  	 if ($response['send_dt'][$i] > $_SESSION['last_logout']) {
										if ($response['mail_st'][$i] == 1) {
											$style="style='font-size:16px;font-weight:bold;color:#0c00ea;'";
		                            	}else{
											$style="style='font-size:16px;color:#0c00ea;'";	                                    
		                                }	
		                            }else
		                            	$style="style='font-size:16px;'";
								?>		
										<tr <?php echo $style;?>>
											<td class="col-sm-1"><?php echo $i+1;?></td>
									        <td class="col-sm-1"><?php echo $response['from_address'][$i];?></td>
									     	<td class="col-sm-1"><?php echo $response['mail_rcvr'][$i];?></td>
									    	<td class="col-sm-2"><?php echo $response['mail_subject'][$i];?></td>
									        <td class="col-sm-4"><?php echo $response['mail_body'][$i];?></td>
									        <td class="col-sm-1"><?php echo $response['send_dt'][$i];?></td>
									        <td class="col-sm-1"><?php if ($response['mail_stat'][$i]==0)
									        	{
													if ($response['is_opened'][$i]==1)
													{
														echo "Yes";
													}else
													{
														echo "No";
													}												
												}
									        	?></td>
									        <td class="col-sm-1"><button type="button" onclick="javascript: ClicktoEmail('<?php echo $response['response_address'][$i];?>');"  class="btn btn-success btn-xs" style="padding:1px">Email</button></td>
									    </tr> 
			                     <?php	                                                   		
			                    	 }
			                     ?>													     
							    </tbody>
		    				</table>
			    		</div>											
                    </div>			   
				</div>
	   		</div>
	   	</div>
	  </form>	
	  
	
	<!-- email preview dialog -->
    <div id="dialog_preview_email" class="modal fade" style="z-index:1000000001" title="" style="display:none;">
        	<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Preview Email</h4>				        
			      	</div>
			      	<div class="modal-body" style="overflow:auto" id="email_preview_div">
			      		
			      	</div>	
			      	<div class="modal-footer">
			      		<center>			      			
			      			<div class="col-xs-offset-4 col-xs-4">
			      				<button type="submit" onclick="javascript:sendEmail()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>
			      			</div>			      							      		
			      		</center>			      		
			      	</div>				     
				</div>
			</div>
		</div>

	<!-- email send modal dialog -->
		<div id="dialog_email" class="modal fade" style="z-index:1000000000"  title="Send Email" style="display:none;">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Send Email</h4>
			      	</div>
			      	<div class="modal-body">
			        	<form enctype="multipart/form-data" method="post" id="SendEmailUpload">
			        		<div class="form-group">
				            	<label for="email_from"><span class="glyphicon glyphicon-user"></span> From</label>
				            	<input type="text" class="form-control" name="email_from" id="email_from" placeholder="Enter email" value="<?php if (isset($_SESSION['google_acc_nm'])) echo $_SESSION['google_acc_nm']; else echo ''; ?> " >				            	
				            </div>
            				<div class="form-group">
				            	<label for="email_to"><span class="glyphicon glyphicon-user"></span>To</label>
				            	<input type="text" name="email_to" id="email_to" 
							    	value="<?php
							  	 	  $p_eml1="";
							  	 	  $p_eml2="";
							  	 	  if (isset($_POST['p_eml1'])) {
			                             $p_eml1 =$_POST['p_eml1'];
			                          } else if (isset($recb['p_eml1'])) {
			                              $p_eml1 =$recb['p_eml1'];
			                          } 
			                           if (isset($_POST['p_eml2'])) {
			                             $p_eml2 =$_POST['p_eml2'];
			                          } else if (isset($recb['p_eml2'])) {
			                              $p_eml2 =$recb['p_eml2'];
			                          } 
			                          
							  	 	  $default_email_to="";
							  	 	  
							  	 	  if ($p_eml1 != '') 
							  	 	  {
									  	$default_email_to =$p_eml1;	
									  	if ($p_eml2 !='') 
									  	{
											$default_email_to .= ";".$p_eml2;	
										}
									  }else if ($p_eml2 !='') 
									  {
										$default_email_to = $p_eml2;	
									  }
							  	 	  echo  $default_email_to; 
							  	 	  ?>"
							  	 	 class="form-control" placeholder="Enter Email Address">				            	
				            </div>
            				<div class="form-group">
				            	<label for="email_subj">Subject</label>
				            	<input type="text" name="email_subj" id="email_subj" value="<?php echo $eml_subj;?>" class="form-control"/>				            	
				            </div>
							<div class="form-group">
				            	<label for="user_email_attach_file_name">Attach File</label>
				            	<input name="user_email_attach_file_name" type="file"  value="<?php echo $eml_att;?>" class="form-control"/>
				            </div>
				            <div class="form-group">
				            	<label for="user_email_attach_file_name">Salutation</label>
				            	<input type="text" name="email_sal" id="email_sal"   value="Hello" class="form-control"/>
				            </div>
				            <div class="form-group">
				            	<label for="email_body">Content</label>
				            	<textarea id="email_body" name="email_body" rows="5" cols="20" class="form-control"><?php echo $eml_cont;?></textarea>				    
				            </div>							
						</form>
			      	</div>
			      		
			      	<div class="modal-footer">
			      		<div class="row">
			      			<div class="col-xs-6">
			      				<button type="submit" onclick="javascript:sendEmail()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>			        	 
			      			</div>
			      			<div class="col-xs-6">
			      				<button type="submit" onclick="javascript:previewEmail()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Preview</button>			        	 
			      			</div>
			      		</div>	
			      	</div>					
				</div>
			</div>
		</div>	
	
	</body>
</html>
