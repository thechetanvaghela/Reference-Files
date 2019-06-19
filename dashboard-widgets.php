<?php
/*
*  DASHBOARD WIDGETS
*/

// Function that outputs the contents of the dashboard widget
function dashboard_widget_function( $post, $callback_args ) {
		$args = array(
	    'number'     => $number,
	    'orderby'    => $orderby,
	    'order'      => $order,
	    'hide_empty' => true,
	    'include'    => $ids
	);

	$product_categories = get_terms( 'product_cat', $args );
	foreach( $product_categories as $cat )  { 
		$link = get_term_link( $cat->term_id, 'product_cat' );
	   echo "<b><a href='".$link."' target='_blank'>".$cat->name.' ('.$cat->count.')'."</a></b><br>"; 
	}
}

// Function used in the action hook
function add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', 'Products Category', 'dashboard_widget_function');
}
// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'add_dashboard_widgets' );
?>