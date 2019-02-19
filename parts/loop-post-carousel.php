<?php


  $thumb = get_post_thumbnail_id($p->ID);

  $img_url = gp_get_img_url($thumb, 'large');
  $title = get_the_title($p);
  $location = get_post_meta($p->ID, 'displayed_address', true);
  $price = get_post_meta($p->ID, 'price', true);
  $bathrooms = get_post_meta($p->ID, 'bathrooms', true);
  $bedrooms = get_post_meta($p->ID, 'bedrooms', true);
  $sqft = get_post_meta($p->ID, 'sqft', true);
  $exposure = 'Northern';
  $url = $p->guid;

 ?>

<div class="listing-container">
  <div class="images">
    <div class="featured-image" style="<?= gp_get_img_style($img_url) ?>"></div>
    <div class="top-fi"></div>
    <div class="bottom-fi"></div>
  </div>
  <div class="listing-details">
    <?php // NOTE: all listing details

    ?>
    <div class="mls">
      <h3 class="text">FEATURED PROPERTIES</h3>
      <h1><?= $title ?></h1>
      <h1><strong><?= $location ?></strong></h1>
    </div>
    <div class="price">
        <h1>$ <?= number_format($price) ?></h1>
    </div>

    <div class="description">
      <h3>Description</h3>
      <?php the_content(); ?>
    </div>
    <div class="featured-details">
      <h3>Features And Details</h3>
      <?php foreach ($metas as $key => $value) : ?>
        <?php
        $val = $value[0];
        $desc = "";
        $icon = "";

        if ($val != "" &&
            $key != "mls_number" &&
            $key != "price" &&
            $key != "mls_number" &&
            $key != "_edit_last" &&
            $key != "_edit_lock" &&
            $key != "_thumbnail_id" &&
            $key != "attachments" &&
            $key != "displayed_address" ) :
        //   # code...

          if ($key == 'sqft') {
            # code...
            $desc = "sqft";
            $icon = "arrows-alt";
          } elseif ($key == 'bedrooms') {
            # code...
            $desc = "Bedrooms";
            $icon = "bed";
          } elseif ($key == 'bathrooms') {
            # code...
            $desc = "Bathrooms";
            $icon = "bath";
          }
         ?>
         <div class="details">
           <label>
             <i class="fas fa-<?= $icon ?>"></i>
             <span><?= $val ?> <?= $desc ?></span>
           </label>
         </div>
      <?php endif;endforeach; ?>
    </div>
    <div class="btns">
      <a href="<?= $url ?>">VIEW LISTING</a>
    </div>
  </div>
</div>
