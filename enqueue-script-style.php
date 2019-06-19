<?php
function online_shop_css_scripts() {
	// Theme custom stylesheet.
	wp_enqueue_style( 'onlineshop-custom-style', get_template_directory_uri() . '/assets/css/custom-style.css','online-shop-style-css', '20181230' );

	if(is_page('login-register'))
	{
		wp_enqueue_style( 'onlineshop-bootstrap-style', get_template_directory_uri() . '/assets/css/bootstrap.min.css','online-shop-style-css', '20181230' );
		wp_enqueue_script( 'onlineshop-bootstrap-script', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array( 'jquery' ), '20181230', true );
	}
	wp_enqueue_style( 'onlineshop-toastr-style', get_template_directory_uri() . '/assets/css/toastr.css','online-shop-style-css', '20181230' );
	wp_enqueue_script( 'onlineshop-toastr-script', get_template_directory_uri() . '/assets/js/toastr.min.js', array( 'jquery' ), '20181230', true );
	

	wp_enqueue_script( 'onlineshop-script', get_template_directory_uri() . '/assets/js/login-register.js', array( 'jquery' ), '20181230', true );

	wp_localize_script('onlineshop-script','frontend_ajax_object',array('ajaxurl' => admin_url( 'admin-ajax.php' ),));

}
add_action( 'wp_enqueue_scripts', 'online_shop_css_scripts' );

?>