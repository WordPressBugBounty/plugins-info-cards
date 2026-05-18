<?php

/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 5.8
 * Requires PHP:      7.1
 * Version:           3.0.0
 * Author:            bPlugins
 * Author URI:        http://bplugins.com
 * Plugin URI:        https://wordpress.org/plugins/info-cards/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       info-cards
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'ic_fs' ) ) {
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
    define( 'INFO_CARDS_PRO', file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) );
    define( 'ICB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '3.0.0' ) );
    define( 'ICB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'ICB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'ICB_DIR', ICB_DIR_URL );
    define( 'ICB_DIR_PATH_LEGACY', ICB_DIR_PATH );
    if ( !function_exists( 'ic_fs' ) ) {
        function ic_fs() {
            global $ic_fs;
            if ( !isset( $ic_fs ) ) {
                if ( INFO_CARDS_PRO ) {
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                } else {
                    require_once dirname( __FILE__ ) . '/freemius-lite/start.php';
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
                $ic_fs = ( INFO_CARDS_PRO ? fs_dynamic_init( $apbConfig ) : fs_lite_dynamic_init( $apbConfig ) );
            }
            return $ic_fs;
        }

        ic_fs();
        do_action( 'ic_fs_loaded' );
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
    new ICB\Init();
    new ICB\Admin();
    new ICB\RestApi();
    // -----------------------------------------------------------------------
    // Register Gutenberg Block Category
    // -----------------------------------------------------------------------
    add_filter(
        'block_categories_all',
        'icb_register_block_category',
        10,
        2
    );
    function icb_register_block_category(  $categories, $context  ) {
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

    add_action( 'enqueue_block_assets', function () {
        // wp_enqueue_script(
        //     'unicorn-studio',
        //     'https://cdn.jsdelivr.net/gh/hiunicornstudio/unicornstudio.js@v1.4.25/dist/unicornStudio.umd.js',
        //     [],
        //     '1.4.25',
        //     true
        // );
        // wp_enqueue_script( 'mgc-jquery-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js', [], '3.6.0', true );
        // wp_enqueue_script( 'mgc-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js', [], '3.11.3', true );
        // wp_enqueue_script( 'mgc-splitting', 'https://unpkg.com/splitting/dist/splitting.min.js', [], null, true );
        // wp_enqueue_style( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', [], '2.3.4' );
        // wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', [ 'owl-carousel' ], '2.3.4' );
        // wp_enqueue_script( 'owl-jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js', [], '3.7.1', true );
        // wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', [ 'owl-jquery' ], '2.3.4', true );
        // wp_enqueue_script( 'owl-carousel-mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.mousewheel.min.js', [ 'owl-carousel-js' ], '2.3.4', true );
        // jQuery (single)
        // wp_enqueue_script( 'jquery' );
        // Core Dependencies for Blocks and AJAX
        // wp_enqueue_script( 'wp-util' );
        // wp_enqueue_script( 'wp-element' );
        // Define ajaxurl for the frontend if it's not defined
        if ( !is_admin() ) {
            wp_add_inline_script( 'wp-util', 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";', 'before' );
        }
        // Unicorn Studio
        wp_enqueue_script(
            'unicorn-studio',
            'https://cdn.jsdelivr.net/gh/hiunicornstudio/unicornstudio.js@v1.4.25/dist/unicornStudio.umd.js',
            [],
            '1.4.25',
            true
        );
        // GSAP + Splitting
        wp_enqueue_script(
            'jquery-cdn',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js',
            [],
            '3.7.1',
            true
        );
        wp_enqueue_script(
            'mgc-gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js',
            [],
            '3.11.3',
            true
        );
        wp_enqueue_script(
            'mgc-splitting',
            'https://unpkg.com/splitting/dist/splitting.min.js',
            [],
            null,
            true
        );
        // Owl Carousel
        wp_enqueue_style(
            'owl-carousel',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
            [],
            '2.3.4'
        );
        wp_enqueue_style(
            'owl-carousel-theme',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css',
            ['owl-carousel'],
            '2.3.4'
        );
        wp_enqueue_script(
            'owl-carousel-js',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
            ['jquery'],
            '2.3.4',
            true
        );
        wp_enqueue_script(
            'owl-carousel-mousewheel',
            'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.mousewheel.min.js',
            ['owl-carousel-js'],
            '2.3.4',
            true
        );
    } );
    // -----------------------------------------------------------------------
    // Helpers for Gutenberg page creation via URL params
    // -----------------------------------------------------------------------
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