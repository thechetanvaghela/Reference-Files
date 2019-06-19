<?php
 $args = array(
        'orderby' => 'name',
        'hierarchical' => 1,
        'taxonomy' => 'category',
        'hide_empty' => 1,
        'parent' => 0,
        );
		$categories = get_categories($args);
		?>
		<select id="main_cat">
			<option value="0">-----Select Category------</option>
		<?php
        foreach($categories as $category) {
        	$option .= '<option value="'.$category->cat_ID.'">';
	       		 $option .= $category->name;
	        	$option .= '</option>';
	        	
        } 
        echo $option;
        ?>
                    </select>
	<select id="sub_cat">
	<option value="0">-----Select Sub Cat------</option> 
	</select>
	<select id="get_this_post">
	<option value="0">------Select Post-----</option> 
	</select>
	<div id="post-content-wrapper">
	</div> 

    

    

<?php
add_action("wp_footer","load_my_script"); 
function load_my_script()
{
	?>
	<script type="text/javascript">
jQuery(function($){
            $('#main_cat').change(function(){
                    var $mainCat=$('#main_cat').val();

                    // call ajax
                    console.log(frontend_ajax_object.ajaxurl);
                     $("#sub_cat").empty();
                     $("#get_this_post").empty();
                      $("#post-content-wrapper").html("");
                        $.ajax({
                            url:frontend_ajax_object.ajaxurl,
                            type:'POST',
                             data:'action=get_sub_category&main_catid=' + $mainCat,
                             success:function(results)
                             {
                                //  alert(results);
					                $("#sub_cat").removeAttr("style");
					                $("#sub_cat").append(results);
                             }
                        });
                     }
             );

             $('#sub_cat').change(function(){
                    var $sub_catid=$('#sub_cat').val();

                    // call ajax
                     $("#get_this_post").empty();
                     $("#post-content-wrapper").html("");
                        $.ajax({
                            url:frontend_ajax_object.ajaxurl,
                            type:'POST',
                             data:'action=get_sub_category_posts&sub_catid='+ $sub_catid,
                             success:function(results)
                             {
					           //  alert(results);
					            // $("#sub_cat").removeAttr("style");
					              $("#get_this_post").append(results);
					         }
                        });
                     }
             );

             $('#get_this_post').change(function(){
                    var $get_this_post=$('#get_this_post').val();

                    // call ajax
                     $("#post-content-wrapper").html("");
                        $.ajax({
                            url:frontend_ajax_object.ajaxurl,
                            type:'POST',
                             data:'action=get_page_data&this_post_id='+ $get_this_post,
                             success:function(results)
                             {
					           //  alert(results);
					            $("#post-content-wrapper").html(results);
					         }
                        });
                     }
             );
});
</script>
	<?php
}


add_action('wp_ajax_get_sub_category', 'get_sub_category');
add_action('wp_ajax_nopriv_get_sub_category', 'get_sub_category');
function get_sub_category() {
    if(isset($_POST['main_catid']))
    {
        global $post;

        if(!empty($_POST['main_catid']))
        {
            $args = array('parent' => $_POST['main_catid']);
            $categories = get_categories( $args );
            $option = "<option value='0'>Select sub cat</option>";
            foreach($categories as $category) { 
                $option .= '<option value="'.$category->term_id.'">';
                 $option .= $category->name;
                $option .= '</option>';
                
            }
            echo $option;
            die();
        } // end if
    }
}

add_action('wp_ajax_get_sub_category_posts', 'get_sub_category_posts');
add_action('wp_ajax_nopriv_get_sub_category_posts', 'get_sub_category_posts');
function get_sub_category_posts() {
    if(isset($_POST['sub_catid']))
    {
        global $post;

        if(!empty($_POST['sub_catid']))
        {
            $args = array( 'posts_per_page' => 5, 'offset'=> 0, 'category' => $_POST['sub_catid'] );

            $myposts = get_posts( $args );
            $option = "<option value='0'>Select Post</option>";
            foreach( $myposts as $post ) : setup_postdata($post); 
                 $option .= '<option value="'.get_the_ID().'">';
                 $option .= get_the_title();
                $option .= '</option>';
            endforeach; 
            echo $option;
            die();
        } // end if
    }
}
add_action('wp_ajax_get_page_data', 'get_page_data');
add_action('wp_ajax_nopriv_get_page_data', 'get_page_data');
function get_page_data() {
    if(isset($_POST['this_post_id']))
    {
        global $post;

        if(!empty($_POST['this_post_id']))
        {
            $p_id= $_POST['this_post_id'];
            
            echo '<h1>'.get_the_title($p_id).'</h1>';
            echo '<p class="the_content">'.get_post_field('post_content', $p_id).'</p>';
     
            die();
        } // end if
    }
}