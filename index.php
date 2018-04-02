<?php get_header();

  $location = get_field('featured_image_location');
 ?>
  <div class="yellow-banner"></div>
  <div class="location"><p><?= $location ?></p></div>
  <section>
    <?php
    $page_obj = get_queried_object();
    $cat_slug = $page_obj->slug;
    // var_dump($page_obj);
    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
    $q = new WP_Query(
      array(
                'paged'         => $paged,
                'category_name' => $cat_slug,
                'orderby'      => array('date' => 'desc'),
                'post_type'     => 'post',
                'post_status'   => 'publish',
                'posts_per_page' => 5
            )
    );


     ?>
    <?php if ($q->have_posts()) : while ($q->have_posts()) : $q->the_post(); ?>

    <!-- To see additional archive styles, visit the /parts directory -->
    <?php get_template_part( 'parts/loop', 'post' ); ?>

  <?php endwhile; ?>


  <?php
    //structure of "format" depends on whether we're using pretty permalinks
     // if( get_option('permalink_structure') ) {
     //   $format = '?paged=%#%';
     // } else {
       $format = 'page/%#%/';
     // }
     $big = 999999999; // need an unlikely integer
     $base = $format =='?paged=%#%' ? $base = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ) : $base = @add_query_arg('paged','%#%');
    $args = [
      'query' => $q,
      'base' => $base,
      'format' => $format
    ];

    echo get_paginated_numbers($args);
  ?>
  <?php else : ?>
    <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
  <?php endif; ?>
  </section>
<?php get_footer() ?>
