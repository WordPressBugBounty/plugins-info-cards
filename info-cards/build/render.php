<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Sanitize URLs in cards array to prevent Stored XSS
if ( isset( $attributes['cards'] ) && is_array( $attributes['cards'] ) ) {
    foreach ( $attributes['cards'] as &$card ) {
        if ( isset( $card['btnUrl'] ) ) {
            $card['btnUrl'] = esc_url_raw( $card['btnUrl'] );
        }
        if ( isset( $card['cardUrl'] ) ) {
            $card['cardUrl'] = esc_url_raw( $card['cardUrl'] );
        }
    }
}

$id = wp_unique_id( 'icbCards-' );
?>
<div
    <?php echo wp_kses_post( get_block_wrapper_attributes() ); ?>
    id="<?php echo esc_attr( $id ); ?>"
    data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'>
</div>