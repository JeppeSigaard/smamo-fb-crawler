<?php
/*
Title: Appindstillinger
Post type: application
*/
$id = (isset($_GET['post'])) ? esc_attr($_GET['post']) : false;
if($id){
    piklist('field',array(
        'type' => 'html',
        'label' => 'Client ID',
        'value' => '<input style="width:70%;" type="text" value="' . get_post_meta($id,'client_id', true) . '" disabled>',
    ));

    piklist('field',array(
        'type' => 'html',
        'label' => 'Client Secret',
        'value' => '<input style="width:70%;" type="text" value="' . get_post_meta($id,'client_secret', true) . '" disabled>',
    ));

    piklist('field',array(
        'type' => 'html',
        'label' => 'Bemærk',
        'value' => 'Nye værdier tilføjes ved opdatering',
    ));
}

else{
    piklist('field',array(
        'type' => 'html',
        'label' => 'Client ID',
        'value' => 'Gem for at tilføje værdi',
    ));

    piklist('field',array(
        'type' => 'html',
        'label' => 'Client Secret',
        'value' => 'Gem for at tilføje værdi',
    ));
}
