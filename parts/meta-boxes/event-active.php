<?php

/*
Title: Aktiv
Post Type: event
order: 3
context : side
*/

piklist('field', array(
    'type' => 'checkbox',
    'label' => 'Skjul i kalender',
    'field' => 'hide_in_calendar',
    'choices' => array(
        '1' => 'Skjul',
    ),
));