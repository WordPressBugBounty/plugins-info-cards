<?php

/**
 * Helpers.php — Central utility functions for the Info Cards plugin.
 * Mirrors: pdf-embed-block/includes/utility/functions.php
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Check if the current user has an active premium license.
 *
 * @return bool
 */
if ( !function_exists('info_cards_is_premium') ) {
    function info_cards_is_premium() {
        return INFO_CARDS_PRO ? info_cards_fs()->can_use_premium_code() : false;
    }
}
