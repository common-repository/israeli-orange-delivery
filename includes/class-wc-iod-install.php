<?php
/**
 * Israeli Orange Delivery order hook.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_IOD_Install' ) ) {

	/**
	 * Plugin install class.
	 *
	 * @since 1.0.0
	 */
	class WC_IOD_Install {

		/**
		 * Install required data.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function install() {
			// Register settings.
			self::register_settings();
			self::register_schedule_events();
		}

		/**
		 * Register plugin settings.
		 *
		 * @return void
		 */
		public static function register_settings() {
			add_option( 'wc_iod_api_username', 'TEST' );
			add_option( 'wc_iod_api_password', '4041' );
			add_option( 'wc_iod_api_customer_code', '4041' );
			add_option( 'wc_iod_seller_company_name', 'TEST' );
			add_option( 'wc_iod_seller_company_address_1', get_option( 'woocommerce_store_address' ) );
			add_option( 'wc_iod_seller_company_address_2', get_option( 'woocommerce_store_address_2' ) );
			add_option( 'wc_iod_seller_company_city', get_option( 'woocommerce_store_city' ) );
			add_option( 'wc_iod_seller_company_postcode', get_option( 'woocommerce_store_postcode' ) );
		}

		/**
		 * Register schedule events.
		 *
		 * @since 1.0.1
		 *
		 * @return void
		 */
		public static function register_schedule_events() {
			if ( ! wp_next_scheduled( 'wc_iod_check_order_shipping_status' ) ) {
				wp_schedule_event( strtotime( '17:00:00' ), 'daily', 'wc_iod_check_order_shipping_status' );
			}
		}

	}

	WC_IOD_Install::install();
}
