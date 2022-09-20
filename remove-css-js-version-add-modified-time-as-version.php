<?php
# remove default version from js and css file
# add last modified time as vesion of file
 function cv_remove_wp_ver_css_js( $src ) {
 
     if ( strpos( $src, 'ver=' ))
     {
         $path = parse_url($src, PHP_URL_PATH);
         $slug_abs_path = $_SERVER['DOCUMENT_ROOT'] . $path;
         if (file_exists($slug_abs_path)) {
             $filetime = filemtime($slug_abs_path);
             $src = remove_query_arg( 'ver', $src );
             $src = add_query_arg( array('ver' => $filetime), $src );
         }
     }
     return $src;
 }
 add_filter( 'style_loader_src', 'cv_remove_wp_ver_css_js', 9999 );
 add_filter( 'script_loader_src', 'cv_remove_wp_ver_css_js', 9999 );

 ?>