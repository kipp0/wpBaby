<?php

/**
 * Simply adds an ID field to image, for people who are scared of computers, and
 * have a hard time reading the URL. (also, URL doesn't show image ID when editing another post).
 *
 * @param         $fields
 * @param WP_Post $post - So this is a WP_Post object, but in the save hook below, its an array.
 */
function gp_attachment_fields_to_edit( $fields, $post ) {

    $new_fields = array();
    $post_id    = isset( $post->ID ) ? $post->ID : false;

    $gp_id_field = array(
        'label' => 'Attachment ID',
        'input' => 'html',
        'html' => '<input disabled value="' . $post_id . '">',
        'helps' => 'This is used in some shortcodes, ie. image="' . $post_id . '"',
    );

    $new_fields[ 'gp_id' ] = $gp_id_field;

    // make ours show up at the top
    $fields = array_merge( $new_fields, $fields );

    return $fields;
}

// high priority lets us determine the order we want
add_filter( 'attachment_fields_to_edit', 'gp_attachment_fields_to_edit', 999999, 2 );
