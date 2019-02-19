<?php
$bg_image = "";

if (has_post_thumbnail($post->ID)) {
  $bg_image = gp_get_img_url_from_post($post->ID);
}
$bg_styles = "background-image: url('$bg_image')";
$hero_title = get_field('hero_title')? get_field('hero_title') : 'title';
$hero_text = get_field('hero_text')? get_field('hero_text') : 'text';


 ?>

<div id="hero" style="<?= $bg_styles ?>">
  <div class="overlay"></div>
  <div class="container-fluid">

    <div class="row col s10 mx-auto">
      <div class="col s6 animate">
        <h1 class="text-white bar"><?= $hero_title ?></h1>
        <div class="text text-white">
          <p class="text-white"><?= $hero_text ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
