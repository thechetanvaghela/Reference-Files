<?php 
/**
 * Plugin Name: WP Column shortcode
 * Plugin URI: #
 * Description: Add shortcode to post column
 * Version: 1.0
 * Author: Chetan Vaghela
 * Author URI: https://github.com/thechetanvaghela
 * License:	GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 */


function cv_add_column_to_admin_dashboard($column_title, $post_type, $cb){
    # Column 
    add_filter( 'manage_' . $post_type . '_posts_columns', function($columns) use ($column_title) {
        $columns[ sanitize_title($column_title) ] = $column_title;
        return $columns;
    } );
    # Column Content
    add_action( 'manage_' . $post_type . '_posts_custom_column' , function( $column, $post_id ) use ($column_title, $cb) {
        if(sanitize_title($column_title) === $column){
            $cb($post_id);
        }
    }, 10, 2 );
}

# column for shortcode
cv_add_column_to_admin_dashboard(__('Shortcode'), 'page', function($post_id){
    echo '<code>';
    echo '[logo_showcase id="'.$post_id.'"]';
    echo '</code>';
});

# column for template shortcode
cv_add_column_to_admin_dashboard(__('Template Shortcode'), 'page', function($post_id){
    $code = '<?php echo do_shortcode("[logo_showcase id="'.$post_id.'"]"); ?>';
    echo '<code>'.htmlspecialchars($code).'</code>';
});

# shortcode
add_shortcode('logo_showcase','logo_showcase_callback');
function logo_showcase_callback($atts)
{
    $html = '';
    $atts = shortcode_atts( array(
        'id' => '0',
    ), $atts, 'default' );
    
    $id = $atts['id'];
    $html .= get_the_title($id);

    $CUSTOM_FIELD = get_post_meta($id,'CUSTOM_FIELD',true);
    return $html;
}