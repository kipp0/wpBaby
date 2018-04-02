

<?php get_header() ?>
  <section>


    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/WebPage">

      <section class="page-content" itemprop="articleBody" aria-label="Page Content">
        <div class="title-container">
            <h1><?= get_field('frontpage_title') ?></h1>
        </div>
        <div class="btn-container">
          <?= get_field('frontpage_button') ?>
        </div>
      </section> <!-- end article section -->

    </article> <!-- end article -->

  </section>
<?php get_footer() ?>
