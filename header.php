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
      <!-- fontawesome css include -->
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css" integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous">
    <?php endif; ?>
    <?php wp_head(); ?>
    <title><?= get_the_title() ?></title>
  </head>
  <body <?php body_class(); ?>>
    <header>
       <?php get_template_part( 'parts/nav', 'topbar' ); ?>
    </header>
    <main id="main" aria-label="Main Content">
