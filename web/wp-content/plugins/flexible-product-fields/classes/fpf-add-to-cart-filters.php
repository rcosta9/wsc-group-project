<?php

/**
 * Class FPF_Add_To_Cart_Filters
 * Handles filters for add to cart button.
 */
class FPF_Add_To_Cart_Filters implements VendorFPF\WPDesk\PluginBuilder\Plugin\Hookable {

	/**
	 * FPF Product.
	 *
	 * @var FPF_Product
	 */
	private $fpf_product;

	/**
	 * In product loop?
	 *
	 * @var bool
	 */
	private $in_product_loop = false;

	/**
	 * FPF_Add_To_Cart_Filters constructor.
	 *
	 * @param FPF_Product $fpf_product FPF Product.
	 */
	public function __construct( FPF_Product $fpf_product ) {
		$this->fpf_product = $fpf_product;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 15 );
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_product_loop_start', array( $this, 'detect_product_loop_start' ) );
		add_filter( 'woocommerce_product_loop_end', array( $this, 'detect_product_loop_end' ) );
	}

	/**
	 * Detect product loop start.
	 *
	 * @param string $content content.
	 * @return string
	 */
	public function detect_product_loop_start( $content ) {
		$this->in_product_loop = true;
		return $content;
	}

	/**
	 * Detect product loop end.
	 *
	 * @param string $content content.
	 * @return string
	 */
	public function detect_product_loop_end( $content ) {
		$this->in_product_loop = false;
		return $content;
	}

	/**
	 * Add to cart text.
	 *
	 * @param string $text Text.
	 *
	 * @return string
	 */
	public function add_to_cart_text( $text ) {
		global $product;
		if ( $product instanceof WC_Product && $this->in_product_loop ) {
			$product_extended_info = new FPF_Product_Extendend_Info( $product );
			if ( ! $product_extended_info->is_type_grouped() && $this->fpf_product->product_has_required_field( $product ) ) {
				$text = __( 'Select options', 'flexible-product-fields' );
			}
		}
		return $text;
	}


	/**
	 * Add to cart URL.
	 *
	 * @param string $url URL.
	 *
	 * @return string
	 */
	public function add_to_cart_url( $url ) {
		global $product;
		if ( $product instanceof WC_Product && $this->in_product_loop ) {
			if ( $this->fpf_product->product_has_required_field( $product ) ) {
				$url = get_permalink( wpdesk_get_product_id( $product ) );
			}
		}
		return $url;
	}

}
