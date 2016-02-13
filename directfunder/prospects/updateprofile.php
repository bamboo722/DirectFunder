<?php
	@session_start();


	if(!isset($_SESSION['user_login']) and !isset($_COOKIE['cookie_login']))//session store admin name
	{
	    header("Location: index.php");//login in AdminLogin.php
	}

	require_once("includes/dbconnect.php");

    $temp=$_SESSION['user_login'];
    if(isset($_POST)){
        if ($_POST['submit'] == "PostMyIdea") // post ieda
        {
			$post_time=date('Y-m-d H:i:s', time());
	        $post_content=$_POST['idea_content'];
	       // echo $post_content;
	        $sql_ins=sprintf("INSERT INTO post_ideas (user_name,user_group,post_time,post_content) values ('%s', '%s', '%s', '%s');",
	        		$temp, $_SESSION['user_group'], $post_time, addslashes($post_content));
        	mysql_query($sql_ins)or die(mysql_error());           
            header("location:editprofile.php?user_username='".$temp."'");
			
		}else if ($_POST['submit'] == "SaveEmailTemplate")	// save Template
		{
		  
	        $eml_templ_subj=$_POST['eml_templ_subj'];
	        $eml_templ_cont=$_POST['eml_templ_cont'];
	        
	        /* Email Template File upload */
	        $Destination = 'userprofile/userfiles/email_attaches';
	        if(!isset($_FILES['eml_templ_att']) || !is_uploaded_file($_FILES['eml_templ_att']['tmp_name'])){
	            $New_EmlTemlImageName= 'default.png';
	            move_uploaded_file($_FILES['eml_templ_att']['tmp_name'], "$Destination/$New_EmlTemlImageName");
	        }
	        else{
	            $RandomNum   = rand(0, 9999999999);
	            $ImageName = str_replace(' ','-',strtolower($_FILES['eml_templ_att']['name']));
	            $ImageType = $_FILES['eml_templ_att']['type'];
	            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
	            $ImageExt = str_replace('.','',$ImageExt);
	            $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
	            $New_EmlTemlImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
	            move_uploaded_file($_FILES['eml_templ_att']['tmp_name'], "$Destination/$New_EmlTemlImageName");
	        }
	        
	        /* Email Template White Label upload */
	        $Destination = 'userprofile/userfiles/email_white_labels';
	        if(!isset($_FILES['eml_templ_white']) || !is_uploaded_file($_FILES['eml_templ_white']['tmp_name'])){
	            $New_EmlWhiteImageName= 'default.png';
	            move_uploaded_file($_FILES['eml_templ_white']['tmp_name'], "$Destination/$New_EmlWhiteImageName");
	        }
	        else{
	            $RandomNum   = rand(0, 9999999999);
	            $ImageName = str_replace(' ','-',strtolower($_FILES['eml_templ_white']['name']));
	            $ImageType = $_FILES['eml_templ_white']['type'];
	            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
	            $ImageExt = str_replace('.','',$ImageExt);
	            $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
	            $New_EmlWhiteImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
	            move_uploaded_file($_FILES['eml_templ_white']['tmp_name'], "$Destination/$New_EmlWhiteImageName");
	        }
	        
	        $sql_upd="UPDATE profile_info SET eml_templ_att='$New_EmlTemlImageName',eml_templ_white='$New_EmlWhiteImageName',eml_templ_subj='$eml_templ_subj',eml_templ_cont='$eml_templ_cont' WHERE user_username = '$temp'";
	        $sql_ins="INSERT INTO profile_info (eml_templ_att,eml_templ_white,eml_templ_subj,eml_templ_cont) VALUES ('$New_EmlTemlImageName','$New_EmlWhiteImageName','$eml_templ_subj','$eml_templ_cont')";
	        $result = mysql_query("SELECT user_id FROM profile_info WHERE user_username = '$temp'");
	        if( mysql_num_rows($result) > 0) {
	           // if(!empty($_FILES['ImageFile']['name'])){
	            	
			        mysql_query($sql_upd)or die(mysql_error());           
	                header("location:editprofile.php?user_username='".$temp."'");
	           // }
	        } 
	        else {
	        	mysql_query($sql_ins)or die(mysql_error());           
	            
	            header("location:editprofile.php?user_username='".$temp."'");
	        }  			
		}else if ($_POST['submit'] == "SaveEmailSignature")	// save signature
		{
		  
	        $eml_sig_mobile_ph=$_POST['eml_sig_mobile_ph'];
	        $eml_sig_office_ph=$_POST['eml_sig_office_ph'];
	        $eml_sig_eml1=$_POST['eml_sig_eml1'];
	        $eml_sig_eml2=$_POST['eml_sig_eml2'];
	        $eml_sig_buss_addr=$_POST['eml_sig_buss_addr'];
	        $eml_sig_fax=$_POST['eml_sig_fax'];
	        
	        /* Email Signature Photo upload */
	        $Destination = 'userprofile/userfiles/email_signatures/photos';
	        if(!isset($_FILES['eml_sig_photo']) || !is_uploaded_file($_FILES['eml_sig_photo']['tmp_name'])){
	            $New_EmlSigPhotoImageName= 'default.gif';
	            move_uploaded_file($_FILES['eml_sig_photo']['eml_sig_photo'], "$Destination/$New_EmlSigPhotoImageName");
	        }
	        else{
	            $RandomNum   = rand(0, 9999999999);
	            $ImageName = str_replace(' ','-',strtolower($_FILES['eml_sig_photo']['name']));
	            $ImageType = $_FILES['eml_sig_photo']['type'];
	            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
	            $ImageExt = str_replace('.','',$ImageExt);
	            $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
	            $New_EmlSigPhotoImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
	            move_uploaded_file($_FILES['eml_sig_photo']['tmp_name'], "$Destination/$New_EmlSigPhotoImageName");
	        }
	        
	        $eml_sig_photo_url = "$Destination/$New_EmlSigPhotoImageName";
	        /* Email Signature Logo upload */
	        $Destination = 'userprofile/userfiles/email_signatures/logos';
	        if(!isset($_FILES['eml_sig_logo']) || !is_uploaded_file($_FILES['eml_sig_logo']['tmp_name'])){
	            $New_EmlSigLogImageName= 'default.gif';
	            move_uploaded_file($_FILES['eml_sig_logo']['tmp_name'], "$Destination/$New_EmlSigLogImageName");
	        }
	        else{
	            $RandomNum   = rand(0, 9999999999);
	            $ImageName = str_replace(' ','-',strtolower($_FILES['eml_sig_logo']['name']));
	            $ImageType = $_FILES['eml_sig_logo']['type'];
	            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
	            $ImageExt = str_replace('.','',$ImageExt);
	            $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
	            $New_EmlSigLogImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
	            move_uploaded_file($_FILES['eml_sig_logo']['tmp_name'], "$Destination/$New_EmlSigLogImageName");
	        }
	        
	        $eml_sig_log_url = "$Destination/$New_EmlSigLogImageName";
	        
	        $sql_upd=sprintf("UPDATE profile_info SET eml_sig_photo='%s',eml_sig_logo='%s',eml_sig_mobile_ph='%s',eml_sig_office_ph='%s',eml_sig_eml1='%s',eml_sig_eml2='%s',eml_sig_buss_addr='%s',eml_sig_fax='%s' WHERE user_username = '%s'",$eml_sig_photo_url,$eml_sig_log_url,$eml_sig_mobile_ph,$eml_sig_office_ph,$eml_sig_eml1,$eml_sig_eml2,$eml_sig_buss_addr,$eml_sig_fax,$temp);
	        $sql_ins=sprintf("INSERT INTO profile_info (user_username,user_email, eml_sig_photo,eml_sig_logo,eml_sig_mobile_ph,eml_sig_office_ph,eml_sig_eml1,eml_sig_eml2,eml_sig_buss_addr,eml_sig_fax) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$temp,$eml_sig_eml1,$eml_sig_photo_url,$eml_sig_log_url,$eml_sig_mobile_ph,$eml_sig_office_ph,$eml_sig_eml1,$eml_sig_eml2,$eml_sig_buss_addr,$eml_sig_fax,$temp);
	        $result = mysql_query("SELECT user_id FROM profile_info WHERE user_username = '$temp'");
	        if( mysql_num_rows($result) > 0) {
	           // if(!empty($_FILES['ImageFile']['name'])){
	            	
			        mysql_query($sql_upd)or die(mysql_error());           
	                header("location:editprofile.php?user_username='".$temp."'");
	           // }
	        } 
	        else {
	        	mysql_query($sql_ins)or die(mysql_error());           
	            
	            header("location:editprofile.php?user_username='".$temp."'");
	        }  			
		}
		else  if ($_POST['submit'] == "SaveProfile")	// save profile
        {
			$user_firstname=$_POST['user_firstname'];
	        $user_lastname=$_POST['user_lastname'];
	        $user_email=$_POST['user_email'];
	        
	        if (isset($_POST['user_email']))
				$_POST['user_email'] = preg_replace("/[^0-9]*/s", "",$_POST['user_email']);
	
	        $user_goo_voi_num=$_POST['user_goo_voi_num'];
	        $user_username=$_POST['user_username'];
	       
	        $eml_templ_subj=$_POST['eml_templ_subj'];
	        $eml_templ_cont=$_POST['eml_templ_cont'];
	        /* Profile Image upload */
	        $Destination = 'userprofile/userfiles/avatars';
	        if(!isset($_FILES['ImageFile']) || !is_uploaded_file($_FILES['ImageFile']['tmp_name'])){
	            $New_ProfileImageName= 'default.png';
	            move_uploaded_file($_FILES['ImageFile']['tmp_name'], "$Destination/$New_ProfileImageName");
	        }
	        else{
	            $RandomNum   = rand(0, 9999999999);
	            $ImageName = str_replace(' ','-',strtolower($_FILES['ImageFile']['name']));
	            $ImageType = $_FILES['ImageFile']['type'];
	            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
	            $ImageExt = str_replace('.','',$ImageExt);
	            $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
	            $New_ProfileImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
	            move_uploaded_file($_FILES['ImageFile']['tmp_name'], "$Destination/$New_ProfileImageName");
	        }
	        
	        $sql_upd="UPDATE profile_info SET user_avatar='$New_ProfileImageName',user_goo_voi_num='$user_goo_voi_num',user_firstname='$user_firstname',user_lastname='$user_lastname',user_email='$user_email',user_goo_voi_num='$user_goo_voi_num' WHERE user_username = '$temp'";
	        $sql_ins="INSERT INTO profile_info (user_avatar,user_username,user_firstname,user_lastname,user_email,user_goo_voi_num) VALUES ('$New_ProfileImageName','$user_username','$user_firstname','$user_lastname','$user_email','$user_goo_voi_num')";
	        $result = mysql_query("SELECT user_username FROM profile_info WHERE user_username = '$temp'");
	        if( mysql_num_rows($result) > 0) {
	           // if(!empty($_FILES['ImageFile']['name'])){
	            	
			        mysql_query($sql_upd)or die(mysql_error());           
	                header("location:editprofile.php?user_username='".$temp."'");
	           // }
	        } 
	        else {
	        	mysql_query($sql_ins)or die(mysql_error());           
	            
	            header("location:editprofile.php?user_username='".$temp."'");
	        }  			
			
		}
   
        header("location:editprofile.php?user_username='".$temp."'&request=profile-update&status=success");
    }    
    
?>