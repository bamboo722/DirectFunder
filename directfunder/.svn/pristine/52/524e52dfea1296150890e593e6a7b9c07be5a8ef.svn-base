<?php 
@session_start();
//ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");

$response = array();
$response['status'] = 'Error';
if (isset($_POST['title']) and isset($_POST['content']))
{
	if (!(trim($_POST['title'])=="" and trim($_POST['content'])==""))
	{
		mysql_query("set time_zone='-7:00';");	
		$_title = addslashes($_POST['title']);
		$_content = addslashes($_POST['content']);
		$sql_ins = sprintf('insert into instruction_info (title,content,added_time,added_by) values ("%s","%s",sysdate(),"%s");',$_title,$_content,$_SESSION['user_login']);
		mysql_query($sql_ins) or die(mysql_error() . "go select error");
	}
	
	
	$response['status'] = 'Success';
}
echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>
