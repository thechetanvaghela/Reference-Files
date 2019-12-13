<?php
# enqueue jQuery UI js and css for popup
function ttp_scripts() {
    wp_enqueue_style( 'wp-jquery-ui-dialog' );
    wp_enqueue_script('jquery-ui-dialog');
}
add_action('wp_enqueue_scripts', 'ttp_scripts');
/**
 * Processing before the checkout form to:
 * 1. Hide the existing Click here link at the top of the page.
 * 2. Instantiate the jQuery dialog with contents of 
 *    form.checkout_coupon which is in checkout/form-coupon.php.
 * 3. Bind the Click here link to toggle the dialog.
 **/
function ttp_wc_show_coupon_js() {
    /* Hide the Have a coupon? Click here to enter your code section                                                
     * Note that this flashes when the page loads and then disappears.                                                
     * Alternatively, you can add a filter on                                                                       
     * woocommerce_checkout_coupon_message to remove the html. */
    wc_enqueue_js('$("a.showcoupon").parent().hide();');

    /* Use jQuery UI's dialog feature to load the coupon html code                                                  
     * into the anchor div. The width controls the width of the                                                     
     * modal dialog window. Check here for all the dialog options:                                                         
     * http://api.jqueryui.com/dialog/ */
    wc_enqueue_js('dialog = $("form.checkout_coupon").dialog({                                                      
                       autoOpen: false,                                                                             
                       width: 500,                                                                                  
                       minHeight: 0,                                                                                
                       modal: false,                                                                                
                       appendTo: "#coupon-anchor",                                                                  
                       position: { my: "left", at: "left", of: "#coupon-anchor"},                                   
                       draggable: false,                                                                            
                       resizable: false,                                                                            
                       dialogClass: "coupon-special",                                                               
                       closeText: "Close",                                                                          
                       buttons: {}});');

    /* Bind the Click here to enter coupon link to load the                                                         
     * jQuery dialog with the coupon code. Note that this                                                               
     * implementation is a toggle. Click on the link again                                                          
     * and the coupon field will be hidden. This works in                                                           
     * conjunction with the hidden close button in the                                                               
     * optional CSS in style.css shown below. */
    wc_enqueue_js('$("#show-coupon-form").click( function() {                                                       
                       if (dialog.dialog("isOpen")) {                                                               
                           $(".checkout_coupon").hide();                                                            
                           dialog.dialog( "close" );                                                                
                       } else {                                                                                     
                           $(".checkout_coupon").show();                                                            
                           dialog.dialog( "open" );                                                                 
                       }                                                                                            
                       return false;});');
}
add_action('woocommerce_before_checkout_form', 'ttp_wc_show_coupon_js', 10);

/**                                                                                                                 
 * Show a coupon link below the place order section.                                                                                                      
 * This is the 'coupon-anchor' div which the modal dialog
 * window will attach to.
 **/
function ttp_wc_show_coupon() {
    global $woocommerce;

    if ($woocommerce->cart->needs_payment()) {
        echo '<p style="padding-bottom: 5px;"> Have a coupon? <a href="#" id="show-coupon-form">Click here to enter your code</a>.</p><div id="coupon-anchor"></div>';
    }
}
# use woocommerce_review_order_after_payment for place link below place order button
add_action('woocommerce_review_order_after_payment', 'ttp_wc_show_coupon', 10);

#https://tamstradingpost.com/move-the-woocommerce-coupon-field-anywhere/
?>