<?php
class customPostType {

    function __construct() {

        add_action( 'init', array( $this, 'create_post_type' ) );

    }

    function create_post_type() {
     
        $testimonial_labels = array(
				'name'               => _x( 'Testimonials', 'Testimonials name', 'wordpress521' ),
				'singular_name'      => _x( 'Testimonial', 'Testimonials name', 'wordpress521' ),
				'menu_name'          => _x( 'Testimonials', 'admin menu', 'wordpress521' ),
				'name_admin_bar'     => _x( 'Testimonials', 'add new on admin bar', 'wordpress521' ),
				'add_new'            => _x( 'Add New', 'Testimonials', 'wordpress521' ),
				'add_new_item'       => __( 'Add New Testimonial', 'wordpress521' ),
				'new_item'           => __( 'New Testimonial', 'wordpress521' ),
				'edit_item'          => __( 'Edit Testimonial', 'wordpress521' ),
				'view_item'          => __( 'View Testimonial', 'wordpress521' ),
				'all_items'          => __( 'All Testimonials', 'wordpress521' ),
				'search_items'       => __( 'Search Testimonials', 'wordpress521' ),
				'parent_item_colon'  => __( 'Parent Testimonials:', 'wordpress521' ),
				'not_found'          => __( 'No Testimonials found.', 'wordpress521' ),
				'not_found_in_trash' => __( 'No Testimonials found in Trash.', 'wordpress521' )
			);

		$testimonial_args = array(
			'labels'             => $testimonial_labels,
			'description'        => __( 'Description.', 'wordpress521' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'testimonials' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'menu_icon'          => 'dashicons-testimonial'
		);
		register_post_type( 'testimonials', $testimonial_args );



		$services_labels = array(
				'name'               => _x( 'Services', 'Services name', 'wordpress521' ),
				'singular_name'      => _x( 'Services', 'Services name', 'wordpress521' ),
				'menu_name'          => _x( 'Services', 'admin menu', 'wordpress521' ),
				'name_admin_bar'     => _x( 'Services', 'add new on admin bar', 'wordpress521' ),
				'add_new'            => _x( 'Add New', 'Services', 'wordpress521' ),
				'add_new_item'       => __( 'Add New Services', 'wordpress521' ),
				'new_item'           => __( 'New Services', 'wordpress521' ),
				'edit_item'          => __( 'Edit Services', 'wordpress521' ),
				'view_item'          => __( 'View Services', 'wordpress521' ),
				'all_items'          => __( 'All Services', 'wordpress521' ),
				'search_items'       => __( 'Search Services', 'wordpress521' ),
				'parent_item_colon'  => __( 'Parent Services:', 'wordpress521' ),
				'not_found'          => __( 'No Services found.', 'wordpress521' ),
				'not_found_in_trash' => __( 'No Services found in Trash.', 'wordpress521' )
			);

		$services_args = array(
			'labels'             => $services_labels,
			'description'        => __( 'Description.', 'wordpress521' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'services' ),
			/*'with_front' 		 => false,*/
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'menu_icon'          => 'dashicons-yes-alt'
		);
		register_post_type( 'services', $services_args );

		$type_labels = array(
	        'name'               => 'Types',
	        'singular_name'      => 'Type',
	        'search_items'       => 'Search Types',
	        'all_items'          => 'All Types',
	        'parent_item'        => 'Parent Type',
	        'parent_item_colon'  => 'Parent Type:',
	        'update_item'        => 'Update Type',
	        'edit_item'          => 'Edit Type',
	        'add_new_item'       => 'Add New Type', 
	        'new_item_name'      => 'New Type Name',
	        'menu_name'          => 'Types'
	    );
	    
	    //define arguments to be used 
	    $type_args = array(
	        'labels'            => $type_labels,
	        'hierarchical'      => true,
	        'show_ui'           => true,
	        'how_in_nav_menus'  => true,
	        'public'            => true,
	        'show_admin_column' => true,
	        'query_var'         => true,
	        'rewrite'           => array('slug' => 'types'),
	        //'with_front' 		=> false
	    );
	    
	    //call the register_taxonomy function
	    register_taxonomy('types','services', $type_args); 

	    $year_labels = array(
	        'name'               => 'Years',
	        'singular_name'      => 'Year',
	        'search_items'       => 'Search Years',
	        'all_items'          => 'All Years',
	        'parent_item'        => 'Parent Year',
	        'parent_item_colon'  => 'Parent Year:',
	        'update_item'        => 'Update Year',
	        'edit_item'          => 'Edit Year',
	        'add_new_item'       => 'Add New Year', 
	        'new_item_name'      => 'New Year Name',
	        'menu_name'          => 'Years'
	    );
	    
	     $year_args = array(
	        'labels'            => $year_labels,
	        'hierarchical'      => true,
	        'show_ui'           => true,
	        'how_in_nav_menus'  => true,
	        'public'            => true,
	        'show_admin_column' => true,
	        'query_var'         => true,
	         'rewrite'           => array('slug' => 'years'),
			/*'rewrite' 			=> array(
							        'with_front' => false
							    )*/
	    );
	    
	    //call the register_taxonomy function
	    register_taxonomy('years','services', $year_args); 


    }

}

$my_class = new customPostType();


/*function change_link( $post_link, $id = 0 ) {
    $post = get_post( $id );
    if( $post->post_type == 'services' ) {
	    if ( is_object( $post ) ) {
	        $terms_t = wp_get_object_terms( $post->ID, array('types') );
	        if ( $terms_t )
	        {
	        	$post_link = str_replace( '/' . $post->post_type . '/', '/'.$terms_t[0]->slug.'/', $post_link );
	        	$terms_y = wp_get_object_terms( $post->ID, array('year') );
		        if ( $terms_y ) {
		            return str_replace( '%cat%', $terms_y[0]->slug, $post_link );
		        }
	        }
	        
	    }
	}
    return $post_link;
}*/

//add_filter( 'post_type_link', 'change_link', 1, 3 );
//load the template on the new generated URL
/*
function resources_cpt_generating_rule($wp_rewrite) {

add_rewrite_rule( '^service/types/([^/]*)/year/([^/]*)/?$', 'index.php?post_type=service&types=$matches[1]&year=$matches[2]', 'top' );

}*/
//add_filter('generate_rewrite_rules', 'resources_cpt_generating_rule');