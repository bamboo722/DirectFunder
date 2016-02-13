<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
require_once("PHPMailer-master/PHPMailerAutoload.php");


$response = array();
$response['status']='Error';
if (is_ajax())
{
	$succ_cnt=0;
	$from_name = $_SESSION['user_login'];
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

	/* --------------------------------------------- */

	/* --------------------------  Normal Way to send email ----------------------- */
	/* This way doesn't work on Godaddy */

	$smtp_mail_usr = $_SESSION['google_acc_nm']; 
	$smtp_mail_pw = $_SESSION['google_acc_pwd']; 
	$smtp_use = 'smtp.gmail.com';

	$to_name = "customer";
		

	if(isset($_POST["email_from"]) && !empty($_POST["email_from"]))
	{
		
			
		$from_email = trim($_POST["email_from"]);
		if (isset($_POST["email_to"]) && !empty($_POST["email_to"])) 
	 	{
	 		$email_to = trim($_POST["email_to"]);
	    	$eml_to_tok = explode(';',$email_to);
	    	$eml_to_cnt = count($eml_to_tok);
			  
		    if (isset($_POST["email_body"]) && isset($_POST["email_subj"])) 
		    { 
		    	//send SMS
		    	
		    	for ($i=0;$i<$eml_to_cnt;$i++)
	            {
	            	$email_body = $_POST["email_body"];
		    		$email_subj = $_POST["email_subj"];
		    		
		    		$sql_sel_cus = sprintf("select p_fl_nm from customer_info where (p_eml1 like '%s') or (p_eml2 like '%s') or (p2_eml like '%s') or (p3_eml like '%s') limit 1",$eml_to_tok[$i],$eml_to_tok[$i],$eml_to_tok[$i],$eml_to_tok[$i]);
					$resb_sel_cus = mysql_query($sql_sel_cus) or die(mysql_error());	
					if ($recb_cus = mysql_fetch_assoc($resb_sel_cus))
					{
						$to_name = $recb_cus['p_fl_nm'];
					}

	            	try {
	            		$mail = new PHPMailer(true);
						$mail->IsSMTP();
		
						$mail->Host = $smtp_use; 
						$mail->SMTPAuth = true; 
						//$mail->Port = 465; 
						$mail->Port = 587; 
						//$mail->SMTPSecure = "ssl"; 
						$mail->SMTPSecure = "tls"; 
						$mail->Username = $smtp_mail_usr; 
						$mail->Password = $smtp_mail_pw; 
						
						$mail->SetFrom($from_email, $from_name); 
						$mail->AddAddress($eml_to_tok[$i], $to_name); 
						
						$mail->Subject = $email_subj;
						
						if ($eml_attach_file != '')
							$mail->addAttachment($eml_attach_file); 
						$real_email_body = $email_body;				
						
						$email_body .= "<br>";
						$email_body .= "<br>";
						$email_body .= "<table cellpadding='0' cellspacing='0' width='450px' style='overflow:auto; overflow-x:scroll;'>";
						
						$email_body .= "<tr>";
						$email_body .= "<td rowspan='5'>";
						
						$email_body .='<img src="'.$src_photo.'">';
						
						$email_body .= "</td>";
						
						$email_body .= "<td colspan='2'>";
						$email_body .= "<span style='margin:0px;font-size:12px;font-weight:bold'>";
						if (($_SESSION['user_group'] == 'Admin')||($_SESSION['user_login'] == 'Duc'))
							$email_body .= $recb['user_firstname'].' '.$recb['user_lastname'].' | '.'Advisor';
						else
							$email_body .= $recb['user_firstname'].' '.$recb['user_lastname'].' | '.$_SESSION['user_group'];
						$email_body .= "</span>";
						$email_body .= "</td></tr>";
						
						$email_body .= "<tr><td align='left' colspan='3' style='padding: 0px;'>";
						$email_body .="<img src='".$src_logo."'>";
						
						$email_body .= "</td></tr>";	
						
						$email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='3'>".'<span>'.$recb['eml_sig_buss_addr'].'</span>';
						$email_body .= "</td></tr>";	
						
						$email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='3'>".'<span>'.'Office:'.'</span>'.$recb['eml_sig_office_ph'].' | '.'<span style="font-weight:bold">'.'Mobile:'.'</span>'.$recb['eml_sig_mobile_ph'].' | '.'<span style="font-weight:bold">'.'Fax:'.'</span>'.$recb['eml_sig_fax'];
						$email_body .= "</td></tr>";	
						
						if (isset($recb['eml_sig_eml2']))
						{
						  $email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='2'>".'<span>'.$recb['eml_sig_eml1']. ' | ' .$recb['eml_sig_eml2'].'</span>';
							
						}else
						{
							$email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='2'>".'<span>'.$recb['eml_sig_eml1'].'</span>';
						}
						$email_body .= "</td></tr></table>";		
						if ($eml_to_cnt>1)
							$email_body = $_POST["email_sal"].' '.$to_name.','.'<br>'.$email_body;
						
						$max_id++;
						//$email_body .= "<img border='0' src='http://www.directfunder.com/agentc3/trackonline.php?email=$eml_to_tok[$i]&id=$max_id&subject=$email_subj' width='1' height='1' alt='image for email' >";
						$mail->MsgHTML($email_body); 
						
						$mail->Send(); 					
						
						mysql_query("set time_zone='-7:00';");
						
						$sql_mail = sprintf("insert into mail_log_info (mail_rcvr,mail_subject,mail_body,send_dt,from_nm,from_address,mail_id,is_opened) values ('%s','%s','%s',sysdate(),'%s','%s','%d','%d')",$eml_to_tok[$i],$email_subj,$real_email_body,$from_name,$from_email,$max_id,$is_opened);

	        			mysql_query($sql_mail) or die(mysql_error());	
	                	
	                  	unset($mail);
	              
	              	} catch (phpmailerException $e) {          		
						$response['status'] =  $e->errorMessage();
					} catch (Exception $e) {
						
						$response['status'] =  $e->getMessage();
					}
	            }   	
	            			
				$response['status'] = 'All emails are sent';								
	   		}   
		}
	}else
	{
		$response['status'] = 'From Email Error';
	}

	/* --------------------------------------------------------------- */

	/* --------------------------  send email on Godaddy ----------------------- */

	/*$from_name = $_SESSION['user_login'];

	if(isset($_POST["email_from"]) && !empty($_POST["email_from"]))
	{
		$from_email = trim($_POST["email_from"]);
		if (isset($_POST["email_to"]) && !empty($_POST["email_to"])) 
	 	{
	 		$email_to = trim($_POST["email_to"]);
	    	$eml_to_tok = explode(';',$email_to);
	    	$eml_to_cnt = count($eml_to_tok);
			  
		    if (isset($_POST["email_body"]) && isset($_POST["email_subj"])) 
		    { 
		    	//send SMS
		    	
		    	for ($i=0;$i<$eml_to_cnt;$i++)
	        	{
					$email_body = $_POST["email_body"];
		    		$email_subj = $_POST["email_subj"];
	        		$sql_sel_cus = sprintf("select p_fl_nm from customer_info where (p_eml1 like '%s') or (p_eml2 like '%s') or (p2_eml like '%s') or (p3_eml like '%s') limit 1",$eml_to_tok[$i],$eml_to_tok[$i],$eml_to_tok[$i],$eml_to_tok[$i]);
					$resb_sel_cus = mysql_query($sql_sel_cus) or die(mysql_error());	
					if ($recb_cus = mysql_fetch_assoc($resb_sel_cus))
					{
						$to_name = $recb_cus['p_fl_nm'];
					}
					
	        		try 
					{
						$mail = new PHPMailer();
						$mail->IsSMTP();
						$mail->SMTPDebug   = 2;
						$mail->DKIM_domain = '127.0.0.1';
						$mail->Debugoutput = 'html';
						$mail->Host        = "localhost";
						$mail->Port        = 25;
						$mail->SMTPAuth    = false;
						$mail->Subject = $email_subj;
			    	
						$mail->SetFrom($from_email, $from_name); 
					
					
						$mail->AddAddress($eml_to_tok[$i], $to_name); 
					
						
						if ($eml_attach_file != '')
							$mail->addAttachment($eml_attach_file);
							 
						$real_email_body = $email_body;					
										
						$email_body .= "<br>";
						$email_body .= "<br>";
						$email_body .= "<table cellpadding='0' cellspacing='0' width='450px' style='overflow:auto; overflow-x:scroll;'>";
					
						
						$email_body .= "<tr>";
						$email_body .= "<td rowspan='5'>";
						
						$email_body .='<img src="'.$src_photo.'">';
						
						$email_body .= "</td>";
						
						$email_body .= "<td colspan='2'>";
						$email_body .= "<span style='margin:0px;font-size:12px;font-weight:bold'>";
						if (($_SESSION['user_group'] == 'Admin')||($_SESSION['user_login'] == 'Duc'))
							$email_body .= $recb['user_firstname'].' '.$recb['user_lastname'].' | '.'Advisor';
						else
							$email_body .= $recb['user_firstname'].' '.$recb['user_lastname'].' | '.$_SESSION['user_group'];
						$email_body .= "</span>";
						$email_body .= "</td></tr>";
						
						$email_body .= "<tr><td align='left' colspan='3' style='padding: 0px;'>";
						$email_body .="<img src='".$src_logo."'>";
						
						$email_body .= "</td></tr>";	
						
						$email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='3'>".'<span>'.$recb['eml_sig_buss_addr'].'</span>';
						$email_body .= "</td></tr>";	
						
						$email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='3'>".'<span>'.'Office:'.'</span>'.$recb['eml_sig_office_ph'].' | '.'<span style="font-weight:bold">'.'Mobile:'.'</span>'.$recb['eml_sig_mobile_ph'].' | '.'<span style="font-weight:bold">'.'Fax:'.'</span>'.$recb['eml_sig_fax'];
						$email_body .= "</td></tr>";	
						
						if (isset($recb['eml_sig_eml2']))
						{
						  $email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='2'>".'<span>'.$recb['eml_sig_eml1']. ' | ' .$recb['eml_sig_eml2'].'</span>';
							
						}else
						{
							$email_body .= "<tr style='font-weight:bold;font-size:10px;'><td colspan='2'>".'<span>'.$recb['eml_sig_eml1'].'</span>';
						}
						$email_body .= "</td></tr></table>";		
						if ($eml_to_cnt>1)
							$email_body = $_POST["email_sal"].' '.$to_name.','.'<br>'.$email_body;
						$max_id++;
						$email_body .= "<img border='0' src='http://www.directfunder.com/agentc3/trackonline.php?email=$eml_to_tok[$i]&id=$max_id&subject=$email_subj' width='1' height='1' alt='image for email' >";
						$mail->MsgHTML($email_body); 
						
						$mail->Send(); 					
						
						mysql_query("set time_zone='-7:00';");

						$sql_mail = sprintf("insert into mail_log_info (mail_rcvr,mail_subject,mail_body,send_dt,from_nm,from_address,mail_id,is_opened) values ('%s','%s','%s',sysdate(),'%s','%s','%d','%d')",$eml_to_tok[$i],mysql_real_escape_string($email_subj),mysql_real_escape_string($real_email_body),$from_name,$from_email,$max_id,$is_opened);

		        		mysql_query($sql_mail) or die(mysql_error());	
		                
		                unset($mail);
		          
		          	} catch (phpmailerException $e) {

						$response['status'] =  $e->errorMessage();
					} catch (Exception $e) {

						$response['status'] =  $e->getMessage();
					}
	        	}
				
				$response['status'] = 'All emails are sent';				
	        }					
		}	
	}else
	{
		$response['status'] = 'From Email Error';
	}*/


	/*========================================= Get Email Notification ==============================================*/ 
	//PHP - Get Gmail new messages (unread) from Atom Feed

	$username = urlencode($_SESSION['google_acc_nm']);	// gmail account
	$password = $_SESSION['google_acc_pwd'];	// gmail account password
	$tag = '';

	$handle = curl_init();
	$options = array( 
		  CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_HEADER         => false,
	      CURLOPT_FOLLOWLOCATION => false,
	      CURLOPT_SSL_VERIFYHOST => '0',
	      CURLOPT_SSL_VERIFYPEER => '1',
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_SSL_VERIFYHOST => 0,		                  
	      CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
	      CURLOPT_VERBOSE        => true,
	      CURLOPT_URL            => 'https://'.$username.':'.$password.'@mail.google.com/mail/feed/atom/'.$tag,
				);
				
	curl_setopt_array($handle, $options);
	$output = (string)curl_exec($handle);
	$xml = simplexml_load_string($output);

	if (curl_errno($handle)) {
	  $response['status'] = 'Error: ' . curl_error($handle);
	  
	}

	curl_close($handle);

	$data = array();
	$data['entries'] = array();
	$data['title'] = (string)$xml->title;
	$data['fullcount'] = (int)$xml->fullcount;
	$data['tagline'] = (string)$xml->tagline;
	$data['modified'] = (string)$xml->modified;


	/* get latest time from mail_log_info */
	$old_dt=0;
	$sql = "select max(recv_dt) as latest_recv_dt from mail_log_info where mail_stat>=1";
	$sql_res = mysql_query($sql) or die(mysql_error() . "go select error");
	$sql_result = mysql_fetch_assoc($sql_res);
	$num_rows = (int)(mysql_num_rows($sql_res));
	$old_dt = strtotime($sql_result['latest_recv_dt']);

	foreach ($xml->entry as $entry){
	    $current_entry = array();
	    $current_entry['modified'] = (string)$entry->modified;
	    $current_entry['modified'] = new DateTime( $current_entry['modified']);
	    $current_entry['modified'] = $current_entry['modified']->format('Y-m-d H:i:s');
	    $feed_dt = strtotime($current_entry['modified']);
	    
	    
	    if ($old_dt < $feed_dt){	
	    
		    $current_entry['author'] = array();
		    $current_entry['contributor'] = array();
		    $current_entry['title'] = (string)$entry->title;
		    
		    /* if this email is sms email, then we except it */
		    $temp_title = trim($current_entry['title']);
		    $temp_title = substr($temp_title,0,8);
		    if ($temp_title=="SMS from")
		    {	    
				continue;
			}
		    
		    $current_entry['summary'] = (string)$entry->summary;
		    $current_entry['link'] = (string)$entry->link['href'];
		  
		    
		    $current_entry['author']['name'] = (string)$entry->author->name;
		    $current_entry['author']['email'] = (string)$entry->author->email;
		    
		    $data['entries'][0] = $current_entry;
		    $mail_state = 1;
		    		  
		    //consider ' character to insert sql_string 
		    //   by add " to sql_string
		    mysql_query("set time_zone='-7:00';");
		    $sql_mail = sprintf('insert into mail_log_info(mail_rcvr, mail_subject,mail_body,send_dt,from_nm,from_address,mail_stat,recv_dt,link) values ("%s","%s","%s",sysdate(),"%s","%s","%s","%s","%s")',$_SESSION['google_acc_nm'],mysql_real_escape_string($current_entry['title']),mysql_real_escape_string($current_entry['summary']),$current_entry['author']['name'],$current_entry['author']['email'],$mail_state,$current_entry['modified'],$current_entry['link']);
		    mysql_query($sql_mail) or die(mysql_error());	
		}
	}

	//Email Sent
	$sql="select count(mail_stat) as email_sent from mail_log_info where  from_address ='".$_SESSION['google_acc_nm']."' and send_dt > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error()."11");
	$res_rec=mysql_fetch_assoc($res);
	$_SESSION['email_sent']=$res_rec['email_sent'];

						
	//Email Receive
	$sql="select count(mail_stat) as email_recv from mail_log_info where  mail_rcvr ='".$_SESSION['google_acc_nm']."' and send_dt > '".$_SESSION['last_logout']."'";
	$res=mysql_query($sql) or die(mysql_error()."11");
	$res_rec=mysql_fetch_assoc($res);
	$_SESSION['email_recv']=$res_rec['email_recv'];

	/* --------------------------------------------------------------- */	
	
	
}

$response['email_sent'] = $_SESSION['email_sent'];		
$response['email_recv'] = $_SESSION['email_recv'];
	
echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

?>