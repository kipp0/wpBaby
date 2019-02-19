<?php


/**
 * Search Within a Taxonomy
 *
 * Support search with tax_query args
 *
 * $query = new WP_Query( array(
 *  'search_tax_query' => true,
 *  's' => $keywords,
 *  'tax_query' => array( array(
 *      'taxonomy' => 'country',
 *      'field' => 'id',
 *      'terms' => $country,
 *  ) ),
 * ) );
 */
 
 // 'search_tax_query' => true,
 // 'post_type' => 'Teams',
 // 'post_status' => 'publish',
 // 'orderby'=> 'title',
 // 'order' => 'ASC',
 // 'posts_per_page' => -1,
 // 'tax_query' => array(
 //   'relation' => "AND",
 //   array(
 //     'taxonomy' => 'team_cat',
 //     'include_children' => false,
 //     'field' => 'slug',
 //     'terms' => $term
 //   )
 // ),
 // 's' => $keyword,
class WP_Query_Taxonomy_Search {
    public function __construct() {
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

    public function pre_get_posts( $q ) {
        if ( is_admin() ) return;

        $wp_query_search_tax_query = filter_var(
            $q->get( 'search_tax_query' ),
            FILTER_VALIDATE_BOOLEAN
        );

        // WP_Query has 'tax_query', 's' and custom 'search_tax_query' argument passed
        if ( $wp_query_search_tax_query && $q->get( 'tax_query' ) && $q->get( 's' ) ) {
            add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 1 );
        }
    }

    public function posts_groupby( $groupby ) {
        return '';
    }
}

new WP_Query_Taxonomy_Search();
