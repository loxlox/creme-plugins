<?php

function creme_add_user_social_acc( $contactmethods ) {
	$contactmethods['twitter']   = esc_html__( 'Twitter Username',		'creme-plugin' );
	$contactmethods['facebook']  = esc_html__( 'Facebook Username',		'creme-plugin' );
	$contactmethods['instagram'] = esc_html__( 'Instagram Username',	'creme-plugin' );
	$contactmethods['pinterest'] = esc_html__( 'Pinterest Username',	'creme-plugin' );

	return $contactmethods;
}
add_filter( 'user_contactmethods', 'creme_add_user_social_acc', 10, 1 );