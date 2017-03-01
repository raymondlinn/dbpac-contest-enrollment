<?php

class Dbpac_Dbapi {

	public static $student_table;
	public static $erollment_table;

	public function _construct() {

	}
	// 11 columns
	public static function get_dbpac_student_table_columns() {
		return array(
			'student_id'=> '%d',
			'user_id'=> '%d',
			'last_name'=> '%s',
			'first_name'=> '%s',
			'dob'=> '%s',
			'user_name'=> '%s',
			'user_email'=> '%s',
			'user_phone'=> '%s',
			'accomp_name'=> '%s',
			'accomp_phone'=> '%s',
			'created'=> '%s',
		);
	}
	
	// =====================================================================================
	// Inserts a student into students table with supplied data
	//
	// @param $data array An array of key=>value pairs to be inserted
	// @return int the student ID of the inserted student. Or WP_Error or false on failure
	// =====================================================================================
	public static function insert_dbpac_students($data=array()) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_students';

		// set all the default values
		$data = wp_parse_args($data, array(
					'user_id'=> get_current_user_id(),
					'date'=> current_time('timestamp'),
		));
		// not sure why we need to check
		//if(!is_float($data['date']) || $data['date'] <= 0)
		//	return 0;

		$data['created'] = date_i18n('Y-m-d H:i:s', $data['date'], true);
		
		$colums_formats = Dbpac_Dbapi::get_dbpac_student_table_columns();
		// change keys to lower case
		$data = array_change_key_case($data);
		$data = array_intersect_key($data, $colums_formats);

		// reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys($data);
		$column_formats= array_merge(array_flip($data_keys), $column_formats);
		$wpdb->insert($table_name, $data, $column_formats);

		return $wpdb->insert_id;
	}

	// =====================================================================================
	// Updates students table with supplied data
	//
	// @param $student_id ID of students table to be updated
	// @param $data array An array of column=>value pairs to be updated
	// @return bool Whether the student table was successfully updated
	// =====================================================================================
	public static function update_dbpac_students ($student_id, $data=array()) {

		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_students';

		$student_id = absint($student_id);

		// echo '<br/>';
		// echo $student_id;

		if (empty($student_id)){
			// echo '<br/>';
			// echo 'no student id from absint()';
			return false;
		}

		$column_formats = Dbpac_Dbapi::get_dbpac_student_table_columns();
		// echo '<br/>';
		// print_r($column_formats);

		$data = array_change_key_case($data);
		// echo '<br/>';
		// echo 'array_change_keycase';
		// echo '<br/>';
		// print_r($data);

		$data = array_intersect_key($data, $column_formats);
		// echo '<br/>';
		// echo 'array_intersect_key';
		// echo '<br/>';
		// print_r($data);

		$data_keys = array_keys($data);
		// echo '<br/>';
		// echo 'data_keys';
		// echo '<br/>';
		// print_r($data_keys);

		$column_formats= array_merge(array_flip($data_keys), $column_formats);
		
		// echo '<br/>';
		// print_r($column_formats);

		if (false === $wpdb->update($table_name, $data, array('student_id'=> $student_id), $column_formats)) {
			return false;
		}
	}

	// =================================Currently not using=========================================
	// this is to retreive the student list by student_id
	// contest_name, instruement_id, division_id
	/***********************************************************************************************
	 * Retrieve students from the database matching $query
	 * $query is an array which can contain the following keys
	 *
	 * 'fields' - an array of columns to include in returned rows. Or 'count' count of rows. Default: empty (all fields)
	 * 'orderby' - student_id, user_id, group_id, contest_name, instrument_name or division_name. Default: user_id
	 * 'order' - asc or desc
	 * 'user_id' - Return only the rows that with user ID
	 * 'contest_name' - Return only the rows that with contest_name
	 * 'instrument_name' - Return only the rows that are with instrument_name
	 * 'division_name' - Return only the rows that are with division_name
	 * 'user_info' - Return the email and user name from users table
	 * 'number' - Only return the 'number' of rows
	 * 'offset' - Only return beginning from 'offset'	 
	 *
	 * @param $query Query array
	 * @return array Array of matching students. False on error.
	 **********************************************************************************************/
	public static function get_dbpac_students($query=array()){

		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_students';

		// echo '<strong>Input:</strong>';
		// echo '<br/>';
		// print_r ($query);

		// setting defaults
		$defaults = array(
			'fields'=>array(),
			'orderby'=>'user_id',
			'order'=>'desc',
			'user_id'=>0,
			'contest_name'=>NULL,
			'instrument_name'=>NULL,
			'division_name'=>NULL,
			'number'=>10,
			'offset'=>0
			);
		// echo '<br/>';
		// echo '<strong>defaults:</strong>';
		// echo '<br/>';
		// print_r ($defaults);
		// merging into $query
		$query = wp_parse_args($query, $defaults);
		// echo '<br/>';
		// echo '<strong>After Merging</strong>';
		// echo '<br/>';
		// print_r ($query);
		// cachng the $query
		$cache_key = 'dbpac_students:' .md5(serialize($query));
		$cache = wp_cache_get($cache_key);
		if(false !== $cache) {
			$cache = apply_filters('get_dbpac_students', $cache, $query);
			return $cache;
		}
		// import variables into symbol table from $query array
		extract($query);
		// echo '<br/>';
		// echo '<strong>After extract()</strong>';
		// echo '<br/>';
		// print_r($query);
		$allowed_fields = Dbpac_Dbapi::get_dbpac_student_table_columns();
		// echo '<br/>';
		// echo '<strong>allowed fields</strong>';
		// echo '<br/>';
		// print_r ($allowed_fields);
		if(is_array($fields)) {
			$fields = array_map('strtolower', $fields);
			// echo '<br/>';
			// echo '<strong>after array map</strong>';
			// echo '<br/>';
			// print_r ($fields);
			$fields = array_intersect($fields, $allowed_fields);
			// echo '<br/>';
			// echo '<strong>after arra intersect</strong>';
			// echo '<br/>';
			// print_r ($fields);
		}else {
			$fields = strtolower($fields);
			// echo '<br/>';
			// echo '<strong>strtolower (fields)</strong>';
			// echo '<br/>';
			// print_r ($fields);
		}

		//echo '<br/>';
		//print_r ($fields);

		if(empty($fields)) {
			// return all
			$select_sql = "SELECT* FROM $table_name";
		}
		elseif ('count' == $fields) {
			// umber of records
			$select_sql = "SELECT COUNT(*) FROM $table_name";
		}
		else {
			// return the fields (columns that are defined in the fields array)
			// join the filesd with comma separated 
			$select_sql = "SELECT ".implode(',', $fields)." FROM $table_name";
		}
		// echo '<br/>';
		// echo 'select sql';
		// echo '<br/>';
		// echo $select_sql;
		// join nothing to join currently unless it needs to display the teacher name 
		// in return of students then need to join with {$wpdb->prefix . 'users'} table
		// we are not using join here yet.
		$join_sql = '';

		// for WHERE SQL Statement
		$where_sql = 'WHERE 1=1';

		if(!empty($student_id)) {
			$where_sql .= $wpdb->prepare(' AND student_id=%d', $student_id);
		}

		if(!empty($user_id)) {
			$user_id = array($user_id);
			$user_id = array_map('absint', $user_id);
			$user_id__in = implode(',', $user_id);
			$where_sql .= " AND user_id IN($user_id__in)";
		}

		if (!empty($contest_name)) {
			$where_sql .= $wpdb->prepare(' AND contest_name=%s', $contest_name);
		}

		if (!empty($instrument_name)) {
			$where_sql .= $wpdb->prepare(' AND instrument_name=%s', $instrument_name);
		}

		if(!empty($division_name)) {
			$where_sql .= $wpdb->prepare(' AND division_name=%s', $division_name);
		}

		// order
		$order = strtoupper($order);
		$order = ('ASC' == $order ? 'ASC' : 'DESC');

		switch ($orderby) {
			case 'student_id':
				$order_sql = "ORDER BY student_id $order";
			break;

			case 'user_id':
				$order_sql = "ORDER BY user_id $order";
			break;

			case 'group_id':
				$order_sql = "ORDER BY group_id $order";
			break;

			case 'contest_name':
				$order_sql = "ORDER BY contest_name $order";
			break;

			case 'instrument_name':
				$order_sql = "ORDER BY instrument_name $order";
			break;

			case 'division_name':
				$order_sql = "ORDER BY division_name $order";
			break;

			default:
			break;
		}

		// Limit
		$offset = absint($offset);
		if ($number == -1) {
			$limit_sql = "";
		}else {
			$number = absint($number);
			$limit_sql = "LIMIT $offset, $number";
		}

		// filter 
		$pieces = array('select_sql', 'join_sql', 'where_sql', 'order_sql', 'limit_sql');
		$clauses = apply_filters('dbpac_students_clauses', compact($pieces), $query);
		foreach($pieces as $piece) {
			// updating the $piece by passing the reference
			$$piece = isset($clauses[$piece]) ? $clauses[$piece] : '';
		}

		// build SQL statement
		$sql = "$select_sql $where_sql $order_sql $limit_sql";
		if ('count' == $fields){
			return $wpdb->get_var($sql);
		}

		$students = $wpdb->get_results($sql);
		wp_cache_add($cache_key, $students, 24*60*60);
		$students = apply_filters('get_dbpac_students', $students, $query);

		return $students;
	}

	// =================================Currently not using=========================================
	/***********************************************************************************************
	 * deleting the record of the given student
	 *
	 *@param $student_id int ID of the students table
	 *@return bool Whether the entry was deleted successfully
	 **********************************************************************************************/
	public static function delete_dbpac_student($student_id) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'dbpac_students';

		$student_id = absint($student_id);
		if (empty($student_id)) {
			return false;
		}

		do_action($table_name, $student_id);

		$sql = $wpdb->prepare("DELETE from $table_name WHERE student_id = %d", $student_id);

		if (!$wpdb->query($sql)) {
			return false;
		}

		do_action('delete_dbpac_student', $student_id);

		return true;
	}

	// ===================================================================================== 
	// SECTION FOR dbpac_enrollments TABLE API IMPLMENTATION
	// ===================================================================================== 

	// 14 columns
	public static function get_dbpac_enrollment_table_columns() {
		return array(
			'enrollment_id'=> '%d',
			'student_id'=> '%d',
			'user_id'=> '%d',
			'group_id'=> '%d',
			'contest_name'=> '%s',
			'instrument_name'=> '%s',
			'division_name'=> '%s',
			'song_title'=> '%s',
			'composer_name'=> '%s',
			'duration'=> '%d',
			'fees'=> '%d',
			'is_enrolled'=> '%s',
			'is_paid'=> '%s',
			'is_morning'=> '%s',
			'created'=> '%s',
		);
	}

	// =====================================================================================
	// Enroll a student into enrollment table with supplied data
	//
	// @param $data array An array of key=>value pairs to be inserted
	// @return int the enrollment ID of the inserted student. Or WP_Error or false on failure
	// =====================================================================================
	public static function insert_dbpac_enrollments($data=array()) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_enrollments';

		// set all the default values
		$data = wp_parse_args($data, array(
					'user_id'=> get_current_user_id(),
					'date'=> current_time('timestamp'),
		));
		// not sure why we need to check
		//if(!is_float($data['date']) || $data['date'] <= 0)
		//	return 0;

		$data['created'] = date_i18n('Y-m-d H:i:s', $data['date'], true);
		
		$colums_formats = Dbpac_Dbapi::get_dbpac_enrollment_table_columns();
		// change keys to lower case
		$data = array_change_key_case($data);
		$data = array_intersect_key($data, $colums_formats);

		// reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys($data);
		$column_formats= array_merge(array_flip($data_keys), $column_formats);
		$wpdb->insert($table_name, $data, $column_formats);

		return $wpdb->insert_id;
	}

	// =====================================================================================
	// Updates enrollment table with supplied data
	//
	// @param $enrollment_id ID of students table to be updated
	// @param $data array An array of column=>value pairs to be updated
	// @return bool Whether the enrollment table was successfully updated
	// =====================================================================================
	public static function update_dbpac_enrollments ($enrollment_id, $data=array()) {

		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_enrollments';

		$enrollment_id = absint($enrollment_id);

		// echo '<br/>';
		// echo $student_id;

		if (empty($enrollment_id)){
			// echo '<br/>';
			// echo 'no student id from absint()';
			return false;
		}

		$column_formats = Dbpac_Dbapi::get_dbpac_enrollment_table_columns();
		// echo '<br/>';
		// print_r($column_formats);

		$data = array_change_key_case($data);
		// echo '<br/>';
		// echo 'array_change_keycase';
		// echo '<br/>';
		// print_r($data);

		$data = array_intersect_key($data, $column_formats);
		// echo '<br/>';
		// echo 'array_intersect_key';
		// echo '<br/>';
		// print_r($data);

		$data_keys = array_keys($data);
		// echo '<br/>';
		// echo 'data_keys';
		// echo '<br/>';
		// print_r($data_keys);

		$column_formats= array_merge(array_flip($data_keys), $column_formats);
		
		// echo '<br/>';
		// print_r($column_formats);

		if (false === $wpdb->update($table_name, $data, 
										array('enrollment_id'=> $enrollment_id), $column_formats)) {
			return false;
		}
	}

	// =====================================================================================
	// Get the data from students and enrollments table with SQL join statement particularly
	// for view-enrollment functionality and update is_paid fees functionality
	// =====================================================================================
	public static function get_enrollment_data_by_id($enrollment_id){
		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_enrollments';

		$enrollment_id = absint($enrollment_id);
		if (empty($enrollment_id)){
			// echo '<br/>';
			// echo 'no student id from absint()';
			return NULL;
		}
		$sql = "SELECT * FROM $table_name WHERE $id = $enrollment_id";
		$enrollment_row = $wpdb->get_results($sql);
		if (count($values) < 0){
			return NULL;
		}

		return $enrollment_row;
	}

	// ======================================================================================
	// Get Student entry by student ID
	// @param $student_id
	// @return $student_row - array of student data
	// ======================================================================================
	public static function get_stundent_data_by_id($student_id){
		global $wpdb;
		$table_name = $wpdb->prefix . 'dbpac_students';

		$student_id = absint($student_id);
		if (empty($student_id)){
			// echo '<br/>';
			// echo 'no student id from absint()';
			return NULL;
		}
		$sql = "SELECT * FROM $table_name WHERE $id = $student_id";
		$student_row = $wpdb->get_results($sql);
		if (count($values) < 0){
			return NULL;
		}

		return $student_row;
	}

} // end - Dbpac_Dbapi()