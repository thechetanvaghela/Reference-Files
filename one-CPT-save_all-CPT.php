<?php
 add_action( 'save_post_entertainment',  'post_entertainment_save' ) ;

  function post_entertainment_save( $post_id ) {

  	
  	 $post_type = get_post_type($post_id);
  	$post_title = get_the_title( $post_id );
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
    $imageSRC = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
    $post_content = get_post_field('post_content', $post_id);
   
     // If the post is not "tribe_events", don't create a new post.  
    if ( "entertainment" != $post_type ) 
        return;

	remove_action( 'save_post', 'post_entertainment_save' );

    $movies_post_exists = get_page_by_title( $post_title, $output, "movies" );

    if ( !empty($movies_post_exists) ) {
        // Update post
        $movie_update_post = array(
            'ID'           =>   $movies_post_exists->ID,
            'post_title'   =>   $post_title,
            'post_content' =>   $post_content,
            'post_type'=>'movies', 
        );

        // Update the post into the database
        wp_update_post( $movie_update_post );
        set_post_thumbnail( $movies_post_exists->ID, $post_thumbnail_id );
    }
    else {
    	$movie_post = array(
		  'post_title'=>$post_title, 
		  'post_type'=>'movies', 
		  'post_content'=>$post_content,
		  'post_status' =>  'publish',
		);
        // Create the new post and retrieve the id of the new post
        $movie_post_id = wp_insert_post ( $movie_post );
        // Set the featured image for the new post to the same image as event post 
        set_post_thumbnail( $movie_post_id, $post_thumbnail_id );
    }   

    $song_post_exists = get_page_by_title( $post_title, $output, "songs" );

    if ( !empty($song_post_exists) ) {
        // Update post
        $songs_update_post = array(
            'ID'           =>   $song_post_exists->ID,
            'post_title'   =>   $post_title,
            'post_content' =>   $post_content,
            'post_type'=>'songs', 
        );

        // Update the post into the database
        wp_update_post( $songs_update_post );
        set_post_thumbnail( $song_post_exists->ID, $post_thumbnail_id );
    }
    else {
    	$song_post = array(
		  'post_title'=>$post_title, 
		  'post_type'=>'songs', 
		  'post_content'=>$post_content,
		  'post_status' =>  'publish',
		);
        // Create the new post and retrieve the id of the new post
        $song_post_id = wp_insert_post ( $song_post );
        // Set the featured image for the new post to the same image as event post 
        set_post_thumbnail( $song_post_id, $post_thumbnail_id );
    }  


     $video_post_exists = get_page_by_title( $post_title, $output, "videos" );

    if ( !empty($video_post_exists) ) {
        // Update post
        $videos_update_post = array(
            'ID'           =>   $video_post_exists->ID,
            'post_title'   =>   $post_title,
            'post_content' =>   $post_content,
            'post_type'=>'videos', 
        );

        // Update the post into the database
        wp_update_post( $videos_update_post );
        set_post_thumbnail( $video_post_exists->ID, $post_thumbnail_id );
    }
    else {
    	$video_post = array(
		  'post_title'=>$post_title, 
		  'post_type'=>'videos', 
		  'post_content'=>$post_content,
		  'post_status' =>  'publish',
		);
        // Create the new post and retrieve the id of the new post
        $video_post_id = wp_insert_post ( $video_post );
        // Set the featured image for the new post to the same image as event post 
        set_post_thumbnail( $video_post_id, $post_thumbnail_id );
    }        

    // Now hook the action
    add_action( 'save_post', 'post_entertainment_save' );

  }

  ?>