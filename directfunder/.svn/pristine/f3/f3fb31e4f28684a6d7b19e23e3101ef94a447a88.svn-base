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


$phone = '+1'.$_SESSION['google_voice_ph'];

if ($_SESSION['user_group'] == 'Admin')
	$seeavail = sprintf("SELECT * from call_log_info where (log_time >= '%s' and log_time <= '%s')",$_SESSION['last_real_logout'],$_SESSION['logout_call_get_time']);		
else
	$seeavail = sprintf("SELECT * from call_log_info where ((from_phone like '%s') or (to_phone like '%s')) and (log_time >= '%s' and log_time <= '%s')",$phone,$phone,$_SESSION['last_real_logout'],$_SESSION['logout_call_get_time']);

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
		 	function ClicktoCall(addr)
	        {
	        	console.log("ClicktoCall : " + addr);
	        	addr = addr.substring(2,addr.length);
	        	console.log("ClicktoCall : " + addr);
	        	if (addr=="")
	        	{
					alert("Phone number is wrong!");
				}	        		
	        	else
	        	{
	        		document.getElementById("connecting_number").innerHTML = addr;
					$("#dialog_connecting").modal();   	
					
					$("#dialog_phone").modal('hide');   	
	        	
		            var data = {"call_to":addr};
					console.log(data);
					
					//alert(data);
					$.ajax({
						type:"POST",
						dataType : "json",
						url : "clicktocall.php",
						data : data,
						success : function(res){						
							console.log(res);
							if (res.status == 'Success')
							{
								setTimeout(function(){$("#dialog_connecting").modal('hide');},5*1000);
            					
								//alert("Phone call is calls_div");
							}							
							else if (res.status == 'Error')
								alert("Phone call is failed");
							
						},
						error:function(res)
						{
							console.log("fail");
							console.log(res);
							
						}
					});			
				}
				
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
							  	    
								?>
								
									<tr style='font-size:16px;'>
										<td class="col-xs-offset-1 col-xs-1"><?php echo $response['row_id'][$i];?></td>
										<td class="col-xs-2"><?php echo $response['from_phone'][$i];?></td>
										<td class="col-xs-2"><?php echo $response['to_phone'][$i];?></td>
										<td class="col-xs-2"><?php echo $response['conv_time'][$i];?></td>													        
										<td class="col-xs-2"><?php echo $response['start_time'][$i];?></td>		
										
										<td class="col-xs-1 col-xs-offset-1"><button type="button" onclick="javascript: ClicktoCall('<?php echo $response['response_phone'][$i];?>');"  class="btn btn-success btn-xs" style="padding:1px">Call</button></td>						
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
	   		<!-- connecting dialog -->
        <div id="dialog_connecting" class="modal fade" title="" style="display:none;">
        	<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Calling <label id='connecting_number'></label> ...</h4>				        
			      	</div>
			      	<div class="modal-body">
			      		<input type="hidden" name="connecting_number" id="connecting_number"/>
			      		<div>		      			
  							<h5 style="padding-left:5px">Please accept the incoming call to connect. It will take 15 seconds.</h5>
  						</div>	        
			      	</div>			     
				</div>
			</div>
		</div>
	  </form>	
	</body>
</html>
