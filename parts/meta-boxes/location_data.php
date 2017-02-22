<?php

/*
Title: Data
Post Type: location
*/

if(isset($_GET['post'])){
    piklist('field',array(
        'type' => 'html',
        'label' => 'Metadata',
        'value' => '<pre>' . print_r(get_post_meta(esc_attr($_GET['post'])), true) . '</pre>',
    ));
}
