<?php
  if (!is_front_page()) :
  // add them to var
  // $heading = get_field('heading');
  $cat = get_the_category();
  $title = get_the_title();
  $sub_heading = get_field('sub_heading');
  $qo = get_queried_object();

  if ($qo instanceof WP_Term) {
    # code...
    $title = $qo->cat_name;
    $sub_heading = "<h2 aria-label=\"Page Description\">$qo->description</h2>";
  }
  if (isset($cat[0])) {
    # code...
    $title = $cat[0]->name;
    $sub_heading = "<h2 aria-label=\"Page Description\">".$cat[0]->description."</h2>";

  }





//          array(1) {
//   [0]=>
//   object(WP_Term)#589 (16) {
//     ["term_id"]=>
//     int(1)
//     ["name"]=>
//     string(17) "News & Events"
//     ["slug"]=>
//     string(4) "news"
//     ["term_group"]=>
//     int(0)
//     ["term_taxonomy_id"]=>
//     int(1)
//     ["taxonomy"]=>
//     string(8) "category"
//     ["description"]=>
//     string(22) "News & Events Blog"
//     ["parent"]=>
//     int(0)
//     ["count"]=>
//     int(5)
//     ["filter"]=>
//     string(3) "raw"
//     ["cat_ID"]=>
//     int(1)
//     ["category_count"]=>
//     int(5)
//     ["category_description"]=>
//     string(22) "News & Events Blog"
//     ["cat_name"]=>
//     string(17) "News & Events"
//     ["category_nicename"]=>
//     string(4) "news"
//     ["category_parent"]=>
//     int(0)
//   }
// }

?>

<section>
  <?=  ($title != '') ? "<h1 aria-label=\"Page Title\">$title</h1>" : ""; ?>
  <?=  ($sub_heading != '') ? "$sub_heading" : ""; ?>
</section>

<?php endif; ?>
