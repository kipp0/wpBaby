<?php

function application_form_process() {
    // do whatever you need in order to process the form.
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    $res = array(
      'err' => false,
      'msg' => 'Application has been submitted'
    );

    $data = $_POST;
    $resume = $_FILES['upload_resume'];

    $upload_dir = wp_upload_dir();
    $content_dir = $upload_dir['basedir'] . '/..';
    $target_dir_url = $content_dir . '/applications';


    $full_name = sanitize_text_field($data['full_name']);
    $email = sanitize_email($data['email_address']);
    $phone_number = $data['phone_number'];
    $linkedIn = sanitize_text_field($data['linkedin_url']);
    $twitter = sanitize_text_field($data['twiitter_url']);
    $position_of_interest = str_replace(',', " ", $data['position_of_interest']);
    $accredidation = sanitize_text_field($data['accredidation']);
    $additional_information = sanitize_text_field($data['additional_information']);

    if ($full_name == '' ||
        $email == '' ||
        $position_of_interest == '' ||
        $phone_number == '' ||
        $resume['error'] == 4 ||
        $resume['size'] == 0) {

          $res['msg'] = "all required fields must be submitted";
          $res['err'] = true;

          echo json_encode($res);
          exit;
    }

    $post_id = wp_insert_post(array (
        'post_type' => 'application',
        'post_title' => "$position_of_interest Application",
        'post_content' => $additional_information,
        'post_status' => 'publish'
    ));
    $target_dir_url = $target_dir_url . "/$post_id/";
    $target_file_url = $target_dir_url . basename($resume["name"]);

    if( ! file_exists( $target_dir_url ) ) {

      if ( ! wp_mkdir_p( $target_dir_url ) ) {

        $res['msg'] = 'error couldn\'t make directory...'. $move_success;
        $res['err'] = true;

        echo json_encode($res);
        exit;
      }
    }


    $move_success = move_uploaded_file($resume["tmp_name"], $target_file_url);

    if (!$move_success) {
      $res['msg'] = "Please upload only .docx or pdf files: $move_success";
      $res['err'] = true;

      echo json_encode($res);
      exit;
    }

    if ($post_id) {
        // insert post meta

        update_post_meta($post_id, 'application_full_name', $full_name);
        update_post_meta($post_id, 'application_email', $email);
        update_post_meta($post_id, 'application_phone_number', $phone_number);
        update_post_meta($post_id, 'application_linkedin_url', $linkedIn);
        update_post_meta($post_id, 'application_twiitter_url', $twitter);
        update_post_meta($post_id, 'application_position_of_interest', $position_of_interest);
        update_post_meta($post_id, 'application_accredidation', $accredidation);
        update_post_meta($post_id, 'application_upload_resume', $resume["tmp_name"]);
        update_post_meta($post_id, 'application_additional_information', $additional_information);

        email_after_application_post_creation($data);
    } else {

      $res['msg'] = "Problem submitting form";
      $res['err'] = true;

      echo json_encode($res);
      exit;
    }




    echo json_encode($res);

}
add_action("wp_ajax_application", "application_form_process");

//use this version for if you want the callback to work for users who are not logged in
add_action("wp_ajax_nopriv_application", "application_form_process");
