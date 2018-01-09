<?php

// Smamo rest cities
function smamo_rest_cities( $data ) {

  // Returns city terms
  return get_terms(array(
    'taxonomy' => 'city',
    'hide_empty' => true,
  ));

}

// Inits rest route
add_action( 'rest_api_init', function () {
    register_rest_route( 'v1', 'cities', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_cities',
	));
});
