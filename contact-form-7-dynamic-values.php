<?php
/*#########################################################################

	Method : 1 

###########################################################################*/
	
	#  Contact Form With Hidden filed
?>
<label>Title[select select-prefix id:select-prefix class:select-prefix "DR" "MISS" "MR" "MRS" "MS"]</label>

<label> First Name (required)
    [text* first-name] </label>

<label> Last Name (required)
    [text* last-name] </label>

<label> Your Email (required)
    [email* your-email] </label>

<label> Mobile Phone Number
    [tel* mobile-phone-number] </label>

[hidden blog_post_event_name default:shortcode_attr] 
[hidden blog_post_event_date default:shortcode_attr] 
[hidden field_blog_post_time default:shortcode_attr] 
[hidden blog_post_event_venue default:shortcode_attr] 

[submit "Submit"]
<!-- Contact form 7 end -->

<!-- functions.php -->

<?php 
	# Register Shortcode attributes for contact form 7 
add_filter( 'shortcode_atts_wpcf7', 'my_shortcode_atts_wpcf7', 10, 3 );
function my_shortcode_atts_wpcf7( $out, $pairs, $atts ) {
    $my_attributes = array('blog_post_event_name', 'blog_post_event_date', 'field_blog_post_time','blog_post_event_venue');

    foreach ($my_attributes as $value) {
      if ( isset( $atts[$value] ) ) {
          $out[$value] = $atts[$value];
      }
    }
    return $out;
}
?>

<!-- Contact Form in template file -->
<?php
if(isset($_GET["event_ID"]) && !empty($_GET["event_ID"]))
{
	$blog_post_id = $_GET["event_ID"];
	$blog_title = get_the_title($blog_post_id);
	$blog_post_event_name  = get_field('blog_post_event_name',$blog_post_id); 
	$blog_post_event_name = !empty($blog_post_event_name) ? $blog_post_event_name : $blog_title;
	$blog_post_event_date  = get_field('blog_post_event_date',$blog_post_id);
	$field_blog_post_time  = get_field('field_blog_post_time',$blog_post_id);
	$blog_post_event_venue = get_field('blog_post_event_venue',$blog_post_id);
}


/*Do shortcode*/
echo do_shortcode('[contact-form-7 id="2777" title="Event form" blog_post_event_name="'.$blog_post_event_name.'"  blog_post_event_date="'.$blog_post_event_date.'"  field_blog_post_time="'.$field_blog_post_time.'"  blog_post_event_venue="'.$blog_post_event_venue.'"]');?>



<?php

/*#########################################################################

	Method : 2 

###########################################################################*/

	#  Contact Form With Shortcode [event-data]
?>
<!-- Contact form 7 -->
<label>Title[select select-prefix id:select-prefix class:select-prefix "DR" "MISS" "MR" "MRS" "MS"]</label>

<label> First Name (required)
    [text* first-name] </label>

<label> Last Name (required)
    [text* last-name] </label>

<label> Your Email (required)
    [email* your-email] </label>

<label> Mobile Phone Number
    [tel* mobile-phone-number] </label>

			[event-data]  <!-- This is Short code with hidden value -->

[submit "Submit"]
<!-- Contact form 7 end -->

<!-- functions.php -->
<?php
/* Create Short code with dynamic value*/
function event_data_shortcode_callback($params = array()) {
	extract(shortcode_atts(array(
		'cat' => "",
		'number' => 9,
	), $params));
	ob_start(); 
	if(isset($_GET["event_ID"]) && !empty($_GET["event_ID"]))
	{
		$blog_post_id = $_GET["event_ID"];
		$blog_title = get_the_title($blog_post_id);
		$blog_post_event_name  = get_field('blog_post_event_name',$blog_post_id); 
		$blog_post_event_name = !empty($blog_post_event_name) ? $blog_post_event_name : $blog_title;
		$blog_post_event_date  = get_field('blog_post_event_date',$blog_post_id);
		$field_blog_post_time  = get_field('field_blog_post_time',$blog_post_id);
		$blog_post_event_venue = get_field('blog_post_event_venue',$blog_post_id);
	}
	?>
		<input type="hidden" value="<?php echo $blog_post_event_name; ?>" name="event-name"/>
		<input type="hidden" value="<?php echo $blog_post_event_date; ?>" name="event-date"/>
		<input type="hidden" value="<?php echo $field_blog_post_time; ?>" name="event-time"/>
		<input type="hidden" value="<?php echo $blog_post_event_venue; ?>" name="event-venue"/>
	<?php
    
    return ob_get_clean();
}
add_shortcode('event-data', 'event_data_shortcode_callback');

/* do shortcode in contact form 7 */
add_filter( 'wpcf7_form_elements', 'mycustom_wpcf7_form_elements' );
function mycustom_wpcf7_form_elements( $form ) {
	$form = do_shortcode( $form );
	return $form;
}
?>

<!-- Contact Form in template file -->
<?php 
		/*Do shortcode*/
		echo do_shortcode('[contact-form-7 id="2777" title="Event form"]'); ?>



reference links :

https://contactform7.com/getting-default-values-from-shortcode-attributes/
https://wordpress.stackexchange.com/questions/45266/how-to-use-other-shortcodes-inside-contact-form-7-forms
https://wordpress.org/support/topic/multiple-shortcode-attributes-for-contact-form-7/
https://wordpress.stackexchange.com/questions/287893/acf-contact-form-7
