<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");


$response = array();
$response['status'] = 'Error';

/*if (empty($_SESSION['geevee']))
{
	$geevee=new GeeVeeAPI($_SESSION['google_acc_nm'],$_SESSION['google_acc_pwd']); // create GeeVeeAPI Object for call and sms
	$_SESSION['geevee'] = serialize($geevee);	
}
$geevee = unserialize($_SESSION['geevee']);


$forward_phone='+1'.$_SESSION['skype_number'];

$response['status'] = 'Success';
$response['called_phone_number']='0000000000';

$sql_sel = sprintf("select customer_id,p_fl_nm,ph_1,ph_2,duration,is_called,called_number from auto_dial_info where user_id='%s' and is_called<'%d' and duration='%d' limit 1",$_SESSION['user_login'],6,0);
$res_sel = mysql_query($sql_sel) or die(mysql_error() . "11");
$num_rows = mysql_num_rows($res_sel);
if ($num_rows==0)
{
	$sql_upd = sprintf("update auto_dial_info set is_called='%d' where user_id='%s' and duration='%d'",0,$_SESSION['user_login'],0);
	mysql_query($sql_upd) or die(mysql_error());			
	
	$sql_sel = sprintf("select customer_id,p_fl_nm,ph_1,ph_2,p2_ph1,p2_ph2,p3_ph1,p3_ph2,duration,is_called,called_number from auto_dial_info where user_id='%s' and is_called<'%d'",$_SESSION['user_login'],6);
	$res_sel = mysql_query($sql_sel) or die(mysql_error() . "11");
}
$res_rec = mysql_fetch_assoc($res_sel);
switch((int)($res_rec['is_called'])){
	case 0:
		$to_pohone=$res_rec['ph_1'];
		break;
	case 1:
		$to_pohone=$res_rec['ph_2'];
		break;
	case 2:
		$to_pohone=$res_rec['p2_ph1'];
		break;
	case 3:
		$to_pohone=$res_rec['p2_ph2'];
		break;
	case 4:
		$to_pohone=$res_rec['p3_ph1'];
		break;
	case 5:
		$to_pohone=$res_rec['p3_ph2'];
		break;	
	default:
		$to_pohone='';
		break;
}

if (isset($res_rec) and $res_rec['called_number'] < 3)
{	
	if (isset($to_pohone) and $to_pohone != '')
	{
		
		$dur = $res_rec['duration'];
		$is_called =$res_rec['is_called'];
		$called_number = $res_rec['called_number'];
		
		if ($is_called == 0)
		{
			$res = $geevee->callNumber($to_phone,$forward_phone);
			sleep(2);
			$is_called++;		
			if ($is_called == 6)
				$called_number++;				
		}
		
		$all = $geevee->getAllMessages();
		if (isset($all))
		{
			if (isset($all['conversations_response']) && isset($all['conversations_response']['conversationgroup']))	
			{
				$dur = (int)($all['conversations_response']['conversationgroup'][0]['call']['duration']);
				if ($dur>0)
				{
					$response['called_phone_number'] = $to_phone;
					//break;
				}else
				{
					if ($is_called != 1)
					{
						$res = $geevee->callNumber($to_phone,$forward_phone);	
						sleep(2);
						$is_called++;		
						if ($is_called == 6)
							$called_number++;		
					}					
				}					
			}
		}	
		
		
		$response['customer_id'] = $res_rec['customer_id'];
		$response['calling_phone'] = $to_pohone;
		$response['p_fl_nm'] = $res_rec['p_fl_nm'];
		
		// update auto_dial_info table. so update phone number in auto dial list 
	    $sql_upd = sprintf("update auto_dial_info set duration='%d',is_called='%d',called_number='%d' where customer_id='%s'",$dur,$is_called,$called_number,$res_rec['customer_id']);
		mysql_query($sql_upd) or die(mysql_error());
		
		
	}else{
		$response['calling_phone'] = null;		
		$is_called++;		
		if ($is_called == 6)
			$called_number++;
		$sql_upd = sprintf("update auto_dial_info set duration='%d',is_called='%d',called_number='%d' where customer_id='%s'",0,$is_called,$called_number,$res_rec['customer_id']);
		mysql_query($sql_upd) or die(mysql_error());
	}				
}*/
$response['status'] = 'Success';

	
echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>