<?php

/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 5.8
 * Requires PHP:      7.1
 * Version:           2.0.7
 * Author:            bPlugins
 * Author URI:        http://bplugins.com
 * Plugin URI:  https://wordpress.org/plugins/info-cards/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       info-cards
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'ic_fs' ) ) {
    register_activation_hook( __FILE__, function () {
        if ( is_plugin_active( 'info-cards/info-cards.php' ) ) {
            deactivate_plugins( 'info-cards/info-cards.php' );
        }
        if ( is_plugin_active( 'info-cards-pro/info-cards.php' ) ) {
            deactivate_plugins( 'info-cards-pro/info-cards.php' );
        }
    } );
} else {
    /**
     * DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE
     * function_exists` CALL ABOVE TO PROPERLY WORK.
     */
    define( 'INFO_CARDS_PRO', file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) );
    define( 'ICB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    if ( !function_exists( 'ic_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ic_fs() {
            global $ic_fs;
            // Include Freemius SDK.
            if ( !isset( $ic_fs ) ) {
                if ( INFO_CARDS_PRO ) {
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                } else {
                    require_once dirname( __FILE__ ) . '/freemius-lite/start.php';
                }
                $apbConfig = array(
                    'id'                  => '17727',
                    'slug'                => 'info-cards',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_a98bc1d71dc1e0a8bf0aede3af3e0',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'trial'               => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                    'menu'                => ( INFO_CARDS_PRO ? array(
                        'slug'       => 'info-cards-dashboard',
                        'first-path' => 'admin.php?page=info-cards-dashboard#/welcome',
                        'support'    => false,
                    ) : array(
                        'slug'       => 'info-cards',
                        'first-path' => 'tools.php?page=info-cards-dashboard#/welcome',
                        'support'    => false,
                        'parent'     => array(
                            'slug' => 'tools.php',
                        ),
                    ) ),
                );
                $ic_fs = ( INFO_CARDS_PRO ? fs_dynamic_init( $apbConfig ) : fs_lite_dynamic_init( $apbConfig ) );
            }
            return $ic_fs;
        }

        // Init Freemius.
        ic_fs();
        // Signal that SDK was initiated.
        do_action( 'ic_fs_loaded' );
    }
    function bpicbIsPremium() {
        return ( INFO_CARDS_PRO ? ic_fs()->can_use_premium_code() : false );
    }

    if ( INFO_CARDS_PRO && bpicbIsPremium() ) {
        require_once ICB_DIR_PATH . '/inc/IcbShortcode.php';
        require_once ICB_DIR_PATH . '/inc/ProadminMenu.php';
    } else {
        require_once ICB_DIR_PATH . '/inc/IcbAdminMeno.php';
    }
    // my code
    class BPICB_Info_Cards {
        private static $instance;

        private function __construct() {
            $this->constants_define();
            add_action( 'init', [$this, 'onInit'] );
            add_action( 'enqueue_block_assets', [$this, 'load_unicorn_studio_script'] );
            // freemius
            add_action( 'wp_ajax_bpicbPremiumChecker', [$this, 'bpicbPremiumChecker'] );
            add_action( 'wp_ajax_nopriv_bpicbPremiumChecker', [$this, 'bpicbPremiumChecker'] );
            add_action( 'admin_init', [$this, 'registerSettings'] );
            add_action( 'rest_api_init', [$this, 'registerSettings'] );
        }

        function bpicbPremiumChecker() {
            $nonce = sanitize_text_field( $_POST['_wpnonce'] ?? null );
            if ( !wp_verify_nonce( $nonce, 'wp_ajax' ) ) {
                wp_send_json_error( 'Invalid Request' );
            }
            wp_send_json_success( [
                'isPipe' => bpicbIsPremium(),
            ] );
        }

        function registerSettings() {
            register_setting( 'bpicbUtils', 'bpicbUtils', [
                'show_in_rest'      => [
                    'name'   => 'bpicbUtils',
                    'schema' => [
                        'type' => 'string',
                    ],
                ],
                'type'              => 'string',
                'default'           => wp_json_encode( [
                    'nonce' => wp_create_nonce( 'wp_ajax' ),
                ] ),
                'sanitize_callback' => 'sanitize_text_field',
            ] );
        }

        public static function get_instance() {
            if ( self::$instance ) {
                return self::$instance;
            }
            self::$instance = new self();
            return self::$instance;
        }

        private function constants_define() {
            // Constant
            define( 'ICB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '2.0.7' ) );
            define( 'ICB_DIR', plugin_dir_url( __FILE__ ) );
        }

        public function onInit() {
            register_block_type( __DIR__ . '/build' );
        }

        function load_unicorn_studio_script() {
            wp_enqueue_script(
                'unicorn-studio',
                'https://cdn.jsdelivr.net/gh/hiunicornstudio/unicornstudio.js@v1.4.25/dist/unicornStudio.umd.js',
                array(),
                '1.4.25',
                true
            );
        }

    }

    BPICB_Info_Cards::get_instance();
}