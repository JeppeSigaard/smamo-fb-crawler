<?php

/*
Title: Data
Post Type: location
order: 2
*/



piklist('field',[
    'field' => 'name',
    'label' => 'Navn',
    'type' => 'text',
    'columns' => 8,
]);

piklist('field',[
    'field' => 'about',
    'label' => 'Om-tekst',
    'type' => 'textarea',
    'attributes' => [
        'rows' => 3,
        'cols' => 60,
    ],
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
    'field' => 'adress',
    'label' => 'Adresse',
    'type' => 'text',
    'columns' => 8,
]);

piklist('field',[
    'field' => 'phone',
    'label' => 'Telefonnummer',
    'type' => 'text',
    'columns' => 8,
]);


piklist('field',[
    'field' => 'website',
    'label' => 'Webadresse',
    'type' => 'text',
    'columns' => 8,
]);

$pic = get_post_meta(esc_attr($_GET['post']), 'picture', true);
if($pic){
    piklist('field',array(
        'type' => 'html',
        'label' => 'Ikon',
        'value' => '<img src="' . $pic . '"/>',
    ));
}
$pic = get_post_meta(esc_attr($_GET['post']), 'coverphoto', true);
if($pic){
    piklist('field',array(
        'type' => 'html',
        'label' => 'Cover',
        'value' => '<img src="' . $pic . '"/>',
    ));
}
