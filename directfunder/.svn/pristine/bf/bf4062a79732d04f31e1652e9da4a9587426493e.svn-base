<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");

$response = array();


$response['row_id'] = array();
$response['from_phone'] = array();
$response['to_phone'] = array();
$response['response_phone'] = array();
$response['content'] = array();
$response['sms_time'] = array();


$phone = $_SESSION['tw_number'];
$seeavail="select * from sms_log_info where  (from_phone ='".$phone."') or (to_phone ='".$phone."') order by sms_time desc";
$seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");
$row_id=0;
if (mysql_num_rows($seeres) == '0'){	

}else 
{
   while ($seerec = mysql_fetch_assoc($seeres)) {
      $response['row_id'][$row_id]=	$row_id+1;
    
      if ($seerec['from_phone'] != '') 
      	$response['from_phone'][$row_id]=$seerec['from_phone'];
      else 
      	$response['from_phone'][$row_id]="&nbsp;";
      	
      if ($seerec['to_phone'] != '') 
      	$response['to_phone'][$row_id]=$seerec['to_phone'];
      else 
      	$response['to_phone'][$row_id]="&nbsp;";
    
      if ($seerec['to_phone'] != $phone) 
      	$response['response_phone'][$row_id]=$seerec['to_phone'];
      else 
      	$response['response_phone'][$row_id]=$seerec['from_phone'];
      	
     if ($seerec['content'] != '') 
      	$response['content'][$row_id]=$seerec['content'];
      else 
      	$response['content'][$row_id]="&nbsp;";
      	
             
      if ($seerec['sms_time'] != '') 
      	$response['sms_time'][$row_id]=$seerec['sms_time'];
      else 
      	$response['sms_time'][$row_id]="&nbsp;";
      	
      if ($seerec['log_time'] != '') 
      	$response['log_time'][$row_id]=$seerec['log_time'];
      else 
      	$response['log_time'][$row_id]="&nbsp;";
      
       $response['sms_flag'][$row_id]=(int)$seerec['sms_flag'];
       
      $row_id++;
   }   
}

$seeavail = "update sms_log_info set sms_flag= 2 where sms_flag=1";
$seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");

//SMS Sent
$sql="select count(sms_flag) as sms_sent from sms_log_info where  from_phone ='".$phone."' and log_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_sent']=$res_rec['sms_sent'];

//SMS Receive
$sql="select count(sms_flag) as sms_recv from sms_log_info where  to_phone ='".$phone."' and log_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error() . "go select error");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['sms_recv']=$res_rec['sms_recv'];

?>

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
	     	
			var click_sms_dialog;
			$(document).ready(function () {
				/* SMS Character limit*/
				$('#characterLeft').text('160 characters left');
				$('#msg_body').keyup(function () {
			        var max = 160;
			        var len = $(this).val().length;
			        if (len >= max) {
			            $('#characterLeft').text('You have reached the limit');
			          
			            $('#btnSMSSubmit').addClass('disabled');            
			        } 
			        else {
			            var ch = max - len;
			            $('#characterLeft').text(ch + ' characters left');
			            $('#btnSMSSubmit').removeClass('disabled');
			          
			        }
			    });    				
		    });
		    
			/* Go to customer page which has this phone number */
			function GotoRecord(phone)
       		{
       			console.log("GotoRecord");      
       			
       			var phone_number;
       			phone_number = phone.substring(2,phone.length);
       			console.log(phone_number);      			
				
				var data = {"phone":phone_number};
				console.log(data);
       			
			    $.ajax({
			        url: 'getcustomerid.php',
			        data: data,
			        type:"POST",
					dataType : "json",
			        success: function ( res ) {
			        	console.log("Success");
			        	console.log(res);
			        	if (res.status == "Success")
			        	{
			        		if (res.customer_id != "")
			        		{
			        			console.log(res.customer_id);
			        			window.open("buyerinfo2.php?rid="+res.customer_id);
			        			//window.location.href="buyerinfo2.php?rid="+res.customer_id;
							}							
						}							
			        },
			        error: function(res) {
			        	console.log("error");
						console.log(res);
					}
			    });				 
				
			}
			
			/* SMS Preview */
			function previewSMS()
       		{
       			console.log("PreviewSMS");      			
			
				var data = {
					"sms_to":document.getElementById('phone_numbers').value,
					"sms_sal":document.getElementById('msg_sal').value,
					"sms_body":document.getElementById('msg_body').value						
				};
				console.log(data);
       			
			    $.ajax({
			        url: 'previewSMS.php',
			        data: data,
			        type:"POST",
					dataType : "json",
			        success: function ( res ) {
			        	console.log("Success");
			        	console.log(res);
			        	if (res.status == "Success")
			        	{
			        		if (res.sms_body != "")
			        		{
			        			console.log(res.sms_body);
								document.getElementById("sms_preview_div").innerHTML =res.sms_body;								
          						$('#dialog_preview_sms').modal();            					
							}							
						}							
			        }
			    });				   
			}
			  
       		function sendSMS(){
			   	var valid = true;
			//	valid = valid && checkRegexp(sms_to, /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/, "Phone address only allow : 0-9" );
			
 				if (valid){
					var data = {
						"sms_to":document.getElementById('phone_numbers').value,
						"sms_sal":document.getElementById('msg_sal').value,
						"sms_body":document.getElementById('msg_body').value						
					};
					console.log(data);
					
					//alert(data);
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "sendsms.php",
						data : data,
						success : function(res){
							console.log(res);
							//document.getElementById("sms_div").innerHTML = res.sms_sent+"/"+res.sms_recv;	
							if (res.status == 'Success')
								alert("Send SMS Success!");								
							else if (res.status == 'Error')
								alert("Send SMS Error");
							
						},
						error:function(res)
						{
							console.log("fail" + res);
						}
					});
					$("#dialog_sms").modal('hide');		
					$('#dialog_preview_sms').modal('hide');      			
				}
				return valid;					
		   }
		   
		   
			function ClicktoSMS(addr)
            {
            	console.log("ClicktoSMS");
        		document.getElementById("phone_numbers").value = addr;
        		
            	$("#dialog_sms").modal();					
			}
	    </script>
	</head>
	<body>
		
		<form name="frmSearch" method="post" action="getHistory_sms.php">
			<div class="container">
				<h1>&nbsp;</h1>
		   		<div class="row">
		   			<div class="panel panel-primary">
				    	<div class="panel-heading"><center><h3>Text Log</h3></center></div>
				      	<div class="panel-body">
				      		<div class="table-responsive" style="height:600px; overflow:auto; overflow-y:scroll;">         
				    			<table class="table table-striped" >  
			    					<thead>
										<tr style="font-size:16px">
									        <th class="col-xs-1">No</th>
									        <th class="col-xs-2">From</th>
									        <th class="col-xs-2">To</th>
									        <th class="col-xs-4">Content</th>
									        <th class="col-xs-2">Time</th>
									        <th class="col-xs-1">Res</th>
									    </tr>
								    </thead>
									<tbody>
										
									</tbody>	
									<?php 
									   	  for ($i=0;$i<count($response['row_id']);$i++)
									  	  {					  	  	
									  	   	 if ($response['log_time'][$i] > $_SESSION['last_logout']) {
												if ($response['sms_flag'][$i] == 1) {
													$style="style='font-size:16px;min-height:50px;font-weight:bold;color:#0c00ea;'";
				                            	}else{
													$style="style='font-size:16px;min-height:50px;color:#0c00ea;'";	                                    
				                                }	
				                            }else
				                            	$style="style='font-size:16px;min-height:50px;'";
									?>							
			    						<tr <?php echo $style;?>>
                                        	<td class="col-xs-1"><?php echo $i+1;?></td>                                        	
									        <td class="col-xs-2"><a href="#" onclick="javascript:GotoRecord('<?php echo $response['from_phone'][$i];?>');"><?php echo $response['from_phone'][$i];?></a></td>
									        <td class="col-xs-2"><a href="#" onclick="javascript:GotoRecord('<?php echo $response['to_phone'][$i];?>');"><?php echo $response['to_phone'][$i];?></a></td>
									        <td class="col-xs-4"><?php echo $response['content'][$i];?></td>
									        <td class="col-xs-2"><?php echo $response['sms_time'][$i];?></td>
									        
									        <td class="col-xs-1"><button type="button" onclick="javascript: ClicktoSMS('<?php echo $response['response_phone'][$i];?>');"  class="btn btn-success btn-xs" style="padding:1px">Sms</button></td>
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
		
		<!-- sms send modal dialog -->
		<div id="dialog_sms" class="modal fade" title="Send SMS"  style="z-index:1000000002;display:none;">
        	<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Send SMS</h4>
			      	</div>
			      	<div class="modal-body">
			      		<div class="form-group">
				        	<label for="phone_numbers"><span class="glyphicon glyphicon-user"></span>Enter numbers</label>
				        	<input type="text" name="phone_numbers" id="phone_numbers" class="form-control" placeholder="Enter Phone Numbers"/>				            
				        </div>
				        <div class="form-group">
				          	<label for="msg_body">Message</label>
				           	<textarea id="msg_body" name="msg_body" rows="7"  maxlength="160" class="form-control"></textarea>	
				           		<span class="help-block"><p id="characterLeft" class="help-block ">You have reached the limit</p></span>     			    
				        </div>			
			      	</div>
					<div class="modal-footer">
						<div class="col-xs-6">
			      			<button type="button" id="btnSMSSubmit"   name="btnSMSSubmit" onclick="javascript:sendSMS()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>			        	 
			      		</div>
			      		<div class="col-xs-6">
			      			<button type="submit" onclick="javascript:previewSMS()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Preview</button>			      		
			      		</div>	
					</div>
				</div>
			</div>	
		</div>
		<!-- SMS preview dialog -->
        <div id="dialog_preview_sms" class="modal fade" style="z-index:1000000003;display:none;" title="" >
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Preview SMS</h4>				        
			      	</div>
			      	<div class="modal-body" style="overflow:auto" id="sms_preview_div">
			      		
			      	</div>	
			      	<div class="modal-footer">
			      		<center>			      			
			      			<div class="col-xs-offset-4 col-xs-4">
			      				<button type="submit" onclick="javascript:sendSMS()" class="btn btn-default btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Send</button>
			      			</div>			      							      		
			      		</center>			      		
			      	</div>				     
				</div>
			</div>
		</div>
	
	
	</body>
</html>
