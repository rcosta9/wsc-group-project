<?php
/**
 * REST API for fields.
 *
 * @package Flexible Product Fields
 */

use VendorFPF\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can handle REST API methods for fields.
 */
class FPF_REST_Api_Fields implements Hookable {

	const NONCE_NAME = 'fpf-fields';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register_fpf_fields_route' ) );
	}

	/**
	 * @param WP_REST_Server $wp_rest_server .
	 */
	public function register_fpf_fields_route( WP_REST_Server $wp_rest_server ) {
		register_rest_route(
			'flexible_product_fields/v1',
			'/fields/(?P<id>\d+)',
			array(
				'methods'  => WP_REST_Server::ALLMETHODS,
				'callback' => array( $this, 'handle_rest_api_fields' ),
			)
		);
	}

	/**
	 * @param array $fields .
	 *
	 * @return array
	 */
	private function trim_options_values_on_fields( array $fields ) {
		foreach ( $fields as $field_id => $field ) {
			if ( isset( $field['options'] ) ) {
				foreach ( $field['options'] as $option_id => $option ) {
					$field['options'][ $option_id ]['value'] = trim( $option['value'] );
				}
				$fields[ $field_id ] = $field;
			}
		}
		return $fields;
	}

	/**
	 * @param array    $json .
	 * @param stdClass $ret .
	 *
	 * @return stdClass
	 */
	private function process_post_data( array $json, $ret ) {
		$post_id = $json['post_id']['value'];
		$post    = get_post( $post_id );
		if ( $post ) {
			$assign_to = $json['assign_to']['value'];
			update_post_meta( $post_id, '_assign_to', $assign_to );
			update_post_meta( $post_id, '_section', $json['section']['value'] );
			update_post_meta( $post_id, '_fields', $this->trim_options_values_on_fields( $json['fields'] ) );
			if ( 'product' === $assign_to ) {
				$products = isset( $json['products'], $json['products']['value'] ) && is_array( $json['products']['value'] ) ? $json['products']['value'] : array();
				update_post_meta( $post_id, '_products', $products );
				delete_post_meta( $post_id, '_product_id' );
				foreach ( $products as $product ) {
					add_post_meta( $post_id, '_product_id', $product['value'] );
				}
			} else {
				delete_post_meta( $post_id, '_product_id' );
			}
			if ( 'category' === $assign_to ) {
				$categories = isset( $json['categories'], $json['categories']['value'] ) && is_array( $json['categories']['value'] ) ? $json['categories']['value'] : array();
				update_post_meta( $post_id, '_categories', $categories );
				delete_post_meta( $post_id, '_category_id' );
				foreach ( $categories as $category ) {
					add_post_meta( $post_id, '_category_id', $category['value'] );
				}
			} else {
				delete_post_meta( $post_id, '_category_id' );
			}
			$ret->code    = 'ok';
			$ret->message = 'updated';
		} else {
			$ret->code    = 'error';
			$ret->message = 'Fields not found.';
		}

		return $ret;
	}

	/**
	 * @param WP_REST_Request $request .
	 *
	 * @return stdClass
	 */
	public function handle_rest_api_fields( WP_REST_Request $request ) {
		$response          = new stdClass();
		$response->code    = 'error';
		$response->message = 'Invalid method.';
		if ( 'POST' === $request->get_method() ) {
			if ( current_user_can( 'manage_options' ) ) {
				$json = $request->get_json_params();
				if ( wp_verify_nonce( $json['nonce'], self::NONCE_NAME ) ) {
					wp_cache_flush();
					$response = $this->process_post_data( $json, $response );
				} else {
					$response->code    = 'error';
					$response->message = 'Invalid security nonce value.';
				}
			} else {
				$response->code    = 'error';
				$response->message = 'Insufficient privileges.';
			}
		}

		return $response;
	}

}
