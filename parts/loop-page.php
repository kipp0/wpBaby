<?php
/**
 * Template part for displaying page content in page.php
 */
?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/WebPage">
<!-- <header><?= the_title(); ?></header> -->
    <?php the_content(); ?>
</div>  
