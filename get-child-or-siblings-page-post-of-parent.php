<?php
/* Sidebar Child Page */
function sidebar_childpages(){
	ob_start();

	$current_page_id = get_the_id();
	$step_parent_id = wp_get_post_parent_id( $current_page_id );
	global $post;
	if ($post->post_parent)  {
		$ancestors=get_post_ancestors($post->ID);
		$root=count($ancestors)-1;
		$parent_id = $ancestors[$root];
	} else {
		$parent_id = $post->ID;
	}

	if(!empty($parent_id))
	{
		$child_args = array(
		    'post_type'      => 'post', //page
		    'posts_per_page' => -1,
		    'post_parent'    => $parent_id,
		    'post_status'    => 'publish',
		    'order'          => 'ASC',
		    'orderby'        => 'menu_order'
			 );
		$parent = new WP_Query( $child_args );
		
		if ( $parent->have_posts() ) : 
		echo '<div class="single-sidebar">';
		echo '<ul>';
			 while ( $parent->have_posts() ) : $parent->the_post();
				$addclass  = "";
				if($current_page_id == get_the_ID())
				{
					$addclass  = "active";
				}
				else if($step_parent_id == get_the_ID())
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
add_shortcode('childpage-listing','sidebar_childpages');