<?php
    // 1) get all student current fields from database and fill those as place holder
    // 2) let user edit the fields with the same validataion method as "AddStudent" Form
    // 3) Form processing backend in "class-dbpac-student.php"
    // get the student id passed in the url
    $id = get_query_var('student_id', NULL);
        
    global $wpdb;
    $table_name = $wpdb->prefix . 'dbpac_students';
    $id = absint($id);
    if (!empty($id)){
        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `student_id` = %d",  $id );        
        $student_row = $wpdb->get_row($sql);
        if (count($student_row) > 0){
            // there is a record associate with this student id so pre fill the data for 
            // user to update the student information
            // change the dob from Y-m-d format to string format (m/d/Y)
            $dob = DateTime::createFromFormat('Y-m-d', $student_row->dob);
            //echo $dob . '<br />';
            $dob_str = $dob->format('m/d/Y');
            //echo $dob_str;
            
            ?>

                <div class="dbpac-form-style">
        
                    <h1>Edit Student<span>Update student information</span></h1>
                    <form id="addstudent" name="editstudent" action="<?php echo esc_url(admin_url("admin-post.php")); ?>" method="post">  
                        
                        <div class="inner-wrap">                
                            <label>Student First Name <span class="required">*</span>
                            <input type="text" name="student_fname" value="<?php echo $student_row->first_name; ?>" />          
                            </label>
                            <label>Student Last Name <span class="required">*</span>
                            <input type="text" name="student_lname" value="<?php echo $student_row->last_name; ?>" />
                            </label>
                            <label>Student Date of Birth <span class="required">*</span>
                            <input type="text" name="student_dob" id="datepicker" value="<?php echo $dob_str; ?>" />
                            </label>
                            <label>Accompanist Full Name (teacher or sponsor) <span class="required">*</span>
                            <input type="text" name="accompanist_fname" value="<?php echo $student_row->accomp_name; ?>" />          
                            </label> 
                            <label>Accompanist Phone Number (teacher or sponsor) <span class="required">*</span>
                            <input type="text" name="accompanist_phone" value="<?php echo $student_row->accomp_phone; ?>" />          
                            </label>
                        </div>
                        <input type="hidden" name="student_id" value="<?php echo $id;?>">
                        <input type="hidden" name="action" value="edit_student">
                        <div class="button-section">
                            <center>
                                <input type="submit" name="editstudent" value="Update" /> 
                            </center>    
                        </div>
                    </form>
                </div>

            <?php
        
        }
        else {
            echo '
                <div class="view-enrollment-style" id="instruction">
                    <center>
                        No data associate with this student id
                    </center>
                </div>
            ';
        }        
    } 
    else {
        echo '
            <div class="view-enrollment-style" id="instruction">
                <center>
                    No data associate with this student id
                </center>
            </div>
        ';

    }   

?> 

 