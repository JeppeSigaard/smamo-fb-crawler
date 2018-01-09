<?php


// per crawl
$location_per_crawl = 100;
if(get_option('location_per_crawl')){ $location_per_crawl = intval(get_option('location_per_crawl'));}
else{ update_option('location_per_crawl', $location_per_crawl); }

// location_paginated_crawl
$location_paginated_crawl = (get_option('location_paginated_crawl')) ? intval(get_option('location_paginated_crawl')) : 0;

// Get hearts
$hearts = array();
$users = get_users();
foreach( $users as $user ) {

    // User hearts
    $userhearts = ( get_user_meta( $user->ID, 'hearts' ) ) [0]['locations'];
    for ( $iter = 0; $iter < count( $userhearts ); $iter++ ) {
        if ( $userhearts[ $iter ] !== null ) {
            if ( $hearts === null ) $hearts[ ((string) $iter) ] = 1;
            else $hearts[ ((string) $iter) ] ++;
        }
    }

}

$locations = get_posts(array( 'post_type' => 'location', 'numberposts' => $location_per_crawl, 'offset' => ($location_per_crawl * $location_paginated_crawl ) ));

// Update location_paginated_crawl
if(count($locations) < $location_per_crawl){$location_paginated_crawl = 0;}
else{$location_paginated_crawl ++;}
update_option('location_paginated_crawl', $location_paginated_crawl);

foreach ($locations as $location) {

    // Update hearts
    if ( $hearts[ ((string) $location->ID) ] !== null ) {
        update_post_meta( $location->ID, 'hearts', $hearts[ ((string) $location->ID ) ] );
    }

    $path = get_post_meta($location->ID,'fb_crawl',true);

    if(!$path && !$update_old){ continue; }

    elseif(!$path){ $path = get_post_meta($location->ID,'fbid',true); }

    if(!$path){ continue; }

    try {
        $fbgr = $fb->get( '/'.urlencode($path).'?fields=id,about,location,name,description,picture.type(large),hours,category,phone,emails,website,cover{source},events{cover{source},name,description,id,start_time,end_time,place,ticket_uri}' );
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

        // Updates post meta
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

        // Website
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
