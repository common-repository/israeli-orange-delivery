<?php
/**
 * Israeli Orange Delivery settings.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_IOD_Admin_Settings admin settings.
 */
class WC_IOD_Admin_Settings {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_wc-iod', array( $this, 'settings_tab' ) );
		add_action( 'woocommerce_update_options_wc-iod', array( $this, 'update_settings' ) );
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['wc-iod'] = __( 'משלוחים תפוז', 'woo-iod' );

		return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public function get_settings() {

		$settings = array(
			'wc_iod_api_section_title'        => array(
				'name' => __( 'הגדרות לתוסף משולחים תפוז', 'woo-iod' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_iod_api_section_title',
			),
			'wc_iod_api_username'             => array(
				'name' => __( 'שם משתמש', 'woo-iod' ),
				'type' => 'text',
				'id'   => 'wc_iod_api_username',
			),
			'wc_iod_api_password'             => array(
				'name'        => __( 'סיסמא', 'woo-iod' ),
				'type'        => 'text',
				'id'          => 'wc_iod_api_password',
				'placeholder' => __( 'קוד ברירת מחדל לביקות הוא 4041', 'woo-iod' ),
			),
			'wc_iod_api_customer_code'        => array(
				'name'        => __( 'קוד לקוח במערכת תפוז', 'woo-iod' ),
				'type'        => 'text',
				'id'          => 'wc_iod_api_customer_code',
				'desc'        => __( 'כדי להתחיל לעבוד עם התוסף יש ליצור קשר עם תפוז ולקבל קוד לקוח במערכת', 'woo-iod' ),
				'placeholder' => __( 'קוד ברירת מחדל לביקות הוא 4041', 'woo-iod' ),
			),
			'wc_iod_seller_company_name'      => array(
				'name' => __( 'שם לקוח במערכת תפוז', 'woo-iod' ),
				'type' => 'text',
				'id'   => 'wc_iod_seller_company_name',
			),
			'wc_iod_seller_company_address_1' => array(
				'name' => __( 'כתובת 1', 'woo-iod' ),
				'type' => 'text',
				'id'   => 'wc_iod_seller_company_address_1',
			),
			'wc_iod_seller_company_address_2' => array(
				'name' => __( 'כתובת 2', 'woo-iod' ),
				'type' => 'text',
				'id'   => 'wc_iod_seller_company_address_2',
			),
			'wc_iod_seller_company_city'      => array(
				'name' => __( 'עיר', 'woo-iod' ),
				'type' => 'text',
				'id'   => 'wc_iod_seller_company_city',
			),
			'wc_iod_seller_company_postcode'  => array(
				'name' => __( 'מיקוד', 'woo-iod' ),
				'type' => 'text',
				'id'   => 'wc_iod_seller_company_postcode',
			),
			'wc_iod_api_section_end'          => array(
				'type' => 'sectionend',
				'id'   => 'wc_iod_api_section_end',
			),
		);

		return apply_filters( 'wc_settings_tab_wc_iod_settings', $settings );
	}

}

return new WC_IOD_Admin_Settings();
