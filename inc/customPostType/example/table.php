<?php

function clients_custom_column_content( $column ) {

    // Get the post object for this row so we can output relevant data
    global $post;

    // Check to see if $column matches our custom column names
    switch ( $column ) {

        case 'name' :
            // Retrieve post meta
            $first_name = get_post_meta( $post->ID, 'client_first_name', true );
            $last_name = get_post_meta( $post->ID, 'client_last_name', true );

            // Echo output and then include break statement
            echo ( !empty( $first_name && $last_name ) ? "$first_name $last_name": '' );
            break;


        case 'phone_number' :
            // Retrieve post meta
            $phone_number = get_post_meta( $post->ID, 'client_phone_number', true );

            // Echo output and then include break statement
            echo ( !empty( $phone_number ) ? $phone_number : '' );
            break;


        case 'email' :
            // Retrieve post meta
            $email = get_post_meta( $post->ID, 'client_email', true );

            // Echo output and then include break statement
            echo ( !empty( $email ) ? $email : '' );
            break;

        case 'address' :
            // Retrieve post meta
            $address = get_post_meta( $post->ID, 'client_address', true );

            // Echo output and then include break statement
            echo ( !empty( $address ) ? $address : '' );
            break;


    }
}

// Let WordPress know to use our action
add_action( 'manage_example_posts_custom_column', 'example_custom_column_content' );