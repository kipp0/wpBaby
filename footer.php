<?php
  $url = get_template_directory_uri();

  $bg_image = get_theme_mod('footer_logo_image');
  $bg_image_style = "style='background-image:url( $bg_image )'";
 ?>

    </main>
    <footer>
      <div class="footer-stuff bg-grey">
        <div class="container">
          <div class="row">
            <div class="col s6 footer-top-left">
              <div class="logo" <?= $bg_image_style ?>>
                <a href="<?= bloginfo('url') ?>"><span class="scr-reader-text">Home</span></a>
              </div>
              <div class="details">
                <p class='bold'>Mailing List</p>
                <p class='hide-phone'>Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
              </div>
              <div class="subscribe">
              <form action="ajax-mc-email">

                <div class="input-group">
                  <input type="email" class='input' name='EMAIL' placeholder='Enter your email address'>
                  <div class="right">
                    <button type="submit" class='btn text-white bg-blue'>Subscribe</button>
                  </div>
                </div>
              </form>
              </div>
            </div>
            <div class="col s6 flex ai-center footer-top-right">
              <div class="hide-phone flex jc-between full-width content">

                <div class="learn-more">
                  <p class='text-14 bold mb-2'>Menu 1</p>
                  <?php build_menu_1() ?>
                </div>
                <div class="resources">
                    <p class='text-14 bold mb-2'>Menu 2</p>
                    <?php build_menu_3() ?>
                  </div>
                  <div class="partners">
                      <p class='text-14 bold mb-2'>Menu 3</p>
                  <?php build_menu_3() ?>
                </div> 
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="copy bg-dark-grey">
        <div class="container">
          <div class="row">
            <div class="col s8">
              <p>Copyright © 2018 Kipp0. A Wordpress Template for all.</p>
              <p> Privacy Policy – Terms & Conditions – License Agreement – Legal – Permissions – Sitemap</p>
            </div>
            <div class="col s4">
              <ul class='social'>
                <li class="item">
                  <a href="#"><i class="fab fa-instagram"></i></a>
                </li>
                <li class="item">
                  <a href="#"><i class="fab fa-twitter"></i></a>
                </li>
                <li class="item">
                  <a href="#"><i class="fab fa-youtube"></i></a>
                </li>
                <li class="item">
                  <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </li>
                <li class="item">
                  <a href="#"><i class="fab fa-facebook-f"></i></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <?php wp_footer(); ?>
  </body>
</html>
