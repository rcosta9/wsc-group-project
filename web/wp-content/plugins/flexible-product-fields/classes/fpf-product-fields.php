<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FPF_Product_Fields {

	const TYPE_TEXTAREA = 'textarea';

	private $plugin = null;

    private $local_cache = array();

    public function __construct( Flexible_Product_Fields_Plugin $plugin ) {
        $this->plugin = $plugin;
        $this->hooks();
    }

    public function hooks() {
        add_action( 'rest_api_init', array( $this, 'register_rest_api_routes' ) );
    }

    public function cache_get( $key ) {
        if ( isset( $this->local_cache[$key] ) ) {
            return $this->local_cache[$key];
        }
        return false;
    }

    public function cache_set( $key, $val ) {
        $this->local_cache[$key] = $val;
    }

    public function get_sections() {
        $ret = array(
            'woocommerce_before_add_to_cart_form'   => array(
                'hook'  => 'woocommerce_before_add_to_cart_button',
                'label' => __( 'Before add to cart button', 'flexible-product-fields' )
            ),
            'woocommerce_after_add_to_cart_form'    => array(
                'hook'  => 'woocommerce_after_add_to_cart_button',
                'label' => __( 'After add to cart button', 'flexible-product-fields' )
            )
        );
        return $ret;
    }

    public function get_field_types_by_type() {
        $ret = array();
        $field_types = $this->get_field_types();
        foreach ( $field_types as $field_type ) {
            $ret[$field_type['value']] = $field_type;
        }
        return $ret;
    }

    public function get_field_types() {
        $ret = array(
	        'text'         => array(
                'value'                 => 'text',
                'label'                 => __( 'Text', 'flexible-product-fields' ),
                'has_max_length'        => true,
                'has_required'          => true,
                'has_options'           => false,
                'has_price'             => false,
                'price_not_available'   => true,
                'has_placeholder'       => true,
                'has_value'             => false,
	            'is_available'          => true,
	            'has_logic'             => false,
	            'logic_not_available'   => true,
            ),
	        self::TYPE_TEXTAREA => array(
                'value'                 => self::TYPE_TEXTAREA,
                'label'                 => __( 'Textarea', 'flexible-product-fields' ),
                'has_max_length'        => true,
                'has_required'          => true,
                'has_options'           => false,
                'has_price'             => false,
                'price_not_available'   => true,
                'has_placeholder'       => true,
                'has_value'             => false,
                'is_available'          => true,
                'has_logic'             => false,
                'logic_not_available'   => true,
            ),
	        'select' => array(
	            'value'                 => 'select',
	            'label'                 => __( 'Select', 'flexible-product-fields' ),
	            'has_max_length'        => false,
	            'has_required'          => true,
	            'has_options'           => true,
	            'has_price_in_options'  => false,
	            'price_not_available_in_options'    => true,
	            'has_price'             => false,
	            'price_not_available'   => false,
	            'has_placeholder'       => false,
	            'has_value'             => false,
	            'is_available'          => true,
	            'has_logic'             => false,
	            'logic_not_available'   => true,
            ),
	        'radio' => array(
	            'value'                 => 'radio',
	            'label'                 => __( 'Radio', 'flexible-product-fields' ),
	            'has_max_length'        => false,
	            'has_required'          => true,
	            'has_options'           => true,
	            'has_price_in_options'  => false,
	            'price_not_available_in_options'    => true,
	            'has_price'             => false,
	            'price_not_available'   => false,
	            'has_placeholder'       => false,
	            'has_value'             => false,
	            'is_available'          => true,
	            'has_logic'             => false,
	            'logic_not_available'   => true,
            ),
	        'checkbox' => array(
	            'value'                 => 'checkbox',
	            'label'                 => __( 'Checkbox', 'flexible-product-fields' ),
	            'has_max_length'        => false,
	            'has_required'          => true,
	            'has_options'           => false,
	            'has_price'             => false,
	            'price_not_available'   => true,
	            'has_placeholder'       => false,
	            'has_value'             => true,
	            'is_available'          => true,
	            'has_logic'             => false,
	            'logic_not_available'   => true,
            ),
	        'heading' => array(
	            'value'                 => 'heading',
	            'label'                 => __( 'Heading', 'flexible-product-fields' ),
	            'has_max_length'        => false,
	            'has_required'          => false,
	            'has_options'           => false,
	            'has_price'             => false,
	            'price_not_available'   => false,
	            'has_placeholder'       => false,
	            'has_value'             => false,
	            'is_available'          => false,
	            'has_logic'             => false,
	            'logic_not_available'   => true,
            ),
	        'fpfdate' => array(
		        'value'                 => 'fpfdate',
		        'label'                 => __( 'Date', 'flexible-product-fields' ),
		        'has_max_length'        => false,
		        'has_required'          => true,
		        'has_options'           => false,
		        'has_price'             => false,
		        'price_not_available'   => true,
		        'has_placeholder'       => true,
		        'has_value'             => false,
		        'is_available'          => false,
		        'has_logic'             => false,
		        'logic_not_available'   => true,
	        ),
        );
        $ret = apply_filters( 'flexible_product_fields_field_types', $ret );
        $field_types = array();
        foreach ( $ret as $field_type ) {
        	$field_types[] = $field_type;
        }
        return $field_types;
    }

    public function handle_rest_api_products(  WP_REST_Request $request ) {
        $ret = new stdClass();
        $ret->code = 'error';
        $ret->message = 'Invalid method.';
        $options = array();
        if ( $request->get_method() == 'GET' ) {
            $search = $request->get_param('search');
            $posts = get_posts( array(
                    'post_type'         => array( 'product' ),
                    'posts_per_page'    =>  -1,
                    's'                 => $search
                )
            );
            foreach ( $posts as $post ) {
                $options[] = array( 'value' => $post->ID, 'label' => $post->post_title );
            }
            return $options;
        }
        return $ret;
    }

    public function handle_rest_api_categories(  WP_REST_Request $request ) {
        $ret = new stdClass();
        $ret->code = 'error';
        $ret->message = 'Invalid method.';
        $options = array();
        if ( $request->get_method() == 'GET' ) {
            $search = $request->get_param('search');
            $categories = get_terms( 'product_cat', array(
                    'hide_empty'        => false,
                    'search'            => $search
                )
            );
            foreach ( $categories as $category ) {
                $options[] = array( 'value' => $category->term_id, 'label' => $category->name );
            }
            return $options;
        }
        return $ret;
    }

	public function get_all_fields() {
		global $wpdb;
		$ret = array();
    	$sql = "select meta_value from {$wpdb->postmeta} m, {$wpdb->posts} p where p.post_type = 'fpf_fields' and p.ID = m.post_id and  m.meta_key = '_fields'";
    	$rows = $wpdb->get_results( $sql );
    	foreach ( $rows as $row ) {
    		$fields = unserialize( $row->meta_value );
    		if ( is_array( $fields ) ) {
    			foreach ( $fields as $field ) {
    				$ret[] = $field;
			    }
		    }
	    }
    	return $ret;
    }


	function init_wpml() {
		$icl_language_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : get_bloginfo('language');
		$fields = $this->get_all_fields();
		foreach ( $fields as $field ) {
			if ( !empty( $field['title'] ) ) {
				icl_register_string( 'flexible-product-fields', $field['title'], $field['title'], false, $icl_language_code );
			}
			if ( !empty( $field['placeholder'] ) ) {
				icl_register_string( 'flexible-product-fields', $field['placeholder'], $field['placeholder'], false, $icl_language_code );
			}
			if ( !empty( $field['options'] ) && is_array( $field['options'] ) ) {
				foreach ( $field['options'] as $option ) {
					if ( !empty( $option['label'] ) ) {
						icl_register_string( 'flexible-product-fields', $option['label'], $option['label'], false, $icl_language_code );
					}
					if ( !empty( $option['value'] ) ) {
						icl_register_string( 'flexible-product-fields', $option['value'], $option['value'], false, $icl_language_code );
					}
				}
			}
		}
	}

	function init_polylang() {
		$fields = $this->get_all_fields();
		foreach ( $fields as $field ) {
			if ( !empty( $field['title'] ) ) {
				pll_register_string( $field['title'], $field['title'], __( 'Flexible Product Fields', 'flexible-product-fields' ) );
			}
			if ( !empty( $field['placeholder'] ) ) {
				pll_register_string( $field['placeholder'], $field['placeholder'], __( 'Flexible Product Fields', 'flexible-product-fields' ) );
			}
			if ( !empty( $field['options'] ) && is_array( $field['options'] ) ) {
				foreach ( $field['options'] as $option ) {
					if ( !empty( $option['label'] ) ) {
						pll_register_string( $option['label'], $option['label'], __( 'Flexible Product Fields', 'flexible-product-fields' ) );
					}
					if ( !empty( $option['value'] ) ) {
						pll_register_string( $option['value'], $option['value'], __( 'Flexible Product Fields', 'flexible-product-fields' ) );
					}
				}
			}
		}
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_api_routes() {
		register_rest_route(
			'flexible_product_fields/v1',
			'/products/',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'handle_rest_api_products' ),
			)
		);
		register_rest_route(
			'flexible_product_fields/v1',
			'/categories/',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'handle_rest_api_categories' ),
			)
		);
	}

}
