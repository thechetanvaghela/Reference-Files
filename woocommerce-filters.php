<?php
/*
######################################################################################################
# Add Course into cart message
######################################################################################################
*/
add_filter( 'wc_add_to_cart_message', 'course_add_to_cart_message', 10, 2 ); 
function course_add_to_cart_message( $message, $product_id ) { 
 	$corse_data = sensei_get_courses_detail_of_woo_product($product_id);
 	$item_title = get_the_title( $product_id );
 	if($corse_data['get_the_title'])
  	{
  		$item_title = $corse_data['get_the_title'];
  	}
  	$title_added = sprintf( 'Course "%s" has been added to your cart.', $item_title ); 
  	$message  = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View cart', 'woocommerce' ), esc_html( $title_added ) );
 	
 	return $message; 
}
/*
######################################################################################################
# Removed item name to course name into cart message
######################################################################################################
*/
add_filter( 'woocommerce_cart_item_removed_title', 'removed_from_cart_title', 12, 2);
function removed_from_cart_title( $message, $cart_item ) {
    $product_id = $cart_item['product_id'] ;
    $corse_data = sensei_get_courses_detail_of_woo_product($product_id);
    $item_title = get_the_title( $product_id );
 	if($corse_data['get_the_title'])
  	{
  		 $item_title = $corse_data['get_the_title'];
  	}
        $message = sprintf( __('Course "%s"'), $item_title );
    
    return $message;
}

/*
######################################################################################################
# Change return to shop url
######################################################################################################
*/
function wc_empty_cart_redirect_url() {
	return home_url();
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

/*
######################################################################################################
# Change return to shop text
######################################################################################################
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
######################################################################################################
# shop page redirect to home
######################################################################################################
*/
function diversity_shop_page_to_homepage_redirect() {
    if( is_shop() ){
        wp_redirect( home_url() );
        exit();
    }
}
add_action( 'template_redirect', 'diversity_shop_page_to_homepage_redirect' );

/*
######################################################################################################
# check for empty-cart get param to clear the cart
######################################################################################################
*/
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
  global $woocommerce;
	
	if ( isset( $_GET['empty-cart'] ) ) {
		$woocommerce->cart->empty_cart(); 
	}
}
/*
######################################################################################################
#	add item only one time in cart
######################################################################################################
*/
function add_the_date_validation( $valid, $product_id, $quantity ) { 
	//if($woocommerce->cart->cart_contents_count == 0){ return true;}
	global $woocommerce;
	if($woocommerce->cart->cart_contents_count > 0){
		foreach($woocommerce->cart->get_cart() as $key => $val ) {
			$_product = $val['data'];
	 		if($product_id == $_product->id ) {
	 			wc_add_notice( __( 'Already added in cart.', 'woocommerce' ), 'error' );
	    		$valid = false;
				//$url = WC()->cart->get_checkout_url();
				//wp_redirect($url);
				//exit;
			}
		}
	}
	return $valid;
}
add_filter( 'woocommerce_add_to_cart_validation', 'add_the_date_validation', 10, 5 );

/*
######################################################################################################
#	Checkout Fields
######################################################################################################
*/

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

	$fields['billing']['billing_first_name']['placeholder'] = __('First name','diversity'); //Förnamn
	$fields['billing']['billing_last_name']['placeholder'] = __('Last name','diversity'); //Efternamn
	//$fields['billing']['billing_company']['placeholder'] = __('Företagsnamn','diversity');
	$fields['billing']['billing_email']['placeholder'] = __('E-Mail','diversity'); //e-post
	$fields['billing']['billing_phone']['placeholder'] = __('Phone number','diversity');	//Telefonnummer

	 $fields['billing']['billing_users'] = array(
		'type' => 'radio',
		'label' => __('', 'diversity'),
		'required' => true,
		'class' => array('address-field'),
		'clear' => true,
		'options' => array(
			'privateperson' => __('Private Person','diversity'), //Privatperson
			'business' => __('Business','diversity'),
		),
		'default' => 'privateperson'
	);
	 $fields['billing']['billing_company']['placeholder'] = __('Company Name','diversity');//Företagsnamn

	/*label remove*/
	$fields['billing']['billing_first_name']['label'] ='';
	$fields['billing']['billing_last_name']['label'] ='';
	$fields['billing']['billing_company']['label'] ='';
	$fields['billing']['billing_email']['label'] ='';
	$fields['billing']['billing_phone']['label'] ='';



	$fields["billing"]['billing_first_name']["priority"] = 10;
	$fields["billing"]['billing_last_name']["priority"] = 20;
	$fields["billing"]['billing_phone']["priority"] = 30;
	$fields["billing"]['billing_email']["priority"] = 40;
	$fields['billing']['billing_users']["priority"] = 50;
	$fields["billing"]['billing_company']["priority"] = 60;

	return $fields;
}


/*
######################################################################################################
#	add content bellow cart item name
######################################################################################################
*/
add_action('woocommerce_after_cart_item_name', 'woocommerce_after_cart_item_content', 10,2);
function woocommerce_after_cart_item_content( $cart_item, $cart_item_key) {
  if($cart_item['product_id'])
  {
  	$product_id = $cart_item['product_id'];
  	$corse_data = sensei_get_courses_detail_of_woo_product($product_id);
  	if($corse_data['get_the_content'])
  	{
  		echo '<p>'.__($corse_data['get_the_content'], 'sensei-lms').'</p>';
  	}
  }
}

/*
######################################################################################################
#	add image of course instead of product
######################################################################################################
*/
add_filter('woocommerce_cart_item_thumbnail', 'woocommerce_cart_item_thumbnail_callback', 10,3);
function woocommerce_cart_item_thumbnail_callback( $productget_image, $cart_item, $cart_item_key) {
  if($cart_item['product_id'])
  {
  	$product_id = $cart_item['product_id'];
  	$corse_data = sensei_get_courses_detail_of_woo_product($product_id);
  	$class = 'attachment-shop_thumbnail wp-post-image'; // Default cart thumbnail class.
  	if($corse_data['post_thumbnail_id'])
  	{
  		$image_attributes = wp_get_attachment_image_src( $corse_data['post_thumbnail_id']);
  		if(!empty($image_attributes))
  		{
  			$src = $image_attributes[0];
  		} 
	    // Construct your img tag.
	    $item_thumb = '<img';
	    $item_thumb.= ' src="' . $src . '"';
	    $item_thumb .= ' class="' . $class . '"';
	    $item_thumb .= ' />';
	    // Output.
	    return $item_thumb;
  	}
  	else
  	{
  		//$src = get_template_directory_uri()."/images/packgebox-bg.jpg";
  		$src = get_template_directory_uri()."/images/image.png";
  		$item_thumb = '<img';
	    $item_thumb.= ' src="' . $src . '"';
	    $item_thumb .= ' class="' . $class . '"';
	    $item_thumb .= ' />';
	    // Output.
	    return $item_thumb;
  	}
  }
}


/*
######################################################################################################
#	Change product link to course link
######################################################################################################
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
######################################################################################################
#	Change product name to course name
######################################################################################################
*/
add_filter( 'woocommerce_order_item_name', 'woocommerce_cart_item_name_callback',10,3) ;
add_filter( 'woocommerce_cart_item_name', 'woocommerce_cart_item_name_callback',10,3) ;
function woocommerce_cart_item_name_callback( $new_name, $cart_item, $cart_item_key )
{
	if($cart_item['product_id'])
	{
	  	$product_id = $cart_item['product_id'];
	  	$corse_data = sensei_get_courses_detail_of_woo_product($product_id);
	  	if($corse_data['get_the_title'] && $corse_data['get_permalink'])
	  	{
	  		if(is_cart())
	  		{
	  			//$new_name =  $corse_data['get_the_title'];
	  			$new_name = sprintf( '<a href="%s">%s</a>', esc_url($corse_data['get_permalink'] ), $corse_data['get_the_title'] );
	  		}
	  		if(is_checkout())
	  		{
	  			$new_name =  $corse_data['get_the_title'];
	  		}

	  	}
	}
	return $new_name;
}

/*
######################################################################################################
#	remove product-quantity from order review
######################################################################################################
*/
add_filter( 'woocommerce_order_item_quantity_html', 'woocommerce_order_item_quantity_html_callback',10,2) ;
function woocommerce_order_item_quantity_html_callback( $html, $item )
{
	$html = "";
	return $html;
}

/*
######################################################################################################
#	remove Order again from order review
######################################################################################################
*/
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
?>