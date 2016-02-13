<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}
define("APIKEY",$_SESSION['eml_marketing_apikey']);

require_once("includes/dbconnect.php");


$response = array();
$response['status']='Error';
$response['sent']=0;
if (is_ajax())
{
		
	if (isset($_POST['camp_id']) and $_POST['camp_id'] !="")
	{	
	
		//Gets a detailed information from a campaign
		$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=getcampaigndetail&id=".$_POST['camp_id']);
		$sxml = new SimpleXMLElement($result);	
		foreach ($sxml->campaign as $record)
		{
			$response['status']=(string)$record->status;
			$response['sent']=(int)($record->sent);	
			break;
		}		
	}

}

/*$response['email_sent'] = $_SESSION['email_sent'];		
$response['email_recv'] = $_SESSION['email_recv'];*/
	
echo json_encode($response);

// Requires cURL extension installed and SimpleXML.
function FetchUrl($url = '',$postFields = '')
{
	if (function_exists('curl_init'))
    {
        
    	if(!$ch = curl_init())
    	{
    	    die("Could not init cURL session.\n");
    	}
    
    	curl_setopt($ch, CURLOPT_URL, $url);
    	//curl_setopt($ch, CURLOPT_POST, true);
    	
    	if (!empty($postFields))
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    	
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    	
    	$result = curl_exec($ch);
    	curl_close($ch);
     }
     else
     {
        die("cUrl not installed");
        //echo 'Cur'
        $result = file_get_contents($url);
        
     }
	
	return $result;
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>