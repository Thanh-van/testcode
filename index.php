<?php
/*
Plugin Name: Config
Plugin URI: #
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Develop team
Version: 1.7.2
Author URI: Develop team
*/

include_once 'navigation_config.php';
include_once 'shortcode/default.php';
include_once 'shortcode/order.php';
include_once 'shortcode/API.php';

/**
 * Enqueue and styles.
 */
function style_custom() {
    wp_register_style('style_custom', plugins_url('css/config_style.css',__FILE__ ));
    wp_enqueue_style('style_custom');
}
add_action( 'admin_init','style_custom');

/**
 * Enqueue scripts.
 */
function my_enqueue() {
    wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . 'js/query.js');
}
add_action('admin_enqueue_scripts', 'my_enqueue');

