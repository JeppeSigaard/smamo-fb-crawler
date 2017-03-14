<?php

// Location type
add_action( 'init', 'smamo_add_post_type_location' );
function smamo_add_post_type_location() {
	register_post_type( 'location', array(

        'menu_icon' 		 => 'dashicons-location',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'location' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 22,
        'show_in_rest'       => true,
  		'rest_base'          => 'locations',
  		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'supports'           => array( 'title', 'taxonomies'),
        'labels'             => array(
            'name'               => _x( 'Steder', 'post type general name', 'smamo' ),
            'singular_name'      => _x( 'Sted', 'post type singular name', 'smamo' ),
            'menu_name'          => _x( 'Steder', 'admin menu', 'smamo' ),
            'name_admin_bar'     => _x( 'Steder', 'add new on admin bar', 'smamo' ),
            'add_new'            => _x( 'Tilføj nyt ', 'sted', 'smamo' ),
            'add_new_item'       => __( 'Tilføj nyt', 'smamo' ),
            'new_item'           => __( 'Nyt sted', 'smamo' ),
            'edit_item'          => __( 'Rediger', 'smamo' ),
            'view_item'          => __( 'Se sted', 'smamo' ),
            'all_items'          => __( 'Se alle', 'smamo' ),
            'search_items'       => __( 'Find sted', 'smamo' ),
            'parent_item_colon'  => __( 'Forældre:', 'smamo' ),
            'not_found'          => __( 'Start med at oprette et nyt sted.', 'smamo' ),
            'not_found_in_trash' => __( 'Papirkurven er tom.', 'smamo' ),
            ),
	   )
    );
}

// Event type
add_action( 'init', 'smamo_add_post_type_event' );
function smamo_add_post_type_event() {
	register_post_type( 'event', array(

        'menu_icon' 		 => 'dashicons-calendar-alt',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'event' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 22,
        'show_in_rest'       => true,
  		'rest_base'          => 'events',
  		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'supports'           => array( 'title', 'taxonomies'),
        'labels'             => array(
            'name'               => _x( 'Begivenheder', 'post type general name', 'smamo' ),
            'singular_name'      => _x( 'Begivenhed', 'post type singular name', 'smamo' ),
            'menu_name'          => _x( 'Begivenheder', 'admin menu', 'smamo' ),
            'name_admin_bar'     => _x( 'Begivenheder', 'add new on admin bar', 'smamo' ),
            'add_new'            => _x( 'Tilføj ny ', 'begivenhed', 'smamo' ),
            'add_new_item'       => __( 'Tilføj ny', 'smamo' ),
            'new_item'           => __( 'Ny begivenhed', 'smamo' ),
            'edit_item'          => __( 'Rediger', 'smamo' ),
            'view_item'          => __( 'Se begivenhed', 'smamo' ),
            'all_items'          => __( 'Se alle', 'smamo' ),
            'search_items'       => __( 'Find begivenhed', 'smamo' ),
            'parent_item_colon'  => __( 'Forældre:', 'smamo' ),
            'not_found'          => __( 'Start med at oprette en ny begivenhed.', 'smamo' ),
            'not_found_in_trash' => __( 'Papirkurven er tom.', 'smamo' ),
            ),
	   )
    );
}

// Taxonomy (Category) to location
add_action( 'init', 'crawlloc_category_tax' );
function crawlloc_category_tax() {
	$labels = array(
        'label' => __( 'Kategori' ),
        'rewrite' => array( 'slug' => 'category' ),
        'hierarchical' => true,
    );

    register_taxonomy(
		'category',
		'location',
		$labels
	);
}


// Registers meta data to rest api
add_action( 'rest_api_init', function() {
    $location_meta_fields = array(
        'fbid', 'about',
        'adress', 'name',
        'category', 'phone',
        'email', 'website',
        'categories', 'picture',
        'coverphoto'
    );

    $event_meta_fields = array(
        'fbid', 'name',
        'parentname', 'parentid',
        'description', 'start_time',
        'adress', 'imgurl',
        'website', 'phone',
        'ticket_uri', 'images','parentpicture'
    );

    for ($i = 0; $i < sizeof($location_meta_fields); $i++) {
        register_rest_field( 'location', $location_meta_fields[$i], array(
                'get_callback' => function ( $object, $field_name, $request ) {
                    return get_post_meta( $object[ 'id' ], $field_name, true );
                }
            )
        );
    }

    for ($i = 0; $i < sizeof($event_meta_fields); $i++) {
        register_rest_field( 'event', $event_meta_fields[$i], array(
                'get_callback' => function ( $object, $field_name, $request ) {
                    return get_post_meta( $object[ 'id' ], $field_name, true );
                }
            )
        );
    }
});
