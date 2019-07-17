<?php
function custom_column_header( $columns ){
    $columns['portfolio_cat'] = 'Shortcode'; 
    return $columns;
}
add_filter( "manage_edit-types_columns", 'custom_column_header', 10);

function custom_column_content( $value, $column_name, $tax_id ){
  
	$term = get_term_by('id',$tax_id, 'types');
	$term_slug = $term->slug;
    if ( 'portfolio_cat' == $column_name ) {
        $content = '[portfolio cat="'.$term_slug.'" number="9"]';
    }
	return $content;
}
add_action( "manage_types_custom_column", 'custom_column_content', 10, 3);


function portfolio_cat_shortcode_callback($params = array()) {
	extract(shortcode_atts(array(
		'cat' => "",
		'number' => 9,
	), $params));
	ob_start(); 

		$args = array('
			post_type'=>'portfolio',	//post_type
			'posts_per_page' => $number,
			'tax_query' => array(
			    array(
			        'taxonomy' => 'types', // taxonomy
			        'field' => 'slug',
			        'terms' => $cat  	//slug
			   		 )
				)
			) ;
		$arr_posts = new WP_Query( $args );
		
		 
		if ( $arr_posts->have_posts() ) :
		 	echo '<div class="portfolio_cat"><a href="'.get_term_link($cat,'types').'">';
		    while ( $arr_posts->have_posts() ) :
		        $arr_posts->the_post();
		        ?>
		        <div class="col-md-4">
		            <?php
		            if ( has_post_thumbnail() ) :
		                the_post_thumbnail(array(200,200));
		            endif;
		            ?>
		        </div>
		        <?php
		    endwhile;
		    echo '</a></div>';
		endif;
    
    return ob_get_clean();
}
add_shortcode('portfolio', 'portfolio_cat_shortcode_callback');