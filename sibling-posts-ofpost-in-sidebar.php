<?php
function sidebar_siblingpost(){
	ob_start();
	$current_page_id = get_the_id();
	if(!empty($current_page_id))
	{
		global $post;
		$post_types = $post->post_type;
		//$post_types = array('what-we-do');
		$siblings_args = array(
		    'post_type'      => $post_types,
		    'posts_per_page' => -1,
		    'post_status'    => 'publish',
		    'order'          => 'ASC',
		    'orderby'        => 'publish_date'
			 );
		$siblings = new WP_Query( $siblings_args );
		
		if ( $siblings->have_posts() ) : 
		echo '<div class="widget">';
		echo '<ul>';
			 while ( $siblings->have_posts() ) : $siblings->the_post();
				$addclass  = "";
				if($current_page_id == get_the_ID())
				{
					$addclass  = "active";
				}
			  ?>						
				<li class="<?php echo $addclass; ?>"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
			    <?php 
			endwhile; 

			echo '</ul>';
			echo '</div>';
		 endif; 
			wp_reset_postdata(); 
	}
	
	return ob_get_clean();
}
add_shortcode('siblings-listing','sidebar_siblingpost');