<?php
/**
 * Israeli Orange Delivery order hook.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_IOD_Order_Hooks class.
 *
 * @since 1.0.0
 */
class WC_IOD_Order_Hooks {

	/**
	 * WC_IOD_Order_Hooks constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'wc_order_statuses', array( $this, 'add_order_statuses' ) );
		add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'admin_order_preview_actions' ), 10, 2 );

		add_action( 'init', array( $this, 'register_order_status' ) );
		add_action( 'woocommerce_order_status_iod-delivery', array( $this, 'order_status_delivery' ) );

		add_action( 'wc_iod_check_order_shipping_status', array( $this, 'check_order_shipping_status' ) );
	}

	/**
	 * Order status delivery.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id Order ID.
	 * @return void
	 */
	public function order_status_delivery( $order_id ) {
		$order            = wc_get_order( $order_id );
		$shipping_details = array();
		$billing_details  = array();

		$billing_details = $order->get_address( 'billing' );

		if ( $order->has_shipping_address() ) {
			$shipping_details = $order->get_address( 'shipping' );
		} else {
			$shipping_details = $order->get_address( 'billing' );
		}

		$args = array(
			'store_street'         => get_option( 'woocommerce_store_address' ),
			'store_number'         => 7,
			'store_city'           => get_option( 'woocommerce_store_city' ),
			'customer_street_1'    => $shipping_details['address_1'] . ', ' . $shipping_details['address_2'],
			'customer_street_2'    => $shipping_details['address_2'],
			'customer_city'        => $shipping_details['city'],
			'store_name'           => wc_iod_get_seller_address( 'name' ),
			'customer_name'        => $shipping_details['first_name'] . ' ' . $shipping_details['last_name'],
			'customer_comment'     => $order->customer_message,
			'order_no_with_prefix' => 'bm' . $order_id,
			'store_id'             => WC_IOD_CUSTOMER_CODE,
			'order_id'             => $order_id,
			'customer_email'       => $billing_details['email'],
			'store_postcode'       => WC_IOD_CUSTOMER_COLLECT_ZIP,
			'customer_postcode'    => $shipping_details['postcode'],
			'customer_phone'       => $billing_details['phone'],
			'delivery_date'        => gmdate( 'Y-m-d' ),

		);

		$response = wc_iod_create_ship( $args );

		if ( ! isset( $response['DeliveryNumber'] ) && empty( $response['DeliveryNumber'] ) ) {
			return;
		}

		$deliver_number = $response['DeliveryNumber'];
		$deliver_string = $response['DeliveryNumberString'];

		$deliver_string_raw = explode( ';', $deliver_string );
		$branch_code        = isset( $deliver_string_raw[1] ) ? $deliver_string_raw[1] : '';
		$distrubution_line  = isset( $deliver_string_raw[2] ) ? $deliver_string_raw[2] : '';

		if ( ! get_post_meta( $order_id, '_wc_iod_delivery_number', $deliver_number ) ) {
			add_post_meta( $order_id, '_wc_iod_delivery_number', $deliver_number );
			add_post_meta( $order_id, '_wc_iod_delivery_branch_code', $branch_code );
			add_post_meta( $order_id, '_wc_iod_delivery_distrubution_line_code', $distrubution_line );
			add_post_meta( $order_id, '_wc_iod_response_data', $response );
		}
	}

	/**
	 * Register order status.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_order_status() {
		register_post_status(
			'wc-iod-delivery',
			array(
				'label'                     => esc_html__( 'משלוח', 'woo-iod' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: משלוח */
				'label_count'               => _n_noop( 'משלוח (%s)', 'משלוח (%s)', 'woo-iod' ),
			)
		);
	}

	/**
	 * Add order status.
	 *
	 * @since 1.0.0
	 *
	 * @param array $order_statuses Order statuses.
	 * @return array Order statuses.
	 */
	public function add_order_statuses( $order_statuses ) {
		$new_order_statuses = array();

		// Add new order status after processing.
		foreach ( $order_statuses as $key => $status ) {

			$new_order_statuses[ $key ] = $status;

			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-iod-delivery'] = esc_html__( 'משלוח', 'woo-iod' );
			}
		}

		return $new_order_statuses;
	}

	/**
	 * Admin order preview.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $actions WooCommerce order actions.
	 * @param WC_Order $order   WooCommerce order object.
	 * @return array Order actions.
	 */
	public function admin_order_preview_actions( $actions, $order ) {
		if ( $order->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {
			$actions['status']['actions']['delivery'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=iod-delivery&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'משלוח', 'woo-iod' ),
				'title'  => __( 'שנה סטטוס הזמנה למשלוח', 'woo-iod' ),
				'action' => 'delivery',
			);
		}

		if ( $order->has_status( array( 'iod-delivery' ) ) ) {
			$actions['status']['actions']['complete'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
				'name'   => __( 'הסתיים', 'woo-iod' ),
				'title'  => __( 'שנה סטטוס הזמנה להסתיים', 'woo-iod' ),
				'action' => 'complete',
			);

			if ( ! isset( $actions['status']['group'] ) ) {
				$actions['status']['group'] = __( 'שנה סטטוס: ', 'woo-iod' );
			}
		}

		return $actions;
	}

	/**
	 * Check order shipping status and marked as completed if order delivered.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function check_order_shipping_status() {
		$orders = new WP_Query(
			array(
				'post_type'      => 'shop_order',
				'post_status'    => 'any',
				'posts_per_page' => -1,
			)
		);

		if ( $orders->have_posts() ) {
			while ( $orders->have_posts() ) {
				$orders->the_post();

				$delivery_number = get_post_meta( get_the_ID(), '_wc_iod_delivery_number', true );

				if ( ! $delivery_number ) {
					continue;
				}

				$deliver_details = wc_iod_get_shipping_detail( $delivery_number );

				if ( ! $deliver_details ) {
					continue;
				}

				$deliver_status = (int) $deliver_details['ListDeliveryDetails']->Records->Record->DeliveryStatus;

				if ( ! $deliver_status || 0 === $deliver_status ) {
					continue;
				}

				// 3 Delivery status means order successfully delivered.
				if ( 3 !== $deliver_status ) {
					continue;
				}

				$order = new WC_Order( get_the_ID() );

				if ( ! $order ) {
					continue;
				}

				$order->update_status( 'completed' );
			}

			wp_reset_postdata();
		}
	}

}

return new WC_IOD_Order_Hooks();
