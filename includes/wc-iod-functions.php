<?php
/**
 * Israeli Orange Delivery functions.
 *
 * @package Israeli_Orange_Delivery
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_iod_connection' ) ) {
	/**
	 * Connect with Israeli Orange Delivery server.
	 *
	 * @since 1.0.0
	 * @param array  $data        Israeli Orange Delivery paramerts.
	 * @param string $wc_iod_func Israeli Orange Delivery API endpoint.
	 * @return bool|string XML response from an API server otherwise false.
	 */
	function wc_iod_connection( $data, $wc_iod_func ) {
		$url = WC_IOD_API_URL . $wc_iod_func;

		$response = wp_remote_post(
			$url,
			array(
				'body' => $data,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return wp_remote_retrieve_body( $response );
	}
}

if ( ! function_exists( 'wc_iod_xml_json' ) ) {
	/**
	 * Convert API XML response to array.
	 *
	 * @since 1.0.0
	 * @param string $wc_iod_response Israeli Orange Delivery XML response.
	 * @return array Converted array.
	 */
	function wc_iod_xml_json( $wc_iod_response ) {
		$wc_iod_xml = html_entity_decode( $wc_iod_response );
		$xml        = new SimpleXMLElement( $wc_iod_xml );

		return (array) $xml;
	}
}

if ( ! function_exists( 'wc_iod_create_ship' ) ) {

	/**
	 * Israeli Orange Delivery create ship.
	 *
	 * @since 1.0.0
	 * @param array $args Israeli Orange Delivery API args.
	 * @return bool|array API response array otherwise false.
	 */
	function wc_iod_create_ship( $args = array() ) {
		$a = wp_parse_args(
			$args,
			array(
				'delivery_type'        => 1,
				'store_street'         => '',
				'store_number'         => 0,
				'store_city'           => '',
				'customer_street_1'    => '',
				'customer_street_2'    => '',
				'customer_city'        => '',
				'store_name'           => '',
				'customer_name'        => '',
				'customer_comment'     => '',
				'priority'             => 1,
				'tapuz_static_1'       => 0,
				'envelope_type'        => 1,
				'no_of_packages'       => 1,
				'return'               => 1,
				'tapuz_static_2'       => 0,
				'order_no_with_prefix' => '',
				'store_id'             => '',
				'order_id'             => 0,
				'customer_email'       => '',
				'tapuz_static_3'       => 0,
				'store_postcode'       => '',
				'customer_postcode'    => '',
				'tapuz_static_4'       => 0,
				'customer_phone'       => '',
				'tapuz_static_5'       => 0,
				'delivery_date'        => gmdate( 'Y-m-d' ),
				'tapuz_static_6'       => 0,
			)
		);

		$p_param = implode( ';', $a );

		$data_send           = array();
		$data_send['pParam'] = $p_param;

		try {
			$wc_iod_response = wc_iod_connection( $data_send, 'SaveData1' );

			return wc_iod_xml_json( $wc_iod_response );
		} catch ( Exception $e ) {
			return false;
		}

	}
}

if ( ! function_exists( 'wc_iod_get_shipping_detail' ) ) {

	/**
	 * Get shipping detail.
	 *
	 * @since 1.0.1
	 *
	 * @param string $shipping_detail Shipping or Delivery number.
	 * @return array|bool Shipping details, false otherwise.
	 */
	function wc_iod_get_shipping_detail( $shipping_detail ) {
		$customer_code = WC_IOD_CUSTOMER_CODE;
		$url           = 'http://crm.tapuzdelivery.co.il/baldarws_c/Service.asmx/ListDeliveryDetails?customerId=' . esc_html( $customer_code ) . '&deliveryNumbers=' . esc_html( $shipping_detail ) . '';
		$remote        = wp_remote_get( $url );
		$response      = wp_remote_retrieve_body( $remote );

		if ( ! $response ) {
			return false;
		}

		$wc_iod_xml = html_entity_decode( $response );
		$xml        = new SimpleXMLElement( $wc_iod_xml );

		return (array) $xml;
	}
}

if ( ! function_exists( 'wc_iod_get_seller_address' ) ) {

	/**
	 * Get seller adderss.
	 *
	 * @since 1.0.1
	 *
	 * @param string $address_part Optional. Get particular part of the address.
	 * @return string Seller address.
	 */
	function wc_iod_get_seller_address( $address_part = '' ) {
		$name      = get_option( 'wc_iod_seller_company_name' );
		$address_1 = get_option( 'wc_iod_seller_company_address_1' );
		$address_2 = get_option( 'wc_iod_seller_company_address_2' );
		$city      = get_option( 'wc_iod_seller_company_city' );
		$postcode  = get_option( 'wc_iod_seller_company_postcode' );

		switch ( $address_part ) {
			case 'name':
				return esc_html( $name );

			case 'address_1':
				return esc_html( $address_1 );

			case 'address_2':
				return esc_html( $address_2 );

			case 'city':
				return esc_html( $city );

			case 'postcode':
				return esc_html( $postcode );

			default:
				return wp_sprintf(
					'%s, %s, %s, %s, %s',
					esc_html( $name ),
					esc_html( $address_1 ),
					esc_html( $address_2 ),
					esc_html( $city ),
					esc_html( $postcode )
				);
		}
	}
}
