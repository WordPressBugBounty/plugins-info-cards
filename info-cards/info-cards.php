<?php
/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 5.8
 * Requires PHP:      7.1
 * Version:           1.0.11
 * Author:            bPlugins
 * Author URI:        http://bplugins.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       info-cards
 */

if ( !defined( 'ABSPATH' ) ) { exit; }

class BPICB_Info_Cards{
    private static $instance;

    private function __construct()
    {
        $this->constants_define();
        add_action( 'init', [$this, 'onInit'] );
    }

    public static function get_instance(){
        if( self::$instance ){
            return self::$instance;
        }

        self::$instance = new self();
        return self::$instance;
    }

    private function constants_define() {
        // Constant
        define( 'ICB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.11' );
        define( 'ICB_DIR', plugin_dir_url( __FILE__ ) );
    }

    public function onInit(){
        register_block_type( __DIR__ . '/build' );
    }

}
BPICB_Info_Cards::get_instance();


