<?php
	global $wpdb;
	$types = $wpdb->get_results( "SELECT post_type as 'type', COUNT(*) as 'count' FROM $wpdb->posts GROUP BY post_type" );
	$result = array();
	foreach( $types as $type )
		array_push( $result, "{$type->type} ({$type->count})" );
	echo implode( $result, ', ' );


	/* OUT PUT something like that*/
	echo " attachment (309), finance-faq (33), finance-project (3), finance-service (6), finance-team (7), finance-testimonial (6), mc4wp-form (1), nav_menu_item (61), noosa-optical-jobs (1), optical-products (10), page (29), porfolio (1), portfolio (19), post (37), revision (235), what-we-do (10), why-choose-us (12), wpcf7_contact_form (3) " ;
?>