<?php

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
