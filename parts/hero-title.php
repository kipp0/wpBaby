
<?php

  global $post;

  $title = 'Title';
  $sub_title = 'Sub Title';
  $text = 'Some Text That is important';

?>
<section>
   <?=  ($title != '') ? "<h1 aria-label='Page Title' class='title'>$title</h1>" : ""; ?>
   <?=  ($sub_title != '') ? "<h2 aria-label='Page Sub Title' class='sub-title'>$sub_title</h2>" : ""; ?>
   <?=  ($text != '') ? "<p class=''>$text</p>" : ""; ?>
</section>


<?//php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <?//php get_template_part( 'parts/loop', 'page' ); ?>

<?//php endwhile; endif; ?>
