<?php

/*
Title: Kategori indstillinger
Taxonomy: category
*/

// Registers a thumbnail field
// to the category taxonomy
piklist('field', array(
    'type' => 'file',
    'field' => 'category_thumbnail',
    'label' => 'Tilføj billede(r)',
    'description' => 'Dette uploader et kategori billede.',
    'taxonomy' => 'category',
    'options' => array (
        'modal_title' => 'Tilføj billede(r)',
        'button' => 'Tilføj',
        'save' => 'url',
    ),
));

// Registers a feature field
// to the category taxonomy
piklist('field', array(
    'type' => 'select',
    'value' => '0',
    'field' => 'category_featured',
    'description' => 'Præsenteret kategori',
    'taxonomy' => 'category',
    'choices' => array(
        '0' => 'Aldrig',
        '1' => 'Altid',
        '2' => 'På en dag',
    ),
));

piklist('field', array(
    'type' => 'select',
    'field' => 'featured_day',
    'label' => 'Dag',
    'add_more' => true,
    'choices' => array(
        '1' => 'Mandag',
        '2' => 'Tirsdag',
        '3' => 'Onsdag',
        '4' => 'Torsdag',
        '5' => 'Fredag',
        '6' => 'Lørdag',
        '0' => 'Søndag',
    ),
    'conditions' => array(
        array(
            'field' => 'category_featured',
            'value' => '2',
        ),
    ),
));
