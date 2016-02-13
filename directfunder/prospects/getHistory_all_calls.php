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

// Call, Email, Sms
$phone = '+1'.$_SESSION['google_voice_ph'];

// Call
if ($_SESSION['user_group'] == 'Admin')
	$sql_click_call = "SELECT * from call_log_info order by start_time desc";
else	
	$sql_click_call = "SELECT * from call_log_info where ((from_phone like ('".$phone."')) or (to_phone like ('".$phone."'))) order by start_time desc ";
$seeres = mysql_query($sql_click_call) or die(mysql_error());

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

$phone = '+1'.$_SESSION['google_voice_ph'];
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
		
	</head>
	<body>
	<form name="frmSearch" method="post" action="getHistory_all_calls.php">
		<div class="container">
			<h1>&nbsp;</h1>
			<div class="row">
	   			<div class="panel panel-primary">
					<div class="panel-heading"><center><h3>All Call History</h3></center></div>
					<div class="panel-body">
						<div class="table-responsive" style="height:600px; overflow:auto; overflow-y:scroll;">    
		  					<table class="table table-striped">
		  						<thead>
		      						<tr style="font-size:16px">
										<th class="col-xs-1">No</th>
										<th class="col-xs-2">From</th>
										<th class="col-xs-2">To</th>
										<th class="col-xs-2">Conversation Time</th>													        
										<th class="col-xs-2">StartTime</th>		
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
										<td class="col-xs-1"><?php echo $response['row_id'][$i];?></td>
										<td class="col-xs-2"><?php echo $response['from_phone'][$i];?></td>
										<td class="col-xs-2"><?php echo $response['to_phone'][$i];?></td>
										<td class="col-xs-2"><?php echo $response['conv_time'][$i];?></td>													        
										<td class="col-xs-2"><?php echo $response['start_time'][$i];?></td>										
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
	</body>
</html>
