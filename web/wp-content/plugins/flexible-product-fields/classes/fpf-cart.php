<?php

/**
 * Handles WooCommerce add to cart.
 */
class FPF_Cart {

	/**
	 * Priority before default
	 */
	const HOOK_BEFORE_DEFAULT = 9;

	/**
	 * Priority after default
	 */
	const HOOK_AFTER_DEFAULT = 20;

	/**
	 * Plugin
	 *
	 * @var Flexible_Product_Fields_Plugin
	 */
	private $_plugin;

	/**
	 * @var FPF_Product_Fields
	 */
    private $_product_fields = null;

	/**
	 * @var FPF_Product|null
	 */
    private $_product = null;

	/**
	 * Product price.
	 *
	 * @var FPF_Product_Price|null
	 */
	private $product_price = null;

	/**
	 * FPF_Cart constructor.
	 *
	 * @param Flexible_Product_Fields_Plugin $plugin
	 * @param FPF_Product_Fields $product_fields
	 * @param FPF_Product $product
	 */
    public function __construct( Flexible_Product_Fields_Plugin $plugin, FPF_Product_Fields $product_fields, FPF_Product $product, FPF_Product_Price $product_price ) {
        $this->_plugin = $plugin;
        $this->_product_fields = $product_fields;
        $this->_product = $product;
	    $this->product_price = $product_price;
        add_action( 'plugins_loaded', array( $this, 'hooks' ) );
    }

	/**
	 * Define hooks
	 */
    public function hooks() {

	    add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'woocommerce_add_to_cart_handler' ), 10, 2 );

        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), self::HOOK_AFTER_DEFAULT, 2 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'woocommerce_get_item_data' ), 10, 2 );

	    if ( defined( 'WC_VERSION' ) ) {
		    if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
			    add_action( 'woocommerce_add_order_item_meta', array(
				    $this,
				    'woocommerce_add_order_item_meta'
			    ), 10, 2 );
		    } else {
			    add_action( 'woocommerce_new_order_item', array( $this, 'woocommerce_new_order_item' ), 10, 3 );
		    }
	    }
    }

	/**
	 * Handle filters and actions for add to cart process but not for grouped product.
	 * @param string $type
	 * @param int $product_id
	 *
	 * @return string
	 */
    public function woocommerce_add_to_cart_handler( $type, $product_id ) {
		if ( $type != FPF_Product_Extendend_Info::PRODUCT_TYPE_GROUPED ) {
			add_filter( 'woocommerce_add_cart_item', array( $this, 'woocommerce_add_cart_item' ), self::HOOK_AFTER_DEFAULT, 1 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ), self::HOOK_AFTER_DEFAULT, 3 );
		}
		return $type;
	}

	/**
	 * @param int $item_id
	 * @param array $values
	 */
	public function woocommerce_add_order_item_meta( $item_id, $values ) {
        if ( ! empty( $values['flexible_product_fields'] ) ) {
            foreach ( $values['flexible_product_fields'] as $field ) {
                $name = $field['name'];
                wc_add_order_item_meta( $item_id, $name, $field['value'] );
            }
        }
    }

	/**
	 * @param int $item_id
	 * @param mixed $item
	 * @param int $order_id
	 */
    public function woocommerce_new_order_item( $item_id, $item, $order_id ) {
    	if ( $item instanceof WC_Order_Item_Product ) {
		    if ( !empty( $item->legacy_values) && !empty( $item->legacy_values['flexible_product_fields'] ) ) {
			    foreach ( $item->legacy_values['flexible_product_fields'] as $field ) {
				    $name = $field['name'];
				    wc_add_order_item_meta( $item_id, $name, $field['value'] );
			    }
		    }
	    }
    }

	/**
	 * @param array $cart_item
	 * @param array $values
	 *
	 * @return mixed
	 */
    public function woocommerce_get_cart_item_from_session( $cart_item, $values ) {
        if ( ! empty( $values['flexible_product_fields'] ) ) {
            $cart_item['flexible_product_fields'] = $values['flexible_product_fields'];
            $cart_item = $this->woocommerce_add_cart_item( $cart_item );
        }
        return $cart_item;
    }

	/**
	 * @param array $cart_item
	 *
	 * @return array mixed
	 */
    public function woocommerce_add_cart_item( $cart_item ) {
        if ( ! empty( $cart_item['flexible_product_fields'] ) ) {
            $extra_cost = 0;
            foreach ( $cart_item['flexible_product_fields'] as $field ) {
                if ( isset( $field['price_type'] ) && $field['price_type'] != '' && isset( $field['price'] ) && floatval( $field['price'] ) != 0 ) {
	                $price = floatval( $field['price'] );
                    $extra_cost += $price;
                }
            }
	        $cart_item['data']->set_price( $cart_item['data']->get_price() + $extra_cost );
        }
        return $cart_item;
    }

	/**
	 * Get field data from posted fields.
	 *
	 * @param array $field Field.
	 * @param int $product_id Product ID.
	 * @param int $variation_id Variation ID.
	 *
	 * @return array|bool|WP_Error
	 * @throws Exception
	 */
    private function get_field_data( $field, $product_id, $variation_id ) {
        $ret = false;
        $value = null;
        if ( isset( $_POST[ $field['id'] ] ) ) {
	        if ( isset( $field['type'] ) && FPF_Product_Fields::TYPE_TEXTAREA === $field['type'] ) {
		        $value = sanitize_textarea_field( wp_unslash( $_POST[ $field['id'] ] ) );
	        } else {
		        $value = sanitize_text_field( wp_unslash( $_POST[ $field['id'] ] ) );
	        }
        }
        if ( $value == null && $field['required'] == '1' ) {
	        throw new Exception( sprintf( __( '<strong>%s</strong>  is required field.', 'flexible-product-fields' ), $field['title'] ) );
        }
        if ( $field['type'] == 'text' || $field['type'] == 'textarea' ) {
        	if ( !empty( $field['max_length'] ) ) {
        		if ( strlen( $value ) > intval( $field['max_length'] ) ) {
        			throw new Exception( sprintf( __( 'Exceeded maximum number of characters for the %s field. (max: %s)', 'flexible-product-fields' ), $field['title'], $field['max_length'] ) );
		        }
	        }
        }
        if ( $value != null ) {
            $ret = array(
                'name' => $field['title'],
                'value' => $value,
            );
            if ( $field['type'] == 'checkbox' ) {
            	if ( !isset( $field['value'] ) ) {
            	    $ret['value'] = __( 'yes', 'flexible-product-fields' );
	            }
	            else {
		            $ret['value'] = $field['value'];
	            }
            }
	        $fields_types_by_type = $this->_product_fields->get_field_types_by_type();
            if ( $fields_types_by_type[$field['type']]['has_price'] ) {
            	if ( !isset( $field['price_type'] ) ) {
		            $field['price_type'] = 'fixed';
	            }
                if ( isset($field['price_type']) && $field['price_type'] != '' && isset($field['price']) && $field['price'] != '' ) {
                    $ret['price_type'] = $field['price_type'];
                    $ret['price'] = $field['price'];
                }
            }
            if ( $fields_types_by_type[$field['type']]['has_options'] ) {
                foreach ( $field['options'] as $option ) {
                    if ( trim( $option['value'] ) === $ret['value'] ) {
                        $ret['value'] = $option['label'];
                        if ( $fields_types_by_type[$field['type']]['has_price_in_options'] ) {
	                        if ( isset( $option['price_type'] ) && $option['price_type'] != '' && isset( $option['price'] ) && $option['price'] != '' ) {
		                        $ret['price_type'] = $option['price_type'];
		                        $ret['price']      = $option['price'];
	                        }
                        }
                    }
                }
            }
            if ( isset($ret['price_type']) && $ret['price_type'] != '' && isset($ret['price']) && $ret['price'] != '' ) {
                if ( isset( $variation_id ) && $variation_id != '' ) {
                    $product_id = $variation_id;
                }
                $product = wc_get_product( $product_id );
                $price = $this->product_price->calculate_price( floatval( $ret['price'] ), $ret['price_type'], $product );
	            $sign = 1;
	            if ( $price < 0 ) {
		            $sign = -1;
		            $price = $price * $sign;
	            }

	            $price = $sign * $price;
	            $ret['price'] = $price ;

                $price = $this->product_price->wc_price( $price );

                $ret['value'] .= ' (' . $price . ')';

            }
        }
        return $ret;
    }

	/**
	 * @param array $other_data
	 * @param array $cart_item
	 *
	 * @return array
	 */
    public function woocommerce_get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['flexible_product_fields'] ) ) {
			foreach ( $cart_item['flexible_product_fields'] as $field ) {
				$name = $field['name'];
                $other_data[] = array(
                    'name'    => $name,
                    'value'   => $field['value'],
                    'display' => (isset( $field['display'] ) ? $field['display'] : '')
                );
			}
        }
        return $other_data;
    }

	/**
	 * @param array $cart_item_data
	 * @param int $product_id
	 * @param int $variation_id
	 *
	 * @return array
	 * @throws Exception
	 */
    public function woocommerce_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
	    $product_data = wc_get_product( $product_id );
        $fields = $this->_product->get_translated_fields_for_product( $product_data );

        $post_data = wp_unslash( $_POST );
	    $fields = apply_filters( 'flexible_product_fields_apply_logic_rules', $fields, $post_data );

        foreach ( $fields['fields'] as $field ) {
            $data = $this->get_field_data( $field, $product_id, $variation_id );
            if ( $data ) {
                if ( !isset( $cart_item_data['flexible_product_fields'] ) ) {
                    $cart_item_data['flexible_product_fields'] = array();
                }
                $cart_item_data['flexible_product_fields'][] = $data;
            }
        }
        return $cart_item_data;
    }


}
