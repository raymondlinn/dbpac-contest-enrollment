<?php
/**
 * Plugin Name: DBPAC Contest Enrollment 
 * Plugin URI:  
 * Description: Contest enrollment functionality for DBPAC application.
 * Author: Raymond Linn
 * Version: 1.0
 * Author URI: http://raymondlinn.com/
 */

// don't allow direct call to this file
//if(!defined('WPINC')) {
//	die;
//}

/**
 *	main function to load contest management plugin
 */
function run_dbpac_contest_management_plugin() {

	// Create the custom pages at plugin activation
	include_once dirname(__FILE__) . '/class-dbpac-user-management.php';

	register_activation_hook(__FILE__, array('Dbpac_User_Manager', 'activate_user_manager'));
	register_deactivation_hook(__FILE__, array('Dbpac_User_Manager', 'deactive_user_manager'));	
	$dbpac_user_management = new Dbpac_User_Manager();

	include_once dirname(__FILE__) . '/class-dbpac-student.php';
	
	Dbpac_Student::$version = '1.0.0';
	register_activation_hook(__FILE__, array('Dbpac_Student', 'create_dbpac_student_table'));
	register_activation_hook(__FILE__, array('Dbpac_Student', 'activate_student'));
	register_deactivation_hook(__FILE__, array('Dbpac_Student', 'deactive_student'));
	$dbpac_student = new Dbpac_Student();

	include_once dirname(__FILE__) . '/class-dbpac-enrollments-export.php';	
	$dbpac_csv_export = new Dbpac_Csv_Export();

	// register and enqueue scripts and styles
	add_action('wp_enqueue_scripts', 'add_dbpac_scripts');
	// use for google api
	add_filter( 'clean_url', 'dbpac_async_defer_scripts', 11, 1 );

	add_action('template_redirect','pages_allow_only_logged_in_user');
	
	add_action('populate_students_for_enroll_form', array('Dbpac_Student', 'populate_students'));
	// populating students for edit enrollment
	add_action('populate_students_for_edit_enrollment', array('Dbpac_Student', 'populate_students_for_edit'));

	add_action('template_redirect', 'redirect_user_to_home');
	add_action('update_profile', array('Dbpac_Student', 'edit_profile_form'), 12, 1);

	add_action('after_setup_theme', 'remove_admin_bar');
}

/**
 * register and enqueue style and java scripts from add_action
 */
function add_dbpac_scripts() {

	dbpac_add_style_sheet();

    // add js	
    dbpac_add_jquery_scripts();

	// add google places api script
	dbpac_add_goggle_places_api_script();
	
    // add live address scripts from smartystreets
	//dbpac_add_liveaddress_script();	

	// for local dbpac contest js
	dbpac_add_local_scripts();
}

/** 
 * add style sheet
 */
function dbpac_add_style_sheet() {
	// add style sheet
	wp_register_style( 'dbpac-style', plugins_url('css/style.css', __FILE__));
	wp_enqueue_style('dbpac-jquery-ui-css',
                'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css',
                false,
                'PLUGIN_VERSION',
                false);
    wp_enqueue_style('dbpac-style');
}

/**
 * add jquery, jquery_ui, jquery-validate scripts
 */
function dbpac_add_jquery_scripts() {
	// jquery
	wp_enqueue_script('jquery');
	// jquery-ui
	if (!wp_script_is('jquery-ui')) {
		wp_enqueue_script('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
	}
	// jquery-validate
	if (!wp_script_is('jquery-validate')) {
		wp_enqueue_script('jquery-validate', 'https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.min.js');
	}
	// jquery-validate additional method
	if(!wp_script_is('jquery-validate-addition')){
		wp_enqueue_script('jquery-validate-addition', 'https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js');
	}
}

/**
 * add google places api script 
 * currently not using it
 * this will need to use async defer flag so add the clean_url hook if use this
 */
function dbpac_add_goggle_places_api_script() {

	// for google places API 
	// the filter dbpac_async_defer_scripts needs to be turned on
	wp_register_script('google-map-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDxCIwG8OVweG-WkSpfXAUY-x_-B9H9vro&libraries=places&callback=initAutocomplete#asyncdefer', 
		'', '', true);	
	wp_register_script('dbpac-address-autocomplete', plugins_url('js/dbpac-google-autocomplete.js', __FILE__), '', '', true);
	global $post;
	if(is_page(array('member-register', 'profile'))){ 

		wp_enqueue_script('google-map-api');
		wp_enqueue_script('dbpac-address-autocomplete');
	}
}


/**
 * add smartystreets live address script
 * this is to validate the address that user fill in the form
 * use with address form feild id #freeform on CSS selector
 */
function dbpac_add_liveaddress_script(){
	// live address

	wp_register_script('smartystreets-api', 'https://d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/3.2/jquery.liveaddress.min.js');	
	wp_register_script('dbpac-address-autocomplete', plugins_url('js/dbpac-address-autocomplete.js', __FILE__), array('jquery'));
	if(is_page(array('member-register', 'profile'))){ 

		wp_enqueue_script('smartystreets-api');
		wp_enqueue_script('dbpac-address-autocomplete');
	}	
}

/**
 * add dbpac related local scripts
 */
function dbpac_add_local_scripts(){
	wp_register_script('dbpac-contest-enrollment', plugins_url('js/dbpac-contest-enrollment.js', __FILE__), array( 'jquery'));
	wp_enqueue_script('dbpac-contest-enrollment');
	wp_localize_script('dbpac-contest-enrollment', 'dbpacScript', array('pluginsUrl' => plugins_url()));	
	
	/*
	wp_register_script('dbpac-ajax', plugins_url('js/dbpac-ajax.js', __FILE__), array( 'jquery'));
	wp_enqueue_script('dbpac-ajax');
	wp_localize_script('dbpac-ajax', 'dbpacAjax', array('ajaxurl'=>admin_url('admin-ajax.php')));
	*/
}
/**
 * adding google place API script to footer of the page that needs to verify address
 * Async load for clean_url hook
 */
function dbpac_async_defer_scripts($url) {
    if ( strpos( $url, '#asyncdefer') === false )
        return $url;
    else if ( is_admin() )
        return str_replace( '#asyncdefer', '', $url );
    else
	return str_replace( '#asyncdefer', '', $url )."' async='async"."' defer='defer"; 
}

/**
 * allowing the logged in user to specific pages
 */
function pages_allow_only_logged_in_user()
{
    if(!is_user_logged_in() && is_page(
    	array('profile', 'view-enrollment', 'view-student', 'add-student', 'enroll-contest', 'shopping-cart', 'update-payment')))
    {
        wp_redirect(home_url('member-login'));
        exit;
    }
} 

/**
 * If user is already logged in and user type in the the following pages in the url bar
 * redirect to home page
 */
function redirect_user_to_home(){
	/*
	if(is_page('profile')){
		wp_redirect(home_url('view-student'));
		exit;
	}

	else */if(is_user_logged_in() && is_page(array('member-login', 'member-register', 'member-password-lost', 'member-password-reset'))){
		wp_redirect(home_url());
		exit;
	}
}

/**
 * remove admin bar for all users except admin
 */
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  show_admin_bar(false);
	}
}
	
// load contest management plugin
run_dbpac_contest_management_plugin();