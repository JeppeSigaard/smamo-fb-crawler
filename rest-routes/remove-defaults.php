<?php

remove_action( 'rest_api_init', 'create_initial_rest_routes', 99 );
add_filter('rest_endpoints', function($endpoints) {
    foreach(array(
        '/oembed/1.0',
        '/oembed/1.0/embed'
    ) as $endpoint){
        if ( isset( $endpoints[$endpoint] ) ) {
            unset( $endpoints[$endpoint] );
        }
    }
    return $endpoints;
});
