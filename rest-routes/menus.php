<?php

function smamo_rest_menus($data){
    // prepare post array
    $r = array();

    // Get menus
    $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

    if($menus){
        foreach($menus as $menu){
            $r[] = array(
                'id' => $menu->term_id,
                'slug' => $menu->slug,
                'title' => $menu->name,
            );
        }
    }

    return $menus;
}


function smamo_rest_menu($data){

    // prepare post array
    $r = array();

    // Get menu items
    $menu_items = wp_get_nav_menu_items($data['id']);

    if($menu_items){
        foreach($menu_items as $item){
            $r[] = array(
                'id' => $item->object_id,
                'title' => $item->title,
                'url' => $item->url,
            );
        }
    }

    return $r;
}


add_action( 'rest_api_init', function () {

    register_rest_route( 'svendborg', 'menus', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_menus',
	) );

    register_rest_route( 'svendborg', 'menus/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_menu',
	) );

} );
