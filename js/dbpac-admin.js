;(function($){

	$(function(){		
		/**
		 * Initialize form validation on update_pay form which is from admin page.
		 * It has the name attribute "update_pay"
		 * need jquery-validate
		 */
		$("form[name='update_pay']").validate({
			// Specify validation rules
			ignore: [],
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
				'is_paid[]': {
					required: true,
					paidInput: ["yes", "no"]
				}
			},
			errorPlacement: function(error, element) 
		    {
		        if ( element.is(":input") ) 
		        {
		            error.appendTo( element.parents('#view-enrollment-table') );
		        }
		        else
		        { // This is the default behavior
		            error.insertAfter( element );
		        }
		    },
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
			  form.submit();
			}
		});

		$.validator.addMethod("paidInput", function(value, element, param) { 
	  		return this.optional(element) || value === param[0] || value === param[1] ; 
		}, "Please Enter 'yes' or 'no' ");
	});
});