<?php 
    $id = get_query_var('enrollment_id', NULL);
        
    global $wpdb;
    $table_name = $wpdb->prefix . 'dbpac_enrollment';
    $id = absint($id);
    if (!empty($id)){
        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `enrollment_id` = %d",  $id );        
        $enrollment_row = $wpdb->get_row($sql);
        if (count($enrollment_row) > 0){
            // convert duration decimal to mm::ss format
            $duration = $enrollment_row->duration;

            // check if it is morning preffered or afternoon
            $is_morning = $enrollment->is_morning;
?>
        <div id='sel_dbpac' class='contest_form'>
            <div class='dbpac-form-style'>
            <h1>Edit Enrollment<span>Update enrollment information</span></h1>
            <form id="enroll_dbpac" name="enroll_dbpac" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">  
                <div class='inner-wrap'>
                    <label>Instrument or Division <span class='required'>*</span>
                    <div id='division-select'> 
                    </div> 
                    </label>               
                    <label>Song Title <span class='required'>*</span>
                    <input type='text' name='song_title' value='<?php echo $enrollment_row->song_title; ?>' />
                    </label>
                    <label>Song Duration (in mm:ss format) <span class='required'>*</span>
                    <input type='text' name='song_duration' value='<?php echo $duration; ?>' />
                    </label>
                    <label>Composer's Name <span class='required'>*</span>
                    <input type='text' name='composer_name' value='<?php echo $enrollment_row->composer_name; ?>' />          
                    </label>            
                    <input type='hidden' name='is_morning' value='no'>
                    <label>Prefer Time <span class='required'>*</span>
                        <input type='radio' name='is_morning' value='yes' checked>
                        Morning
                        <input type='radio' name='is_morning' value='no'>
                        Evening<br>
                    </label>
                    <input type='hidden' name='contest' id='contest' value='dbpac'>
<?php            
                    do_action('populate_students_for_edit_enrollment', true);  
?>   
            </form>
            </div>
        </div>
<?php 
        } // (count($enrollment_row) > 0)
        else {
            echo '
                <div class="view-enrollment-style" id="instruction">
                    <center>
                        No data associate with this Enrolment id
                    </center>
                </div>
            ';
        }
    } // if (!empty($id))
    else {
        echo '
            <div class="view-enrollment-style" id="instruction">
                <center>
                    No data associate with this Enrollment id
                </center>
            </div>
        ';
    }  
?>