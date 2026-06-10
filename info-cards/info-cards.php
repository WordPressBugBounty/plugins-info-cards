<?php

/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 6.5
 * Requires PHP:      7.1
 * Version:           3.0.1
 * Author:            bPlugins
 * Author URI:        http://bplugins.com
 * Plugin URI:        https://wordpress.org/plugins/info-cards/
 * License:          GPLv3 or later
 * License URI:        http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       info-cards
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'info_cards_fs' ) ) {
    register_activation_hook( __FILE__, function () {
        if ( !function_exists( 'is_plugin_active' ) ) {
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
    define( 'INFO_CARDS_PRO', file_exists( dirname( __FILE__ ) . '/vendor/freemius/start.php' ) );
    define( 'ICB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '3.0.1' ) );
    define( 'ICB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'ICB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'ICB_DIR', ICB_DIR_URL );
    define( 'ICB_DIR_PATH_LEGACY', ICB_DIR_PATH );
    if ( !function_exists( 'info_cards_fs' ) ) {
        function info_cards_fs() {
            global $info_cards_fs;
            if ( !isset( $info_cards_fs ) ) {
                if ( INFO_CARDS_PRO ) {
                    require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
                } else {
                    require_once dirname( __FILE__ ) . '/vendor/freemius-lite/start.php';
                }
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
                $info_cards_fs = ( INFO_CARDS_PRO ? fs_dynamic_init( $apbConfig ) : fs_lite_dynamic_init( $apbConfig ) );
            }
            return $info_cards_fs;
        }

        info_cards_fs();
        do_action( 'info_cards_fs_loaded' );
    }
    require_once ICB_DIR_PATH . 'inc/Helpers.php';
    if ( INFO_CARDS_PRO ) {
        require_once ICB_DIR_PATH . 'inc/LicenseActivation.php';
    }
    require_once ICB_DIR_PATH . 'inc/Init.php';
    require_once ICB_DIR_PATH . 'inc/Admin.php';
    require_once ICB_DIR_PATH . 'inc/RestApi.php';
    require_once ICB_DIR_PATH . 'inc/Posts.php';
    require_once ICB_DIR_PATH . 'inc/IcbShortcode.php';
    new info_cards\Init();
    new info_cards\Admin();
    new info_cards\RestApi();
    // -----------------------------------------------------------------------
    // Register Gutenberg Block Category
    // -----------------------------------------------------------------------
    add_filter(
        'block_categories_all',
        'info_cards_register_block_category',
        10,
        2
    );
    function info_cards_register_block_category(  $categories, $context  ) {
        if ( !is_array( $categories ) ) {
            $categories = [];
        }
        // Prevent duplicate category
        foreach ( $categories as $category ) {
            if ( isset( $category['slug'] ) && 'info-cards' === $category['slug'] ) {
                return $categories;
            }
        }
        // Add category at top
        array_unshift( $categories, [
            'slug'  => 'info-cards',
            'title' => __( 'Info Cards', 'info-cards' ),
            'icon'  => null,
        ] );
        return $categories;
    }

    // -----------------------------------------------------------------------
    // Register external CDN scripts/styles (NOT enqueue).
    // Each block's block.json declares which handles it needs in viewScript/style,
    // so WordPress will auto-enqueue them ONLY on pages where that block is used.
    // -----------------------------------------------------------------------
    add_action( 'init', function () {
        // -- Unicorn Studio (used by: info-cards) --
        wp_register_script(
            'unicorn-studio',
            'https://cdn.jsdelivr.net/gh/hiunicornstudio/unicornstudio.js@v1.4.25/dist/unicornStudio.umd.js',
            [],
            '1.4.25',
            true
        );
        // -- GSAP (used by: magnifying-glass-cards) --
        wp_register_script(
            'mgc-gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js',
            [],
            '3.11.3',
            true
        );
        // -- Splitting.js (used by: magnifying-glass-cards) --
        wp_register_script(
            'mgc-splitting',
            'https://unpkg.com/splitting/dist/splitting.min.js',
            [],
            null,
            true
        );
        // -- Owl Carousel CSS (used by: expandable-animated-card-slider) --
        wp_register_style(
            'owl-carousel',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
            [],
            '2.3.4'
        );
        wp_register_style(
            'owl-carousel-theme',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css',
            ['owl-carousel'],
            '2.3.4'
        );
        // -- Owl Carousel JS (used by: expandable-animated-card-slider) --
        wp_register_script(
            'owl-carousel-js',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
            ['jquery'],
            '2.3.4',
            true
        );
        wp_register_script(
            'owl-carousel-mousewheel',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.mousewheel.min.js',
            ['owl-carousel-js'],
            '2.3.4',
            true
        );
    }, 5 );
    // priority 5 → runs before block registration (priority 10)
    add_filter(
        'default_title',
        function ( $title, $post = null ) {
            if ( is_object( $post ) && isset( $post->post_type ) && 'page' === $post->post_type && isset( $_GET['title'] ) ) {
                return sanitize_text_field( wp_unslash( $_GET['title'] ) );
            }
            return $title;
        },
        10,
        2
    );
    add_filter(
        'default_content',
        function ( $content, $post = null ) {
            if ( is_object( $post ) && isset( $post->post_type ) && 'page' === $post->post_type && isset( $_GET['content'] ) ) {
                return wp_unslash( $_GET['content'] );
            }
            return $content;
        },
        10,
        2
    );
    // -----------------------------------------------------------------------
    // Plugin action links
    // -----------------------------------------------------------------------
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
        if ( !is_array( $links ) ) {
            $links = [];
        }
        $dashboard_link = '<a href="' . admin_url( 'admin.php?page=info-cards-dashboard' ) . '" style="color:#f18500;font-weight:bold;">Help And Demo</a>';
        array_unshift( $links, $dashboard_link );
        return $links;
    } );
}