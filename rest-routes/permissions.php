<?php

// Basic app ID / app Secret check
function smamo_rest_check_client_id_secret($client_id, $client_secret){

    // Get apps with matching ids
    $apps = get_posts(array(
        'post_type' => 'application',
        'posts_per_page' => -1,
        'meta_key' => 'client_id',
        'meta_value' => esc_attr($client_id),
    ));

    // If no client ID, deny access
    if(!$apps){return false;}

    // If secret mismatch, deny access
    foreach($apps as $app){

        $secret = get_post_meta($app->ID,'client_secret', true);

        if($secret === esc_attr($client_secret)){
            return true;
        }
    }
}

// Basic user token check
function smamo_rest_check_user_token($user_id, $user_token){
    $token = get_user_meta(esc_attr($user_id),'token', true);
    if($token === esc_attr($user_token)){ return true; }
    return false;
}

// Basic permission check
function smamo_rest_permission($data){

    if(isset($data['token']) && isset($data['user'])){
        return smamo_rest_check_user_token($data['user'], $data['token']);
    }

    elseif(isset($data['client_id']) && isset($data['client_secret'])){
        return smamo_rest_check_client_id_secret($data['client_id'], $data['client_secret']);
    }

    return false;
}

// Specifically app permission check
function smamo_rest_permission_client($data){
    return smamo_rest_check_client_id_secret($data['client_id'], $data['client_secret']);
}

// Specifically user permission check
function smamo_rest_permission_user($data){
   return smamo_rest_check_user_token($data['id'], $data['token']);
}
