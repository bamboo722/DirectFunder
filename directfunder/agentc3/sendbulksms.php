<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

if (isset($_GET['file']) and $_GET['file'] != "")
{
	$sms_sal = $_GET["sal"];
	$sms_body = $_GET["body"];
	$sms_cnt = $_GET["to_cnt"];	
	$sms_addr = $_GET['file'];
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
				
				send5SMS();
				
				/* send sms to 5 addresses */	       	
				function send5SMS() {
				   console.log("[send5SMS]");
				   total_cnt = document.getElementById("total_cnt").value;
				   if (start_ind<total_cnt)
				   {
						var data = {"start_ind":start_ind,
						   	"sms_body":document.getElementById("sms_body").value,
						   	"sms_sal":document.getElementById("sms_sal").value,
						   	"sms_addr":document.getElementById("sms_addr").value
						   	};
						   	
						$.ajax({
							type:"POST",
							dataType : "json",
							data : data,
							url : "send5sms.php",						
							success : function(res){
								console.log(res);
								console.log(start_ind);
								console.log(total_cnt);
								//alert(res.status);
								if (res.status == "Success")
								{
									/* set progress bar */
									
									if (start_ind+5 >= total_cnt)
										cur_percent = 100;
									else
										cur_percent = (start_ind+5)*100/total_cnt;
									cur_percent = cur_percent.toFixed(2);
									console.log(cur_percent);
									document.getElementById("progress_value").style.width = cur_percent+"%";
									document.getElementById("progress_value").innerHTML = cur_percent+"%";
									
								}
								start_ind+=5;
								if (start_ind>=total_cnt)
								{
									document.getElementById("progress_status").innerHTML = "Sending Bulk SMS is completed!";
									alert("Sending Bulk SMS is completed!");
									
								}else
								{
									setTimeout(send5SMS,1000*10);	
								}
							},
							error:function(res)
							{
								console.log('[fail]');
								console.log(res);
														
							}
					   });		
				   }				   
				}					
			});
		</script>
	</head>
	<body>
		<div class="container" style="margin:1px;width:99%">
			<input type="hidden" name="total_cnt" value="<?php echo $sms_cnt; ?>" id="total_cnt" />
			<input type="hidden" name="sms_body" value="<?php echo $sms_body; ?>" id="sms_body" />
			<input type="hidden" name="sms_addr" value="<?php echo $sms_addr; ?>" id="sms_addr" />
			<input type="hidden" name="sms_sal" value="<?php echo $sms_sal; ?>" id="sms_sal" />
			
			<div class="row" style="margin:10px">	
				<center>
					<h1>Send Bulk SMS</h1>			
				</center>				
			</div>			
			<div class="row" style="margin:10px">	
				<center>
					<h4 id="progress_status">Sending Bulk SMS is in progress ...</h4>
					<h4 id="sent_sms_div"></h4>			
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