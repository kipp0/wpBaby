<?php

  $featured_image = '';

  if ( $post instanceof WP_Post && !is_front_page() ) {
    // $featured_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
    $featured_image = gp_get_img_url(get_post_thumbnail_id($post->ID),'full');
  }
 ?>


<?php if ( !is_front_page() ) : ?>
  <style media="screen">
    .hero {
      background-image: url('<?php echo $featured_image ?>');
    }
  </style>
<?php endif; ?>
