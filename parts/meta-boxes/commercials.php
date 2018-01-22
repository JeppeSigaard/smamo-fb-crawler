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

// Knapper
piklist('field', array(

	'type'     => 'group',
	'field'    => 'buttons',
	'label'    => 'Knapper',
	'add_more' => true,
	'fields'   => array(

    // Button Icon
    array(
      'type'    => 'radio',
      'field'   => 'button_icon',
      'label'   => 'Knap Ikon',
      'choices' => array(
        'web'   => 'Website',
        'fb'    => 'Facebook',
        'ig'    => 'Instagram'
      )
    ),

    // Button text
    array(
      'type'  => 'text',
      'field' => 'button_text',
      'label' => 'Knap tekst',
    ),

    // Button URL
    array(
      'type'  => 'text',
      'field' => 'button_url',
      'label' => 'Knap URL',
    )

  )

));
