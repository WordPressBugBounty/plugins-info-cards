<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$id = wp_unique_id( 'bBlocksTestPurpose-' );
?>

<div
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo get_block_wrapper_attributes();
	?>
	id="<?php echo esc_attr( $id ); ?>"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>"
	data-attributes="<?php echo esc_attr( wp_json_encode( array_merge( $attributes, [ 'currentPostId' => get_the_ID() ] ) ) ); ?>"
></div>