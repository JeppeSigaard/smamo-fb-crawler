<?php

// Get user fields (default * or specified)
function smamo_rest_get_user_fields($user, $fields = false){

    // default fields are always included
    $response_array = array(
        'id' => $user->ID,
    );

    // These are included, if no fields array is passed
    if(!$fields){
        $fields = array(
            'display_name', 'email'
        );
    }

    // get some data or that field aight
    foreach($fields as $field){ if('' !== $field){
        $prfx = 'user_' . $field;

        if(isset($user->$field)){
            $response_array[$field] = $user->$field;
        }

        elseif(isset($user->$prfx)){
            $response_array[$field] = $user->$prfx;
        }

        elseif(get_user_meta($user->ID,$field,true)){
            $response_array[$field] = get_user_meta($user->ID, $field, true);
        }
    }}

    return $response_array;
}

// Gets a single user from the db
function smamo_rest_get_user($data){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    $user_id = esc_attr($data['id']);
    if(!$user_id){
        return new WP_Error( 'error_no_id', 'Missing user ID', array( 'status' => 100 ) );
    }

    $user = get_user_by('ID', $user_id);
    if(!$user){
        $fbid_query = get_users(array(
            'meta_key' => 'fbid',
            'meta_value' => $user_id,
        ));

        if($fbid_query && isset($fbid_query[0])){$user = $fbid_query[0];}
    }

    if(!$user){
        return new WP_Error( 'error_invalid_id', 'No user found in the database by that ID', array( 'status' => 404 ) );
    }

    return smamo_rest_get_user_fields($user, $fields);
}

// Updates a single user from the db
function smamo_rest_update_user($data){
    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;
    $user = get_user_by('ID', esc_attr($data['id']));
    if(!$user){
        return new WP_Error( 'error_invalid_id', 'No user found in the database by that ID', array( 'status' => 404 ) );
    }

    /// Modtag data
    $post_data = isset($data['data']) ? $data['data'] : array();
    $post_meta = isset($data['meta_data']) ? $data['meta_data'] : array();
    $overwrite = isset($data['overwrite']) ? $data['overwrite'] : true;

    if($post_data){
        $post_data['ID'] = $user->ID;
        wp_update_user($post_data);
    }

    foreach($post_meta as $k => $v){
        if($overwrite){
            $update = update_user_meta($user->ID, $k, $v);
            if(!$update){ return new WP_Error( 'error', 'Data could not be updated', array( 'status' => 400 ) );}
        }

        else{
            $add = add_user_meta($user->ID, $k, $v, false);
            if(!$add){ return new WP_Error( 'error', 'Data could not be added', array( 'status' => 400 ) );}
        }
    }

    return smamo_rest_get_user_fields($user, $fields);
}

// Create and/or return a single user
function smamo_rest_signon($data){

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;
    $email = isset($data['email']) ? esc_attr($data['email']) : false;
    $name = isset($data['name']) ? esc_attr($data['name']) : false;
    $token = isset($data['token']) ? esc_attr($data['token']) : false;
    $fbid = isset($data['fbid']) ? esc_attr($data['token']) : false;
    $user = false;

    if(!$email || !$fbid || !$token || !$name){
        return new WP_Error( 'error_missing_args', 'Missing arguments', array( 'status' => 404 ) );
    }

    $fbid_query = get_users(array(
        'meta_key' => 'fbid',
        'meta_value' => $fbid,
    ));

    // Found user by fbid
    if($fbid_query && isset($fbid_query[0])){
        $user = $fbid_query[0];

        update_user_meta($user->ID, 'token', $token);

        return smamo_rest_get_user_fields($user, $fields);
    }

    // Found user by email
    $user = get_user_by('email', $email);
    if($user){

        update_user_meta($user->ID, 'token', $token);
        update_user_meta($user->ID, 'fbid', $fbid);

        return smamo_rest_get_user_fields($user, $fields);
    }

    // Create new user
    if(!$user){

        $login = $name;
        $name_array = explode(' ', $name, 2);

        $i = '';
        $found = false;
        while (!$found) {
            $user = get_user_by('name', $login . $i);
            if(!$user) {
                $found = true;
                $login .= $i;
            }
            if($i === ''){$i = 1;}
            else{$i++;}
        }

        $user_id = wp_insert_user(array(
            'first_name' => $name_array[0],
            'last_name' => $name_array[1],
            'user_login' => $login,
            'user_email' => $email,
            'user_nicename' => $name,
            'user_pass' => wp_generate_password(64, true),
        ));

        if(!$user_id){
            return new WP_Error( 'error', 'Could not create new user', array( 'status' => 404 ) );
        }

        update_user_meta($user_id, 'token', $token);
        update_user_meta($user_id, 'fbid', $fbid);

        $user = get_user_by('ID', $user_id);
        return smamo_rest_get_user_fields($user, $fields);
    }
}

add_action( 'rest_api_init', function () {

    register_rest_route( 'svendborg', 'user/(?P<id>\d+)', array(

        array(
            'methods' => 'GET',
            'callback' => 'smamo_rest_get_user',
            'permission_callback' => 'smamo_rest_permission',
        ),

        array(
            'methods' => 'POST',
            'callback' => 'smamo_rest_update_user',
            'permission_callback' => 'smamo_rest_permission_user',
        ),
	));

    register_rest_route( 'svendborg', 'user/signon',array(
        'methods' => 'POST',
        'callback' => 'smamo_rest_signon',
        'permission_callback' => 'smamo_rest_permission_client',
	));
});
