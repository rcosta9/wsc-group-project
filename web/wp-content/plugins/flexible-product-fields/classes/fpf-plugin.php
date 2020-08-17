<?php
/**
 * Plugin.
 *
 * @package Flexible Product Fields
 */

/**
 * Plugin.
 */
class Flexible_Product_Fields_Plugin extends VendorFPF\WPDesk\PluginBuilder\Plugin\AbstractPlugin implements VendorFPF\WPDesk\PluginBuilder\Plugin\HookableCollection {

	use VendorFPF\WPDesk\PluginBuilder\Plugin\HookableParent;
	use VendorFPF\WPDesk\PluginBuilder\Plugin\TemplateLoad;

	/**
	 * Scripts version string.
	 *
	 * @var string
	 */
	private $scripts_version = FLEXIBLE_PRODUCT_FIELDS_VERSION . '.69';

	/**
	 * FPF product fields.
	 *
	 * @var FPF_Product_Fields
	 */
	private $fpf_product_fields;

	/**
	 * FPF Product.
	 *
	 * @var FPF_Product
	 */
	private $fpf_product;

	/**
	 * FPF Product Price.
	 *
	 * @var FPF_Product_Price
	 */
	private $fpf_product_price;

	/**
	 * FPF Cart.
	 *
	 * @var FPF_Cart
	 */
	private $fpf_cart;

	/**
	 * FPF post type.
	 *
	 * @var FPF_Post_Type
	 */
	private $fpf_post_type;


	/**
	 * Flexible_Invoices_Reports_Plugin constructor.
	 *
	 * @param VendorFPF\WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( VendorFPF\WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
		parent::__construct( $this->plugin_info );

	}

	/**
	 * Init base variables for plugin
	 */
	public function init_base_variables() {
		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_path      = $this->plugin_info->get_plugin_dir();
		$this->template_path    = $this->plugin_info->get_text_domain();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();
		$this->template_path    = $this->plugin_info->get_text_domain();
	}

	/**
	 * Enqueue front scripts.
	 */
	public function wp_enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_style( 'fpf_front', trailingslashit( $this->get_plugin_assets_url() ) . 'css/front' . $suffix . '.css', array(), $this->scripts_version );
		wp_enqueue_style( 'fpf_front' );
		if ( is_singular( 'product' ) ) {
			wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), $this->scripts_version );
			wp_enqueue_script( 'fpf_product', trailingslashit( $this->get_plugin_assets_url() ) . 'js/fpf_product' . $suffix . '.js', array(
				'jquery',
				'accounting',
			), $this->scripts_version );
			if ( ! function_exists( 'get_woocommerce_price_format' ) ) {
				$currency_pos = get_option( 'woocommerce_currency_pos' );
				switch ( $currency_pos ) {
					case 'left':
						$format = '%1$s%2$s';
						break;
					case 'right':
						$format = '%2$s%1$s';
						break;
					case 'left_space':
						$format = '%1$s&nbsp;%2$s';
						break;
					case 'right_space':
						$format = '%2$s&nbsp;%1$s';
						break;
				}

				$currency_format = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), $format ) );
			} else {
				$currency_format = esc_attr( str_replace( array( '%1$s', '%2$s' ), array(
					'%s',
					'%v',
				), get_woocommerce_price_format() ) );
			}
			$product = wc_get_product( get_the_ID() );
			wp_localize_script( 'fpf_product', 'fpf_product',
				array(
					'total'                        => __( 'Total', 'flexible-product-fields' ),
					'currency_format_num_decimals' => absint( get_option( 'woocommerce_price_num_decimals' ) ),
					'currency_format_symbol'       => get_woocommerce_currency_symbol(),
					'currency_format_decimal_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
					'currency_format_thousand_sep' => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
					'currency_format'              => $currency_format,
					'fields_rules'                 => $this->fpf_product->get_logic_rules_for_product( $product ),
				)
			);
		}
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_enqueue_scripts() {
		$pl       = get_locale() === 'pl_PL';
		$pro_link = 'https://www.wpdesk.net/products/flexible-product-fields-pro-woocommerce/?utm_source=Flexible%20Product%20Fields&utm_medium=Settings';
		if ( $pl ) {
			$pro_link = 'https://www.wpdesk.pl/sklep/flexible-product-fields-pro-woocommerce/?utm_source=Flexible%20Product%20Fields&utm_medium=Settings';
		}
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( isset( $screen ) && ( 'edit-fpf_fields' === $screen->id || 'fpf_fields' === $screen->id ) ) {

			wp_register_script( 'fpf_admin', trailingslashit( $this->get_plugin_assets_url() ) . 'js/fpf_admin' . $suffix . '.js', array(), $this->scripts_version, false );

			$number_step = '1';

			$price_decimals = wc_get_price_decimals();
			if ( 1 === $price_decimals ) {
				$number_step = '0.1';
			} else {
				$number = '0.';
				for ( $i = 1; $i < $price_decimals; $i ++ ) {
					$number_step .= '0';
				}
				$number_step .= '1';
			}

			$rest_url   = esc_url_raw( rest_url() );
			$rest_param = '&';
			if ( is_multisite() && get_blog_option( null, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
				$rest_param = '?';
			}

			wp_localize_script( 'fpf_admin', 'fpf_admin',
				array(
					'rest_url'                       => $rest_url,
					'rest_param'                     => $rest_param,
					'rest_nonce'                     => wp_create_nonce( 'wp_rest' ),
					'add_field_label'                => __( 'Add Field', 'flexible-product-fields' ),
					'section_label'                  => __( 'Section', 'flexible-product-fields' ),
					'assign_to_label'                => __( 'Assign this group to', 'flexible-product-fields' ),
					'products_label'                 => __( 'Select products', 'flexible-product-fields' ),
					'categories_label'               => __( 'Select categories', 'flexible-product-fields' ),
					'menu_order_label'               => __( 'Order Group', 'flexible-product-fields' ),
					'select_placeholder'             => __( 'Select ...', 'flexible-product-fields' ),
					'field_title_label'              => __( 'Label', 'flexible-product-fields' ),
					'field_type_label'               => __( 'Field Type', 'flexible-product-fields' ),
					'field_required_label'           => __( 'Required', 'flexible-product-fields' ),
					'field_max_length_label'         => __( 'Character Limit', 'flexible-product-fields' ),
					'field_css_class_label'          => __( 'CSS Class', 'flexible-product-fields' ),
					'field_placeholder_label'        => __( 'Placeholder', 'flexible-product-fields' ),
					'field_value_label'              => __( 'Value', 'flexible-product-fields' ),
					'field_price_type_label'         => __( 'Price type', 'flexible-product-fields' ),
					'field_price_label'              => __( 'Price', 'flexible-product-fields' ),
					'field_date_format_label'        => __( 'Date format', 'flexible-product-fields' ),
					'field_days_before_label'        => __( 'Days before', 'flexible-product-fields' ),
					'field_days_after_label'         => __( 'Days after', 'flexible-product-fields' ),
					'field_options_label'            => __( 'Options', 'flexible-product-fields' ),
					'field_logic_label'              => __( 'Conditional logic', 'flexible-product-fields' ),
					'new_field_title'                => __( 'New field', 'flexible-product-fields' ),
					'edit_label'                     => __( 'Edit field', 'flexible-product-fields' ),
					'delete_label'                   => __( 'Delete field', 'flexible-product-fields' ),
					'add_option_label'               => __( 'Add Option', 'flexible-product-fields' ),
					'option_value_label'             => __( 'Value', 'flexible-product-fields' ),
					'option_label_label'             => __( 'Label', 'flexible-product-fields' ),
					'option_price_type_label'        => __( 'Price Type', 'flexible-product-fields' ),
					'option_price_label'             => __( 'Price', 'flexible-product-fields' ),
					'select_type_to_search'          => __( 'Type to search', 'flexible-product-fields' ),
					'save_error'                     => __( 'Sorry, there has been an error. Please try again later. Returned status code: ', 'flexible-product-fields' ),
					'fields_adv'                     => __( 'This field type is available in PRO version.', 'flexible-product-fields' ),
					'fields_adv_link'                => $pro_link,
					'fields_adv_link_text'           => __( 'Upgrade to PRO →', 'flexible-product-fields' ),
					'assign_to_adv'                  => __( 'This option is available in PRO version.', 'flexible-product-fields' ),
					'assign_to_adv_link'             => $pro_link,
					'assign_to_adv_link_text'        => __( 'Upgrade to PRO →', 'flexible-product-fields' ),
					'assign_to_fields_adv'           => __( 'Fields are available in PRO version.', 'flexible-product-fields' ),
					'assign_to_fields_adv_link'      => $pro_link,
					'assign_to_fields_adv_link_text' => __( 'Upgrade to PRO →', 'flexible-product-fields' ),
					'menu_order_adv'                 => __( 'This option is available in PRO version.', 'flexible-product-fields' ),
					'menu_order_adv_link_text'       => __( 'Upgrade to PRO →', 'flexible-product-fields' ),
					'menu_order_adv_link'            => $pro_link,
					'price_adv'                      => __( 'Price fields are available in PRO version.', 'flexible-product-fields' ),
					'price_adv_link'                 => $pro_link,
					'price_adv_link_text'            => __( 'Upgrade to PRO →', 'flexible-product-fields' ),
					'logic_label_operator'           => __( 'Show this field if', 'flexible-product-fields' ),
					'logic_label_rules'              => __( 'Rules', 'flexible-product-fields' ),
					'logic_label_operator_and'       => __( 'all rules match (and)', 'flexible-product-fields' ),
					'logic_label_operator_or'        => __( 'one or more rules match (or)', 'flexible-product-fields' ),
					'logic_label_field'              => __( 'Field', 'flexible-product-fields' ),
					'logic_label_compare'            => __( 'Condition', 'flexible-product-fields' ),
					'logic_label_value'              => __( 'Field value', 'flexible-product-fields' ),
					'logic_compare_is'               => __( 'is', 'flexible-product-fields' ),
					'logic_compare_is_not'           => __( 'is not', 'flexible-product-fields' ),
					'logic_select_field'             => __( 'Select field', 'flexible-product-fields' ),
					'logic_select_field_value'       => __( 'Select field value', 'flexible-product-fields' ),
					'add_rule_label'                 => __( 'Add rule', 'flexible-product-fields' ),
					'logic_adv'                      => __( 'Conditional logis is available in PRO version.', 'flexible-product-fields' ),
					'logic_adv_link'                 => $pro_link,
					'logic_adv_link_text'            => __( 'Upgrade to PRO →', 'flexible-product-fields' ),
					'number_step'                    => $number_step,
					'checked_label'                  => __( 'checked', 'flexible-product-fields' ),
					'unchecked_label'                => __( 'unchecked', 'flexible-product-fields' ),
				)
			);

			wp_enqueue_script( 'fpf_admin' );

			wp_register_style( 'fpf_react_select', trailingslashit( $this->get_plugin_assets_url() ) . 'css/react-select' . $suffix . '.css', array(), $this->scripts_version );
			wp_enqueue_style( 'fpf_react_select' );

			wp_register_style( 'fpf_admin', trailingslashit( $this->get_plugin_assets_url() ) . 'css/admin' . $suffix . '.css', array(), $this->scripts_version );
			wp_enqueue_style( 'fpf_admin' );
		}
	}

	/**
	 * Load dependencies.
	 */
	private function load_dependencies() {
		require_once $this->plugin_path . '/inc/wpdesk-woo27-functions.php';

		new WPDesk_Flexible_Product_Fields_Tracker();
		$this->fpf_product_price  = new FPF_Product_Price();
		$this->fpf_product_fields = new FPF_Product_Fields( $this );
		$this->fpf_product        = new FPF_Product( $this, $this->fpf_product_fields, $this->fpf_product_price );
		$this->fpf_cart           = new FPF_Cart( $this, $this->fpf_product_fields, $this->fpf_product, $this->fpf_product_price );
		$this->fpf_post_type      = new FPF_Post_Type( $this, $this->fpf_product_fields );
		$this->add_hookable( new FPF_Add_To_Cart_Filters( $this->fpf_product ) );

		$this->add_hookable( new FPF_REST_Api_Fields() );

		new FPF_Order( $this );
	}

	/**
	 * Init.
	 */
	public function init() {
		$this->init_base_variables();
		$this->load_dependencies();
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		parent::hooks();
		add_action( 'init', array( $this, 'init_polylang' ) );
		add_action( 'admin_init', array( $this, 'init_wpml' ) );
		$this->hooks_on_hookable_objects();
	}

	/**
	 * Init Polylang actions.
	 */
	public function init_polylang() {
		if ( function_exists( 'pll_register_string' ) ) {
			$this->fpf_product_fields->init_polylang();
		}
	}

	/**
	 * Init WPML actions.
	 */
	public function init_wpml() {
		if ( function_exists( 'icl_register_string' ) ) {
			$this->fpf_product_fields->init_wpml();
		}
	}

	/**
	 * Add links to plugin on plugins page.
	 *
	 * @param array $links Links array.
	 *
	 * @return array
	 */
	public function links_filter( $links ) {
		$pl         = get_locale() === 'pl_PL';
		$domain     = $pl ? 'pl' : 'net';
		$utm_source = $this->get_namespace();
		$utm_medium = 'quick-link';
		$pro_link   = $pl ? 'https://www.wpdesk.pl/sklep/flexible-product-fields-pro-woocommerce/' : 'https://www.wpdesk.net/products/flexible-product-fields-pro-woocommerce/';

		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=fpf_fields' ) . '">' . __( 'Settings', 'flexible-product-fields' ) . '</a>',
			'<a href="https://www.wpdesk.' . $domain . '/docs/flexible-product-fields-woocommerce/?utm_source=' . $utm_source . '&utm_medium=' . $utm_medium . '&utm_campaign=docs-quick-link" target="_blank">' . __( 'Documentation', 'flexible-product-fields' ) . '</a>',
			'<a href="https://wordpress.org/support/plugin/flexible-product-fields" target="_blank">' . __( 'Support', 'flexible-product-fields' ) . '</a>',
		);

		if ( ! wpdesk_is_plugin_active( 'flexible-product-fields-pro/flexible-product-fields-pro.php' ) ) {
			$plugin_links[] = '<a href="' . $pro_link . '?utm_source=' . $utm_source . '&utm_medium=' . $utm_medium . '&utm_campaign=upgrade-quick-link" target="_blank" style="color:#d64e07;font-weight:bold;">' . __( 'Upgrade', 'flexible-product-fields' ) . '</a>';
		}

		return array_merge( $plugin_links, $links );
	}


}
