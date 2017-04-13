<?php

function smamo_rest_events( $data ) {

    // prepare post array
    $r = array();

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Set after and before
    $parent = (isset($data['parent'])) ? esc_attr($data['parent']) : false;
    $after = (isset($data['after'])) ? date('Y-m-d\TH:i:s', strtotime(esc_attr( $data['after'] ))) : false;
    $before = (isset($data['before'])) ? date('Y-m-d\TH:i:s', strtotime(esc_attr( $data['before']))) : false;

    // Main query vars
    $query = array(
        'post_type' => 'event',
        'meta_key' => 'start_time',
        'meta_type' => 'DATETIME',
        'orderby' => 'meta_value',
    );

    // if per page
    if(isset($data['per_page'])){
        $query['posts_per_page'] = esc_attr($data['per_page']);

        // if page
        if(isset($data['page'])){
            $query['offset'] = esc_attr($data['per_page']) * ( (int)esc_attr($data['page']) - 1 );
        }

    }

    // prepare meta query
    $meta_query = array(
        'relation' => 'AND',
    );

    // If parent is set
    if($parent){
        $meta_query[] = array(
            'key' => 'parentid',
            'value' => $parent,
            'compare' => '=',
        );
    }

    // If after is set
    if($after){
        $query['order'] = 'ASC';
        $meta_query[] = array(
            'key' => 'start_time',
            'value' => $after,
            'compare' => '>',
            'type' => 'DATETIME',
        );
    }

    // if before is set
    if($before){
        $query['order'] = 'DESC';
        $meta_query[] = array(
            'key' => 'start_time',
            'value' => $before,
            'compare' => '<',
            'type' => 'DATETIME',
        );
    }

    // if category
    if(isset($data['cat'])){

        $cats = explode(',', $data['cat']);
        $loc_ids = array();
        $locations = get_posts(array(
            'post_type' => 'location',
            'posts_per_page' => -1,
            'tax_query' => array(
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
            ),
        ));

        foreach($locations as $location){
            $loc_ids[] = get_post_meta( $location->ID,'fbud', true);
        }

        $meta_query[] = array(
            'key' => 'parentid',
            'value' => $loc_ids,
            'compare' => 'IN',
        );

    }

    $query['meta_query'] = $meta_query;

    // Fetch posts
    $posts = get_posts($query);

    // Loop through and apply fields
    foreach($posts as $p){

       $r[] = smamo_rest_get_fields($p,$fields);
    }

    return $r;
}

function smamo_rest_event_single( $data ){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // prepare post array
    $r = array();

    // Catch identifier
    $id = esc_attr($data['id']);

    // First try ID
    $post = get_post($id);

    // Then try fbid
    if (!$post || 'event' !== $post->post_type){
        $fbid_query = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_key' => 'fbid',
            'meta_value' => $id,
        ));

        if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
    }

    if ($post && 'publish' === $post->post_status && 'event' === $post->post_type ){

        $r[] = smamo_rest_get_fields($post, $fields);
    }

    return $r;
}

function smamo_rest_update_event($data){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Catch identifier
    $id = esc_attr($data['id']);

    // First try ID
    $post = get_post($id);

    // Then try fbid
    if (!$post || 'event' !== $post->post_type){
        $fbid_query = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_key' => 'fbid',
            'meta_value' => $id,
        ));

        if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
    }

    if ($post && 'publish' === $post->post_status && $post->post_type === 'event' ){

         /// Modtag data
        $post_data = isset($data['data']) ? $data['data'] : array();
        $post_meta = isset($data['meta_data']) ? $data['meta_data'] : array();
        $overwrite = isset($data['overwrite']) ? $data['overwrite'] : true;

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

    return new WP_Error( 'error', 'Could not update event', array( 'status' => 404 ) );
}

add_action( 'rest_api_init', function () {

    register_rest_route( 'svendborg', 'events', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_events',
	) );

    register_rest_route( 'svendborg', 'events/(?P<id>\d+)', array(
		array(
            'methods' => 'GET',
            'callback' => 'smamo_rest_event_single',
        ),

        array(
            'methods' => 'POST',
            'callback' => 'smamo_rest_update_event',
            'permission_callback' => 'smamo_rest_permission_event',
        )
	) );

} );
