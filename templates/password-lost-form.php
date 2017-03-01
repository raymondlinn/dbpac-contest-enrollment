<div class="dbpac-form-style">
    <h1>Forgot Your Password?</h1>
    
    <center>
        <p style="color: red ;">
            <?php
                _e(
                    "Enter your email address and we'll send you a link you can use to pick a new password.",
                    'dbpac-login'
                );
            ?>
        </p>
    </center>

    <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
        <?php foreach ( $attributes['errors'] as $error ) : ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
 
    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
    <div class="inner-wrap">
        <p class="form-row">
            <label for="user_login"><?php _e( 'Email', 'dbpac-login' ); ?></label>
            <input type="text" name="user_login" id="user_login">
        </p>
 
        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button"
                   value="<?php _e( 'Reset Password', 'dbpac-login' ); ?>"/>
        </p>
    </div>
    </form>
</div>