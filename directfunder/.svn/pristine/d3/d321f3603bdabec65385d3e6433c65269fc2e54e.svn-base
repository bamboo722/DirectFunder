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
$response['start_time'] = array();


$phone = $_SESSION['tw_number'];
$seeavail="select * from call_log_info where  (from_phone ='".$phone."') or (to_phone ='".$phone."') order by start_time desc";
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
      	
     if ($seerec['conv_time'] != '') 
      	$response['conv_time'][$row_id]=$seerec['conv_time'];
      else 
      	$response['conv_time'][$row_id]="&nbsp;";
      	
             
      if ($seerec['start_time'] != '') 
      	$response['start_time'][$row_id]=$seerec['start_time'];
      else 
      	$response['start_time'][$row_id]="&nbsp;";
      	
      if ($seerec['log_time'] != '') 
      	$response['log_time'][$row_id]=$seerec['log_time'];
      else 
      	$response['log_time'][$row_id]="&nbsp;";
      
       $response['call_flag'][$row_id]=(int)$seerec['call_flag'];
       
      $row_id++;
   }   
}

$seeavail = "update call_log_info set call_flag= 2 where call_flag=1";
$seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");

$phone = $_SESSION['tw_number'];
//Calls Made
$sql="select count(call_flag) as call_made from call_log_info where  from_phone like ('%".$phone. "%') and start_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['calls_made']=$res_rec['call_made'];

//Calls connect
$sql="select count(call_flag) as call_con from call_log_info where   TIME_TO_SEC(conv_time)>0 and  (from_phone like ('%".$phone. "%') or to_phone like ('%".$phone. "%')) and start_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['calls_con']=$res_rec['call_con'];

//Conversation minutes
$sql="select (SUM(TIME_TO_SEC(conv_time))) as call_time_secs from call_log_info where  (from_phone like ('%".$phone. "%') or to_phone like ('%".$phone. "%')) and start_time > '".$_SESSION['last_logout']."'";
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$call_time_secs = $res_rec['call_time_secs'];
				
$_SESSION['conv_minutes']=0;
if ($call_time_secs !=NULL)
{
	//if ($_SESSION['calls_made']>0)
		$_SESSION['conv_minutes']=ceil($call_time_secs/60);
}

$cur_time = time();
$duration_secs = $cur_time-$_SESSION['admin_login_time'];

if (($call_time_secs != NULL)&& ($call_time_secs!=0))
{
	//$_SESSION['ratio']=sprintf("%.1f",(float)($call_time_secs*100/$duration_secs));	
	$_SESSION['ratio']=sprintf("%.1f",(float)($_SESSION['conv_minutes']*100/((int)($duration_secs/60))));	
}else
	$_SESSION['ratio']=0;	

/* twilio */
$accountSid = $_SESSION['tw_account_sid'];
$authToken  = $_SESSION['tw_auth_token'];
$capability = new Services_Twilio_Capability($accountSid, $authToken);
$capability->allowClientOutgoing($_SESSION['tw_app_sid']);
$_SESSION['tw_token'] = $capability->generateToken();
					
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
 		
 		<!-- Twilio -->
 		<script type="text/javascript" src="//static.twilio.com/libs/twiliojs/1.2/twilio.min.js"></script>
 			
		<script type="text/javascript">
		 	/**
			* Twilio Call
			*/ 
			Twilio.Device.setup("<?php echo $_SESSION['tw_token']; ?>", {"debug":true});
		      
			Twilio.Device.ready(function (device) {
		        $("#log").text("Ready");
		    });

		    Twilio.Device.error(function (error) {
		        $("#log").text("Error: " + error.message);
		    });

			Twilio.Device.connect(function (conn) {
				$("#log").text("Successfully established call");
			});

			Twilio.Device.disconnect(function (conn) {
			      $("#log").text("Call ended");
			});
			function ClicktoPhone(ph_num)
			{
				console.log("ClicktoPhone");
				
        		document.getElementById("dialog_phone_number").innerHTML = ph_num;
        		$("#dialog_phone").modal();
			}
			
			function ClicktoCall(addr)
	        {
	        	console.log("ClicktoCall : " + addr);
	        	if (addr=="")
	        	{
					alert("Phone number is wrong!");
				}	        		
	        	else
	        	{		        		 		
	        		params = {"tocall": addr,"callerid":"<?php echo $_SESSION['tw_number']; ?>"}; 	        		
			  		Twilio.Device.connect(params);  
				}				
	        }              	        			
			function Hangup()
			{
				console.log("Hangup : " );
				$("#dialog_phone").modal('hide');   	
				Twilio.Device.disconnectAll();
			}
			Twilio.Device.incoming(function (connection) {
			     if (confirm('Accept incoming call from ' + connection.parameters.From + '?')){
			         connection.accept();
			     } else {
			         connection.reject();
			  }
			});
     		/**-----------------------------**/
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
	    </script> 
	</head>
	<body>
	<form name="frmSearch" method="post" action="getHistory_call.php">
		<div class="container">
			<h1>&nbsp;</h1>
	   		<div class="row">
	   			<div class="panel panel-primary">
					<div class="panel-heading"><center><h3>Call History</h3></center></div>
					<div class="panel-body">
						<div class="table-responsive" style="height:600px; overflow:auto; overflow-y:scroll;">    
		  					<table class="table table-striped">
		  						<thead>
		      						<tr style="font-size:16px">
										<th class="col-xs-offset-1 col-xs-1">No</th>
										<th class="col-xs-2">From</th>
										<th class="col-xs-2">To</th>
										<th class="col-xs-2">Conversation Time</th>													        
										<th class="col-xs-2">StartTime</th>		
										<th class="col-xs-1 col-xs-offset-1">Res</th>						
									</tr>      						
		      					</thead>
		      					<tbody>
		  						<?php 
							   	  for ($i=0;$i<count($response['row_id']);$i++)
							  	  {					  	  	
							  	   	 
			                         if ($response['start_time'][$i] > $_SESSION['last_logout']) {
										if ($response['call_flag'][$i] == 1) {
											$style="style='font-size:16px;min-height:50px;font-weight:bold;color:#0c00ea;'";
			                        	}else{
											$style="style='font-size:16px;min-height:50px;color:#0c00ea;'";	                                    
			                            }	
			                        }else
			                        	$style="style='font-size:16px;min-height:50px;'";		                        
								?>
								
									<tr <?php echo $style;?>>
										<td class="col-xs-offset-1 col-xs-1"><?php echo $response['row_id'][$i];?></td>
										<td class="col-xs-2"><a href="#" onclick="javascript:GotoRecord('<?php echo $response['from_phone'][$i];?>');"><?php echo $response['from_phone'][$i];?></a></td>
									    <td class="col-xs-2"><a href="#" onclick="javascript:GotoRecord('<?php echo $response['to_phone'][$i];?>');"><?php echo $response['to_phone'][$i];?></a></td>									    
										<td class="col-xs-2"><?php echo $response['conv_time'][$i];?></td>													        
										<td class="col-xs-2"><?php echo $response['start_time'][$i];?></td>		
										
										<td class="col-xs-1 col-xs-offset-1"><button type="button" onclick="javascript: ClicktoPhone('<?php echo $response['response_phone'][$i];?>');"  class="btn btn-success btn-xs" style="padding:1px">Call</button></td>
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
	   	<!-- click to phone (call,sms) modal dialog -->
        <div id="dialog_phone" class="modal fade" title="" style="display:none;">
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h2 class="modal-title"><center><label id='dialog_phone_number'></label></center></h2>				    	
			      	</div>
			      	<div class="modal-body">
			      		<div class="row">
			      			<div class="col-xs-6">
			      				<button type="submit" style="font-size:26px" onclick="javascript:ClicktoCall(document.getElementById('dialog_phone_number').innerHTML)" class="btn btn-default btn-primary btn-md btn-block"><!--<span class="glyphicon glyphicon-earphone"></span>-->&nbsp;Call</button>
			      			</div>
			      			<div class="col-xs-6">
			      				<button type="submit" style="font-size:26px"  onclick="javascript:Hangup();" class="btn btn-default btn-primary btn-md btn-block">Hangup</button>
			      			</div>			      			
			      		</div>			
			      		<div class="row">		
			      			<center>      			
  								<h3><label id="log">Ready</label></h3>
  							</center>
  						</div> 
			      	</div>			     
				</div>
			</div>
		</div>
		
		
	  </form>	
	</body>
</html>
