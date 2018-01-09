<?php

$events = get_posts(array( 'post_type' => 'event', 'numberposts' => -1 ));

// Use id as post id or fbid #smart
$fbid = get_post_meta($id, 'fbid', true);
if(!$fbid){wp_die(json_encode('error')); $fbid = $id;}

// Gets crawl locations decoded body from the fb api
try {
    $fbgr = $fb->get( '/'.$fbid.'?fields=cover{source,id},name,description,id,start_time,end_time,place,ticket_uri' );
    $body = $fbgr->getDecodedBody();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    $response['error'][] = 'Graph returned an error: ' . $e->getMessage(); wp_die(json_encode($response));
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    $response['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); wp_die(json_encode($response));
}

$event_buffer = false;
foreach ( $events as $event_post ) {
    if ( (string)get_post_meta($event_post->ID, 'fbid', true) == (string)$event['id'] ) {
        $event_buffer = $event_post->ID;
        break;
    }
}

// Gets id of either newly created post or old
if ( !$event_buffer ) {

    $event_post_id = wp_insert_post(array(
        'post_type'   => 'event',
        'post_title'  => $event['name'],
        'post_name' => sanitize_title($event['name']),
        'post_status' => 'publish',
    ));
    $response['events']['new'] = $event_post_id;

}
else {
    $event_post_id = $event_buffer;
    $response['events']['old'] = $event_post_id;
}

// Updates event meta
if($update_old || !$event_buffer ){

    try { $fbgr = $fb->get( '/'.$body['cover']['id'].'?fields=images' ); $cover = $fbgr->getDecodedBody();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $response['error'][] = 'Graph returned an error: ' . $e->getMessage(); wp_die(json_encode($response));
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        $response['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); wp_die(json_encode($response));
    }

    $images = array(); $i = 0;
    foreach($cover['images'] as $image){
        $key = ($i === 0) ? 'full' : $image['height'];
        $images[$key] = $image['source'];
        $i ++;
    }

    update_post_meta($event_post_id, "images", $images);
    update_post_meta($event_post_id, "fbid", $body["id"]);
    update_post_meta($event_post_id, "name", $body["name"]);
    update_post_meta($event_post_id, "description", $body["description"]);
    update_post_meta($event_post_id, "start_time", substr($body["start_time"], 0, 19));
    update_post_meta($event_post_id, "adress", $body["place"]["location"]["street"]);
    update_post_meta($event_post_id, "imgurl", $body["cover"]["source"]);
    update_post_meta($event_post_id, "ticket_uri", $body["ticket_uri"]);
}
