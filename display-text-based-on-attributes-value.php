<?php
/*
# Display text based on change attribute value in product page
*/

// Set the defined product attribute taxonomy
function vendor_defined_taxonomy() {
    // The targeted product attribute taxonomy
    return 'pa_color'; 
}

// Display the vendors on product meta
add_action( 'woocommerce_product_meta_end', 'display_product_vendors', 10 );
function display_product_vendors() {
    $taxonomy = vendor_defined_taxonomy();
    $term_ids = wp_get_post_terms( get_the_ID(), $taxonomy, array('fields' => 'ids') );
    if( sizeof($term_ids) > 0 ){ 
        echo '<span class="posted_in vendors"></span>';
    }
}

// Display the selected variation vendor in a hidden imput field
add_filter( 'woocommerce_available_variation', 'selected_variation_vendor_value', 10, 3 );
function selected_variation_vendor_value( $data, $product, $variation ) {
    $taxonomy = vendor_defined_taxonomy();

    if( isset($data['attributes']['attribute_'.$taxonomy]) )
        $term = get_term_by( 'slug', $data['attributes']['attribute_'.$taxonomy], $taxonomy );

    if( isset($term) && is_a($term, 'WP_Term' ) )
        $data['variation_description'] .= '<input type="hidden" name="vendor-hidden" id="vendor-hidden" value="'.$term->name.'">';

    return $data;
}

// Replace the vendors on product meta by the selected variation vendor
add_action('woocommerce_after_variations_form', 'custom_product_jquery_script');
function custom_product_jquery_script() {
    global $product;

    $taxonomy     = vendor_defined_taxonomy();
    $terms_string = $product->get_attribute($taxonomy);

    if( ! empty($terms_string) ) :
    ?>
    <script type="text/javascript">
    jQuery(function($) {
        var form = 'form.variations_form',       selected = 'input[name="variation_id"]',
            vendorVal = 'input#vendor-hidden',   vendorTarget = 'span.vendors',
            vendorHtml = $(vendorTarget).text(), vendorLabel = '';

        // On variation select
        $(form).on( 'blur', 'select', function() {
            if($(selected).val() != ''){
            	$(vendorTarget).text("");
            	if($(vendorVal).val() == 'Green'){
                	//$(vendorTarget).text(vendorLabel+' '+$(vendorVal).val());
                	$(vendorTarget).text("here is your text");
            	}
            } 
        });
    });
    </script>
    <?php
    endif;
}
?>