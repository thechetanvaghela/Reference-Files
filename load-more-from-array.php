
<!-- scripts.js -->
<script type="text/javascript">
/* Load more logo script start*/
jQuery(document).ready(function() {
    jQuery('.load-more-btn-wrap .load-more-logo').on('click', function () {
        var number = jQuery(this).attr('data-number');
        var this_link = jQuery(this);
        jQuery.ajax({
          type:'post',
          dataType:'json',
          url: frontent_ajax_object.ajax_url,
          data:{
            'action': 'load_more_logo',
            'page_id': frontent_ajax_object.page_id,
            'number': number,
          },
          beforeSend: function(){
            jQuery('.product-line-logos-wrap').append("<div class='loader'></div>");
          },
          success: function(response){
              jQuery('.product-line-logos-wrap').find(".loader").remove();
              if(response.success)
              {
                  jQuery('.product-line-logos-wrap').append(response.html);
                  this_link.attr('data-number',parseInt(number)+parseInt(1));
                  if(response.remove_load_more)
                  {
                      jQuery('.load-more-btn-wrap').remove();
                  }
              }
              else
              {
                  jQuery('.load-more-btn-wrap').remove();
              }
          }
        }); 
      });
});
/* Load more logo script over*/
</script>

<?php
/* Enqueue script*/
if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

function enqueue_scripts() {
	wp_enqueue_script('scripts-js', get_template_directory_uri() . '/assets/js/scripts.js', array(), _S_VERSION, true);
	wp_localize_script('scripts-js', 'frontent_ajax_object', array(
		'ajax_url' => admin_url('admin-ajax.php'), 
		'page_id' => get_the_ID(),
	));
}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );


/* ajax function */
# define ajax action and function for load more logo
add_action('wp_ajax_load_more_logo','load_more_logo_callback');
add_action('wp_ajax_nopriv_load_more_logo','load_more_logo_callback');
# load more logo callback function start
function load_more_logo_callback()
{
	$page_id = $_POST['page_id'];
	$number = isset($_POST['number']) ? $_POST['number'] : 0;
	$product_line_section = get_field("product_line_section",$page_id);
	$product_line_logos = isset($product_line_section["product_line_logos"]) ? $product_line_section["product_line_logos"] : '';   
	$default_show_logo = isset($product_line_section["default_show_logo"]) ? $product_line_section["default_show_logo"] : 18;
	$load_more_item = isset($product_line_section["load_more_item"]) ? $product_line_section["load_more_item"] : 12;

	$success = $remove_load_more = false;
    $html = '';
	if(!empty($product_line_logos))
  	{
     	$loaded_item = $load_more_item * $number;
     	$remove_logos = $default_show_logo + $loaded_item;
       	$product_logos = array_slice($product_line_logos, $remove_logos,$load_more_item);
       	$load_new = count($product_logos);
       	$html = '';
       	if(!empty($product_logos))
       	{
       		$success = true;
       		foreach ($product_logos as $key => $product_line_logo) 
            {
               $product_logo = $product_line_logo['product_line_logo'];
     
               if(!empty($product_logo))
               {
                  
                  $html .= '<div class="col-lg-2 col-md-3 col-sm-4 col-6">';
                  $html .= '<div class="partners-img">';
                  $html .= getlazyload_img_wh($product_logo,'banner-img blur-up','170','40');
                  $html .= '</div>';
                  $html .= '</div>';
                }
            }

            if($load_more_item > $load_new)
            {
            	$remove_load_more = true;
            }
       	}
       	else
       	{
       		$success = false;
       		$html = '';
       	}
  	}
	wp_send_json(array('success'=> $success,'remove_load_more'=> $remove_load_more, 'html'=>$html ));
}
# load more logo callback function Over


/*
Template File
*/
$product_line_section = get_field("product_line_section");
if(!empty($product_line_section))
{
  $title = isset($product_line_section["title"]) ? $product_line_section["title"] : ''; 
  $content = isset($product_line_section["content"]) ? $product_line_section["content"] : ''; 
  $product_line_logos = isset($product_line_section["product_line_logos"]) ? $product_line_section["product_line_logos"] : '';
  $load_more_lable = isset($product_line_section["load_more_lable"]) ? $product_line_section["load_more_lable"] : 'Load More';
  $default_show_logo = isset($product_line_section["default_show_logo"]) ? $product_line_section["default_show_logo"] : 18;
  $load_more_item = isset($product_line_section["load_more_item"]) ? $product_line_section["load_more_item"] : 12;
  ?>
  <section class="part-categories-section common-space">
     <div class="container container-min">
        <div class="row">
           <?php
           # Title
           if(!empty($title))
           {?>
              <div class="col-lg-12">
                 <div class="page-title">
                    <h2 class="a2-title"><?php _e( $title, 'tidewater' ); ?></h2>
                 </div>
              </div><?php
           }
           # Content
           if(!empty($content))
           {?>
              <div class="col-lg-12 part-content">
                 <?php echo $content; ?>
              </div><?php
           }
           # Logo Section
           if(!empty($product_line_logos))
           {
              $total_logos = count($product_line_logos);
              if($total_logos > $default_show_logo)
              {
                 $product_line_logos = array_slice($product_line_logos, 0,$default_show_logo);
              }
              ?>
              <div class="product-line-logos-wrap row">
                 <?php
                 foreach ($product_line_logos as $key => $product_line_logo) 
                 {
                    $product_logo = $product_line_logo['product_line_logo'];
                    if(!empty($product_logo))
                    {
                       ?>
                       <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                          <div class="partners-img">
                             <?php echo getlazyload_img_wh($product_logo,'banner-img blur-up','180','40'); ?>
                          </div>
                       </div>
                       <?php
                       }
                    }
                    ?>
              </div>
              <?php
              if($total_logos > $default_show_logo)
              {?>
                 <div class="col-lg-12 text-center load-more-btn-wrap">
                    <a href="javascript:void(0);" class="btn-gray btn-margin load-more-logo" data-number="0"><span><?php _e($load_more_lable,'tidewater'); ?></span><span class="btn-arrow icon-right-arrow"></span></a>
                 </div>
                 <?php  
              }
           }
           ?>
        </div>
     </div>
  </section>
  <?php
}
?>