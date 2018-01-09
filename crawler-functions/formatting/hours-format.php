<?php

// Format hour data
function format_hours( $data ) {
    $resp = array();
    foreach ( $data as $key => $value ) {
        $day = substr( $key, 0, 3 );
        if ( $resp[ $day ] === null ) { $resp[ $day ] = [ $value ]; }
        else { array_push( $resp[ $day ], $value ); }
    } return $resp;
}
