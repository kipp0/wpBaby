
<?php
/**
 * Template part for displaying posts
 *
 * Used for single, index, archive, search.
 */
 /*the_title(); */
  global $post;

  $img_url = "";

  if (has_post_thumbnail($post->ID)) {
    $img_url = gp_get_img_url_from_post($post->ID);
  }
  $temp = get_the_date('d M y');
  $date = explode(" ", $temp);

  $post_type = get_post_type();
  $slogan = "Alone we can do so little, together we can do so much.";
  $title = get_the_title();
  $content = the_content();
  $temp_dir = get_template_directory_uri();

 ?>

<?= do_shortcode($content) ?>
