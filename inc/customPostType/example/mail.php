<?php

function email_after_example_post_creation( $data ) {


  $name = $data['your_name'];
  // your email action
  $subject = 'An enrollment was submitted';

  $msg = "$name would like to enroll";

  // Send email.
  $admin_email = "pierre@geekpower.ca";
  wp_mail( $admin_email, $subject, $msg );
}
