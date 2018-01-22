<?php

// Fetches all towwwn categories
function towwwn_rest_categories($data) {

  // Creates response field
  $response = array( );

  // Extracts data
  $fields  = (isset( $data['fields'] ))   ? explode(',', $data['fields']) : false;
  $orderby = (isset( $data['orderby'] ))  ? esc_attr($data['orderby'])    : 'id';
  $order   = (isset( $data['order'] ))    ? esc_attr($data['order'])      : 'ASC';
  $perpage = (isset( $data['per_page'] )) ? esc_attr($data['per_page'])   : 999;
  $city    = (isset( $data['city'] ))     ? esc_attr($data['city'])       : null;

  // Prepares query
  $termquery = array(
    'taxonomy'   => 'category',
    'hide_empty' => false,
    'orderby'    => $orderby,
    'order'      => $order,
    'number'     => $perpage,
  );

  // Gets all places in defined city
  $placesInCity = null;
  if ( $city != null ) {

    // Gets all places and creates response field
    $places = get_posts(array('post_type' => 'location', 'posts_per_page' => -1));
    $placesInCity = array( );

    // Loops through places and compares
    foreach ( $places as $place ) {

      // Get terms and compares
      $terms = wp_get_post_terms( $place->ID, 'city' );
      foreach ( $terms as $term ) {
        if ( (int) $term->term_id == (int) $city ) {
          array_push( $placesInCity, $place );
        }
      }

    }

  }

  // Gets the terms
  $terms = get_terms( $termquery );

  // Loops through terms and generates the response
  $resp_term = null;
  foreach ( $terms as $term ) {

    // Creates response term
    $resp_term = array( 'category_id' => $term->term_id );

    // Name
    if( !$fields || in_array( 'name', $fields ) ){
      $resp_term['category_name'] = $term->name;
    }

    // Place count
    if( !$fields || in_array( 'count', $fields ) ){
      $resp_term['location_count'] = $term->count;
    }

    // Parent
    if( !$fields || in_array( 'parent', $fields ) ){
      $resp_term['category_parent'] = $term->parent;
    }

    // City
    if ( $placesInCity != null ) {

      // Counter field
      $counter = 0;

      // Loops through places and compares
      foreach ( $placesInCity as $place ) {
        $tmp_terms = wp_get_post_terms( $place->ID, 'category' );
        foreach ( $tmp_terms as $tmp_term ) {
          if ( (int) $tmp_term->term_id == (int) $term->term_id ) {
            $counter ++;
          }
        }
      }

      // Sets location count
      $resp_term['location_count'] = $counter;

    }

    // Pushes to response
    array_push( $response, $resp_term );

  }

  // Returns
  return $response;

}

function towwwn_rest_category_single($data){
    // prepare post array
    $r = array();

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Catch identifier
    $id = esc_attr($data['id']);

    $term = get_term_by('id', $id, 'category');

    if(!$term){
        $term = get_term_by('slug', $id, 'category');
    }

    if(!$term){
        return $r;
    }

    $term_loc = get_posts(array(
        'post_type' => 'location',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ),

        ),
    ));

    $r = array(
        'category_id' => $term->term_id,
    );

    if(!$fields || in_array('name', $fields)){
        $r['category_name'] = $term->name;
    }

    if(!$fields || in_array('parent', $fields)){
        $r['parent'] = $term->parent;
        $r['category_parent'] = $term->parent;
    }

    if(!$fields || in_array('slug', $fields)){
        $r['category_slug'] = $term->slug;
        $r['slug'] = $term->slug;
    }

    if(!$fields || in_array('img', $fields)){
        $r['category_imgurl'] = get_term_meta( $term->term_id, 'category_thumbnail', true);
    }

    if(!$fields || in_array('type', $fields)){
        $r['type'] = 'category';
    }

    if(!$fields || in_array('count', $fields)){
        $location_count = 0;

        foreach($term_loc as $l){
            $location_count ++;

            $r['locations'][] = smamo_rest_get_fields($l, $fields);
        }


        $r['location_count'] = $location_count;
    }

    if(!$fields || in_array('location_img', $fields)){
        foreach($term_loc as $l){
           if(get_post_meta($l->ID,'coverphoto', true)){
               $r['location_img'] = get_post_meta($l->ID,'coverphoto', true);
               break;
           }
        }
    }

    if(!$fields || in_array('category_children', $fields)){
        $children = array();
        $children_query = get_terms(array('child_of' => $term->term_id, 'taxonomy' => 'category'));

        foreach($children_query as $child){
            $children[] = array(
                'category_id' => $child->term_id,
                'category_name' => $child->name,
                'category_slug' => $child->slug,
                'slug' => $child->slug,
                'category_parent' => $child->parent,
                'parent' => $child->parent,
                'category_count' => $child->count,
                'type' => 'category',
            );
        }
        $r['children'] = $children;
    }

    return $r;
}

// Registers the rest routes
add_action( 'rest_api_init', function () {

  register_rest_route( 'v1', 'categories', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_categories',
  ));

  register_rest_route( 'v1', 'categories/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'towwwn_rest_category_single',
  ));

} );
