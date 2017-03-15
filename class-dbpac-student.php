<?php

class Dbpac_Student {

	public static $version;

	public function __construct() {		

		require_once dirname( __FILE__ )  . '/class-dbpac-dbapi.php';	
		
		/* ******************************************************************************* *
		 * This section is the actions section
		 * ******************************************************************************* */
		add_action('admin_post_add_student', array($this, 'process_add_student_form'));
		add_action('admin_post_enroll_contest', array($this, 'process_enroll_contest_form'));
		add_action('admin_post_update_profile', array($this, 'process_update_profile'));
		add_action('admin_post_edit_student', array($this, 'process_edit_student_form'));
		add_action('admin_post_edit_enrollment', array($this, 'process_edit_enrollment_form'));

		/* ******************************************************************************* *
		 * This section is the filters section
		 * ******************************************************************************* */
		

		/* ******************************************************************************* *
		 * This section add all the shortcodes for the user managment and studtents pages 
		 * ******************************************************************************* */
		add_shortcode('dbpac-enroll-contest-form', array($this, 'render_enroll_contest_form'));
		add_shortcode('dbpac-add-student-form', array($this, 'render_add_student_form'));
		add_shortcode('dbpac-view-student', array($this, 'render_view_student'));
		add_shortcode('dbpac-view-enrollment', array($this, 'render_view_enrollment'));
		add_shortcode('dbpac-shopping-cart', array($this, 'render_shopping_cart'));

		add_shortcode('dbpac-edit-student', array($this, 'render_edit_student'));
		add_shortcode('dbpac-edit-enrollment', array($this, 'render_edit_enrollment'));		
		
		add_filter('query_vars', array($this, 'add_query_vars'));
				
	}

	/***********************************************************************************************
	 * Plugin activation hook
	 *
	 * Creates necessary pages needed by the plugin
	 **********************************************************************************************/
	public static function activate_student() {

		// Information needed for creating the plugin;s pages
		$page_definitions = array(
			'add-student' => array (
		    	'title' => __('Add a Student', 'dbpac-user'),
		    	'content' => '[dbpac-add-student-form]'
		    ),
		    'enroll-contest' => array (
		    	'title' => __('Enroll Contest', 'dbpac-user'),
		    	'content' => '[dbpac-enroll-contest-form]'
		    ),
		    'view-student' => array (
		    	'title' => __('View Your Student', 'dbpac-user'),
		    	'content' => '[dbpac-view-student]'
		    ),
		    'view-enrollment' => array (
		    	'title' => __('View Enrollment', 'dbpac-user'),
		    	'content' => '[dbpac-view-enrollment]'
		    ),
		    'shopping-cart' => array (
		    	'title' => __('Shopping Cart', 'dbpac-user'),
		    	'content' => '[dbpac-shopping-cart]'
		    ),
		    'edit-student' => array (
		    	'title' => __('Edit Student', 'dbpac-user'),
		    	'content' => '[dbpac-edit-student]'
		    ),
		    'edit-enrollment' => array (
		    	'title' => __('Edit Enrollment', 'dbpac-user'),
		    	'content' => '[dbpac-edit-enrollment]'
		    )
		);

		// post the above pages
		$page_value = array();
		foreach ($page_definitions as $slug => $page) {
			// Checke that the page does not exist already
			$query = new WP_Query('pagename=' . $slug);
			if(! $query->have_posts()) {
				// Add the page using the data from the array above
				$page_value[] = wp_insert_post(
					array(
						'post_content' 	=> $page['content'],
						'post_name'		=> $slug,
						'post_title'	=> $page['title'],
						'post_status'	=> 'publish',
						'post_type'		=> 'page',
						'ping_status'	=> 'closed',
						'comment_status'=> 'closed',
					)
				);
			}
		}

		// remember the page id's so we can delete those pages when user deactivate the plugin
		delete_option('dbpac-student');
		add_option('dbpac-student', $page_value);
	}

	/***********************************************************************************************
	 * Deactivate
	 *
	 * Clean up the tables and the other options
	 **********************************************************************************************/
	public static function deactive_student() {
		
		$dbpac_option = get_option('dbpac-student');
		$max = sizeof($dbpac_option);
		for ($i = 0; $i < $max; $i++) {
			if(isset($dbpac_option[$i])){
				wp_delete_post($dbpac_option[$i], true);
			}
		}
		
		// drop table here
		/*
		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_students';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
	
		// drop two contest tables
		
		$table_name = $wpdb->prefix . 'dbpac_enrollments';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
		*/

		/*
		$table_name = $wpdb->prefix . 'dbpac_instruments_divisions';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
		*/		
	}

	/***********************************************************************************************
	 * create_dbpac_student_table
	 *
	 * Creating student table
	 **********************************************************************************************/
	public static function create_dbpac_student_table(){
		
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$table_name = $wpdb->prefix . 'dbpac_students';

		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
			$charset_collate = $wpdb->get_charset_collate();

			// split it into three tables so students table only has student information
			// 11 columns
			$sql = "CREATE TABLE $table_name (
				student_id bigint(20) unsigned NOT NULL auto_increment,
				user_id bigint(20) unsigned NOT NULL DEFAULT '0',
				last_name varchar(20) NOT NULL DEFAULT 'LastName',
				first_name varchar(20) NOT NULL DEFAULT 'FirstName',
				dob date NOT NULL DEFAULT '0000-00-00',
				user_name varchar(64) NOT NULL DEFAULT 'username',
				user_email varchar(100) NOT NULL DEFAULT 'useremail',
				user_phone varchar(10) NOT NULL DEFAULT '0000000000',
				accomp_name varchar(64) NOT NULL DEFAULT 'accompanistName',
				accomp_phone varchar(10) NOT NULL DEFAULT '0000000000',
				created datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY  (student_id),
				KEY user_id (user_id)
			) $charset_collate;";

			dbDelta($sql);	
		}

		// creating enrollment table
		$table_name = $wpdb->prefix . 'dbpac_enrollments';

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
			$charset_collate = $wpdb->get_charset_collate();

			// 14 columns
			$sql = "CREATE TABLE $table_name (
				enrollment_id bigint(20) unsigned NOT NULL auto_increment,
				student_id bigint(20) unsigned NOT NULL DEFAULT '0',
				user_id bigint(20) unsigned NOT NULL DEFAULT '0',
				group_id bigint(20) unsigned NOT NULL DEFAULT '0',
				contest_name varchar(20) NOT NULL DEFAULT 'contest',
				instrument_name varchar(20) NOT NULL DEFAULT 'instrument',
				division_name varchar(70) NOT NULL DEFAULT 'division',
				song_title varchar(64) NOT NULL DEFAULT 'song',
				composer_name varchar(64) NOT NULL DEFAULT 'composerName',
				duration int NOT NULL DEFAULT '240',
				fees int NOT NULL DEFAULT '0',
				is_enrolled varchar(3) NOT NULL DEFAULT 'no',
				is_paid varchar(3) NOT NULL DEFAULT 'no',
				is_morning varchar(3) NOT NULL DEFAULT 'no',
				created datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY  (enrollment_id),
				KEY student_id (student_id),
				KEY user_id (user_id),
				KEY group_id (group_id),
				KEY contest_name (contest_name),
				KEY instrument_name (instrument_name),
				KEY division_name (division_name)
			) $charset_collate;";
			
			dbDelta($sql);	
		}
	}


	/***********************************************************************************************
	 * A shortcode for rendering add student form
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 **********************************************************************************************/
	public function render_add_student_form($attributes, $content = null) {
		// parse shortcode attributes
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'add-student-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('add-student-form', $attributes);
		}
	}

	/***********************************************************************************************
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array  $attributes    The PHP variables for the template
	 *
	 * @return string               The contents of the template.
	 **********************************************************************************************/
	private function get_template_html( $template_name, $attributes = null ) {
	    if ( ! $attributes ) {
	        $attributes = array();
	    }
	 
	    ob_start();
	 
	    do_action( 'dbpac_login_before_' . $template_name );
	 
	    require( 'templates/' . $template_name . '.php');
	 
	    do_action( 'dbpac_login_after_' . $template_name );
	 
	    $html = ob_get_contents();
	    ob_end_clean();
	 
	    return $html;
	}

	/***********************************************************************************************
	 * A shortcode for rendering enroll contest form
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 **********************************************************************************************/
	public function render_enroll_contest_form($attributes, $content = null) {
		// parse shortcode attributes
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'enroll-contest-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('enroll-contest-form', $attributes);
		}
	}

	/***********************************************************************************************
	 * A shortcode for rendering view student page
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 **********************************************************************************************/
	public function render_view_student($attributes, $content = null) {
		// parse shortcode attributes
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'add-student-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('view-student', $attributes);
		}
	}

	/**
	 * A shortcode for rendering view enrollment page
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 */
	public function render_view_enrollment($attributes, $content = null){
		// parse shortcode attributes
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'enroll-contest-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('view-enrollment', $attributes);
		}
	}

	/**
	 * A shortcode for rendering shopping cart page
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 */
	public function render_shopping_cart($attributes, $content = null){
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'shopping-cart-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('shopping-cart', $attributes);
		}		
	}


	/**
	 * A shortcode for rendering edit student page
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 */
	public function render_edit_student($attributes, $content = null){
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'edit-student-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('edit-student', $attributes);
		}		
	}

	/**
	 * A shortcode for rendering edit enrollment page
	 *
	 * @param 	array 	$attributes Shortcode attributes
	 * @param 	string 	$content 	The text content for shortcode. Not use
	 *
	 * @return 	string 	The shortcode output
	 */
	public function render_edit_enrollment($attributes, $content = null){
		$defualt_attributes = array('show_title' => false);
		$attributes = shortcode_atts($defualt_attributes, $attributes);
		$show_title = $attributes['show_title'];

		if (!is_user_logged_in()){
			$redirect_url = home_url('access-error');
			$redirect_url = add_query_arg( 'edit-enrollment-errors', 'login', $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		} else {
			// Render the login form using an external template
			return $this->get_template_html('edit-enrollment', $attributes);
		}		
	}
	
	/***********************************************************************************************
	 * process_add_student_form
	 *
	 * form processing of add student form. sanitize and create a record in the student database 
	 **********************************************************************************************/
	public function process_add_student_form(){

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	        $redirect_url = home_url( 'add-student' );
			if(!empty($_POST)) {
				
				// Check the if user is logged in
				if(is_user_logged_in()) {
					global $current_user;
      				wp_get_current_user();
					$user_id = $current_user->ID;
					$user_name = $current_user->user_firstname .' ' .$current_user->user_lastname;
					$user_email = $current_user->user_email;

					$user_phone = get_user_meta($current_user->ID, 'user_phone', true);
                                        $user_phone = preg_replace('/[^0-9]/', '', $user_phone);

					// Sanitize the POST field
		            $first_name = sanitize_text_field( $_POST['student_fname'] );
		            $last_name = sanitize_text_field( $_POST['student_lname'] );
		            
		            // is the date  and it is string
		            $dob = sanitize_text_field( $_POST['student_dob'] );

		            $accomp_name = sanitize_text_field( $_POST['accompanist_fname'] );

		            // sanitize phone number
		            $accomp_phone = preg_replace('/[^0-9]/', '', $_POST['accompanist_phone']);		            
		 			
		            $result = $this->create_student($user_id, $user_name, $user_email, $user_phone, $first_name, $last_name, $dob, $accomp_name, $accomp_phone );
			    		            
		            if ($result === 0) {
		            	$errors = 'result 0 error';
		            	echo $errors;
		            	$redirect_url = add_query_arg( 'add-student-errors', $errors, $redirect_url );
		            }		 			
		            if ( is_wp_error( $result ) ) {
		                // Parse errors into a string and append as parameter to redirect
		                $errors = join( ',', $result->get_error_codes() );
		                $redirect_url = add_query_arg( 'add-student-errors', $errors, $redirect_url );
		            } else {
		                // Success, redirect to login page.
		                $redirect_url = home_url( 'view-student' );
		                $redirect_url = add_query_arg( 'added', $first_name, $redirect_url );
		            }		            
		        } // is_user_logged_in		 
		        wp_redirect( $redirect_url );
		        exit;
			} //if(!empty($_POST))
		} // if ( 'POST' == $_SERVER['REQUEST_METHOD'] )		
	} // end - process_add_student_form()

	/***********************************************************************************************
	 * create_student
	 *
	 * creating student record by calling Dbpac_Dbapi 
	 **********************************************************************************************/
	// 9 fields need to be inserted 
	private function create_student($user_id, $user_name, $user_email, $user_phone, $first_name, $last_name, $dob, $accomp_name, $accomp_phone ) {

		$dob = date("Y-m-d", strtotime($dob));
		$data = compact('user_id', 'user_name', 'user_email', 'user_phone', 'first_name', 'last_name', 'dob', 'accomp_name', 'accomp_phone');

		return Dbpac_Dbapi::insert_dbpac_students($data);
	}

	/***********************************************************************************************
	 * process_edit_student_form
	 *
	 * form processing of edit student form. sanitize and update a record in the student database 
	 * it is almost the same as process_add_student_form with the student_id to update student
	 * database
	 **********************************************************************************************/

	public function process_edit_student_form(){
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	        $redirect_url = home_url( 'view-student' );
			if(!empty($_POST)) {
				echo "process edit student starts";
				
				// Check the if user is logged in
				if(is_user_logged_in()) {
					global $current_user;
      				wp_get_current_user();
					$user_id = $current_user->ID;
					$user_name = $current_user->user_firstname .' ' .$current_user->user_lastname;
					$user_email = $current_user->user_email;

					$user_phone = get_user_meta($current_user->ID, 'user_phone', true);
                                        $user_phone = preg_replace('/[^0-9]/', '', $user_phone);

					// sanitize student id
                    $student_id = sanitize_text_field($_POST['student_id']);

                    // Following five fields will be updated
                    
                    
                    /*
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'dbpac_students';
                    $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `student_id` = %d",  $id );        
			        $student_row = $wpdb->get_row($sql);
			        if (count($student_row) == 0){
			        	$errors = 'result 0 error';
		            	echo $errors;
		            	$redirect_url = add_query_arg( 'edit-student-errors', $errors, $redirect_url );
			        }
					*/
					// Sanitize the POST field
		            $first_name = sanitize_text_field( $_POST['student_fname'] );
		            $last_name = sanitize_text_field( $_POST['student_lname'] );
		            
		            // is the date  and it is string
		            $dob = sanitize_text_field( $_POST['student_dob'] );
		            $dob = date("Y-m-d", strtotime($dob));

		            $accomp_name = sanitize_text_field( $_POST['accompanist_fname'] );

		            // sanitize phone number
		            $accomp_phone = preg_replace('/[^0-9]/', '', $_POST['accompanist_phone']);		            
		 			
		 			// update the student database record
		 			// prepare $data
		 			$data = compact('user_id', 'last_name', 'first_name', 'dob', 'user_name', 'user_email', 'user_phone', 'accomp_name', 'accomp_phone');
		 			$result = Dbpac_Dbapi::update_dbpac_students($student_id, $data);
		 			          
		            if ($result === false) {
		            	$errors = 'result 0 error';
		            	echo $errors;
		            	$redirect_url = add_query_arg( 'edit-student-errors', $errors, $redirect_url );
		            }		 			
		            if ( is_wp_error( $result ) ) {
		                // Parse errors into a string and append as parameter to redirect
		                $errors = join( ',', $result->get_error_codes() );
		                $redirect_url = add_query_arg( 'edit-student-errors', $errors, $redirect_url );
		            } else {
		                // Success, redirect to login page.
		                $redirect_url = home_url( 'view-student' );
		                $redirect_url = add_query_arg( 'updated', $first_name, $redirect_url );
		            }
		            	            
		        } // is_user_logged_in		 
		        wp_redirect( $redirect_url );
		        exit;
			} //if(!empty($_POST))
		} // if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
	}

	/***********************************************************************************************
	 * process_enroll_contest_form
	 *
	 * form processing of enrollment of contest form. sanitize and create a record in the enrollments database 
	 **********************************************************************************************/	
	public function process_enroll_contest_form() {
		echo '<br />';
		echo 'process enroll dbpac form';

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	        $redirect_url = home_url( 'enroll-contest' );
			if(!empty($_POST)) {
				echo '<br />';
				echo 'not empty';
				// Check the if user is logged in
				if(is_user_logged_in()) {
					echo '<br />';
					echo 'user logged in';

					global $current_user;
      				wp_get_current_user();
					$user_id = $current_user->ID;

					// decide whether this is for what contest enrollment
					// by checking the hidden input from the front-end form
					$contest = ($_POST['contest']);

					// processing dbpac enrollment form
					if ($contest == 'dbpac'){
						$contest_name = '2017 DBPAC';
						// sanitize the post fields
						$selected_value = sanitize_text_field($_POST['enroll_contest']);
						echo '<br />';
						echo $selected_value;

						$options = explode(";", $selected_value);
						$instrument_name = $options[0];
						$division_name = $options[1];
						echo '<br/>';
						echo $instrument_name;
						echo '<br/>';
						echo $division_name;
						// get the fees from division name
						// example division name: "P Solo 1a - Age 5 (2 minutes) - $35"
						$extract_fees = explode(" - ", $division_name);
						$fees = str_replace('$', '', $extract_fees[2]);
					} // end - processing dbpac enrollment form

					// processing baroque enrollment form
					else {
						$contest_name = '2017 Baroque';
						// sanitize the post fields
						$selected_value = sanitize_text_field($_POST['enroll_contest']);
						echo '<br />';
						echo $selected_value;

						$instrument_name = $selected_value;
						echo '<br/>';
						echo $instrument_name;

						// set division to no division
						$division_name = 'no divion';

						if($instrument_name == 'Mixed Ensemble'){
							$fees = '10';
						}
						else {
							$fees = '15';
						}						
					} // end - processing baroque enrollment form
					
					// change $fees to int type
					

					$song_title = sanitize_text_field($_POST['song_title']);

					$song_duration = sanitize_text_field($_POST['song_duration']);
					$mixed_durations = explode(":", $song_duration);
					$minute = absint($mixed_durations[0]);
					$second = absint($mixed_durations[1]);
					$duration = $minute * 60 + $second;

					echo '<br/>';
					echo $duration;

					$composer_name = sanitize_text_field($_POST['composer_name']);
					
					$is_enrolled = 'yes';
					$is_paid = 'no';
					
					$is_morning = $_POST['is_morning'];

					$student_ids = $_POST['sel_students'];
					$student_count = count($student_ids);

					if($student_count === 0){
						// return error
						$errors = 'no student selected error';
						$redirect_url = add_query_arg( 'enroll-contest-errors', $errors, $redirect_url );
					}
					else {				
						// could be multiple students for duo, trio, quartet and mixed ensemble
						// so handle it in create_enrollment function
						$fees = absint($fees);
						if ($contest == 'dbpac'){
							if ( $student_count > 1){
								echo "<br/>";
								echo "dbpac and more than one student";
								$fees = $fees/$student_count;
							}
						} 
						echo '<br/>';
						echo $fees;

						$result = $this->create_enrollment($user_id, $contest_name, $instrument_name, $division_name, $song_title, $duration, $composer_name, $fees, $is_enrolled, $is_paid, $is_morning, $student_ids);
						if ($result === false) {
							$errors = 'enroll contest errors';
							$redirect_url = add_query_arg( 'enroll-contest-errors', $errors, $redirect_url );
						}
						if (is_wp_error($result)){
							$errors = join( ',', $result->get_error_codes() );
		                	$redirect_url = add_query_arg( 'enroll-contest-errors', $errors, $redirect_url );
						}else {
							$redirect_url = home_url( 'view-enrollment' );
		                	$redirect_url = add_query_arg( 'enrolled', $student_ids, $redirect_url );
						}
					}
					
				} // if(is_user_logged_in())
				wp_redirect( $redirect_url );
		        exit;
			} // if(!empty($_POST))
		} // if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) 
	} // end - process_enroll_baroque_form

	/***********************************************************************************************
	 * create_enrollment
	 *
	 * creating student record by calling Dbpac_Dbapi 
	 **********************************************************************************************/
	// 9 fields need to be inserted 
	private function create_enrollment($user_id, $contest_name, $instrument_name, $division_name, $song_title, $duration, $composer_name, $fees, $is_enrolled, $is_paid, $is_morning, $student_ids) {

		// if more than one student, that means entring contest as a group so, set group Id to be the sum
		// of student's id's
		$group_id = 0;
		$result = false;

		if (count($student_ids) > 1) {
			// start group id from 10000
			$group_id += 10000;
			// first loop to add student_id of the group to group_id
			foreach ($student_ids as $student_id) {
				$group_id += $student_id;
			}
		}
		// compact the data here - again we have to loop through for duo, trio, quartet, ensemble
		foreach ($student_ids as $student_id){
			$data = compact('student_id', 'user_id', 'group_id', 'contest_name', 'instrument_name', 'division_name', 'song_title', 'composer_name', 'duration', 'fees', 'is_enrolled', 'is_paid', 'is_morning');

			 $result = Dbpac_Dbapi::insert_dbpac_enrollments($data);
			 if($result === 0)
			 	return false;
		}
		return true;
	}


	/***********************************************************************************************
	 * process_edit_enrollment_form
	 *
	 * form processing of edit enrollment form. sanitize and update a record in the enrollment 
	 * database 
	 * it is almost the same as process_enroll_contest_form with the enollment_id to update
	 * enrollment database
	 **********************************************************************************************/
	public function process_edit_enrollment_form(){
		//echo '<br />';
		//echo 'process edit enroll dbpac form';

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	        $redirect_url = home_url( 'view-enrollment' );
			if(!empty($_POST)) {
				//echo '<br />';
				//echo 'not empty';
				// Check the if user is logged in
				if(is_user_logged_in()) {
					//echo '<br />';
					//echo 'user logged in';

					global $current_user;
      				wp_get_current_user();
					$user_id = $current_user->ID;

					$enrollment_id = $_POST['enrollment_id'];
					$enrollment_id = absint($enrollment_id);

					// decide whether this is for what contest enrollment
					// by checking the hidden input from the front-end form
					$contest = ($_POST['contest']);

					// processing dbpac enrollment form
					if ($contest == 'dbpac'){
						$contest_name = '2017 DBPAC';
						// sanitize the post fields
						$selected_value = sanitize_text_field($_POST['enroll_contest']);
						//echo '<br />';
						//echo $selected_value;

						$options = explode(";", $selected_value);
						$instrument_name = $options[0];
						$division_name = $options[1];
						//echo '<br/>';
						//echo $instrument_name;
						//echo '<br/>';
						//echo $division_name;
						// get the fees from division name
						// example division name: "P Solo 1a - Age 5 (2 minutes) - $35"
						$extract_fees = explode(" - ", $division_name);
						$fees = str_replace('$', '', $extract_fees[2]);
					} // end - processing dbpac enrollment form

					// processing baroque enrollment form
					else {
						$contest_name = '2017 Baroque';
						// sanitize the post fields
						$selected_value = sanitize_text_field($_POST['enroll_contest']);
						echo '<br />';
						echo $selected_value;

						$instrument_name = $selected_value;
						echo '<br/>';
						echo $instrument_name;

						// set division to no division
						$division_name = 'no divion';

						if($instrument_name == 'Mixed Ensemble'){
							$fees = '10';
						}
						else {
							$fees = '15';
						}						
					} // end - processing baroque enrollment form
					
					// change $fees to int type
					

					$song_title = sanitize_text_field($_POST['song_title']);

					$song_duration = sanitize_text_field($_POST['song_duration']);
					$mixed_durations = explode(":", $song_duration);
					$minute = absint($mixed_durations[0]);
					$second = absint($mixed_durations[1]);
					$duration = $minute * 60 + $second;

					//echo '<br/>';
					//echo $duration;

					$composer_name = sanitize_text_field($_POST['composer_name']);
					// do not update these hiden fields - is_enrolled, is_paid
					//$is_enrolled = 'yes';
					//$is_paid = 'no';
					
					$is_morning = $_POST['is_morning'];

					$student_id = $_POST['sel_student'];
									
					// could be multiple students for duo, trio, quartet and mixed ensemble
					// so handle it in create_enrollment function
					$fees = absint($fees);
					
					//echo '<br/>';
					//echo $fees;

					// TODO #######################################kF
					// call dbpac_dbapi::update_dbpac_enrollments
					$data = compact('instrument_name', 'division_name', 'song_title', 'duration', 'composer_name', 'fees', 'is_morning');

					$result = dbpac_dbapi::update_dbpac_enrollments($enrollment_id, $data);
					if ($result === false) {
						$errors = 'edit enrollment errors';
						$redirect_url = add_query_arg( 'edit-enrollment-errors', $errors, $redirect_url );
					}
					if (is_wp_error($result)){
						$errors = join( ',', $result->get_error_codes() );
	                	$redirect_url = add_query_arg( 'edit-enrollment-errors', $errors, $redirect_url );
					}else {
						$redirect_url = home_url( 'view-enrollment' );
	                	$redirect_url = add_query_arg( 'edited', $enrollment_id, $redirect_url );
					}
					
					
				} // if(is_user_logged_in())
				wp_redirect( $redirect_url );
		        exit;
			} // if(!empty($_POST))
		} // if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) 
	}


	/***********************************************************************************************
	 * ########## NOT USE ##########
	 * update_student
	 *
	 * updating array of student records by calling Dbpac_Dbapi 
	 **********************************************************************************************/
	private function update_student($user_id, $contest_name, $instrument_name, $division_name, $song_title, $duration, $composer_name, $fees, $is_enrolled, $is_paid, $student_ids) {

		// if more than one student, that means entring contest as a group so, set group Id to be the sum
		// of student's id's
		$group_id = 0;

		if (count($student_ids) > 1) {
			// group id starts from 10000
			$group_id += 10000;
			foreach ($student_ids as $student_id){
				$group_id += $student_id;
			}
			$data = compact('user_id', 'contest_name', 'instrument_name', 'division_name', 'song_title', 'duration', 'composer_name', 'group_id', 'fees', 'is_enrolled', 'is_paid');
		} else {

			$data = compact('user_id', 'contest_name', 'instrument_name', 'division_name', 'song_title', 'duration', 'composer_name', 'group_id', 'fees', 'is_enrolled', 'is_paid');
		}

		// echo '<br/>';
		// print_r($data);

		$result = false;
		foreach ($student_ids as $student_id){

			// echo '<br/>';
			// echo $student_id;

			$result = Dbpac_Dbapi::update_dbpac_students($student_id, $data);
			if ($result === false)
				return false;
		}
		return true;
	}
	
	/***********************************************************************************************
	 * populate_students
	 *
	 * for the enroll contest form student selec fields
	 **********************************************************************************************/
	public static function populate_students($isDbpac){

		if(is_user_logged_in()){
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			global $wpdb;
			$table_name = $wpdb->prefix . 'dbpac_students';

			$query = "SELECT student_id, first_name, last_name, dob
						FROM $table_name
						WHERE user_id = $user_id
						";

		    $students = $wpdb->get_results($query);  

		    $count = count($students);

		    if ($count !== 0){
		    	echo "<label>Select one or more students for this enrollment <span class='required'>*</span>";
	            echo "<select name='sel_students[]' multiple >";

	            foreach($students as $row){
	            	unset($id, $first, $last);
                  	$id = $row->student_id;
                  	$first = $row->first_name;
                  	$last = $row->last_name;
                  	$dob = $row->dob;
                  	echo '<option value="'.$id.'">'.$first.' '.$last. ' (' .$dob. ')' .'</option>';
	            }               
	            echo "</select>"; 
	            echo "</label>" ;

	            // add instruction on how to select multiple students
	            echo "<label style='color:#c45544;'>" . " * Use 'Control' and 'Mouse' keys to choose multiple students for Duet, Trio, Quartet and Group Contest." . "</label>";

	            if($isDbpac == true){
		            echo '
		            		</div>
	            				<input type="hidden" name="action" value="enroll_contest">
	            				<div class="button-section">
	                			<center>
	                   				<input type="submit" name="enroll_dbpac" value="Enroll" /> 
	                			</center>    
	            				</div>
		            		';
	            } 
	            else {
	            	echo '
		            		</div>
	            				<input type="hidden" name="action" value="enroll_contest">
	            				<div class="button-section">
	                			<center>
	                   				<input type="submit" name="enroll_baroque" value="Enroll" /> 
	                			</center>    
	            				</div>
		            		';
	            }
			}
			else {
				echo '
						</div>
						<div>							
							<h4> You have no students added </h>								
							<p> Please go to <a href="'.home_url('add-student').'">add student</a> to enroll a contest.</p>							
						</div>

					';					
				
			}
		}// is_user_logged_in

	}// end of populate_students


		/***********************************************************************************************
	 * populate_students_for_edit
	 *
	 * for the enroll contest form student selec fields
	 **********************************************************************************************/
	public static function populate_students_for_edit($enrollment_id){

		if(is_user_logged_in()){
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			global $wpdb;

			$enrollment_table_name = $wpdb->prefix . 'dbpac_enrollments';
		    $enrollment_id = absint($enrollment_id);		    
		    $sql = $wpdb->prepare( "SELECT student_id FROM `$enrollment_table_name` WHERE `enrollment_id` = %d",  $enrollment_id );     
		    $enrollment_row = $wpdb->get_row($sql);

		    // retreive students to be populated 
			$table_name = $wpdb->prefix . 'dbpac_students';
			$query = "SELECT student_id, first_name, last_name, dob
						FROM $table_name
						WHERE user_id = $user_id AND student_id = $enrollment_row->student_id
						";
		    $students = $wpdb->get_results($query); 
		    $count = count($students);
		    if ($count !== 0){
		    	/*
		    	echo "<label>The following student is enrolled.";
	            //echo "<select name='sel_students[]' multiple >";
	            echo "<select name='sel_student' >";

	            foreach($students as $row){
	            	unset($id, $first, $last);
                  	$id = $row->student_id;
                  	$first = $row->first_name;
                  	$last = $row->last_name;
                  	$dob = $row->dob;
                  	// pre-select for enrollment id for editing
                  	// 1) check enrollment id
                  	// 2) if it is a group with (group id)
                  	// 3) use <option selected> as selected depends on the student id of enrollment table
                  	if ($id == $enrollment_row->student_id){
                  		echo '<option selected value="'.$id.'">'.$first.' '.$last. ' (' .$dob. ')' .'</option>';
                  	}
                  	else {
                  		echo '<option value="'.$id.'">'.$first.' '.$last. ' (' .$dob. ')' .'</option>';
                  	}
	            }               
	            echo "</select>"; 
	            echo "</label>" ;
				*/
				foreach($students as $row) {
					unset($id, $first, $last);
                  	$id = $row->student_id;
                  	$first = $row->first_name;
                  	$last = $row->last_name;
                  	$dob = $row->dob;
                  	if ($id == $enrollment_row->student_id){
                  		echo '<label><strong style="color:#c45544; font-size:125%">Update' .' ' .$first.' '.$last. ' (' .$dob. ')' . ' enrollment information</strong>';
                  	}
                  	else {
                  		echo '<label style="color:#c45544;"">No student found';
                  	}
				}
				echo "</label>";
	            // add instruction on how to select multiple students
	            // echo "<label style='color:#c45544;'>" . " * The selected student is currently enrolled. Please update the student if you would like." . "</label>";
	            //  echo "<label style='color:#c45544;'>" . " Please contact <a href='mailto:joanna@teachers.org'>Ms. Lo</a> for editing the group enrollment." . "</label>";
	            
			}
			else {
				echo '
						</div>
						<div>							
							<h4> You should have already enrolled to edit enrollment </h>								
							<p> Please go to <a href="'.home_url('enroll-contest').'">enollment</a> to enroll a contest.</p>
						</div>

					';					
				
			}
		}// is_user_logged_in

	}// end of populate_students_for_edit


	/***********************************************************************************************
	 * process_update_profile
	 *
	 * processing update profile form
	 **********************************************************************************************/
	public function process_update_profile(){		
		
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			
	        $redirect_url = home_url( 'profile' );

			if(!empty($_POST)) {
	
				global $current_user;
	      		wp_get_current_user();
				$user_id = $current_user->ID;

				if(empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email'])
					|| empty($_POST['address']) || empty($_POST['phone'])) {
					$redirect_url = add_query_arg( 'profile-errors', 'empty field(s).', $redirect_url );
					wp_redirect($redirect_url);
					exit;
				}
				
				// update first name
		        if (!empty($_POST['first_name'])){
		        	$firstname = sanitize_text_field($_POST['first_name']);
		        	if($current_user->first_name != $firstname){
		        		if(!update_user_meta($user_id, 'first_name', $firstname)) {
							$redirect_url = add_query_arg( 'profile-errors', 'update failed', $redirect_url );
							wp_redirect($redirect_url);
							exit;
						}
					}
		        }		        	
		        
		        // update last name
		        if (!empty($_POST['last_name'])){
		        	$lastname = sanitize_text_field($_POST['last_name']);
		        	if($current_user->last_name != $lastname){
		        		if(!update_user_meta($user_id, 'last_name', $lastname)){
							$redirect_url = add_query_arg( 'profile-errors', 'update failed', $redirect_url );
							wp_redirect($redirect_url);
							exit;
						}
					}		        			        	
		        }

		        // update email    
		        if (!empty( $_POST['email'])) {
				    $email = sanitize_text_field($_POST['email']);
				    if ($current_user->user_email != $email) {       
				        // check if email is free to use
				        if (email_exists( $email)){
	        				$redirect_url = add_query_arg( 'profile-errors', 'The email address you entered is not valid.', $redirect_url );
							wp_redirect($redirect_url);
							exit;
				        } else {
				            $args = array(
				                'ID'         => $user_id,
				                'user_email' => $email
				            );            
				        	wp_update_user( $args );
				       }   
				   }
				}

				// update address
		        if (!empty($_POST['address'])){
		        	$address = sanitize_text_field($_POST['address']);
		        	$user_address = get_user_meta($current_user->ID, 'user_address', true);
		        	if($user_address != $address){
		        		if (!update_user_meta($user_id, 'user_address', $address)){
							$redirect_url = add_query_arg( 'profile-errors', 'update failed', $redirect_url );
							wp_redirect($redirect_url);
							exit;
						}
					}		        			        	
		        }

		        // update phone    
		        if (!empty($_POST['phone'])){
		        	$phone = sanitize_text_field($_POST['phone']);
		        	$user_phone = get_user_meta($current_user->ID, 'user_phone', true);
		        	if($user_phone != $phone){
		        		if(!update_user_meta($user_id, 'user_phone', $phone)) {
							$redirect_url = add_query_arg( 'profile-errors', 'update failed', $redirect_url );
							wp_redirect($redirect_url);
							exit;
						}
		        		}		        	
		        	}		     
			} //if(!empty($_POST)) 	
			wp_redirect($redirect_url);
			exit;		
		} // if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
	} // end of process_update_profile	

	/***********************************************************************************************
	 * Adding the query variables
	 *
	 * to know what paypal pass it back /view-enrollment?checkout=cancel or /view-enrollment?checkout=success
	 **********************************************************************************************/
	
	public function add_query_vars($vars){
		$vars[] = 'checkout';
		$vars[] = 'student_id';
		$vars[] =  'enrollment_id';
		return $vars;
	}
	
	
} // end of Dbpac_Student Class definition