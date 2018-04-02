<?php
/**
 * The template for displaying 404 (page not found) pages.
 *
 * For more info: https://codex.wordpress.org/Creating_an_Error_404_Page
 */

get_header(); ?>
<section>
  <article class="">

    <header class="article-header">
      <h1 class="text-grey"><?php _e( 'Epic 404 - Article Not Found', 'jointswp' ); ?></h1>
    </header> <!-- end article header -->
    <footer>
      <p><?php _e( 'The article you were looking for was not found, but maybe try looking again!', 'jointswp' ); ?></p>
    </footer>
  </article>
</section>

<?php get_footer(); ?>
