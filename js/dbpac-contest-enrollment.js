/**
 *	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script> 
 */

 ;(function($){

	$(function(){

		/**
		 * for populating options for instrument and division
		 */		
		$.getJSON( dbpacScript.pluginsUrl + '/dbpac-contest-enrollment/js/dbpac-options.json', function(data){

			var $select = $("<select>", {name: 'enroll_contest', id: 'dbpac_division'});
			$.each(data, function(i, optgroups) {

			    $select.appendTo("#division-select");

			    $.each(optgroups, function(groupName, options) {
			        var $optgroup = $("<optgroup>", {label: groupName});
			        $optgroup.appendTo($select);

			        $.each(options, function(j, option) {
			            var $option = $("<option>", {text: option.division, id: option.divisionID, 
			            	value: groupName + ';' + option.division});
			            $option.appendTo($optgroup);
			        });
			    });
			});
		});

		/**
		 * for the dob datepicker
		 * <link rel="stylesheet" 
		 * href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
 		 * <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		 */
		$("#datepicker").datepicker({
	            changeMonth: true,
	            changeYear: true,
	            yearRange: '-100:+0'
	    });

		/**
		 * for selecting contest between DBPAC and Baroque Forms
		 */	    
	   	/* Turn off for one contest at a time
		    $('.contest_form').hide();
		    //$('#opt_dbpac option:selected');
		    //$('#sel_dbpac').show();
		    $('#select_contest').change(function(){
		    	$('.contest_form').hide();
		    	$('#'+$(this).val()).show(100);
		    }).change();
		*/
	    
	   	/**
		 * Initialize form validation on the signupform form.
		 * It has the name attribute "signupform"
		 * need jquery-validate
		 */ 
		$("form[name='signupform']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				first_name: "required",
				last_name: "required",
				email: {
				required: true,
				// Specify that email should be validated
				// by the built-in "email" rule
				email: true
				},
				password: {
				required: true,
				minlength: 5
				},
				phone: {
					required: true,
					phoneUS: true
				},
				address:
				{
					required: true,
				}
			},
			// Specify validation error messages
			messages: {
				firstname: "Please enter your first name",
				lastname: "Please enter your last name",
				password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long"
				},
				email: "Please enter a valid email address",
				phone: "Please enter a valid phone nmuber",
				address: "Please enter a valid address"
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});


		/**
		 * Initialize form validation on the add-student form.
		 * It has the name attribute "addstudent"
		 * need jquery-validate
		 */
		$("form[name='addstudent']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				student_fname: "required",
				student_lname: "required",
				student_dob: "required",
				accompanist_fname: {
					required: true,
					rangelength: [5, 64]
				},
				accompanist_phone: {
					required: true,
					phoneUS: true
				}
			},
			// Specify validation error messages
			messages: {
				student_fname: "Please enter your first name",
				student_lname: "Please enter your last name",
				student_dob: "Please select your date of birth",
				accompanist_fname: {
					required: "Please enter accompanist full name",
					rangelength: "Accompnist full name must be between 5 and 64 letters long"
				},
				accompanist_phone: "Please enter a valid phone nmuber"
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});
/*
		$.validator.addMethod("phoneUS", function (phone_number, element) {
			phone_number = phone_number.replace(/\s+/g, "");
			return this.optional(element) || phone_number.length > 9 &&
			      phone_number.match(/\(?[\d\s]{3}\)[\d\s]{3}-[\d\s]{4}$/);
		}, "Invalid phone number");
*/
		/**
		 * Initialize form validation on the enroll_dbpac form.
		 * It has the name attribute "enroll_dbpac"
		 * need jquery-validate
		 */
		$("form[name='enroll_dbpac']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				dbpac_contest: "required",
				song_title: {
					required: true,
					rangelength:[5, 64]
				},
				song_duration: {
					required: true,
					duration: true
				},
				composer_name: {
					required: true,
					rangelength: [5, 64]
				},
				'sel_students[]': {
					required: true,					
					dbpac_check_select: true
				}
			},
			// Specify validation error messages
			messages: {
				dbpac_contest: "Please select instrument/division",
				song_title: {
					required: "Please enter song title",
					rangelength: "Song title must be between 5 and 64 letters long"
				},
				song_duration: "Please enter duration of the song in mm:ss format",
				composer_name: {
					required: "Please enter composer name",
					rangelength: "Composer name must be between 5 and 64 letters long"
				},
				'sel_students[]': {
					required: "Please select one or more students to enroll"
				}
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});

		var check_select_students = function(length) {
			var isError = true;
			// check if divison consits of 'Solo' then it is the solo
			if($("#dbpac_division option:selected").text().indexOf('Solo') === -1
				&& length <= 1) {
				iError = false;
			}
			else {
				isError = true;
			}
			return isError;
		};
		
		$.validator.addMethod("dbpac_check_select", function(dbpac_select , element){	
			var length = dbpac_select.length;		
			return this.optional(element) || check_select_students(length); 
		}, "Please make sure you select one student for solo contest or more than one student for group contest.");

		
		$.validator.addMethod("duration", function(song_duration , element){
			return this.optional(element) || song_duration.match(/(^[0-5]\d:[0-5]\d$)/g);
		}, "Invalid duration");

		/**
		 * Initialize form validation on the enroll_baroque form.
		 * It has the name attribute "enroll_baroque"
		 * need jquery-validate
		 */
		$("form[name='enroll_baroque']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				dbpac_contest: "required",
				song_title: {
					required: true,
					rangelength:[5, 64]
				},
				song_duration: {
					required: true,
					duration: true
				},
				composer_name: {
					required: true,
					rangelength: [5, 64]
				},
				'sel_students[]': {
					required: true,					
					baroque_check_select: true
				}
			},
			// Specify validation error messages
			messages: {
				dbpac_contest: "Please select instrument",
				song_title: {
					required: "Please enter song title",
					rangelength: "Song title must be between 5 and 64 letters long"
				},
				song_duration: "Please enter duration of the song in mm:ss format",
				composer_name: {
					required: "Please enter composer name",
					rangelength: "Composer name must be between 5 and 64 letters long"
				},
				'sel_students[]': {
					required: "Please select one or more students to enroll"
				}
			},

			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});

		var check_select = function(length) {
			var isError = true;
			if ($("#baroque_instrument option:selected").text() === "Mixed Ensemble" 
				&& length <= 1) {
				isError = false;
			}
			else
				isError = true;
			return isError;
		};
		
		$.validator.addMethod("baroque_check_select", function(baroque_select , element){
			var length = baroque_select.length;
			return this.optional(element) || check_select(length); 
		}, "Please make sure you select one student for solo contest or more than one student for group contest.");

		/**
		 * Initialize form validation on the updateprofileform form.
		 * It has the name attribute "updateprofileform"
		 * need jquery-validate
		 */
		$("form[name='updateprofileform']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				first_name: "required",
				last_name: "required",
				email: {
				required: true,
				// Specify that email should be validated
				// by the built-in "email" rule
				email: true
				},
				phone: {
					required: true,
					phoneUS: true
				},
				address:
				{
					required: true,
				}
			},
			// Specify validation error messages
			messages: {
				firstname: "Please enter your first name",
				lastname: "Please enter your last name",
				password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long"
				},
				email: "Please enter a valid email address",
				phone: "Please enter a valid phone nmuber",
				address: "Please enter a valid address"
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});

		/**
		 * Initialize form validation on the editstudent form.
		 * It has the name attribute "editstudent"
		 * need jquery-validate
		 */
		$("form[name='editstudent']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				student_fname: "required",
				student_lname: "required",
				student_dob: "required",
				accompanist_fname: {
					required: true,
					rangelength: [5, 64]
				},
				accompanist_phone: {
					required: true,
					phoneUS: true
				}
			},
			// Specify validation error messages
			messages: {
				student_fname: "Please enter your first name",
				student_lname: "Please enter your last name",
				student_dob: "Please select your date of birth",
				accompanist_fname: {
					required: "Please enter accompanist full name",
					rangelength: "Accompnist full name must be between 5 and 64 letters long"
				},
				accompanist_phone: "Please enter a valid phone nmuber"
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});


		/**
		 * Initialize form validation on the edit_enrollment form.
		 * It has the name attribute "edit_enrollment"
		 * need jquery-validate
		 */
		$("form[name='edit_enrollment']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				dbpac_contest: "required",
				song_title: {
					required: true,
					rangelength:[5, 64]
				},
				song_duration: {
					required: true,
					duration: true
				},
				composer_name: {
					required: true,
					rangelength: [5, 64]
				}
			},
			// Specify validation error messages
			messages: {
				dbpac_contest: "Please select instrument/division",
				song_title: {
					required: "Please enter song title",
					rangelength: "Song title must be between 5 and 64 letters long"
				},
				song_duration: "Please enter duration of the song in mm:ss format",
				composer_name: {
					required: "Please enter composer name",
					rangelength: "Composer name must be between 5 and 64 letters long"
				}
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});


		/**
		 * Pagination features for table
		 */
		var pagination = function(){

			var req_num_row = 20;
			var table_row = $('#enrollment-table > tbody > tr');
			var total_num_row = table_row.length;
			var num_pages = 0;
			if(total_num_row % req_num_row == 0){
				num_pages = total_num_row / req_num_row;
			}
			if(total_num_row % req_num_row >= 1){
				num_pages = total_num_row / req_num_row;
				num_pages++;
				num_pages = Math.floor(num_pages++);
			}
			
			for(var i = 1; i <= num_pages; i++){
				$('#pagination').append("<a> " + i + " </a>");
			}
			table_row.each(function(i){
				$(this).hide();
				if(i + 1 <= req_num_row){
					table_row.eq(i).show();
				}
			
			});

			$('#pagination a').click(function(e){
				e.preventDefault();
				table_row.hide();
				var page = $(this).text();
				var temp = page - 1;
				var start = temp * req_num_row;
				//alert(start);				
				for(var i = 0; i < req_num_row; i++){					
					table_row.eq(start+i).show();				
				}
			});
		};
		pagination();

	});

}(jQuery));



