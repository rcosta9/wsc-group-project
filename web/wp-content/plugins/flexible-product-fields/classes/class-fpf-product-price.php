<?php
/**
 * Product price.
 *
 * @package Flexible Product Fields
 */

/**
 * Product price calculation and format.
 */
class FPF_Product_Price {

	/**
	 * Get price in formatted by WooCommerce.
	 *
	 * @param float $price .
	 *
	 * @return string
	 */
	public function wc_price( $price ) {
		$price = wc_price( $price );
		$price = strip_tags( $price );
		return $price;
	}

	/**
	 * Calculate percent to price.
	 *
	 * @param float      $percent Percent.
	 * @param WC_Product $product Product.
	 *
	 * @return float
	 */
	private function calculate_percent_to_price( $percent, $product ) {
		$product_extended_info = new FPF_Product_Extendend_Info( $product );
		if ( ! $product_extended_info->is_type_variable() && ! $product_extended_info->is_type_grouped() ) {
			$product_price = $product->get_price();
			$price         = $product_price * $percent / 100;
		} else {
			$price = 0;
		}
		return $price;
	}

	/**
	 * Calculate price.
	 *
	 * @param float      $price_or_percent Price or percent (percent, if price type is percent).
	 * @param string     $price_type Price type.
	 * @param WC_Product $product Product.
	 *
	 * @return float
	 */
	public function calculate_price( $price_or_percent, $price_type, WC_Product $product ) {
		$sign = 1;
		if ( $price_or_percent < 0 ) {
			$sign             = - 1;
			$price_or_percent = $price_or_percent * $sign;
		}

		if ( 'percent' === $price_type ) {
			$price = $this->calculate_percent_to_price( $price_or_percent, $product );
		} else {
			$price = $price_or_percent;
		}

		$price = round( $price, wc_get_price_decimals() );

		$price = $this->multicurrency_calculate_price( $price );

		/**
		 * Can modify calculated price.
		 *
		 * @param float      $price Calculated price.
		 * @param WC_Product $product Processed product.
		 *
		 * @return float
		 */
		$price = apply_filters( 'flexible_product_fields_calculate_price', $price, $product );

		$price = $sign * $price;

		return $price;
	}

	/**
	 * Pass calculated price to multi currency plugins.
	 * Returns converted value for changed currency (if changed).
	 * Currently supported multi currency plugins:
	 *   - WPML
	 *
	 * @param float $price .
	 *
	 * @return float
	 */
	private function multicurrency_calculate_price( $price ) {
		return apply_filters( 'wcml_raw_price_amount', $price );
	}

	/**
	 * Prepare price to display.
	 *
	 * @param WC_Product $product .
	 * @param float      $price_to_display .
	 *
	 * @return float|string
	 */
	public function prepare_price_to_display( WC_Product $product, $price_to_display ) {
		$price_sign       = $price_to_display < 0 ? -1 : 1;
		$price_to_display = abs( $price_to_display );
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		if ( 'excl' === $tax_display_mode ) {
			$price_to_display = wpdesk_get_price_excluding_tax( $product, 1, $price_to_display );
		} else {
			$price_to_display = wpdesk_get_price_including_tax( $product, 1, $price_to_display );
		}
		return $price_sign * $price_to_display;
	}


}
