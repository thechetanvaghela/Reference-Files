<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package diversity
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function diversity_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'diversity_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function diversity_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'diversity_pingback_header' );

/*sensi Hook*/
remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

add_action('sensei_before_main_content', 'my_theme_wrapper_start', 10);
add_action('sensei_after_main_content', 'my_theme_wrapper_end', 10);
add_action('sensei_archive_before_course_loop', 'my_theme_wrapper_end_section', 10);

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start' );
add_action('woocommerce_after_main_content', 'woo_wrapper_end' );
/*add_action('woocommerce_before_lost_password_form', 'my_theme_wrapper_start', 10);*/
/*add_action('woocommerce_after_lost_password_form', 'woo_wrapper_end', 10);*/
function woo_wrapper_end() {
  echo '</div></div></div></section>';
}

function my_theme_wrapper_start()
{

	if(is_tax())
	{
		echo '<section class="comman-sectionpadding skyblue-bg"><div class="container"><div class="row"><div class="col-md-12">';	# code...
	}
	elseif(is_home() || is_front_page() || is_archive('course') )
	{

		echo '<section class="vara-kurser-section"><div class="container"><div class="row"><div class="col-md-12">';
	}
	else 
	{
		echo '<section class="comman-sectionpadding skyblue-bg"><div class="container"><div class="row"><div class="col-md-12">';
	}

}
remove_action( 'sensei_single_course_content_inside_after', array( 'Sensei_Core_Modules', 'load_course_module_content_template' ), 8 );
remove_action( 'sensei_single_course_content_inside_after', array( Sensei()->modules, 'load_course_module_content_template' ), 8 );
remove_action( 'sensei_single_course_modules_before', array( Sensei()->modules, 'course_modules_title' ), 20 );
add_action('sensei_before_main_content', array( Sensei()->modules, 'load_course_module_content_template' ), 9);

function my_theme_wrapper_end_section() {
  echo '</div></div><div class="row"><div class="col-md-12">';
	//get_sidebar();
}
function my_theme_wrapper_end() {
  echo '</div></div></div></div>';
	//get_sidebar();
}

remove_action( 'sensei_archive_before_course_loop', array( 'Sensei_Course', 'course_archive_sorting' ) );
remove_action( 'sensei_archive_before_course_loop', array( 'Sensei_Course', 'course_archive_filters' ) );
remove_action( 'sensei_course_content_inside_before', array( 'Sensei_Course', 'the_course_meta' ) );

remove_action( 'sensei_course_content_inside_before', array( 'Sensei_Course', 'the_course_meta' ) );

add_filter('course_archive_title','course_page_title',10,1);
function course_page_title($html)
{
	$before_html = '<h1 class="title-div-h1">';
	$after_html = '</h1>';
	$frontpage_id = get_option( 'page_on_front' );
	$post_content = get_post($frontpage_id);
	//$content = $post_content->post_content;
	$page_title = $post_content->post_title;
	$html = $before_html . __( $page_title, 'diversity' ) . $after_html;
	//$html = $before_html . __( 'Våra kurser', 'diversity' ) . $after_html;
	return $html ;
}

remove_action( 'sensei_course_content_inside_before', array( 'Sensei_Templates', 'the_title' ),5 );
add_action('sensei_course_content_inside_before','course_page_loop_title',5,1);
function course_page_loop_title($post)
{
	if( is_numeric( $post ) ){
		$post = get_post( $post );
	}

	$title_classes = apply_filters('sensei_the_title_classes', $post->post_type . '-title' );
	$html .= '<h2><a href="' . get_permalink( $post->ID ) . '" >';
	$html .= $post->post_title ;
	$html .= '</a></h2>';
	echo $html;
}

add_action('sensei_archive_before_course_loop','sensi_course_page_content');
function sensi_course_page_content()
{
	$frontpage_id = get_option( 'page_on_front' );
	$post_content = get_post($frontpage_id);
	//$content = $post_content->post_content;
	$content = strip_tags($post_content->post_content);
	//$content = __('Lorem ipsum dolor sit amet sensie, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. Ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna','diversity');
	echo '<p class="conetent-p">';
	echo $content;
	echo '</p>';
	 wc_print_notices();
}


add_action('sensei_after_main_content', 'archive_footer_load', 15);
function archive_footer_load()
{
	if(is_home() || is_front_page() || is_archive('course') )
	{
		$frontpage_id = get_option( 'page_on_front' );
	
		$experience_section = get_field('experience_section',$frontpage_id);
		
		$background_image = $experience_section['background_image'];	
		$background_color = !empty($experience_section['background_color']) ? $experience_section['background_color'] : "#2F80ED";	
		$section_content = $experience_section['section_content'];	

		$background_style = "";
		if (!empty($background_image)){ 
		 $background_style = 'style="background:url(\''.$background_image.'\') no-repeat '.$background_color.';background-position: top right;"';
		}
		?>
		<section class="experience-ins-section" <?php echo $background_style;?>>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<?php 
						if(!empty($section_content))
						{  ?>
						<div class="experience-ins-text"><?php echo $section_content; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

add_filter('sensei_course_loop_content_class','sensei_course_loop_content_class',10,2);
function sensei_course_loop_content_class($extra_classes, $post)
{
	$extra_classes[] ='varakurser-packgebox';
	return $extra_classes;
}

add_action('sensei_course_content_before','sensei_course_content_before_background');
function sensei_course_content_before_background($id)
{
	$background = "";
	//$bg_image = get_template_directory_uri()."/images/packgebox-bg.jpg";
	if (has_post_thumbnail($id) ): 
		 $image = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'full' ); 
		 $bg_image =  $image[0];
		 $background = 'style="background:url(\''.$bg_image.'\') no-repeat;background-position:center;background-size:cover;"';
	endif;  
	echo '<div class="packgebox-bg" '.$background.'></div><div class="packgebox-content clearfix">';

}

add_action('sensei_course_content_inside_before','sensei_course_content_inside_before_title_price',6,1);
function sensei_course_content_inside_before_title_price($course_id)
{
	ob_start();
	$wc_post_id = get_post_meta( $course_id, '_course_woocommerce_product', true );
	if ( empty( $wc_post_id ) ) {
		return;
	}

	// Get the product.
	$product = Sensei_WC::get_product_object( $wc_post_id );
	if ( isset( $product ) && ! empty( $product ) && $product->is_purchasable() && $product->is_in_stock()) 
	{
		?>
		<div class="price-packge">
			<?php
				echo '<span class="course-price">';
				echo wp_kses_post( $product->get_price_html() );
				echo '</span>';
			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
	return ob_get_contents();
}

add_action('sensei_course_content_after','sensei_course_content_after_background');
function sensei_course_content_after_background($id)
{
	echo '</div>';
}

# Homepage cource listing data
add_action('sensei_course_content_inside_after','sensei_course_content_inside_after_meta');
function sensei_course_content_inside_after_meta($id)
{
	$course = get_post( $id );

	global $post, $current_user;

	if ( 'course' != $post->post_type ) {
		return;
	}

	# images
	$course_time_svg = get_template_directory_uri()."/images/clock-icon.svg";
	$book_icon_svg = get_template_directory_uri()."/images/book-icon.svg";
	$graph_icon_svg = get_template_directory_uri()."/images/graph-icon.svg";
	$setting_icon_svg = get_template_directory_uri()."/images/setting-icon.svg";
	$check_square_icon_svg = get_template_directory_uri()."/images/check-square-icon.svg";
	$sticker_icon_svg = get_template_directory_uri()."/images/sticker-icon.svg";

	$course_total_module_li = "";
	$course_time_li = "";

	# Total module of cource
	$course_modules  = Sensei()->modules->get_course_modules( $course->ID );
	$total_cource_module = count($course_modules);
	
	if(!empty($total_cource_module))
	{
		//$course_total_module_li = '<li><img src="'.$book_icon_svg.'" alt="Icon"> <span>Kursen har '. Sensei()->course->course_lesson_count( $course->ID ).' kapitel</span></li>';
		$course_total_module_li = '<li><img src="'.$book_icon_svg.'" alt="Icon"> <span>Kursen har '.$total_cource_module.' kapitel</span></li>';
	}

	$course_time = get_field('course_time',$course->ID);		
	if(!empty($course_time))
	{
		$course_time_li = '<li><img src="'.$course_time_svg.'" alt="Icon"> <span>Kursens längd '.$course_time.' minuter</span></li>';
	}
	
	echo '<ul class="clearfix packges-list">
			'.$course_time_li.'
			'.$course_total_module_li.'
			<li><img src="'.$graph_icon_svg.'" alt="Icon"> <span>Varje kapitel ha ett quiz med kuriosa</span></li>
			<li><img src="'.$setting_icon_svg.'" alt="Icon"> <span>Varje kapitel har tips för att komma igång</span></li>
			<li><img src="'.$check_square_icon_svg.'" alt="Icon"> <span>Innehåller Kunskapstest</span></li>
			<li><img src="'.$sticker_icon_svg.'" alt="Icon"> <span>Du får slutdiplom</span></li>
		  </ul>';

	echo '<div class="btn-group">';
		echo '<a href="'.get_permalink($course).'" class="defult-border-btn">Läs mer</a>';
		//echo '<a href="javascript:void(0)" rel="coupon_bar_'.$id.'" class="show_coupon_bar defult-border-btn">Jag har kod</a>';

			$is_user_taking_course = Sensei_Utils::user_started_course( $post->ID, $current_user->ID );
			
			if ( $is_user_taking_course ) 
			{
				$user_course_status = Sensei_Utils::user_course_status( $post->ID, $current_user->ID );
				$completed_course   = Sensei_Utils::user_completed_course( $user_course_status );
				
				if ( $completed_course ) 
				{
					$has_quizzes = Sensei()->course->course_quizzes( $post->ID, true );
					if ( has_filter( 'sensei_results_links' ) || $has_quizzes ) 
					{
						?>
						<!-- <p class="sensei-results-links"> -->
							<?php
								$results_link = '';
								if ( $has_quizzes ) 
								{
									$results_link = '<a class="defult-bg-btn" href="' . esc_url( Sensei()->course_results->get_permalink( $post->ID ) ) . '">' . esc_html__( 'Visa resultat', 'sensei-lms' ) . '</a>';
								}
								$results_link = apply_filters( 'sensei_results_links', $results_link, $post->ID );
								echo wp_kses_post( $results_link );
								?>
						<!-- </p> -->
						<?php
					}
				}
				else
				{
					?>
						<div class="status in-progress"><?php echo esc_html__( 'pågående', 'sensei-lms' ); ?></div>
					<?php
				}
			}
			elseif ( Sensei_WC::is_woocommerce_active() && Sensei_WC::is_course_purchasable( $post->ID ) )
			{
					/*if ( !Sensei_WC::has_customer_bought_product( get_current_user_id(), $post->ID )
						|| !empty( $post->ID ) ) {
					}*/
					if ( !Sensei_WC::is_course_in_cart( $post->ID ) )
					{
						echo '<a href="javascript:void(0)" rel="coupon_bar_'.$id.'" class="show_coupon_bar defult-border-btn">Jag har kod</a>';
					}
					
				
                Sensei_WC::the_add_to_cart_button_html( $post->ID );
            }
			else
			{
                if( get_option( 'users_can_register') ) 
                {
                     // set the permissions message
                     $anchor_before = '<a href="' . esc_url( sensei_user_login_url() ) . '" >';
                     $anchor_after = '</a>';
                     $notice = sprintf(
                         __('or %slog in%s to view this course.', 'woothemes-sensei'),
                         $anchor_before,
                        $anchor_after
                     );
                    // register the notice to display
                     if( Sensei()->settings->get( 'access_permission' ) ){
                         Sensei()->notices->add_notice( $notice, 'info' ) ;
                    }

                   	$my_courses_page_id = '';

                    $wp_register_link = apply_filters('sensei_use_wp_register_link', false);

                    $settings = Sensei()->settings->get_settings();
                    if( isset( $settings[ 'my_course_page' ] )
                         && 0 < intval( $settings[ 'my_course_page' ] ) ){
                         $my_courses_page_id = $settings[ 'my_course_page' ];
                     }
                      if( !empty( $my_courses_page_id ) && $my_courses_page_id && !$wp_register_link){
                         $my_courses_url = get_permalink( $my_courses_page_id  );
                         //$register_link = '<a href="'.$my_courses_url. '">' . __('Register', 'woothemes-sensei') .'</a>';
                         //echo '<div class="status register">' . $register_link . '</div>' ;
                         echo $register_link = '<a href="'.$my_courses_url. '" class="defult-border-btn">' . __('Register', 'woothemes-sensei') .'</a>';
                     } else{
                         wp_register( '<div class="status register">', '</div>' );
                     }
                 }
            }
		?>
		</div>
<?php
}

add_action('sensei_course_content_after','sensei_course_content_after_box');
function sensei_course_content_after_box($id)
{
	$course = get_post( $id );
	$wc_post_id = get_post_meta( $id, '_course_woocommerce_product', true );
	if ( empty( $wc_post_id ) ) {
		return;
	}
	echo '<div class="packgebox-footer clearfix hide" id="coupon_bar_'.$id.'" >
			<div class="stang-div">
				<a href="javascript:void(0)" class="hide_coupon_bar">STÄNG</a>
			</div>
			<div class="coupen-div">
			<form class="coupon-form" action="" method="POST">
				<p class="form-row form-row-first">
					<input type="hidden" name="add-to-cart" class="form-control" value="'.$wc_post_id.'">
					<input type="text" name="coupon_code" class="coupon_code form-control" placeholder="Coupon code" value="">
					<button class="defult-border-btn border-white dd-custom-add-to-cart-button" value="Checka ut" data-product-id="'.$wc_post_id.'">Checka ut</button>
				</p>
				<div class="clear"></div>
			</form>
			</div>
		</div>'; // type="submit"
}


/*single course*/
function so_25700650_remove_sidebar(){
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action('woocommerce_before_main_content', 'so_25700650_remove_sidebar' );


remove_action( 'sensei_single_course_content_inside_after', 'course_single_lessons', 10 );
remove_action( 'sensei_single_course_content_inside_after', array( 'Sensei_Course', 'the_course_lessons_title' ), 9 );

remove_action('sensei_single_course_content_inside_before',array('Sensei_Course','the_course_video'),40);
remove_action('sensei_single_course_content_inside_before',array('Sensei_Course','the_course_enrolment_actions'),30);
remove_action( 'sensei_single_course_content_inside_before', array( 'Sensei_Messages', 'send_message_link' ), 35 );
remove_action( 'sensei_pagination', array( 'Sensei_Frontend', 'load_content_pagination' ), 30 );
remove_action( 'sensei_single_course_content_inside_before', array( 'Sensei_Course', 'the_title' ), 10 );

add_action('sensei_single_course_content_inside_before','single_course_content',29);
function single_course_content($course_id)
{
	the_content();
	?>
	<div class="course_video clearfix">
		<?php Sensei_Course::the_course_video(); ?>
 	</div>
<?php
}

add_action('sensei_single_course_content_inside_before','single_course_content_action',45);
function single_course_content_action($course_id)
{
	global $post;
	?>
	<div class="btn-group">
	<!-- <a href="javascript:void(0)" rel="<?php //echo 'coupon_bar_'.$course_id  ?>" class="show_coupon_bar defult-border-btn"> <?php //_e('Jag har kod','diversity'); ?></a> -->
	<?php
	$is_user_taking_course = Sensei_Utils::user_started_course( $course_id, $current_user->ID );

	if ( $is_user_taking_course )
	{
		 $user_course_status = Sensei_Utils::user_course_status( $course_id, $current_user->ID );
		 $completed_course   = Sensei_Utils::user_completed_course( $user_course_status );
		 if ( $completed_course ) 
		 {
			$has_quizzes = Sensei()->course->course_quizzes( $course_id, true );
			if ( has_filter( 'sensei_results_links' ) || $has_quizzes ) 
			{
				?>
				<p class="sensei-results-links">
				<?php
					$results_link = '';
					if ( $has_quizzes ) 
					{
						$results_link = '<a class="defult-bg-btn" href="' . esc_url( Sensei()->course_results->get_permalink( $post->ID ) ) . '">' . esc_html__( 'Visa resultat', 'diversity' ) . '</a>';
					}
					$results_link = apply_filters( 'sensei_results_links', $results_link, $post->ID );
					echo wp_kses_post( $results_link );
					?>
				</p>
				<?php
			}
		}
		else
		{
			?>
			<div class="status in-progress"><?php echo esc_html__( 'pågående', 'diversity' ); ?></div>
			<?php
		}
	}
	elseif ( Sensei_WC::is_woocommerce_active() && Sensei_WC::is_course_purchasable( $post->ID ) )
	{
		if ( !Sensei_WC::is_course_in_cart( $post->ID ) )
		{
		?>
			<a href="javascript:void(0)" rel="<?php echo 'coupon_bar_'. $post->ID  ?>" class="show_coupon_bar defult-border-btn"> <?php _e('Jag har kod','diversity'); ?></a>
		<?php
		}
		Sensei_WC::the_add_to_cart_button_html( $post->ID );
		echo '<div class="course-coupon-wrap">';
		sensei_course_content_after_box($post->ID);
		echo '</div>';
	}
	else
	{
		if( get_option( 'users_can_register') ) 
		{
			// set the permissions message
			$anchor_before = '<a href="' . esc_url( sensei_user_login_url() ) . '" >';
			$anchor_after = '</a>';
			$notice = sprintf(
						__('or %slog in%s to view this course.', 'diversity'),
						$anchor_before,
						$anchor_after
						);
			// register the notice to display
			if( Sensei()->settings->get( 'access_permission' ) ){
				Sensei()->notices->add_notice( $notice, 'info' ) ;
			}

			$my_courses_page_id = '';
			$wp_register_link = apply_filters('sensei_use_wp_register_link', false);

			$settings = Sensei()->settings->get_settings();
			if( isset( $settings[ 'my_course_page' ] ) && 0 < intval( $settings[ 'my_course_page' ] ) )
			{
				$my_courses_page_id = $settings[ 'my_course_page' ];
			}
			
			if( !empty( $my_courses_page_id ) && $my_courses_page_id && !$wp_register_link)
			{
				$my_courses_url = get_permalink( $my_courses_page_id  );
				/*$register_link = '<a href="'.$my_courses_url. '">' . __('Register', 'diversity') .'</a>';
				echo '<div class="status register">' . $register_link . '</div>' ;*/
				echo $register_link = '<a href="'.$my_courses_url. '" class="defult-border-btn">' . __('Register', 'woothemes-sensei') .'</a>';
			} 
			else
			{
				wp_register( '<div class="status register">', '</div>' );
			}
		}
	}
}

add_action( 'sensei_single_course_content_inside_before','single_fourse_title', 10 );
function single_fourse_title()
{
	$lyssna_mp3 = get_field('course_lyssna',get_the_ID());
	$lyssna = "";
	if(!empty($lyssna_mp3))
	{
		$lyssna =  do_shortcode('[sc_embed_player fileurl="'.$lyssna_mp3.'"]');
		/*$lyssna =  '<audio class="lyssna-mp3" >
		    <source src="'.$lyssna_mp3.'">
		    <source src="'.$lyssna_mp3.'">
		  </audio>';*/
	}
	?>
	<div class="title-and-mp3-wrap">
	<p><strong><?php the_title() ; ?></strong><?php echo $lyssna; ?></p></div>
	
	<!-- <p><strong><?php //the_title(); ?></strong></p> -->
	<?php 
}

add_action('sensei_single_course_content_inside_after','sensei_single_course_content_inside_after_before_pruchase',1);
function sensei_single_course_content_inside_after_before_pruchase($course_id)
{
	/*$is_user_taking_course = Sensei_Utils::user_started_course( $course_id, $current_user->ID );
	if (!$is_user_taking_course )
	{
		?>
		<div class="course-tab-foot">
			<ul class="clearfix course-tabul">
				<li class="active" rel="kursbeskrivning">Viktiga ord</li>
				<li rel="innehåll">Innehåll</li>
				<li rel="utbildare">Utbildare</li>
			</ul>

			<div class="coursetab_container">
				<h3 class="active tab_course_heading" rel="kursbeskrivning">Kursbeskrivning</h3>
				<div id="kursbeskrivning" class="course_tab_content">
					<div class="coursetab_innercontent">
						<h3>Discrimination:</h3>
						<p>Lorem ipsum dolor sit amet, ut per corpora ancillae, putent torquatos cum et, ea cum accusata invenire.</p>
					</div>
					<div class="coursetab_innercontent">
						<h3>Discrimination:</h3>
						<p>Lorem ipsum dolor sit amet, ut per corpora ancillae, putent torquatos cum et, ea cum accusata invenire.</p>
					</div>
					<div class="coursetab_innercontent">
						<h3>Discrimination:</h3>
						<p>Lorem ipsum dolor sit amet, ut per corpora ancillae, putent torquatos cum et, ea cum accusata invenire.</p>
					</div>
				 </div>

				<h3 class="tab_course_heading" rel="innehåll">Innehåll</h3>
				<div id="innehåll" class="course_tab_content">
				<div class="coursetab_infocontent">
					<h2>Innehåll</h2>
						<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
						<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
						<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
					</div>
				</div>

				<h3 class="tab_course_heading" rel="utbildare">Utbildare here</h3>
				<div id="utbildare" class="course_tab_content">
					<div class="coursetab_infocontent">
						<h2>Utbildare</h2>
						<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
						<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
						<p>Nunc dui velit, scelerisque eu placerat volutpat, dapibus eu nisi. Vivamus eleifend vestibulum odio non vulputate.</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	else
	{*/
		
		$course_content = get_field('course_content',$course_id);
		$course_trainers = get_field('course_trainers',$course_id);
		?>
		<div class="course-tab-foot">
			<ul class="clearfix course-tabul">
				<?php 
				if( have_rows('course_snapshot',$course_id) )
			 	{ ?>
			 	 	<li class="active" rel="kursbeskrivning">Kursbeskrivning</li> <?php 
			  	}
			  	if(!empty($course_content)) 
			  	{ ?>
					<li rel="innehåll">Innehåll</li>
				<?php
				}
				if(!empty($course_trainers))
			 	{ ?>
					<li rel="utbildare">Utbildare</li>
				  <?php 
			  	} 
			  	?>
			</ul>

			<div class="coursetab_container">
			  	<h3 class="active tab_course_heading" rel="kursbeskrivning">Kursbeskrivning</h3>
			  	<div id="kursbeskrivning" class="course_tab_content">
			  		<?php
					if( have_rows('course_snapshot',$course_id) ): ?>
						<div class="">
						<?php 
						while( have_rows('course_snapshot',$course_id) ): the_row(); 
							$word_title = get_sub_field('word_title',$course_id);
							$description = get_sub_field('description',$course_id);
							?>
							<div class="coursetab_innercontent">
								<?php if( $word_title ): ?>
									<h3> <?php echo $word_title. ":"; ?></h3>
								<?php endif; ?>
								<?php if( $description ): ?>
									<p> <?php echo $description; ?></p>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
						</div>

					<?php endif; ?>
			 	 </div>
			 	 <?php 
			 	if(!empty($course_content))
			 	{
			 	 ?>
				  	<h3 class="tab_course_heading" rel="innehåll">Innehåll</h3>
				  	<div id="innehåll" class="course_tab_content">
				 		<div class="coursetab_infocontent">
				 			<h2>Innehåll</h2>
				 			<p><?php echo $course_content; ?> </p>		    		
				    	</div>
				  	</div>
			  	<?php 
			  	}	
			 	
			 	if(!empty($course_trainers))
			 	{
			 	?>
				  	<h3 class="tab_course_heading" rel="utbildare">Utbildare</h3>
				  	<div id="utbildare" class="course_tab_content">
				  		<div class="coursetab_infocontent">
			  				<h2>Utbildare</h2>
				    		<p><?php echo $course_trainers; ?> </p>
				    	</div>
			  		</div>
			  	<?php
			 	 }
			  	?>
			</div>
		</div>
		<?php
	/*}*/

}

/*end single course*/
add_action( 'header_part', 'header_part' );
function header_part()
{
	if(!is_user_logged_in())
	{
	 	get_template_part( 'template-parts/popup/login', '' );
	}
}

/*check out*/
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields )
{
	//	print_r($fields);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_postcode']);

	unset($fields['shipping']);
	unset($fields['order']);
	unset($fields['account']);

	$fields['billing']['billing_first_name']['placeholder'] = __('Förnamn','diversity');
	$fields['billing']['billing_last_name']['placeholder'] = __('Efternamn','diversity');
	$fields['billing']['billing_company']['placeholder'] = __('Företagsnamn','diversity');
	$fields['billing']['billing_email']['placeholder'] = __('e-post','diversity');
	$fields['billing']['billing_phone']['placeholder'] = __('Telefonnummer','diversity');

	 $fields['billing']['billing_users'] = array(
		'type' => 'radio',
		'label' => __('', 'diversity'),
		'required' => true,
		'class' => array('address-field'),
		'clear' => true,
		'options' => array(
			'privateperson' => __('Privatperson','diversity'),
			'business' => __('företag','diversity'),
		),
		'default' => 'privateperson'
	);

	/*label remove*/
	$fields['billing']['billing_first_name']['label'] ='';
	$fields['billing']['billing_last_name']['label'] ='';
	$fields['billing']['billing_company']['label'] ='';
	$fields['billing']['billing_email']['label'] ='';
	$fields['billing']['billing_phone']['label'] ='';

	return $fields;
}

add_filter('sensei_wc_paid_courses_add_to_cart_button_text','diversity_sensei_wc_paid_courses_add_to_cart_button_text');
function diversity_sensei_wc_paid_courses_add_to_cart_button_text($button_text)
{
	$button_text = __('Add To cart','diversity');
	return $button_text;
}

//add_filter( 'woocommerce_product_add_to_cart_url', 'wc_ninja_edit_add_to_cart_url', 10, 2 );
function wc_ninja_edit_add_to_cart_url( $url, $product )
{
	global $post;
	$url = get_permalink();
	return $url;
}

add_filter('woocommerce_enable_order_notes_field','diversity_woocommerce_enable_order_notes_field',10,1);
function diversity_woocommerce_enable_order_notes_field($path)
{
	return false;
}

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_checkout_billing', 'woocommerce_checkout_payment', 20 );

add_action('after_col_2','sensei_related_courses');

# related courses for checkout and cart page
function sensei_related_courses()
{
	$cart_product_ids = array();
	foreach( WC()->cart->get_cart() as $cart_item ){
	    $cart_product_ids[] = $cart_item['product_id'];
	}

	$related_courses_per_page = get_field('related_courses_per_page','option');
	$cart_per_page = !empty($related_courses_per_page['related_courses_cart_per_page']) ? $related_courses_per_page['related_courses_cart_per_page'] : 2;
	$checkoutper_page = !empty($related_courses_per_page['related_courses_checkout_per_page']) ? $related_courses_per_page['related_courses_checkout_per_page'] : 4;

	if(is_cart())
	{
		$course_per_page = $cart_per_page;
	}
	else if(is_checkout())
	{
		$course_per_page = $checkoutper_page;
	}
	$cart_course_args = array(
	   'post_type' => 'course',
	   'fields' => 'ids',
	   'meta_query' => array(
	       array(
	           'key' => '_course_woocommerce_product',
	           'value' => $cart_product_ids,
	           'compare' => 'IN',
	       )
	   )
	);
	$cart_course_query = new WP_Query($cart_course_args);

	$related_course_args = array(
	   'post_type' => 'course',
	   'posts_per_page' => $course_per_page, 
       'orderby' => 'rand', 
       'post__not_in' => $cart_course_query->posts,
	   
	);
	$related_course_query = new WP_Query($related_course_args);
	
	if($related_course_query->have_posts())
	{
		 
		?>
		<div class="related-product-div">
			<h3>Våra andra kurser</h3>
			<div class="rproduct-innerdiv clearfix">
				<?php
				while ( $related_course_query->have_posts() ) : $related_course_query->the_post();

					$bg_image = get_template_directory_uri()."/images/image.png";
					if (has_post_thumbnail() ): 
						 $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' ); 
						 $bg_image =  $image[0];
					endif;  
				?>
				<div class="rproduct-div clearfix">
					<div class="rproduct-img">
						<img  src="<?php echo $bg_image; ?>" alt="" class="img-responsive">
					</div>
					<div class="rproduct-content">
						<p class="rproduct-title"><strong><?php the_title();?></strong></p>
						<p class="rproduct-text"><?php 

						$excerpt = get_the_content();
						$excerpt = esc_attr( strip_tags( stripslashes( $excerpt ) ) );
						$excerpt = wp_trim_words( $excerpt, $num_words = 7, $more = NULL );
						//$excerpt = get_the_excerpt();
						//$excerpt = substr( $excerpt , 0, 50); 
						echo wp_kses_post( $excerpt );?></p>
						<a href="<?php the_permalink();?>">Se kurs</a>
					</div>
				</div>
				<?php
			endwhile;
   			wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
	}
}

/*single Lession*/
add_action('sensei_single_lesson_content_inside_before','lession_page_title');
function lession_page_title()
{
	echo '<header><h1 class="title-defult">'.get_the_title().'</h1></header>';
}

add_filter('sensei_lesson_archive_title','sensei_lesson_archive_title_callback',10,1);
function sensei_lesson_archive_title_callback($html)
{
	//<div class="module-header-lesson">
	$before_html = '<header class="archive-header dd-archive-header"><h1 class="title-defult">';
		$after_html  = '</h1></header>';

	$title = '';
		if ( is_post_type_archive( 'lesson' ) ) {

			$title = __( 'Lessons Archive', 'sensei-lms' );

	} elseif ( is_tax( 'module' ) ) {
		global $wp_query;
		$term  = $wp_query->get_queried_object();
		$title = $term->name;
	}
	$html = $before_html . $title . $after_html;
	return wp_kses_post( $html  );
}

remove_action( 'sensei_single_lesson_content_inside_before', array( 'Sensei_Templates', 'deprecate_sensei_lesson_single_title' ), 15 );
remove_action( 'sensei_single_lesson_content_inside_before', array( 'Sensei_Lesson', 'the_title' ), 15 );

add_action('sensei_single_lesson_content_inside_after','sensei_single_lesson_content_inside_after',100);
function sensei_single_lesson_content_inside_after($lessionid)
{
	$course_id = Sensei()->lesson->get_course_id( $lessionid);
	$is_user_taking_course = Sensei_Utils::user_started_course( $course_id, $current_user->ID );

	if ($is_user_taking_course )
	{
		
		$course_content = get_field('course_content',$course_id);
		$course_trainers = get_field('course_trainers',$course_id);
		if($course_id)
		{
			$quiz_id = 0;
			if ( 0 < $lessionid ) 
			{
				$quiz_id = Sensei_Lesson::lesson_quizzes( $lessionid, 'any' );
			}
		
			?>
			<div class="course-tab-foot">
				<ul class="clearfix course-tabul">
					<?php 
					if( have_rows('course_snapshot',$course_id) )
				 	{ ?>
				 	 	<li class="active" rel="kursbeskrivning">Kursbeskrivning</li> <?php 
				  	}
				  	if ( $quiz_id && is_user_logged_in())
					{
						?>
						<li rel="<?php _e('quiz','diversity') ?>"><?php _e('quiz','diversity') ?></li>
						<?php
					}
					if(!empty($course_trainers))
				 	{ ?>
						<li rel="utbildare">Utbildare</li>
					  <?php 
				  	} 
				  	?>
				</ul>

				<?php
				if( have_rows('course_snapshot',$course_id) ): ?>
					<div class="coursetab_container">
						<div id="kursbeskrivning" class="course_tab_content">
							<div class="">
								<?php 
								while( have_rows('course_snapshot',$course_id) ): the_row(); 
									$word_title = get_sub_field('word_title',$course_id);
									$description = get_sub_field('description',$course_id);
									?>
									<div class="coursetab_innercontent">
										<?php if( $word_title ): ?>
											<h3> <?php echo $word_title. ":"; ?></h3>
										<?php endif; ?>
										<?php if( $description ): ?>
											<p> <?php echo $description; ?></p>
										<?php endif; ?>
									</div>
								<?php endwhile; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php
					$quiz_id = 0;
					if ( 0 < $lessionid ) 
					{
						$quiz_id = Sensei_Lesson::lesson_quizzes( $lessionid, 'any' );
						//$questions = Sensei()->lesson->lesson_quiz_questions($quiz_id);
						if ( $quiz_id && is_user_logged_in())
						{
						?>	
							<div id="<?php _e('quiz','diversity') ?>" class="course_tab_content">
								<div class="coursetab_infocontent">
									<?php
									//$lesson_ids = Sensei()->course->course_lessons( $course_id, 'any', 'ids' );
									//$lessionid = $lesson_ids[0];
									global $post;
									$post = get_post($quiz_id);
									Sensei_Templates::get_template( 'modules/module-quiz.php' );
									$post = null;								
									?>
								</div>
							</div>
						<?php
						}
					}

					if(!empty($course_trainers))
				 	{
				 	?>
					  	<div id="utbildare" class="course_tab_content">
					  		<div class="coursetab_infocontent">
				  				<h2>Utbildare</h2>
					    		<p><?php echo $course_trainers; ?> </p>
					    	</div>
				  		</div>
				  	<?php
				 	 }
				  	?>
				</div>
			</div>
			<?php
		}
	}

}

/*module detail page */
add_action('sensei_before_main_content', 'sensei_taxonomy_module_content_before_dd', 9);
function sensei_taxonomy_module_content_before_dd()
{
	if(is_tax('module'))
	{
		global $post;
		$post = get_post($_GET['course_id']);
		Sensei_Templates::get_template( 'modules/module-bar.php' );
		$post = null;
	}
	global $wp_query;
	$course_slug = $wp_query->query_vars['course_results'];
	if(!empty($course_slug))
	{
		global $post;
		$course_post = get_page_by_path($course_slug,OBJECT,'course');
		$post = get_post($course_post->ID);
		Sensei_Templates::get_template( 'modules/result-nav.php' );
		$post = null;
	}
}
add_action('sensei_taxonomy_module_content_inside_after','sensei_taxonomy_module_content_inside_before_course_list',11);
function sensei_taxonomy_module_content_inside_before_course_list()
{
	$term = get_queried_object();
	$module_id = $term->term_id;
	$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	global $current_user;
  	$user_id = $current_user->ID;
	?>
	<section class="entry fix">
		<div class="lesstion_wrapdiv diversity-course-module-course-wrap">
		<?php while ( have_posts() ) : the_post(); 
			$lesson_id = get_the_ID();
			//$lesson_status = get_user_meta( intval( $user_id ), '_user_course_' . intval( $course_id ) . '_module_' . intval( $module_id ).'_lession_' . intval( $lesson_id ),true );
			$bg_image = get_template_directory_uri()."/images/about-bg.jpg";
			if (has_post_thumbnail( $lesson_id ) ): 
				 $image = wp_get_attachment_image_src( get_post_thumbnail_id( $lesson_id ), 'full' ); 
				 $bg_image =  $image[0];
			endif;  	
			?>
			<div class="lesstion_loop diversity-course-module-course-loop <?php echo "lession-".get_the_ID(); ?>">
				<input type="hidden" class="diversity-lession-id" value="<?php the_ID(); ?>">
				<input type="hidden" class="diversity-course-id" value="<?php echo $course_id ; ?>">
				<input type="hidden" class="diversity-module-id" value="<?php echo $module_id; ?>">
				<div class="course-bg banner-top-bg" style="background-image: url('<?php echo $bg_image; ?>');"></div>
				<div class="course-content innercontent-boxdiv clearfix">
					<div class="content-wrap">
						<?php /*if($lesson_status == 'lession-completed'){?>
					<div class="sensei-message tick"><?php esc_html_e( 'Lession Complete', 'sensei-lms' ); ?></div>
					<?php }*/ 
					$lyssna_mp3 = get_field('lyssna_lesson',$lesson_id);
					$lyssna = "";
					if(!empty($lyssna_mp3))
					{
						$lyssna =  do_shortcode('[sc_embed_player fileurl="'.$lyssna_mp3.'"]');
						/*$lyssna =  '<audio class="lyssna-mp3" >
						    <source src="'.$lyssna_mp3.'">
						    <source src="'.$lyssna_mp3.'">
						  </audio>';*/
					}
					?>
						<div class="title-and-mp3-wrap">
						<p><strong><?php the_title() ; ?></strong><?php echo $lyssna; ?></p></div>
						<?php  the_content(); ?>
					</div>
				</div>
		</div>

		<?php endwhile; ?>
		</div>
	</section>
	<?php
}

/*
#	add tab content od mudule
*/
add_action('sensei_taxonomy_module_content_after','sensei_taxonomy_module_content_after_dd',12);
function sensei_taxonomy_module_content_after_dd()
{
	global $current_user;
	$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	$term = get_queried_object();
	#$term_id = get_queried_object()->term_id;
	if($course_id)
	{
		$is_user_taking_course = Sensei_Utils::user_started_course( $course_id, $current_user->ID );
		if ($is_user_taking_course )
		{
			if($term)
			{
				$lesson_ids = diversity_lession_ids_of_modules( $term, $course_id, 'publish', 'ids');
			}
			else
			{
				$lesson_ids = Sensei()->course->course_lessons( $course_id, 'publish', 'ids' );
			}
	
			$lesson_ids_with_lession = array();
			foreach ( $lesson_ids as $lesson_id ) {
				$has_questions = Sensei_Lesson::lesson_quiz_has_questions(  $lesson_id );
				if ( $has_questions ) {
					$lesson_ids_with_lession[] = $lesson_id;
				}
			}

			//$lessionid = $lesson_ids[0];
			$lessionid = $lesson_ids_with_lession[0];
			$quiz_id = 0;
			if ( 0 < $lessionid ) 
			{
					$quiz_id = Sensei_Lesson::lesson_quizzes( $lessionid, 'any' );
			}

			?>
			<div class="course-tab-foot">
				<ul class="clearfix course-tabul">
					<?php
					if( have_rows('important_words',$term) )
					{ 	?>
						<li class="active" rel="kursbeskrivning">Viktiga ord</li>
						<?php 
					}
					if ( $quiz_id && is_user_logged_in())
					{
						?>
						<li rel="<?php _e('quiz','diversity') ?>"><?php _e('quiz','diversity') ?></li>
						<?php
					}
					if( have_rows('tips_for_getting_started',$term) )
					{ 	?>
						<li rel="utbildare"><?php _e('tips for att komma agang','diversity') ?></li>
						<?php 
					}
					?>
				</ul>
				<div class="coursetab_container">
				<?php 
				$lyssna_mp3 = get_field('module_lyssna',$term);
			    $lyssna = "";
			    if(!empty($lyssna_mp3))
			    {
			    	echo '<div class="module-lyssna-wrap">';
			        echo $lyssna =  do_shortcode('[sc_embed_player fileurl="'.$lyssna_mp3.'"]');
			        echo '</div>';
			    }

				?>
				<?php
				if( have_rows('important_words',$term) ): ?>
					<h3 class="active tab_course_heading" rel="kursbeskrivning">Kursbeskrivning</h3>
					<div id="kursbeskrivning" class="course_tab_content">
							<div class="">
							<?php while( have_rows('important_words',$term) ): the_row(); 
								$word_title = get_sub_field('word_title',$term);
								$description = get_sub_field('description',$term);
								?>
								<div class="coursetab_innercontent">
									<?php if( $word_title ): ?>
										<h3> <?php echo $word_title. ":"; ?></h3>
									<?php endif; ?>
									<?php if( $description ): ?>
										<p> <?php echo $description; ?></p>
									<?php endif; ?>
								</div>
							<?php endwhile; ?>
							</div>
				 	</div>
					 <?php 
				endif;
					 	
				//$questions = Sensei()->lesson->lesson_quiz_questions($quiz_id);
				if ( $quiz_id && is_user_logged_in())
				{
					?>
					<h3 class="tab_course_heading" rel="<?php _e('quiz','diversity') ?>"><?php _e('quiz','diversity') ?></h3>
						<div id="<?php _e('quiz','diversity') ?>" class="course_tab_content">
							<!-- <div class="coursetab_infocontent diversity-module-quiz"> -->
							<?php
							if(is_tax('module'))
							{
								global $post;
								$post = get_post($quiz_id);
								Sensei_Templates::get_template( 'modules/module-quiz.php' );
								$post = null;
							}
							?>
							<!-- </div> -->
						</div>
					<?php
				}
				
				if( have_rows('tips_for_getting_started',$term) )
				{
					 ?>
					<h3 class="tab_course_heading" rel="utbildare"><?php _e('tips for att komma agang','diversity') ?></h3>
					<div id="utbildare" class="course_tab_content">
						<div class="">
							<div class="">
								<?php 
								$tips_count = 1;
								while( have_rows('tips_for_getting_started',$term) ): the_row(); 
									$tips_points = get_sub_field('tips_points',$term);
									?>
										<div class="coursetab_innercontent tips-get-started">
										<?php if( $tips_points ): ?>
												<?php echo "<span>".$tips_count.".</span>"; ?> 
												<p><?php echo $tips_points; ?></p>
											<?php endif; ?>
										</div>
									<?php 
									$tips_count++;
								endwhile; ?>
							</div>	
						</div>
					</div>
					<?php 
				}
				?>
				</div>
			</div>
			<?php
		}
	}
}


/*
#	remove back to button from bottom
*/
remove_action( 'template_redirect', array( 'Sensei_Quiz', 'reset_button_click_listener' ), 30 );


/*
# remove message from RESULT PAGE 
*/
remove_action( 'sensei_course_results_content_inside_before_lessons', array( Sensei()->course_results, 'course_info' ));

/*
#
*/
add_action( 'template_redirect',  'diversity_reset_button_click_listener', 30 );
function diversity_reset_button_click_listener() {
	if ( ! isset( $_POST['dd_quiz_reset'] )
		|| ! wp_verify_nonce( $_POST['woothemes_sensei_reset_quiz_nonce'], 'woothemes_sensei_reset_quiz_nonce' ) > 1 ) {
		return; // exit
	}

	$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	if($course_id)
	{
		$term = get_queried_object();
		if($term)
		{
			$lesson_ids = diversity_lession_ids_of_modules( $term, $course_id, 'publish', 'ids');
		}
		else
		{
			$lesson_ids = Sensei()->course->course_lessons( $course_id, 'publish', 'ids' );
		}
		$lesson_ids_with_lession = array();
		foreach ( $lesson_ids as $lesson_id ) {
			$has_questions = Sensei_Lesson::lesson_quiz_has_questions(  $lesson_id );
			if ( $has_questions ) {
				$lesson_ids_with_lession[] = $lesson_id;
			}
		}
		$lesson_id = $lesson_ids_with_lession[0];
		//$lesson_id = $lesson_ids[0];
	
		$current_quiz_id = 0;
		if ( 0 < $lesson_id ) 
		{
			$current_quiz_id = Sensei_Lesson::lesson_quizzes( $lesson_id, 'any' );
		}
	}
	else
	{
		global $post;
		$current_quiz_id = $post->ID;
		
	}
	$lesson_id       = Sensei_Quiz::get_lesson_id( $current_quiz_id );
	// reset all user data
	Sensei_Quiz::reset_user_lesson_data( $lesson_id, get_current_user_id() );
	// this function should only run once
	remove_action( 'template_redirect','diversity_reset_button_click_listener' );

} 

/*
#	remove back to button from bottom
*/
add_filter( 'sensei_breadcrumb_output', 'diversity_remove_lession_quiz_title',20,2);
function diversity_remove_lession_quiz_title($title, $post_id)
{
	$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	if($course_id)
	{
		$title = "";
	}
	return $title;
}

/*
# add next module button with message
*/
add_filter('sensei_user_quiz_status','change_button_of_lession_to_module',10,4);
function change_button_of_lession_to_module($data=array(),$lesson_id,$user_id,$is_lesson)
{

	$module_course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	if($module_course_id)
	{
		if($data["status"] == "passed")
		{
			$course_id = absint( get_post_meta( $lesson_id, '_lesson_course', true ) );
			// Has user started course
			//$started_course = Sensei_Utils::user_started_course( $course_id, $user_id );
			// Has user completed lesson
			$user_lesson_status = Sensei_Utils::user_lesson_status( $lesson_id, $user_id );
			$lesson_complete    = Sensei_Utils::user_completed_lesson( $user_lesson_status );

			// Quiz ID
			$quiz_id = Sensei()->lesson->lesson_quizzes( $lesson_id );

			// Quiz grade
			$quiz_grade = 0;
			if ( $user_lesson_status ) {
				// user lesson status can return as an array.
				if ( is_array( $user_lesson_status ) ) {
					$comment_ID = $user_lesson_status[0]->comment_ID;

				} else {
					$comment_ID = $user_lesson_status->comment_ID;
				}

				$quiz_grade = get_comment_meta( $comment_ID, 'grade', true );
			}

			// Quiz passmark
			$quiz_passmark = absint( get_post_meta( $quiz_id, '_quiz_passmark', true ) );

			// Pass required
			$pass_required = get_post_meta( $quiz_id, '_pass_required', true );

			// Quiz questions
			$has_quiz_questions = Sensei_Lesson::lesson_quiz_has_questions( $lesson_id );

			$status    = 'passed';
				$box_class = 'tick';
				// Lesson status will be "complete" (has no Quiz)
				if ( ! $has_quiz_questions ) {
					$message = sprintf( __( 'Congratulations! You have passed this lesson.', 'sensei-lms' ) );
				}
				// Lesson status will be "graded" (no passmark required so might have failed all the questions)
				elseif ( empty( $quiz_grade ) ) {
					$message = sprintf( __( 'Congratulations! You have completed this lesson.', 'sensei-lms' ) );
				}
				// Lesson status will be "passed" (passmark reached)
				elseif ( ! empty( $quiz_grade ) && abs( $quiz_grade ) >= 0 ) {
					if ( $is_lesson ) {
						// translators: Placeholder is the quiz grade.
						$message = sprintf( __( 'Congratulations! You have passed this lesson\'s quiz achieving %s%%', 'sensei-lms' ), Sensei_Utils::round( $quiz_grade ) );
					} else {
						// translators: Placeholder is the quiz grade.
						$message = sprintf( __( 'Congratulations! You have passed this quiz achieving %s%%', 'sensei-lms' ), Sensei_Utils::round( $quiz_grade ) );
					}
				}
			// add next lesson button
				$term = get_queried_object();
				$nav_links = sensei_get_prev_next_module( $term );
				
				// Output HTML
				if ( isset( $nav_links['next'] ) ) {
					$message .= ' ' . '<a class="next-lesson" href="' . esc_url( $nav_links['next']['url'] )
								. '" rel="next"><span class="meta-nav"></span>' . __( 'Next Module', 'sensei-lms' )
								. '</a>';

				}
				$data['message'] = $message;
		}
	}
	return $data;
}

/*
# get next previous module link
*/
function sensei_get_prev_next_module( $term = 0 ) {
	$links               = array();
	$lesson_ids          = array();
	$modules_and_lessons = array();
	$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	if($course_id)
	{
		$course_modules      = Sensei()->modules->get_course_modules( $course_id );
		$t_id = $term->term_id;
		
		if ( ! empty( $course_modules ) ) {
			$key = "";
			foreach (  $course_modules as $module_k => $module_v ) {
				if($module_v->term_id == $t_id)
				{
					$key = $module_k;
					break;
				}
			}
			$previous = $course_modules[$key-1];
			$next = $course_modules[$key+1];
		}

		if ( isset( $previous ) ) {
			$links['previous'] = array(
				'url'  => sensei_get_navigation_url( $course_id, $previous ),
				'name' => sensei_get_navigation_link_text( $previous ),
			);
		}
		if ( isset( $next ) ) {
			$links['next'] = array(
				'url'  => sensei_get_navigation_url( $course_id, $next ),
				'name' => sensei_get_navigation_link_text( $next ),
			);
		}
	}
	return $links;
} 

/*
# get lessions id of course id and module ids
*/
function diversity_lession_ids_of_modules($term,$course_id = 0, $post_status = 'publish', $fields = 'all')
{
	
	$post_args     = array(
		'post_type'        => 'lesson',
		'posts_per_page'   => -1,
		'orderby'          => 'date',
		'order'            => 'ASC',
		'meta_query'       => array(
			array(
				'key'   => '_lesson_course',
				'value' => intval( $course_id ),
			),
		),
		'tax_query' => array(
        array (
            'taxonomy' => 'module',
            'field' => 'id',
            'terms' => $term->term_id,
        )
   		 ),
		'post_status'      => $post_status,
		'suppress_filters' => 0,
	);
	$query_results = new WP_Query( $post_args );
	$lessons       = $query_results->posts;

	// re order the lessons. This could not be done via the OR meta query as there may be lessons
	// with the course order for a different course and this should not be included. It could also not
	// be done via the AND meta query as it excludes lesson that does not have the _order_$course_id but
	// that have been added to the course.
	if ( count( $lessons ) > 1 ) {

		foreach ( $lessons as $lesson ) {

			$order = intval( get_post_meta( $lesson->ID, '_order_' . $course_id, true ) );
			// for lessons with no order set it to be 10000 so that it show up at the end
			$lesson->course_order = $order ? $order : 100000;
		}
		//uasort( $lessons, array('Sensei_Course', '_short_course_lessons_callback' ) );
	}
	/**
	 * Filter runs inside Sensei_Course::course_lessons function
	 *
	 * Returns all lessons for a given course
	 *
	 * @param array $lessons
	 * @param int $course_id
	 */
	$lessons = apply_filters( 'sensei_course_get_lessons', $lessons, $course_id );

	// return the requested fields
	// runs after the sensei_course_get_lessons filter so the filter always give an array of lesson
	// objects
	if ( 'ids' == $fields ) {
		$lesson_objects = $lessons;
		$lessons        = array();

		foreach ( $lesson_objects as $lesson ) {
			$lessons[] = $lesson->ID;
		}
	}
	return $lessons;
}

/*
# remove action button 
# Remove lession title from quesion
*/
$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
if($course_id)
{
	remove_action( 'sensei_single_quiz_content_inside_before', array( 'Sensei_Quiz', 'the_title' ), 20);
	remove_action( 'sensei_single_quiz_questions_after', array( 'Sensei_Quiz', 'action_buttons' ), 10, 0 );
	/*
	# add quiz action button
	# complete quiz, Save Quiz, reset quiz
	*/
	add_action( 'sensei_single_quiz_content_inside_before', 'diversity_result_of_questions', 10, 0 );
	add_action( 'sensei_single_quiz_questions_after', 'diversity_action_buttons', 10, 0 );
}
function diversity_result_of_questions()
{
	
	$lesson_id           = Sensei()->quiz->get_lesson_id( get_the_id() );
	//$user_quizzes       = Sensei()->quiz->get_user_answers( $lesson_id, get_current_user_id() );
	$lesson_quiz_questions = Sensei()->lesson->lesson_quiz_questions( get_the_id());
	//total_right_ans_from_questions($lesson_id,sensei_get_the_question_id(),$correct_ans_total);
	
	$user_id = get_current_user_id();

	$correct_ans_total = 0;
	$total_quiz_questions = count($lesson_quiz_questions);

	foreach ($lesson_quiz_questions as $key => $value) {
		$correct_ans_total = total_right_ans_from_questions($lesson_id,$value->ID,$correct_ans_total);
	}

	$user_lesson_status = Sensei_Utils::user_lesson_status( $lesson_id, $user_id );
	
	$lesson_complete    = Sensei_Utils::user_completed_lesson( $user_lesson_status );

	$quiz_grade = 0;
	if ( $lesson_complete ) {
		if ( $user_lesson_status ) {
			// user lesson status can return as an array.
			if ( is_array( $user_lesson_status ) ) {
				$comment_ID = $user_lesson_status[0]->comment_ID;

			} else {
				$comment_ID = $user_lesson_status->comment_ID;
			}

			$quiz_grade = get_comment_meta( $comment_ID, 'grade', true );
		

			$result = Sensei_Utils::round( $quiz_grade );
			?>
			<div class="quiz_summary_innercontent">
				<div class="out-of-total">
					<?php 
					echo $correct_ans_total. __( ' av ', 'woocommerce' ).$total_quiz_questions;
					?>
				</div>
				<div class="right-out-of-total">
					<?php echo __( ' Rätta Svar ', 'woocommerce' ); ?>
				</div>
				<div class="lession-messages">
					<?php echo __( ' Grattis ', 'woocommerce' ); ?>
				</div>
				<div class="you-are-star-of"><?php echo __( 'Du är en stjärna pä diskriminering ', 'woocommerce' ); ?></div>
			</div>
			<?php
		}
	}
}
function diversity_action_buttons()
{
	global $post, $current_user;

		$lesson_id           = Sensei()->quiz->get_lesson_id( $post->ID );
		$lesson_course_id    = (int) get_post_meta( $lesson_id, '_lesson_course', true );
		$lesson_prerequisite = (int) get_post_meta( $lesson_id, '_lesson_prerequisite', true );
		$show_actions        = true;
		$user_lesson_status  = Sensei_Utils::user_lesson_status( $lesson_id, $current_user->ID );

		// setup quiz grade
		$user_quiz_grade = '';
		if ( ! empty( $user_lesson_status ) ) {

			// user lesson status can return as an array.
			if ( is_array( $user_lesson_status ) ) {
				$comment_ID = $user_lesson_status[0]->comment_ID;

			} else {
				$comment_ID = $user_lesson_status->comment_ID;
			}

			$user_quiz_grade = get_comment_meta( $comment_ID, 'grade', true );
		}

		if ( intval( $lesson_prerequisite ) > 0 ) {

			// If the user hasn't completed the prereq then hide the current actions
			$show_actions = Sensei_Utils::user_completed_lesson( $lesson_prerequisite, $current_user->ID );

		}
		if ( $show_actions && is_user_logged_in() && Sensei_Utils::user_started_course( $lesson_course_id, $current_user->ID ) ) {

			// Get Reset Settings
			$reset_quiz_allowed = get_post_meta( $post->ID, '_enable_quiz_reset', true );
			?>

			 <!-- Action Nonce's -->
			 <input type="hidden" name="woothemes_sensei_complete_quiz_nonce" id="woothemes_sensei_complete_quiz_nonce"
					value="<?php echo esc_attr( wp_create_nonce( 'woothemes_sensei_complete_quiz_nonce' ) ); ?>" />
			 <input type="hidden" name="woothemes_sensei_reset_quiz_nonce" id="woothemes_sensei_reset_quiz_nonce"
					value="<?php echo esc_attr( wp_create_nonce( 'woothemes_sensei_reset_quiz_nonce' ) ); ?>" />
			 <input type="hidden" name="woothemes_sensei_save_quiz_nonce" id="woothemes_sensei_save_quiz_nonce"
					value="<?php echo esc_attr( wp_create_nonce( 'woothemes_sensei_save_quiz_nonce' ) ); ?>" />
			 <!-- End Action Nonce's -->
			 <div class="diversity-action-btns">
			 <?php if ( '' == $user_quiz_grade && ( ! $user_lesson_status || 'ungraded' !== $user_lesson_status->comment_approved ) ) { ?>

				 <span class="diversity-quiz-complete"><input type="submit" name="quiz_complete" class="quiz-submit complete" value="<?php esc_attr_e( 'Komplett Quiz', 'sensei-lms' ); ?>"/></span>

				 <span class="diversity-quiz-save"><input type="submit" name="quiz_save" class="quiz-submit save" value="<?php esc_attr_e( 'Spara Quiz', 'sensei-lms' ); ?>"/></span>

				<?php } // End If Statement ?>

			 <?php if ( isset( $reset_quiz_allowed ) && $reset_quiz_allowed ) { ?>

				 <span class="diversity-quiz-reset"><input type="submit" name="dd_quiz_reset" class="quiz-submit reset" value="<?php esc_attr_e( 'Gör Test Igen', 'sensei-lms' ); ?>"/></span>

				<?php } ?>
			</div>
			<?php
		}

}

/*
# remove Progress statment and meter from module  
# percentage , complete
*/
remove_action( 'sensei_single_course_content_inside_before', array( Sensei()->course, 'the_progress_statement' ), 15 );
remove_action( 'sensei_single_course_content_inside_before', array( Sensei()->course, 'the_progress_meter' ), 16 );

/*
# add competed module count with star
*/
 add_filter( 'sensei_lesson_archive_title','add_module_progress_in_header',10,1 );
 function add_module_progress_in_header($html)
 {
	global $current_user;
	$course_id  = !empty($_GET['course_id']) ? $_GET['course_id'] : 0;
	if($course_id)
	{
		$html .= add_module_progress_star_in_header($course_id,$current_user->ID,true);
		
	}
	return $html;
 }

 function add_module_progress_star_in_header($course_id,$current_user_id,$button_show)
 {
	if($course_id)
	{
		$is_user_taking_course = Sensei_Utils::user_started_course( $course_id, $current_user_id );

		// If user is taking course, display progress.
		if ( $is_user_taking_course ) {
			$course_modules      = Sensei()->modules->get_course_modules( $course_id );
			if ( ! empty( $course_modules ) ) {
				$complete_module = 0;
				foreach (  $course_modules as $module_k => $module_v ) {
					$module_term_id = $module_v->term_id;
					//$module_progress = Sensei()->modules->get_user_module_progress( $module_term_id, $course_id, get_current_user_id() );
					$module_progress = get_user_meta( intval( get_current_user_id() ), '_module_progress_' . intval( $course_id ) . '_' . intval( $module_term_id ), true );
					//if ( $module_progress == 100 ) {
					if ( !empty($module_progress)) {
						$complete_module++;
					}
				}
			
				$total_module = count($course_modules);
				$html .= '<div class="module-rating-wrap">';
				$html .= '<p>';
				$html .= __( 'avklarade kursmoment! ', 'sensei-lms' );
				$html .= '</p>';
				$html .= '<div class="module-rating">';
				for($i=0;$i<$total_module; $i++){
				   $complete_class='';
				   $filled='';
				   if($i<$complete_module){
				      $complete_class= "module-complete";
				      //$filled = 'style="border:1px solid black;"';
				   }
				   //$html .='<span class="star '.$complete_class.'" '.$filled.'>  star'.($i+1).'</span>';
				   $html .='<span class="star '.$complete_class.'"><i class="fa fa-star" aria-hidden="true"></i></span>';
				}
				$html .= '</div>';
				if($button_show)
				{
					$html .= '<a href="#" class="defult-bg-btn bg-white kursmoment-btn">'.__( 'Kursmoment', 'sensei-lms' ).'</a>';
				}
				$html .= '</div>';
				$html .= '<div class="clearfix"></div>';
			}
		}
	}
	return $html;
 }

/*
# remove Progress status from module detail 
# completed , In Progress
*/
remove_action( 'sensei_loop_lesson_inside_before', array(Sensei()->modules, 'module_archive_description' ), 30 );
remove_action( 'sensei_taxonomy_module_content_inside_before', array( Sensei()->modules, 'module_archive_description' ), 30 );
add_action('sensei_loop_lesson_inside_before', 'diversity_module_archive_description', 30);
add_action('sensei_taxonomy_module_content_inside_before', 'diversity_module_archive_description', 30);

function diversity_module_archive_description() {
	// ensure this only shows once on the archive.
	remove_action( 'sensei_loop_lesson_before','diversity_module_archive_description', 30 );
	if ( is_tax('module') ) {

		$module = get_queried_object();
		echo '<p class="archive-description module-description">' . wp_kses_post( apply_filters( 'sensei_module_archive_description', nl2br( $module->description ), $module->term_id ) ) . '</p>';
	}
}

/*
# update lession status to complete 
# completed , In Progress
*/
add_action( 'wp_ajax_data_update_user_course_module_lession', 'data_update_user_course_module_lession_callback' );
add_action( 'wp_ajax_nopriv_data_update_user_course_module_lession', 'data_update_user_course_module_lession_callback' );

function data_update_user_course_module_lession_callback() {

	

	$lesson_id = $_POST['lession_id'];
	$course_id = $_POST['course_id'];
  	$module_id = $_POST['module_id'];
  	
  	$status = "error";
	$message = "failed";
	if(is_user_logged_in())
	{
		global $current_user;
		$user_id = $current_user->ID;
	  	if(!empty($lesson_id))
	  	{
	  		$status    = 'success';
			$message = "completed";
			$module = get_term_by('id',$module_id, 'module');
			if($module)
			{
				$lesson_ids = diversity_lession_ids_of_modules( $module, $course_id, 'publish', 'ids');
			}
			else
			{
				$lesson_ids = Sensei()->course->course_lessons( $course_id, 'publish', 'ids' );
			}

			$lesson_ids_with_lession = array();
			foreach ( $lesson_ids as $lesson_id ) {
				$has_questions = Sensei_Lesson::lesson_quiz_has_questions(  $lesson_id );
				if ( $has_questions ) {
					$lesson_ids_with_lession[] = $lesson_id;
				}
			}
			//Force Complete lession
			if($lesson_id != $lesson_ids_with_lession[0])
	  		{
	  			Sensei_Utils::sensei_start_lesson( $lesson_id, $user_id, true );
	  		}

	  		$lesson_status = get_user_meta( intval( $user_id ), '_user_course_' . intval( $course_id ) . '_module_' . intval( $module_id ).'_lession_' . intval( $lesson_id ),true );
	  		if($lesson_status != 'lession-completed')
	  		{
		  		
				$message = "done";
		  		update_user_meta( intval( $user_id ), '_user_course_' . intval( $course_id ) . '_module_' . intval( $module_id ).'_lession_' . intval( $lesson_id ),'lession-completed' );	
		  		
		  	}
	  	}
	}
  	$data = array(
        "status"     => $status,
        "message"     => $message,
	);
	echo json_encode($data);
    die();
}

//add_action( 'sensei_log_activity_before','add_new_field_for_lession_status'10,2);
function add_new_field_for_lession_status( $args, $data )
{
	$data['comment_karma'] = ! empty( $args['status'] ) ? esc_html( $args['status'] ) : 'log';
}

/*add_filter( 'sensei_the_module_permalink', 'add_lession_id_in_module_url',10,3 );
function add_lession_id_in_module_url( $module_url, $module_term_id, $course_id )
{

}*/

/*
# Change return to shop url
*/
function wc_empty_cart_redirect_url() {
	return home_url();
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

/*
# Change return to shop text
*/
add_filter( 'gettext', 'change_woocommerce_return_to_shop_text', 20, 3 );
function change_woocommerce_return_to_shop_text( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Return to shop' :
                $translated_text = __( 'Return to Courses', 'woocommerce' );
                break;
        }
    return $translated_text;
}

/*
# shop page redirect to home
*/
function diversity_shop_page_to_homepage_redirect() {
    if( is_shop() ){
        wp_redirect( home_url() );
        exit();
    }
}
add_action( 'template_redirect', 'diversity_shop_page_to_homepage_redirect' );

/*
# add shortcode for image path
*/
add_shortcode('theme_image_folder_path','theme_image_folder_path_callback');
function theme_image_folder_path_callback()
{
	return get_stylesheet_directory_uri()."/images/";
}
/*
# use other shortcodes inside Contact form 7- forms
*/
add_filter( 'wpcf7_form_elements', 'mycustom_wpcf7_form_elements' );
function mycustom_wpcf7_form_elements( $form ) {
	$form = do_shortcode( $form );
	return $form;
}

/*
# Total Number of right answers from all quesion
*/
function total_right_ans_from_questions($lesson_id,$question_id,$correct_ant_total)
{
	$correct_ans = Sensei_Question::get_correct_answer( $question_id );
	$user_data = Sensei()->quiz->get_user_question_answer( $lesson_id, $question_id, get_current_user_id() );
	$correct_ans." ".$user_data[0];
	$user_ans = $user_data[0];
	if($user_ans == $correct_ans)
	{
		
		$correct_ant_total++;
	}
	
	return $correct_ant_total;
}

/*
# add wrong and right ans after complete qu
*/
add_filter( 'sensei_question_answer_message_text','sensei_question_answer_message_text_callback',12,5);
function sensei_question_answer_message_text_callback( $answer_message, $lesson_id, $question_id, $get_current_user_id, $user_correct )
{
	$question_grade      = Sensei()->question->get_question_grade( $question_id );
	$user_question_grade = Sensei()->quiz->get_user_question_grade( $lesson_id, $question_id, get_current_user_id() );
	$get_user_answer = Sensei()->quiz->get_user_question_answer( $lesson_id, $question_id, get_current_user_id() );
	
	$wrong_ans_message = '<div class="ans-summery">';
	$wrong_ans_message .= '<div class="ans-summery-title">';
	$wrong_ans_message .= __( 'Ditt Svar', 'sensei-lms' );
	$wrong_ans_message .= '</div>';
	$wrong_ans_message .= '<div class="ans-summery-wrap">';
	$wrong_ans_message .= '<img src="'.get_stylesheet_directory_uri().'/images/quiz-cancel.svg" alt="Image" />'. $get_user_answer[0];
	$wrong_ans_message .= '</div>';
	$wrong_ans_message .= '</div>';


	$right_ans_message = '<div class="ans-summery">';
	$right_ans_message .= '<div class="ans-summery-title">';
	$right_ans_message .= __( 'Rätta Svar', 'sensei-lms' );
	$right_ans_message .= '</div>';
	$right_ans_message .= '<div class="ans-summery-wrap">';
	$right_ans_message .= '<img src="'.get_stylesheet_directory_uri().'/images/quiz-checked.svg" alt="Image" />'. Sensei_Question::get_correct_answer( $question_id );
	$right_ans_message .= '</div>';
	$right_ans_message .= '</div>';

	$answer_message = "";
	// For zero grade mark as 'correct' but add no classes
	if ( 0 == $question_grade ) {
		$user_correct         = true;
		$answer_message       = '';
	} elseif ( $user_question_grade > 0 ) {
		$user_correct         = true;
		$answer_message       = '';
	} else {
		$user_correct         = false;
		$answer_message = $wrong_ans_message;
		$answer_message .= $right_ans_message;
	}
	return $answer_message;
}

/*
# Add new class to ans-summery section
*/
add_filter( 'sensei_question_answer_message_css_class','sensei_question_answer_message_css_class_callback',12,5);
function sensei_question_answer_message_css_class_callback(  $answer_message_class, $lesson_id, $question_id, $get_current_user_id, $user_correct )
{
	$answer_message_class .= ' ans-summery-container';
	return $answer_message_class;
}


/*
# check for empty-cart get param to clear the cart
*/
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
  global $woocommerce;
	
	if ( isset( $_GET['empty-cart'] ) ) {
		$woocommerce->cart->empty_cart(); 
	}
}


/*
# return course data based on woo product id
*/
function sensei_get_courses_detail_of_woo_product($product_id)
{
	$course_args = array(
	   'post_type' => 'course',
	   'meta_query' => array(
	       array(
	           'key' => '_course_woocommerce_product',
	           'value' => $product_id,
	           'compare' => 'IN',
	       )
	   )
	);
	$course_query = new WP_Query($course_args);
	$course_data = array();
	$get_permalink = "";//array();
	$get_the_content = "";//array();

	if($course_query->have_posts())
	{ 
		while ( $course_query->have_posts() ) : $course_query->the_post();
			$get_permalink = get_the_permalink();
				$excerpt = get_the_content();
				$excerpt = esc_attr( strip_tags( stripslashes( $excerpt ) ) );
			$get_the_content = wp_trim_words( $excerpt, $num_words = 15, $more = NULL );
		endwhile;
		wp_reset_postdata();		
	}

	$course_data = array(
		'get_permalink' => $get_permalink,
		'get_the_content' => $get_the_content,
	);
	return $course_data;
}

/*
# add content bellow cart item name
*/
add_action('woocommerce_after_cart_item_name', 'woocommerce_after_cart_item_content', 10,2);
function woocommerce_after_cart_item_content( $cart_item, $cart_item_key) {
  if($cart_item['product_id'])
  {
  	$product_id = $cart_item['product_id'];
  	$corse_data = sensei_get_courses_detail_of_woo_product($product_id);
  	if($corse_data['get_the_content'])
  	{
  		echo '<p>'.$corse_data['get_the_content'].'</p>';
  	}
  }
}

/*
# Change product link to course link
*/

add_filter( 'woocommerce_cart_item_permalink', 'woocommerce_cart_item_permalink_callback',10,3) ;
add_filter( 'woocommerce_order_item_permalink', 'woocommerce_cart_item_permalink_callback',10,3) ;
function woocommerce_cart_item_permalink_callback( $new_permalink, $cart_item, $cart_item_key )
{
	if($cart_item['product_id'])
	{
	  	$product_id = $cart_item['product_id'];
	  	$corse_data = sensei_get_courses_detail_of_woo_product($product_id);
	  	if($corse_data['get_permalink'])
	  	{
	  		$new_permalink =  $corse_data['get_permalink'];
	  	}
	}
	return $new_permalink;
}

/*
# remove product-quantity from order review
*/
add_filter( 'woocommerce_order_item_quantity_html', 'woocommerce_order_item_quantity_html_callback',10,2) ;
function woocommerce_order_item_quantity_html_callback( $html, $item )
{
	$html = "";
	return $html;
}
/*
# Remove MP3 shortcode from plugin
*/
add_action('init', 'remove_plugin_player_shortcodes');
function remove_plugin_player_shortcodes() {
	remove_shortcode( 'sc_embed_player' );
	add_shortcode( 'sc_embed_player', 'addtheme_player_shortcodes' );
}

function addtheme_player_shortcodes($atts, $content = null) {
    extract(shortcode_atts(array(
        'fileurl' => '',
        'autoplay' => '',
        'volume' => '',
        'class' => '',
        'loops' => '',
                    ), $atts));
    if (empty($fileurl)) {
        return '<div style="color:red;font-weight:bold;">Compact Audio Player Error! You must enter the mp3 file URL via the "fileurl" parameter in this shortcode. Please check the documentation and correct the mistake.</div>';
    }
    if (empty($volume)) {
        $volume = '80';
    }
    if (empty($class)) {
        $class = "sc_player_container1";
    }//Set default container class
    if (empty($loops)) {
        $loops = "false";
    }
    $ids = uniqid('', true);//uniqid();

    $player_cont = '<div class="' . $class . ' diversity-class listen-a">';
    $player_cont .= '<input type="button" id="btnplay_' . $ids . '" class="lyssna-label" value="'.__( 'Lyssna', 'woocommerce' ).'" onClick="play_mp3(\'play\',\'' . $ids . '\',\'' . $fileurl . '\',\'' . $volume . '\',\'' . $loops . '\');show_hide(\'play\',\'' . $ids . '\');" />';
    $player_cont .= '<input type="button"  id="btnstop_' . $ids . '" style="display:none" class="lyssa-pause" value="'.__( 'Paus', 'woocommerce' ).'" onClick="play_mp3(\'stop\',\'' . $ids . '\',\'\',\'' . $volume . '\',\'' . $loops . '\');show_hide(\'stop\',\'' . $ids . '\');" />';
    $player_cont .= '<div id="sm2-container"><!-- flash movie ends up here --></div>';
    $player_cont .= '</div>';

    if (!empty($autoplay)) {
        $path_to_swf = SC_AUDIO_BASE_URL . 'swf/soundmanager2.swf';
        $player_cont .= <<<EOT
<script type="text/javascript" charset="utf-8">
soundManager.setup({
	url: '$path_to_swf',
	onready: function() {
		var mySound = soundManager.createSound({
		id: 'btnplay_$ids',
		volume: '$volume',
		url: '$fileurl'
		});
		var auto_loop = '$loops';
		mySound.play({
    		onfinish: function() {
				if(auto_loop == 'true'){
					loopSound('btnplay_$ids');
				}
				else{
					document.getElementById('btnplay_$ids').style.display = 'inline';
					document.getElementById('btnstop_$ids').style.display = 'none';
				}
    		}
		});
		document.getElementById('btnplay_$ids').style.display = 'none';
                document.getElementById('btnstop_$ids').style.display = 'inline';
	},
	ontimeout: function() {
		// SM2 could not start. Missing SWF? Flash blocked? Show an error.
		alert('Error! Audio player failed to load.');
	}
});
</script>
EOT;
    }//End autopay code

    return $player_cont;
}


/*add_action( 'wp_loaded', 'capture_coupon_code' );
add_action( 'woocommerce_before_cart','add_discout_to_cart', 10, 0 );
function capture_coupon_code() {
	$param_atc = 'add-to-cart';
	$param_cc = 'coupon_code';
	if( ! isset($_GET[$param_atc]) && ! isset($_GET[$param_cc]) &&  empty($_GET[$param_atc]) && empty($_GET[$param_cc]) ) {
		return;
	}

	$coupon_code = esc_attr( $_GET[$param_cc] );

	$coupon_valid = is_referral_coupon_valid($coupon_code);
	if(!$coupon_valid) {
		wc_print_notices();
		return;
	}

	if ( ! WC()->session->has_session() ) {
		WC()->session->set_customer_session_cookie( true );
	}
	
	WC()->session->set( 'coupon_code', $coupon_code ); // store the coupon code in session
}

function is_referral_coupon_valid( $coupon_code ) {
    $coupon = new \WC_Coupon( $coupon_code );   
    $discounts = new \WC_Discounts( WC()->cart );
    $valid_response = $discounts->is_coupon_valid( $coupon );
  
    if ( is_wp_error( $valid_response ) ) {
        return false;
    } else {
        return true;
    }
}

function add_discout_to_cart() {
	$coupon_code = WC()->session->get('coupon_code');
	if ( ! empty( $coupon_code ) && ! WC()->cart->has_discount( $coupon_code ) ){
		WC()->cart->add_discount( $coupon_code ); // apply the coupon discount
		WC()->session->__unset('coupon_code'); // remove coupon code from session
	}

	$param_atc = 'add-to-cart';
	$param_cc = 'coupon_code';
	if( ! isset($_GET[$param_atc]) && ! isset($_GET[$param_cc]) &&  empty($_GET[$param_atc]) && empty($_GET[$param_cc]) ) {
		return;
	}
	$product_id = $_GET[$param_atc];
	 if ( WC()->cart->has_discount( $coupon_code ) ) return;
 
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
 
    // this is your product ID
    $autocoupon = array( $product_id );
 
	    if( in_array( $cart_item['product_id'], $autocoupon ) ) {   
	        WC()->cart->add_discount( $coupon_code );
	        WC()->session->__unset('coupon_code');
	        wc_print_notices();

			// Remove the default `Added to cart` message
			//wc_clear_notices();
			
	    }
 
    }
    wp_redirect(WC()->cart->get_checkout_url());
	exit();
}

//add_filter ( 'add_to_cart_redirect', 'redirect_to_checkout' );
//add_action( 'template_redirect', 'redirect_to_checkout',99);
function redirect_to_checkout() {
    $param_atc = 'add-to-cart';
	$param_cc = 'coupon_code';
	if( isset($_GET[$param_atc]) && isset($_GET[$param_cc]) &&  !empty($_GET[$param_atc]) && !empty($_GET[$param_cc]) ) {
		global $woocommerce;
		// Remove the default `Added to cart` message
		//wc_clear_notices();
		return $woocommerce->cart->get_checkout_url();
	}
	
}*/

/*
# Coupon Code
*/
add_action('wp_footer', 'dd_custom_wc_button_script');
function dd_custom_wc_button_script() {
	?>
	<script>
		jQuery(document).ready(function($) {
			var ajaxurl = "<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>";
			$( document.body).on('click', '.dd-custom-add-to-cart-button', function(e) {
				e.preventDefault();
				var $this = $(this);
				if( $this.is(':disabled') ) {
					return;
				}
				var id = $(this).data("product-id");
				//var coupon_code = $(this).closest(".coupon_code").val();
				var coupon_code = $(this).closest("form.coupon-form").find("input[name=coupon_code]").val();
				var data = {
					action     : 'dd_custom_add_to_cart',
					product_id : id,
					coupon_code : coupon_code
				};
				$.post(ajaxurl, data, function(response) {
					console.log(response);
					if( response.success ) {
						$this.text("added to cart");
						$this.attr('disabled', 'disabled');
						$( document.body ).trigger( 'wc_fragment_refresh' );
						console.log(response.href);
						if(response.href)
						{
							window.location.href = response.href;
						
						}
						
					}
					else
					{
						alert(response.message);
					}
				}, 'json');
			})
		});
	</script>
	<?php
}
/*
# Apply Coupon code using ajax
*/
add_action('wp_ajax_dd_custom_add_to_cart', "dd_custom_add_to_cart");
add_action('wp_ajax_nopriv_dd_custom_add_to_cart', "dd_custom_add_to_cart");
function dd_custom_add_to_cart() {
	$retval = array(
		'success' => false,
		'message' => "",
		'href' => ""
	);
	if( !function_exists( "WC" ) ) {
		$retval['message'] = "woocommerce not installed";
	} elseif( empty( $_POST['product_id'] ) ) {
		$retval['message'] = "no product id provided";
	} else {
		$product_id = $_POST['product_id'];
		if( dd_custom_cart_contains( $product_id ) ) {
			$retval['message'] = "product already in cart";
		} else {

			// Get the value of the coupon code
			 $code = $_REQUEST['coupon_code'];
			    // Check coupon code to make sure is not empty
		    if( empty( $code ) || !isset( $code ) ) {
	
		        $retval = array(
					'success' => false,
					'message' => "Code text field can not be empty."
				);

		        header( 'Content-Type: application/json' );
		        echo json_encode( $retval );

		        // Always exit when doing ajax
		        exit();
		    }

		    // Create an instance of WC_Coupon with our code
		    $coupon = new WC_Coupon( $code );
	     	// Check coupon to make determine if its valid or not
		    if( ! $coupon->id && ! isset( $coupon_id ) ) {
		        // Build our response
		        $retval = array(
					'success' => false,
					'message' => "Invalid code entered. Please try again."
				);
		        header( 'Content-Type: application/json' );
		        echo json_encode( $retval );

		        // Always exit when doing ajax
		        exit();
		    }
		    else
		    {
		    	if(in_array($product_id, $coupon->product_ids))
		    	{
		    		$cart = WC()->cart;
					$retval['success'] = $cart->add_to_cart( $product_id );
					WC()->cart->add_discount( $code );
		    					
					if( !$retval['success'] ) {
						$retval['message'] = "product could not be added to cart";
					} else {
						$retval['message'] = "product added to cart";

				        // populate the cart with the attached products
				        /*foreach( $coupon->product_ids as $prod_id ) {
				        	 if( $prod_id == $_POST['product_id'] ) {
				        	 	// Attempting to add the coupon code as a discount.
				        	 	WC()->cart->add_discount( $code );
				           		 //WC()->cart->add_to_cart( $prod_id );
				        	}
				        }*/
				        // Build our response
				        $retval = array(
							'success' => true,
							'href' => WC()->cart->get_checkout_url(),
							//'href' => WC()->cart->get_cart_url()
						);
				        header( 'Content-Type: application/json' );
				        echo json_encode( $retval );

				        // Always exit when doing ajax
				        exit();
		    		}
	    		}
	    		else
	    		{
	    			$retval = array(
						'success' => false,
						'message' => "Sorry, this coupon is not applicable to selected products."
					);
			        header( 'Content-Type: application/json' );
			        echo json_encode( $retval );

			        // Always exit when doing ajax
			        exit();
	    		}
			}
		}
	}
	echo json_encode( $retval );
	wp_die();
}
function dd_custom_cart_contains( $product_id ) {
	$cart = WC()->cart;
	$cart_items = $cart->get_cart();
	if( $cart_items ) {
		foreach( $cart_items as $item ) {
			$product = $item['data'];
			if( $product_id == $product->id ) {
				return true;
			}
		}
	}
	return false;
}


function dd_sensei_module_has_lessons() {

	global $wp_query, $sensei_modules_loop;

	if ( 'lesson' == $wp_query->get( 'post_type' ) ) {

		$index = $sensei_modules_loop['current'] ;

		if ( isset( $sensei_modules_loop['modules'][ $index ] ) ) {
			// setup the query for the module lessons
			$course_id = $sensei_modules_loop['course_id'];

			$module_term_id = $sensei_modules_loop['modules'][ $index ]->term_id;
			$modules_query  = Sensei()->modules->get_lessons_query( $course_id, $module_term_id );

			// setup the global wp-query only if the lessons
			if ( $modules_query->have_posts() ) {

				return have_posts();

			}
		}

		return false;

	} else {

		// if the loop has not been initiated check the first module has lessons
		/*if ( -1 == $sensei_modules_loop['current'] ) {

			$index = 0;

			if ( isset( $sensei_modules_loop['modules'][ $index ] ) ) {
				// setup the query for the module lessons
				$course_id = $sensei_modules_loop['course_id'];

				$module_term_id = $sensei_modules_loop['modules'][ $index ]->term_id;
				$modules_query  = Sensei()->modules->get_lessons_query( $course_id, $module_term_id );

				// setup the global wp-query only if the lessons
				if ( $modules_query->have_posts() ) {

					return true;

				}
			}
		}*/
		// default to false if the first module doesn't have posts
		return false;

	}

}