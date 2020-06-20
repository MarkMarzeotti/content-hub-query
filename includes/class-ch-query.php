<?php

class CH_Query {

    private static $current_post = 0;

    private static $previous_posts = array();

    private $args = array(
        'post_type'      => array( 'post' ),
        'post_status'    => array( 'publish' ),
        'posts_per_page' => 10,
    );

    public $query;

    private $tax_query = array();

    private $meta_query = array();

    public $posts;

    private $curated_posts = array();

    private $logic = array();

    private $logic_relationship = array( 'relation' => 'AND' );

    public function __construct( $args = array(), $fields = array() ) {

        // store the current post.
        if ( 0 === self::$current_post && is_single() ) {
            global $post;
            self::$current_post     = $post;
            self::$previous_posts[] = $post->ID;
        }

        // apply passed args to default args.
        $this->args = ! empty( $args ) ? array_merge( $this->args, $args ) : $this->args;

        $this->logic = isset( $fields['logic'] ) && empty( $fields['logic'] ) ? $fields['logic'] : $this->logic;

        $this->logic_relationship = isset( $fields['logic_relationship'] ) && empty( $fields['logic_relationship'] ) ? $fields['logic_relationship'] : $this->logic_relationship;

        if ( isset( $fields['curated_posts'] ) && ! empty( $fields['curated_posts'] ) ) {
            // get array of ids and run standard WP_Query
            $this->with_posts( $post_ids );
            
            // can I run just one that will get specific and the rest recent? - If not will need to update posts_per_page
        }

        // add previously retrieved posts to args
        $this->args['post__not_in'] = isset( $this->args['post__not_in'] ) ? array_merge( $this->args['post__not_in'], self::$previous_posts ) : self::$previous_posts;

    }

    /* UTILITIES */

    private function get_term_var_type( $term_var ) {

        $term_var_type = gettype( $term_var );

        $field = false;

        if ( 'string' === $term_var_type ) {
            $field = 'slug';
        }

        if ( 'integer' === $term_var_type ) {
            $field = 'term_id';
        }

        if ( 'array' === $term_var_type ) {
            $array_type = gettype( $term_var[0] );
            
            if ( 'string' === $term_var_type ) {
                $field = 'slug';
            }

            if ( 'integer' === $term_var_type ) {
                $field = 'term_id';
            }
        }

        return $field;

    }

    /* QUERY MODS */

    public function with_posts( $post_ids ) {}

    public function in_tax( $term, $taxonomy = 'category' ) {

        $term_var_type = $this->get_term_var_type( $term );

        if ( false !== $term_var_type ) {
            $this->tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => $term_var_type,
                'terms'    => $term,
            );
        }

    }

    // public function with_meta( $key, $value ) {}

    // public function is_adjacent( $next = true ) {}

    /* RUN */

    public function run() {

        if ( ! empty( $this->tax_query ) ) {
            $this->args['tax_query'] = array_merge( $this->logic_relationship, $this->tax_query );
        }

        if ( ! empty( $this->meta_query ) ) {
            $this->args['meta_query'] = $this->meta_query;
        }

        $this->query = new WP_Query( $this->args );
        $this->posts = $this->query->posts;

        $post_ids = ! empty( $this->posts ) ? array_column( $this->posts, 'ID' ) : array();
        self::$previous_posts = array_merge( self::$previous_posts, $post_ids );

        return $this->posts;

    }

}