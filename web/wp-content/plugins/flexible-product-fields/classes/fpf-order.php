<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FPF_Order {

	private $_plugin = null;

	public function __construct( Flexible_Product_Fields_Plugin $plugin ) {
		$this->_plugin               = $plugin;
		$this->hooks();
	}

	public function hooks() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	public function plugins_loaded() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '>=' ) ) {
			add_filter( 'woocommerce_order_item_display_meta_value', array(
				$this,
				'woocommerce_order_item_display_meta_value'
			), 10, 3 );
		}
	}

	/**
	 * @param string $display_value
	 * @param WC_Meta_Data $meta
	 * @param WC_Order_Item $order_item
	 */
	public function woocommerce_order_item_display_meta_value( $display_value, $meta, $order_item ) {
		/*
		revert sanitize function for display_value https://github.com/woocommerce/woocommerce/commit/2e08bfdb3369bbb51161455b55567ebf334244d0#diff-71b4f7d1db1e3924579021706e3575e6
		*/
		$display_value = $meta->value;

		$attribute_key = str_replace( 'attribute_', '', $meta->key );
		if ( taxonomy_exists( $attribute_key ) ) {
			$term = get_term_by( 'slug', $meta->value, $attribute_key );
			if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
				$display_value = $term->name;
			}
		}

		return $display_value;
	}
}