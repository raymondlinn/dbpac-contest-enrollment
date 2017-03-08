<?php 
    $id = get_query_var('enrollment_id', NULL);
        
    global $wpdb;
    $table_name = $wpdb->prefix . 'dbpac_enrollments';
    $id = absint($id);
    if (!empty($id)){
        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `enrollment_id` = %d",  $id );        
        $enrollment_row = $wpdb->get_row($sql);
            if (count($enrollment_row) > 0){

                // get the group id to check if the enrollment is as a group then 
                // notify user to contact to admin to edit
                if ($enrollment_row->group_id == 0){
                    // convert duration decimal to mm::ss format
                    $duration = $enrollment_row->duration;
                    $mins = floor($duration / 60 % 60);
                    $seconds = floor($duration % 60);
                    $duration_str = sprintf('%02d:%02d', $mins, $seconds);
                    // check if it is morning preffered or afternoon
                    $is_morning = $enrollment_row->is_morning;

                    // get the division to pre select the dropdown division list
                    $division = $enrollment_row->division_name;

            
?>
                    <div id='sel_dbpac' class='contest_form'>
                        <div class='dbpac-form-style'>
                        <h1>Edit Enrollment<span>Update enrollment information</span></h1>
                        <form id="enroll_dbpac" name="enroll_dbpac" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">  
                            <div class='inner-wrap'>
<?php            
                                do_action('populate_students_for_edit_enrollment', $id);  
?> 
                                <label>Instrument or Division <span style='color:#c45544;'>(Select to update)</span><span class='required'> *</span>
                                
                                <div>
<?php                                
                                    $plugin_path = plugins_url();
                                    $file_name = $plugin_path . '/dbpac-contest-enrollment/js/dbpac-options.json';
                                    $option_json = file_get_contents($file_name);
                                    $jsonObj = json_decode($option_json);                                   
                                    
                                    echo '<select name="enroll_contest" id="dbpac_division">';
                                    foreach($jsonObj as $key => $optgroups){
                                        echo $key . '<br />';
                                        if (gettype($optgroups) == "object"){
                                            foreach($optgroups as $optgroup => $value){
                                                echo '<optgroup label="' . $optgroup . ' ">';
                                                foreach($value as $key => $option){
                                                    // selece the current enrollment division
                                                    if ($division == $option->division) {
                                                        echo '<option id="' .$option->divisionID . '" value="' . $optgroup . ';' . $option->division . '" selected>' . $option->division . '</option>';
                                                    } else{
                                                        echo '<option id="' .$option->divisionID . '" value="' . $optgroup . ';' . $option->division . '">' . $option->division . '</option>'; 
                                                    }                         
                                                }
                                            }
                                        }
                                    } 
                                    echo '</select>';                                    
?>
                                <div>

                                </label>               
                                <label>Song Title <span class='required'>*</span>
                                <input type='text' name='song_title' value='<?php echo $enrollment_row->song_title; ?>' />
                                </label>
                                <label>Song Duration (in mm:ss format) <span class='required'>*</span>
                                <input type='text' name='song_duration' value='<?php echo $duration_str; ?>' />
                                </label>
                                <label>Composer's Name <span class='required'>*</span>
                                <input type='text' name='composer_name' value='<?php echo $enrollment_row->composer_name; ?>' />          
                                </label>            
                                <input type='hidden' name='is_morning' value='no'>
                                <label>Prefer Time <span class='required'>*</span>
                                <?php
                                    if ($is_morning == 'yes') {
                                        echo " 
                                            <input type='radio' name='is_morning' value='yes' checked>
                                            Morning
                                            <input type='radio' name='is_morning' value='no'>
                                            Evening<br>
                                        ";
                                    }
                                    else {
                                        echo " 
                                            <input type='radio' name='is_morning' value='no'>
                                            Morning
                                            <input type='radio' name='is_morning' value='yes' checked>
                                            Evening<br>
                                        ";
                                    }
                                ?>
                                    
                                </label>
                                <input type="hidden" name="enrollment_id" value="<?php echo $id;?>">
                                <input type='hidden' name='contest' id='contest' value='dbpac'>
                            </div>
                            <input type="hidden" name="action" value="edit_enrollment">
                            <div class="button-section">
                            <center>
                                <input type="submit" name="edit_enrollment" value="Update Enrollment" /> 
                            </center>    
                            </div>    
  
                        </form>
                        </div>
                    </div>
<?php 
            } // if ($enrollment_row->group_id == 0)
            else { // this enrollment is a group enrollment so let user know to contact admin to update
                echo '
                    <div class="view-enrollment-style" id="instruction">
                        <center>
                            <p>
                                This is the group enrollment for two or more students.<br />
                                Please contact <a href="mailto:joanna@teachers.org">Ms. Lo</a> for editing the group enrollment.<br />
                            </p> 
                        </center>
                    </div>
                ';
            }
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