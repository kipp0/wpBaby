<?php

  $featured_image = '';

  if ( $post instanceof WP_Post && is_front_page() ) {
    $featured_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
  }

  if (!$featured_image) {
    # code...
    $hero_bg = get_field('hero_bg');
  }
 ?>

<?php if(is_front_page()): ?>
  <style media="screen">
    .hero {
      background-image: url('<?php echo $featured_image ?>');
    }
    .hero-btn {
      display: block;
    }
  </style>
<?php endif; ?>
