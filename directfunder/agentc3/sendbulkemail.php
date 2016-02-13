<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

if (isset($_GET['file']) and $_GET['file'] != "")
{
	$eml_sal = $_GET["sal"];
	$eml_body = $_GET["body"];
	$eml_subj = $_GET["subj"];
	$eml_cnt = $_GET["to_cnt"];	
	$eml_addr = $_GET['file'];
}
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
		
		<!-- Twilio -->
		<script type="text/javascript" src="//static.twilio.com/libs/twiliojs/1.2/twilio.min.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function () {
           		var start_ind = 0;
           		var total_cnt = 0;
				
				SendBulkEmail();
				
				/* prepare for send bulk email : add list, add message, create campaign */	       	
				function SendBulkEmail() {
				   	console.log("[SendBulkEmail]");
				   
					var data = {
						   	"eml_body":document.getElementById("eml_body").value,
						   	"eml_subj":document.getElementById("eml_subj").value,
						   	"eml_sal":document.getElementById("eml_sal").value,
						   	"eml_addr":document.getElementById("eml_addr").value
						   	};
						   	
					$.ajax({
						type:"POST",
						dataType : "json",
						data : data,
						url : "sendrealbulkemail.php",						
						success : function(res){
							console.log(res);
							if (res.status == "Success")
							{
								/* set progress bar */
								
								document.getElementById("progress_status").innerHTML = "Prepare Sending Bulk Email is completed! Sending Bulk Email is started!";
								document.getElementById("camp_id").value = res.camp_id;
								
								document.getElementById("progress_value").style.width = 10+"%";
								document.getElementById("progress_value").innerHTML = 10+"%";
								
								GetBulkEmailStatus();
							}				
							
						},
						error:function(res)
						{
							console.log('[fail]');
							console.log(res);
													
						}
				   	});				   				   
				}
				
				/* Get Bulk Email Status */	       	
				function GetBulkEmailStatus() {
				   	console.log("[GetBulkEmailStatus]");
				    total_cnt = document.getElementById("total_cnt").value;
					var data = {"camp_id":document.getElementById("camp_id").value};
						   	
					$.ajax({
						type:"POST",
						dataType : "json",
						data : data,
						url : "getbulkemailstatus.php",						
						success : function(res){
							console.log('[success]');
							console.log(res);
							console.log(total_cnt);
							console.log(res.sent);
							
							cur_percent = res.sent*100/total_cnt;
							cur_percent = cur_percent.toFixed(2);
							console.log(cur_percent);
							
							if (cur_percent<10)
								cur_percent=10;
							document.getElementById("progress_value").style.width = cur_percent+"%";
							document.getElementById("progress_value").innerHTML = cur_percent+"%";
							document.getElementById("progress_status").innerHTML = "Sending Bulk Email is in progress...";
							if (res.status == "Completed")
							{
								document.getElementById("progress_status").innerHTML = "Sending Bulk Email is completed!";
								
								document.getElementById("progress_value").style.width = 100+"%";
								document.getElementById("progress_value").innerHTML = 100+"%";
								alert("Sending Bulk Email is completed!");
								
							}else
							{
								setTimeout(GetBulkEmailStatus,1000*5);
							}
						},
						error:function(res)
						{
							console.log('[fail]');
							console.log(res);													
						}
				   	});				   				   
				}
						
			});
		</script>
	</head>
	<body>
		<div class="container" style="margin:1px;width:99%">
			<input type="hidden" name="total_cnt" value="<?php echo $eml_cnt; ?>" id="total_cnt" />
			<input type="hidden" name="eml_body" value="<?php echo $eml_body; ?>" id="eml_body" />
			<input type="hidden" name="eml_addr" value="<?php echo $eml_addr; ?>" id="eml_addr" />
			<input type="hidden" name="eml_sal" value="<?php echo $eml_sal; ?>" id="eml_sal" />
			<input type="hidden" name="eml_subj" value="<?php echo $eml_subj; ?>" id="eml_subj" />
			<input type="hidden" name="camp_id" id="camp_id" />
			<div class="row" style="margin:10px">	
				<center>
					<h1>Send Bulk Email</h1>			
				</center>				
			</div>			
			<div class="row" style="margin:10px">	
				<center>
					<h4 id="progress_status">Sending Bulk Email is in progress ...</h4>
					<h4 id="sent_eml_div"></h4>			
				</center>				
			</div>		
			<div class="row" style="margin:10px">			
				<div class="progress">
				    <div class="progress-bar" id="progress_value">0%</div>
		    	</div>
			</div>	
		</div>	
	</body>
</html>