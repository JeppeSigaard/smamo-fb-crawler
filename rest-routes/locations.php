<?php

function smamo_rest_locations( $data ) {

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;
    $orderby = (isset($data['orderby'])) ? esc_attr($data['orderby']) : 'RAND';
    $order = (isset($data['order'])) ? esc_attr($data['order']) : 'ASC';

    // Main query vars
    $query = array(
        'post_type' => 'location',
        'orderby' => $orderby,
        'order' => $order,
    );

    // if per page
    if(isset($data['per_page'])){
        $query['posts_per_page'] = esc_attr($data['per_page']);

        // if page
        if(isset($data['page'])){
            $query['offset'] = esc_attr($data['per_page']) * ( (int)esc_attr($data['page']) - 1 );
        }

    }

    // if category
    if(isset($data['cat'])){

        $cats = explode(',', $data['cat']);

        $query['tax_query'] = array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $cats
            ),

            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $cats
            )
        );

    }

    // Fetch posts
    $posts = get_posts($query);

    // prepare post array
    $r = array();

    // Loop through and apply fields
    foreach($posts as $p){
        if('' !== $p->post_title){
            $r[] = smamo_rest_get_fields($p,$fields);
        }
    }

    return $r;
}

function smamo_rest_location_single( $data ){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // prepare post array
    $r = array();

    // Catch identifier
    $id = esc_attr($data['id']);

    // First try ID
    $post = get_post($id);

    // Then try fbid
    if (!$post || 'location' !== $post->post_type){
        $fbid_query = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_key' => 'fbid',
            'meta_value' => $id,
        ));

        if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
    }

    if ($post && 'publish' === $post->post_status && $post->post_type === 'location' ){

        $r[] = smamo_rest_get_fields($post, $fields);
    }

    return $r;
}

function smamo_rest_update_location($data){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Catch identifier
    $id = esc_attr($data['id']);

    // First try ID
    $post = get_post($id);

    // Then try fbid
    if (!$post || 'location' !== $post->post_type){
        $fbid_query = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_key' => 'fbid',
            'meta_value' => $id,
        ));

        if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
    }

    if ($post && 'publish' === $post->post_status && $post->post_type === 'location' ){

         /// Modtag data
        $post_data = isset($data['data']) ? $data['data'] : array();
        $post_meta = isset($data['meta_data']) ? $data['meta_data'] : array();
        $overwrite = isset($data['overwrite']) ? $data['overwrite'] : false;

        if($post_data){
            $post_data['ID'] = $post->ID;
            wp_update_user($post_data);
        }

        foreach($post_meta as $k => $v){
            if($overwrite){
                update_post_meta($post->ID, $k, $v);
            }

            else{
                add_post_meta($post->ID, $k, $v, false);
            }
        }

        return smamo_rest_get_fields($post, $fields);
    }

    return new WP_Error( 'error', 'Could not update location', array( 'status' => 404 ) );
}

add_action( 'rest_api_init', function () {

    register_rest_route( 'svendborg', 'locations', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_locations',
	) );

    register_rest_route( 'svendborg', 'locations/(?P<id>\d+)', array(
		array(
            'methods' => 'GET',
            'callback' => 'smamo_rest_location_single',
        ),

        array(
            'methods' => 'POST',
            'callback' => 'smamo_rest_update_location',
            'permission_callback' => 'smamo_rest_permission_location',
        ),
	) );

} );
