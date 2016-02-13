<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

// Update with your api key found in the API tab under get api key
define("APIKEY","903c5d283b735d99fc2f43ee34212e86ebf333e0y");

require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");



$response = array();
$response['status']='Error';
$response['users'] = '';
if (is_ajax())
{
	
	// -- get email marketing user info --
	
	$sql_sel = "select eml_marketing_user_eml, eml_marketing_fristname,eml_marketing_lastname,eml_marketing_id,eml_marketing_id,eml_marketing_lastlogin, eml_marketing_username, eml_marketing_primaryuser  from admin_user where user_id='".$_SESSION['user_login']."'";
	$sql_res = mysql_query($sql_sel) or die(mysql_error());	
	$sql_result = mysql_fetch_assoc($sql_res);
	if (isset($sql_result))
	{	
		
		
		if (isset($_POST["email_to"]) && !empty($_POST["email_to"])) 
	 	{
	 		$max_id = 1;
			$is_opened=0;
			
			// -- Get max mail id from current database -- 
			$sql_sel = "select max(mail_id) mxid from mail_log_info";
			$res_sel = mysql_query($sql_sel) or die(mysql_error() . "11");
			$rec_sel = mysql_fetch_assoc($res_sel);
			if (isset($rec_sel))
				$max_id = $rec_sel['mxid'];
			
			// -- Email Template -- 
			$eml_white= 'default.png';
			$eml_attach_file_sub_path = 'userprofile/userfiles/email_attaches/';
			$eml_white_sub_path = 'userprofile/userfiles/email_white_labels/';

			// -- Email Template File upload --
			$Destination = 'userprofile/userfiles/email_attaches';
			$New_EmlTemlImageName="";
			if(!isset($_FILES['user_email_attach_file_name']) || !is_uploaded_file($_FILES['user_email_attach_file_name']['tmp_name'])){
			  
			}
			else{
			     $RandomNum   = rand(0, 9999999999);
			     $ImageName = str_replace(' ','-',strtolower($_FILES['user_email_attach_file_name']['name']));
			     $ImageType = $_FILES['user_email_attach_file_name']['type'];
			     $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
			     $ImageExt = str_replace('.','',$ImageExt);
			     $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
			     $New_EmlTemlImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
			     move_uploaded_file($_FILES['user_email_attach_file_name']['tmp_name'], "$Destination/$New_EmlTemlImageName");
			}
				        
				            
			$sql_eml_templ = sprintf("select * from profile_info where user_username='%s'",$from_name);
			$resb = mysql_query($sql_eml_templ) or die(mysql_error());	
			$eml_templ_white = "";
			if( mysql_num_rows($resb) > 0) 
			{
				$recb = mysql_fetch_assoc($resb);
				$eml_templ_att = $recb['eml_templ_att'];
				$eml_templ_cont = $recb['eml_templ_cont'];
				$eml_templ_subj = $recb['eml_templ_subj'];
				$eml_templ_white = $recb['eml_templ_white'];
			}
			if (isset($recb) and isset($recb['user_tw_number']))
				$recb['user_tw_number'] = '('.substr($recb['user_tw_number'],0,3).') '.substr($recb['user_tw_number'],3,3).'-'.substr($recb['user_tw_number'],6,4);
			if ($eml_templ_white == "")
			 $eml_templ_white = 'default.png';
			$eml_white = $eml_white_sub_path.$eml_templ_white;

			$eml_attach_file = '';
			if ($New_EmlTemlImageName !="")
			{
				$eml_attach_file = trim($New_EmlTemlImageName);
				$eml_attach_file =$eml_attach_file_sub_path.$eml_attach_file;
			}		

			// -------------- Email Signature Template --------------- 
			if (isset($recb))
			{
				$eml_sig_mobile_ph=$recb['eml_sig_mobile_ph'];
				$eml_sig_office_ph=$recb['eml_sig_office_ph'];
			    $eml_sig_eml1=$recb['eml_sig_eml1'];
			    $eml_sig_eml2=$recb['eml_sig_eml2'];
			    $eml_sig_buss_addr=$recb['eml_sig_buss_addr'];
			    $eml_sig_fax=$recb['eml_sig_fax'];
			    $eml_sig_logo=$recb['eml_sig_logo'];
			    $eml_sig_photo=$recb['eml_sig_photo'];	        
				
			    if (!isset($eml_sig_logo))
			    {
					$eml_sig_logo = 'userprofile/userfiles/email_signatures/logos/default.gif';
				}
				if (!isset($eml_sig_photo))
			    {
					$eml_sig_photo = 'userprofile/userfiles/email_signatures/photos/default.gif';
				}
				// Read image path, convert to base64 encoding
				$photo_imgData = base64_encode(file_get_contents($eml_sig_photo));
				$logo_imgData = base64_encode(file_get_contents($eml_sig_logo));

				// Format the image SRC:  data:{mime};base64,{data};
				$src_photo = 'data:'.'image/jpeg'.';base64,'.$photo_imgData;
				$src_logo = 'data:'.'image/jpeg'.';base64,'.$logo_imgData;
				
			}


	 		$email_to = trim($_POST["email_to"]);
	    	$eml_to_tok = explode(';',$email_to);
	    	$eml_to_cnt = count($eml_to_tok);
	    	for ($i=0;$i<$eml_to_cnt;$i++)
	        {
	        	// Email you are sending to (required)
				$emailAddress = $eml_to_tok[$i];
				
				// Subject Line (required)
				$subject = $_POST["email_subj"];
				
				// from address prefix without @ if it is info@test.com prefix would be info(required)
				$str_pos = strpos($sql_result['eml_marketing_user_eml'],'@');
				if ($str_pos === false) {
					$response['status']='Error';
					break;
				}else
				{
					$fromaddress = substr($sql_result['eml_marketing_user_eml'],0,$str_pos);
					
					// Send time Unix Time Stamp
					
				
					date_default_timezone_set("America/Phoenix");
					$current = date("Y-m-d h:i:s", time());
					$date = strtotime($current1);

					
					// From name usally a person's name or company name (required)
					$fromname = $sql_result['eml_marketing_username'];
					
					// HTML Message Part
					/*$email_body = $_POST["email_body"];
					$real_email_body = $email_body;				
					$htmlmsg = $email_body;*/
					$htmlmsg = 'Testing Message,
							Checking out the sending single api call

							Thank you
							John Smith
							';

					// Text message part. To use you must also set sendtexthtml = 1
					//$textmsg = $real_email_body;
					$textmsg = '';


					// Send html and text part (1 = enabled 0 equals disabled)
					$sendtexthtml = 0;


					// Domain/IP Combniation used Profile field id's found either using getsendingprofiles api or inside the system Under Sending Profiles'
					// profile id. (required)
					$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=getsendingprofiles&start=0&limit=10");
					$sxml = new SimpleXMLElement($result);
					$profileID=0;
					foreach ($sxml->profile as $record)
					{
						// Output data to the screen
						$profileID = $record->id;
						break;
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
	
				

					// Email authenication recommend to be always set to 1  (1 = enabled 0 equals disabled)
					$dkim =1;

					// If you using a message id instead of passing a message
					$messageID = 0;

					// View as a webpage link (1 = enabled 0 equals disabled)
					$viewasawebpage = 0;



					// Build the request
					$postData =  'email=' . $emailAddress . '&dkim=' . $dkim . '&profileid=' . $profileID . '&whoisid=' . $whoisid . '&messageid=' . $messageID;
					$postData .=  '&sendtexthtml=' . $sendtexthtml . '&viewasawebpage=' . $viewasawebpage . '&fromaddress=' . $fromaddress. '&fromname=' . urlencode($fromname);
					$postData .= '&subject=' . urlencode($subject) . '&date=' . $date . '&htmlmsg=' . urlencode($htmlmsg) . '&textmsg=' . urlencode($textmsg);

					// Connect to the api // Note: email will send within 2 minutes of the api call.
					$result = FetchUrl('http://www.softwarelogin.com/api.php?apikey=' . APIKEY . "&area=email&action=sendsingleemail",$postData);

					/*$max_id++;
					
					mysql_query("set time_zone='-7:00';");
						
					$sql_mail = sprintf("insert into mail_log_info (mail_rcvr,mail_subject,mail_body,send_dt,from_nm,from_address,mail_id,is_opened) values ('%s','%s','%s',sysdate(),'%s','%s','%d','%d')",$emailAddress,$email_subj,$real_email_body,urlencode($fromname),$sql_result['eml_marketing_user_eml'],$max_id,$is_opened);

	        		mysql_query($sql_mail) or die(mysql_error());	*/
	        		
	        		// Dispaly the response will be xml either success or the error mesage.
					$response['status'] = 'All emails are sent';				
					$response['response'] = htmlspecialchars($result,ENT_QUOTES);
					
					$_SESSION['email_sent']++;	
					///break;
				}
	        }
	 	}
	
	}				
	
}

$response['email_sent'] = $_SESSION['email_sent'];		
$response['email_recv'] = $_SESSION['email_recv'];
	
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