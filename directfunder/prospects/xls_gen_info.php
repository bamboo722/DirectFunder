<?php

session_start();

if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name

{

	header("Location: index.php");//login in AdminLogin.php

}

require_once("includes/dbconnect.php");



$sql_exp = "select center,lead_src,priority_opt,f_nm,l_nm,p_eml1,email_2,p_ph1,p_ph2,p_hm_addr,p_city,p_state,p_zip,

machine_no,total_amt,invoice_dt,invoice_number,financing_opt,financing_stat,pay1_dt,pay1_amt,pay1_stat,pay2_dt,pay2_amt,pay2_stat, 

pay3_dt,pay3_amt,pay3_stat, etd_dt,eta_dt,freight_com_info,shipping_method,funding_dt,follow_up,next_payment, agent from customer_info where priority_opt!='Delete'";



//print($sql_exp);		  

//Optional: print out title to top of Excel or Word file with Timestamp

//for when file was generated:

//set $Use_Titel = 1 to generate title, 0 not to use title

$Use_Title = 0;

//define date for title: EDIT this to create the time-format you need

$now_date = date('m-d-Y H:i');

//define title for .doc or .xls file: EDIT this if you want

$title = "Items";



//select database



//execute query

$result = mysql_query($sql_exp);

$w=2;



$file_type = "vnd.ms-excel";

$file_ending = "xls";



//header info for browser: determines file type ('.doc' or '.xls')



header("Content-Type: application/$file_type");

header("Content-Disposition: attachment; filename=database_dump.$file_ending");

header("Pragma: no-cache");

header("Expires: 0");



if ($Use_Title == 1)

{

echo("$title\n");

}

//define separator (defines columns in excel & tabs in word)

$sep = "\t"; //tabbed character



//start of printing column names as names of MySQL fields

/*for ($i = 0; $i < mysql_num_fields($result); $i++) 

{

		echo mysql_field_name($result,$i) . "\t";

}*/

echo 'Buy Date'. "\t";

echo 'Lead Source'. "\t";

echo 'Priority'. "\t";

echo 'First Name'. "\t";

echo 'Last Name'. "\t";

echo 'Email 1'. "\t";

echo 'Email 2'. "\t";

echo 'Phone 1'. "\t";

echo 'Phone 2'. "\t";



echo 'Address'. "\t";

echo 'City'. "\t";

echo 'State'. "\t";

echo 'Zip'. "\t";

echo 'Qty'. "\t";

echo 'Total Amount'. "\t";

echo 'Invoice Date'. "\t";

echo 'Invoice Number'. "\t";

echo 'Financing'. "\t";

echo 'Financing Status'. "\t";



echo 'Payment 1 Date'. "\t";

echo 'Payment 1 Amount'. "\t";

echo 'Payment 1 Status'. "\t";

echo 'Payment 2 Date'. "\t";

echo 'Payment 2 Amount'. "\t";

echo 'Payment 2 Status'. "\t";

echo 'Payment 3 Date'. "\t";

echo 'Payment 3 Amount'. "\t";

echo 'Payment 3 Status'. "\t";



echo 'ETD'. "\t";

echo 'ETA'. "\t";

echo 'Freight Company Information'. "\t";

echo 'Shipping Method'. "\t";

echo 'Delivery Date'. "\t";

echo 'Next Follow Up Date'. "\t";

echo 'Next Payment Date'. "\t";
echo 'Agent'. "\t";




print("\n");

//end of printing column names



//start while loop to get data

/*

note: the following while-loop was taken from phpMyAdmin 2.1.0.

--from the file "lib.inc.php".

*/



    while($row = mysql_fetch_row($result))

    {

			/*$center='';

			$lead_src='';

			$priority_opt='';

			$f_nm='';

			$l_nm='';

			$p_eml1='';

			$email_2='';

			$p_ph1='';

			$p_ph2='';

			

			$p_hm_addr='';

			$p_city='';

			$p_state='';

			$p_zip='';

			$machine_no='';

			$total_amt='';

			$invoice_dt='';

			$invoice_number='';

			$financing_opt='';

			$financing_stat='';

			

			$pay1_dt='';

			$pay1_amt='';

			$pay1_stat='';

			$pay2_dt='';

			$pay2_amt='';

			$pay2_stat='';

			$pay3_dt='';

			$pay3_amt='';

			$pay3_stat='';

			

			$etd_dt='';

			$eta_dt='';

			$freight_com_info='';

			$shipping_method='';

			$funding_dt='';*/		

        $schema_insert = "";

        for($j=0; $j<mysql_num_fields($result);$j++)

        {

				if(!isset($row[$j]))

				{

					$schema_insert .= "".$sep;

				}

				elseif ($row[$j] != "")

				{

					$schema_insert .= "$row[$j]".$sep;

				}

				else

				{

					$schema_insert .= "".$sep;

				}

			

        }

		//value addition on excel

		/*$schema_insert .= $center.$sep;

		$schema_insert .= $lead_src.$sep;

		$schema_insert .= $priority_opt.$sep;

		$schema_insert .= $f_nm.$sep;

		$schema_insert .= $l_nm.$sep;

		$schema_insert .= $p_eml1.$sep;

		$schema_insert .= $email_2.$sep;

		$schema_insert .= $p_ph1.$sep;

		$schema_insert .= $p_ph2.$sep;

		

		$schema_insert .= $p_hm_addr.$sep;

		$schema_insert .= $p_city.$sep;

		$schema_insert .= $p_state.$sep;

		$schema_insert .= $p_zip.$sep;

		$schema_insert .= $machine_no.$sep;

		$schema_insert .= $total_amt.$sep;

		$schema_insert .= $invoice_dt.$sep;

		$schema_insert .= $invoice_number.$sep;

		$schema_insert .= $financing_opt.$sep;

		$schema_insert .= $financing_stat.$sep;

		

		$schema_insert .= $pay1_dt.$sep;

		$schema_insert .= $pay1_amt.$sep;

		$schema_insert .= $pay1_stat.$sep;

		$schema_insert .= $pay2_dt.$sep;

		$schema_insert .= $pay2_amt.$sep;

		$schema_insert .= $pay2_stat.$sep;

		$schema_insert .= $pay3_dt.$sep;

		$schema_insert .= $pay3_amt.$sep;

		$schema_insert .= $pay3_stat.$sep;

		

		$schema_insert .=$etd_dt.$sep;

		$schema_insert .=$eta_dt.$sep;

		$schema_insert .=$freight_com_info.$sep;

		$schema_insert .=$shipping_method.$sep;

		$schema_insert .=$funding_dt.$sep;*/

		

		

		

		

		//this corrects output in excel when table fields contain \n or \r

		//these two characters are now replaced with a space

		$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);

        $schema_insert .= "\t";

		

        print(trim($schema_insert));

		

        print "\n";

    }



?>

