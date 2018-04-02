<?php
/**
 * Template part for displaying a single post
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

	<header class="article-header">
		<h2 class="entry-title single-title" itemprop="headline" aria-label="Post Title" style="margin-bottom:1rem;"><?php the_title(); ?></h2>
		<?php get_template_part( 'parts/content', 'byline' ); ?>
  </header> <!-- end article header -->

  <div class="article-content" itemprop="articleBody">
		<?php the_post_thumbnail('full'); ?>
		<?php the_content(); ?>
	</div> <!-- end article section -->

	<!-- footer class="article-footer">
		<?php //wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wpBaby' ), 'after'  => '</div>' ) ); ?>
		<p class="tags"><?php //the_tags('<span class="tags-title">' . __( 'Tags:', 'wpBaby' ) . '</span> ', ', ', ''); ?></p>
	</footer --> <!-- end article footer -->

	<?php // comments_template(); ?>

</article> <!-- end article -->
