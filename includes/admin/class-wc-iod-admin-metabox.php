<?php
/**
 * Israeli Orange Delivery admin metabox.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_IOD_Admin_Metabox class.
 *
 * @since 1.0.0
 */
class WC_IOD_Admin_Metabox {

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'order_meta_box' ) );
	}

	/**
	 * Order metabox.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function order_meta_box() {
		add_meta_box(
			'wc-iod-order-metabox',
			esc_html__( 'משלוחים תפוז ישראלי', 'woo-iod' ),
			function( $post ) {
				$wc_iod_delivery_number = get_post_meta( $post->ID, '_wc_iod_delivery_number', true );

				if ( $wc_iod_delivery_number ) {
					?>
				<style>
					.wc-iod-order-card {
						border: 1px dashed #ccc;
						padding: 5px 10px;
						margin-bottom: 10px;
					}
				</style>
				<div class="wc-iod-order-card">
					<p><strong><?php esc_html_e( 'מספר משלוח:', 'woo-iod' ); ?></strong> <?php echo esc_html( $wc_iod_delivery_number ); ?></p>
					<p>
						<a href="javascript:;" class="button button-primary js-wc-iod-print-label-popup-toggle"><?php esc_html_e( 'הדפס מדבקה', 'woo-iod' ); ?></a>
					</p>
				</div>
					<?php
				} else {
					esc_html_e( 'לא נמצא מידע על המשלוח!', 'woo-iod' );
				}
			},
			'shop_order',
			'side'
		);
	}
}

return new WC_IOD_Admin_Metabox();
