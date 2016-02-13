<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");

$response = array();
if (is_ajax()) 
{
$sql_ins = sprintf("delete from opportunity_info");
$res_sel = mysql_query($sql_ins) or die(mysql_error() . "go select error");

for ($i=0;$i<count($_POST['op_opportunity']); $i++)
{
	$sql_ins = sprintf("insert into opportunity (opportunity,yes_no,referal_company_name,referal_person_name,phone1,phone2,best_email,fee_amount,date_paid,notes) values ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$_POST['op_opportunity'][$i],$_POST['op_yes_no'][$i],$_POST['op_comp_nm_ary'][$i],$_POST['op_ref_comp_nm'][$i],$_POST['op_ph1'][$i],$_POST['op_ph2'][$i],$_POST['op_bst_eml'][$i],$_POST['op_fee_amt'][$i],$_POST['op_date_paid'][$i],$_POST['op_notes'][$i]);
	$res_sel =  mysql_query($sql_ins) or die(mysql_error() . "go select error");
}
$response['status'] = 'Success';					
}
echo json_encode($response);

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>