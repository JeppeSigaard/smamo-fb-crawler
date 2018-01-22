<?php

// Towwwn rest API locations
function towwwn_rest_places( $data ) {

  // Extracts dats
  $fields  = (isset( $data['fields'] ))  ? explode(',', $data['fields']) : false;
  $orderby = (isset( $data['orderby'] )) ? esc_attr($data['orderby'])    : 'RAND';
  $order   = (isset( $data['order'] ))   ? esc_attr($data['order'])      : 'ASC';

  $perpage = (isset( $data['per_page'] )) ? esc_attr($data['per_page']) : null;
  $page    = (isset( $data['page'] ))     ? esc_attr($data['page'])     : null;

  $city = (isset( $data['city'] )) ? esc_attr($data['city']) : null;
  $cat  = (isset( $data['cat'] ))  ? esc_attr($data['cat'])  : null;

  $taxquery = array( 'relation' => 'AND' );

  // Set main query fields
  $query = array(
    'post_type' => 'location',
    'orderby'   => $orderby,
    'order'     => $order,
  );

  // Applies per page
  if ( $perpage !== null ) {

    // Sets per page
    $query['posts_per_page'] = $perpage;

    // The page
    if ( $page  !== null) {
      $query['offset'] = $perpage * ( (int) $page - 1 );
    }

  }

  // Applies city to tax query
  if ( $city !== null ) {
    array_push( $taxquery, array(
      'taxonomy' => 'city',
      'field'    => 'term_id',
      'terms'    => array($city),
    ));
  }

  // Applies category to tax query
  if ( $cat !== null ) {
    $cats = explode(',', $cat);
    array_push( $taxquery, array(
      'taxonomy' => 'category',
      'field' => 'term_id',
      'terms' => $cats,
    ));
  }

  // Makes query contain tax query n' gets the places
  $query['tax_query'] = $taxquery;
  $posts = get_posts($query);

  // Creates response field
  $response = array();

  // Loop through and apply fields
  foreach ( $posts as $post ) {
    if( '' !== $post->post_title ){
      $response[] = smamo_rest_get_fields( $post, $fields );
    }
  }

  // Returns
  return $response;

}

// Fetches a single place
function towwwn_rest_place_single( $data ){

  $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

  // prepare post array
  $r = array();

  // Catch identifier
  $id = esc_attr($data['id']);

  // First try ID
  $post = get_post($id);

  // Then try fbid
  if (!$post || 'location' !== $post->post_type){
    $fbid_query = get_posts(array(
      'post_type' => 'event',
      'posts_per_page' => 1,
      'post_status' => 'publish',
      'meta_key' => 'fbid',
      'meta_value' => $id,
    ));

    if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
  }

  if ($post && 'publish' === $post->post_status && $post->post_type === 'location' ){
    $r[] = smamo_rest_get_fields($post, $fields);
  }

  return $r;

}

add_action( 'rest_api_init', function () {

  register_rest_route( 'v1', 'places', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_places',
  ));

  register_rest_route( 'v1', 'places/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_place_single',
  ));

});
