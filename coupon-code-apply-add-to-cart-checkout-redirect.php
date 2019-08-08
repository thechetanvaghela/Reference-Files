<?php 
/*
# Coupon Code
*/
add_action('wp_footer', 'dd_custom_wc_button_script');
function dd_custom_wc_button_script() {

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
?>