<?php
#redirect non logged user to specific page
add_action( 'template_redirect', 'redirect_to_specific_page' );
function redirect_to_specific_page() {
	if ( !is_page('sample-page') && ! is_user_logged_in() ) {
		wp_redirect( home_url()."/sample-page/" ); 
  		exit();
    }
}
?>