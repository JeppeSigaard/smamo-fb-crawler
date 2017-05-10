<?php

foreach(get_posts([ 'post_type' => 'event', 'posts_per_page' => -1, 'post_status' => 'publish']) as $event){

    // Delete old events
    $event_start = strtotime(substr(get_post_meta($event->ID, 'start_time', true), 0, 19));
    $time = strtotime('-365 day');
    if($event_start < $time){
        wp_delete_post( $event->ID, true );
        continue;
    }

    // Facebook ID, slet hvis mangler
    $fbid = get_post_meta($event->ID, 'fbid', true);
    if(!$fbid){
        wp_delete_post( $event->ID, true );
        continue;
    }

    // Forælders facebook ID, slet hvis mangler
    $parentfbid = get_post_meta($event->ID, 'parentfbid', true);
    if(!$parentfbid){
        wp_delete_post( $event->ID, true );
        continue;
    }


    try {

        $fbgr = $fb->get( '/'.$fbid.'?fields=is_canceled' );
        $body = $fbgr->getDecodedBody();

    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        wp_delete_post($event->ID, true);
        continue;

    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        continue;
    }

    if($body['error']){
        wp_delete_post($event->ID, true);
        continue;
    }

    if($body['is_canceled']){
        wp_delete_post($event->ID, true);
        continue;
    }
}

