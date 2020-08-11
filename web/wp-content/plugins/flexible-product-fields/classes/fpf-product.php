<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FPF_Product {

	/**
	 * Priority before default
	 */
	const HOOK_BEFORE_DEFAULT = 9;

	/**
	 * Priority after default
	 */
	const HOOK_AFTER_DEFAULT = 20;

	/**
     * Is hook woocommerce_before_add_to_cart_button already fired?
     *
	 * @var bool
	 */
	private $is_woocommerce_before_add_to_cart_button_fired = false;

	/**
	 * @var null|Flexible_Product_Fields
	 */
    private $_plugin = null;

	/**
	 * @var FPF_Product_Fields|null
	 */
    private $_product_fields = null;

	/**
	 * Product price.
	 *
	 * @var FPF_Product_Price|null
	 */
	private $product_price = null;

	/**
	 * FPF_Product constructor.
	 *
	 * @param Flexible_Product_Fields $plugin
	 * @param FPF_Product_Fields $product_fields
	 */
    public function __construct( Flexible_Product_Fields_Plugin $plugin, FPF_Product_Fields $product_fields, FPF_Product_Price $product_price ) {
        $this->_plugin = $plugin;
        $this->_product_fields = $product_fields;
        $this->product_price = $product_price;
        $this->hooks();
    }

	/**
	 *
	 */
    public function hooks() {

        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woocommerce_before_add_to_cart_button' ) );
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'woocommerce_after_add_to_cart_button' ) );

        add_filter( 'woocommerce_product_supports', array( $this, 'woocommerce_product_supports' ), 10, 3 );

        add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'woocommerce_add_to_cart_handler' ), 10, 2 );

    }

	/**
	 * @param string $type
	 * @param int $product_id
	 *
	 * @return string
	 */
    public function woocommerce_add_to_cart_handler( $type, $product_id ) {
	    if ( $type != FPF_Product_Extendend_Info::PRODUCT_TYPE_GROUPED ) {
		    add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woocommerce_add_to_cart_validation' ), self::HOOK_AFTER_DEFAULT, 3 );
        }
        return $type;
    }

	/**
	 * @param string $error
	 */
    public function add_error( $error ) {
        wc_add_notice( $error, 'error' );
    }

	/**
     * Is field required?
     *
     * @param array $field .
     *
	 * @return bool
	 */
    private function is_field_required( $field ) {
	    return isset( $field['required'] ) && is_numeric( $field['required'] ) && intval( $field['required'] ) === 1;
    }

	/**
	 * @param array $field
	 * @param mixed $value
	 *
	 * @throws Exception
	 */
    private function validate_field_requirement( $field, $value ) {
        if ( $this->is_field_required( $field ) && ( empty( $value ) || $value == '' ) ) {
            throw new Exception( sprintf( __( '<strong>%s</strong>  is required field.', 'flexible-product-fields' ), $field['title'] ) );
        }
    }

	/**
	 * @param array $field
	 * @param mixed $value
	 *
	 * @throws Exception
	 */
	private function validate_field_value( $field, $value ) {
	    if ( isset( $field['has_options'] ) && $field['has_options'] && ( $this->is_field_required( $field ) || ! empty( $value ) ) ) {
		    $valid_values = array_map( function( $options ) {
			        return $options['value'];
			    },
                $field['options']
            );
	        if ( ! in_array( $value, $valid_values, true ) ) {
		        throw new Exception( sprintf( __( '%1$s has invalid value.', 'flexible-product-fields' ), sprintf( '<strong>%1$s</strong>', $field['title'] ) ) );
            }
        }
	}

	/**
	 * @param bool $passed
	 * @param int $product_id
	 * @param int $qty
	 * @param null|array $post_data
	 *
	 * @return bool
	 */
    public function woocommerce_add_to_cart_validation( $passed, $product_id, $qty, $post_data = null ) {
        if ( is_null( $post_data ) && isset( $_POST ) ) {
            $post_data = $_POST;
        }
        $post_data = wp_unslash( $post_data );

        $product = wc_get_product( $product_id );
        $fields = $this->get_translated_fields_for_product( $product );

        $fields = apply_filters( 'flexible_product_fields_apply_logic_rules', $fields, $post_data );

        if ( is_array( $fields['fields'] ) ) {
            foreach ( $fields['fields'] as $field ) {
                $value = null;
                if ( isset( $post_data[ $field['id'] ] ) ) {
                	if ( isset( $field['type'] ) && FPF_Product_Fields::TYPE_TEXTAREA === $field['type'] ) {
		                $value = sanitize_textarea_field( $post_data[ $field['id'] ] );
	                } else {
		                $value = sanitize_text_field( $post_data[ $field['id'] ] );
	                }
                }
                try {
                    $this->validate_field_requirement($field, $value);
                    $this->validate_field_value( $field, $value );
                } catch (Exception $e) {
                    $this->add_error($e->getMessage());
                    $passed = false;
                }
            }
        }

        return $passed;
    }

	/**
	 * @param bool $supports
	 * @param string $feature
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
    public function woocommerce_product_supports( $supports, $feature, $product ) {
        if ( 'ajax_add_to_cart' === $feature && $this->product_has_required_field( $product ) ) {
            $supports = false;
        }
        return $supports;
    }

	/**
	 * @param string $type
	 *
	 * @return array
	 */
    public function get_field_type( $type ) {
        $ret = array();
        $field_types = $this->_product_fields->get_field_types();
        foreach ( $field_types as $field_type ) {
            if ( $field_type['value'] == $type ) {
                return $field_type;
            }
        }
        return $ret;
    }

	/**
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
    public function product_has_required_field( $product ) {
        $fields = $this->get_translated_fields_for_product( $product );
        return $fields['has_required'];
    }

	/**
     * Get translated fields for product.
     * Titles and labels will be translated to current language.
     *
	 * @param WC_Product $product
	 * @param bool|string $hook
	 *
	 * @return array
	 */
    public function get_translated_fields_for_product( $product, $hook = false ) {
        return $this->translate_fields_titles_and_labels( $this->get_fields_for_product( $product, $hook ) );
    }

	/**
	 * @param WC_Product $product
	 * @param bool|string $hook
	 *
	 * @return array
	 */
	public function get_fields_for_product( $product, $hook = false ) {
		$cache_key = 'product_' . wpdesk_get_product_id( $product );
		if ( $hook ) {
			$cache_key .= '_h_' . $hook;
		}
		$ret = $this->_product_fields->cache_get( $cache_key );
		if ( $ret === false ) {
			$ret = array(
				'posts' => array(),
				'fields' => array(),
				'display_fields' => array(),
				'has_required' => false
			);
			$fields_posts = array();
			$args = array(
				'post_type' => 'fpf_fields',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' => '_assign_to',
						'value' => 'product',
						'compare' => '='
					),
					array(
						'key' => '_product_id',
						'value' => wpdesk_get_product_id( $product ),
						'compare' => '='
					),
				),
			);
			if ( $hook ) {
				$args['meta_query'][] = array(
					'key' => '_section',
					'value' => $hook,
					'compare' => '='
				);
			}
			$posts = get_posts($args);
			foreach ($posts as $post) {
				$ret['posts'][$post->ID] = $post;
			}

			$categories = wp_get_post_terms( wpdesk_get_product_id( $product ), 'product_cat', array( 'fields' => 'ids' ) );
			foreach ( $categories as $category ) {
				$cat_cache_key = 'category_' . $category;
				if ( $hook ) {
					$cat_cache_key .= '_h_' . $hook;
				}
				$posts = $this->_product_fields->cache_get( $cat_cache_key );
				if ( $posts === false ) {
					$args = array(
						'post_type' => 'fpf_fields',
						'posts_per_page' => -1,
						'meta_query' => array(
							array(
								'key' => '_assign_to',
								'value' => 'category',
								'compare' => '='
							),
							array(
								'key' => '_category_id',
								'value' => $category,
								'compare' => 'in'
							),
						),
					);
					if ($hook) {
						$args['meta_query'][] = array(
							'key' => '_section',
							'value' => $hook,
							'compare' => '='
						);
					}
					$posts = get_posts($args);
					$this->_product_fields->cache_set( $cat_cache_key, $posts );
				}
				foreach ($posts as $post) {
					$ret['posts'][$post->ID] = $post;
				}
			}

			$cat_cache_key = 'all';
			if ( $hook ) {
				$cat_cache_key .= '_h_' . $hook;
			}
			$posts = $this->_product_fields->cache_get( $cat_cache_key );
			if ( $posts === false ) {
				$args = array(
					'post_type' => 'fpf_fields',
					'posts_per_page' => -1,
					'meta_query' => array(
						array(
							'key' => '_assign_to',
							'value' => 'all',
							'compare' => '='
						),
					),
				);
				if ($hook) {
					$args['meta_query'][] = array(
						'key' => '_section',
						'value' => $hook,
						'compare' => '='
					);
				}
				$posts = get_posts($args);
				$this->_product_fields->cache_set( $cat_cache_key, $posts );
			}
			foreach ($posts as $post) {
				$ret['posts'][$post->ID] = $post;
			}
			$ret['posts'] = apply_filters( 'flexible_product_fields_sort_groups_posts', $ret['posts'] );
			foreach ( $ret['posts'] as $key => $post ) {
				$ret['posts'][$key]->fields_meta = get_post_meta( $post->ID, '_fields', true );
				if ( is_array( $ret['posts'][$key]->fields_meta ) ) {
					$ret['fields'] = array_merge($ret['fields'], $ret['posts'][$key]->fields_meta);
				}
			}
			foreach ( $ret['fields'] as $key => $field ) {
				$field_type = $this->get_field_type( $field['type'] );
				$ret['fields'][$key]['has_price'] = isset( $field_type['has_price'] ) ? $field_type['has_price'] : false;
				$ret['fields'][$key]['has_price_in_options'] = isset( $field_type['has_price_in_options'] ) ? $field_type['has_price_in_options'] : false;
				$ret['fields'][$key]['has_options'] = $field_type['has_options'];
				if ( empty( $field_type['is_available'] ) && !$field_type['is_available'] ) {
					unset( $ret['fields'][$key] );
				}
				else {
					if ( $field['required'] == 1 ) {
						$ret['has_required'] = true;
					}
				}
			}
			$this->_product_fields->cache_set( $cache_key, $ret );
		}
		return $ret;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
    public function get_logic_rules_for_product( $product ) {
        $fields = $this->get_translated_fields_for_product( $product );
	    $rules = array();
        foreach ( $fields['fields'] as $field ) {
            if ( isset( $field['logic'] ) && $field['logic'] == '1' && isset( $field['logic_operator'] ) && isset( $field['logic_rules'] ) ) {
                $rules[$field['id']] = array();
	            $rules[$field['id']]['rules'] = $field['logic_rules'];
	            $rules[$field['id']]['operator'] = $field['logic_operator'];
            }
        }
        return $rules;
    }

	/**
	 * @param WC_Product $product
	 * @param bool|string $hook
	 *
	 * @return array
	 */
    public function create_fields_for_product( $product, $hook ) {
        $fields = $this->get_translated_fields_for_product( $product, $hook );
        foreach ( $fields['fields'] as $field ) {
            $fields['display_fields'][] = $this->create_field( $field, $product );
        }
        return $fields;
    }

	/**
	 * @param string $field
	 * @param string $key
	 * @param array $args
	 * @param string $value
	 *
	 * @return string
	 */
	public function woocommerce_form_field_radio( $field, $key, $args, $value ) {

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce'  ) . '">*</abbr>';
		} else {
			$required = '';
		}

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( ! empty( $args['validate'] ) ) {
			foreach( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$field = '';

		$field_container = '<fieldset class="form-row %1$s" id="%2$s">%3$s</fieldset>';

        $label_id = current( array_keys( $args['options'] ) );
        if ( ! empty( $args['options'] ) ) {
            foreach ( $args['options'] as $option_key => $option_text ) {
                $input = '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
                $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) .'">' . $input . ' ' . $option_text . '</label>';
            }
        }

		if ( ! empty( $field ) ) {
			$field_html = '';

			if ( $args['label'] && 'checkbox' != $args['type'] ) {
				$field_html .= '<legend for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</legend>';
			}

			$field_html .= $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
			}

			$container_class = esc_attr( implode( ' ', $args['class'] ) );
			$container_id = esc_attr( $args['id'] ) . '_field';

			$after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';

			$field = sprintf( $field_container, $container_class, $container_id, $field_html ) . $after;
		}

		return $field;
	}

	/**
	 * @param array $field
	 * @param WC_Product $product
	 *
	 * @return string
	 */
    public function create_field( array $field, WC_Product $product ) {
        $args = array(
            'type'          => $field['type'],
            'label'         => $field['title'],
            'placeholder'   => isset( $field['placeholder'] ) && $field['placeholder'] != '' ? $field['placeholder'] : '',
            'class'         => isset( $field['css_class'] ) ? array( $field['css_class'] ) : array( '' ),
        );
        $key = $field['id'];
        $field_type = $this->get_field_type( $field['type'] );
        $value = '';
        if ( isset( $_POST[$field['id']] ) ) {
	        if ( isset( $field['type'] ) && FPF_Product_Fields::TYPE_TEXTAREA === $field['type'] ) {
		        $value = sanitize_textarea_field( wp_unslash( $_POST[ $field['id'] ] ) );
	        } else {
		        $value = sanitize_text_field( wp_unslash( $_POST[ $field['id'] ] ) );
	        }
        }
        if ( $field_type['has_price'] ) {
            if ( !isset( $field['price_type'] ) ) {
	            $field['price_type'] = 'fixed';
            }
            if ( isset( $field['price_type'] ) && $field['price_type'] != '' && isset( $field['price'] ) && $field['price'] != '' ) {
                $price = $this->product_price->calculate_price( floatval( $field['price'] ), $field['price_type'], $product );
                $price_to_display = $this->product_price->wc_price( $this->product_price->prepare_price_to_display( $product, $price ) );
	            $args['label'] .= ' <span id="' . $key . '_price">(' . $price_to_display . ')</span>';
            }
        }
        if ( $field_type['has_options'] ) {
            $options = array();
            if ( is_array( $field['options'] ) ) {
                foreach ( $field['options'] as $option ) {
                    $options[$option['value']] = $option['label'];
	                if ( $field_type['has_price_in_options'] ) {
                        if ( isset( $option['price_type'] ) && $option['price_type'] != '' && isset( $option['price'] ) && $option['price'] != '' ) {
	                        $price                       = $this->product_price->calculate_price( floatval( $option['price'] ), $option['price_type'], $product );
	                        $price_to_display            = $this->product_price->wc_price( $this->product_price->prepare_price_to_display( $product, $price ) );
	                        if ( $field['type'] == 'radio' ) {
		                        $options[ $option['value'] ] .= ' <span id="' . $key . '_' . $option['value'] . '_price">(' . $price_to_display . ')</span>';
	                        }
	                        else {
		                        $options[ $option['value'] ] .= ' (' . $price_to_display . ')';
                            }
                        }
                    }
                }
            }
            $args['options'] = $options;
        }
        if ( isset( $field['max_length'] ) ) {
            $args['custom_attributes'] = array( 'maxlength' => $field['max_length'] );
        }
        if ( $field_type['has_required'] && isset( $field['required'] ) && $field['required'] == '1' ) {
            $args['label'] .= ' <abbr class="required" title="' . __( 'Required field', 'flexible-product-fields' ) . '">*</abbr>';
            $args['class'][] = 'fpf-required';
        }
        $args['input_class'] = array( 'fpf-input-field' );
	    if ( $field['type'] == 'fpfdate' ) {
	        if ( empty( $args['custom_attributes'] ) ) {
		        $args['custom_attributes'] = array();
            }
		    $args['custom_attributes']['date_format'] = $field['date_format'];
		    $args['custom_attributes']['days_before'] = '';
		    if ( isset( $field['days_before'] ) ) {
			    $args['custom_attributes']['days_before'] = $field['days_before'];
			    if ( $field['days_before'] == '0' ) {
				    $args['custom_attributes']['days_before'] = '00';
			    }
		    }
		    $args['custom_attributes']['days_after'] = '';
		    if ( isset( $field['days_after'] ) ) {
			    $args['custom_attributes']['days_after'] = $field['days_after'];
			    if ( $field['days_after'] == '0' ) {
				    $args['custom_attributes']['days_after'] = '00';
			    }
		    }
	    }
        if ( $field['type'] == 'heading' ) {
	        $class = implode( '', $args['class'] );
            $ret = $this->_plugin->load_template(
            	'heading',
	            'fields',
	            array(
	            	'title' => $field['title'],
		            'type' => $field['type'],
		            'args' => $args,
		            'key' => $key,
		            'class' => $class,
	            )
            );
        }
	    else if ( $field['type'] == 'fpfdate' ) {
		    $ret = $this->_plugin->load_template( 'date', 'fields', array( 'key' => $key, 'args' => $args, 'value' => $value, 'type' => $field['type'] ) );
	    }
        else if ( $field['type'] == 'radio' ) {
            add_filter( 'woocommerce_form_field_radio', array( $this, 'woocommerce_form_field_radio' ), 10, 4 );
	        $ret = $this->_plugin->load_template( $field['type'], 'fields', array( 'key' => $key, 'args' => $args, 'value' => $value, 'type' => $field['type'] ) );
	        remove_filter( 'woocommerce_form_field_radio', array( $this, 'woocommerce_form_field_radio' ), 10 );
        }
        else {
            $ret = $this->_plugin->load_template( $field['type'], 'fields', array( 'key' => $key, 'args' => $args, 'value' => $value, 'type' => $field['type'] ) );
        }
        return $ret;
    }

	/**
	 * @param bool|string $hook
	 */
    public function show_fields( $hook ) {
        global $product;
        $fields = $this->create_fields_for_product( $product, $hook );
        if ( count( $fields['display_fields'] ) ) {
            echo $this->_plugin->load_template( $hook, 'hooks', array( 'fields' => $fields['display_fields'] ) );
        }
    }

	/**
     * Translate fields titles and labels.
     *
	 * @param array $fields
	 *
	 * @return array
	 */
    private function translate_fields_titles_and_labels( array $fields ) {
	    foreach ( $fields['fields'] as $key => $field ) {
	        $field['title'] = wpdesk__( $field['title'], 'flexible-product-fields' );
	        if ( isset( $field['placeholder'] ) ) {
		        $field['placeholder'] = wpdesk__( $field['placeholder'], 'flexible-product-fields' );
	        }
		    if ( $field['has_options'] ) {
			    foreach ( $field['options'] as $option_key => $option ) {
				    $field['options'][ $option_key ]['label'] = wpdesk__( $option['label'], 'flexible-product-fields' );
			    }
		    }
		    $fields['fields'][ $key ] = $field;
        }
        return $fields;
    }

	/**
	 * Fired by woocommerce_before_add_to_cart_button hook.
	 */
    public function woocommerce_before_add_to_cart_button() {
        /** Prevent display fields more than once. Action may be fired by other third party plugins, ie. Woocommerce Subscriptions */
        if ( $this->is_woocommerce_before_add_to_cart_button_fired ) {
            return;
        }
	    $this->is_woocommerce_before_add_to_cart_button_fired = true;
        global $product;
	    $product_extended_info = new FPF_Product_Extendend_Info( $product );
        if ( $product_extended_info->is_type_grouped() ) {
            return;
        }
        $this->show_fields( 'woocommerce_before_add_to_cart_button' );
        echo $this->_plugin->load_template( 'display', 'totals', array() );
        $fields = $this->translate_fields_titles_and_labels( $this->get_translated_fields_for_product( $product ) );
        foreach ( $fields['fields'] as $key => $field ) {
            $fields['fields'][$key]['price_value'] = 0;
            if ( !isset( $field['price_type'] ) ) {
	            $field['price_type'] = 'fixed';
	            $fields['fields'][$key]['price_type'] = 'fixed';
            }
            if ( $field['has_price'] && isset($field['price_type']) && $field['price_type'] != '' && isset($field['price']) && $field['price'] != '' ) {
            	$price_value = $this->product_price->calculate_price( floatval($field['price']), $field['price_type'], $product );
                $fields['fields'][$key]['price_value'] = $price_value;
	            $fields['fields'][$key]['price_display'] = $this->product_price->prepare_price_to_display( $product, $price_value );
            }
            if ( $field['has_options'] ) {
                foreach ( $fields['fields'][$key]['options'] as $option_key => $option ) {
                    $fields['fields'][$key]['options'][$option_key]['price_value'] = 0;
	                if ( $field['has_price_in_options'] ) {
                        if ( isset($option['price_type']) && $option['price_type'] != '' && isset($option['price']) && $option['price'] != '' ) {
                        	$price_value = $this->product_price->calculate_price( floatval( $option['price'] ), $option['price_type'], $product );
	                        $fields['fields'][ $key ]['options'][ $option_key ]['price_value']   = $price_value;
	                        $fields['fields'][ $key ]['options'][ $option_key ]['price_display'] = $this->product_price->prepare_price_to_display( $product, $price_value );
                        }
                    }
                }
            }
        }
        $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
        if ( $tax_display_mode == 'excl' ) {
	        $product_price = wpdesk_get_price_excluding_tax( $product );
        }
        else {
	        $product_price = wpdesk_get_price_including_tax( $product );
        }
        ?>
        <script type="text/javascript">
            var fpf_fields = <?php echo json_encode( $fields['fields'] ); ?>;
            var fpf_product_price = <?php echo json_encode( $product_price ); ?>;
        </script>
        <?php
    }

	/**
	 *
	 */
    public function woocommerce_after_add_to_cart_button() {
	    global $product;
	    $product_extended_info = new FPF_Product_Extendend_Info( $product );
	    if ( $product_extended_info->is_type_grouped() ) {
		    return;
	    }
        $this->show_fields( 'woocommerce_after_add_to_cart_button' );
    }


}
