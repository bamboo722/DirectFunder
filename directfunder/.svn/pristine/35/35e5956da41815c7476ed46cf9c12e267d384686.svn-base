<?php
if(!isset($_GET["f"])) {
	exit("no parametter");
}

$fontFile=$_GET["f"];
$font = @file_get_contents($fontFile);
if ($font === false) {
	exit("Font not found");
}
header("Access-Control-Allow-Origin: *");
echo $font;
?>