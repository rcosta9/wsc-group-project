<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FPF_Product_Extendend_Info {

	/**
	 * Product type variable
	 */
	const PRODUCT_TYPE_VARIABLE = 'variable';

	/**
	 * Product type grouped
	 */
	const PRODUCT_TYPE_GROUPED = 'grouped';

	/**
	 * @var WC_Product
	 */
	private $product;

	/**
	 * FPF_Product_Extendend_Info constructor.
	 *
	 * @param WC_Product $product
	 */
	public function __construct( WC_Product $product ) {
		$this->product = $product;
	}

	/**
	 * @param $product_id
	 *
	 * @return static
	 */
	public static function create_from_id( $product_id ) {
		return new static( wc_get_product( $product_id ) );
	}

	/**
	 * @return bool
	 */
	public function is_type_grouped() {
		return $this->product->is_type( self::PRODUCT_TYPE_GROUPED );
	}

	/**
	 * @return bool
	 */
	public function is_type_variable() {
		return $this->product->is_type( self::PRODUCT_TYPE_VARIABLE );
	}

}