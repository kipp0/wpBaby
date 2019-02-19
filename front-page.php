<?//php add_hero_class(); ?>
<?php get_header() ?>
<section>
  <article class="">
    <div class="">

    <div class="btn-group">
      <button class="btn bg-blue text-white">test</button>
      <button class="btn bg-blue text-white">test</button>
      <button class="btn bg-blue text-white">test</button>
    </div>
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

          <?php the_content(); ?>

      <?php endwhile; endif; ?>
    </div>
  </article>
</section>
<?php get_footer() ?>
