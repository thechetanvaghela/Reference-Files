<?php
add_action( 'comment_form_top', 'wpcr_change_comment_form_defaults');
function wpcr_change_comment_form_defaults( ) {


    $star1_title = __('Very bad', 'post-rating');
    $star2_title = __('Bad', 'post-rating');
    $star3_title = __('Meh', 'post-rating');
    $star4_title = __('Pretty good', 'post-rating');
    $star5_title = __('Rocks!', 'post-rating');


    echo '<fieldset class="rating">
    <legend>Rating<span class="required">*</span></legend>
    <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="'.$star5_title.'">5 stars</label>
    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="'.$star4_title.'">4 stars</label>
    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="'.$star3_title.'">3 stars</label>
    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="'.$star2_title.'">2 stars</label>
    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="'.$star1_title.'">1 star</label>
    </fieldset>';

}
//////// save comment meta data ////////
add_action( 'comment_post', 'wpcr_save_comment_meta_data' );

function wpcr_save_comment_meta_data( $comment_id ) {
    $rating =  (empty($_POST['rating'])) ? FALSE : $_POST['rating'];
    add_comment_meta( $comment_id, 'rating', $rating );
}
if(!is_admin())
{

function top_rated_post_via_comment($post_per_page = 5){
 global $wpdb;
	
	$results = $wpdb->get_results("SELECT DISTINCT(wp_comments.comment_post_ID), GROUP_CONCAT(wp_comments.comment_iD separator ', ') comment_ids FROM wp_comments JOIN wp_commentmeta ON wp_commentmeta.comment_id = wp_comments.comment_ID GROUP BY wp_comments.comment_post_ID", ARRAY_A);


	foreach($results as $key => $value) {

		$c_post_id = $value['comment_post_ID'];
		$comment_ids = $value['comment_ids'];
	    $res = $wpdb->get_results( "SELECT AVG(`meta_value`) as avg_rate FROM wp_commentmeta WHERE `meta_key` = 'rating' AND comment_ID IN ($comment_ids) ORDER BY meta_value" );
	   $results[$key]['avg_rate'] = $res[0]->avg_rate;
  	}
  	# sort value by high rated
  	$avg_rate = array_column($results, 'avg_rate');
	array_multisort($avg_rate, SORT_DESC, $results);

	$top_rated = array();
	foreach ($results as $result) {
	
		if($result['avg_rate'] && $result['comment_ids'] )
		{
			$top_rated[] = $result['comment_post_ID'];
		}
	}
	
	$args = array(
		'post_type' => "services",
	    'posts_per_page' => $post_per_page,
	    'post__in' => $top_rated,
	    'orderby' => 'post__in' 
	);
	
	$top_rated_posts = new WP_Query( $args );

	// The Loop
	if ( $top_rated_posts->have_posts() ) {
		echo '<ul>';
		
		while ( $top_rated_posts->have_posts() ) {
			$top_rated_posts->the_post();
			$new_key = array_search(get_the_id(), array_column($results, 'comment_post_ID'));
			echo '<li>Post Name : ' . get_the_title() . ' | Average Rate :'.number_format((float)$results[$new_key]['avg_rate'], 2, '.', '').'</li>';
			
		}
		echo '</ul>';
		
		wp_reset_postdata();
	} else {
		// no posts found
	}
}


add_filter( 'manage_servicies_posts_columns', 'set_custom_edit_columns' );
add_filter( 'manage_posts_columns', 'set_custom_edit_columns' );
function set_custom_edit_columns($columns) {
    $columns['avg_rate'] = __( 'Average Rate', 'your_text_domain' );
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_posts_custom_column' , 'custom_column', 10, 2 );
add_action( 'manage_servicies_custom_column' , 'custom_column', 10, 2 );
function custom_column( $column, $post_id ) {
    switch ( $column ) {

        case 'avg_rate' :
            global  $wpdb;
          	$results = $wpdb->get_results("SELECT DISTINCT(wp_comments.comment_post_ID), GROUP_CONCAT(wp_comments.comment_iD separator ', ') comment_ids FROM wp_comments JOIN wp_commentmeta ON wp_commentmeta.comment_id = wp_comments.comment_ID GROUP BY wp_comments.comment_post_ID", ARRAY_A);
			foreach($results as $key => $value) {

				$c_post_id = $value['comment_post_ID'];
				$comment_ids = $value['comment_ids'];
			    $res = $wpdb->get_results( "SELECT AVG(`meta_value`) as avg_rate FROM wp_commentmeta WHERE `meta_key` = 'rating' AND comment_ID IN ($comment_ids) ORDER BY meta_value" );
			   $results[$key]['avg_rate'] = $res[0]->avg_rate;
		  	}
		  	$new_key = array_search($post_id, array_column($results, 'comment_post_ID'));
            if($results[$new_key]['avg_rate']){
            echo number_format((float)$results[$new_key]['avg_rate'], 2, '.', '');
        	}
        	else
        	{
        		echo "No rating";
        	}
            break;

      

    }
}
?>
