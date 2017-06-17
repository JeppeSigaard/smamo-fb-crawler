<?php

 function smamo_rest_get_fields($post, $fields = false){

    // default fields are always included
    $response_array = array(
        'id' => $post->ID,
    );

    // These are included, if no fields array is passed
    if(!$fields){
        $fields = array(
            'type','date','date_gmt','title',
            'start_time','end_time','fbid','parentname','parentid', 'parentfbid',
            'name','description','adress','phone','imgurl',
            'ticket_uri', 'website', 'images', 'picture', 'parentpicture',
            'categories', 'about', 'description', 'slug', 'coverphoto',
            'hours', 'hearts'
        );
    }

    // get some data or that field aight
    foreach($fields as $field){ if('' !== $field){
        $prfx = 'post_' . $field;

        if('slug' == $field && isset($post->post_name)){
            $response_array[$field] = $post->post_name;
        }

        elseif('categories' == $field){
            $cat_array = array();
            $terms = wp_get_post_terms($post->ID, 'category');
            foreach($terms as $term){
                $cat_array[] = array(
                    'category_id' => $term->term_id,
                    'type' => 'category',
                    'category_name' => $term->name,
                    'category_slug' => $term->slug,
                    'slug' => $term->slug,
                    'category_parent' => $term->parent,
                    'parent' => $term->parent,
                    'category_imgurl' => get_term_meta( $term->term_id, 'category_thumbnail', true),
                );
            }

            if(!empty($cat_array)){
                $response_array[$field] = $cat_array;
            }
        }

        elseif(isset($post->$field)){
            $response_array[$field] = $post->$field;
        }

        elseif(isset($post->$prfx)){
            $response_array[$field] = $post->$prfx;
        }

        elseif(get_post_meta($post->ID,$field,true)){
            $response_array[$field] = get_post_meta($post->ID,$field,true);
        }
    }}

   return $response_array;
}
