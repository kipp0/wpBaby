<?php
/**
 * The template for displaying 404 (page not found) pages.
 *
 * For more info: https://codex.wordpress.org/Creating_an_Error_404_Page
 */

get_header(); ?>
<section>
  <article>
    <div class="container">
      <div class="row">
        <div class="col s12">
          <h1 class="text-grey text-center"><?php _e( 'Epic 404 - Article Not Found', 'wpBabywp' ); ?></h1>
          <p class='text-center'><?php _e( 'The article you were looking for was not found, but maybe try looking again!', 'wpBabywp' ); ?></p>
        </div>
      </div>
    </div>
  </article>
</section>

<?php get_footer(); ?>
