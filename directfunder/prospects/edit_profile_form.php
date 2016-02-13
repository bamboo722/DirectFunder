<form action="updateprofile.php" method="post" enctype="multipart/form-data" id="UploadForm">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
      <li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
      <li><a href="#email_template" data-toggle="tab">Email Template</a></li>
      <li><a href="#email_sig_template" data-toggle="tab">Email Signature Template</a></li>
      <!--li><a href="#idea_post" data-toggle="tab">Idea</a></li-->
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade in active" id="profile">         
            <div class = "col-sm-4">
                <div class="form-group float-label-control">
                    <div >
                    	<br>                                                
                        <div class="shortpreview" id="imagePreview" style='<?php if (isset($rws['user_avatar'])) echo "background-image: url(userprofile/userfiles/avatars/".$rws["user_avatar"].")"; else echo "background-image: url(userprofile/userfiles/avatars/default.jpg)";?>'>
                    	</div>
                    </div>
                    <div>    
                    	<br>
                        <input name="ImageFile" type="file" id="uploadFile"/>                        
                    </div>                 
                </div>
            </div>  
            <div class = "col-sm-6">
            	<div class="form-group float-label-control">    
            		<br>                  
	            	<table style="width:100%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">First Name</label></td>
		    				<td><input type="text" class="form-control" placeholder="<?php echo $rws['user_firstname'];?>" name="user_firstname" value="<?php echo $rws['user_firstname'];?>"></td>
		            	</tr>
		            </table>
	           	</div>
	           	<div class="form-group float-label-control">   
	           		<table style="width:100%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">Last Name</label></td>
		    				<td><input type="text" class="form-control" placeholder="<?php echo $rws['user_lastname'];?>" name="user_lastname" value="<?php echo $rws['user_lastname'];?>"></td>
		            	</tr>
	            	</table>
	            </div>	            	 
	       
	            <div class="form-group float-label-control">   
	            	<table style="width:100%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">User Name</label></td>
		    				<td><input type="text" class="form-control" placeholder="<?php echo $rws['user_username'];?>" name="user_username" value="<?php if (isset($rws['user_login'])) echo $rws['user_username']; else echo $_SESSION['user_login'];?>"></td>
		            	</tr>
	            	</table>
	            </div>
	            	 
	            <div class="form-group float-label-control">   
	            	<table style="width:100%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">Email</label></td>
		    				<td><input type="text" class="form-control" placeholder="<?php echo $rws['user_email'];?>" name="user_email" value="<?php if (isset($rws['user_email'])) echo $rws['user_email']; else echo $_SESSION['google_acc_nm'];?>"></td>
		            	</tr>
	            	</table>
	           	</div>
	            	 	
	            <div class="form-group float-label-control"> 
		            <table style="width:100%;">  
		            	<tr>
		            		<td style="width:25%;"><label for="">Google Voice Number</label></td>
		    				<td><input type="text" class="form-control" placeholder="<?php echo $rws['user_goo_voi_num'];?>" name="user_goo_voi_num" value="<?php if (isset($rws['user_goo_voi_num'])) echo $rws['user_goo_voi_num']; else echo $_SESSION['google_voice_ph']?>"></td>
		            	</tr>
		            </table>
	            </div>
	            <div class="form-group float-label-control"> 
	            	<center>
	            		<button class="btn btn-primary ladda-button" data-style="zoom-in" type="submit" name="submit"  value="SaveProfile" />Save Your Profile</button>	
	            	</center>
	            </div>           
            </div>
        </div>
        <div class="tab-pane fade" id="email_template">
        	 <div class = "col-sm-12">
            	<div class="form-group float-label-control">    
            		<br>                  
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">Title</label></td>
		    				<td><input type="text" class="form-control" placeholder="<?php echo $rws['eml_templ_subj'];?>" name="eml_templ_subj" value="<?php echo $rws['eml_templ_subj'];?>"></td>
		            	</tr>
		            </table>
	           	</div>
	           	<div class="form-group float-label-control">   
	           		<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">Content</label></td>
		            		<td>
		            			
		            			<textarea name="eml_templ_cont" class="form-control" cols="50" rows="3" style="background:#F9F8C2;"><?php if (isset($rws['eml_templ_cont'])) 
		            		   				  {
                                                  echo $rws['eml_templ_cont'];
                                              }?></textarea>
                                
                            </td>		    				
		            	</tr>
	            	</table>
	            </div>	            	 
	       
	            <div class="form-group float-label-control">   
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">Attach File</label></td>
		    				<td><input name="eml_templ_att"  class="form-control" style="border:0px" type="file" id="eml_templ_att_uploadFile"/></td>		    				
		            	</tr>
	            	</table>
	            </div>
	            	 
	            <!--div class="form-group float-label-control">   
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="">While Label</label></td>
		    				<td> 
		    					<div class="form-group float-label-control">
				                    <div >
				                    	<br>                                                
				                        <div class="shortpreview" id="eml_templ_white_label" style='<?php if (isset($rws['eml_templ_white'])) echo "background-image: url(userprofile/userfiles/email_white_label/".$rws["eml_templ_white"].")"; else echo "background-image: url(userprofile/userfiles/email_white_label/default.jpg)";?>'>
				                    	</div>
				                    </div>
				            		<div>    
				                    	<br>
					                     <input name="eml_templ_white" class="form-control" style="border:0px" type="file" id="eml_templ_white_uploadFile"/>                        
					                 </div>                 
					            </div>
					       	</td>		    			
		            	</tr>
	            	</table>
	           	</div-->           	 	
	          	 <div class="form-group float-label-control"> 
	            	<center>
	            		<button class="btn btn-primary ladda-button" data-style="zoom-in" type="submit" name="submit"  value="SaveEmailTemplate" />Save Email Template</button>	
	            	</center>
	            </div>     
            </div>           
        </div>
		<div class="tab-pane fade" id="email_sig_template">
			<div class = "col-sm-12">
            	<div class="form-group">    
            		<br>                  
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_mobile_ph">Mobile Phone Number</label></td>
		    				<td><input type="text" class="form-control" value="<?php echo $rws['eml_sig_mobile_ph'];?>" name="eml_sig_mobile_ph" id="eml_sig_mobile_ph"></td>
		            	</tr>
		            </table>
	           	</div>
	           	<div class="form-group ">    
            		
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_office_ph">Office Phone Number</label></td>
		    				<td><input type="text" class="form-control" value="<?php echo $rws['eml_sig_office_ph'];?>" name="eml_sig_office_ph" id="eml_sig_office_ph"></td>
		            	</tr>
		            </table>
	           	</div>
	           	<div class="form-group ">   
	           		<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_eml1">Email 1</label></td>
		            		<td><input class="form-control" value="<?php echo $rws['eml_sig_eml1'];?>" name="eml_sig_eml1" id="eml_sig_eml1"></td>
		            	</tr>
	            	</table>
	            </div>
	             	<div class="form-group ">   
	           		<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_eml2">Email 2</label></td>
		            		<td><input class="form-control" value="<?php echo $rws['eml_sig_eml2'];?>" name="eml_sig_eml2" id="eml_sig_eml2"></td>
		            	</tr>
		            </table>
	            </div>
	            <div class="form-group">    
            		            
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_buss_addr">Business Address</label></td>
		    				<td><input type="text" class="form-control" value="<?php echo $rws['eml_sig_buss_addr'];?>" name="eml_sig_buss_addr" id="eml_sig_buss_addr"></td>
		            	</tr>
		            </table>
	           	</div>
	           	<div class="form-group">   
	           		<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_fax">Fax</label></td>
		            		<td><input type="text" class="form-control" value="<?php echo $rws['eml_sig_fax'];?>" name="eml_sig_fax" id="eml_sig_fax"></td>
		            	</tr>
	            	</table>
	            </div>	            	 
	       
	            <div class="form-group">   
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_eml_photo">Attach Photo</label></td>
		    				<td><input class="form-control"  style="border:0px" type="file" id="eml_sig_photo" name="eml_sig_photo"/></td>
		    				<td style="width:10%;" align="right"><label for="">Old file</label></td>
		    				<td align="left"><input class="form-control" value="<?php echo $rws['eml_sig_photo'];?>" style="border:0px" type="text"/></td>		    				
		            	</tr>
	            	</table>
	            </div>
	            
	            <div class="form-group">   
	            	<table style="width:90%;">
		            	<tr>
		            		<td style="width:25%;"><label for="eml_sig_logo">Attach Logo</label></td>
		    				<td><input name="eml_sig_logo"  class="form-control" style="border:0px" type="file" id="eml_sig_logo" name="eml_sig_logo"/></td>		    				
		    				<td style="width:10%;" align="right"><label for="">Old file</label></td>
		    				<td align="left"><input class="form-control" value="<?php echo $rws['eml_sig_logo'];?>" style="border:0px" type="text"/></td>		    				
		            	</tr>
	            	</table>
	            </div>	 
				<div class="form-group"> 
	            	<center>
	            		<button class="btn btn-primary ladda-button" data-style="zoom-in" type="submit" name="submit"  value="SaveEmailSignature" />Save Email Signature</button>	
	            	</center>
	            </div>     
            </div>           
        </div>

   	</div>         
</form>