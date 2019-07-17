<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$post_per_page = -1;
    $args = array(
    'post_type'     => 'post',  //post type
    'posts_per_page' => $post_per_page,
    'paged' => $paged,
    'orderby' => 'post_title',
    'order' => 'ASC',
);

$loop = new WP_Query($args);
if ($loop->have_posts()) : while($loop->have_posts()) : $loop->the_post(); ?>
        <div class="post-item">
                <div class="post-post-wrap">
                    <?php if ( has_post_thumbnail()) { ?>
                        <div class="post-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('thumb'); ?>
                            </a>
                        </div><!-- thumbnail-->
                    <?php } ?>

                    <div class="loop-content">
                        <a href="<?php the_permalink(); ?>"><h4 class="title"><?php the_title(); ?></h4></a>
                        
                        <p class="excerpt"><?php echo get_the_excerpt(); ?></p>
                    </div>
                    <div class="view-more">
                        <i class="icon-finance-plus"></i><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', '' ); ?></a>
                    </div>
                </div>
            </div>
<?php endwhile; 

if (function_exists("numeric_pagination")) {
    numeric_pagination($loop->max_num_pages);
}
wp_reset_postdata(); endif; 


function numeric_pagination($pages = '', $range = 4)
{  
     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   
 
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}
?>