<?php
/**
 * Israeli Orange Delivery
 *
 * @package           Israeli_Orange_Delivery
 * @author            Gamliel Solutions
 * @copyright         2021 Gamliel Solutions
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       משלוחים תפוז ישראלי
 * Plugin URI:        https://gamliel.solutions/plugins
 * Description:       תוסף ל woocommerce למשלוחים של חברת תפוז הישראלית
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Gamliel Solutions
 * Author URI:        https://gamliel.solutions/about-gamliel-solutions/
 * Text Domain:       woo-iod
 * License:           GPL v2 or later
 * Domain Path:       /languages/
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WC_IOD_PLUGIN_FILE' ) ) {
	define( 'WC_IOD_PLUGIN_FILE', __FILE__ );
}

if ( ! function_exists( 'wc_iod_plugin_installation' ) ) {

	/**
	 * Plugin activation hook.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function wc_iod_plugin_installation() {
		include_once dirname( WC_IOD_PLUGIN_FILE ) . '/includes/class-wc-iod-install.php';
	}

	register_activation_hook( WC_IOD_PLUGIN_FILE, 'wc_iod_plugin_installation' );
}

// Include the main WC_IOD class.
if ( ! class_exists( 'WC_IOD', false ) ) {
	include_once dirname( WC_IOD_PLUGIN_FILE ) . '/includes/class-wc-iod.php';
}

/**
 * Returns the main instance of WC_IOD.
 *
 * @since  1.0.0
 * @return WC_IOD
 */
function WC_IOD() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WC_IOD::instance();
}

// Global for backwards compatibility.
$GLOBALS['wc_iod'] = WC_IOD();
