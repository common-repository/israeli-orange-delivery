<?php
/**
 * Create PDF Label with barcode
 *
 * @package Woocommmerce_Tapuz
 */

if ( ! class_exists( 'TCPDF' ) ) {
	require_once WC_IOD_PLUGIN_PATH . 'includes/libraries/TCPDF/tcpdf.php';
}

/**
 * WC_IOD_Admin_Print_Label class.
 *
 * @since 1.0.0
 */
class WC_IOD_Admin_Print_Label {

	/**
	 * PDF object.
	 *
	 * @var object
	 */
	private $pdf;

	/**
	 * Print args.
	 *
	 * @var array
	 */
	private $args;

	/**
	 * Barcode style.
	 *
	 * @var array
	 */
	private $barcode_style = array(
		'position'     => 'R',
		'align'        => 'R',
		'stretch'      => true,
		'fitwidth'     => false,
		'cellfitalign' => '',
		'border'       => false,
		'hpadding'     => false,
		'vpadding'     => false,
		'fgcolor'      => array( 0, 0, 0 ),
		'bgcolor'      => false,
		'text'         => true,
		'font'         => 'helvetica',
		'fontsize'     => 8,
		'stretchtext'  => '',
	);

	/**
	 * UTF-8 label titles.
	 * WC_IOD_Admin_Print_Label constructor.
	 *
	 * @param array $args Shipping data.
	 */
	public function __construct( $args = array() ) {
		$this->args = $args;
	}

	/**
	 * Set PDF info.
	 */
	private function set_pdf_info() {
		$this->pdf->SetCreator( 'WordPress Plugin' );
		$this->pdf->SetAuthor( 'WordPress Plugin' );
		$this->pdf->SetTitle( 'Israeli Orange Delivery label' );
		$this->pdf->SetSubject( 'Israeli Orange Delivery shipping label' );
		$this->pdf->setPrintHeader( false );
		$this->pdf->setPrintFooter( false );
		$this->pdf->SetAutoPageBreak( false );
		$this->pdf->SetTextColor( 0, 0, 0 );
		$this->pdf->setRTL( true );
	}

	/**
	 * Create A4 label.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function create_a4_label() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['order_id'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order_id = absint( wp_unslash( $_GET['order_id'] ) );
		$order    = wc_get_order( $order_id );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$print_location        = isset( $_GET['print_location'] ) ? sanitize_text_field( wp_unslash( $_GET['print_location'] ) ) : 'A1';
		$customer_full_name    = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$customer_full_address = $order->get_billing_address_1() . ', ' . $order->get_billing_address_2() . ', ' . $order->get_billing_city() . ', ' . $order->get_billing_country() . ', ' . $order->get_billing_postcode();
		$customer_phone        = $order->get_billing_phone();
		$customer_city         = $order->get_billing_city();
		$customer_comment      = $order->get_customer_note();

		$this->pdf = new TCPDF( 'P', 'mm', 'A4', true, 'UTF-8', false );
		$this->set_pdf_info();

		$this->pdf->SetHeaderMargin( 0 );
		$this->pdf->SetFooterMargin( 0 );

		if ( 'B1' === $print_location ) {
			$this->pdf->SetMargins( 105, 10, 5 );
		} elseif ( 'A2' === $print_location ) {
			$this->pdf->SetMargins( 5, 100, 105 );
		} elseif ( 'B2' === $print_location ) {
			$this->pdf->SetMargins( 105, 100, 5 );
		} elseif ( 'A3' === $print_location ) {
			$this->pdf->SetMargins( 5, 200, 105 );
		} elseif ( 'B3' === $print_location ) {
			$this->pdf->SetMargins( 105, 200, 5 );
		} else {
			$this->pdf->SetMargins( 5, 10, 105 );
		}

		$this->pdf->SetFont( 'dejavusans', '', 10 );

		$this->pdf->setRTL( true );

		$this->pdf->AddPage();

		if ( 'B1' === $print_location ) {
			$this->pdf->SetY( 24 );
			$this->pdf->SetX( 94 );
		} elseif ( 'A2' === $print_location ) {
			$this->pdf->SetY( 118 );
			$this->pdf->SetX( 194 );
		} elseif ( 'B2' === $print_location ) {
			$this->pdf->SetY( 118 );
			$this->pdf->SetX( 94 );
		} elseif ( 'A3' === $print_location ) {
			$this->pdf->SetY( 218 );
			$this->pdf->SetX( 194 );
		} elseif ( 'B3' === $print_location ) {
			$this->pdf->SetY( 218 );
			$this->pdf->SetX( 94 );
		} else {
			$this->pdf->SetY( 24 );
			$this->pdf->SetX( 194 );
		}

		$this->pdf->SetFont( 'dejavusans', '', 12 );
		$this->pdf->StartTransform();
		$this->pdf->Rotate( -90 );
		$this->pdf->writeHTMLCell( 20, 10, '', '', $this->args['distrubution_line_code'] . '<br>' . $this->args['delivery_branch_code'], 0, 0, false, true, 'C' );
		$this->pdf->StopTransform();
		$this->pdf->Ln( 1 );

		if ( 'B1' === $print_location ) {
			$this->pdf->SetY( 5 );
		} elseif ( 'A2' === $print_location ) {
			$this->pdf->SetY( 100 );
		} elseif ( 'B2' === $print_location ) {
			$this->pdf->SetY( 100 );
		} elseif ( 'A3' === $print_location ) {
			$this->pdf->SetY( 200 );
		} elseif ( 'B3' === $print_location ) {
			$this->pdf->SetY( 200 );
		} else {
			$this->pdf->SetY( 5 );
		}

		$this->pdf->write1DBarcode( $this->args['delivery_number'], 'C128A', '', '', 70, 20, 0.33, $this->barcode_style );
		$this->pdf->Ln( 6 );

		$this->pdf->SetFont( 'dejavusans', '', 10 );
		$this->pdf->Cell( 70, 8, 'מאת: ' . get_option( 'wc_iod_seller_company_name' ), 1 );
		$this->pdf->Cell( 30, 8, '', 1 );
		$this->pdf->Ln();

		$this->pdf->SetFont( 'dejavusans', '', 10 );
		$this->pdf->Cell( 70, 8, $this->args['sender_company'] . ', ' . $this->args['sender_address_1'] . ', ' . $this->args['sender_address_2'] . ', ' . $this->args['sender_city'], 1, 0, '', false, '', 0, false, 'T', 'C' );
		$this->pdf->Cell( 30, 8, '1 -מ- 1', 1 );
		$this->pdf->Ln();

		$this->pdf->SetFont( 'dejavusans', 'B', 10 );
		$this->pdf->Cell( 70, 8, "{$customer_full_name}  {$customer_phone}", 1, 0, '', false, '', 0, false, 'T', 'C' );
		$this->pdf->Cell( 30, 8, $customer_city, 1 );
		$this->pdf->Ln();

		$this->pdf->SetFont( 'dejavusans', 'B', 10 );
		$this->pdf->Cell( 70, 8, $customer_full_address, 1 );
		$this->pdf->Cell( 30, 8, '0.00', 1 );
		$this->pdf->Ln();

		$this->pdf->SetFont( 'dejavusans', '', 10 );
		$this->pdf->Cell( 70, 8, $customer_phone, 1 );
		$this->pdf->SetFont( 'dejavusans', 'B', 10 );
		$this->pdf->Cell( 30, 8, 'רגיל', 1 );
		$this->pdf->Ln();

		$this->pdf->SetFont( 'dejavusans', '', 10 );
		$this->pdf->Cell( 100, 8, $customer_comment, 1 );
		$this->pdf->Ln();

		$this->pdf->SetFont( 'dejavusans', '', 10 );
		$this->pdf->Cell( 100, 8, gmdate( 'd/m/Y', strtotime( $order->get_date_created() ) ), 0, 0, 'L' );
		$this->pdf->Ln();

		$this->pdf->setRTL( false );

		$this->pdf->Output( 'wc_iod_label_' . $this->ship_data['delivery_number'] . '.pdf', 'I', true );
	}
}
