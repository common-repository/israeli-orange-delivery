<?php
/**
 * Israeli Orange Delivery admin.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OID_Admin class.
 *
 * @since 1.0.0
 */
class WC_IOD_Admin {

	/**
	 * WC_OID_Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'actions' ) );
		add_action( 'admin_footer', array( $this, 'order_footer_script' ) );
	}

	/**
	 * Admin actions.
	 *
	 * @return void
	 */
	public function actions() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['wc_iod_pdf'] ) || 'create' !== $_GET['wc_iod_pdf'] ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['order_id'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order_id = absint( wp_unslash( $_GET['order_id'] ) );

		$deliver_number         = get_post_meta( $order_id, '_wc_iod_delivery_number', true );
		$delivery_branch_code   = get_post_meta( $order_id, '_wc_iod_delivery_branch_code', true );
		$distrubution_line_code = get_post_meta( $order_id, '_wc_iod_delivery_distrubution_line_code', true );

		$args = array(
			'order_id'               => $order_id,
			'delivery_number'        => $deliver_number,
			'delivery_branch_code'   => $delivery_branch_code,
			'distrubution_line_code' => $distrubution_line_code,
			'sender_company'         => wc_iod_get_seller_address( 'name' ),
			'sender_address_1'       => wc_iod_get_seller_address( 'address_1' ),
			'sender_address_2'       => wc_iod_get_seller_address( 'address_2' ),
			'sender_city'            => wc_iod_get_seller_address( 'city' ),
		);

		$pdf = new WC_IOD_Admin_Print_Label( $args );
		$pdf->create_a4_label();

		exit();
	}

	/**
	 * Inject script to footer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function order_footer_script() {
		if ( 'shop_order' !== get_post_type() ) {
			return;
		}
		?>
		<style>
			.wc-iod-order-print-popup {
				display: none;
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 300px;
				z-index: 99999999;
				background: #fff;
				padding: 30px;
				box-shadow: 0 0 50px rgb(0 0 0 / 10%);
				border-radius: 6px;
				border: 1px solid #ccc;
			}

			.wc-iod-order-print-popup.wc-iod-order-print-popup--show {
				display: block;
			}

			.wc-iod-order-print-popup-close {
				display: flex;
				justify-content: flex-end;
				margin-bottom: 10px;
			}

			.wc-iod-order-print-popup-close svg {
				width: 28px;
				cursor: pointer;
			}

			.wc-iod-order-print {
				display: flex;
				flex-wrap: wrap;
			}

			.wc-iod-order-print > div {
				width: calc(50% - 2px);
				display: grid;
				place-content: center;
				height: 90px;
				border: 1px solid #ccc;
			}

			.wc-iod-order-print-submit {
				display: flex;
				justify-content: flex-end;
				margin-top: 10px;
			}

			.order-status.status-delivery {
				background: #FFA500;
				color: #fff;
			}
			.order-status.status-fabrication {
				background: #F0E68C;
				color: #686868;
			}
		</style>
		<form id="wc-iod-order-print-popup-form" action="<?php echo admin_url( 'post.php' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" method="get" class="wc-iod-order-print-popup" target="_blank">
			<input type="hidden" name="wc_iod_pdf" value="create">
			<input type="hidden" name="order_id" value="<?php echo get_the_ID(); ?>">

			<div>
				<div class="wc-iod-order-print-popup-close js-wc-iod-print-label-popup-toggle">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</div>
				<p><?php esc_attr_e( 'בחר מיקום מדבקה להדפסה.', 'woo-iod' ); ?></p>
				<div class="wc-iod-order-print">
					<div>
						<input type="radio" name="print_location" value="A1"> <?php esc_html_e( 'A1', 'woo-iod' ); ?>
					</div>
					<div>
						<input type="radio" name="print_location" value="B1"> <?php esc_html_e( 'B1', 'woo-iod' ); ?>
					</div>
					<div>
						<input type="radio" name="print_location" value="A2"> <?php esc_html_e( 'A2', 'woo-iod' ); ?>
					</div>
					<div>
						<input type="radio" name="print_location" value="B2"> <?php esc_html_e( 'B2', 'woo-iod' ); ?>
					</div>
					<div>
						<input type="radio" name="print_location" value="A3"> <?php esc_html_e( 'A3', 'woo-iod' ); ?>
					</div>
					<div>
						<input type="radio" name="print_location" value="B3"> <?php esc_html_e( 'B3', 'woo-iod' ); ?>
					</div>
				</div>
				<div class="wc-iod-order-print-submit">
					<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'הדפס תווית משלוח', 'woo-iod' ); ?>">
				</div>
			</div>
		</form>
		<script>
			( function( $ ) {
				'use strict';

				$( document ).ready( function() {

					function getUrlVars( url ) {
						var params;
						var vars   = {};
						var hashes = url.split("?")[1];
						var hash   = hashes.split('&');

						for ( var i = 0; i < hash.length; i++ ) {
							params = hash[ i ].split( "=" );
							vars[ params[0] ] = params[1];
						}

						return vars;
					}

					$( document ).on( 'click', '.js-wc-iod-print-label-popup-toggle', function( e ) {
						e.preventDefault();

						if ( $( '.wc-iod-order-print-popup' ).hasClass( 'wc-iod-order-print-popup--show' ) ) {
							$( '.wc-iod-order-print-popup' ).removeClass( 'wc-iod-order-print-popup--show' );
						} else {
							$( '.wc-iod-order-print-popup' ).addClass( 'wc-iod-order-print-popup--show' );
						}

					} );

					$( '#wc-iod-order-print-popup-form' ).on( 'submit', function() {
						$( this ).hide();
					} );

					$( document ).on( 'click', '.wc-action-button-fabrication', function( e ) {
						var statusUrl = $( this ).attr( 'href' );
						var params    = getUrlVars( statusUrl );
						var orderID   = params.order_id ? params.order_id : '';

						window.open( "<?php echo admin_url( 'post.php?wc_fabrication_pdf=create&order_id=' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" + orderID, "_blank" );
					} );

					$( document ).on( 'click', '.wc-action-button-delivery', function( e ) {
						e.preventDefault();

						var statusUrl = $( this ).attr( 'href' );
						var params    = getUrlVars( statusUrl );
						var orderID   = params.order_id ? params.order_id : '';

						if ( $( '.wc-iod-order-print-popup' ).hasClass( 'wc-iod-order-print-popup--show' ) ) {
							$( '.wc-iod-order-print-popup' ).hide();
							$( '.wc-iod-order-print-popup' ).removeClass( 'wc-iod-order-print-popup--show' );
						} else {
							$( '.wc-iod-order-print-popup' ).show();
							$( '.wc-iod-order-print-popup' ).addClass( 'wc-iod-order-print-popup--show' );
						}

						$( '#wc-iod-order-print-popup-form [name="order_id"]' ).val( orderID );

						$( document ).on( 'click', '.wc-iod-order-print-submit [type="submit"]', function() {
							setTimeout( function() {
								window.open( statusUrl, "_self" );
							}, 1000 );
						} );
					} );

				} ); // Doc.ready end.

			} )( jQuery );
		</script>
		<?php
	}
}

return new WC_IOD_Admin();
