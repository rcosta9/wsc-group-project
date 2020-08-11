<?php
/**
 * After Add To Cart Button
 *
 * This template can be overridden by copying it to yourtheme/flexible-product-fields/fields/select.php
 *
 * @author 		WP Desk
 * @package 	Flexible Product Fields/Templates
 * @version     1.0.0
 */
?>
<div class="fpf-field fpf-<?php echo $type; ?>">
	<?php woocommerce_form_field( $key, $args, $value ); ?>
</div>
