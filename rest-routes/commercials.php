<?php

// Data handling
function smamo_rest_commercials( $data ) {
    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Main query vars
    $query = array( 'post_type' => 'commercial', 'post_status' => 'publish' );

    // if per page
    if(isset($data['per_page'])){
        $query['posts_per_page'] = esc_attr($data['per_page']);

        // if page
        if(isset($data['page'])){
            $query['offset'] = esc_attr($data['per_page']) * ( (int)esc_attr($data['page']) - 1 );
        }

    }

    // if order
    if(isset($data['orderby'])){
        $query['orderby'] = esc_attr($data['orderby']);
        $query['order'] = (isset($data['order'])) ? esc_attr($data['order']) : 'ASC';
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

// Inits rest route
add_action( 'rest_api_init', function () {
    register_rest_route( 'svendborg', 'commercials', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_commercials',
	));
});
