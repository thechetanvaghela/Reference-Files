<?php
/**
*  ADD OPTION IN SETTING PAGE
**/

add_action('admin_init', 'general_section_adding');  
function general_section_adding() {  
    add_settings_section(  
        'general_settings_section', // Section ID 
        '<hr>Per Page Display', // Section Title
        'general_section_options_callback', // Callback
        'general' // What Page?  This makes the section show up on the General Settings Page
    );

     add_settings_section(  
        'writing_settings_section', // Section ID 
        '<hr>Per Page Display', // Section Title
        'writing_section_options_callback', // Callback
        'writing' // What Page?  This makes the section show up on the General Settings Page
    );

    add_settings_field( // Option 1
        'testimonials_display_count', // Option ID
        'Testimonial Per Page Display', // Label
        'general_section_textbox_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'general_settings_section', // Name of our section
        array( // The $args
            'testimonials_display_count' // Should match Option ID
        )  
    ); 

    add_settings_field( // Option 2
        'services_display_count', // Option ID
        'Services Per Page Display', // Label
        'general_section_textbox_callback', // !important - This is where the args go!
        'general', // Page it will be displayed
        'general_settings_section', // Name of our section (General Settings)
        array( // The $args
            'services_display_count' // Should match Option ID
        )  
    ); 

    add_settings_field( // Option 3
        'post_display_count', // Option ID
        'Post Per Page Display', // Label
        'writing_section_textbox_callback', // !important - This is where the args go!
        'writing', // Page it will be displayed
        'writing_settings_section', // Name of our section (General Settings)
        array( // The $args
            'post_display_count' // Should match Option ID
        )  
    ); 

    register_setting('general','testimonials_display_count', 'esc_attr');
    register_setting('general','services_display_count', 'esc_attr');
    register_setting('writing','post_display_count', 'esc_attr');
}

function general_section_options_callback() { // Section Callback
    echo '<p>Insert a Number to Display Post per page</p>';  
}
function general_section_textbox_callback($args) {  // Textbox Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
}


function writing_section_options_callback() { // Section Callback
    echo '<p>Insert a Number to Display Post per page</p>';  
}
function writing_section_textbox_callback($args) {  // Textbox Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
}
?>