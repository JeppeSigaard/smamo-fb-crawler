<?php
/*
Title: Crawl Facebook for steder og begivenheder, opdater gammelt indhold mm.
Setting: crawl_settings
*/

piklist('field', array(
    'type' => 'select',
    'field' => 'sammo_crawl_do',
    'label' => 'Jeg vil gerne',
    'choices' => array(
        'crawl' => 'Opdatere Sted(er)',
        'events' => 'Opdatere Begivenhed(er)',
        'crawl_events' => 'Opdatere steder og begivenheder',
    )
));

piklist('field', array(
    'type' => 'text',
    'field' => 'sammo_crawl_id',
    'label' => 'Evt. ID (blank for alle)',
    'attributes' => array(
      'placeholder' => 'Alle'
    )
  ));

piklist('field', array(
    'type' => 'select',
    'field' => 'sammo_crawl_update_old',
    'label' => 'Overskriv data?',
    'choices' => array(
        'false' => 'Overskriv ikke gammel data',
        'true' => 'Overskriv gammel data',
    )
));
