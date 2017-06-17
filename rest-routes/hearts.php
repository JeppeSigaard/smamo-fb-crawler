<?php



// Rest Route
add_action( 'rest_api_init', function() {
    register_rest_route( 'svendborg', 'hearts', array(
        array(
            'methods' => 'POST',
            'callback' => 'smamo_rest_update_hearts',
            'permission_callback' => 'smamo_rest_permission_client',
        ),
    ));
});

// Update hearts
function smamo_rest_update_hearts( $data ) {

    // Update number of hearts a certain location, event, etc. has
    return update_post_meta( intval( $data['id'] ), 'hearts', intval( $data['hearts'] ) );

}
