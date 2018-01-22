<?php
/*
Gets default app information,
ie. event, location & category count. etc.
*/

// Function
function towwwn_rest_default_data( $data ) {

  // Extracts data
  $city = (isset( $data['city'] )) ? esc_attr($data['city']) : null;
  if ( $city == null ) { return new WP_Error( '404', "City wasn't set" ); }

  // Place count
  $places = get_posts( array(

    'post_type' => 'location',
    'numberposts' => -1,

    // Specific city
    'tax_query' => array( array(
      'taxonomy' => 'city',
      'field' => 'term_id',
      'terms' => array((int) $city),
    )),

  ));

  // Future event count
  $future_event_count = 0;
  foreach ( $places as $place ) {
    $future_event_count += count(get_posts(array(

      'post_type' => 'event',
      'posts_per_page' => -1,

      'meta_query' => array(
        'relation' => 'AND',

        // Makes sure its from the city
        array(
          'key'   => 'parentid',
          'value' => $place->ID,
        ),

        // Checks that it's future
        array(
          'key'     => 'start_time',
          'value'   => date("Y-m-d H:i:s") ,
          'compare' => '>=',
          'type'    => 'DATETIME'
        )

      ),

    )));
  }

  // Response composition & returns
  return array(
    'future_event_count' => $future_event_count,
    'place_count' => count( $places ),
  );

}

// Rest route
add_action('rest_api_init', function () {
  register_rest_route( 'v1', 'default_data', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_default_data',
  ));
});
