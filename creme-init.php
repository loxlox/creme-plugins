<?php
/**
 * Plugin Name: Creme Di Menta Extentions
 * Description: This plugin additional functions to support Creme Di Menta Theme.
 * Author:		ARTheme
 * Author URI: 	http://themeforest.net/user/arthemewipes
 * Version:		1.0
 * Text Domain: creme-plugin
 */

require_once plugin_dir_path( __FILE__ ) . '/author.php';
require_once plugin_dir_path( __FILE__ ) . '/shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . '/widgets/ar-about-widget.php';
require_once plugin_dir_path( __FILE__ ) . '/widgets/ar-popular-widget.php';
require_once plugin_dir_path( __FILE__ ) . '/widgets/ar-ads-widget.php';
require_once plugin_dir_path( __FILE__ ) . '/widgets/ar-category-widget.php';

function creme_init_plugin() {
	load_plugin_textdomain( 'creme-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	add_image_size( 'creme-cat-banner', '600', '600', true );
}
add_action( 'init', 'creme_init_plugin', 999 );