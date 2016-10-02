<?php

function creme_default_filters() {
	add_filter( 'widget_text', 'do_shortcode' );
	add_filter( 'term_description', 'do_shortcode' );
	add_filter( 'the_excerpt', 'do_shortcode');
}
add_action( 'after_setup_theme', 'creme_default_filters', 3 );