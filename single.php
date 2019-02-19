<?php get_header() ?>
<?php

  if ( have_posts() )
  the_post();

  global $post;
  // $location = get_field('featured_image_location');
 ?>
  <section>
      <?php get_template_part( 'parts/loop', 'single' ); ?>
  </section>
<?php get_footer() ?>

<!-- <div class="modal" tabindex="-1" role="dialog">
  <div class="modal-container" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> -->