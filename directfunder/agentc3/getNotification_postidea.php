<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
$response = array();
$response['user_name']=array();
$response['user_group']=array();
$response['post_time']=array();
$response['post_content']=array();
$response['post_ideas']="";
$response['status'] = "";
//$seeavail = sprintf("select * from post_ideas where (user_group='Amdin' or (user_group='Manager' and user_name='%s')) and (post_time > '%s')",$_SESSION['user_owner'],$_SESSION['last_post_time']);
//$seeavail = sprintf("select * from post_ideas where (post_time > '%s')",$_SESSION['last_post_time']);
$seeavail = sprintf("select * from post_ideas where (user_group='Admin') and (post_time > '%s')",$_SESSION['last_post_time']);
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
    
      if ($seerec['post_time'] != '') 
      	$response['post_time'][$row_id]=$seerec['post_time'];
      else 
      	$response['post_time'][$row_id]="&nbsp;";
         
      if ($seerec['post_content'] != '') 
      	$response['post_content'][$row_id]=$seerec['post_content'];
      else 
      	$response['post_content'][$row_id]="&nbsp;";
      $response['post_ideas'] .= $response['row_id'][$row_id] ." ".$response['user_name'][$row_id]." ".$response['user_group'][$row_id]." ".$response['post_time'][$row_id];
      $response['post_ideas'] .= "      ".$response['post_content'][$row_id]."\n";
      $row_id++;
   }   
}
/*** 
** Update Last view post time
**/
/*$sql="select sysdate() as cur_time;";  
$res=mysql_query($sql) or die(mysql_error()."11");
$res_rec=mysql_fetch_assoc($res);
$_SESSION['last_post_time']=$res_rec['cur_time'];*/
					

if ($response['status'] == "" )
	$response['status'] = 'Success';	
else	
	$response['status'] = 'Error';	
echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>