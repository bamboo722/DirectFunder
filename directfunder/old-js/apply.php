<?php
require_once("prospects/includes/dbconnect.php");  
require("cnf.php");
require('mail/sendmail.php');
require('mail/pdf.php');

header('Content-Type: application/json');

$name = GetField("name");
$Email = GetField("Email");

$phone = GetField("phone");
$phone = preg_replace("/[^0-9]*/s", "",$phone);
$amountNeeded = GetField("amountNeeded");
$comment = GetField("comment");

if(Validation() == false) {
	echo "{\"status\": \"ERR\", \"msg\": \"There is an error in your submission.\"}";
	exit();
}
$to = $_POST["Email"];
$subject = "Mail to admin";
$from ="info@directfunder.com";
$message = ReadTemplate("mail/admin.txt");
foreach($_REQUEST as $key=>$val) {
	$message = str_replace("{{".$key."}}",$val,$message);
}

$sent=true;


        
foreach ($applyList as $admin) {
	sleep(1);
	$sent = $sent && sendMail($admin,$subject,$message,$from);                                                                       
}

/*** Start sending buyer info to buyer_info_master ***/
// create buyer_info_id 
/*$sql_sel = "select max(buyer_id) mxid from buyer_info_mast";
$res_sel = mysql_query($sql_sel) or die(mysql_error()."11");
$rec_sel = mysql_fetch_assoc($res_sel);
if($rec_sel['mxid'] =='')
{
    $recid = 'BY00000001';
}
else
{
    $schlid = "select concat(substring(buyer_id,1,2),lpad(max(convert(substring(buyer_id,3),signed))+1,8,'0')) recid from buyer_info_mast";
    $schres = mysql_query($schlid) or die(mysql_error()."go select error");
    $schrec = mysql_fetch_assoc($schres);
    $recid = $schrec['recid'];
}
       
$flagg = 0;
		
// insert full_name, mobile_phone,email,amount_needed(ammount_requested), comment into buyer_info_master 
if($Email!='')
{
    $sql_duplicate_check = "SELECT * from buyer_info_mast where email_1 = '".$Email."' ";
    $sql_duplicate_check_result = mysql_query($sql_duplicate_check) or die(mysql_error()."go select error");
    $sql_duplicate_check_row = mysql_fetch_assoc($sql_duplicate_check_result);
    if($sql_duplicate_check_row['buyer_id']!='')
    {
        $sql_ins = "insert into buyer_info_mast	(buyer_id, buy_dt, lead_source, priority_opt, f_nm, l_nm, com_nm, email_1, email_2, ph_1, ph_2, buyer_add, buyer_city, buyer_state, buyer_zip, machine_no, total_amt, invoice_dt, invoice_number,financing_opt, financing_stat, pay1_dt, pay1_amt, pay1_stat, pay2_dt, pay2_amt, pay2_stat, pay3_dt, pay3_amt, pay3_stat, etd_dt, freight_com_info, shipping_method, eta_dt, funding_dt, follow_up, next_payment, data_insert_by, data_insert_dt, data_update_by, data_update_dt, sales_agent, page_nm, is_duplicate, full_name, ss_com_name, ss_contact_person, ss_contact_phone, ss_contact_email, ss_technician, ss_tech_phone, ss_tech_email, ss_fax, ss_url, ss_address, ss_city, ss_state, ss_zipcode, is_kiosk, ship_com_name, ship_com_phone, ship_con_person, ship_ph, ship_driver_name, ship_driver_ph, ship_email1, ship_email2, ship_web, ship_addr, ship_city, ship_state, ship_zipcode, ship_pickup_date, ship_pickup_time, ship_estimated_delivery_date, ship_estimated_delivery_time, ship_ship_charge, ship_inv_no, pay4_dt, pay4_amt, pay4_stat, pay5_dt, pay5_amt, pay5_stat, pay6_dt, pay6_amt, pay6_stat, pay7_dt, pay7_amt, pay7_stat, pay8_dt, pay8_amt, pay8_stat, pay9_dt, pay9_amt, pay9_stat, pay10_dt, pay10_amt, pay10_stat, pay11_dt, pay11_amt, pay11_stat, pay12_dt, pay12_amt, pay12_stat, amount_requested,comment ) values ('".$recid."', curdate(),'"."','".$sql_duplicate_check_row['lead_source']."','".$sql_duplicate_check_row['priority_opt']."', '".$fnm."', '".$lnm."', '".$sql_duplicate_check_row['com_nm']."', '".$Email."','".$sql_duplicate_check_row['email_2']."', '".$phone."','".$sql_duplicate_check_row['ph_2']."', '".$sql_duplicate_check_row['buyer_add']."', '".$sql_duplicate_check_row['buyer_city']."', '".$sql_duplicate_check_row['buyer_state']."', '".$sql_duplicate_check_row['buyer_zip']."', '".$qty."', '".$sql_duplicate_check_row['total_amt']."', '".$sql_duplicate_check_row['invoice_dt']."','".$sql_duplicate_check_row['invoice_number']."', '".$sql_duplicate_check_row['financing_opt']."', '".$sql_duplicate_check_row['financing_stat']."', '".$sql_duplicate_check_row['pay1_dt']."','".$sql_duplicate_check_row['pay1_amt']."', '".$sql_duplicate_check_row['pay1_stat']."','".$sql_duplicate_check_row['pay2_dt']."','".$sql_duplicate_check_row['pay2_amt']."','".$sql_duplicate_check_row['pay2_stat']."','".$sql_duplicate_check_row['pay3_dt']."','".$sql_duplicate_check_row['pay3_amt']."','".$sql_duplicate_check_row['pay3_stat']."','".$sql_duplicate_check_row['etd_dt']."','".$sql_duplicate_check_row['freight_com_info']."','".$shipping_method."','".$sql_duplicate_check_row['eta_dt']."', '".$delivery2."', '".$sql_duplicate_check_row['follow_up']."', '".$sql_duplicate_check_row['next_payment']."','".$sql_duplicate_check_row['data_insert_by']."', curdate(),'".$sql_duplicate_check_row['data_update_by']."', curdate(), '".$sql_duplicate_check_row['sales_agent']."', '".$sql_duplicate_check_row['page_nm']."', 1, '".mysql_escape_string($name)."', '".$sql_duplicate_check_row['ss_com_name']."', '".$sql_duplicate_check_row['ss_contact_person']."', '".$sql_duplicate_check_row['ss_contact_phone']."', '".$sql_duplicate_check_row['ss_contact_email']."', '".$sql_duplicate_check_row['ss_technician']."', '".$sql_duplicate_check_row['ss_tech_phone']."', '".$sql_duplicate_check_row['ss_tech_email']."', '".$sql_duplicate_check_row['ss_fax']."', '".$sql_duplicate_check_row['ss_url']."', '".$sql_duplicate_check_row['ss_address']."', '".$sql_duplicate_check_row['ss_city']."', '".$sql_duplicate_check_row['ss_state']."', '".$sql_duplicate_check_row['ss_zipcode']."', '".$sql_duplicate_check_row['is_kiosk']."', '".$sql_duplicate_check_row['ship_com_name']."', '".$sql_duplicate_check_row['ship_com_phone']."', '".$sql_duplicate_check_row['ship_con_person']."', '".$sql_duplicate_check_row['ship_ph']."', '".$sql_duplicate_check_row['ship_driver_name']."', '".$sql_duplicate_check_row['ship_driver_ph']."', '".$sql_duplicate_check_row['ship_email1']."', '".$sql_duplicate_check_row['ship_email2']."', '".$sql_duplicate_check_row['ship_web']."', '".$sql_duplicate_check_row['ship_addr']."', '".$sql_duplicate_check_row['ship_city']."', '".$sql_duplicate_check_row['ship_state']."', '".$sql_duplicate_check_row['ship_zipcode']."', '".$sql_duplicate_check_row['ship_pickup_date']."', '".$sql_duplicate_check_row['ship_pickup_time']."', '".$sql_duplicate_check_row['ship_estimated_delivery_date']."','".$sql_duplicate_check_row['ship_estimated_delivery_time']."', '".$sql_duplicate_check_row['ship_ship_charge']."', '".$sql_duplicate_check_row['ship_inv_no']."', '".$sql_duplicate_check_row['pay4_dt']."', '".$sql_duplicate_check_row['pay4_amt']."', '".$sql_duplicate_check_row['pay4_stat']."', '".$sql_duplicate_check_row['pay5_dt']."', '".$sql_duplicate_check_row['pay5_amt']."', '".$sql_duplicate_check_row['pay5_stat']."', '".$sql_duplicate_check_row['pay6_dt']."', '".$sql_duplicate_check_row['pay6_amt']."', '".$sql_duplicate_check_row['pay6_stat']."', '".$sql_duplicate_check_row['pay7_dt']."', '".$sql_duplicate_check_row['pay7_amt']."', '".$sql_duplicate_check_row['pay7_stat']."', '".$sql_duplicate_check_row['pay8_dt']."', '".$sql_duplicate_check_row['pay8_amt']."', '".$sql_duplicate_check_row['pay8_stat']."', '".$sql_duplicate_check_row['pay9_dt']."', '".$sql_duplicate_check_row['pay9_amt']."', '".$sql_duplicate_check_row['pay9_stat']."', '".$sql_duplicate_check_row['pay10_dt']."', '".$sql_duplicate_check_row['pay10_amt']."', '".$sql_duplicate_check_row['pay10_stat']."', '".$sql_duplicate_check_row['pay11_dt']."', '".$sql_duplicate_check_row['pay11_amt']."', '".$sql_duplicate_check_row['pay11_stat']."', '".$sql_duplicate_check_row['pay12_dt']."', '".$sql_duplicate_check_row['pay12_amt']."', '".$sql_duplicate_check_row['pay12_stat']."', '".$amountNeeded."', '".$comment."' )";
	mysql_query($sql_ins) or die(mysql_error());
        $flagg = 1;
	mysql_query("DELETE FROM buyer_info_mast where buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE conversation_log SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE file_upload SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE mail_log SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
    }
}
			
if($phone!='')
{
    $sql_duplicate_check = "SELECT * from buyer_info_mast where ph_1 = '".$phone."' ";
    $sql_duplicate_check_result = mysql_query($sql_duplicate_check) or die(mysql_error()."go select error");
    $sql_duplicate_check_row = mysql_fetch_assoc($sql_duplicate_check_result);
			
    if($sql_duplicate_check_row['buyer_id']!='')
    {
        $sql_ins = "insert into buyer_info_mast	(buyer_id, buy_dt, lead_source, priority_opt, f_nm, l_nm, com_nm, email_1, email_2, ph_1, ph_2, buyer_add, buyer_city, buyer_state, buyer_zip, machine_no, total_amt, invoice_dt, invoice_number,financing_opt, financing_stat, pay1_dt, pay1_amt, pay1_stat, pay2_dt, pay2_amt, pay2_stat, pay3_dt, pay3_amt, pay3_stat, etd_dt, freight_com_info, shipping_method, eta_dt, funding_dt, follow_up, next_payment, data_insert_by, data_insert_dt, data_update_by, data_update_dt, sales_agent, page_nm, is_duplicate, full_name, ss_com_name, ss_contact_person, ss_contact_phone, ss_contact_email, ss_technician, ss_tech_phone, ss_tech_email, ss_fax, ss_url, ss_address, ss_city, ss_state, ss_zipcode, is_kiosk, ship_com_name, ship_com_phone, ship_con_person, ship_ph, ship_driver_name, ship_driver_ph, ship_email1, ship_email2, ship_web, ship_addr, ship_city, ship_state, ship_zipcode, ship_pickup_date, ship_pickup_time, ship_estimated_delivery_date, ship_estimated_delivery_time, ship_ship_charge, ship_inv_no, pay4_dt, pay4_amt, pay4_stat, pay5_dt, pay5_amt, pay5_stat, pay6_dt, pay6_amt, pay6_stat, pay7_dt, pay7_amt, pay7_stat, pay8_dt, pay8_amt, pay8_stat, pay9_dt, pay9_amt, pay9_stat, pay10_dt, pay10_amt, pay10_stat, pay11_dt, pay11_amt, pay11_stat, pay12_dt, pay12_amt, pay12_stat, amount_requested,comment ) values ('".$recid."', curdate(),'"."','".$sql_duplicate_check_row['lead_source']."','".$sql_duplicate_check_row['priority_opt']."', '".$fnm."', '".$lnm."', '".$sql_duplicate_check_row['com_nm']."', '".$Email."','".$sql_duplicate_check_row['email_2']."', '".$phone."','".$sql_duplicate_check_row['ph_2']."', '".$sql_duplicate_check_row['buyer_add']."', '".$sql_duplicate_check_row['buyer_city']."', '".$sql_duplicate_check_row['buyer_state']."', '".$sql_duplicate_check_row['buyer_zip']."', '".$qty."', '".$sql_duplicate_check_row['total_amt']."', '".$sql_duplicate_check_row['invoice_dt']."','".$sql_duplicate_check_row['invoice_number']."', '".$sql_duplicate_check_row['financing_opt']."', '".$sql_duplicate_check_row['financing_stat']."', '".$sql_duplicate_check_row['pay1_dt']."','".$sql_duplicate_check_row['pay1_amt']."', '".$sql_duplicate_check_row['pay1_stat']."','".$sql_duplicate_check_row['pay2_dt']."','".$sql_duplicate_check_row['pay2_amt']."','".$sql_duplicate_check_row['pay2_stat']."','".$sql_duplicate_check_row['pay3_dt']."','".$sql_duplicate_check_row['pay3_amt']."','".$sql_duplicate_check_row['pay3_stat']."','".$sql_duplicate_check_row['etd_dt']."','".$sql_duplicate_check_row['freight_com_info']."','".$shipping_method."','".$sql_duplicate_check_row['eta_dt']."', '".$delivery2."', '".$sql_duplicate_check_row['follow_up']."', '".$sql_duplicate_check_row['next_payment']."','".$sql_duplicate_check_row['data_insert_by']."', curdate(),'".$sql_duplicate_check_row['data_update_by']."', curdate(), '".$sql_duplicate_check_row['sales_agent']."', '".$sql_duplicate_check_row['page_nm']."', 1, '".mysql_escape_string($name)."', '".$sql_duplicate_check_row['ss_com_name']."', '".$sql_duplicate_check_row['ss_contact_person']."', '".$sql_duplicate_check_row['ss_contact_phone']."', '".$sql_duplicate_check_row['ss_contact_email']."', '".$sql_duplicate_check_row['ss_technician']."', '".$sql_duplicate_check_row['ss_tech_phone']."', '".$sql_duplicate_check_row['ss_tech_email']."', '".$sql_duplicate_check_row['ss_fax']."', '".$sql_duplicate_check_row['ss_url']."', '".$sql_duplicate_check_row['ss_address']."', '".$sql_duplicate_check_row['ss_city']."', '".$sql_duplicate_check_row['ss_state']."', '".$sql_duplicate_check_row['ss_zipcode']."', '".$sql_duplicate_check_row['is_kiosk']."', '".$sql_duplicate_check_row['ship_com_name']."', '".$sql_duplicate_check_row['ship_com_phone']."', '".$sql_duplicate_check_row['ship_con_person']."', '".$sql_duplicate_check_row['ship_ph']."', '".$sql_duplicate_check_row['ship_driver_name']."', '".$sql_duplicate_check_row['ship_driver_ph']."', '".$sql_duplicate_check_row['ship_email1']."', '".$sql_duplicate_check_row['ship_email2']."', '".$sql_duplicate_check_row['ship_web']."', '".$sql_duplicate_check_row['ship_addr']."', '".$sql_duplicate_check_row['ship_city']."', '".$sql_duplicate_check_row['ship_state']."', '".$sql_duplicate_check_row['ship_zipcode']."', '".$sql_duplicate_check_row['ship_pickup_date']."', '".$sql_duplicate_check_row['ship_pickup_time']."', '".$sql_duplicate_check_row['ship_estimated_delivery_date']."','".$sql_duplicate_check_row['ship_estimated_delivery_time']."', '".$sql_duplicate_check_row['ship_ship_charge']."', '".$sql_duplicate_check_row['ship_inv_no']."', '".$sql_duplicate_check_row['pay4_dt']."', '".$sql_duplicate_check_row['pay4_amt']."', '".$sql_duplicate_check_row['pay4_stat']."', '".$sql_duplicate_check_row['pay5_dt']."', '".$sql_duplicate_check_row['pay5_amt']."', '".$sql_duplicate_check_row['pay5_stat']."', '".$sql_duplicate_check_row['pay6_dt']."', '".$sql_duplicate_check_row['pay6_amt']."', '".$sql_duplicate_check_row['pay6_stat']."', '".$sql_duplicate_check_row['pay7_dt']."', '".$sql_duplicate_check_row['pay7_amt']."', '".$sql_duplicate_check_row['pay7_stat']."', '".$sql_duplicate_check_row['pay8_dt']."', '".$sql_duplicate_check_row['pay8_amt']."', '".$sql_duplicate_check_row['pay8_stat']."', '".$sql_duplicate_check_row['pay9_dt']."', '".$sql_duplicate_check_row['pay9_amt']."', '".$sql_duplicate_check_row['pay9_stat']."', '".$sql_duplicate_check_row['pay10_dt']."', '".$sql_duplicate_check_row['pay10_amt']."', '".$sql_duplicate_check_row['pay10_stat']."', '".$sql_duplicate_check_row['pay11_dt']."', '".$sql_duplicate_check_row['pay11_amt']."', '".$sql_duplicate_check_row['pay11_stat']."', '".$sql_duplicate_check_row['pay12_dt']."', '".$sql_duplicate_check_row['pay12_amt']."', '".$sql_duplicate_check_row['pay12_stat']."', '".$amountNeeded."', '".$comment."' )";
        mysql_query($sql_ins) or die(mysql_error());
        $flagg = 1;
        mysql_query("DELETE FROM buyer_info_mast where buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
        mysql_query("UPDATE conversation_log SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
        mysql_query("UPDATE file_upload SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
        mysql_query("UPDATE mail_log SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['buyer_id']."' ") or die(mysql_error());
    }
}
	
// insert into buyer_info_mast
if($flagg==0)
{
    $sql_ins = "insert into buyer_info_mast(buyer_id,full_name,email_1,ph_1,amount_requested,comment,buy_dt,priority_opt,data_insert_by,data_insert_dt) values('".$recid."','".mysql_escape_string($name)."','".$Email."','".$phone."','".$amountNeeded."','".$comment."', curdate(), 'NEW','Manual', curdate())";
    mysql_query($sql_ins) or die(mysql_error());
}
		
// insert into conversation log
if($comment!='')
{
    $sql_log = "insert into conversation_log(buyer_id, log_time, out_come, record_by, record_dt, log_subject) values ( '".$recid."', sysdate(), '".$comment."', 'Quotes', curdate(),'Lead Entry')";
    mysql_query($sql_log) or die(mysql_error());       
}

// auto mail sending to the signed up user start 
if ($to != '')
{
    $sql_mail = "insert into mail_log(mail_rcvr,mail_subject,mail_body,send_dt,from_nm,from_address) values ('".$to."','".$subject."', '".$comment."',sysdate(), 'Administrator','".$from."')";
    mysql_query($sql_mail) or die(mysql_error()); 
}
*/


        
// create customer_id 

$sql_sel = "select max(customer_id) mxid from customer_info";
$res_sel = mysql_query($sql_sel) or die(mysql_error()."11");
$rec_sel = mysql_fetch_assoc($res_sel);
if($rec_sel['mxid'] =='')
{
    $recid = 'BY00000001';
}
else
{
    $schlid = "select concat(substring(customer_id,1,2),lpad(max(convert(substring(customer_id,3),signed))+1,8,'0')) recid from customer_info";
    $schres = mysql_query($schlid) or die(mysql_error()."go select error");
    $schrec = mysql_fetch_assoc($schres);
    $recid = $schrec['recid'];
}

//echo $recid;
$flagg = 0;
		
// insert full_name, mobile_phone,email,amount_needed(ammount_requested), comment into customer_infoer 
/*if($Email!='')
{
    $sql_duplicate_check = "SELECT * from customer_info where p_eml1 = '".$Email."' ";
    $sql_duplicate_check_result = mysql_query($sql_duplicate_check) or die(mysql_error()."go select error");
    $sql_duplicate_check_row = mysql_fetch_assoc($sql_duplicate_check_result);
    if($sql_duplicate_check_row['customer_id']!='')
    {
        $sql_ins = "insert into customer_info "
                . "(customer_id, apply_dt, priority_opt, cust_apl_by, cust_upd_by,cust_upd_dt,agent,follow_up,funding_dt,amt_granted,"
                . "cred_review_fee_amt,cred_review_fee_dt_paid,cred_estab_fee_amt,cred_estab_fee_dt_paid,liq_fee_amt,"
                . "liq_fee_dt_paid,miscel_fee_amt,miscel_fee_dt_paid,df_cnslt_fee_amt,df_cnslt_fee_date_paid,cred_repair_fee_amt,cred_repair_fee_dt_paid,lst_log_info,"
                . "dur_info,calls_md_info,calls_conn_info,conv_min_info,eml_snd_info,eml_rcv_info,p_fl_nm,p_ph1,p_eml1,p_amt_req,p_cmt,p_fr_nm,p_mi_nm,"
                . "p_la_nm,p_ph2,p_hm_addr,p_ye_addr,p_city,p_state,p_zip,p_dob,p_ss,p_is_us,p_mam_maiden_nm,p_drv_lic,p_unq_id,p_hv_af,p_wh_af,p_hv_dod,"
                . "p_wh_dod,p_bnk_nm,b_stg,b_leg_nm,b_ent_typ,b_ind_typ,b_fed_tax_id,b_ph,b_fax,b_addr,b_city,b_state,b_zip,b_ye_busi,b_empl,b_reg_state,b_wb_site,"
                . "b_bnk_nm,b_acpt_cred_card,b_hv_cred_card,b_seeking,b_hv_not_show_cred_card,b_wht_bnk_issu_thm,b_hv_401k_ira,b_how_much,b_cred_usr,b_cred_pwd,"
                . "values ('".$recid."', curdate(),'"."','"."'New'"."','"."'Manual'"."','".$sql_duplicate_check_row['cust_upd_by']."','".$sql_duplicate_check_row['cust_upd_by']."','".$sql_duplicate_check_row['cust_upd_dt']."','".$sql_duplicate_check_row['agent']."','".$sql_duplicate_check_row['follow_up']."','".$sql_duplicate_check_row['funding_dt']."','".$sql_duplicate_check_row['amt_granted']
                ."','".$sql_duplicate_check_row['cred_review_fee_amt']."','".$sql_duplicate_check_row['cred_review_fee_dt_paid']."','".$sql_duplicate_check_row['cred_estab_fee_amt']."','".$sql_duplicate_check_row['cred_estab_fee_dt_paid']."','".$sql_duplicate_check_row['liq_fee_amt']."','".$sql_duplicate_check_row['liq_fee_dt_paid']
                ."','".$sql_duplicate_check_row['miscel_fee_amt']."','".$sql_duplicate_check_row['miscel_fee_dt_paid']."','".$sql_duplicate_check_row['df_cnslt_fee_amt']."','".$sql_duplicate_check_row['df_cnslt_fee_date_paid']."','".$sql_duplicate_check_row['cred_repair_fee_amt']."','".$sql_duplicate_check_row['cred_repair_fee_dt_paid']
                ."','".$sql_duplicate_check_row['lst_log_info']."','".$sql_duplicate_check_row['dur_info']."','".$sql_duplicate_check_row['calls_md_info']."','".$sql_duplicate_check_row['calls_conn_info']."','".$sql_duplicate_check_row['conv_min_info']."','".$sql_duplicate_check_row['eml_snd_info']
                ."','".$sql_duplicate_check_row['eml_rcv_info']."','".$name."','".$phone."','".$Email."','".$amountNeeded."','".$comment
                ."','".$sql_duplicate_check_row['p_fr_nm']."','".$sql_duplicate_check_row['p_mi_nm']."','".$sql_duplicate_check_row['p_la_nm']."','".$sql_duplicate_check_row['p_ph2']."','".$sql_duplicate_check_row['p_hm_addr']."','".$sql_duplicate_check_row['p_ye_addr']
                ."','".$sql_duplicate_check_row['p_city']."','".$sql_duplicate_check_row['p_state']."','".$sql_duplicate_check_row['p_zip']."','".$sql_duplicate_check_row['p_dob']."','".$sql_duplicate_check_row['p_ss']."','".$sql_duplicate_check_row['p_is_us']
                ."','".$sql_duplicate_check_row['p_mam_maiden_nm']."','".$sql_duplicate_check_row['p_drv_lic']."','".$sql_duplicate_check_row['p_unq_id']."','".$sql_duplicate_check_row['p_hv_af']."','".$sql_duplicate_check_row['p_wh_af']."','".$sql_duplicate_check_row['p_hv_dod']
                ."','".$sql_duplicate_check_row['p_wh_dod']."','".$sql_duplicate_check_row['p_bnk_nm']."','".$sql_duplicate_check_row['b_stg']."','".$sql_duplicate_check_row['b_leg_nm']."','".$sql_duplicate_check_row['b_ent_typ']."','".$sql_duplicate_check_row['b_ind_typ']
                ."','".$sql_duplicate_check_row['b_fed_tax_id']."','".$sql_duplicate_check_row['b_ph']."','".$sql_duplicate_check_row['b_fax']."','".$sql_duplicate_check_row['b_addr']."','".$sql_duplicate_check_row['b_city']."','".$sql_duplicate_check_row['b_state']
                ."','".$sql_duplicate_check_row['b_zip']."','".$sql_duplicate_check_row['b_ye_busi']."','".$sql_duplicate_check_row['b_empl']."','".$sql_duplicate_check_row['b_reg_state']."','".$sql_duplicate_check_row['b_wb_site']."','".$sql_duplicate_check_row['b_bnk_nm']
                ."','".$sql_duplicate_check_row['b_acpt_cred_card']."','".$sql_duplicate_check_row['b_hv_cred_card']."','".$sql_duplicate_check_row['b_seeking']."','".$sql_duplicate_check_row['b_hv_not_show_cred_card']."','".$sql_duplicate_check_row['b_wht_bnk_issu_thm']."','".$sql_duplicate_check_row['b_hv_401k_ira']
                ."','".$rec_sel['b_how_much']."','".$rec_sel['b_cred_usr']."','".$rec_sel['b_cred_pwd']."')";
                
	mysql_query($sql_ins) or die(mysql_error());
        $flagg = 1;
	mysql_query("DELETE FROM customer_info where customer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE conversation_log_info SET customer_id = '".$recid."' WHERE customer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE file_upload SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE mail_log SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
    }
}
			
if($phone!='')
{
    $sql_duplicate_check = "SELECT * from customer_info where p_ph1 = '".$phone."' ";
    $sql_duplicate_check_result = mysql_query($sql_duplicate_check) or die(mysql_error()."go select error");
    $sql_duplicate_check_row = mysql_fetch_assoc($sql_duplicate_check_result);
			
    if($sql_duplicate_check_row['customer_id']!='')
    {
          $sql_ins = "insert into customer_info "
                . "(customer_id, apply_dt, priority_opt, cust_apl_by, cust_upd_by,cust_upd_dt,agent,follow_up,funding_dt,amt_granted,"
                . "cred_review_fee_amt,cred_review_fee_dt_paid,cred_estab_fee_amt,cred_estab_fee_dt_paid,liq_fee_amt,"
                . "liq_fee_dt_paid,miscel_fee_amt,miscel_fee_dt_paid,df_cnslt_fee_amt,df_cnslt_fee_date_paid,cred_repair_fee_amt,cred_repair_fee_dt_paid,lst_log_info,"
                . "dur_info,calls_md_info,calls_conn_info,conv_min_info,eml_snd_info,eml_rcv_info,p_fl_nm,p_ph1,p_eml1,p_amt_req,p_cmt,p_fr_nm,p_mi_nm,"
                . "p_la_nm,p_ph2,p_hm_addr,p_ye_addr,p_city,p_state,p_zip,p_dob,p_ss,p_is_us,p_mam_maiden_nm,p_drv_lic,p_unq_id,p_hv_af,p_wh_af,p_hv_dod,"
                . "p_wh_dod,p_bnk_nm,b_stg,b_leg_nm,b_ent_typ,b_ind_typ,b_fed_tax_id,b_ph,b_fax,b_addr,b_city,b_state,b_zip,b_ye_busi,b_empl,b_reg_state,b_wb_site,"
                . "b_bnk_nm,b_acpt_cred_card,b_hv_cred_card,b_seeking,b_hv_not_show_cred_card,b_wht_bnk_issu_thm,b_hv_401k_ira,b_how_much,b_cred_usr,b_cred_pwd,"
                . "values ('".$recid."', curdate(),'"."','"."'New'"."','"."'Manual'"."','".$sql_duplicate_check_row['cust_upd_by']."','".$sql_duplicate_check_row['cust_upd_by']."','".$sql_duplicate_check_row['cust_upd_dt']."','".$sql_duplicate_check_row['agent']."','".$sql_duplicate_check_row['follow_up']."','".$sql_duplicate_check_row['funding_dt']."','".$sql_duplicate_check_row['amt_granted']
                ."','".$sql_duplicate_check_row['cred_review_fee_amt']."','".$sql_duplicate_check_row['cred_review_fee_dt_paid']."','".$sql_duplicate_check_row['cred_estab_fee_amt']."','".$sql_duplicate_check_row['cred_estab_fee_dt_paid']."','".$sql_duplicate_check_row['liq_fee_amt']."','".$sql_duplicate_check_row['liq_fee_dt_paid']
                ."','".$sql_duplicate_check_row['miscel_fee_amt']."','".$sql_duplicate_check_row['miscel_fee_dt_paid']."','".$sql_duplicate_check_row['df_cnslt_fee_amt']."','".$sql_duplicate_check_row['df_cnslt_fee_date_paid']."','".$sql_duplicate_check_row['cred_repair_fee_amt']."','".$sql_duplicate_check_row['cred_repair_fee_dt_paid']
                ."','".$sql_duplicate_check_row['lst_log_info']."','".$sql_duplicate_check_row['dur_info']."','".$sql_duplicate_check_row['calls_md_info']."','".$sql_duplicate_check_row['calls_conn_info']."','".$sql_duplicate_check_row['conv_min_info']."','".$sql_duplicate_check_row['eml_snd_info']
                ."','".$sql_duplicate_check_row['eml_rcv_info']."','".$name."','".$phone."','".$Email."','".$amountNeeded."','".$comment
                ."','".$sql_duplicate_check_row['p_fr_nm']."','".$sql_duplicate_check_row['p_mi_nm']."','".$sql_duplicate_check_row['p_la_nm']."','".$sql_duplicate_check_row['p_ph2']."','".$sql_duplicate_check_row['p_hm_addr']."','".$sql_duplicate_check_row['p_ye_addr']
                ."','".$sql_duplicate_check_row['p_city']."','".$sql_duplicate_check_row['p_state']."','".$sql_duplicate_check_row['p_zip']."','".$sql_duplicate_check_row['p_dob']."','".$sql_duplicate_check_row['p_ss']."','".$sql_duplicate_check_row['p_is_us']
                ."','".$sql_duplicate_check_row['p_mam_maiden_nm']."','".$sql_duplicate_check_row['p_drv_lic']."','".$sql_duplicate_check_row['p_unq_id']."','".$sql_duplicate_check_row['p_hv_af']."','".$sql_duplicate_check_row['p_wh_af']."','".$sql_duplicate_check_row['p_hv_dod']
                ."','".$sql_duplicate_check_row['p_wh_dod']."','".$sql_duplicate_check_row['p_bnk_nm']."','".$sql_duplicate_check_row['b_stg']."','".$sql_duplicate_check_row['b_leg_nm']."','".$sql_duplicate_check_row['b_ent_typ']."','".$sql_duplicate_check_row['b_ind_typ']
                ."','".$sql_duplicate_check_row['b_fed_tax_id']."','".$sql_duplicate_check_row['b_ph']."','".$sql_duplicate_check_row['b_fax']."','".$sql_duplicate_check_row['b_addr']."','".$sql_duplicate_check_row['b_city']."','".$sql_duplicate_check_row['b_state']
                ."','".$sql_duplicate_check_row['b_zip']."','".$sql_duplicate_check_row['b_ye_busi']."','".$sql_duplicate_check_row['b_empl']."','".$sql_duplicate_check_row['b_reg_state']."','".$sql_duplicate_check_row['b_wb_site']."','".$sql_duplicate_check_row['b_bnk_nm']
                ."','".$sql_duplicate_check_row['b_acpt_cred_card']."','".$sql_duplicate_check_row['b_hv_cred_card']."','".$sql_duplicate_check_row['b_seeking']."','".$sql_duplicate_check_row['b_hv_not_show_cred_card']."','".$sql_duplicate_check_row['b_wht_bnk_issu_thm']."','".$sql_duplicate_check_row['b_hv_401k_ira']
                ."','".$sql_duplicate_check_row['b_how_much']."','".$sql_duplicate_check_row['b_cred_usr']."','".$sql_duplicate_check_row['b_cred_pwd']."','".$sql_duplicate_check_row['b_hv_not_show_cred_card']."','".$sql_duplicate_check_row['b_wht_bnk_issu_thm']."','".$sql_duplicate_check_row['b_hv_401k_ira']."')";

   	mysql_query($sql_ins) or die(mysql_error());
        $flagg = 1;
	mysql_query("DELETE FROM customer_info where customer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE conversation_log_info SET customer_id = '".$recid."' WHERE customer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE file_upload SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
	mysql_query("UPDATE mail_log SET buyer_id = '".$recid."' WHERE buyer_id = '".$sql_duplicate_check_row['customer_id']."' ") or die(mysql_error());
    }
}*/
	
// insert into customer_info
if($flagg==0)
{
    $sql_ins = "insert into customer_info(customer_id,p_fl_nm,p_eml1,p_ph1,p_amt_req,p_cmt,apply_dt,priority_opt,cust_apl_by) values('".$recid."','".mysql_escape_string($name)."','".$Email."','".$phone."','".$amountNeeded."','".$comment."', curdate(), 'NEW','Manual')";
 //   echo $sql_ins;
    mysql_query($sql_ins) or die(mysql_error());
}
		
// insert into conversation log
if ($comment != '')
{
    $sql_log = "insert into conversation_log_info(customer_id, log_time, out_come, record_by, record_dt, log_subject) values ( '".$recid."', sysdate(), '".$comment."', 'Quotes', sysdate(),'Lead Entry')";
//    echo $sql_log;
    mysql_query($sql_log) or die(mysql_error());       

}

// auto mail sending to the signed up user start 
if ($to != '')
{
    $sql_mail = "insert into mail_log_info(customer_id,mail_rcvr,mail_subject,mail_body,send_dt,from_nm,from_address) values ('".$recid."','".$to."','".mysql_real_escape_string($subject)."', '".mysql_real_escape_string($comment)."',sysdate(), 'Administrator','".$from."')";
    mysql_query($sql_mail) or die(mysql_error()); 
}

/********************************************************************************************/   
if ($sent==true) {
	$data ="{\"status\": \"OK\", \"msg\": \"Thank you for your submit.\"}";
} else {
	$data ="{\"status\": \"ERR\", \"msg\": \"server error has occurred..Please try to submit again.\"}";
}
echo $data;
                                                                       
function GetField($item) {
	if(!isset($_POST[$item])) {
		return "";
	}
	return $_POST[$item];
}
function Validation() {
	if ($GLOBALS['name'] =="") return false;
	if ($GLOBALS['phone'] =="") return false;
	if ($GLOBALS['Email'] == "") return false;
	if ($GLOBALS['amountNeeded'] =="") return false;        
	return true;
}
?>