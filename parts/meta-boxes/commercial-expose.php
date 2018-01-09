<?php

/*
SqTitle: Indstillinger for eksponering
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


// CO Expose
piklist('field', array(
    'type' => 'number',
    'field' => 'co_expose',
    'label' => 'Visninger',
    'post_type' => 'commercial',
    'columns' => 12,
));

// Expose
piklist('field', array(
    'type' => 'number',
    'field' => 'expose',
    'label' => 'Primære Visninger',
    'post_type' => 'commercial',
    'columns' => 12,
));

// Limit
piklist('field', array(
    'type' => 'number',
    'field' => 'expose_limit',
    'label' => 'Grænse for primære visninger',
    'post_type' => 'commercial',
    'columns' => 12,
));

// start_date
piklist('field', array(
    'type' => 'datepicker',
    'field' => 'start_date',
    'label' => 'Startdato',
    'post_type' => 'commercial',
    'columns' => 12,
    'options' => array(
      'dateFormat' => 'yy-m-d'
    )
));

// end_date
piklist('field', array(
    'type' => 'datepicker',
    'field' => 'end_date',
    'label' => 'Slutdato',
    'post_type' => 'commercial',
    'columns' => 12,
    'options' => array(
      'dateFormat' => 'yy-m-d'
    )
));
