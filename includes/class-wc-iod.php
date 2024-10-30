<?php
/**
 * Israeli Orange Delivery main file.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_IOD class.
 *
 * @since 1.0.0
 */
final class WC_IOD {

	/**
	 * The single instance of the class.
	 *
	 * @var WC_IOD
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main WC_IOD Instance.
	 *
	 * Ensures only one instance of WC_IOD is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC_IOD()
	 * @return WC_IOD - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {
		define( 'WC_IOD_PLUGIN_PATH', plugin_dir_path( WC_IOD_PLUGIN_FILE ) );
		define( 'WC_IOD_PLUGIN_URL', plugin_dir_url( WC_IOD_PLUGIN_FILE ) );

		define( 'WC_IOD_API_URL', 'http://crm.tapuzdelivery.co.il/baldarwebservice/Service.asmx/' );
		define( 'WC_IOD_API_USERNAME', get_option( 'wc_iod_api_username' ) );
		define( 'WC_IOD_API_PASSWORD', get_option( 'wc_iod_api_password' ) );
		define( 'WC_IOD_PAPER_SIZE', 'A4' );
		define( 'WC_IOD_CUSTOMER_CODE', get_option( 'wc_iod_api_customer_code' ) );
	}

	/**
	 * Load files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		include_once WC_IOD_PLUGIN_PATH . 'includes/wc-iod-functions.php';
		include_once WC_IOD_PLUGIN_PATH . 'includes/class-wc-iod-order-hooks.php';

		if ( is_admin() ) {
			include_once WC_IOD_PLUGIN_PATH . 'includes/admin/class-wc-iod-admin.php';
			include_once WC_IOD_PLUGIN_PATH . 'includes/admin/class-wc-iod-admin-metabox.php';
			include_once WC_IOD_PLUGIN_PATH . 'includes/admin/class-wc-iod-admin-settings.php';
			include_once WC_IOD_PLUGIN_PATH . 'includes/admin/class-wc-iod-admin-print-label.php';
		}
	}

	/**
	 * Hooked function with WordPress init hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woo-iod', false, dirname( plugin_basename( WC_IOD_PLUGIN_FILE ) ) . '/languages' );
	}

}
