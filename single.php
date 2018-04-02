<?php get_header() ?>
<?php

  if ( have_posts() )
  the_post();

  global $post;
  $location = get_field('featured_image_location');
 ?>
  <div class="yellow-banner"></div>
  <div class="location"><p><?= $location ?></p></div>
  <section>
      <?php get_template_part( 'parts/loop', 'single' ); ?>
  </section>
<?php get_footer() ?>
