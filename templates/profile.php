<div class="profile-style" id="instruction" >

	<center><h4>Instruction on how to enroll your students</h4></center>
	<p>
		This is a two step process:
		<br/>
		First step: Add your students information by going to "<a href="<?php echo esc_url(home_url('add-student')); ?>"><strong>Add Student</strong></a>".
		<br/>
		Second step: Once you finish adding your students, "<a href="<?php echo esc_url(home_url('enroll-contest')); ?>"><strong>Enroll Contest</strong></a>" to enroll your students.
	</p>	
	<p style="color:red;"> *If you are updating your profile please update the form below.</p>
</div>


<center>
<div id="profile_table">
	<center style="background-color: #2A88AD/*#90CAF9*/; color: white; padding:20px; margin:1px auto; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.13); -moz-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.13);	-webkit-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.13);">
		<strong>PERSONAL INFORMATION</strong>
	</center>
	
	<table style= "box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.13); -moz-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.13);	-webkit-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.13);" >
		<tr style="background-color: #FFF; border: 1px solid #F9F9F9; padding:20px;">
			<td>
				<strong><?php _e( 'First Name ', 'dbpac-login' ); ?></strong>
			</td>
			<td>				
				<?php global $current_user; wp_get_current_user(); echo $current_user->first_name; ?>	
			</td>		
		</tr>
		<tr style="background-color: #FFF; border: 1px solid #F9F9F9; padding:20px;">
			<td>
				<strong><?php _e( 'Last Name ', 'dbpac-login' ); ?></strong>
			</td>
			<td>				
				<?php global $current_user; wp_get_current_user(); echo $current_user->last_name; ?>	
			</td>		
		</tr>
		<tr style="background-color: #FFF; border: 1px solid #F9F9F9; padding:20px;">
			<td>
				<strong><?php _e( 'Email ', 'dbpac-login' ); ?></strong>
			</td>
			<td>				
				<?php global $current_user; wp_get_current_user(); echo $current_user->user_email; ?>
			</td>								
		</tr>
		<tr style="background-color: #FFF; border: 1px solid #F9F9F9; padding:20px;">
			<td>
				<strong><?php _e( 'Mailing Address ', 'dbpac-login' ); ?></strong>
			</td>
			<td>				
				<?php global $current_user; wp_get_current_user(); $user_address = get_user_meta($current_user->ID, 'user_address', true); echo $user_address; ?>
			</td>		
		</tr>
		<tr style="background-color: #FFF; border: 1px solid #F9F9F9; padding:20px;">
			<td>
				<strong><?php _e( 'Phone ', 'dbpac-login' ); ?></strong>
			</td>
			<td>				
				<?php global $current_user; wp_get_current_user(); $user_phone = get_user_meta($current_user->ID, 'user_phone', true); echo $user_phone; ?>
			</td>				
		</tr>
	</table>
</div>
</center>

<!-- START-Currently not allowing user to change the profile yet -->
<div class="dbpac-form-style" id="update_profile_form" >
    <h1>Personal Information<span>Update only the field(s) that you want to update.</span></h1>
    <form id="updateprofileform" name="updateprofileform" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <div class="inner-wrap">  
    	
    	<p class="form-row">       
	        <label for="first_name"><?php _e( 'First Name ', 'dbpac-login' ); ?><span class="required">*</span></label>
	        <input type="text" name="first_name" id="first_name" value="<?php global $current_user; wp_get_current_user(); echo $current_user->first_name; ?>" >
        </p>

        <p class="form-row">
	        <label for="last_name"><?php _e( 'Last Name ', 'dbpac-login' ); ?><span class="required">*</span></label>
	        <input type="text" name="last_name" id="last-name" value="<?php global $current_user; wp_get_current_user(); echo $current_user->last_name; ?>" > 
        </p>
        
        <p class="form-row">
	        <label for="email"><?php _e( 'Email ', 'dbpac-login' ); ?><span class="required">*</span></label>
	        <input type="text" name="email" id="email" value="<?php global $current_user; wp_get_current_user(); echo $current_user->user_email; ?>" >
        </p>

        <input type="hidden" name="country" id="country" value="US">   

        <p class="form-row">
	        <label for="address"><?php _e( 'Address ', 'dbpac-login' ); ?><span class="required">*</span></label>
	        <input type="text" name="address" id="freeform" value="<?php global $current_user; wp_get_current_user(); $user_address = get_user_meta($current_user->ID, 'user_address', true); echo $user_address; ?>" /> <span id="tick" style="color:red; float: right;"></span> 
        </p>

        <p class="form-row">
	        <label for="phone"><?php _e( 'Phone ', 'dbpac-login' ); ?><span class="required">*</span></label>
	        <input type="text" name="phone" id="phone" value="<?php global $current_user; wp_get_current_user(); $user_phone = get_user_meta($current_user->ID, 'user_phone', true); echo $user_phone; ?>" >
        </p>
      
    	<input type="hidden" name="action" value="update_profile">
    
        <p class="signup-submit">
            <input type="submit" name="update-profile" value="Update" /> 
        </p>   
    </div>    
    </form>
</div>
<!-- END-Currently not allowing user to change the profile yet -->



 	


		
