<?php

if(!define('WP_UNISTALL_PULGIN')){
	exit;
}

// delete options 
delete_option('dbpac-user');
delete_option('dbpac_student');

// drop table
global $wpdb;
$table_name = $wpdb->prefix . 'dbpac_students';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
//delete_option('student_db_version');

$division_table_name = $wpdb->prefix . 'dbpac_instruments_divisions';
$sql = "DROP TABLE IF EXISTS $division_table_name";
$wpdb->query($sql);
