<?php
/**
	Plugin Name: Flexible Product Fields
	Plugin URI: https://wordpress.org/plugins/flexible-product-fields/
	Description: Allow customers to customize WooCommerce products before adding them to cart. Add fields text or textarea.
	Version: 1.3.0
	Author: WP Desk
	Author URI: https://www.wpdesk.net/
	Text Domain: flexible-product-fields
	Domain Path: /lang/
	Requires at least: 4.5
	Tested up to: 5.4.2
	WC requires at least: 3.8
	WC tested up to: 4.3
	Requires PHP: 5.6

	Copyright 2018 WP Desk Ltd.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 * @package Flexible Product Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '1.3.0';

$plugin_name        = 'Flexible Product Fields';
$product_id         = 'Flexible Product Fields';
$plugin_class_name  = 'Flexible_Product_Fields_Plugin';
$plugin_text_domain = 'flexible-product-fields';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

define( 'FLEXIBLE_PRODUCT_FIELDS_VERSION', $plugin_version );
define( $plugin_class_name, $plugin_version );

$requirements = array(
	'php'     => '5.6',
	'wp'      => '4.5',
	'plugins' => array(
		array(
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
		),
	),
);

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow/src/plugin-init-php52-free.php';


