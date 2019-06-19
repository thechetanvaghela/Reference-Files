<?php
function remove_all_scripts_from_theme() {
    global $wp_scripts;
    # remove all js
    // $wp_scripts->queue = array();
    foreach( $wp_scripts->queue as $handle ) {
    	
    	if (strpos($wp_scripts->registered[$handle]->src, '/themes/') !== false) {
        	# dequeue js
        	  wp_dequeue_script( $handle );
			    # deregister js
			   wp_deregister_script( $handle);
		}
	}
   		
}
//add_action('wp_print_scripts', 'remove_all_scripts_from_theme', 100);

function remove_all_styles_from_theme() {
    global $wp_styles;
     # remove all css
   // $wp_styles->queue = array();

    foreach( $wp_styles->queue as $handle ) {
    	
    	if (strpos($wp_styles->registered[$handle]->src, '/themes/') !== false) {
        	# dequeue js
        	  wp_dequeue_style( $handle );
			    # deregister js
			   wp_deregister_style( $handle);
		}
	}
}
?>