<div class="dbpac-form-style">
	<h1>Sign in<span>Sign in to your account</span></h1>

	<!-- Show errors if there are any -->
	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
		<?php foreach ( $attributes['errors'] as $error ) : ?>
			<p class="login-error">
				<?php echo $error; ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<!-- Show logged out message if user just logged out -->
	<?php if ( $attributes['logged_out'] ) : ?>
		<p class="login-info">
			<?php _e( 'You have signed out. Would you like to sign in again?', 'dbpac-login' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['registered'] ) : ?>
		<p class="login-info">
			<?php
				printf(
					__( 'You have successfully registered.', 'dbpac-login' ),
					get_bloginfo( 'name' )
				);
			?>
		</p>
	<?php endif; ?>

	<!-- Show password sent message -->
	<?php if ( $attributes['lost_password_sent'] ) : ?>
	    <p class="login-info">
	        <?php _e( 'Check your email for a link to reset your password.', 'dbpac-login' ); ?>
	    </p>
	<?php endif; ?>

	<!-- Show reset password message -->
	<?php if ( $attributes['password_updated'] ) : ?>
	    <p class="login-info">
	        <?php _e( 'Your password has been changed. You can sign in now.', 'dbpac-login' ); ?>
	    </p>
	<?php endif; ?>
	<div class="inner-wrap">
	<form name="loginform" id="loginform" action="<?php echo wp_login_url(); ?>" method="post">
			
		<p class="login-username">
			<label for="user_login">Email <span style="color: #F44336;">*</span></label>
			<input type="text" name="log" id="user_login" class="input" value="" size="20">
		</p>
		<p class="login-password">
			<label for="user_pass">Password <span style="color: #F44336;">*</span></label>
			<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
		</p>
		
		<p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>
		<p class="login-submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Sign In">
			<input type="hidden" name="redirect_to" value="">
		</p>
		
	</form>
	<?php
	
		wp_login_form(
			array(
				
				//'label_username' => __( 'Email', 'dbpac-login' ),
				//'label_log_in' => __( 'Sign In', 'dbpac-login' ),
				'echo' => false,
				'redirect' => $attributes['redirect'],				
			)
		);
	
	?>
	</div>		
	<a class="forgot-password" href="<?php echo wp_lostpassword_url(); ?>">
		<?php _e( 'Forgot your password?', 'dbpac-login' ); ?>
	</a>
	<a href="<?php echo home_url( 'member-register' ); ?>" >
		<?php _e(' | Sign up for an account.'); ?>
	</a>
		
	
</div>
