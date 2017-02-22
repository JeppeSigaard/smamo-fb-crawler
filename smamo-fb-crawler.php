<?php
/*
Plugin Type: Piklist
Plugin Name: Smamo Facebook crawler
Author: Smamo
Version: 1.0
*/

// Requires files
require 'custom-posttypes.php';
require 'settings-page.php';


// Rest routes
require 'rest-routes/help-functions.php';
require 'rest-routes/future-events.php';
require 'rest-routes/events.php';
require 'rest-routes/locations.php';
require 'rest-routes/categories.php';

// Smamo crawl
add_action( 'wp_ajax_smamo_crawl', 'smamo_crawl' );
add_action( 'wp_ajax_nopriv_smamo_crawl', 'smamo_crawl' );
function smamo_crawl() {

    require_once plugin_dir_path(__FILE__) . 'php-graph-sdk-5.0.0/src/Facebook/autoload.php';

    $response = array(
        'started' => date("Y-m-d H:i:s"),
    );
    $do = (isset($_POST['do'])) ? esc_attr($_POST['do']) : 'crawl';
    $id = (isset($_POST['id'])) ? esc_attr($_POST['id']) : false;
    $update_old = ( isset($_POST['update_old']) && 'true' === $_POST['update_old'] ) ? true : false;
    $silent = ( isset($_POST['silent']) && 'true' === $_POST['silent'] ) ? true : false;


    // Open fb api
    $fb = new Facebook\Facebook(array(
        'app_id' => '1253090394747963',
        'app_secret' => '85ace41d494e339fd9a130b86d944a95',
        'default_graph_version' => 'v2.5'
    ));

    $fb->setDefaultAccessToken('1253090394747963|85ace41d494e339fd9a130b86d944a95');

    /*---------------*/
    // Hent ID liste
    if('fetch_ids' === $do && isset($_POST['type'])){
        $type = esc_attr($_POST['type']);
        $return['type'] = $type;
        $return['ids'] = array();

        $posts = get_posts(array('post_type' => $type, 'numberposts' => -1));

        foreach($posts as $post){
            $return['ids'][] = array(
                'id' => $post->ID,
                'fbid' => get_post_meta( $post->ID, 'fbid', true ),
            );
        }
    }

    /*---------------*/
    // Crawl all
    if('crawl' === $do && !$id){
        $locations = get_posts(array( 'post_type' => 'location', 'numberposts' => -1 ));
        $crawllocations = get_posts(array( 'post_type' => 'crawllocation', 'numberposts' => -1, 'post_status' => 'publish' ));

        foreach ($crawllocations as $crawlloc) {

            // Gets crawl locations decoded body from the fb api
            try {
                $fbgr = $fb->get( '/'.urlencode($crawlloc->post_title).'?fields=id,about,location,name,picture,category,phone,emails,website,events{cover{source},name,description,id,start_time,end_time,place,ticket_uri}' );
                $body = $fbgr->getDecodedBody();
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                $response['error'][] = 'Graph returned an error: ' . $e->getMessage(); continue;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $response['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); continue;
            }


            // Check for existing location
            $post_id = 0;
            foreach($locations as $loc){
                $fbid = get_post_meta($loc->ID, 'fbid', true);
                if( (string)$fbid === (string)$body['id'] ){
                    $post_id = $loc->ID;
                }
            }

            // Updates location meta using previous found id
            if($update_old || $post_id === 0){

                // Insert new post or update exsting
                $loc_id = wp_insert_post(array(
                    'ID' => $post_id,
                    'post_type'   => 'location',
                    'post_title'  => $body['name'],
                    'post_status' => 'publish',
                ));

                update_post_meta($loc_id, "fbid", $body["id"]);
                update_post_meta($loc_id, "about", $body["about"]);
                update_post_meta($loc_id, "adress", $body["location"]["street"]);
                update_post_meta($loc_id, "name", $body["name"]);
                update_post_meta($loc_id, "category", $body["category"]);
                update_post_meta($loc_id, "phone", $body["phone"]);
                update_post_meta($loc_id, "email", $body["emails"]);
                update_post_meta($loc_id, "picture", $body["picture"]['data']['url']);


                if (filter_var($body["website"], FILTER_VALIDATE_URL)) {
                    update_post_meta($loc_id, "website", $body["website"]);
                } else {
                    delete_post_meta($loc_id, "website");
                }

                // Takes categories from parent crawlloc
                // and adds its as meta to location post
                delete_post_meta( $loc_id, 'categories');
                $categories = array();
                $term_ids = array();
                $terms = wp_get_post_terms( $crawlloc->ID, 'crawl_cat' );
                foreach ($terms as $term) {
                    array_push( $categories, array(
                        'category_id' => $term->term_id,
                        'category_name' => $term->name,
                        'category_imgurl' => get_term_meta( $term->term_id, 'category_thumbnail', true),
                    ));

                    $term_ids[] = $term->term_id;
                }

                //update_post_meta( $loc_id, 'categories', $categories );
                wp_set_post_terms( $loc_id, $term_ids, 'category', false );
            }
        }
    }

    /*---------------*/
    // Crawl single
    if('crawl' === $do && $id){

        $locations = get_posts(array( 'post_type' => 'location', 'numberposts' => -1 ));

        // Use id as post id or fbid #smart
        $fbid = get_post_meta($id, 'fbid', true);
        if(!$fbid){$fbid = $id;}


        // Gets crawl locations decoded body from the fb api
        try {
            $fbgr = $fb->get( '/'.$fbid.'?fields=id,about,location,name,picture,category,phone,emails,website,events{cover{source},name,description,id,start_time,end_time,place,ticket_uri}' );
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
        $loc_id = wp_insert_post(array(
            'ID' => $post_id,
            'post_type'   => 'location',
            'post_title'  => $body['name'],
            'post_status' => 'publish',
        ));

        $response['locations'][] = array($loc_id,(0 === $post_id) ? 'new' : 'old');

        update_post_meta($loc_id, "fbid", $body["id"]);
        update_post_meta($loc_id, "about", $body["about"]);
        update_post_meta($loc_id, "adress", $body["location"]["street"]);
        update_post_meta($loc_id, "name", $body["name"]);
        update_post_meta($loc_id, "category", $body["category"]);
        update_post_meta($loc_id, "phone", $body["phone"]);
        update_post_meta($loc_id, "email", $body["emails"]);
        update_post_meta($loc_id, "picture", $body["picture"]['data']['url']);



        if (filter_var($body["website"], FILTER_VALIDATE_URL)) {
            update_post_meta($loc_id, "website", $body["website"]);
        } else {
            delete_post_meta($loc_id, "website");
        }


        // Takes categories from parent crawlloc
        // and adds its as meta to location post
        $categories = array();
        $term_ids = array();
        delete_post_meta( $loc_id, 'categories');
        $terms = wp_get_post_terms( $crawlloc->ID, 'crawl_cat' );
        for ( $i = 0; $i < sizeof( $terms ); $i++ ) {
            array_push( $categories, array(
                'category_id' => $term->term_id,
                'category_name' => $term->name,
                'category_imgurl' => get_term_meta( $term->term_id, 'category_thumbnail', true),
            ));

            $term_ids[] = $term->term_id;
        }

        update_post_meta( $loc_id, 'categories', $categories );
        wp_set_post_terms( $loc_id, $term_ids, 'category', false );

    }

    /*-------*/
    // Hent events for alle
    if('events' === $do && !$id){
        $locations = get_posts(array( 'post_type' => 'location', 'numberposts' => -1 ));
        $events = get_posts(array( 'post_type' => 'event', 'numberposts' => -1 ));

        foreach($locations as $location){
            $fbid = get_post_meta($location->ID, 'fbid', true);

            // Gets crawl locations decoded body from the fb api
            try {
                $fbgr = $fb->get( '/'.$fbid.'?fields=id,about,location,name,picture,category,phone,emails,website,events{photos{images},cover{source,id},name,description,id,start_time,end_time,place,ticket_uri,owner,is_canceled}' );
                $body = $fbgr->getDecodedBody();
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                $response['error'][] = 'Graph returned an error: ' . $e->getMessage(); continue;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $response['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage(); continue;
            }

            // Loops through all events
            foreach ( $body['events']['data'] as $event ) {

                // Skip events before now
                $event_start = strtotime(substr($event["start_time"], 0, 19));
                $now = strtotime('now');
                if($event_start < $now){
                    continue;
                }

                // Skip if owner - location mismatch
                if($event['owner']['id'] !== $body['id']){
                    continue;
                }


                $event_buffer = false;
                foreach ( $events as $event_post ) {
                    if ( (string)get_post_meta($event_post->ID, 'fbid', true) == (string)$event['id'] ) {
                        $event_buffer = $event_post->ID;
                        break;
                    }
                }

                // Delete if in database and is_canceled is true
                if($event['is_canceled'] ){
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
                    foreach($event['photos']['data']['images'] as $image){
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
                    update_post_meta($event_post_id, "start_time", substr($event["start_time"], 0, 19));
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
    }

    /*-------*/
    // Hent events for ét sted
    if('events' === $do && $id){
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
    }

    /*-------*/
    // Monitor: Hent enkelt post og meta
    if('get_post' === $do && $id){
        $response['post'] = get_post($id);
        $response['post_meta'] = get_post_meta($id);
    }

    $response['ended'] = date("Y-m-d H:i:s");

    if($silent){
        wp_die(json_encode('Silent success, shush...'));
    }

    wp_die(json_encode($response));
}

// Smamo crawl async call
add_action( 'admin_init', 'async_smamo_crawl' );
// wp_schedule_event(time(), 'hourly', 'async_smamo_crawl');
function async_smamo_crawl() {
    wp_enqueue_script(
        'async_smamo_crawl_call',
        plugin_dir_url( __FILE__ ) . 'async_smamo_crawl_call.js'
    );
}

/**
 * Modify url base from wp-json to 'api'
 */
add_filter( 'rest_url_prefix', 'buddydev_api_slug');

function buddydev_api_slug( $slug ) {

    return 'api';
}
