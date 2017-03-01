<div class="view_table">
	<h3><center>View Your Student</center></h3>	
<?php

	if(is_user_logged_in()){
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'dbpac_students';
			$query = "SELECT student_id, first_name, last_name, dob, accomp_name, accomp_phone
						FROM $table_name
						WHERE user_id = $user_id
						";
			$students = $wpdb->get_results($query);

			$count = count($students);

			if ($count === 0){
				echo '
						<div class="view-enrollment-style" id="instruction">
							<center>
							You have no students added. 
							<p>If you would like to add your students for enrolling, please <a href="'.home_url('add-student').'">add student</a> first to enroll a contest.</p>
							</center>
						</div>
						';				
			} else {
				echo '
				<div id="view-student-table">
				<table>
					<thead>
						<tr>							
							<th> First Name </th>
							<th> Last Name </th>
							<th> DOB </th>
							<th>Accompanist Name</th>
							<th>Accompanist Phone</th>
							<th>Update</th>
						</tr>		
					</thead>
					<tbody>';

				foreach($students as $row){
					echo "<tr>";        			
        			echo "<td>" . $row->first_name . "</td>";
        			echo "<td>" . $row->last_name . "</td>";
        			echo "<td>" . $row->dob . "</td>";
        			echo "<td>" . $row->accomp_name . "</td>";
        			echo "<td>" . $row->accomp_phone . "</td>";
        			echo "<td> <a href=" .add_query_arg('sutdent_id', $row->student_id, home_url('edit-student')) . ">edit</a></td>";
        			echo "</tr>";	
				}
				echo '
					</tbody>
				</table>
				</div>
				<div id="pagination">
				</div>
				
				';
				echo '
					<br/>
					<a href="'.home_url('add-student').'"><button>Add student</button></a>
				';
			}			
	}		
?>

</div>

	


