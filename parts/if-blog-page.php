<?php

  $featured_image = '';

  $query_object = get_queried_object();

  if ($query_object instanceof WP_Term) {
    $featured_image = get_field( 'featured_image', 'category_' . $query_object->term_id);
  }
 ?>


<!-- <?php// if ( !is_front_page() ) : ?>
  <style media="screen">
    .hero {
      background-image: url('<?= //$featured_image ?>');
    }
  </style>
<?php// endif; ?> -->
