<div id="view_enroll">
	<div class="view_table">
	<?php

		// check whether user is logged in
		if(!is_user_logged_in()) {
			echo "
				<h3><center>You need to log in to your acount to view enrollment.</center></h3>
			";
		} else {
			// check if DBPAC Season or Baroque Season
			$isDbpac = 'false';

			// preparing to get the data
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'dbpac_students';
			$enrollment_table_name = $wpdb->prefix . 'dbpac_enrollments';
			$contest = '';
			if($isDbpac == 'true'){
				// print header
				echo "		
					<h3><center> 2017 DBPAC Contest Registration</center></h3>
				";
				// prepare querey
				$query = "SELECT $table_name.student_id, $table_name.first_name, $table_name.last_name, $table_name.dob, $enrollment_table_name.division_name, $enrollment_table_name.composer_name, $enrollment_table_name.song_title, $enrollment_table_name.duration, $enrollment_table_name.group_id, $enrollment_table_name.fees, $enrollment_table_name.is_paid
						FROM $table_name
						INNER JOIN $enrollment_table_name
						ON ($table_name.student_id = $enrollment_table_name.student_id AND $table_name.user_id = $user_id AND $enrollment_table_name.is_enrolled = 'yes' AND $enrollment_table_name.contest_name = '2017 DBPAC')						
						ORDER BY $enrollment_table_name.group_id
					";
				
			} else{
				//print header
				echo "		
					<h3><center>2017 Baroque Festival Registration</center></h3>
				";
				// prepare querey
				$query = "SELECT $table_name.student_id, $table_name.first_name, $table_name.last_name, $table_name.dob, $enrollment_table_name.instrument_name, $enrollment_table_name.composer_name, $enrollment_table_name.song_title, $enrollment_table_name.duration, $enrollment_table_name.group_id, $enrollment_table_name.fees, $enrollment_table_name.is_paid
						FROM $table_name
						INNER JOIN $enrollment_table_name
						ON ($table_name.student_id = $enrollment_table_name.student_id AND $table_name.user_id = $user_id AND $enrollment_table_name.is_enrolled = 'yes' AND $enrollment_table_name.contest_name = '2017 Baroque')
						ORDER BY $enrollment_table_name.group_id						
					";
			
			}

			$students = $wpdb->get_results($query);						

			$count = count($students);

			if ($count == 0) {
				echo '
					<div class="view-enrollment-style" id="instruction">						
					<center>
						You have not enrolled any students. 
						<p>If you would like to enroll your students to Baroque Festival, please click <a href="'.home_url('enroll-contest').'">enroll student</a> to enroll your students to a contest.</p>
					</center>
					</div>						
				';
			} else {
				// if it is successfully checkout, say thank you here!
				$checkout = get_query_var('checkout');
				if ($checkout == 'success'){
					echo thank_you();
				}	
				// -- START -- Printing Sponsor information
				echo sponsor_information();
				// -- END -- Printing Sponsor information	

				// -- START -- Table Header for Enrollment List
				echo "
					<div id='view-enrollment-table'>
					<table id='enrollment-table'>
						<thead>
							<tr>							
								<th> First Name </th>
								<th> Last Name </th>
				";
				if($isDbpac == true) {
					echo "
									<th> Division </th>
					";
				}else {	
					echo "
									<th> Instrument </th>
					";
				}
				echo "				
								<th> DOB </th>
								<th> Composer Name </th>
								<th> Song Title </th>
								<th> Duration (seconds) </th>
								<th> Group ID </th>
								<th> Fees (in dollar) </th>
								<th> Payment </th>
							</tr>		
						</thead>
						<tbody>
				";
				// -- END -- Table Header for Enrollment List

				// intitialize $total_fees
				$total_fees = 0;

				// -- START -- filling the Enrollment List table 
				foreach($students as $row){
					echo "<tr>";        			
        			echo "<td>" . $row->first_name . "</td>";
        			echo "<td>" . $row->last_name . "</td>"; 

        			if($isDbpac == 'true'){
        				echo "<td>" . $row->division_name . "</td>";        			
        			}else {
        				echo "<td>" . $row->instrument_name . "</td>";
        			}

        			echo "<td>" . $row->dob . "</td>";
        			echo "<td>" . $row->composer_name . "</td>";
        			echo "<td>" . $row->song_title . "</td>";
        			echo "<td>" . $row->duration . "</td>";
        			echo "<td>" . $row->group_id . "</td>";        			
        			echo "<td>" . $row->fees . "</td>";
        			
        			// --- START -- filling the 'add to cart' button -------
    				if ($row->is_paid == 'no'){
    					// add the 67 divisions will be tested in this if else case here
    					if ($isDbpac == 'true'){		    				
							// add division_name and enrollment id in the product name
    						$name = str_replace(' ', '', $row->division_name);
    						$product = $name . '-id=' . $row->enrollment_id; 						
							echo "<td>" . do_shortcode("[wp_cart_button name=$product  price=$row->fees]") . "</td>";						
						} // if ($isDbpac == 'true')

						// only 5 instruments (4 solos and one ensemble)
						else {
							//echo baroque_add_to_cart($row->instrument_name);
							// add instrument_name and enrollment id in the product name
							$name = str_replace(' ', '', $row->instrument_name);
    						$product = $name . '-id=' . $row->enrollment_id;
							echo "<td>" . do_shortcode("[wp_cart_button name=$product price=$row->fees]") . "</td>";
						} // else -- $isDbpac == 'true'						
	    			} // if ($row->is_paid == 'no')
	    			// --- END -- filling the 'add to cart' button -------

	    			else {
	    				//echo "<td>" . $row->is_paid . "</td>";
	    				echo "<td>paid</td>";
	    			}	    	
        			
        			echo "</tr>";        			
        			$total_fees +=	$row->fees;
				} // foreach ends
				// -- END -- filling the Enrollment List table 
				
				// add rows for total fees and check 
				echo check_payment($total_fees, $isDbpac);
				
				// closing table and add pagination
				echo "
					</tbody>
				</table>
				</div>
				<div id='pagination'>
				</div>
				";

				// add instrucntion
				echo instrunction_on_payment();
				// add view cart and enroll contest buttons
				echo add_view_cart_n_enroll_buttons();
				// adding print-freiendly
				if(function_exists('pf_show_link')){
					echo pf_show_link();
				}

			} // else $count == 0
		} // else is_user_logged_in()
	?>	
	</div> <!-- class="view_table" -->
</div><!-- id="view_enroll" -->

<?php

	function thank_you(){
		echo '
				<div class="view-enrollment-style" id="instruction">						
					<center>
						<h4>Thank you for the enrollment.</h4>						
						<p> 
						You have enrolled your student(s) sucessfully. We will be sending you an email.
						</p>
					</center>
				</div>	
		';
	}

	function sponsor_information(){
		$sponsor_info = "";
		$sponsor_info .= "<div class='view-enrollment-style' id='instruction'>
							<p>
								<strong>Sponsor or Teacher Name: </strong>";
		global $current_user; 
		wp_get_current_user(); 				 
		$sponsor_info .= $current_user->first_name;
		$sponsor_info .= " "; 
		$sponsor_info .= $current_user->last_name; 

		$sponsor_info .= "<br/>  
							<strong>Email: </strong>";			 
		$sponsor_info .= $current_user->user_email; 
		 
		$sponsor_info .= "<br/>  
							<strong>Address: </strong>"; 
		$user_address = get_user_meta($current_user->ID, 'user_address', true);  				 
		$sponsor_info .= $user_address;			
		
		$sponsor_info .= "<br/>  
							<strong>Phone: </strong>"; 
		$user_phone = get_user_meta($current_user->ID, 'user_phone', true);  				 
		$sponsor_info .= $user_phone;
		
		$sponsor_info .= "</p> 
						</div>
						";			
		return $sponsor_info;
	}

	function check_payment($total_fees, $isDbpac){
		$check_payment = "";
		// total fees row
		$check_payment .= "<div class='check_payment'>"; 
		$check_payment .= "<tr>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		if($isDbpac == 'true'){
        	// need to add processing fees
        	$total_fees += 10;
        	$check_payment .= "<td>" . "Total Fees:"  . "</td>";
			$check_payment .= "<td> <u>" . $total_fees . "________" . "</u></td>";
			$check_payment .= "<td><strong>" . "$10 processsing fees is added" . "</strong></td>
							</tr>
							";
        }else {						
			$check_payment .= "<td>" . "Total Fees:"  . "</td>";
			$check_payment .= "<td> <u>" . $total_fees . "________" . "</u></td>";
			$check_payment .= "<td></td>
							</tr>
							";
		}
		
		// last row with total fees and check number to be filled out.
		$check_payment .= "<tr>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";
		$check_payment .= "<td></td>";				
    	$check_payment .= "<td>" . "Check Number:"  . "</td>";
    	$check_payment .= "<td>" . "___________" . "</td>";
    	$check_payment .= "<td></td>";
    	$check_payment .= "</tr>";
    	$check_payment .= "</div>";

    	return $check_payment;
	}

	function instrunction_on_payment(){
		$instruction = "";
		$instruction .= "
						<br/>
						<p> 
							* Group ID: 0 means the student is in Solo and Group ID other than 0 means the students with the same Group ID are in either Duet, Trio, Quartet or Group contest.
							<br/>
							* If you would like to pay by PayPal, click 'Add to Cart' button and it will take you to PayPal.
							<br/>
							* If you would like to pay by check, print the enrolled list form by clicking the print button on the bottom right corner, write a check with payable to <strong>DBPAC</strong> and mail the form and check to <strong>DBPAC, PO BOX 4043 Diamond Bar, CA_91765.</strong> 
							<br/>
							* Contact info: Ms. Lo, email: joanna@teachers.org
							<br/>
						</p>					

					";
		return $instruction;
	}

	function add_view_cart_n_enroll_buttons(){
		$cart_enroll_buttons = "";
		$cart_enroll_buttons .= '				
								<a href="'.home_url('enroll-contest').'"><button>Enroll student</button></a>
								<a href="'.home_url('shopping-cart').'"><img src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_viewcart_113x26.png" alt="Pay for Enrollment"></a>

							';
		return $cart_enroll_buttons;			 
	}
?>

