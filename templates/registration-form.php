<div class="dbpac-form-style">
    <h1>Sign Up<span>Sign up an account for enrolling your students.</span></h1>

    <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
        <?php foreach ( $attributes['errors'] as $error ) : ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
 
    <form id="signupform" name="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
    <div class="inner-wrap"> 
        <p class="form-row">
            <label for="first_name"><?php _e( 'First Name ', 'dbpac-login' ); ?><span style="color: #F44336;">*</span></label>
            <input type="text" name="first_name" id="first-name" placeholder="John">
        </p>
 
        <p class="form-row">
            <label for="last_name"><?php _e( 'Last Name ', 'dbpac-login' ); ?><span style="color: #F44336;">*</span></label>
            <input type="text" name="last_name" id="last-name" placeholder="Doe">
        </p>

        <p class="form-row">
            <label for="email"><?php _e( 'Email ', 'dbpac-login' ); ?><span style="color: #F44336;">*</span></label>
            <input type="text" name="email" id="email" placeholder="jdoe@example.com">
        </p>

        <p class="form-row">
            <label for="password"><?php _e( 'Password ', 'dbpac-login' ); ?><span style="color: #F44336;">*</span></label>
            <input type="password" name="password" id="password" placeholder="******">
        </p>

        <input type="hidden" name="country" id="country" value="US">

        <p class="form-row">
            <label for="address"><?php _e( 'Mailing Address ', 'dbpac-login' ); ?><span style="color: #F44336;">*</span></label>
            <input type="text" name="address" id="freeform" placeholder="Enter an address"><span id="tick" style="color:red;"></span>
        </p>

         <p class="form-row">
            <label for="phone"><?php _e( 'Phone ', 'dbpac-login' ); ?><span style="color: #F44336;">*</span></label>
            <input type="text" name="phone" id="phone" placeholder="000 000 0000">
        </p>
        
        <?php //if ( $attributes['recaptcha_site_key'] ) : ?>
        <!--
            <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="<?php echo $attributes['recaptcha_site_key']; ?>"></div>
            </div>
        -->
        <?php //endif; ?>
        
        <p class="signup-submit">
            <input type="submit" name="submit" class="register-button"
                   value="<?php _e( 'Sign Up', 'dbpac-login' ); ?>"/>
        </p>
        <p>Upon signing up an account, you agree to our <a href="http://dbpac.org/terms">Terms of Use</a>.</p>
    </div>
    </form>
</div>

