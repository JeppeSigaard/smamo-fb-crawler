<?php

function smamo_rest_categories($data){

    // prepare post array
    $r = array();

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    $term_query = array(
        'taxonomy' => 'category',
        'hide_empty' => false,
        'orderby' => 'id',
        'order' => 'ASC',
    );

    $terms = get_terms($term_query);

    foreach($terms as $term){

        if(isset($data['featured']) && $data['featured']){

            $featured = get_term_meta( $term->term_id, 'category_featured', true);

            if(!$featured || '0' == $featured){
                continue;
            }

            if('2' == $featured){
                $include = false;
                $days = get_term_meta($term->term_id,'featured_day', false);
                foreach($days as $day){
                    if($day == date_i18n('w')){
                        $include = true;
                    }
                }

                if(!$include){
                    continue;
                }
            }
        }

        $r_term = array(
            'category_id' => $term->term_id,
        );

        if(!$fields || in_array('name', $fields)){
            $r_term['category_name'] = $term->name;
        }

        if(!$fields || in_array('featured', $fields)){
            $r_term['category_featured'] = get_term_meta( $term->term_id, 'category_featured', true);
        }


        if(!$fields || in_array('slug', $fields)){
            $r_term['category_slug'] = $term->slug;
            $r_term['slug'] = $term->slug;
        }

        if(!$fields || in_array('img', $fields)){
            $r_term['category_imgurl'] = get_term_meta( $term->term_id, 'category_thumbnail', true);
        }

        if(!$fields || in_array('count', $fields)){
            $r_term['location_count'] = $term->count;
        }

        if(!$fields || in_array('parent', $fields)){
            $r_term['category_parent'] = $term->parent;
        }

        if(!$fields || in_array('type', $fields)){
            $r_term['type'] = 'category';
        }

        if(!$fields || in_array('location_img', $fields)){
            $term_loc = get_posts(array(
                'post_type' => 'location',
                'posts_per_page' => -1,
                'orderby' => 'RAND',
                'order' => 'ASC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $term->term_id,
                    ),

                ),
            ));

            foreach($term_loc as $l){
               if(get_post_meta($l->ID,'coverphoto', true)){
                   $r_term['location_img'] = get_post_meta($l->ID,'coverphoto', true);
                   break;
               }
            }
        }

        $r[] = $r_term;
    }

    return $r;
}

function smamo_rest_category_single($data){
    // prepare post array
    $r = array();

    $fields = (isset($data['fields'])) ? explode(',', $data['fields']) : false;

    // Catch identifier
    $id = esc_attr($data['id']);

    $term = get_term_by('id', $id, 'category');

    if(!$term){
        $term = get_term_by('slug', $id, 'category');
    }

    if(!$term){
        return $r;
    }

    $term_loc = get_posts(array(
        'post_type' => 'location',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ),

        ),
    ));

    $r = array(
        'category_id' => $term->term_id,
    );

    if(!$fields || in_array('name', $fields)){
        $r['category_name'] = $term->name;
    }

    if(!$fields || in_array('slug', $fields)){
        $r['category_slug'] = $term->slug;
        $r['slug'] = $term->slug;
    }

    if(!$fields || in_array('img', $fields)){
        $r['category_imgurl'] = get_term_meta( $term->term_id, 'category_thumbnail', true);
    }

    if(!$fields || in_array('type', $fields)){
        $r['type'] = 'category';
    }

    if(!$fields || in_array('count', $fields)){
        $location_count = 0;

        foreach($term_loc as $l){
            $location_count ++;

            $r['locations'][] = smamo_rest_get_fields($l, $fields);
        }


        $r['location_count'] = $location_count;
    }

    if(!$fields || in_array('location_img', $fields)){
        foreach($term_loc as $l){
           if(get_post_meta($l->ID,'coverphoto', true)){
               $r['location_img'] = get_post_meta($l->ID,'coverphoto', true);
               break;
           }
        }
    }

    return $r;
}

add_action( 'rest_api_init', function () {

    register_rest_route( 'svendborg', 'categories', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_categories',
	) );

    register_rest_route( 'svendborg', 'categories/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'smamo_rest_category_single',
	) );

} );
