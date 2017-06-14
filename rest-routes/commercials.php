<?php

function smamo_rest_get_commercial_fields($post, $fields = false){

    // Always carry expose and priority
    if ($fields && !isset($fields['expose'])){array_push($fields,'expose');}
    if ($fields && !isset($fields['priority'])){array_push($fields,'priority');}

    // default fields are always included
    $response_array = array(
        'id' => $post->ID,
    );

    // These are included, if no fields array is passed
    if(!$fields){
        $fields = array(
            'link', 'title', 'subtitle', 'img_logo',
            'img_search', 'img_event_calendar', 'img_event_single', 'img_location_single',
            'priority', 'expose', 'expose_limit', 'end_date',
        );
    }

    // get some data or that field aight
    foreach($fields as $field){ if('' !== $field){
        $prfx = 'post_' . $field;

        if('slug' == $field && isset($post->post_name)){
            $response_array[$field] = $post->post_name;
        }

        elseif(isset($post->$field)){
            $response_array[$field] = $post->$field;
        }

        elseif(isset($post->$prfx)){
            $response_array[$field] = $post->$prfx;
        }

        elseif(get_post_meta($post->ID,$field,true)){
            $response_array[$field] = get_post_meta($post->ID,$field,true);
        }
    }}

   return $response_array;
}


// Data handling
function smamo_rest_commercials( $data ) {
    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Main query vars
    $query = array( 'post_type' => 'commercial', 'post_status' => 'publish' );

    // if per page
    $fetch_max = (isset($data['per_page'])) ? esc_attr($data['per_page']) : 9999999;

    // if for
    $fetch_for = (isset($data['for'])) ? esc_attr($data['for']) : false;


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
    $i = 0; foreach($posts as $p){

        // Validate purpose
        if($fetch_for){
            if('search' === $fetch_for && !get_post_meta($p->ID, 'img_search', true)) {continue;}
            if('event_calendar' === $fetch_for && !get_post_meta($p->ID, 'img_event_calendar', true)) {continue;}
            if('event_single' === $fetch_for && !get_post_meta($p->ID, 'img_event_single', true)) {continue;}
            if('location_single' === $fetch_for && !get_post_meta($p->ID, 'img_location_single', true)) {continue;}
        }

        // Update exposure
        $expose = get_post_meta($p->ID, 'expose', true);
        $expose_limit = get_post_meta($p->ID, 'exposelimit', true);

        if($expose < 0){$expose = 0;}

        if($expose_limit && $expose_limit > 0 && $expose > $expose_limit){return;}
        $expose ++;


        $i++; if ($i > $fetch_max) {continue;}
        update_post_meta($p->ID,'expose', $expose);

        $r[] = smamo_rest_get_commercial_fields($p,$fields);
    }

    // Sort $r here before sending
    /* --- */

    /* --- */
    // Did you sort it? K good :-)

    return $r;

}

// Inits rest route
add_action( 'rest_api_init', function () {
    register_rest_route( 'svendborg', 'commercials', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_commercials',
	));
});
