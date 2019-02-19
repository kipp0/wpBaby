<?php
function wpbaby_add_custom_meta_box() {
   add_meta_box(
       'resume',       // $id
       'Resume',                  // $title
       'show_custom_meta_bo',  // $callback
       'application',             // $page
       'normal',                  // $context
       'high'                     // $priority
   );
}
add_action('add_meta_boxes', 'wpbaby_add_custom_meta_box');



//showing custom form fields
function show_custom_meta_box() {
    global $post;
    $id = $post->ID;
    $meta = get_post_meta($id);
    $file_url = $meta['application_upload_resume'][0];

    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'applicaiton_nonce' );

    ?>

    <!-- my custom value input -->
    <a href="<?= $file_url ?>" class="button" download>Download Resume</a>

    <?php
}
?>
