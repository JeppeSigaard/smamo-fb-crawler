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
require 'rest-routes/permissions.php';
require 'rest-routes/help-functions.php';
require 'rest-routes/future-events.php';
require 'rest-routes/events.php';
require 'rest-routes/locations.php';
require 'rest-routes/categories.php';
require 'rest-routes/commercials.php';
require 'rest-routes/menus.php';
require 'rest-routes/user.php';

// Crawler functions
require 'crawler-functions/formatting/hours-format.php';

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

    // Hent ID liste
    if('fetch_ids' === $do && isset($_POST['type'])){
        include plugin_dir_path(__FILE__) . 'crawler-functions/fetch-ids.php';
    }

    // Crawl all
    if('crawl' === $do && !$id){
        include plugin_dir_path(__FILE__) . 'crawler-functions/locations.php';
    }

    // Crawl single
    if('crawl' === $do && $id){
        include plugin_dir_path(__FILE__) . 'crawler-functions/single-location.php';
    }

    // Hent events for alle
    if('events' === $do && !$id){
        include plugin_dir_path(__FILE__) . 'crawler-functions/events.php';
    }

    // Hent events for ét sted
    if('events' === $do && $id){
         include plugin_dir_path(__FILE__) . 'crawler-functions/single-event.php';
    }

    // Monitor: Hent enkelt post og meta
    if('get_post' === $do && $id){
        include plugin_dir_path(__FILE__) . 'crawler-functions/get-post.php';
    }

    // Purge events
    if('purge_events' === $do){
        include plugin_dir_path(__FILE__) . 'crawler-functions/purge-events.php';
    }

    $response['ended'] = date("Y-m-d H:i:s");

    wp_die(json_encode($response));
}

// Smamo crawl async call
add_action( 'admin_init', 'async_smamo_crawl' );
function async_smamo_crawl() {
    wp_enqueue_script(
        'async_smamo_crawl_call',
        plugin_dir_url( __FILE__ ) . 'async_smamo_crawl_call.js'
    );
}

add_filter( 'rest_url_prefix', 'buddydev_api_slug');
function buddydev_api_slug( $slug ) {
    return 'api';
}















