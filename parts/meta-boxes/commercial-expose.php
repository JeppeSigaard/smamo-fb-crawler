<?php

/*
Title: Indstillinger for eksponering
Post Type: commercial
Context: side
*/


// Priority
piklist('field', array(
    'type' => 'number',
    'field' => 'priority',
    'label' => 'Prioritet',
    'post_type' => 'commercial',
    'columns' => 12,
));


// Expose
piklist('field', array(
    'type' => 'number',
    'field' => 'expose',
    'label' => 'Visninger',
    'post_type' => 'commercial',
    'columns' => 12,
));

// Limit
piklist('field', array(
    'type' => 'number',
    'field' => 'expose_limit',
    'label' => 'Grænse for visninger',
    'post_type' => 'commercial',
    'columns' => 12,
));

// end_date
piklist('field', array(
    'type' => 'date',
    'field' => 'end_date',
    'label' => 'Slutdato',
    'post_type' => 'commercial',
    'columns' => 12,
));
