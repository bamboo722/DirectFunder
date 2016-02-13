<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");


$response = array();
$response['auto_no'] = array();
$response['row_id'] = array();
$response['response_address'] = array();
$response['from_address'] = array();
$response['subject'] = array();
$response['content'] = array();
$response['recv_dt'] = array();
$response['mail_st'] = array();
$cur_login_time = $_SESSION['cur_login_time'];
$agent_name = $_SESSION['user_login'];


$filterAgent='';
if ($_SESSION['user_group'] == 'Manager')
{
	$sql_sel_agents = sprintf("select user_id from admin_user where owner='%s'",$_SESSION['user_login']);
	$sql_res_agents = mysql_query($sql_sel_agents) or die(mysql_error());
	$filterAgent = " ( from_nm='" . $_SESSION['user_login'] . "'";
	while($sql_rec_agents = mysql_fetch_assoc($sql_res_agents))
	{
		if ($sql_rec_agents['user_id']!="")	
			$filterAgent .= " or from_nm='".$sql_rec_agents['user_id']."'";
	}
	$filterAgent .= ")";
}   
$seeavail = "select * from  mail_log_info where is_opened=1 $filterAgent order by send_dt desc";
$seeres = mysql_query($seeavail) or die(mysql_error());



$row_id=0;
if (mysql_num_rows($seeres) == '0'){	

}else 
{
   while ($seerec = mysql_fetch_assoc($seeres)) {
      $response['row_id'][$row_id]=	$row_id+1;
    
      if ($seerec['auto_no'] != '') 
      	$response['auto_no'][$row_id]=$seerec['auto_no'];
      else 
      	$response['auto_no'][$row_id]="&nbsp;";
      	
      if ($seerec['from_address'] != '') 
      	$response['from_address'][$row_id]=$seerec['from_address'];
      else 
      	$response['from_address'][$row_id]="&nbsp;";
      	
      if ($seerec['mail_rcvr'] != '') 
      	$response['mail_rcvr'][$row_id]=$seerec['mail_rcvr'];
      else 
      	$response['mail_rcvr'][$row_id]="&nbsp;";
    
            	
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
      
       if ($seerec['opened_time'] != '') 
      	$response['opened_time'][$row_id]=$seerec['opened_time'];
      else 
      	$response['opened_time'][$row_id]="&nbsp;";
      	
      if ($seerec['mail_stat'] != '') 
      	$response['mail_stat'][$row_id]=$seerec['mail_stat'];
      else 
      	$response['mail_stat'][$row_id]="&nbsp;";
      	
      $response['mail_st'][$row_id]=(int)$seerec['mail_stat'];
      
      
      $row_id++;
   }   
}

/* --------------------------------------------------------------------- */
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
		    	$("#dialog-email").modal('hide');
		  		$('#dialog_preview_email').modal('hide');      
		  		setTimeout(getEmailStatus,30*1000);
				return valid;						
			}
			
		    function ClicktoEmail(addr)
            {
            	console.log('ClicktoEMAIL');
            	document.getElementById("email_to").value = addr;            	
            	$("#dialog-email").modal();				
			}
			function ClicktoReply(addr)
			{
				var chk_ary = document.getElementsByName('sel_emails');
				var email_ary='';
				console.log(chk_ary);
				for (var i=0;i<chk_ary.length;i++)
				{
					if (chk_ary[i].checked)
					{
						var str = chk_ary[i].value;
						var str_ary = str.split(';');
						var customer_email_ary = str_ary[1];
												
						if (customer_email_ary!="")
							email_ary += customer_email_ary+';';			
						
					}						
						
				}	
			
				
				document.getElementById("email_to").value = email_ary.substring(0,email_ary.length-1);		
				$("#dialog-email").modal();
			}
			function Select_all_records()
			{
				console.log("Selected all records");
				sel_stat = document.getElementById('selected_status_val').value;
				
				var chk_ary = document.getElementsByName('sel_emails');
				for (var i=0;i<chk_ary.length;i++)
				{
					chk_ary[i].checked = 1-sel_stat;							
				}
				document.getElementById('selected_status_val').value = 1-sel_stat;				
			}
			function deleteOpenedEmails()
	        {
	        	console.log("deleteOpenedEmails");
	        	var chk_ary = document.getElementsByName('sel_emails');
				var email_ary='';
				console.log(chk_ary);
				for (var i=0;i<chk_ary.length;i++)
				{  
					if (chk_ary[i].checked)
					{
						var str = chk_ary[i].value;
						var str_ary = str.split(';');
						var customer_email_ary = str_ary[0];												
						if (customer_email_ary!="")
							email_ary += customer_email_ary+';';
					}
				}	
				
				console.log(email_ary);
	        	var data = {"auto_no":email_ary.substring(0,email_ary.length-1)};
										
				//alert(data);
				$.ajax({
					type:"POST",
					dataType : "json",
					url : "deleteopened_emails.php",
					data : data,
					success : function(res){						
						console.log(res);
						if (res.status == 'Error')
							alert("delete is failed");
						else{
							alert("delete is successed");
							
                			document.frmOpendedEmail.submit();
						}
					},
					error:function(res)
					{
						console.log("fail");
						console.log(res);					
					}
				});	
	        }  
			
	    </script>
	</head>
	<body>
		<form name="frmOpendedEmail" method="post" action="showOpened_emails.php">
		<div class="container">
			<center><h1>Opened Emails</h1></center>
			<br>
			<div class="row">				
				<div class="col-xs-6 col-sm-4">
					<button type="button" class="btn btn-success btn-md" onclick="javascript:ClicktoReply();">Reply</button>
					<button type="button" class="btn btn-success btn-md" onclick="javascript:deleteOpenedEmails();">Delete</button>
					<button type="button" class="btn btn-success btn-md" onclick="javascript:Select_all_records()">SelectAll</button>
					<input type="hidden" name="selected_status_val" value="0" id="selected_status_val" />
				</div>								
			</div>
			<br>
	   		<div class="row">
	   			<div class="panel panel-primary">
					<div class="panel-heading"></div>
					<div class="panel-body">
			      		<div class="table-responsive" style="height:600px; overflow:auto; overflow-y:scroll;">    
			    			<table class="table table-striped">  
		    					<thead>
									<tr style="font-size:16px">
										<th class="col-sm-1">No</th>										
										<th class="col-sm-1">From</th>
										<th class="col-sm-1">To</th>										
										<th class="col-sm-1">Subject</th>										
										<th class="col-sm-4">Content</th>
										<th class="col-sm-2">Send Time</th>		
										<th class="col-sm-2">Opened Time</th>										
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
											<td class="col-sm-1"><input type="checkbox" id="sel_emails" name="sel_emails" value="<?php echo $response['auto_no'][$i].';'.$response['mail_rcvr'][$i]; ?>">&nbsp;&nbsp;&nbsp;<?php echo $i+1;?></td>
											
									        <td class="col-sm-1"><?php echo $response['from_address'][$i];?></td>
									     	<td class="col-sm-1"><?php echo $response['mail_rcvr'][$i];?></td>
									    	<td class="col-sm-1"><?php echo $response['mail_subject'][$i];?></td>
									        <td class="col-sm-4"><?php echo $response['mail_body'][$i];?></td>
									        <td class="col-sm-2"><?php echo $response['send_dt'][$i];?></td>
									        <td class="col-sm-2"><?php echo $response['opened_time'][$i];?></td>											        
									        
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
		<div id="dialog-email" class="modal fade" style="z-index:1000000000"  title="Send Email" style="display:none;">
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
