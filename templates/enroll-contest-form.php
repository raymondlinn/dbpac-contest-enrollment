<!--
<div id="enroll_contest">
<center>
    <h4>Please Select the Dropdown to Enroll the Contest </h4>
    <select class="dbpac-form-style" id="select_contest">
        <option value="" disabled selected>Select your Contest</option>
        <option id="opt_dbpac" value="sel_dbpac">DBPAC Contest</option>
        <option id="opt_baroque" value="sel_baroque">Baroque Contest</option>
    </select>
</center>
</div>
-->
<!-- START-currently turned off since the season is for Baroque Festival -->
<div id="sel_dbpac" class="contest_form">
    <div class="dbpac-form-style">
    <h1>Enroll Contest<span>Enroll your students in DBPAC</span></h1>
    <form id="enroll_dbpac" name="enroll_dbpac" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">    
        <div class="inner-wrap">
            <label>Instrument or Division <span class="required">*</span>
            <div id="division-select"> 
            </div> 
            </label>               
            <label>Song Title <span class="required">*</span>
            <input type="text" name="song_title" placeholder="Sonatinas. Op 36" />
            </label>
            <label>Song Duration (in mm:ss format) <span class="required">*</span>
            <input type="text" name="song_duration" placeholder="00:00" />
            </label>
            <label>Composer's Name <span class="required">*</span>
            <input type="text" name="composer_name" placeholder="Clemnti Muzio" />          
            </label>            
	    <input type="hidden" name="is_morning" value="no">

            <input type="hidden" name="contest" id="contest" value="dbpac">
        
            <?php
                do_action('populate_students_for_enroll_form', true);                
            ?>                     
            
        
    </form>
    </div>
</div>
<!-- END-currently turned off since the season is for Baroque Festival -->

<?php 
if(is_user_logged_in())
    {
        global $current_user;
        $current_user = wp_get_current_user();
        if($current_user->user_email == "joanna@teachers.org") { // joanna@teachers.org
            echo "
                <div id='sel_baroque' class='contest_form'>
                    <div class='dbpac-form-style'>
                    <h1>Enroll in Baroque Festival<span>If you are entering a group please choose Mixed Ensemble</span></h1>
            ";
?>
                    <form id="enroll_baroque" name="enroll_baroque" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post"> 
<?php
            echo "   
                        <div class='inner-wrap'>
                            <label>Instrument or Division <span class='required'>*</span>
                                <select name='enroll_contest' id='baroque_instrument'>
                                    <option value='Piano'>Piano</option>
                                    <option value='Mixed Ensemble'>Mixed Ensemble</option>
                                    <option value='String'>String</option>
                                    <option value='Woodwind'>Woodwinds</option>
                                    <option value='Guitar'>Guitar</option>
                                </select>         
                            </label>        
                            <label>Song Title <span class='required'>*</span>
                            <input type='text' name='song_title' placeholder='Ave Maria' />
                            </label>
                            <label>Song Duration (in mm:ss format) <span class='required'>*</span>
                            <input type='text' name='song_duration' placeholder='00:00' />
                            </label>
                            <label>Composer's Name <span class='required'>*</span>
                            <input type='text' name='composer_name' placeholder='Johann Sebastian Bach' />          
                            </label> 
                        <label>Prefer Time <span class='required'>*</span>
                            <input type='radio' name='is_morning' value='yes' checked>
                            Morning
                            <input type='radio' name='is_morning' value='no'>
                            Evening<br>
                            </label>
                            
                            <input type='hidden' name='contest' id='contest' value='baroque'>
                 ";
                            
                            
                            do_action('populate_students_for_enroll_form', false);   
                                 
                echo "           
                    </form>
                    </div>

                </div>
            ";
        }
        else {

            echo "
                <div class='view-enrollment-style' id='instruction'>                        
                    <center>
                        <h3 style ='color:red;''>Enrollment has been closed for Baroque Festival.<h3>
                        <p>If you want to check your enrollment please go to <a href='".home_url('view-enrollment')."'>view your enrollment</a></p>
                    </center>
                </div>  
            ";

        }
    }        
    
?>

<!--
<div id="sel_baroque" class="contest_form">
    <div class="dbpac-form-style">
    <h1>Enroll in Baroque Festival<span>If you are entering a group please choose Mixed Ensemble</span></h1>
    <form id="enroll_baroque" name="enroll_baroque" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">    
        <div class="inner-wrap">
            <label>Instrument or Division <span class="required">*</span>
                <select name="enroll_contest" id="baroque_instrument">
                    <option value="Piano">Piano</option>
                    <option value="Mixed Ensemble">Mixed Ensemble</option>
                    <option value="String">String</option>
                    <option value="Woodwind">Woodwinds</option>
                    <option value="Guitar">Guitar</option>
                </select>         
            </label>        
            <label>Song Title <span class="required">*</span>
            <input type="text" name="song_title" placeholder="Ave Maria" />
            </label>
            <label>Song Duration (in mm:ss format) <span class="required">*</span>
            <input type="text" name="song_duration" placeholder="00:00" />
            </label>
            <label>Composer's Name <span class="required">*</span>
            <input type="text" name="composer_name" placeholder="Johann Sebastian Bach" />          
            </label> 
	    <label>Prefer Time <span class="required">*</span>
            <input type="radio" name="is_morning" value="yes" checked>
            Morning
            <input type="radio" name="is_morning" value="no">
            Evening<br>
            </label>
            
            <input type="hidden" name="contest" id="contest" value="baroque">
            
            <?php
                do_action('populate_students_for_enroll_form', false);   
            ?>          
           
    </form>
    </div>

</div>


<div class="view-enrollment-style" id="instruction">                        
    <center>
        <h3 style ="color:red;">Enrollment has been closed for Baroque Festival.<h3>
        <p>If you want to check your enrollment please go to <a href="'.home_url('view-enrollment').'">view your enrollment</a></p>
    </center>
</div> 
--> 
