<?php

// Smamo rest commercials
function towwwn_rest_commercials( $data ) {

  // Fetches all commercials
  $posts = get_posts(array(
    'post_type' => 'commercial',
    'numberposts' => -1,
  ));

  // Generates response
  $response = array( );
  foreach ( $posts as $post ) {

    // Gets meta data
    $meta_data = get_post_meta($post->ID);
    $buttons   = get_post_meta( $post->ID, 'buttons', true );

    // Formats data
    $tmp = array(
      'id' => $post->ID,
      'title' => isset($meta_data['title']) ? $meta_data['title'][0] : null,
      'img' => isset($meta_data['img']) ? $meta_data['img'][0] : null,
      'buttons' => $buttons,
    );

    // Pushes formatted data
    array_push( $response, $tmp );

  }

  // Returns response
  return $response;

}

// Inits rest route
add_action( 'rest_api_init', function () {
    register_rest_route( 'v1', 'commercials', array(
		'methods' => 'GET',
		'callback' => 'towwwn_rest_commercials',
	));
});
