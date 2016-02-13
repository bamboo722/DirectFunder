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
$response['users'] = '';
if (is_ajax())
{
	
	// -- get email marketing user info --
	
	$sql_sel = "select eml_marketing_user_eml, eml_marketing_firstname,eml_marketing_lastname,eml_marketing_id,eml_marketing_id,eml_marketing_lastlogin, eml_marketing_username, eml_marketing_primaryuser  from admin_user where user_id='".$_SESSION['user_login']."'";
	$sql_res = mysql_query($sql_sel) or die(mysql_error());	
	$sql_result = mysql_fetch_assoc($sql_res);
	if (isset($sql_result))
	{	
		
		$eml_sal = $_POST["eml_sal"];
		$eml_subj = rawurlencode($_POST["eml_subj"]);
		$eml_body = $eml_sal.','."\n".$_POST["eml_body"];
		$eml_body = rawurlencode($eml_body);
				
		/*  Add Lists */
			
		// Uploads a list to the account given a url to a list.
		$list_id=0;
		$uploadlist_url = "http://51017a4f.ngrok.io/directfunder/agentc3/".$_POST['eml_addr'];
		//$uploadlist_url = "http://e19ef646.ngrok.io/directfunder/agentc3/tmp_eml_1455329364.txt";
		$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=uploadlistbyurl&url=".$uploadlist_url);
		$sxml = new SimpleXMLElement($result);				
		$list_id = $sxml->listid;
		
		/*  Add Message */
		
		$url = "http://www.softwarelogin.com/api.php?apikey=" . APIKEY . "&area=email&action=addmessage&title=".$eml_subj."&body=".$eml_body;
		$result = FetchUrl($url);
		$sxml = new SimpleXMLElement($result);				
		$message_id = $sxml->id;
		
		/* Create Campaign */
		$camp_title = sprintf("camp_%d",time());
		$camp_subj = $eml_subj;
		
		// Domain/IP Combniation used Profile field id's found either using getsendingprofiles api or inside the system Under Sending Profiles'
		// profile id. (required)
		$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=getsendingprofiles&start=0&limit=50");
		$sxml = new SimpleXMLElement($result);
		$profileID=0;
		foreach ($sxml->profile as $record)
		{
			// Output data to the screen
			if ($record->serverid==1728)	//directm.space
			{
				$profileID = $record->id;
				break;
			}				
		}
		
		// Whois ID used for email footer required http://softwarelogin.com/api/viewapi.php?id=16 (required)
		// in this case, the head of xml is missed. '<response>'
		$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . '&area=email&action=getcanspamwhoisinfo&start=0&limit=10');
		$result = "<response>".$result;
		$sxml = new SimpleXMLElement($result);
		$whoisid=0;
		foreach ($sxml->whois as $record)
		{
			// Output data to the screen
			$whoisid = $record->id;
			break;
		}
		
		// from address prefix without @ if it is info@test.com prefix would be info(required)
		$str_pos = strpos($sql_result['eml_marketing_user_eml'],'@');
		if ($str_pos === false) {
			$response['status']='Error';		
		}else
		{
			$fromaddress = substr($sql_result['eml_marketing_user_eml'],0,$str_pos);
			
			// From name usally a person's name or company name (required)
			$fromname = $sql_result['eml_marketing_username'];
		
			//get address list status
			while (1)
			{
				$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=getlistdetail&id=".$list_id);
				$sxml = new SimpleXMLElement($result);	
				if ($sxml->error)
					sleep(5);
				else
					break;
			}
			
						
			//Sends an email campaign
			$url = 'http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=createcampaign&listid=".$list_id."&title=".$camp_subj."&subject=".$camp_subj."&fromaddress=".$fromaddress."&fromname=".$fromname."&profileid=".$profileID."&messageid=".$message_id."&whoisid=".$whoisid;
			$result = FetchUrl($url);
			$sxml = new SimpleXMLElement($result);
			
			$response['camp_id'] = (int)($sxml->id);
    		
    		// Dispaly the response will be xml either success or the error mesage.
			$response['status'] = 'Success';				
			
			
			//$_SESSION['email_sent']++;			
		}
		//@unlink($_POST['eml_addr']); delete after campaign is finished
	}	
}


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