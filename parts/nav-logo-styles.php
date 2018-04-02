<?php
// uncomment if you are using the customizer

// $logo_url = get_custom_logo();
//
// if ( isset($logo_url) ) {
//   $logo_url = $logo_url;
// } else{
//   $logo_url = get_template_directory_uri() . 'assets/images/logo.png';
// };

$active_logo_url = '/assets/images/mobile-logo-white.png';
$logo_url =  '/assets/images/nav-logo.png';

?>
<style media="screen">
  .nav-logo {
    background-image: url('<?= get_template_directory_uri() . $logo_url ?>');
  }
  .active .nav-logo {
    background-image: url('<?= get_template_directory_uri() . $active_logo; ?>');
    transition: all 500ms cubic-bezier(0.000, 0.995, 0.990, 1.000);
  }
</style>
