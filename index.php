<?php get_header();

  $location = get_field('featured_image_location');
 ?>
  <section>hello
    <?php
    $page_obj = get_queried_object();
    var_dump($page_obj->slug);
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


<?php
// object(WP_Post)#355 (24)
// {
//   ["ID"]=> int(5)
//   ["post_author"]=> string(1) "1"
//   ["post_date"]=> string(19) "2018-04-04 16:06:03"
//   ["post_date_gmt"]=> string(19) "2018-04-04 16:06:03"
//   ["post_content"]=> string(8) "listings"
//   ["post_title"]=> string(8) "Listings"
//   ["post_excerpt"]=> string(0) ""
//   ["post_status"]=> string(7) "publish"
//   ["comment_status"]=> string(6) "closed"
//   ["ping_status"]=> string(6) "closed"
//   ["post_password"]=> string(0) ""
//   ["post_name"]=> string(8) "listings"
//   ["to_ping"]=> string(0) ""
//   ["pinged"]=> string(0) ""
//   ["post_modified"]=> string(19) "2018-04-04 16:06:03"
//   ["post_modified_gmt"]=> string(19) "2018-04-04 16:06:03"
//   ["post_content_filtered"]=> string(0) ""
//   ["post_parent"]=> int(0)
//   ["guid"]=> string(58) "//localhost:3000/geekpower/tara-billington/?page_id=5"
//   ["menu_order"]=> int(0)
//   ["post_type"]=> string(4) "page"
//   ["post_mime_type"]=> string(0) ""
//   ["comment_count"]=> string(1) "0"
//   ["filter"]=> string(3) "raw"
//   }


 ?>
