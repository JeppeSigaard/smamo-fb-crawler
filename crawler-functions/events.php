<?php

// per crawl
$event_per_crawl = 100;
if(get_option('event_per_crawl')){ $event_per_crawl = intval(get_option('event_per_crawl'));}
else{ update_option('event_per_crawl', $event_per_crawl); }

// event_paginated_crawl
$event_paginated_crawl = (get_option('event_paginated_crawl')) ? intval(get_option('event_paginated_crawl')) : 0;

$locations = get_posts(array( 'post_type' => 'location', 'numberposts' => $event_per_crawl, 'offset' => ($event_paginated_crawl * $event_per_crawl) ));

// Update event_paginated_crawl
if(count($locations) < $event_per_crawl){$event_paginated_crawl = 0; }
else{$event_paginated_crawl ++;}
update_option('event_paginated_crawl', $event_paginated_crawl);

$events = get_posts(array( 'post_type' => 'event', 'numberposts' => -1 ));

foreach($locations as $location){
    $fbid = get_post_meta($location->ID, 'fbid', true);

    // Gets crawl locations decoded body from the fb api
    try {
        $fbgr = $fb->get( '/'.$fbid.'?fields=id,about,location,name,picture,category,phone,emails,website,events{photos{images},cover{source,id},name,description,id,start_time,end_time,place,ticket_uri,owner,is_canceled}' );
        $body = $fbgr->getDecodedBody();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $response['response_error'][] = 'Graph returned an error: ' . $e->getMessage(); continue;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        $response['sdk_error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); continue;
    }

    // Loops through all events
    foreach ( $body['events']['data'] as $event ) {

        // Skip events before a month ago
        $event_start = strtotime(substr($event["start_time"], 0, 19));
        $event_start_limit = strtotime('-1 month');
        if($event_start < $event_start_limit){
            continue;
        }

        // Skip if owner - location mismatch
        if($event['owner']['id'] !== $body['id']){
            continue;
        }


        $event_buffer = false;
        foreach ( $events as $event_post ) {

            // Purge orphans
            if(get_post_meta($event_post->ID, 'parentfbid', true) == ''){ wp_delete_post($event_post->ID, true);}

            if ( (string)get_post_meta($event_post->ID, 'fbid', true) == (string)$event['id'] ) {
                $event_buffer = $event_post->ID;
                break;
            }
        }

        // Delete if in database and is_canceled is true
        if($event['is_canceled'] === 'true'){
            $response['events']['deleted'][] = $event_buffer;
            if($event_buffer){
                 wp_delete_post($event_buffer, true);
            }
            continue;
        }

        // Gets id of either newly created post or old
        if ( !$event_buffer ) {

            $event_post_id = wp_insert_post(array(
                'post_type'   => 'event',
                'post_title'  => $event['name'],
                'post_name' => sanitize_title($event['name']),
                'post_status' => 'publish',
            ));
            $response['events']['new'][] = $event_post_id;
        }

        else {
            $event_post_id = $event_buffer;
            $response['events']['old'][] = $event_post_id;
        }


        update_post_meta($event_post_id, "parentpicture", $body["picture"]['data']['url']);

        // Updates event meta
        if($update_old || !$event_buffer ){

            $images = array(); $i = 0;
            foreach($event['photos']['data'][0]['images'] as $image){
                $key = ($i === 0) ? 'full' : $image['height'];
                $images[$key] = $image['source'];
                $i ++;
            }

            update_post_meta($event_post_id, "images", $images);
            update_post_meta($event_post_id, "fbid", $event["id"]);
            update_post_meta($event_post_id, "name", $event["name"]);
            update_post_meta($event_post_id, "parentname", $body["name"]);
            update_post_meta($event_post_id, "parentid", $location->ID);
            update_post_meta($event_post_id, "parentfbid", $body["id"]);
            update_post_meta($event_post_id, "description", $event["description"]);
            update_post_meta($event_post_id, "start_time", $event["start_time"]);
            update_post_meta($event_post_id, "end_time", $event["end_time"]);
            update_post_meta($event_post_id, "adress", $event["place"]["location"]["street"]);
            update_post_meta($event_post_id, "imgurl", $event["cover"]["source"]);
            update_post_meta($event_post_id, "ticket_uri", $event["ticket_uri"]);
            update_post_meta($event_post_id, "phone", $body["phone"]);

            if (filter_var($body["website"], FILTER_VALIDATE_URL)) {
                update_post_meta($event_post_id, "website", $body["website"]);
            } else {
                delete_post_meta($event_post_id, "website");
            }
        }
    }
}
