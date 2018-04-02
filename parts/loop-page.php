<?php
/**
 * Template part for displaying page content in page.php
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/WebPage">
  <header><?= the_title(); ?></header>
  <div class="page-content" itemprop="pageBody" aria-label="Page Content">
    <?php the_content(); ?>
  </div> <!-- end article section -->
  <footer></footer>
</article> <!-- end article -->
