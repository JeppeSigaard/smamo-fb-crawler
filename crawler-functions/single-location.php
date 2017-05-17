<?php

$locations = get_posts(array( 'post_type' => 'location', 'numberposts' => -1 ));

// Use id as post id or fbid #smart
$fbid = get_post_meta($id, 'fbid', true);
if(!$fbid){$fbid = $id;}


// Gets locations decoded body from the fb api
try {
    $fbgr = $fb->get( '/'.$fbid.'?fields=id,about,location,name,description, picture,category,phone,emails,website,events{cover{source},name,description,id,start_time,end_time,place,ticket_uri}' );
    $body = $fbgr->getDecodedBody();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    $response['error'][] = 'Graph returned an error: ' . $e->getMessage(); wp_die(json_encode($response));
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    $response['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); wp_die(json_encode($response));
}


// Check for existing location
$post_id = 0;
foreach($locations as $loc){
    $location_fbid = get_post_meta($loc->ID, 'fbid', true);
    if( (string)$location_fbid === $fbid ){
        $post_id = $loc->ID;
    }
}

// Insert new post or update exsting
$loc_id = wp_update_post(array(
    'ID' => $post_id,
    'post_type'   => 'location',
    'post_title'  => $body['name'],
    'post_name'  => sanitize_title($body['name']),
    'post_status' => 'publish',
));

$response['locations'][] = array($loc_id,(0 === $post_id) ? 'new' : 'old');

update_post_meta($loc_id, "fbid", $body["id"]);
update_post_meta($loc_id, "about", $body["about"]);
update_post_meta($loc_id, "description", $body["description"]);
update_post_meta($loc_id, "adress", $body["location"]["street"]);
update_post_meta($loc_id, "name", $body["name"]);
update_post_meta($loc_id, "category", $body["category"]);
update_post_meta($loc_id, "phone", $body["phone"]);
update_post_meta($loc_id, "email", $body["emails"]);
update_post_meta($loc_id, "picture", $body["picture"]['data']['url']);
update_post_meta($loc_id, "coverphoto", $body["cover"]["source"]);


if (filter_var($body["website"], FILTER_VALIDATE_URL)) {
    update_post_meta($loc_id, "website", $body["website"]);
} else {
    delete_post_meta($loc_id, "website");
}
