<?php add_hero_class(); ?>
<?php get_header() ?>
<section>
  <article class="">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

          <?php get_template_part( 'parts/loop', 'page' ); ?>

      <?php endwhile; endif; ?>
  </article>
</section>
<?php get_footer() ?>