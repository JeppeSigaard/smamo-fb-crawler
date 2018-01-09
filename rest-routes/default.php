<?php
/*
Gets default app information,
ie. event, location & category count. etc.
*/

// Function
function smamo_rest_default_data( $data ) {

  // Extracts data
  // Event count
  $future_event_count = count( get_posts( array(

    'post_type' => 'event',
    'meta_key' => 'start_time',
    'meta_type' => 'DATETIME',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'numberposts' => -1,

    // ONLY FUTURE:
    'meta_query' => array(
      'key' => 'start_time',
      'value' => date('Y-m-d\TH:i:s'),
      'compare' => '>',
      'type' => 'DATETIME',
    )

  )));

  // Place count
  $place_count = count( get_posts( array(
    'post_type' => 'location',
    'numberposts' => -1,
  )));

  // Response composition & returns
  return array(
    'future_event_count' => $future_event_count,
    'place_count' => $place_count,
  );

}

// Rest route
add_action('rest_api_init', function () {
  register_rest_route( 'v1', 'default_data', array(
    'methods' => 'GET',
    'callback' => 'smamo_rest_default_data',
  ));
});
