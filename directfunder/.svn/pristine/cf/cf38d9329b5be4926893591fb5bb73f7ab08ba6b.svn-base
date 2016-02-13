<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
$response = array();
$response['sms_news']=0;
$response['user_name'] = array();
$response['user_group'] = array();
$response['post_time'] = array();
$response['post_content'] = array();

$seeavail = "select user_name,user_group,post_content,post_time from post_ideas order by post_time desc";
$seeres = mysql_query($seeavail) or die(mysql_error() . "go select error");
$row_id=0;
if (mysql_num_rows($seeres) == '0'){	

}else 
{
   while ($seerec = mysql_fetch_assoc($seeres)) {
      $response['row_id'][$row_id]=	$row_id+1;
    
      if ($seerec['user_name'] != '') 
      	$response['user_name'][$row_id]=$seerec['user_name'];
      else 
      	$response['user_name'][$row_id]="&nbsp;";
      	
      if ($seerec['user_group'] != '') 
      	$response['user_group'][$row_id]=$seerec['user_group'];
      else 
      	$response['user_group'][$row_id]="&nbsp;";
    
      $post_content = $seerec['post_content'];
      if ($post_content != '') 
      	$response['post_content'][$row_id]= $post_content;
      else 
         $response['post_content'][$row_id]="&nbsp;";
         
      if ($seerec['post_time'] != '') 
      	$response['post_time'][$row_id]=$seerec['post_time'];
      else 
      	$response['post_time'][$row_id]="&nbsp;";
      
      $row_id++;
   }   
}

/*** 
** Update Last view post time
**/
mysql_query("set time_zone='-7:00';");
$sql="select sysdate() as cur_time;";  
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['last_post_time']=$res_rec['cur_time'];
 

?>
<html>
	<head>
		<title>Sales Lead DB</title>
        <meta charset="utf-8">
	    <!--meta http-equiv="X-UA-Compatible" content="charset=utf-8; IE=edge"-->
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
    	<link href="css/demo.min.css" rel="stylesheet" type="text/css" media="all"/>
    	
        <!-- for google hangout button -->
        <link rel="canonical" href="http://www.example.com" />        
		<script src="https://apis.google.com/js/platform.js" async defer></script>
        <!------------------------------->
       
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- jQuery (necessary for Right Navigation Bar) -->
  		<script src="js/jquery.js" type="text/javascript"></script>  		
  
	<head>
	<body>
		<div class="container">
			<br>
	   		<div class="row">
	   			<div class="panel panel-primary">
			    	<div class="panel-heading"><center>Posted Ideas</center></div>
			      	<div class="panel-body">
			      		<div class="table-responsive" style="height:600px; overflow:auto; overflow-y:scroll;">         
			    			<table class="table table-striped" >  
		    					<thead>
									<tr style="font-size:13px">
								        <th class="col-xs-1">No</th>
								        <th class="col-xs-2">User</th>
								        <th class="col-xs-2">Group</th>
								        <th class="col-xs-5">Post Idea</th>
								        <th class="col-xs-2">Time</th>								        
								    </tr>
							    </thead>
								<tbody>
									
								</tbody>	
								<?php 
								  	for ($i=0;$i<count($response['row_id']);$i++)
							  	  	{
									?>							
			    						<tr style="font-size:12px;">
	                                    	<td class="col-xs-1"><?php echo $i+1;?></td>
									        <td class="col-xs-2"><?php echo $response['user_name'][$i];?></td>
									        <td class="col-xs-2"><?php echo $response['user_group'][$i];?></td>
									        <td class="col-xs-5"><?php echo $response['post_content'][$i];?></td>
									        <td class="col-xs-2"><?php echo $response['post_time'][$i];?></td>									        
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
	</body>
</html>



