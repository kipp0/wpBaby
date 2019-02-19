<?php add_hero_class(); ?>
<?php get_header() ?>
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
  $content = get_the_content();
  $temp_dir = get_template_directory_uri();
  $star_url = $temp_dir . "/assets/images/star.png";
  $rating = 3.4;
  
  wp_register_script( 'some_handle', $temp_dir .'/assets/scripts/dist/stars.js' );

 // Localize the script with new data
  $post = array(
     'rating' => $rating,
 );
 wp_localize_script( 'some_handle', 'post', $post );

// Enqueued script with localized data.
wp_enqueue_script( 'some_handle' );

 ?>
<section>
hello
    <article>




    <div class="grid hero flex ai-center bg-blue">
    <div class="container">

        <div class="shortcode row">
            <div class="col s12">
                <h2 class='text-center mb-1 text-white'><?= $post_type ?></h2>
                <h3 class='text-center text-white'><?= $slogan ?></h3>
            </div>
        </div>
    </div>
    </div>

    <div class="grid breadcrumbs">
    <div class="container bg-white flex p0">
        <div class="crumb home text-white"><span>Home</span>

        </div>
        
        <div class="crumb post-type"><span><?= $post_type ?></span>

        </div>
        <div class="crumb post-name"><span><?= $title ?></span>
        
        </div>
    </div>
    </div>
    <div class="position-relative">

    </div>
    <div class="grid header">
    <div class="container">

        <div class="row mt-5">
            <div class="col s12">
                <div class="stars mb-1">
                <i class='fas fa-star'><img class='star' src='<?= $star_url ?>' alt=''></i>
                <i class='fas fa-star'><img class='star' src='<?= $star_url ?>' alt=''></i>
                <i class='fas fa-star'><img class='star' src='<?= $star_url ?>' alt=''></i>
                <i class='fas fa-star'><img class='star' src='<?= $star_url ?>' alt=''></i>
                <i class='fas fa-star'><img class='star' src='<?= $star_url ?>' alt=''></i>
                </div>
            </div>
            <div class="col s12">
                <h2 class='bold'>Alexandra Allen</h2>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row bar"></div>
    </div>
    <div class="container">
        <div class="row col s12">
            <p class='status'>Frequent User</p>
            <p class='med-quotes text-blue'>“</p>
        </div>
        <div class="row">
            <div class="col s12">
            <div class="featured-img-container">
                <img class='circle' src="<?= $img_url ?>" alt="">
            </div>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
                    <?php get_template_part( 'parts/loop', 'single' ); ?>

                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>



    </div>

    
  </article>
</section>
<?php get_footer() ?>