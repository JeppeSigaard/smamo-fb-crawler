<?php

foreach(get_posts([ 'post_type' => 'event', 'posts_per_page' => -1, 'post_status' => 'publish']) as $event){

    // Skip events before now
    $event_start = strtotime(substr($event["start_time"], 0, 19));
    $now = strtotime('now');
    if($event_start < $now){ continue; }

    // Facebook ID, slet hvis mangler
    $fbid = get_post_meta($event->ID, 'fbid', true);
    if(!$fbid){
        wp_delete_post( $event->ID, true );
        continue;
    }

    // Forælders facebook ID, slet hvis mangler
    $parentfbid = get_post_meta($event->ID, 'parentfbid', true);
    if(!$parent$fbid){
        wp_delete_post( $event->ID, true );
        continue;
    }


    try {

        $fbgr = $fb->get( '/'.$fbid.'?fields=is_canceled' );
        $body = $fbgr->getDecodedBody();

    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        wp_update_post( ['ID' => $event->ID, 'post_status' => 'deleted'] );

    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        continue;
    }

    if($body['is_canceled']){
         wp_update_post( ['ID' => $event->ID, 'post_status' => 'canceled'] );
    }

}

