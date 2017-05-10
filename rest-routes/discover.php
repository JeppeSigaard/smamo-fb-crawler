<?php

function smamo_rest_discover($data){
    $term = (isset($data['term'])) ? urldecode(esc_attr($data['term'])) : false;
    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;
    $post_types = (isset($data['type'])) ? explode(',', $data['type']) : array('location', 'event');
    $orderby = (isset($data['orderby'])) ? esc_attr($data['orderby']) : 'RAND';
    $order = (isset($data['order'])) ? esc_attr($data['order']) : 'ASC';
    $per_page = (isset($data['per_page'])) ? esc_attr($data['per_page']) : '100';
    $page = (isset($data['page'])) ? esc_attr($data['page']) : '1';
    $after = (isset($data['after'])) ? strtotime( esc_attr( $data['after'] )) : false;
    $before = (isset($data['before'])) ? strtotime( esc_attr( $data['before'] )) : false;

    $r = array();
    if ($term){
        $meta_query = array(
            'relation' => 'or',
            array(
                'key' => 'description',
                'value' => $term,
                'compare' => 'LIKE',
            ),

            array(
                'key' => 'about',
                'value' => $term,
                'compare' => 'LIKE',
            ),

            array(
                'key' => 'parentname',
                'value' => $term,
                'compare' => 'LIKE',
            ),

            array(
                'key' => 'title',
                'value' => $term,
                'compare' => 'LIKE',
            ),

        );

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

        $query = get_posts(array(
            'post_type' => $post_types,
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => $per_page,
            'page' => $page,
            'meta_query' => $meta_query,
        ));


        foreach($query as $p){


            $r[] = smamo_rest_get_fields($p, $fields);
        }

    }
    return $r;

}


add_action( 'rest_api_init', function () {

    register_rest_route( 'svendborg', 'discover', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_discover',
	) );

    register_rest_route( 'svendborg', 'discover/(?<term>(.*)+)', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_discover',
	) );

} );
