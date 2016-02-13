<?php
/****-------------------------------------------------------------------**************************	

		Purpose 	: 	This page will act as the connect to the database with the pages 

		Project 	:   	

	 	Developer 	: 	Kelvin Smith

		Version		:	1.0

	 	Create Date : 	15/02/2012     

****-------------------------------------------------------------------************************/

//connection to database server 
//$link=mysql_connect("prospectsadmin.db.7029164.hostedresource.com","prospectsadmin","pRospects@123") or die("Could not connect to database");
$link=mysql_connect("localhost","root","") or die("Could not connect to database");
//connection to database server end

// set character 
/*mysql_query("SET character_set_client = utf8");
mysql_query("SET character_set_results = utf8");
mysql_query("SET character_set_connection = utf8");
mysql_query("commit");*/
mysql_set_charset("utf8");

//connection to smart_travel_db database

//mysql_select_db("prospectsadmin") or die("Could not select database");
mysql_select_db("ducn_db") or die("Could not select database");

//connection to smart_travel_db database end

//set connection time unlimited

set_time_limit(0);

//function number format for uk

function number_format_uk($no)

{

$ln = strlen($no);

$con=1;

$sep=3;

$fmNo="";

if ($ln>3)

{

$fmNo=substr($no, -3);

$no = substr($no,0, $ln-3);

$ln=$ln-3;

}

else

{

$con="0";

$fmNo=$no;

}

while ($con==1)

{

if ($ln<1)

	$con="0";

else

{

$fmNo= substr($no, -2) . "," . $fmNo;

$no = substr($no,0, $ln-2);

$ln=$ln-2;

}



}



return $fmNo;

}

//function number format for uk end

//function to handle single quote problem

function sql_quote( $value1 )

{

if( get_magic_quotes_gpc() )

{

      $value1 = stripslashes( $value1 );

}

//check if this function exists

if( function_exists( "mysql_real_escape_string" ) )

{

      $value1 = mysql_real_escape_string( $value1 );

}

//for PHP version < 4.3.0 use addslashes

else

{

      $value1 = addslashes( $value1 );

}

return $value1;

}

//function to handle single quote problem end

//control insert date formats

function insertdate($user_date)

{	

	$strDateString=explode("/",$user_date);

	if (count($strDateString==3))

		$insert_date=$strDateString[2] . "-" .$strDateString[1]."-".$strDateString[0];

	else

		$insert_date="0000-00-00";

	return $insert_date;



}

//control insert date formats ends

//control display date formats

function DisplayDate($user_date)

{

		$strDateString=explode("-",$user_date);

		$insert_date=substr($user_date,8,2) . "/" .substr($user_date,5,2) ."/". substr($user_date,0,4) ;

		return $insert_date;

}

//control display date formats ends

//control update date formats

function getUpdatedDate()

{

			$strBare_Query = "select upd_dt  from site_upd_date ";

		    $rsResult_All = MYSQL_QUERY($strBare_Query);

		    $intNumber_All = mysql_Numrows($rsResult_All);

			if ($intNumber_All==0)

			    { $pdate="";    }

			else

		  { $pdate= DisplayDate(mysql_result($rsResult_All,0,"upd_dt")); }



		return $pdate;

}

//control update date formats ends

//funtion for security purpose to eradicate injection problem

function cleanup($data, $write=false) {

    if (is_array($data)) {

        foreach ($data as $key => $value) {

            $data[$key] = cleanup_lvl2($value, $write);

        }

    } else {

        $data = cleanup_lvl2($data, $write);

    }

    return $data;

}



function cleanup_lvl2($data, $write=false) {

    if (isset($data)) { // preserve NULL

        if (get_magic_quotes_gpc()) {

            $data = stripslashes($data);

        }

        if ($write) {

            $data = mysql_real_escape_string($data);

        }

    }

    return $data;
}
function clean($data)
{

	$data=utf8_decode($data);

	$data=addslashes($data);

	$data=htmlentities($data);

	$data=strip_tags($data);

	$data=cleanup($data);

	return $data;
}
//funtion for security purpose to eradicate injection problem end
?>