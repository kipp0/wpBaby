<?php
function set_sport_session_var($sport) {

  $_SESSION['sport'] = $sport;


  return $sport;
}
function get_session_var($var) {

   return $_SESSION["$var"];

}

function redirect_if_no_sport($sport) {

  // NOTE: incase we allow default search
  $term = 'hockey';

  if(isset($sport)) {

    $term = $sport;
    return $term;
  } else {
    // code...
    wp_redirect(home_url(''));
    exit();
  }
}
