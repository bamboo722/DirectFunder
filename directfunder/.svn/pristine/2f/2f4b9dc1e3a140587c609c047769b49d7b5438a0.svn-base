<?php
require_once("includes/dbconnect.php");
if (!empty($_GET['email'])) {
	$id = $_GET['id'];
	$checkid = "Id:" . $id;
	$email = $_GET['email'];
	$sub = $_GET['subject'];
	mysql_query("set time_zone='-7:00';");	
	$sql_time = mysql_query("select sysdate() as current;");	
	if ($sql_rec = mysql_fetch_assoc($sql_time))
	{
		$date = $sql_rec['current'];	
	}
	
	$fh = fopen("email.txt", "a+"); //file handler
	$a = fgets($fh);
	$found = false; // init as false
	while (($buffer = fgets($fh)) !== false) {
		if (strpos($buffer, $checkid) !== false) {
		$found = true;
		break; // Once you find the string, you should break out the loop.
	}
}
if ($found == false) {
	$string = $email."opened the mail with subject:".$sub."on%".$date."% with mailId:".$id."\n";
	fwrite($fh, $string);
}
fclose($fh);

//Get the http URI to the image
$graphic_http = 'http://www.formget.com/tutorial/php-email-tracker/blank.gif';

//Get the filesize of the image for headers
$filesize = filesize('blank.gif');

//Now actually output the image requested, while disregarding if the database was affected
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Disposition: attachment; filename="blank.gif"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $filesize);
readfile($graphic_http);

//All done, get out!
exit;
}