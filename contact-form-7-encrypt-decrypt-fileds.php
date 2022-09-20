<?php
# required plugins:
# - contact form 7 => https://wordpress.org/plugins/contact-form-7/
# - Contact Form 7 Database Addon – CFDB7 => https://wordpress.org/plugins/contact-form-cfdb7/

add_filter('cfdb7_before_save_data','cfdb7_before_save_data_callback', 10, 1);
function cfdb7_before_save_data_callback($form_data)
{
	if(!empty($form_data))
	{
		$encrepted_data = array();
		
		foreach ($form_data as $form_key => $form_value) {
			if(!is_array( $form_value))
			{
				$encrepted_value = cv_twoway_encrypt($form_value,'e');
				$encrepted_data[$form_key] = $encrepted_value;
			}
			else
			{
				if(is_array($form_value))
				{
					$encrepted_sub_data = array();
					foreach ($form_value as $sub_key => $sub_value) {
						$encrepted_sub_value = cv_twoway_encrypt($sub_value,'e');
						$encrepted_sub_data[$sub_key] = $encrepted_sub_value;
						$encrepted_data[$form_key] = $encrepted_sub_data;
					}
				}
				else
				{
					$encrepted_data[$form_key] = $form_value;
				}
			}
		}
		return $encrepted_data;
	}
	return $form_data;	
}


function cv_twoway_encrypt($stringToHandle = "",$encryptDecrypt = 'e'){
     // Set default output value
     $output = null;
     // Set secret keys
     $secret_key = 'jf8gf8g^3*s'; // Change this!
     $secret_iv = 'd&&9"dh4%:@'; // Change this!
     $key = hash('sha256',$secret_key);
     $iv = substr(hash('sha256',$secret_iv),0,16);
     // Check whether encryption or decryption
     if($encryptDecrypt == 'e'){
        // We are encrypting
        $output = base64_encode(openssl_encrypt($stringToHandle,"AES-256-CBC",$key,0,$iv));
     }else if($encryptDecrypt == 'd'){
        // We are decrypting
        $output = openssl_decrypt(base64_decode($stringToHandle),"AES-256-CBC",$key,0,$iv);
     }
     // Return the final value
     return $output;
}

add_action('cfdb7_before_formdetails_title','cfdb7_before_formdetails_title_callback', 10, 1);
function cfdb7_before_formdetails_title_callback( $form_post_id)
{
	if(isset($_POST['cv-view-data-pass']) && !empty($_POST['cv-view-data-pass']))
  	{
		$password = get_option('cfdb7-password');
		if($_POST['cv-view-data-pass'] != $password)
		{
			echo '<div class="notice notice-error is-dismissible">
	             <p>Your Password is wrong!</p>
	         </div>';
	    }
	}
}
add_action('cfdb7_after_formdetails','cfdb7_after_formdetails_callback', 10, 1);

function cfdb7_after_formdetails_callback( $form_post_id ){ 

  	if(isset($_POST['cv-view-data-pass']) && !empty($_POST['cv-view-data-pass']))
  	{
  		$password = get_option('cfdb7-password');
  		if($_POST['cv-view-data-pass'] == $password)
  		{
  			?>
  			<style type="text/css">
  				#welcome-panel
  				{
  					display: none;
  				}
  			</style>
  			<?php
  			global $wpdb;
	        $cfdb          = apply_filters( 'cfdb7_database', $wpdb );
	        $table_name    = $cfdb->prefix.'db7_forms';
	        $upload_dir    = wp_upload_dir();
	        $cfdb7_dir_url = $upload_dir['baseurl'].'/cfdb7_uploads';
	        $rm_underscore = apply_filters('cfdb7_remove_underscore_data', true); 
	        $form_post_id = isset( $_GET['fid'] ) ? (int) $_GET['fid'] : 0;
       		$form_id      = isset( $_GET['ufid'] ) ? (int) $_GET['ufid'] : 0;


	        $results    = $cfdb->get_results( "SELECT * FROM $table_name WHERE form_post_id = $form_post_id AND form_id = $form_id LIMIT 1", OBJECT );
	        echo '<div class="notice notice-success is-dismissible">
	             <p>Data decrypted.</p>
	         </div>';
	        ?>
	        <div id="welcome-panel-password" class="cfdb7-panel">
                <div class="cfdb7-panel-content">
                    <div class="welcome-panel-column-container">
                        <?php do_action('cfdb7_before_formdetails_title',$form_post_id ); ?>
                        <h3><?php echo get_the_title( $form_post_id ); ?></h3>
                        <?php do_action('cfdb7_after_formdetails_title', $form_post_id ); ?>
                        <p></span><?php echo $results[0]->form_date; ?></p>
                        <?php $form_data  = unserialize( $results[0]->form_value );
                        	
                        foreach ($form_data as $key => $data):

                            $matches = array();
                            $key     = esc_html( $key );

                            if ( $key == 'cfdb7_status' )  continue;
                            if( $rm_underscore ) preg_match('/^_.*$/m', $key, $matches);
                            if( ! empty($matches[0]) ) continue;

                            if ( strpos($key, 'cfdb7_file') !== false ){

                                $key_val = str_replace('cfdb7_file', '', $key);
                                $key_val = str_replace('your-', '', $key_val);
                                $key_val = str_replace( array('-','_'), ' ', $key_val);
                                $key_val = ucwords( $key_val );
                                echo '<p><b>'.$key_val.'</b>: <a href="'.$cfdb7_dir_url.'/'.$data.'">'
                                .$data.'</a></p>';
                            }else{


                                if ( is_array($data) ) {

                                    $key_val      = str_replace('your-', '', $key);
                                    $key_val      = str_replace( array('-','_'), ' ', $key_val);
                                    $key_val      = ucwords( $key_val );
                                    $arr_str_data =  implode(', ',$data);
                                    $arr_str_data = cv_twoway_encrypt($arr_str_data,'d');
                                    $arr_str_data =  esc_html( $arr_str_data );
                                    echo '<p><b>'.$key_val.'</b>: '. nl2br($arr_str_data) .'</p>';

                                }else{

                                    $key_val = str_replace('your-', '', $key);
                                    $key_val = str_replace( array('-','_'), ' ', $key_val);

                                    $key_val = ucwords( $key_val );
                                    $data = cv_twoway_encrypt($data,'d');
                                    $data    = esc_html( $data );
                                    echo '<p><b>'.$key_val.'</b>: '.nl2br($data).'</p>';
                                }
                            }

                        endforeach;

                        /*$form_data['cfdb7_status'] = 'read';
                        $form_data = serialize( $form_data );
                        $form_id = $results[0]->form_id;

                        $cfdb->query( "UPDATE $table_name SET form_value =
                            '$form_data' WHERE form_id = '$form_id' LIMIT 1"
                        );*/
                        ?>
                    </div>
                </div>
            </div>
            <?php
  		}
  	}
} 

add_action('cfdb7_before_formdetails_title','cfdb7_before_formdetails_title_two_callback', 10, 1);
function cfdb7_before_formdetails_title_two_callback( $form_post_id)
{
	if(!isset($_POST['cv-view-data-pass']))
  	{
		cfdb_password_form();
	}
	else
	{
		$password = get_option('cfdb7-password');
  		if($_POST['cv-view-data-pass'] != $password)
  		{
			cfdb_password_form();
		}
	}
}
function cfdb_password_form()
{
	echo '<a href="javascript:void(0);" class="cv-view-content">Decrypt Data</a>';
		?>
		<style type="text/css">
			.cfdb7-form-wrap
			{
				display: none;
			}
		</style>
		<script type="text/javascript">
			 jQuery(".cv-view-content").click(function(){
		    jQuery(".cfdb7-form-wrap").toggle();
		  });
		</script>
  	<form method="POST" class="cfdb7-form-wrap">
  		<div>
  			<label>Enter Password</label>
  			<input type="password" name="cv-view-data-pass">
  		</div>
  		<div>
  			<input type="submit" name="cv-view-data-pass-subit" value="Submit">
  		</div>
  	</form>
  	<?php
}

//add_submenu_page('cfdb7-list.php', 'Security', 'Security', 'manage_options', 'cfdb7-security',  'cfdb7_security' );
//add_menu_page( __( 'Contact Forms Security', 'contact-form-cfdb7' ), __( 'Contact Forms', 'contact-form-cfdb7' ), 'manage_options', 'cfdb7-security', 'cfdb7_security', 'dashicons-lock',11 );
add_submenu_page('tools.php', 'Contact Forms DB Security', 'Contact Forms DB Security', 'manage_options', 'cfdb7-security',  'cfdb7_security' );
/**
 * Extensions page
 */
function cfdb7_security(){
	$password = get_option('cfdb7-password');

	if(isset($_POST['cfdb7-password']) && isset($_POST['cfdb7-password-save']))
	{
		$user_password = $_POST['cfdb7-password'];
		update_option('cfdb7-password',$user_password);
		echo '<div class="notice notice-success is-dismissible">
             <p>Setting Saved.</p>
         </div>';
	}
    ?>
    <div class="wrap">
        <h2><?php _e( 'Security', 'contact-form-cfdb7' ); ?>
        </h2>
        <form action="" method="post" name="cfdb7-security" id="cfdb7-security">
	        <table class="form-table">
	            <tr class="form-field form-required">
	                <th scope="row"><label for="cfdb7-password"><?php _e('Password'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
	                <td><input name="cfdb7-password" type="password" id="cfdb7-password" required value="<?php echo $password; ?>" aria-required="true" style="width:250px;"/></td>
	            </tr>
	        </table>
	        <?php submit_button( __( 'Save '), 'primary', 'cfdb7-password-save', true, array( 'id' => 'cfdb7-password-save' ) ); ?>
	    </form>
    </div>
    <?php
}