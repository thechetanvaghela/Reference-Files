<?php
/*
For non logged in users
*/
function enable_maintenance_mode() {
    if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) 
    {
      	wp_die('Briefly unavailable for scheduled maintenance. Check back in a minute..');
	}
}
add_action('get_header', 'enable_maintenance_mode');