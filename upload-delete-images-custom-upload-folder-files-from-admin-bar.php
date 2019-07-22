<?php

/*Create Image using merge two image by url*/
$bg_image_src = "";
$transparent_image_src = "";
$upload_dir = wp_upload_dir();
$upload_basedir = $upload_dir['basedir'];
if (!file_exists($upload_basedir."/temp-images/")) {
  mkdir($upload_basedir."/temp-images/", 0755, true);
}

$image_1 = imagecreatefromstring(file_get_contents($bg_image_src));
$image_2 = imagecreatefrompng($transparent_image_src);

$image_1 = imagescale($image_1, 500,500);
$im_width = imagesx($image_1);
$im_height = imagesy($image_1);

//$image_2 = imagescale($image_2, $im_width/5);
$wt_width = imagesx($image_2);
$wt_height = imagesy($image_2);

imagealphablending($image_2, true);
imagesavealpha($image_2, true);
//imagecopy($image_1, $image_2, 700, 200, 0, 0, 365, 365);
imagecopy($image_1, $image_2, $im_width - $wt_width -50 , $im_height - $wt_height - 100, 0, 0, $wt_width, $wt_height);
header('Content-Type: image/png');

//$upload_basedirs = $upload_dir['path'];
//$image_url = $upload_basedirs.'/merged-'.time().'.jpg';
$image_url = $upload_basedir.'/temp-images/merged-'.time().'.jpg';

imagepng($image_1, $image_url);
//imagepng($image_1);
$wp_filetype = wp_check_filetype(basename($image_url), null );

$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => basename($image_url),
    'post_content' => '',
    'post_status' => 'inherit'
);
$attachment_id = wp_insert_attachment( $attachment, $image_url );
$attach_data = wp_generate_attachment_metadata( $attachment_id, $image_url );
wp_update_attachment_metadata( $attachment_id,  $attach_data );

imagedestroy($image_1);
imagedestroy($image_2);
##############################################################################################
function admin_custom_node($wp_admin_bar){
	$upload_dir = wp_upload_dir();
	$upload_basedir = $upload_dir['basedir'];
	if (file_exists($upload_basedir.'/temp-images/')) {
	$foldersize   = wpfoldersize( $upload_basedir.'/temp-images/' );
	$tempimagesize = wp_folder_format_size( $foldersize );
		if($foldersize)
    	{
    		$args = array(
        	'id' => 'temp-images-node',
        	'title' => 'Purge Temp-images ('.$tempimagesize.')',
        	'href' => '#'        	
    	);
    
    	$wp_admin_bar->add_node($args);
		}
	}
}
add_action('admin_bar_menu', 'admin_custom_node',100);

/* Here you trigger the ajax handler function using jQuery */
add_action( 'admin_footer', 'cache_purge_action_js' );
add_action( 'wp_footer', 'cache_purge_action_js' );
function cache_purge_action_js() { ?>
  <script type="text/javascript" >
    jQuery(document).ready(function($) {
     jQuery("li#wp-admin-bar-temp-images-node .ab-item").on( "click", function() {
        var data = {
                      'action': 'products_temp_images_purge',
                    };
                    
        <?php if(is_admin()) {?>
        /* since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php */
        jQuery.post(ajaxurl, data, function(response) {
           alert( response );
           location.reload();
        });
         <?php }else{?>
             jQuery.post(frontend_ajax_object.ajaxurl, data, function(response) {
           alert( response );
           location.reload();
        });
         <?php } ?>
      });
        });
  </script> <?php
}
/* Here you hook and define ajax handler function */
add_action( 'wp_ajax_products_temp_images_purge', 'products_temp_images_purge_callback' );
add_action( 'wp_ajax_nopriv_products_temp_images_purge', 'products_temp_images_purge_callback' );

function products_temp_images_purge_callback() {
   $upload_dir = wp_upload_dir();
    $upload_basedir = $upload_dir['basedir'];
    $upload_baseurl = $upload_dir['baseurl'];
	if (file_exists($upload_basedir.'/temp-images/')) {
		//The name of the folder.
    $folder = $upload_basedir.'/temp-images/';
		$baseurlfolder = $upload_baseurl.'/temp-images/';
		//Get a list of all of the file names in the folder.
		$files = glob($folder . '/*');
		//Loop through the file list.
		foreach($files as $file){
          $filename = basename($file);
          $baseurlfile = $baseurlfolder.$filename;
          $attachmentid =  attachment_url_to_postid( $baseurlfile );
          if($attachmentid)
          {
            wp_delete_attachment( $attachmentid, true);
          }
		        //unlink($file);
		}
	
	    $response = "Images Purged !";
	}
	echo $response;
	wp_die(); 
   
} 

function wpfoldersize( $path ) 
{
    $total_size = 0;
    $files = scandir( $path );
    $cleanPath = rtrim( $path, '/' ) . '/';

    foreach( $files as $t ) {
        if ( '.' != $t && '..' != $t ) 
        {
            $currentFile = $cleanPath . $t;
            if ( is_dir( $currentFile ) ) 
            {
                $size = wpfoldersize( $currentFile );
                $total_size += $size;
            }
            else 
            {
                $size = filesize( $currentFile );
                $total_size += $size;
            }
        }   
    }

    return $total_size;
}

function wp_folder_format_size($size) 
{
    $units = explode( ' ', 'B KB MB GB TB PB' );

    $mod = 1024;

    for ( $i = 0; $size > $mod; $i++ )
        $size /= $mod;

    $endIndex = strpos( $size, "." ) + 3;

    return substr( $size, 0, $endIndex ) . ' ' . $units[$i];
}