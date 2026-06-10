<?php


namespace info_cards;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Init {

    function __construct() {
        add_action( 'init', [ $this, 'onInit' ] );
    }

    function onInit() {
        $this->icb_register_blocks();
    }

    /**
     * Loop through build/blocks/ and register each block.
     * Free blocks are always registered.
     * Pro blocks are only registered when premium is active.
     * Disabled blocks (toggled OFF) are skipped entirely.
     */
    function icb_register_blocks() {
        $blocks_path = ICB_DIR_PATH . 'build/blocks/';
        
        // Use scandir instead of glob to prevent issues on restricted servers
        if ( ! is_dir( $blocks_path ) ) {
            return;
        }
        
        $files = scandir( $blocks_path );
        $all_blocks = [];
        
        foreach ( $files as $file ) {
            if ( $file !== '.' && $file !== '..' && is_dir( $blocks_path . $file ) ) {
                $all_blocks[] = $blocks_path . $file;
            }
        }

        if ( empty( $all_blocks ) ) {
            return;
        }

        // Blocks toggled OFF by the admin dashboard.
        $disabled_blocks = get_option( 'icbBlocks', [] );
        if ( ! is_array( $disabled_blocks ) ) {
            $disabled_blocks = [];
        }

        $is_premium = info_cards_is_premium();

        foreach ( $all_blocks as $block_path ) {
            $block_name = basename( $block_path );

            // Skip if admin toggled this block OFF.
            if ( in_array( $block_name, $disabled_blocks, true ) ) {
                continue;
            }

            // Free blocks — always register.
            // 'parent' is the selector/chooser UI, needed by all users.
            if ( in_array( $block_name, [ 'info-cards', 'parent' ], true ) ) {
                register_block_type( $block_path );
                continue;
            }

            // All other blocks are Pro — only register when premium is active.
            if ( $is_premium ) {
                register_block_type( $block_path );
            }
        }
    }
}
