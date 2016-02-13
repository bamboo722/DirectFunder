<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

$response = array();
$response['status']='Fail';
if (is_ajax()) 
{
	switch($_POST['stat_name']){
		case 'New':
			$_SESSION['newLeads'] = $_POST['stat_val'];
			break;
		case 'Opened Emails':
			$_SESSION['opened_emails'] = $_POST['stat_val'];
			break;
		case 'Clickthroughs':
			$_SESSION['clickthroughs'] = $_POST['stat_val'];
			break;
		case 'Retry':
			$_SESSION['retryLeads'] = $_POST['stat_val'];
			break;
		case 'hdnTodaysFolloups':
			$_SESSION['followUpCount'] = $_POST['stat_val'];
			break;
		case 'hdnSevenDayOverdue':
			$_SESSION['past_due'] = $_POST['stat_val'];
			break;
		case 'no_follow_up_date':
			$_SESSION['no_follow_up_date'] = $_POST['stat_val'];
			break;
			
		case 'hdnThirtyDayOverdue':
			$_SESSION['delinquent'] = $_POST['stat_val'];
			break;
		case 'Warm':
			$_SESSION['warmLeads'] = $_POST['stat_val'];
			break;
		case 'Hot':
			$_SESSION['hotLeads'] = $_POST['stat_val'];
			break;
		case 'Credit Check':
			$_SESSION['credit_checks'] = $_POST['stat_val'];
			break;
		case 'Credit Repair':
			$_SESSION['credit_repairs'] = $_POST['stat_val'];
			break;
		case 'Credit Ready':
			$_SESSION['credit_ready'] = $_POST['stat_val'];
			break;
		case 'Pre-approved':
			$_SESSION['pre_approveds'] = $_POST['stat_val'];
			break;
		case 'Doc. Sent':
			$_SESSION['doc_sents'] = $_POST['stat_val'];
			break;
		case 'Pending Funding':
			$_SESSION['pending_fundings'] = $_POST['stat_val'];
			break;
		case 'Funded':
			$_SESSION['fundedLeads'] = $_POST['stat_val'];
			break;
		case 'Fee Pending':
			$_SESSION['fee_pending'] = $_POST['stat_val'];
			break;
		case 'hdnBuyingTimeThirty':
			$_SESSION['thirty_day_funding'] = $_POST['stat_val'];
			break;
		case 'hdnBuyingTimeSixty':
			$_SESSION['sixty_day_funding'] = $_POST['stat_val'];
			break;
		case 'hdnBuyingTimeNinety':
			$_SESSION['sixty_ninety_day_fundings'] = $_POST['stat_val'];
			break;
		case 'Clients':
			$_SESSION['clients'] = $_POST['stat_val'];
			break;
		case 'is_opportunity':
			$_SESSION['other_opportunity'] = $_POST['stat_val'];
			break;		
		case 'call':
			$_SESSION['call'] = $_POST['stat_val'];
			
			break;		
		case 'sms':
			$_SESSION['sms'] = $_POST['stat_val'];
			break;		
		case 'email':
			$_SESSION['email'] = $_POST['stat_val'];
			break;		
		default:
			break;
	}
	$response['status']='Success';
}
echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>