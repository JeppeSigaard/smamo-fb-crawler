<?php

// Towwwn API get event
function towwwn_rest_events( $data ) {

  // Creates response field
  $response = array( );

  // Extracts data
  $fields = (isset( $data['fields'] )) ? explode(',', $data['fields']) : false;
  $parent = (isset( $data['parent'] )) ? esc_attr($data['parent']) : null;
  $city   = (isset( $data['city'] ))   ? esc_attr($data['city']) : null;

  $after  = (isset( $data['after'] ))  ? date('Y-m-d\TH:i:s', strtotime(esc_attr( $data['after'] ))) : null;
  $before = (isset( $data['before'] )) ? date('Y-m-d\TH:i:s', strtotime(esc_attr( $data['before']))) : null;

  $perpage = (isset( $data['per_page'] )) ? esc_attr($data['per_page']) : null;
  $page    = (isset( $data['page'] ))     ? esc_attr($data['page'])     : null;

  // Prepares queries
  $metaquery = array( 'relation' => 'AND' );
  $query = array(
    'posts_per_page' => -1,
    'post_type' => 'event',
    'meta_key'  => 'start_time',
    'meta_type' => 'DATETIME',
    'orderby'   => 'meta_value',
  );

  // Applies parent
  if( $parent !== null ){
    array_push( $metaquery, array(
      'key' => 'parentid',
      'value' => $parent,
      'compare' => '=',
    ));
  }

  // Applies after
  if( $after !== null ){
    array_push( $metaquery, array(
      'key' => 'start_time',
      'value' => $after,
      'compare' => '>',
      'type' => 'DATETIME',
    ));
  }

  // Applies before
  if( $before !== null ){
    array_push( $metaquery, array(
      'key' => 'start_time',
      'value' => $before,
      'compare' => '<',
      'type' => 'DATETIME',
    ));
  }

  // Sets meta query and fetches events
  $query['meta_query'] = $metaquery;
  $posts = get_posts( $query );

  // Loop through, apply fields and check for city
  foreach( $posts as $post ) {

    // Gets parent
    $parentid = get_post_meta( $post->ID, 'parentid', true );
    $cities   = wp_get_post_terms( (int) $parentid, 'city' );

    // Has city
    $hascity = false;
    if ( $city !== null ) {
      foreach ( $cities as $term ) {
        if ( (int) $term->term_id === (int) $city ) {
          $hascity = true;
        }
      }
    }

    // If city isn't set, return all.
    else { $hascity = true; }

    // Sets response
    if ( $hascity ) {
      array_push( $response, smamo_rest_get_fields( $post, $fields ) );
    }
  }

  // Sorts the response
  usort( $response, function( $a, $b ) {

    $atime = strtotime(get_post_meta( (int) $a['id'], 'start_time', true ));
    $btime = strtotime(get_post_meta( (int) $b['id'], 'start_time', true ));

    if ( $atime > $btime ) { return 1; }
    if ( $atime < $btime ) { return -1; }
    return 0;

  });

  // Applies page offset & returns
  $response = array_slice($response, $page * $perpage, $perpage);
  return $response;

}

function towwwn_rest_event_single( $data ){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // prepare post array
    $r = array();

    // Catch identifier
    $id = esc_attr($data['id']);

    // First try ID
    $post = get_post($id);

    // Then try fbid
    if (!$post || 'event' !== $post->post_type){
        $fbid_query = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_key' => 'fbid',
            'meta_value' => $id,
        ));

        if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
    }

    if ($post && 'publish' === $post->post_status && 'event' === $post->post_type ){

        $r[] = smamo_rest_get_fields($post, $fields);
    }

    return $r;
}

function towwwn_rest_update_event($data){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Catch identifier
    $id = esc_attr($data['id']);

    // First try ID
    $post = get_post($id);

    // Then try fbid
    if (!$post || 'event' !== $post->post_type){
        $fbid_query = get_posts(array(
            'post_type' => 'event',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_key' => 'fbid',
            'meta_value' => $id,
        ));

        if($fbid_query && isset($fbid_query[0])){$post = $fbid_query[0];}
    }

    if ($post && 'publish' === $post->post_status && $post->post_type === 'event' ){

         /// Modtag data
        $post_data = isset($data['data']) ? $data['data'] : array();
        $post_meta = isset($data['meta_data']) ? $data['meta_data'] : array();
        $overwrite = isset($data['overwrite']) ? $data['overwrite'] : true;

        if($post_data){
            $post_data['ID'] = $post->ID;
            wp_update_user($post_data);
        }

        foreach($post_meta as $k => $v){
            if($overwrite){
                update_post_meta($post->ID, $k, $v);
            }

            else{
                add_post_meta($post->ID, $k, $v, false);
            }
        }

        return smamo_rest_get_fields($post, $fields);
    }

    return new WP_Error( 'error', 'Could not update event', array( 'status' => 404 ) );
}

add_action( 'rest_api_init', function () {

  register_rest_route( 'v1', 'events', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_events',
  ));

  register_rest_route( 'v1', 'events/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_event_single',
  ));

});
