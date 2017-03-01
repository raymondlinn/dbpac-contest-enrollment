<div class="dbpac-form-style">
    <h1>Add Student<span>Add students to enroll</span></h1>
    <form id="addstudent" name="addstudent" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">    
        <div class="inner-wrap">            	
            <label>Student First Name <span class="required">*</span>
            <input type="text" name="student_fname" placeholder="John" />          
            </label>
            <label>Student Last Name <span class="required">*</span>
            <input type="text" name="student_lname" placeholder="Doe" />
            </label>
            <label>Student Date of Birth <span class="required">*</span>
            <input type="text" name="student_dob" id="datepicker" placeholder="MM/DD/YYYY" />
            </label>
            <label>Accompanist Full Name (teacher or sponsor) <span class="required">*</span>
            <input type="text" name="accompanist_fname" placeholder="Jane Smith" />          
            </label> 
            <label>Accompanist Phone Number (teacher or sponsor) <span class="required">*</span>
            <input type="text" name="accompanist_phone" placeholder="000 000 0000" />          
            </label>
        </div>
        <input type="hidden" name="action" value="add_student">
        <div class="button-section">
            <center>
                <input type="submit" name="addstudent" value="Add Student" /> 
            </center>    
        </div>
    </form>
</div> 

 


