<?php

/*
Title: Data
Post Type: event
order: 2
*/

piklist('field',[
    'field' => 'name',
    'label' => 'Navn',
    'type' => 'text',
    'columns' => 8,
]);


piklist('field',[
    'field' => 'description',
    'label' => 'Beskrivelse',
    'type' => 'textarea',
    'attributes' => [
        'rows' => 6,
        'cols' => 60,
    ],
]);

piklist('field',[
    'field' => 'start_time',
    'label' => 'Starttid (00-00-00T00:00)',
    'type' => 'text',
    'columns' => 8,
]);

piklist('field',[
    'field' => 'ticketurl',
    'label' => 'Billetlink',
    'type' => 'text',
    'columns' => 8,
]);


$pic = get_post_meta(esc_attr($_GET['post']), 'imgurl', true);
if($pic){
    piklist('field',array(
        'type' => 'html',
        'label' => 'Billede',
        'value' => '<img style="max-width: 70%;" src="' . $pic . '"/>',
    ));
}
