<?php 
@session_start();
ob_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
{
    header("Location: index.php");//login in AdminLogin.php
}

require_once("includes/dbconnect.php");
require_once("includes/class.phpmailer.php");

$response = array();
$succ_cnt=0;

/*$smtp_mail_usr = "duc@directfunder.com";//smtp account
$smtp_mail_pw = "w532UrzC";//smtp account password
$smtp_use = 'pro.turbo-smtp.com'; // smtp server ( was created by duc@directfunder.com)*/
$from_name = $_SESSION['user_login'];
//$to_name = "customer";
//require_once("class.phpmailer.php");

$mail = new PHPMailer(true);
$mail->IsSMTP();
	
$smtp_mail_usr = "Ducana2016@gmail.com";//smtp account
$smtp_mail_pw = "Caden123";//smtp account password
$smtp_use = "ssl://smtp.gmail.com"; // smtp server ( was created by duc@directfunder.com)
//$smtp_use = "smtp.gmail.com"; // smtp server ( was created by duc@directfunder.com)
/*if (is_ajax()) {
	if(isset($_POST["eml_from"]) && !empty($_POST["eml_from"]))
	{
		$from_email = trim($_POST["eml_from"]);
		if (isset($_POST["eml_to"]) && !empty($_POST["eml_to"])) 
	 	{
	 		$eml_to = trim($_POST["eml_to"]);
	    	$eml_to_tok = explode(';',$eml_to);
	    	$eml_to_cnt = count($eml_to_tok);
			  
		    if (isset($_POST["eml_body"]) && isset($_POST["eml_subj"])) 
		    { 
		    	//send SMS
		    	$eml_body = $_POST["eml_body"];
		    	$eml_subj = $_POST["eml_subj"];
		    
                for ($i=0;$i<$eml_to_cnt;$i++)
                {
            
            		try {
						$mail->Host = $smtp_use; 
						$mail->SMTPAuth = true; 
						$mail->Port = 465; 
						$mail->SMTPSecure = "ssl"; 
						$mail->Username = $smtp_mail_usr; 
						$mail->Password = $smtp_mail_pw; 
						$mail->SetFrom($from_email, $from_name); 
						$mail->AddAddress($eml_to_tok[$i], $to_name); 
						$mail->Subject = $eml_subj; 
						$mail->MsgHTML($eml_body); 
						$mail->Send();				
						 
						 
					
						
				
						
						$succ_cnt++;
                       	$sql_mail = "insert into mail_log_info(mail_rcvr, mail_subject,mail_body,send_dt,from_nm,from_address) values ('".$eml_to_tok[$i]."','".$eml_subj."','".$eml_body."',sysdate(),'".$from_name."','".$from_email."')";
                        mysql_query($sql_mail) or die(mysql_error());	
                  
                  	} catch (phpmailerException $e) {
						$response['status'] =  $e->errorMessage();
					} catch (Exception $e) {
						$response['status'] =  $e->getMessage();
					}				
		
					
                }                 
				
				if ($succ_cnt == $eml_to_cnt)
				{
					$response['status'] = 'All emails are sent';					
				}				
	   		}   
   		}
	}else
  	{
		$response['status'] = 'From Email Error';
  	}*/
  	date_default_timezone_set('Etc/UTC');

require '../PHPMailerAutoload.php';

//Create a new SMTP instance
$smtp = new SMTP;

//Enable connection-level debug output
$smtp->do_debug = SMTP::DEBUG_CONNECTION;

try {
//Connect to an SMTP server
    if ($smtp->connect('smtp.gmail.com', 25)) {
        //Say hello
        if ($smtp->hello('localhost')) { //Put your host name in here
            //Authenticate
            if ($smtp->authenticate('Ducana2016@gmail.com', 'Caden123')) {
                echo "Connected ok!";
            } else {
                throw new Exception('Authentication failed: ' . $smtp->getLastReply());
            }
        } else {
            throw new Exception('HELO failed: '. $smtp->getLastReply());
        }
    } else {
        throw new Exception('Connect failed');
    }
} catch (Exception $e) {
    echo 'SMTP error: '. $e->getMessage(), "\n";
}

//"Ducana2016@gmail.com";//smtp account
//$smtp_mail_pw = "Caden123";//s


//Whatever happened, close the connection.
$response['status'] = 'All emails are sent';					
$smtp->quit(true);


echo json_encode($response);


//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>

<? /*
<form action="" method="POST">
 <table style="border-color: #000;" cellpadding="10">
 <tr><td>Name</td><td><input type="text" name="name"/></td></tr>
 <tr><td>Email</td><td><input type="text" name="email"/></td></tr>
 <tr><td>Comment</td><td><textarea name="comment"></textarea></td></tr>
 <tr><th><input type="submit" name="submit" value="Send"/></th></tr>
 </table>
</form>
<?php 
if(isset($_POST['submit'])){
$to=$_POST['email'];
$subject = '7topics simple email';
$name=$_POST['name'];
$comment=$_POST['comment'];
$message = '<html><body>';
$message .= "Dear " . $name . ",</br>Your details are given below:</br><table style='border-color: #000;' cellpadding='10'>";
$message .= "<tr style='background: #eee;'>
 <td><strong>Name:</strong></td>
 <td>" . $name . "</td>
 </tr>";
$message .= "<tr><td><strong>Comment:</strong></td><td>" . $comment . "</td></tr>";
$message .= "</table>";
$message .= "By Rahul Ranjan <br/><a href='http://7topics.com'>7topics.com</a>";
$message .= "</body></html>";
$headers ='From: webmaster@7topics.com' . "\r\n" .
'Reply-To: webmaster@7topics.com' . "\r\n" .
'MIME-Version: 1.0'."\r\n".
'Content-Type: text/html; charset=ISO-8859-1'."\r\n".
'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);
}
?>  */ ?>