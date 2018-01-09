<?php
function smamo_future_events( $data ) {
	$time = date('Y-m-d\TH:i:s');
    $posts = get_posts( array(
        'post_type' => 'event',
		'posts_per_page' => $data['per_page'],

        'meta_key' => 'start_time',
        'meta_type' => 'DATETIME',
        'orderby' => 'meta_value',
        'order' => 'ASC',

        'meta_query' => array(
            array(
                'key' => 'start_time',
                'value' => $time,
                'compare' => '>',
                'type' => 'DATETIME',
            ),
        ),
	) );

    $r = array();
    foreach($posts as $p){
        $r[] = array(
            'id' => $p->ID,
            'date' => $p->post_date,
            'date_gmt' => $p->post_date_gmt,
            'title' => $p->post_title,
            'slug' => $p->name,
            'start_time' => get_post_meta($p->ID, 'start_time', true),
            'fbid' => get_post_meta($p->ID, 'fbid', true),
            'parentname' => get_post_meta($p->ID, 'parentname', true),
            'parentid' => get_post_meta($p->ID, 'parentid', true),
            'name' => get_post_meta($p->ID, 'name', true),
            'description' => get_post_meta($p->ID, 'description', true),
            'adress' => get_post_meta($p->ID, 'adress', true),
            'phone' => get_post_meta($p->ID, 'phone', true),
            'imgurl' => get_post_meta($p->ID, 'imgurl', true),
            'ticket_uri' => get_post_meta($p->ID, 'ticket_uri', true),
            'website' => get_post_meta($p->ID, 'website', true),
            'images' => get_post_meta($p->ID, 'images', true),
        );
    }

    return $r;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'wp/v2', 'events/upcoming/(?P<per_page>\d+)', array(
		'methods' => 'GET',
		'callback' => 'smamo_future_events',
	) );
} );
