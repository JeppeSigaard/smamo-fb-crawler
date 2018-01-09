<?php

// Smamo rest commercials
function smamo_rest_commercials( $data ) {

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

    // Formats data
    $tmp = array(
      'id'    => $post->ID,
      'title' => isset($meta_data['title']) ? $meta_data['title'][0] : null,
      'img'   => isset($meta_data['img'])   ? $meta_data['img'][0]   : null,
      'link'  => isset($meta_data['link'])  ? $meta_data['link'][0]  : null,
      'insta' => isset($meta_data['insta']) ? $meta_data['insta'][0] : null,
      'fburl' => isset($meta_data['fburl']) ? $meta_data['fburl'][0] : null,
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
		'callback' => 'smamo_rest_commercials',
	));
});
