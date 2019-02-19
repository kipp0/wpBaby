<?
// Template Name: Sample
?>
<?php get_header(); global $post; ?>
<?php get_template_part('parts/hero', 'section'); ?>

<section>
	<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
		<div class="article-content" itemprop="articleBody">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	      <?php the_content() ?>

	    <?php endwhile; endif; ?>
		</div>
	</article>
</section>


<?php get_footer(); ?>
