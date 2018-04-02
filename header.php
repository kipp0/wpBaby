<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!-- Force IE to use the latest rendering engine available -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- If Site Icon isn't set in customizer -->
		<?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) :?>
			<!-- Icons & Favicons -->
			<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
			<link href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-icon-touch.png" rel="apple-touch-icon" />
    <?php endif; ?>
    <?php wp_head(); ?>

    <title><?= get_the_title() ?></title>
    <?php
      global $post;

      // logic for page hero
      $featured_image = '';
      get_template_part( 'parts/if', 'front-page' );
      get_template_part( 'parts/if', 'blog-page' );
      get_template_part( 'parts/if', 'page' );
    ?>
      <style media="screen">
        .hero figure {
          background-image: url('<?= $featured_image ?>');
        }
      </style>
  </head>
  <body <?php body_class(); ?>>
    <header>
       <?php get_template_part( 'parts/nav', 'topbar' ); ?>
       <div id="hero" class="hero">
         <figure aria-label="Hero Image"></figure>
         <div class="overlay"></div>
         <?= get_template_part('parts/hero', 'title') ?>
       </div>
    </header>
    <main id="main" aria-label="Main Content">
