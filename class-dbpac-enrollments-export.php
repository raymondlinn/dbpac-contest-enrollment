<?php
class Dbpac_Csv_Export {

  /**
   * Constructor
   */
  public function __construct() {
    
    if (isset($_GET['report'])) {

      $csv = $this->generate_csv();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private", false);
      header("Content-Type: application/octet-stream");
      $now = new DateTime(null, new DateTimeZone('America/Los_Angeles'));
      $time = $now->format('m-d-Y-his');
      $file_name='enrollments_'.$time.'.csv';
      header("Content-Disposition: attachment; filename=$file_name;");
      header("Content-Transfer-Encoding: binary");

      echo $csv;
      exit;
    }    

    // add a new capability to editor and adminstrator role
    add_action('admin_init', array($this, 'add_new_capability'));

    // Add extra menu items for admins
    add_action('admin_menu', array($this, 'modify_menu'));

    // add the css for admin page
    add_action('admin_enqueue_scripts', array($this, 'add_admin_page_style'));

    // Create end-points
    add_filter('query_vars', array($this, 'add_query_vars'));
    add_action('parse_request', array($this, 'process_parse_request'));

  } // end - __construct()

  /**
   * add new capability for both editor and administrator role
   */
  public function add_new_capability(){
    $editor_role = get_role('editor');
    $admin_role = get_role('administrator');
    $editor_role->add_cap('update_enrollments_table');
    $admin_role->add_cap('update_enrollments_table');
  }

  /**
   * Add extra menu items for admins
   */
  public function modify_menu() {

    add_menu_page('Dbpac Enrollments', // page title
                  'Dbpac Enrollments', // menu title
                  'update_enrollments_table', // capability
                  'dbpac_enrollments', // menu slug
                  array($this, 'enrollment_main_page')); // function
                                
    add_submenu_page('dbpac_enrollments', // parent slug
                  'Export Enrollments', // page title
                  'Export Enrollments', // menu title
                  'update_enrollments_table', // capability 
                  'export_enrollments', // menu slug
                  array($this, 'export_enrollments')); // function
    
    $GLOBALS['update_payment'] = add_submenu_page('dbpac_enrollments', // parent slug
                  'Update payment', // page title
                  'Update payment', // menu title
                  'update_enrollments_table', // capability
                  'update_payment', // menu slug
                  array($this, 'update_payment')); // function        
  }

  /**
   *
   */
  public function add_admin_page_style($hook){

    if($hook == $GLOBALS['update_payment']){
      wp_register_style( 'dbpac-admin-style', plugins_url('css/admin-style.css', __FILE__));
      wp_enqueue_style('dbpac-admin-style');

      // jquery
      if(!wp_script_is('jquery')){
        wp_enqueue_script('jquery');
      }   
      
      // jquery-validate
      if (!wp_script_is('jquery-validate')) {
        wp_enqueue_script('jquery-validate', 'https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.min.js', array('jquery'));
      }

      // jquery-validate additional method
      if(!wp_script_is('jquery-validate-addition')){
        wp_enqueue_script('jquery-validate-addition', 'https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js', array('jquery'));
      }
    
      wp_register_script('dbpac-admin', plugins_url('js/dbpac-admin.js', __FILE__), array( 'jquery'));
      wp_enqueue_script('dbpac-admin');
    }
  }

  /**
   * Allow for custom query variables
   */
  public function add_query_vars($query_vars) {
    $query_vars[] = 'export_enrollments';
    return $query_vars;
  }

  /**
   * Parse the request
   */
  public function process_parse_request(&$wp) {
    if (array_key_exists('export_enrollments', $wp->query_vars)) {
      $this->export_enrollments();
      exit;
    }
  }

  /**
   * enrollment main page
   */
  public function enrollment_main_page(){
    echo '<div class="wrap">';
    echo '<div id="icon-tools" class="icon32"></div>';
    echo '<div class="view_table">';
    echo '<h2>DBPAC Enrollments</h2>';
    echo '<button><a href="?page=export_enrollments&report=enrollments" style="text-decoration:none;color:black;">Export the Enrollments</a></button>';
    echo '<button><a href="?page=update_payment" style="text-decoration:none; color:black;">Update Payment</a></button>';
    echo '</div>';    
  }

  /**
   * Download report
   */
  public function export_enrollments() {
    echo '<div class="wrap">';
    echo '<div id="icon-tools" class="icon32">
			</div>';
    echo '<div class="view_table">';  
    echo '<h2>Export Enrollments</h2>';
    echo '<p><a href="?page=export_enrollments&report=enrollments">Export the Enrollments</a></p>';
    echo '</div>';
  }  

  /**
   * enrollment main page
   */
  public function update_payment($values){
    // render a page here
    echo '<div class="wrap">';
    echo '<div id="icon-tools" class="icon32">
      </div>';
    echo '<div class="view_table">';
    echo '<h2>Update Payment</h2>';
    echo '</div>';

    // ============ Rendering of Update Payment Page ========================================== 
    if(empty($_POST)){

      global $wpdb;
      $user_table = $wpdb->prefix . 'users';

      $query = "SELECT ID, user_email
          FROM $user_table
          ORDER BY ID
        ";
      $users = $wpdb->get_results($query);
      $num_users = count($users);
      if ($num_users > 0){        
        echo "<div class='dbpac-form-style'>";
        ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <?php
        echo "<select name='sel_user' >";
        echo '<option>Select a user that you want to update payment value</option>';
        foreach($users as $row){
          unset($email, $id, $user_info, $first, $last);
          $email = $row->user_email;
              $id = $row->ID;
              $user_info = get_userdata($id);
              $first = $user_info->first_name;
              $last = $user_info->last_name;                    
              echo '<option value="'.$id.'">'.$first.' '.$last. ' (' .$email. ')' .'</option>';
        }               
        echo "</select>"; 
        echo "</label>" ;      
        echo "<p><input type='submit' name='go_update' value='Go Update'></p>";       
        echo "</form>";
        echo "</div>"; // end -- <div class='dbpac-form-style'>
        echo "</div>"; // end -- <div class="wrap">
      } 
    } // end if(empty($_POST))

    // ============ Select User Processing ========================================== 
    else if (!empty($_POST['sel_user'])){

      if (isset($_POST['sel_user'])) {

      $user_id = $_POST['sel_user'];
      // retrieve the enrollments for this user's students
      global $wpdb;
      $table_name = $wpdb->prefix . 'dbpac_students';
      $enrollment_table_name = $wpdb->prefix . 'dbpac_enrollments';

      $sql = "SELECT $table_name.user_id, $table_name.first_name, $table_name.last_name, $table_name.dob, $table_name.user_email, $table_name.user_name, $enrollment_table_name.division_name, $enrollment_table_name.instrument_name, $enrollment_table_name.composer_name, $enrollment_table_name.enrollment_id, $enrollment_table_name.song_title, $enrollment_table_name.group_id, $enrollment_table_name.fees, $enrollment_table_name.is_paid
          FROM $table_name
          INNER JOIN $enrollment_table_name
          ON ($table_name.student_id = $enrollment_table_name.student_id AND $enrollment_table_name.is_enrolled = 'yes' AND $table_name.user_id = $user_id)            
          ORDER BY $enrollment_table_name.group_id
        ";

      $values = $wpdb->get_results($sql);
      if (count($values) > 0){
        // -- START -- Table Header for Enrollment List
        ?>
        <div>
        <form method="post" name="update_pay" id="update_pay" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <?php
        echo "
          <div id='view-enrollment-table'>
          <p style='color:red;'>Type 'yes' in the corresponding payment input box if the user already made the payment.</p>
          <p style='color:red;'>Check instrument or division name and enrollment Id to verify with payapl item name to update the payment information.</p>
          <table id='enrollment-table'>
            <thead>
              <tr> 
                <th> User Name </th>
                <th> User Email </th>             
                <th> First Name </th>
                <th> Last Name </th>
                <th> DOB </th>
		<th> Enrollment ID </th>
                <th> Division </th>
                <th> Instrument </th>                
                <th> Composer Name </th>
                <th> Song Title </th>
                <th> Group ID </th>
                <th> Fees </th>
                <th> Payment </th>
              </tr>   
            </thead>
            <tbody>
        ";
        // -- END -- Table Header for Enrollment List
        // -- START -- filling the Enrollment List table 

        foreach($values as $row){
          echo "<tr>";   
          echo "<td>" . $row->user_name . "</td>";  
          echo "<td>" . $row->user_email . "</td>";         
          echo "<td>" . $row->first_name . "</td>";
          echo "<td>" . $row->last_name . "</td>"; 
          echo "<td>" . $row->dob . "</td>";
          echo "<td>" . $row->enrollment_id . "</td>";
          echo "<td>" . $row->division_name . "</td>";
          echo "<td>" . $row->instrument_name . "</td>";          
          echo "<td>" . $row->composer_name . "</td>";
          echo "<td>" . $row->song_title . "</td>";
          echo "<td>" . $row->group_id . "</td>";             
          echo "<td>" . $row->fees . "</td>";
          
          // add input box here to update            
          ?>
          <input type="hidden" name="student_name[]" value="<?php echo $row->first_name . ' ' . $row->last_name; ?>" >
          <input type="hidden" name="enrollment_id[]" value="<?php echo $row->enrollment_id; ?>" >
          <td><input type="text" id="is_paid_<?php echo $row->enrollment_id; ?>" name="is_paid[]" maxlength="3" size="3" value="<?php echo $row->is_paid; ?>" ><span class='required'>*</span></td>
          <?php           
          echo "</tr>"; 
        } // foreach ends
          // -- END -- filling the Enrollment List table 
          echo "
              </tbody>
            </table> 
            </div> <!-- end div view-enrollment-table -->
            
          ";       
          echo "
            <p><center><input type='submit' name='update_payment' value='Update Payment'></center></p>
            </form>
            </div>
            </div> <!-- end div wrap -->
          ";
        } // end--- if (count($values) > 0)
        else {
          $user_info = get_userdata($user_id);
          echo "<div class='view-enrollment-style' id='instruction'>";
          echo "<p>" .$user_info->first_name . " " . $user_info->last_name . " does not have any students enrolled. </p>" ;
          echo "</div>";
          echo "</div>"; // <!-- end div wrap -->
        }
      } // end--if (isset($_POST['sel_user']))
    } // end -- else if (!empty($_POST['sel_user']))

    // ============ Update is_paid Field Processing ========================================== 
    else if (!empty($_POST['update_payment'])){

      if(isset($_POST['is_paid'])){

        $b_invalid_input = false;
        $is_paid_inputs = $_POST['is_paid'];
        foreach ($is_paid_inputs as $is_paid_input){

          if(empty($is_paid_input)){            
            $b_invalid_input = true;
            break;
          }
          else if($is_paid_input === 'yes' || $is_paid_input === 'no'){
            $b_invalid_input = false;
          }
          else {
            $b_invalid_input = true;
          }
        }

        if($b_invalid_input === false) { // is_paid_inputs are not empty          

          $enrollment_ids = $_POST['enrollment_id'];
          $student_names = $_POST['student_name'];          
                          
          $num_inputs = count($is_paid_inputs);

          for($i = 0; $i < $num_inputs; $i++){
            // update enrollment db here
            unset($enrollment_id, $is_paid, $student_name, $result);
            $enrollment_id = absint($enrollment_ids[$i]);   

            $is_paid = $is_paid_inputs[$i];

            $data = compact('is_paid');          
            $result = Dbpac_Dbapi::update_dbpac_enrollments($enrollment_id, $data);         

          } // end -- for($i = 0; $i < $num_inputs; $i++)

          if ($result === false){ // update fails
            echo "<div class='view-enrollment-style' id='instruction'>";                     
            echo "<p> Something went wrong while you are updating!</p>";
            echo "<p> Please try and update again. If the problem is consistent, please send and email to <a href='mailto:admin@dbpac.orgs' >admin</a></p>";          
            echo "</div>"; // end -- <div class='view-enrollment-style' id='instruction'>
          } // end -- if ($result === false)

          else {  // successful update   
            echo "<div class='view-enrollment-style' id='instruction'>";
            echo "<p> You have successfully updated " . $num_inputs . " entries. </p>";
            echo "<p> Here are Enrollment ID(s) that you have updated: </p>";
            $i = 0;
            for ($i = 0; $i < $num_inputs; $i++){
              echo "Enrollment ID: " .  $enrollment_ids[$i] . "\t" . "Student Name: " . $student_names[$i]; 
              echo "<br />";

            }

            echo "</div>"; // end -- <div class='view-enrollment-style' id='instruction'>

          } // end -- successful update

          echo "</div>"; // <!-- end div wrap -->

        } // end -- if($b_invalid_input === false)
        
        else {
          echo "<div class='view-enrollment-style' id='instruction'>";                     
          echo "<p style='color: red;' > You have to fill in either 'yes' or 'no' in all the input fields !</p>";
          echo "<p> Please try and update again.</p>";          
          echo "</div>";
        } 

      } // end -- if(isset($_POST['is_paid']))

    } // end--- else if (!empty($_POST['update_payment']))

  } // end --- update_payment()

  /**
   * Converting data to CSV
   */
  public function generate_csv() {

    // ==============got to join two tables here===============
    $csv_output = '';
    $separator = ',';
    global $wpdb;
    $table_name = $wpdb->prefix . 'dbpac_students';
    $enrollment_table_name = $wpdb->prefix . 'dbpac_enrollments';

    /*
    $sql = "SHOW COLUMNS FROM $table_name";
    $result = $wpdb->get_results($sql);

    if (count($result) > 0) {
    	foreach($result as $row){
    		$csv_output = $csv_output . $row->Field . $separator;
    	}
    	$csv_output = substr($csv_output, 0, -1); 
    }
    $csv_output .= "\n";
    */

    $csv_output .= "user_id,user_email,user_name,student_id,first_name,last_name,dob,enrollment_id,instrument_name,division_name,song_title,composer_name,duration,group_id,fees,is_paid"; 
    $csv_output .= "\n";

    $sql = "SELECT $table_name.user_id, $table_name.student_id, $table_name.first_name, $table_name.last_name, $table_name.dob, $table_name.user_email, $table_name.user_name, $enrollment_table_name.division_name, $enrollment_table_name.instrument_name, $enrollment_table_name.composer_name, $enrollment_table_name.enrollment_id, $enrollment_table_name.song_title, $enrollment_table_name.duration, $enrollment_table_name.group_id, $enrollment_table_name.fees, $enrollment_table_name.is_paid
            FROM $table_name
            INNER JOIN $enrollment_table_name
            ON ($table_name.student_id = $enrollment_table_name.student_id AND $enrollment_table_name.is_enrolled = 'yes')            
            ORDER BY $enrollment_table_name.group_id, $enrollment_table_name.user_id ASC
          ";

    //$sql = "SELECT * FROM $table_name";
    $values = $wpdb->get_results($sql);
    if (count($values) > 0){
      foreach($values as $row){
        	//$fields = array_values((array) $row_value);		//Getting rid of the keys and using numeric array to get values
          //$csv_output .= implode($separator, $fields);	//Generating string with field separator
          $csv_output = $csv_output . $row->user_id . $separator;
          $csv_output = $csv_output . $row->user_email . $separator;
          $csv_output = $csv_output . $row->user_name . $separator;
          $csv_output = $csv_output . $row->student_id . $separator;
          $csv_output = $csv_output . $row->first_name . $separator;
          $csv_output = $csv_output . $row->last_name . $separator;
          $csv_output = $csv_output . $row->dob . $separator;
          $csv_output = $csv_output . $row->enrollment_id . $separator;
          $csv_output = $csv_output . $row->instrument_name . $separator;
          $csv_output = $csv_output . $row->division_name . $separator;
          $csv_output = $csv_output . $row->song_title . $separator;
          $csv_output = $csv_output . $row->composer_name . $separator;
          $csv_output = $csv_output . gmdate("i:s", $row->duration) . $separator;
          $csv_output = $csv_output . $row->group_id . $separator;
          $csv_output = $csv_output . $row->fees . $separator;
          $csv_output = $csv_output . $row->is_paid . $separator;


          $csv_output .= "\n";
        }
      }
      else {
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
        $csv_output = $csv_output . 'no results return' . $separator;
      }
    
    return $csv_output;
  }

}
