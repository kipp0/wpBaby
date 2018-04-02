<?php

  $featured_image = '';

  if ( $post instanceof WP_Post && is_front_page() ) {
    $featured_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
  }
 ?>

<!-- <?php //if(is_front_page()): ?>
  <style media="screen">
    .hero {
      background-image: url('<?=// $featured_image ?>');
    }
  </style>
<?php //endif; ?> -->
