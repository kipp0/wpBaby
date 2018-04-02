<?php get_template_part('parts/nav', 'logo-styles'); ?>

<a href="#main" class="scr-reader-text">Skip to Main Content</a>
<nav class="topbar" role="navigation" aria-label="Main Navigation">

  <div class="nav-wrapper">

    <div class="temp-bar" style="width:100%;height:98px;background-color:transparent;position:absolute;top:0;z-index:99;"></div>

    <div class="nav-logo" style="z-index:999;">
      <a href="<?= home_url() ?>"><span class="scr-reader-text">Home</span></a>
    </div>

    <div class="nav">

      <!-- nav walker goes here -->
      <?php build_topbar_nav() ?>

      <button id="nav-btn" class="hamburger hamburger--spin" type="button" style="z-index:999;"
              aria-label="Menu" aria-controls="navigation">
        <span class="hamburger-box">
          <span class="hamburger-inner"></span>
        </span>
      </button>
    </div>

  </div>
</nav>
