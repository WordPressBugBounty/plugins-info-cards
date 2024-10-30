<?php

/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 5.8
 * Requires PHP:      7.1
 * Version:           1.0.3
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       info-cards
 */

if (!defined('ABSPATH')) {
    exit;
}

define( 'ICB_VERSION', 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.3' );
define( 'ICB_DIR', plugin_dir_url( __FILE__ ) );

if (!class_exists('ICBPlugin')) {
    class ICBPlugin{
        function __construct(){
            add_action('init', [$this, 'onInit']);
        }

        function onInit(){
            register_block_type(__DIR__ . '/build');
        }
    }
    new ICBPlugin();
}