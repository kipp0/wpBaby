<?php get_template_part('parts/nav', 'logo-styles'); ?>
<a href="#main" class="scr-reader-text">Skip to Main Content</a>


<?php get_template_part( 'parts/info', 'bar' ); ?>
<nav class="topbar" role="navigation" aria-label="Main Navigation">

  <div class="nav-wrapper lg-container">
    
    <div class="temp-bar" style="width:100%;height:98px;background-color:transparent;position:absolute;top:0;z-index:99;"></div>

    <div class="nav-logo" style="z-index:999;">
      <a href="<?= home_url() ?>"><span class="scr-reader-text">Home</span></a>
    </div>

    <div class="nav">

      <!-- nav walker goes here -->
      <!-- add topbar2 if you have a need for a second menu -->
      <!-- <div class="menu-wrapper2">
          <?// php build_topbar2_nav() ?>
      </div> -->
      <div class="menu-wrapper">
          <?php build_topbar_nav() ?>
      </div>

      <button id="nav-btn" class="hamburger hamburger--squeeze" type="button" style="z-index:999;"
              aria-label="Menu" aria-controls="navigation">
        <span class="hamburger-box">
          <span class="hamburger-inner"></span>
        </span>
      </button>
    </div>

  </div>
</nav>
