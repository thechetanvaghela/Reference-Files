<?php

/*	Include Media for wp uploader
*	action : admin_enqueue_scripts
*	function : gloniq_admin_scipts
*	used functions : wp_enqueue_media
*/
add_action( 'admin_enqueue_scripts', 'gloniq_admin_scipts', 20 );
function gloniq_admin_scipts()
{
	wp_enqueue_media();
	wp_enqueue_script( 'gloniq-custom', get_template_directory_uri() . '/js/admin-scripts.js', array(), '20151215', true );
}


/*	add text below product image 
*	action : woocommerce_before_shop_loop_item_title
*	function : add_quote_bg_text
*	used functions : wp_enqueue_media
*/
add_action( 'woocommerce_before_shop_loop_item_title', 'add_quote_bg_text', 20 );
function add_quote_bg_text() {
	if(	!is_front_page())
	{
  		echo '<p class="pick-line">'.__('Pick your quote and background!').'</p>';
  	}

}

/*REMOVE ADD TO CART BUTTONS*/
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 20);

/*add image class*/
add_filter( 'woo_variation_gallery_product_image_classes','add_images_calss_into_product_gallery',10,1);
function add_images_calss_into_product_gallery($classes)
{
	$classes[] ="images";
	return $classes;
}


/*remove review tab*/
add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
    function wcs_woo_remove_reviews_tab($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}

/*	add a media upload for transparent image
*	action : woocommerce_product_after_variable_attributes
*	function : gloniq_add_custom_field_to_variations
*	used functions : get_post_meta, wp_get_attachment_image_src
*/
//add_action( 'woocommerce_variation_options_pricing', 'gloniq_add_custom_field_to_variations', 10, 3 );
add_action( 'woocommerce_product_after_variable_attributes', 'gloniq_add_custom_field_to_variations', 10, 3 );
if ( ! function_exists( 'gloniq_add_custom_field_to_variations' ) ):
	function gloniq_add_custom_field_to_variations( $loop, $variation_data, $variation ) 
	{
		$variation_id   = absint( $variation->ID );
		$transparent_image_id = get_post_meta( $variation_id, 'woo_variation_transparent_image', true );		
		?>
        <div class="form-row form-row-full woo-variation-transparent-wrapper" id="variation-transparent-<?php echo $variation_id?>">
            <h4><?php esc_html_e( 'Variation Transparent Image', 'woocommerce' ) ?></h4>
            <div class="woo-variation-transparent-image-container">
                <ul class="woo-variation-transparent-images">
					<?php
						
						$img_style = 'style="display:none;"';
						if (! empty( $transparent_image_id ) ) 
						{
							$image = wp_get_attachment_image_src( $transparent_image_id );
							$image_src = $image[ 0 ];
							$img_style = "";
						}
							?>
					        <li class="image-data-container" <?php echo $img_style; ?>>
					            <input type="hidden" class="transparent-image-id" name="woo_variation_transparent[<?php echo $variation_id ?>]" value="<?php echo $transparent_image_id ?>">
					            <img class="transparent-image-field" src="<?php echo esc_url($image_src) ?>" style="width:64px;hwight:64px">
					            <a href="javascript:void(0);" class="delete remove-woo-variation-transparent-image"><span class="dashicons dashicons-dismiss"></span></a>
					        </li>
							<?php 
						
					?>
                </ul>
            </div>
                <a href="javascript:void(0);" data-product_variation_id="<?php echo absint( $variation->ID ) ?>" class="button add-woo-variation-transparent-image"><?php esc_html_e( 'Add Transparent Image', 'woocommerce' ) ?></a>
        </div>
		<?php
	}
endif; 

/*	save and delete transparent image
*	action : woocommerce_save_product_variation
*	function : gloniq_save_variation_transparent_image
*	used functions : update_post_meta, delete_post_meta
*/
add_action( 'woocommerce_save_product_variation', 'gloniq_save_variation_transparent_image', 10, 2 );
if ( ! function_exists( 'gloniq_save_variation_transparent_image' ) ):
		function gloniq_save_variation_transparent_image( $variation_id, $i ) {
			if ( isset( $_POST[ 'woo_variation_transparent' ] ) ) {
				if ( isset( $_POST[ 'woo_variation_transparent' ][ $variation_id ] ) ) {
					update_post_meta( $variation_id, 'woo_variation_transparent_image', $_POST[ 'woo_variation_transparent' ][ $variation_id ] );
				} else {
					delete_post_meta( $variation_id, 'woo_variation_transparent_image' );
				}
			} else {
				delete_post_meta( $variation_id, 'woo_variation_transparent_image' );
			}
		}
	endif;

/*	save and delete background images
*	action : save_post
*	function : gloniq_backgrounds_posts_save
*	used functions : update_post_meta, delete_post_meta
*/
add_action( 'save_post', 'gloniq_backgrounds_posts_save', 10, 1 );
function gloniq_backgrounds_posts_save($post_id){
    $post_type = get_post_type($post_id);
    if($post_type == 'product') {
    	if ( isset( $_POST[ 'bg_posts_ids' ] ) && !empty( $_POST[ 'bg_posts_ids' ] ) ) {
			update_post_meta( $post_id, 'gloniq_backgrounds_post_ids', $_POST[ 'bg_posts_ids' ]);
		} else {
			delete_post_meta( $post_id, 'gloniq_backgrounds_post_ids' );
		}
        
    }
}

/*	add new product tab
*	action : woocommerce_product_data_tabs
*	function : gloniq_background_product_tabs
*	return : tabs
*/
function gloniq_background_product_tabs( $tabs) {

	$tabs['gloniq-backgrounds'] = array(
		'label'		=> __( 'Backgrounds', 'woocommerce' ),
		'target'	=> 'backgrounds_options',
		'class'		=> array( 'show_if_simple', 'show_if_variable'  ),
	);

	return $tabs;

}
add_filter( 'woocommerce_product_data_tabs', 'gloniq_background_product_tabs' );



/**
* Contents of the background options product tab.
*	action : woocommerce_product_data_panels
*	function : gloniq_background_options_product_tab_content
*	return : display data in panel
*/
function gloniq_background_options_product_tab_content() {

	global $post;
	// Note the 'id' attribute needs to match the 'target' parameter set above 
	?>
	<div id='backgrounds_options' class='panel woocommerce_options_panel'>
		<div class='options_group'>
			<p class="form-field">
				<?php
				$bg_posts_ids = get_post_meta($post->ID, 'gloniq_backgrounds_post_ids',true );
				?>
				<label for="bg_posts_ids"><?php esc_html_e( 'Backgrounds', 'woocommerce' ); ?></label>
				<select class="wc-background-search" multiple="multiple" style="width: 50%;" id="bg_posts_ids" name="bg_posts_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a Background', 'woocommerce' ); ?>" data-post-type="gloniq-background">
					<?php
					foreach ($bg_posts_ids as $key => $bg_posts_id) {
						echo '<option value="' . esc_attr($bg_posts_id) . '"' . selected( true, true, false ) . '>' . get_the_title($bg_posts_id) . '</option>';
					}
					?>
				</select> 
				<?php echo wc_help_tip( __( 'Select Background image for product.', 'woocommerce' ) ); // WPCS: XSS ok. ?>
			</p>
		</div>
	</div>
	<?php
}
add_filter( 'woocommerce_product_data_panels', 'gloniq_background_options_product_tab_content' ); // WC 2.6 and up



/**
* 	serach and select post id and title
*	action : wp_ajax_background_post_select_lookup
*	function : background_post_select_lookup
*	return : id and title of searched post
*/
add_action('wp_ajax_background_post_select_lookup', 'background_post_select_lookup');
function background_post_select_lookup() {
    global $wpdb;
    $result = array();
    $search = like_escape($_REQUEST['q']);
    $post_type = $_REQUEST['post_type'];

    // Don't forget that the callback here is a closure that needs to use the $search from the current scope
    add_filter('posts_where', function( $where ) use ($search) {
		$where .= (" AND post_title LIKE '%" . $search['term'] . "%'");
		return $where;
	});
    				
    $default_query = array(
    					'posts_per_page' => -1,
    					'post_status' => array('publish'),
    					'post_type' => 'gloniq-background',
    					'order' => 'ASC',
    					'orderby' => 'title'	    			
    				);

    $ajax_bg_posts = new wp_Query( $default_query );
   
     while ( $ajax_bg_posts -> have_posts() ) {
				$ajax_bg_posts->the_post();
		        $bg_post_title = get_the_title();
		        $bg_id = get_the_id();

        $result[] = array(
        				'id' => $bg_id,
        				'title' => $bg_post_title,
        				);
    }

    echo json_encode($result);

    die();
}

/**
* 	 Available transparent image
*	action : woocommerce_available_variation
*	function : gloniq_available_variation_transparent
*	return : id and title of searched post
*/

add_filter( 'woocommerce_available_variation', 'gloniq_available_variation_transparent', 90, 3 );
if ( ! function_exists( 'gloniq_available_variation_transparent' ) ):
	function gloniq_available_variation_transparent( $available_variation, $variationProductObject, $variation ) {		
		$product_id                   = absint( $variation->get_parent_id() );
		$product                      = wc_get_product( $product_id );
		$variation_id                 = absint( $variation->get_id() );
		
		// duplicate the line for each field
		$transparent_image_id = get_post_meta($variation_id, 'woo_variation_transparent_image', true );
		if (! empty( $transparent_image_id ) ) 
		{
			$image = wp_get_attachment_image_src( $transparent_image_id , 'full' );
			$image_src = $image[ 0 ];
				          
		 	$transparent_image = '<img src="'.esc_url($image_src).'">';
		 	$available_variation['_select_transparent_image_id'] = $transparent_image_id;
		 	$available_variation['_select_transparent_image'] = $transparent_image;
		  
			return apply_filters( 'gloniq_available_variation_transparent', $available_variation, $variation, $product );
		}
	}
endif;


/**
*	action : backgroung_quote_image_set
*	function : backgroung_quote_image_set
*	return : full and thumb image html
*/
add_action('wp_ajax_backgroung_quote_image_set', 'backgroung_quote_image_set');
add_action('wp_ajax_nopriv_backgroung_quote_image_set', 'backgroung_quote_image_set');
function backgroung_quote_image_set() {
	
    if(isset($_POST['bg_post_id']) && isset($_POST['thumb_img_src']) && isset($_POST['full_img_src']) ) 
	{
		$bg_post_id = $_POST['bg_post_id'];
		$thumb_img_src = $_POST['thumb_img_src'];
      	$bg_image_src = $_POST['full_img_src'];
      	$product_id = $_POST['product_id'];
      	$var_id = $_POST['var_id'];
      	$transparent_image_src = $_POST['transparent_image_src'];

      	$data = array();
      	$status = "";
      	$thumb_img_html = "";
      	$full_img_html = "";
      	$slider_require = "true";
      
      
	    if(!empty($bg_post_id))
      	{
      		$status    = "success";
      		ob_start();
      		
      		$product = wc_get_product( $product_id );

      		if(!empty($bg_post_id) && !empty($var_id) && !empty($transparent_image_src))
      		{
      			$upload_dir = wp_upload_dir();
				$upload_basedir = $upload_dir['basedir'];
				if (!file_exists($upload_basedir."/temp-images/")) {
					mkdir($upload_basedir."/temp-images/", 0755, true);
				}

				$image_1 = imagecreatefromstring(file_get_contents($bg_image_src));
				$image_2 = imagecreatefrompng($transparent_image_src);

				$image_1 = imagescale($image_1, 500,500);
				$im_width = imagesx($image_1);
				$im_height = imagesy($image_1);

				//$image_2 = imagescale($image_2, $im_width/5);
				$wt_width = imagesx($image_2);
				$wt_height = imagesy($image_2);

				imagealphablending($image_2, true);
				imagesavealpha($image_2, true);
				//imagecopy($image_1, $image_2, 700, 200, 0, 0, 365, 365);
				imagecopy($image_1, $image_2, $im_width - $wt_width -50 , $im_height - $wt_height - 100, 0, 0, $wt_width, $wt_height);
				header('Content-Type: image/png');

				//$upload_basedirs = $upload_dir['path'];
				//$image_url = $upload_basedirs.'/merged-'.time().'.jpg';
				$image_url = $upload_basedir.'/temp-images/merged-'.time().'.jpg';

				imagepng($image_1, $image_url);
				//imagepng($image_1);
				$wp_filetype = wp_check_filetype(basename($image_url), null );

				$attachment = array(
				    'post_mime_type' => $wp_filetype['type'],
				    'post_title' => basename($image_url),
				    'post_content' => '',
				    'post_status' => 'inherit'
				);
				$attachment_id = wp_insert_attachment( $attachment, $image_url );
				$attach_data = wp_generate_attachment_metadata( $attachment_id, $image_url );
 				wp_update_attachment_metadata( $attachment_id,  $attach_data );
				
				imagedestroy($image_1);
				imagedestroy($image_2);

				$full_img_html = wvg_get_gallery_image_html( $attachment_id, true );
				$thumb_img_html = "";
				$slider_require = "false";
      			
      		}
      		else
      		{

	      		/*if(!empty($var_id))
	      		{
	      			$attachment_ids = get_post_meta( $var_id, 'woo_variation_gallery_images', true );
	      		}
	      		else
	      		{*/
	      			$attachment_ids = $product->get_gallery_image_ids();
				//}
				
				$has_gallery_thumbnail = ( has_post_thumbnail($product_id) && ( count( $attachment_ids ) > 0 ) );

				if(!empty($_POST['bg_post_id']))
				{
					$bg_post_thumb_id = get_post_thumbnail_id( $bg_post_id );
					$full_img_html .=  wvg_get_gallery_image_html( $bg_post_thumb_id, true );
				}

	      		if ( has_post_thumbnail($product_id) ) :
	      			$post_thumbnail_id = get_post_thumbnail_id( $product_id );
					$full_img_html .= wvg_get_gallery_image_html( $post_thumbnail_id, true );
				else:
					$full_img_html .= '<div class="wvg-gallery-image wvg-gallery-image-placeholder">';
					$full_img_html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
					$full_img_html .= '</div>';
				endif;
				
				// Gallery Image
				if ( $has_gallery_thumbnail ) :
					foreach ( $attachment_ids as $attachment_id ) :
						$full_img_html .= wvg_get_gallery_image_html( $attachment_id, true );
					endforeach;
				endif;

				#thumbnail slider

				if(!empty($_POST['bg_post_id']))
				{
					$bg_post_thumb_id = get_post_thumbnail_id( $bg_post_id );
					$thumb_img_html .=  wvg_get_gallery_image_html( $bg_post_thumb_id, false );
				}
				if ( $has_gallery_thumbnail ):
					// Main Image
					$thumb_img_html .= wvg_get_gallery_image_html( $post_thumbnail_id );
					
					// Gallery Image
					foreach ( $attachment_ids as $key => $attachment_id ) :
						$thumb_img_html .= wvg_get_gallery_image_html( $attachment_id, false, $key );
					endforeach;
				endif;
			}
			ob_get_clean();
			


      		//ob_start();
      		//include_once woo_variation_gallery()->template_path( 'product-images.php' );
      		//include(WOO_VG_PLUGIN_TEMPLATE_PATH."product-images.php");
      		//include(WP_PLUGIN_URL."/templates/product-images.php");

		    //$full_img_html = WP_PLUGIN_URL."/woo-variation-gallery/templates/product-images.php";//ob_get_clean(); //WP_PLUGIN_DIR."  ".WP_PLUGIN_URL;
		    //$full_img_html = ob_get_clean(); //WP_PLUGIN_DIR."  ".WP_PLUGIN_URL;
		    //$status    = "success";
		    //$thumb_img_html   = "success";
		    //$attachment_id = get_post_thumbnail_id( $bg_post_id );
		   
	      	//$full_img_html =  wvg_get_gallery_image_html( $attachment_id, true );
	      	//$thumb_img_html = wvg_get_gallery_image_html( $attachment_id, false );

	      	//add_filter( 'wvg_gallery_image_html_class', array('bg-image-added'), $attachment_id );
	      	/*add_filter( 'wvg_gallery_image_html_class', 'tutsplus_the_content' );
			function tutsplus_the_content( $content, $attachment_id ) {
			    return $content[] = 'bg-image-added';
			}*/
			//add_filter( 'woo_variation_gallery_thumbnail_image_html_class', array('bg-image-added'), $attachment_id );
			/*add_filter( 'woo_variation_gallery_thumbnail_image_html_class', 'tutsplus_the_content_2' );
			function tutsplus_the_content_2( $content , $attachment_id ) {
			   return $content[] = 'bg-image-added';
			}*/

		}
	}
	else
	{
	    $status    = "error";
        $thumb_img_html   = "Something Wrong!";
        $full_img_html   = "Something Wrong!";
        $slider_require  = "Something Wrong!";
	}

	$data = array(
		            "status"     => $status,
		            "thumb_img_html"  => $thumb_img_html,
		            "full_img_html"   => $full_img_html,
		            "slider_require"   => $slider_require
	);
    echo json_encode($data);
    die();
}



/**
 * Add engraving text to cart item.
 *
 * @param array $cart_item_data
 * @param int   $product_id
 * @param int   $variation_id
 *
 * @return array
 */
function add_bg_images_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
	$bg_qoutes_post_id = filter_input( INPUT_POST, 'bg-qoutes' );
	$transparent_image_id = filter_input( INPUT_POST, '_select_transparent_image_id' );
	$attribute_pa_color = filter_input( INPUT_POST, 'attribute_pa_color' );
	$personalize_note_text = filter_input( INPUT_POST, 'personalize-note-text' );
	$personalize_note_font = filter_input( INPUT_POST, 'personalize-font-family' );

	/*if ( empty( $bg_qoutes_post_id ) && empty( $transparent_image_id ) ) {
		return $cart_item_data;
	}*/

	$cart_item_data['selected_attribute_color'] = $attribute_pa_color;
	$cart_item_data['bg_qoutes_post_id'] = $bg_qoutes_post_id;
	$cart_item_data['transparent_image_id'] = $transparent_image_id;
	$cart_item_data['personalize_note_text'] = $personalize_note_text;
	$cart_item_data['personalize_note_font'] = $personalize_note_font;

	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_bg_images_to_cart_item', 10, 3 );


add_action('wp_ajax_data_update_existing_cart_item_meta', 'data_update_existing_cart_item_meta');
add_action('wp_ajax_nopriv_data_update_existing_cart_item_meta', 'data_update_existing_cart_item_meta');
function data_update_existing_cart_item_meta() {

	if(isset($_POST['donate_to']) && !empty($_POST['donate_to']) ) 
	{
		 $cart = WC()->cart->cart_contents;
		 foreach( $cart as $cart_item_id=>$cart_item ) {
			 $cart_item['donate_to'] = $_POST['donate_to'];
			 WC()->cart->cart_contents[$cart_item_id] = $cart_item;
		 }
		 WC()->cart->set_session();
		  $status    = "success";
        $message   = "Data update!";
    }
    else
	{
	    $status    = "error";
        $message   = "Something Wrong!";
	}

	$data = array(
		   "status"     => $status,
		   "message"     => $message,
		          
	);
    echo json_encode($data);
    die();
}

/**
 * Display in the cart.
 *
 * @param array $item_data
 * @param array $cart_item
 *
 * @return array
 */
function gloniq_display_bg_images_cart( $item_data, $cart_item ) {
    
 	$bg_qoutes_post_title = get_the_title($cart_item['bg_qoutes_post_id']);
 	$product = $cart_item['data'];
    $sku = $product->get_sku();
    
    if(!empty($cart_item['selected_attribute_color']))
    {
	    $item_data[] = array(
	        'key'     => __( 'Color', 'woocommerce' ),
	        'value'   => wc_clean(ucfirst($cart_item['selected_attribute_color'])),
	        'display' => '',
	    );
	}
	if(!empty($sku))
    {
	     $item_data[] = array(
	        'key'     => __( 'SKU', 'woocommerce' ),
	        'value'   => wc_clean( $sku ),
	        'display' => '',
	    );
	}
	if(!empty($cart_item['bg_qoutes_post_id']))
    {
	    $item_data[] = array(
	        'key'     => __( 'Graphic', 'woocommerce' ),
	        'value'   => wc_clean( $bg_qoutes_post_title ),
	        'display' => '',
	    );
	}
	if(!empty($cart_item['personalize_note_text']))
    {
	    $item_data[] = array(
	        'key'     => __( 'Message', 'woocommerce' ),
	        'value'   => wc_clean( $cart_item['personalize_note_text'] ),
	        'display' => '',
	    );
 	}
 	if(!empty($cart_item['personalize_note_font']) && !empty($cart_item['personalize_note_text']))
    {
	    $item_data[] = array(
	        'key'     => __( 'Font', 'woocommerce' ),
	        'value'   => wc_clean( $cart_item['personalize_note_font'] ),
	        'display' => '',
	    );
 	}
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'gloniq_display_bg_images_cart', 10, 2 );



/*	add pernoalize text price in main price 
*	function : woocommerce_before_calculate_totals
*/
add_action( 'woocommerce_before_calculate_totals', 'add_personalize_item_price', 10 );
function add_personalize_item_price( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    foreach ( $cart->get_cart() as $cart_item ) {
        // Product ID
        $product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $cart_item['data']->id : $cart_item['data']->get_id();
        // Product original price
        $original_price = version_compare( WC_VERSION, '3.0', '<' ) ? $cart_item['data']->price : $cart_item['data']->get_price();
        //$original_price = $item_values['data']->price; 

        ## --- Get your custom cart item data --- ##
        $personalize_note_price = 0;

        if( isset($cart_item['personalize_note_text']) && !empty($cart_item['personalize_note_text']) )
        {
            $personalize_note_text  = $cart_item['personalize_note_text'];
            $personalize_note_price = !empty(get_option( 'woocommerce_personalize_note_price', 1 )) ? get_option( 'woocommerce_personalize_note_price', 1 ) : 0;
        }
 	
        // CALCULATION FOR EACH ITEM:
        ## Make HERE your own calculation to feet your needs  <==  <==  <==  <==
        $new_price = $original_price + $personalize_note_price;

        ## Set the new item price in cart
        if( version_compare( WC_VERSION, '3.0', '<' ) )
            $cart_item['data']->price = $new_price;
        else
            $cart_item['data']->set_price($new_price);
    }
}



/*
*	add pernoalize text price OPTION IN WOOCOMMERCE SETTING
*	function : woocommerce_general_settings
    woocommerce_general_settings
    woocommerce_catalog_settings
    woocommerce_page_settings
    woocommerce_inventory_settings
    woocommerce_tax_settings
    woocommerce_shipping_settings
    woocommerce_payment_gateways_settings
    woocommerce_email_settings
*/
add_filter( 'woocommerce_general_settings', 'add_personalized_text_price_setting' );
function add_personalized_text_price_setting( $settings ) {
  $updated_settings = array();
  foreach ( $settings as $section ) {
    // at the bottom of the General Options section
    if ( isset( $section['id'] ) && 'general_options' == $section['id'] &&
       isset( $section['type'] ) && 'sectionend' == $section['type'] ) {

      $updated_settings[] = array(
        'name'     => __( 'Personalize Note Price', 'woocommerce' ),
        'desc_tip' => __( 'Price will apply on selected product with personalize note.', 'woocommerce' ),
        'id'       => 'woocommerce_personalize_note_price',
        'type'     => 'text',
        'css'      => 'min-width:300px;',
        'std'      => '0',  // WC < 2.0
        'default'  => '0',  // WC >= 2.0
        'desc'     => __( 'Price will apply on selected product with personalize note.', 'woocommerce' ),
      );

    }
    $updated_settings[] = $section;
  }
  return $updated_settings;
}


/*	add Charity in order review section
*	function : woocommerce_review_order_after_cart_contents
*/
function action_woocommerce_review_order_after_cart_contents() { 
  	 //if ( ! is_ajax() ) {
  	 	$cart = WC()->cart->cart_contents;
  	 	$donate_to = "";
  	  	foreach( $cart as $cart_item_id=>$cart_item ) {
				$donate_to =  $cart_item['donate_to'];
				 break;
		 } 

		 if(!empty($donate_to))
		 {
  		?>
  		<tr class="cart-charity">
			<th><?php _e( 'Charity Name', 'woocommerce' ); ?></th>
			<td><?php _e( $donate_to, 'woocommerce' );  ?></td>
		</tr>
  <?php
		}
	//}
}      
add_action( 'woocommerce_review_order_after_cart_contents', 'action_woocommerce_review_order_after_cart_contents', 10, 0 ); 



/*	add plcae order button below order review in checkout
*	function : woocommerce_review_order_after_order_total
*/
add_action( 'woocommerce_review_order_after_order_total', 'add_place_order_button', 10, 0 ); 

function add_place_order_button()
{
	 $order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );
    
	?>
	<tr class="order-place" >
			<td colspan="2">
			<?php echo '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />';?><td>
		</tr>
	<?php
}

/*	remove plcae order button html content
*	function : woocommerce_review_order_after_order_total
*/
function woocommerce_order_button_html() {
    return '';
}
add_filter( 'woocommerce_order_button_html', 'remove_woocommerce_order_button_html' );


/*	enable shipping for checkout page only
*	function : woocommerce_cart_needs_shipping
*/
/*add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_on_cart' );
add_filter( 'woocommerce_cart_needs_shipping', 'disable_shipping_on_cart' );
function disable_shipping_on_cart( $enabled ){
    return is_checkout() ? true : false;
}
*/


/*	remove order note from shipping and add belo billing form
*	function : woocommerce_checkout_fields
*/
//add_filter( 'woocommerce_checkout_fields' , 'customizing_checkout_fields', 10, 1 );
function customizing_checkout_fields( $fields ) {
    // Remove the Order Notes
    unset($fields['order']['order_comments']);
    // Define custom Order Notes field data array
    $customer_note  = array(
        'type' => 'textarea',
        'class' => array('form-row-wide', 'notes'),
        'label' => __('Order Notes', 'woocommerce'),
        'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce')
    );

    // Set custom Order Notes field
    $fields['billing']['billing_customer_note'] = $customer_note;

    // Define billing fields new order
    $ordered_keys = array(
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_country',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_phone',
        'billing_email',
        'billing_customer_note', // <= HERE
    );

    // Set billing fields new order
    $count = 0;
    foreach( $ordered_keys as $key ) {
        $count += 10;
        $fields['billing'][$key]['priority'] = $count;
    }

    return $fields;
}


/*	Set the custom field 'billing_customer_note' in the order object as a default order note (before it's saved)
*	function : woocommerce_checkout_create_order
*/
//add_action( 'woocommerce_checkout_create_order', 'customizing_checkout_create_order', 10, 2 );
function customizing_checkout_create_order( $order, $data ) {
    $order->set_customer_note( isset( $data['billing_customer_note'] ) ? $data['billing_customer_note'] : '' );
}


/*	# remove payment methods from order summry and add below order review
*	functions : woocommerce_checkout_order_review, woocommerce_after_order_notes
*/
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_checkout_before_order_review_heading', 'woocommerce_checkout_payment', 20 );

/*	# remove coupne form from above and add below order review
*	functions : woocommerce_before_checkout_form, woocommerce_after_order_notes
*/
//remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
//add_action( 'woocommerce_after_order_notes', 'woocommerce_checkout_coupon_form' );


/**
 * Add custom data to order.
 */
function woocommerce_add_custom_data_to_order_items( $item, $cart_item_key, $values, $order ) {
	
	$attribute_pa_color = $values['selected_attribute_color'];
	$bg_qoutes_post_id = $values['bg_qoutes_post_id'];
	$transparent_image_id = $values['transparent_image_id'];
	$personalize_note_text = $values['personalize_note_text'];
	$personalize_note_font = $values['personalize_note_font'];
	$donate_to = $values['donate_to'];
	
	$product = $item->get_product();
 	//echo "<pre>";
	//print_r($product);
	//print_r($sku);
	//print_r($values);
	//echo "</pre>";
	//die("581");

    if(!empty($attribute_pa_color))
    {
		$item->add_meta_data( __( 'Color', 'woocommerce' ), $attribute_pa_color );
	}
	/*if(!empty($sku))
    {
		$item->add_meta_data( __( 'SKU', 'woocommerce' ), $sku );
	}*/
	if(!empty($bg_qoutes_post_id))
    {
    	$bg_qoutes_post_title = get_the_title($bg_qoutes_post_id);
		$item->add_meta_data( __( 'Graphic', 'woocommerce' ), $bg_qoutes_post_title );
	}
	if(!empty($personalize_note_text))
    {
		$item->add_meta_data( __( 'Message', 'woocommerce' ), $personalize_note_text );
	}
	if(!empty($personalize_note_font) && !empty($personalize_note_text))
    {
		$item->add_meta_data( __( 'Font', 'woocommerce' ), $personalize_note_font );
	}
	if(!empty($donate_to))
    {
		$item->add_meta_data( __( 'Charity', 'woocommerce' ), $donate_to );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'woocommerce_add_custom_data_to_order_items', 10, 4 );

/*
* hook into the fragments in AJAX and add our new table to the group
*/
add_filter('woocommerce_update_order_review_fragments', 'websites_depot_order_fragments_split_shipping', 10, 1);
function websites_depot_order_fragments_split_shipping($order_fragments) {
	ob_start();
	websites_depot_woocommerce_order_review_shipping_split();
	$websites_depot_woocommerce_order_review_shipping_split = ob_get_clean();
	$order_fragments['.websites-depot-checkout-review-shipping-table'] = $websites_depot_woocommerce_order_review_shipping_split;
	return $order_fragments;
}

/*
* get the template that just has the shipping options that we need for the new table
*/
function websites_depot_woocommerce_order_review_shipping_split( $deprecated = false ) {
	wc_get_template( 'checkout/shipping-order-review.php', array( 'checkout' => WC()->checkout() ) );
}


/*
* add shippig method icon
*/
add_action('woocommerce_checkout_before_order_review_heading', 'websites_depot_move_new_shipping_table', 5);
function websites_depot_move_new_shipping_table() {
	echo '<table class="shop_table websites-depot-checkout-review-shipping-table"><h3 class="shipping-method-lable"><i class="fal fa-truck"></i> &nbsp;Shipping Methods</h3></table>';
}


/*
* update cart item detail in popup using ajax
*/
add_filter( 'woocommerce_add_to_cart_fragments', 'iconic_cart_count_fragments', 10, 1 );
function iconic_cart_count_fragments( $fragments ) {
    $fragments['span.header-cart-count'] = '<span class="header-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>'; 
    return $fragments;
}

/*
*  Display Cart items in cart and checkout page in popup
*/
add_filter( 'woocommerce_widget_cart_is_hidden', '__return_false', 40, 0 );


/*
* Remove lable from checkout fields
*/
add_filter('woocommerce_checkout_fields','custom_wc_checkout_fields_no_label');
function custom_wc_checkout_fields_no_label($fields) {
    // loop by category
    foreach ($fields as $category => $value) {
        // loop by fields
        foreach ($fields[$category] as $field => $property) {
            // remove label property
            unset($fields[$category][$field]['label']);
        }
    }
     return $fields;
}


/*
*	override default address checkout fields
*/
add_filter( 'woocommerce_default_address_fields', 'custom_override_default_checkout_fields', 10, 1 );
function custom_override_default_checkout_fields( $address_fields ) {
    // Remove labels for "address 2" shipping fields
    unset($address_fields['address_1']['placeholder']);
    return $address_fields;
}

/*
*	Change Placeholder of checkout fields
*/
add_filter( 'woocommerce_checkout_fields' , 'override_billing_checkout_fields', 20, 1 );
 function override_billing_checkout_fields( $fields ) {

     $fields['billing']['billing_first_name']['placeholder'] = 'First name';
     $fields['billing']['billing_last_name']['placeholder'] = 'Last name';
     $fields['billing']['billing_company']['placeholder'] = 'Company name (optional)';
     $fields['billing']['billing_country']['placeholder'] = 'Country';
     $fields['billing']['billing_address_1']['placeholder'] = 'Street address';
     $fields['billing']['billing_postcode']['placeholder'] = 'Postcode / ZIP';
     $fields['billing']['billing_phone']['placeholder'] = 'Phone';
     $fields['billing']['billing_city']['placeholder'] = 'Town / City';
     $fields['billing']['billing_state']['placeholder'] = 'State / County';
     $fields['billing']['billing_email']['placeholder'] = 'Email address';


     $fields['shipping']['shipping_first_name']['placeholder'] = 'First name';
     $fields['shipping']['shipping_last_name']['placeholder'] = 'Last name';
     $fields['shipping']['shipping_company']['placeholder'] = 'Company name (optional)';
     $fields['shipping']['shipping_country']['placeholder'] = 'Country';
     $fields['shipping']['shipping_address_1']['placeholder'] = 'Street address';
     $fields['shipping']['shipping_postcode']['placeholder'] = 'Postcode / ZIP';
     $fields['shipping']['shipping_phone']['placeholder'] = 'Phone';
     $fields['shipping']['shipping_city']['placeholder'] = 'Town / City';
     $fields['shipping']['shipping_state']['placeholder'] = 'State / County';
     $fields['shipping']['shipping_email']['placeholder'] = 'Email address';

     return $fields;
 }



/*if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
  //wp_schedule_event( time(), 'hourly', 'my_task_hook' );
}

add_action( 'my_task_hook', 'my_task_function' );

function my_task_function() {
 // wp_mail( 'your@email.com', 'Automatic email', 'Automatic scheduled email from WordPress.');
}*/