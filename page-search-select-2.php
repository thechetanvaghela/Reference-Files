<?php
function online_select2_css_scripts() {
	wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
	wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
}
//	add_action( 'wp_enqueue_scripts', 'online_select2_css_scripts' );

add_action('wp_ajax_wordpress_page_select_lookup', 'wordpress_page_select_lookup');
add_action('wp_ajax_nopriv_wordpress_page_select_lookup', 'wordpress_page_select_lookup');
function wordpress_page_select_lookup() {
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
    					'post_type' => 'page',
    					'order' => 'ASC',
    					'orderby' => 'title'	    			
    				);

    $ajax_wp_pages = new wp_Query( $default_query );
   
     while ( $ajax_wp_pages -> have_posts() ) {
				$ajax_wp_pages->the_post();
		        $bg_post_title = get_the_title();
		        $bg_id = get_the_id();
		        $get_the_permalink = get_the_permalink();

        $result[] = array(
        				'id' => $get_the_permalink,
        				'title' => $bg_post_title,
        				);

         $set_result = array(
        				'id' => $get_the_permalink,
        				'title' => $bg_post_title,
        				);
    }
     //$most_searched_pages[] = $result;
    if(!empty(get_site_transient('most_searched_pages')) ) 
    {
		$most_searched_pages = get_site_transient('most_searched_pages');
		if(array_search($set_result['id'], array_column($most_searched_pages, 'id')) == false) {
			 $most_searched_pages[] = $set_result;
		}
		
	}
	else
	{
		$most_searched_pages[] = $set_result;
	}
  	set_site_transient('most_searched_pages', $most_searched_pages , MONTH_IN_SECONDS  );
 	

    echo json_encode($result);

    die();
}

add_action("wp_footer","load_my_script"); 
function load_my_script()
{
	?>
	<script type="text/javascript">
jQuery(function($){
          

             if ($(".wp-page-search").length > 0)
            {
                $('.wp-page-search').select2( {
                    placeholder: 'Select a Page',
                    multiple: false,
                    minimumInputLength: 3,
                    ajax: {
                        url: frontend_ajax_object.ajaxurl,
                        dataType: 'json',
                        data: function (term, page) {
                            return {
                                q: term,
                                action: 'wordpress_page_select_lookup',
                                post_type: 'page',
                                //background_post_select_field_id: $(this).attr('data-s2ps-post-select-field-id')
                            };
                        },
                        processResults: function (data) {                               
                            var items=[];
                            var newItem=null;

                            for (var thisId in data) {
                                newItem = {
                                    'id': data[thisId]['id'],
                                    'text': data[thisId]['title']
                                };
                                items.push(newItem);
                            }
                            return { results: items };
                          },
                          cache: true
                    },
                        
                });
                $(document).on('change', '.wp-page-search', function (e) {
                    var val = $(this).val();
                    window.location.href =  val;
                });
    }
});
</script>
?>
<select class="wp-page-search" style="width: 50%;" id="wp_pages" name="wp_pages" data-placeholder="<?php esc_attr_e( 'Search for a Page', '' ); ?>" data-post-type="page">
        <?php 
        if(!empty(get_site_transient('most_searched_pages')) ) 
        {
            $most_searched_pages = get_site_transient('most_searched_pages'); 
           
            foreach ($most_searched_pages as $key => $value) {
                echo '<option value="'.$value['id'].'">'.$value['title'].'</option>';               
            }
        }
        ?>
    </select>