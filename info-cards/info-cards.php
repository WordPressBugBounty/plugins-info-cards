<?php
/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Version:           3.0.5
 * Author:            bPlugins
 * Author URI:        http://bplugins.com
 * Plugin URI:        https://wordpress.org/plugins/info-cards/
 * License:          GPLv3 or later
 * License URI:        http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       info-cards
 * @fs_free_only /vendor/freemius-lite
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( function_exists( 'info_cards_fs' ) ) {
    register_activation_hook( __FILE__, function () {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if ( is_plugin_active( 'info-cards/info-cards.php' ) ) {
            deactivate_plugins( 'info-cards/info-cards.php' );
        }
        if ( is_plugin_active( 'info-cards-pro/info-cards.php' ) ) {
            deactivate_plugins( 'info-cards-pro/info-cards.php' );
        }
    } );
} else {

    define( 'ICB_VERSION',    isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '3.0.5' );
    define( 'ICB_DIR_URL',    plugin_dir_url( __FILE__ ) );
    define( 'ICB_DIR_PATH',   plugin_dir_path( __FILE__ ) );

    define( 'ICB_DIR',      ICB_DIR_URL );
    define( 'ICB_DIR_PATH_LEGACY', ICB_DIR_PATH );

    if ( ! function_exists( 'info_cards_fs' ) ) {
        function info_cards_fs() {
            global $info_cards_fs;

            if ( ! isset( $info_cards_fs ) ) {
               
                    require_once dirname( __FILE__ ) . '/vendor/freemius-lite/start.php';
                

                $apbConfig = [
                    'id'                  => '17727',
                    'slug'                => 'info-cards',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_a98bc1d71dc1e0a8bf0aede3af3e0',
                    'is_premium'          => false,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'trial'               => [
                        'days'               => 7,
                        'is_require_payment' => false,
                    ],
                    'menu'                => [
                        'slug'       => 'info-cards-dashboard',
                        'first-path' => 'admin.php?page=info-cards-dashboard#/welcome',
                        'support'    => false,
                    ],
                ];

                $info_cards_fs =fs_lite_dynamic_init( $apbConfig );
            }

            return $info_cards_fs;
        }

        info_cards_fs();
        do_action( 'info_cards_fs_loaded' );
    }

    

    require_once ICB_DIR_PATH . 'includes/utility/functions.php';
    require_once ICB_DIR_PATH . 'includes/rootPlugin/plugin.php';
    require_once ICB_DIR_PATH . 'includes/class-infoCards-analytics.php';
    require_once ICB_DIR_PATH . 'includes/class-icb-promo-banner.php';

    new InfoCards_Analytics();

    // -----------------------------------------------------------------------
    // Plugin action links
    // -----------------------------------------------------------------------
function pluginActionLinks( $links, $file ) {
    if ( plugin_basename( __FILE__ ) === $file ) {
        $goProLink = 'https://bplugins.com/products/info-cards/pricing/?utm_source=admin&utm_medium=plugins&utm_campaign=bfcm2026';

        $linkText = ( current_time( 'Y-m-d' ) > '2026-12-05' )
            ? __( 'Upgrade to Pro', 'info-cards' )
            : __( 'Upgrade to Pro & Save Up to 45% This Black Friday!', 'info-cards' );

        $links['help-and-demos'] = sprintf(
            '<a href="%s" target="_blank" rel="noopener noreferrer" style="%s">%s</a>',
            esc_url( $goProLink ),
            'color:#d63638;font-weight:bold;',
            $linkText
        );
    }

    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pluginActionLinks', 10, 2 );
}