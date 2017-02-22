<?php

add_filter('piklist_admin_pages', 'smamo_crawl_settings');
    function smamo_crawl_settings($pages){
         $pages[] = array(
            'page_title' => __('Facebook crawler'),
            'menu_title' => __('Crawler', 'piklist'),
            'sub_menu' => 'options-general.php',
            'capability' => 'manage_options',
            'menu_slug' => 'crawl',
            'setting' => 'crawl_settings',
            'single_line' => true,
            'default_tab' => 'Basic',
            'save_text' => 'Start Crawler',
        );

    return $pages;
}
