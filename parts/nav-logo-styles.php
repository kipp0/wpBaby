<?php
// uncomment if you are using the customizer

$logo_id = get_theme_mod('custom_logo');
$logo_url = gp_get_img_url($logo_id);

$mobile_nav_logo_url = get_theme_mod('mobile_nav_logo');

if ( !isset($logo_url) ) {
  $logo_url = get_template_directory_uri() . '/assets/images/nav-logo.png';
}

$logo2_url = get_template_directory_uri() . '/assets/images/jnd.png';

?>
<?php if ($mobile_nav_logo_url): ?>
  <style media="screen">
  @media only screen and (max-width: 1340px) {

    .nav-logo {
      background-image: url('<?= $mobile_nav_logo_url; ?>')!important;
      width: 240px !important;
    }
  }
  </style>
<?php endif; ?>

<style media="screen">
  .logo2 {
    background-image: url('<?= $logo2_url ?>');
    background-position: center;
    background-size: contain;
    background-repeat: no-repeat;
    width: 370px;
  }
  .nav-logo {
    background-image: url('<?= $logo_url; ?>');
    /* transition: all 500ms cubic-bezier(0.000, 0.995, 0.990, 1.000); */
  }
</style>
