<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FPF_Post_Type {

    const POST_TYPE = 'fpf_fields';

	private $plugin = null;
	private $product_fields = null;

    public function __construct( Flexible_Product_Fields_Plugin $plugin, FPF_Product_Fields $product_fields ) {
    	$this->plugin = $plugin;
    	$this->product_fields = $product_fields;
    	$this->hooks();
    }

    public function hooks() {
	    add_action( 'init', array( $this, 'register_post_types' ), 20);
	    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

	    add_filter( 'manage_edit-fpf_fields_columns', array( $this, 'manage_edit_fpf_fields_columns' ), 11 );
        add_action( 'manage_fpf_fields_posts_custom_column' , array( $this, 'manage_fpf_fields_posts_custom_column' ), 11 );

	    add_filter( 'flexible_product_fields_assign_to_options', array( $this, 'flexible_product_fields_assign_to_options' ), 10 );

	    add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

	    add_filter( 'bulk_actions-edit-fpf_fields', array( $this, 'bulk_actions' ) );

	    add_action( 'admin_menu', array( $this, 'admin_menu' ), 9999 );

	    add_action( 'admin_head-edit.php', array( $this, 'admin_head_edit' ) );

	}

    /**
     * Register post types.
     */
    public function register_post_types() {

        if ( post_type_exists('fpf_fields') ) {
            return;
        }

        register_post_type( 'fpf_fields',
            array(
                'labels'              => array(
                    'name'                => __('Fields Groups', 'flexible-product-fields'),
                    'singular_name'       => __('Product Fields', 'flexible-product-fields'),
                    'menu_name'           => __('Product Fields', 'flexible-product-fields'),
                    'parent_item_colon'   => '',
                    'all_items'           => __('Product Fields', 'flexible-product-fields'),
                    'view_item'           => __('View Product Fields', 'flexible-product-fields'),
                    'add_new_item'        => __('Add new Fields Group', 'flexible-product-fields'),
                    'add_new'             => __('Add New', 'flexible-product-fields'),
                    'edit_item'           => __('Edit Fields Group', 'flexible-product-fields'),
                    'update_item'         => __('Save Fields Group', 'flexible-product-fields'),
                    'search_items'        => __('Search Fields Group', 'flexible-product-fields'),
                    'not_found'           => __('Fields Group not found', 'flexible-product-fields'),
                    'not_found_in_trash'  => __('Fields Group not found in trash', 'flexible-product-fields')
                ),
                'description'         => __( 'Product Fields.', 'flexible-product-fields' ),
                'public'              => false,
                'show_ui'             => true,
                'capability_type'     => 'post',
                'capabilities'        => array(),
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'hierarchical'        => false,
                'query_var'           => true,
                'supports'            => array( 'title' ),
                'has_archive'         => false,
                'show_in_nav_menus'   => false,
//                'show_in_menu'		  => 'product',
            )
        );

    }

    public function manage_edit_fpf_fields_columns( $columns ) {
        $ret = array();
        foreach ( $columns as $key => $column ) {
            if ( $key == 'date' ) {
	            $ret['fpf_assign_to'] = __( 'Assign to', 'flexible-product-fields' );
                $ret['fpf_fields'] = __( 'Fields', 'flexible-product-fields' );
            }
            $ret[$key] = $column;
        }
        unset( $ret['date'] );
        return $ret;
    }

    public function manage_fpf_fields_posts_custom_column( $column ) {
        if ( $column == 'fpf_fields' ) {
	        global $post;
	        $fields = get_post_meta( $post->ID, '_fields', true );
	        if ( $fields == '' ) {
		        $fields = array();
	        }
	        $first = true;
	        foreach ( $fields as $field ) {
	            if ( !$first ) {
	                echo ', ';
                }
	            echo $field['title'];
	            $first = false;
            }
        }
	    if ( $column == 'fpf_assign_to' ) {
		    global $post;
		    $assign_to = get_post_meta( $post->ID, '_assign_to', true );
		    $assign_to_options = array(
			    'product'    => __( 'Product', 'flexible-product-fields' ),
			    'category'   => __( 'Category', 'flexible-product-fields' ),
			    'all'        => __( 'All products', 'flexible-product-fields' ),
		    );
		    if ( isset( $assign_to_options[$assign_to] ) ) {
			    echo '<b>' . $assign_to_options[ $assign_to ] . '</b>';
		    }
		    if ( $assign_to == 'product' ) {
			    $products = get_post_meta( $post->ID, '_products', true );
			    if ( !is_array( $products ) ) {
				    $products = array();
			    }
			    echo ':<br/>';
			    $first = true;
			    foreach ( $products as $product ) {
				    if ( !$first ) {
					    echo ', ';
				    }
			        echo $product['label'];
				    $first = false;
                }
            }
		    if ( $assign_to == 'category' ) {
			    $categories = get_post_meta( $post->ID, '_categories', true );
			    if ( !is_array( $categories ) ) {
				    $categories = array();
			    }
			    echo ':<br/>';
			    $first = true;
			    foreach ( $categories as $category ) {
				    if ( !$first ) {
					    echo ', ';
				    }
				    echo $category['label'];
				    $first = false;
			    }
		    }
	    }
    }

    public function add_meta_boxes() {
	    add_meta_box(
		    'fpf_settings', __( 'Settings', 'flexible-product-fields' ),
		    array( $this, 'settings_meta_box_output' ),
		    'fpf_fields',
		    'normal',
		    'high'
	    );
	    add_meta_box(
		    'fpf_fields', __( 'Fields', 'flexible-product-fields' ),
		    array( $this, 'fields_meta_box_output' ),
		    'fpf_fields',
		    'advanced',
		    'default'
	    );
	    $activation_tracker = new VendorFPF\WPDesk\PluginBuilder\Plugin\ActivationTracker( $this->plugin->get_namespace() );
	    if ( $activation_tracker->is_activated_more_than_two_weeks() ) {
		    add_meta_box(
			    'fpf_rate_it', __( 'Enjoying the free version? Rate it!', 'flexible-product-fields' ),
			    array( $this, 'rate_it_meta_box_output' ),
			    'fpf_fields',
			    'side',
			    'default'
		    );
	    }
	    add_meta_box(
		    'fpf_docs', __( 'Documentation', 'flexible-product-fields' ),
		    array( $this, 'documentation_meta_box_output' ),
		    'fpf_fields',
		    'side',
		    'default'
	    );
    }

    public function rate_it_meta_box_output() {
	    include( 'views/metabox-rate-it.php' );
    }

    public function documentation_meta_box_output() {
        include( 'views/metabox-documentation.php' );
    }

    public function flexible_product_fields_assign_to_options( $fpf_assign_to_options = array() ) {
	    $fpf_assign_to_options = array(
		    array( 'value' => 'product', 'label' => __( 'Product', 'flexible-product-fields' ), 'is_available' => true ),
		    array( 'value' => 'category', 'label' => __( 'Category', 'flexible-product-fields' ), 'is_available' => false ),
		    array( 'value' => 'all', 'label' => __( 'All products', 'flexible-product-fields' ), 'is_available' => false ),
	    );
	    remove_filter( 'flexible_product_fields_assign_to_options', array( $this, 'flexible_product_fields_assign_to_options' ), 10 );
	    $fpf_assign_to_options = apply_filters( 'flexible_product_fields_assign_to_options', $fpf_assign_to_options );
	    add_filter( 'flexible_product_fields_assign_to_options', array( $this, 'flexible_product_fields_assign_to_options' ), 10 );
	    return $fpf_assign_to_options;
    }

    public function settings_meta_box_output( $post ) {
    	$fpf_settings = array(
            'nonce' => wp_create_nonce( FPF_REST_Api_Fields::NONCE_NAME ),
        );
		$fpf_settings['post_title'] = array( 'value' => $post->post_title );
		$fpf_settings['post_id'] = array( 'value' => $post->ID );
		$fpf_settings['assign_to'] = array( 'value' => get_post_meta( $post->ID, '_assign_to', true ) );
	    if ( $fpf_settings['assign_to']['value'] == '' ) {
		    $assign_to_options = apply_filters( 'flexible_product_fields_assign_to_options', array() );
		    foreach ( $assign_to_options as $assign_to_option ) {
			    if ( $assign_to_option['is_available'] ) {
				    $fpf_settings['assign_to']['value'] = $assign_to_option['value'];
			    }
		    }
	    }
        $fpf_settings['section'] = array( 'value' => get_post_meta( $post->ID, '_section', true ) );
	    if ( $fpf_settings['section']['value'] == '' ) {
		    $fpf_settings['section']['value'] = 'woocommerce_before_add_to_cart_button';
        }
        $fpf_settings['products'] = array( 'value' => get_post_meta( $post->ID, '_products', true ) );
        if ( !is_array( $fpf_settings['products'] ) ) {
            $fpf_settings['products'] = array( 'value' => array() );
        }
        $fpf_settings['categories'] = array( 'value' => get_post_meta( $post->ID, '_categories', true ) );
        if ( !is_array( $fpf_settings['categories'] ) ) {
		    $fpf_settings['categories'] = array( 'value' => array() );
	    }
	    $fpf_settings['menu_order'] = array( 'value' => $post->menu_order );
	    $fpf_settings['fields'] = get_post_meta( $post->ID, '_fields', true );
    	if ( $fpf_settings['fields'] == '' ) {
		    $fpf_settings['fields'] = array();
	    }
	    foreach ( $fpf_settings['fields'] as $key => $field ) {
            $fpf_settings['fields'][$key]['display'] = false;
        }
        $sections = $this->product_fields->get_sections();
    	$fpf_sections_options = array();
    	foreach ( $sections as $section ) {
    	    $fpf_sections_options[] = array( 'value' => $section['hook'], 'label' => $section['label'] );
        }
        $fpf_assign_to_options = apply_filters( 'flexible_product_fields_assign_to_options', array() );
	    $fpf_assign_to_values = array();
	    foreach ( $fpf_assign_to_options as $fpf_assign_to_option ) {
		    $fpf_assign_to_values[$fpf_assign_to_option['value']] = $fpf_assign_to_option;
	    }
    	$fpf_field_type_options = $this->product_fields->get_field_types();
    	$fpf_field_types = array();
        foreach ( $fpf_field_type_options as $field_type ) {
            $fpf_field_types[$field_type['value']] = $field_type;
        }
        $fpf_price_type_options = array(
            array( 'value' => 'fixed', 'label' => __( 'Fixed', 'flexible-product-fields' ) ),
            array( 'value' => 'percent', 'label' => __( 'Percent', 'flexible-product-fields' ) ),
        );
		?>
		<div id="fpf_settings_container"></div>
		<script type="text/javascript">
			var fpf_settings = <?php echo json_encode( $fpf_settings ); ?>;
            var fpf_sections_options = <?php echo json_encode( $fpf_sections_options ); ?>;
            var fpf_assign_to_options = <?php echo json_encode( $fpf_assign_to_options ); ?>;
            var fpf_assign_to_values = <?php echo json_encode( $fpf_assign_to_values ); ?>;
            var fpf_field_type_options = <?php echo json_encode( $fpf_field_type_options ); ?>;
            var fpf_field_types = <?php echo json_encode( $fpf_field_types ); ?>;
            var fpf_price_type_options = <?php echo json_encode( $fpf_price_type_options ); ?>;
            var fpf_field_group_menu_order_is_available = <?php echo json_encode( is_flexible_products_fields_pro_active() ); ?>;
			var Fields = null;
        </script>
		<?php
	}

    public function fields_meta_box_output( $post ) {
    	?>
		<div id="fpf_fields_container"></div>
		<?php
    }

    public function admin_menu() {
	    remove_menu_page( 'edit.php?post_type=fpf_fields' );
	    add_submenu_page(
	        'edit.php?post_type=product',
            __('Product Fields', 'flexible-product-fields'),
            __('Product Fields', 'flexible-product-fields'),
            'manage_options',
            'edit.php?post_type=fpf_fields'
        );
    }

	public function post_row_actions( $actions, $post ) {
		global $current_screen;
		if ( !empty( $current_screen ) && $current_screen->post_type == 'fpf_fields' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
    }

	function bulk_actions( $actions ){
		unset( $actions[ 'edit' ] );
		return $actions;
	}

	/**
     * Hook action: admin_head-edit.php.
     *
	 * @param $hook_suffix Hook suffix.
	 */
	function admin_head_edit( $hook_suffix ) {
		global $post_type;
		if ( 'fpf_fields' === $post_type ) {
			$activation_tracker = new VendorFPF\WPDesk\PluginBuilder\Plugin\ActivationTracker( $this->plugin->get_namespace() );
			$show_rate_it_metabox = false;
			/* Show Rate It metabox 2 weeks after plugin activation and when there are any fields groups. */
			if ( $activation_tracker->is_activated_more_than_two_weeks() ) {
			    $posts = get_posts( array( 'post_type' => self::POST_TYPE, 'post_status' => 'any' ) );
			    if ( count( $posts ) ) {
				    $show_rate_it_metabox = true;
			    }
			}
		   include( 'views/admin-head-edit.php' );
		}
	}

}

