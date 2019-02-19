<?php
/**
 * Template part for displaying posts
 *
 * Used for single, index, archive, search.
 */
 /*the_title(); */

 $temp = get_the_date('d M y');
 $date = explode(" ", $temp);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article">

	<header class="article-header">
		<h2><?= $date[0]?></h2><p><?= $date[1]?> <?= $date[2]?></p>
		<?php get_template_part( 'parts/content', 'byline' ); ?>
	</header> <!-- end article header -->

	<section class="entry-content" itemprop="articleBody">
		<a href="<?php the_permalink() ?>" aria-label="<?= $date[1] ?> <?= $date[0] ?> 20<?= $date[2] ?>"><?php //the_post_thumbnail('full'); ?></a>
    <?php global $post;
     ?>
    <h2 aria-label="Post Title"><?php the_title(); ?></h2>
		<?php the_excerpt('<button class="btn text-white bg-blue">' . __( 'Read more...', 'wpBaby' ) . '</button>'); ?>
	</section> <!-- end article section -->

	<!-- footer class="article-footer">
    	<p class="tags"><?php //the_tags('<span class="tags-title">' . __('Tags:', 'wpBabytheme') . '</span> ', ', ', ''); ?></p>
	</footer --> <!-- end article footer -->

</article> <!-- end article -->
