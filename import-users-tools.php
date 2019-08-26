<?php
/***
#  Import user Tool Page
***/

# create class
class dd_tool_page {

	# define construct
	function __construct() {
		# add admin menu
		add_action( 'admin_menu', array( $this, 'dd_admin_menu' ) );
	}

	# admin menu call back
	function dd_admin_menu() {
		# create page in tools.php
		add_submenu_page( 'tools.php', 'Import Users with Courses', 'Import Users with Courses', 'manage_options', 'diversity-import-user-page',  array( $this, 'sensei_import_users_callback' ));
	}

	# import sensei user callback
	function sensei_import_users_callback() {

		# if submit 
	 	if(isset($_POST['sensie-users-csv-submit']))
		{
			# check file size
			if ($_FILES['sensie-users-csv'][size] > 0) 
			{
			    # get the csv file properties
				$file_name = $file = $_FILES['sensie-users-csv']['name']; 
			    $file = $_FILES['sensie-users-csv']['tmp_name']; 

			    # get file extention from file 
				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
				
				# if csv file
				if($ext=='csv')
				{
					# upload to upload directory
				    $upload = wp_upload_bits($_FILES['sensie-users-csv']['name'], null, file_get_contents($file));

				    # if no error in upload file
				    if($upload['error'] == "")
				    {
				    	# get file url
					    if($upload['file'])
					    {
					    	# import user start
					    	$csv = $this->DDreadCSVnew($upload['file']);
					    }
					    else
						{
							?>
							<div class="notice notice-error"><h4><?php _e('Oops! Something Went Wrong!','diversity'); ?></h4></div>
							<?php
						}
					}
					else
					{
						?>
						<div class="notice notice-error"><h4><?php _e('Oops! Something Went Wrong!','diversity'); ?></h4></div>
						<?php
					}
				}
				else
				{
					?>
					<div class="notice notice-error"><h4><?php _e('You Must Upload Only File with .CSV Extension','diversity'); ?></h4></div>
					<?php
				}
			}
			else
			{
				?>
				<div class="notice notice-error"><h4><?php _e('Oops! Something Went Wrong!','diversity'); ?></h4></div>
				<?php
			}
		}
		
		?>
		
		<div class="wrap">
			<h1><?php _e('Import Users With Courses','diversity');?></h1> 
			<div class="narrow">
				<p><?php _e('Howdy! Upload your Users data (CSV) file and we’ll import the Users and User Courses into this site.','diversity'); ?></p>
				<p><?php _e('Choose a Specific formated (.csv) file to upload, Please Download Sample file from below link for reference, then click Upload file and import.','diversity'); ?></p>
				<form enctype="multipart/form-data" id="sensie-users-import-upload-form" method="post" class="wp-upload-form" action="">
					<p>
						<label for="upload"><?php _e('Choose a file from your computer:','diversity');?></label> <!-- (Maximum size: 128 MB) -->
						<input type="file" id="sensie-users-csv" name="sensie-users-csv" size="25">
					</p>
					<p class="submit">
						<input type="submit" name="sensie-users-csv-submit" id="sensie-users-csv-submit" class="button button-primary" value="Upload file and import">
						<?php 
						$sample_file_exists = get_template_directory()."/sample/sensei-users.csv";
						if (file_exists($sample_file_exists)) 
						{
							$sample_file =  get_template_directory_uri()."/sample/sensei-users.csv";
							?>
							<a href="<?php echo $sample_file;?>" download><?php _e('Download Sample','diversity');?></a>
							<?php 
						} ?>
					</p>
				</form>
			</div>
		</div><!-- .wrap -->
		<?php
	}

	function DDreadCSVnew( $csvFile ){

		# set time limit
		set_time_limit(0);

		# user role
		$user_role = "customer";

		# get wordpress default role
		$default_role = get_option( 'default_role' );

		# define raw
		$row = 1;

		# Open csv file from path
		$file_handle = fopen( $csvFile, 'r' ); 

		# Output lines until EOF is reached
		while ( !feof( $file_handle ) ) { 
			# get fata from file
			$data = fgetcsv($file_handle);
		
			# Skip first line as it is label row
			if( $data && $row != 1){
				
				#echo "<br><hr><br>";
				#echo "<br>first_name : ";
				$first_name = trim($data[0]);
				#echo "<br>last_name : ";
				$last_name = trim($data[1]);
				#echo "<br>user_email : ";
				$user_email = trim($data[2]);
				#echo "<br>user_password : ";
				$user_password = trim($data[3]);				
				#echo "<br>organization : ";
				$organization = trim($data[4]);
				#echo "<br>course_ids : ";
				$course_ids = trim($data[5]);
				
				if($user_email && $user_password)
				{
					# create usernames from user email
					$usename = explode("@", $user_email)[0];

					# create user data 
					$userdata_array = array (
				        'first_name'    =>  $first_name,
				        'last_name'    =>  $last_name,
				        'user_pass'     =>  $user_password,
				    ) ;

				    # check user name and user email already exists or not
					if ( email_exists($user_email) == false && username_exists($usename) == false ) 
					{
			      		# not exists userdata
			      		$not_exists_userdata =  array ('user_login' =>  $usename,'user_email' =>  $user_email);

			      		# add not exists userdata to all user data
			      		$userdata_array =  $userdata_array + $not_exists_userdata;

			      		# insert new users
					    $user_id = wp_insert_user( $userdata_array ) ;

					} else {
						# update userdata
					    $user_id = wp_update_user( $userdata_array ) ;
					}

					# update/insert user successfully
					if($user_id)
					{
					 	# update user role , if empty then role set as subscriber
				   		$selected_role = !empty($user_role) ? $user_role : $default_role;
					    wp_update_user( array ('ID' => $user_id, 'role' => $selected_role) ) ;

					    # organization available
					    if($organization)
					    {
					    	# update billing company
							update_user_meta($user_id,'billing_company',$organization);
						}
						$message =  '<div class="notice notice-success"><h4>'.__('Data Import Successfully','diversity').'</h4></div>';
						# get course ids 
						$course_ids = array_map('trim', explode(',', $course_ids));
						if(!empty($course_ids))
						{
							# course ids
							if (class_exists('Sensei_Utils'))
							{
								foreach ($course_ids as $key => $course_id) 
								{
									# add courses to user
									$result = Sensei_Utils::user_start_course( $user_id, $course_id );									
								}
							}
							else
							{
								$message =  '<div class="notice notice-error"><h4>'.__('Sensei_Utils class not exists','diversity').'</h4></div>';
							}
						}
						
				    }
				}
				//echo "<br><hr><br>";
			} 
			$row++;
		} //While loop end
		echo $message;
		fclose($file_handle);
	}
}
new dd_tool_page;
?>