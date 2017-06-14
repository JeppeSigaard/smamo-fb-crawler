<?php


$locations = get_posts(array( 'post_type' => 'location', 'numberposts' => -1 ));
foreach ($locations as $location) {

    $path = get_post_meta($location->ID,'fb_crawl',true);

    if(!$path && !$update_old){ continue; }

    elseif(!$path){ $path = get_post_meta($location->ID,'fbid',true); }

    if(!$path){ continue; }

    try {
        $fbgr = $fb->get( '/'.urlencode($path).'?fields=id,about,location,name,description,picture,hours,category,phone,emails,website,cover{source},events{cover{source},name,description,id,start_time,end_time,place,ticket_uri}' );
        $body = $fbgr->getDecodedBody();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $response['error'][] = 'Graph returned an error: ' . $e->getMessage(); continue;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        $response['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); continue;
    }

    $fbid = get_post_meta($location->ID, 'fbid', true);

    if($update_old || !$fbid){

        // Insert new post or update exsting
        $loc_id = wp_update_post(array(
            'ID' => $location->ID,
            'post_type'   => 'location',
            'post_title'  => $body['name'],
            'post_name' => sanitize_title($body['name']),
            'post_status' => 'publish',
        ));

        update_post_meta($loc_id, "fbid", $body["id"]);
        update_post_meta($loc_id, "about", $body["about"]);
        update_post_meta($loc_id, "description", $body["description"]);
        update_post_meta($loc_id, "adress", $body["location"]["street"]);
        update_post_meta($loc_id, "name", $body['name']);
        update_post_meta($loc_id, "category", $body["category"]);
        update_post_meta($loc_id, "hours", json_encode( format_hours( $body['hours'] ) ));
        update_post_meta($loc_id, "phone", $body["phone"]);
        update_post_meta($loc_id, "email", $body["emails"]);
        update_post_meta($loc_id, "picture", $body["picture"]['data']['url']);
        update_post_meta($loc_id, "coverphoto", $body["cover"]["source"]);

        if (filter_var($body["website"], FILTER_VALIDATE_URL)) {
            update_post_meta($loc_id, "website", $body["website"]);
        } else {
            delete_post_meta($loc_id, "website");
        }

        // location register hook
        if(!$fbid){
            do_action( 'twn_location_register', $location->ID );
        }
    }
}
