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

// Commercial Post type
add_action( 'init', 'smamo_add_post_type_commercial' );
function smamo_add_post_type_commercial() {
	register_post_type( 'commercial', array(

        'menu_icon' 		 => 'dashicons-format-image',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'commercial' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 22,
		'supports'           => array( 'title', 'thumbnail'),
        'labels'             => array(
            'name'               => _x( 'Reklamer', 'post type general name', 'smamo' ),
            'singular_name'      => _x( 'Reklame', 'post type singular name', 'smamo' ),
            'menu_name'          => _x( 'Reklamer', 'admin menu', 'smamo' ),
            'name_admin_bar'     => _x( 'Reklamer', 'add new on admin bar', 'smamo' ),
            'add_new'            => _x( 'Tilføj ny ', 'sted', 'smamo' ),
            'add_new_item'       => __( 'Tilføj ny', 'smamo' ),
            'new_item'           => __( 'Ny reklame', 'smamo' ),
            'edit_item'          => __( 'Rediger', 'smamo' ),
            'view_item'          => __( 'Se reklme', 'smamo' ),
            'all_items'          => __( 'Se alle', 'smamo' ),
            'search_items'       => __( 'Find reklame', 'smamo' ),
            'parent_item_colon'  => __( 'Forældre:', 'smamo' ),
            'not_found'          => __( 'Start med at oprette en ny reklme.', 'smamo' ),
            'not_found_in_trash' => __( 'Papirkurven er tom.', 'smamo' ),
            ),
	   )
    );
}

// Client access post type
add_action( 'init', 'smamo_add_post_type_access' );
function smamo_add_post_type_access() {
	register_post_type( 'application', array(

        'menu_icon' 		 => 'dashicons-lock',
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'application' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 22,
		'supports'           => array( 'title'),
        'labels'             => array(
            'name'               => _x( 'Apps', 'post type general name', 'smamo' ),
            'singular_name'      => _x( 'App', 'post type singular name', 'smamo' ),
            'menu_name'          => _x( 'Apps', 'admin menu', 'smamo' ),
            'name_admin_bar'     => _x( 'Apps', 'add new on admin bar', 'smamo' ),
            'add_new'            => _x( 'Tilføj ny ', 'app', 'smamo' ),
            'add_new_item'       => __( 'Tilføj ny', 'smamo' ),
            'new_item'           => __( 'Ny app', 'smamo' ),
            'edit_item'          => __( 'Rediger', 'smamo' ),
            'view_item'          => __( 'Se app', 'smamo' ),
            'all_items'          => __( 'Se alle', 'smamo' ),
            'search_items'       => __( 'Find app', 'smamo' ),
            'parent_item_colon'  => __( 'Forældre:', 'smamo' ),
            'not_found'          => __( 'Start med at oprette en ny app.', 'smamo' ),
            'not_found_in_trash' => __( 'Papirkurven er tom.', 'smamo' ),
            ),
	   )
    );
}

// Automatically assign id and secret to apps on save/update
add_action('save_post',function($id){
    if('application' === get_post_type($id)){
        update_post_meta($id,'client_id', wp_generate_password(24, false));
        update_post_meta($id,'client_secret', wp_generate_password(48, false));
    }
});

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
