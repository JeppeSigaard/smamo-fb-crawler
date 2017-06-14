<?php

/*
Title: Reklameskilte
Post Type: commercial
*/


// Link
piklist('field',array(
    'type' => 'url',
    'field' => 'link',
    'label' => 'Link',
    'columns' => 8,
));

piklist('field',array(
    'type' => 'text',
    'field' => 'title',
    'label' => 'Titel',
    'columns' => 8,
));


piklist('field',array(
    'type' => 'textarea',
    'field' => 'subtitle',
    'label' => 'Undertitel',
    'columns' => 8,
    'attributes' => array(
        'rows' => 4,
        'columns' => 12,
    ),
));

// Logo
piklist('field', array(
    'type' => 'file',
    'field' => 'img_logo',
    'label' => 'Tilføj logo',
    'post_type' => 'commercial',
    'options' => array(
        'modal_title' => 'Tilføj billede',
        'button' => 'Tilføj',
        'multiple' => false,
        'save' => 'url'
    )
));

// Baggrund søgefelt
piklist('field', array(
    'type' => 'file',
    'field' => 'img_search',
    'label' => 'Tilføj baggrund til søgefelt',
    'post_type' => 'commercial',
    'options' => array(
        'modal_title' => 'Tilføj billede',
        'button' => 'Tilføj',
        'multiple' => false,
        'save' => 'url'
    )
));

// Begivenhedsliste
piklist('field', array(
    'type' => 'file',
    'field' => 'img_event_calendar',
    'label' => 'Tilføj billede til begivenhedsliste',
    'post_type' => 'commercial',
    'options' => array(
        'modal_title' => 'Tilføj billede',
        'button' => 'Tilføj',
        'multiple' => false,
        'save' => 'url'
    )
));

// Begivenhedsliste
piklist('field', array(
    'type' => 'file',
    'field' => 'img_event_single',
    'label' => 'Tilføj billede til Enkeltbegivenhed',
    'post_type' => 'commercial',
    'options' => array(
        'modal_title' => 'Tilføj billede',
        'button' => 'Tilføj',
        'multiple' => false,
        'save' => 'url'
    )
));

// Stedliste
piklist('field', array(
    'type' => 'file',
    'field' => 'img_location_single',
    'label' => 'Tilføj billede til Enkeltbegivenhed',
    'post_type' => 'commercial',
    'options' => array(
        'modal_title' => 'Tilføj billede',
        'button' => 'Tilføj',
        'multiple' => false,
        'save' => 'url'
    )
));
