<?php

/**
 * Plugin Name: WooCommerce Order Test
 * Plugin URI:  http://www.wpfixit.com
 * Description: A payment gateway plugin for WooCommerce to see if your checkout works.
 * Author:      WP Fix It
 * Author URI:  http://www.wpfixit.com
 * Version:     1.6
 */

function sb_wc_test_init() {
	if (!class_exists('WC_Payment_Gateway')) {
		return;
	}
	
	class WC_Gateway_sb_test extends WC_Payment_Gateway {
	
		public function __construct() {
			$this->id = 'sb_test';
			$this->has_fields = false;
			$this->method_title = __( 'Order Test', 'woocommerce' );
			$this->init_form_fields();
			$this->init_settings();
			$this->title = 'Order Test Gateway';
	
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
		
		function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woocommerce' ),
					'type' => 'checkbox',
					'label' => __( 'Enable order test gateway', 'woocommerce' ),
					'default' => 'yes'
				)
			);
		}
	    
		
		public function admin_options() {
			echo '	<h3>Order Test Gateway</h3><br>
			<p>Enable this below to test the checkout process on your site.  Only admin users will see this option on the checkout page.</p>
				<table class="form-table">';
				
			$this->generate_settings_html();
			
			echo '	</table>';
		}
	
		public function process_payment( $order_id ) {
			global $woocommerce;
	    
			$order = new WC_Order( $order_id );
			$order->payment_complete();
			$order->reduce_order_stock();
			$woocommerce->cart->empty_cart();
	
			return array(
				'result' => 'success',
				//'redirect' => add_query_arg('key', $order->order_key, add_query_arg('order', $order->id, get_permalink(woocommerce_get_page_id('thanks')))),
				'redirect' => $order->get_checkout_order_received_url()
			);
		}
	
	}	

	function add_sb_test_gateway( $methods ) {
		if (current_user_can('administrator') || WP_DEBUG) {
			$methods[] = 'WC_Gateway_sb_test';
		}
		
		return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'add_sb_test_gateway' );
	
}

add_filter('plugins_loaded', 'sb_wc_test_init' );

?>