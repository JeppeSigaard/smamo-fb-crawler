<?php

/*
Title: Reklameskilte
Post Type: commercial
*/

// Title
piklist('field',array(
    'type' => 'text',
    'field' => 'title',
    'label' => 'Titel',
    'columns' => 8,
));

// Image
piklist('field', array(
    'type' => 'file',
    'field' => 'img',
    'label' => 'Billede',
    'post_type' => 'commercial',
    'options' => array(
        'modal_title' => 'Tilføj billede',
        'button' => 'Tilføj',
        'multiple' => false,
        'save' => 'url'
    )
));

// Link
piklist('field',array(
    'type' => 'url',
    'field' => 'link',
    'label' => 'Link',
    'columns' => 8,
));

// Facebook
piklist('field',array(
    'type' => 'url',
    'field' => 'fburl',
    'label' => 'Facebook link',
    'columns' => 8,
));

// Instagram
piklist('field',array(
    'type' => 'url',
    'field' => 'insta',
    'label' => 'Instagram link',
    'columns' => 8,
));

// piklist('field',array(
//     'type' => 'textarea',
//     'field' => 'subtitle',
//     'label' => 'Undertitel',
//     'columns' => 8,
//     'attributes' => array(
//         'rows' => 4,
//         'columns' => 12,
//     ),
// ));

// piklist('field', array(
//     'type' => 'select',
//     'add_more' => 'true',
//     'field' => 'for',
//     'label' => 'Vis reklame i',
//     'choices' => array(
//         '0' => 'Vælg placering(er)',
//         'search' => 'Søgefelt',
//         'event_single' => 'Enkeltbegivenheder',
//         'event_calendar' => 'Begivenhedskalender',
//         'location_single' => 'Enkelt sted',
//     ),
// ));


// Baggrund søgefelt
// piklist('field', array(
//     'type' => 'file',
//     'field' => 'img_search',
//     'label' => 'Tilføj baggrund til søgefelt',
//     'post_type' => 'commercial',
//     'options' => array(
//         'modal_title' => 'Tilføj billede',
//         'button' => 'Tilføj',
//         'multiple' => false,
//         'save' => 'url'
//     )
// ));

// Begivenhedsliste
// piklist('field', array(
//     'type' => 'file',
//     'field' => 'img_event_calendar',
//     'label' => 'Tilføj billede til begivenhedsliste, enkeltbegivenhed og enkeltsted',
//     'post_type' => 'commercial',
//     'options' => array(
//         'modal_title' => 'Tilføj billede',
//         'button' => 'Tilføj',
//         'multiple' => false,
//         'save' => 'url'
//     )
// ));
