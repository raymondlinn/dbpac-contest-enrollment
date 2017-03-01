<?php		
		
		/*
		// 20 columns
		$sql = "CREATE TABLE $table_name (
			student_id bigint(20) unsigned NOT NULL auto_increment,
			user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			group_id bigint(20) unsigned NOT NULL DEFAULT '0',
			last_name varchar(20) NOT NULL DEFAULT 'LastName',
			first_name varchar(20) NOT NULL DEFAULT 'FirstName',
			dob date NOT NULL DEFAULT '0000-00-00',
			user_name varchar(64) NOT NULL DEFAULT 'username',
			user_email varchar(100) NOT NULL DEFAULT 'useremail',
			accomp_name varchar(64) NOT NULL DEFAULT 'accompanistName',
			accomp_phone varchar(10) NOT NULL DEFAULT '0000000000',
			contest_name varchar(20) NOT NULL DEFAULT 'contest',
			instrument_name varchar(20) NOT NULL DEFAULT 'instrument',
			division_name varchar(70) NOT NULL DEFAULT 'division',
			song_title varchar(64) NOT NULL DEFAULT 'song',
			composer_name varchar(64) NOT NULL DEFAULT 'composerName',
			duration int NOT NULL DEFAULT '240',
			fees int NOT NULL DEFAULT '0',
			is_enrolled varchar(3) NOT NULL DEFAULT 'no',
			is_paid varchar(3) NOT NULL DEFAULT 'no',
			created datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY  (student_id),
			KEY user_id (user_id),
			KEY group_id (group_id),
			KEY contest_name (contest_name),
			KEY instrument_name (instrument_name),
			KEY division_name (division_name)
		) $charset_collate;";
		*/
			
		/*
		// another table that stores the instrument and division data
		$table_name = $wpdb->prefix . 'dbpac_instruments_divisions';
		$charset_collate = $wpdb->get_charset_collate();

		// 16 columns
		$sql = "CREATE TABLE $table_name (
			instrument_id int NOT NULL, 
			instrument_name varchar(14) NOT NULL, 
			division_id int NOT NULL,
			division_name varchar(66) NOT NULL,
			fees numeric(4,2) NOT NULL,		
			KEY instrument_id (instrument_id),
			KEY division_id (division_id)
		) $charset_collate;";	
		
		dbDelta($sql);	
		*/	

		public static function get_dbpac_student_table_columns() {
		return array(
			'student_id'=> '%d',
			'user_id'=> '%d',
			'group_id'=> '%d',
			'last_name'=> '%s',
			'first_name'=> '%s',
			'dob'=> '%s',
			'user_name'=> '%s',
			'user_email'=> '%s',
			'user_phone'=> '%s',
			'accomp_name'=> '%s',
			'accomp_phone'=> '%s',
			'contest_name'=> '%s',
			'instrument_name'=> '%s',
			'division_name'=> '%s',
			'song_title'=> '%s',
			'composer_name'=> '%s',
			'duration'=> '%d',
			'fees'=> '%d',
			'is_enrolled'=> '%s',
			'is_paid'=> '%s',
			'created'=> '%s',
		);
	}
	AND $table_name.user_id = $user_id
						AND $enrollment_table_name.is_enrolled = 'yes'
						AND $enrollment_table_name.contest_name = '2017 Baroque'
						ORDER BY group_id
?>			