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
        '0' => 'nej',
        '1' => 'ja',
    ),
));
