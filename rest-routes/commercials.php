<?php

function smamo_rest_get_commercial_fields($post, $fields = false, $fetch_for = 'search'){

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
            'priority', 'expose', 'expose_limit', 'end_date',
        );
    }

    if('search' === $fetch_for){
        $response_array['img_search'] = get_post_meta($post->ID,'img_search', true);
    }
    else{
        $response_array['img_' . $fetch_for] = get_post_meta($post->ID,'img_event_calendar', true);
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

    $fields = array('link', 'title', 'subtitle', 'img_logo');

    // Main query vars
    $query = array( 'post_type' => 'commercial', 'post_status' => 'publish', 'posts_per_page' => -1 );


    // if per page
    $fetch_max = (isset($data['per_page'])) ? esc_attr($data['per_page']) : 9999999;

    // if for
    $fetch_for = (isset($data['for'])) ? esc_attr($data['for']) : false;
    if(!$fetch_for){return(array());} // Lets not allow non-for, for now ;-)


    // Order by exposure, then RAND
    $query['meta_key'] = 'expose';
    $query['orderby'] = 'meta_value_num';
    $query['order'] = 'ASC';


    // Fetch posts
    $posts = get_posts($query);

    // prepare post array
    $r = array();

    // Loop through, validate and apply fields
    $i = 0; foreach($posts as $p){

        // Validate purpose
        $for = get_post_meta($p->ID,'for', false);
        if(!in_array($fetch_for,$for)){continue;}

        // Validate start date
        $start_date = get_post_meta($p->ID,'end_date', true);
        if($start_date && strtotime($start_date) > strtotime('now')){continue;}

        // Validate end date
        $end_date = get_post_meta($p->ID,'end_date', true);
        if($end_date && strtotime($end_date) < strtotime('now')){continue;}

        // Update exposure
        $expose = get_post_meta($p->ID, 'expose', true);
        $co_expose = get_post_meta($p->ID, 'co_expose', true);
        $expose_limit = get_post_meta($p->ID, 'expose_limit', true);

        if($expose < 0){$expose = 0;}
        if($co_expose < 0){$co_expose = 0;}

        if($expose_limit && $expose_limit > 0 && $expose > $expose_limit){continue;}

        // Update co-exposure
        $co_expose ++;
        update_post_meta($p->ID,'co_expose', $co_expose);

        // update exposure for first
        if($i === 0){
            $expose ++;
            update_post_meta($p->ID,'expose', $expose);
        }

        $i++; if ($i > $fetch_max) {continue;}
        $r[] = smamo_rest_get_commercial_fields($p, $fields, $fetch_for);
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
