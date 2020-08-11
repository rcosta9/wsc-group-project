<?php
/**
 * After Add To Cart Button
 *
 * This template can be overridden by copying it to yourtheme/flexible-product-fields/hooks/woocommerce_after_add_to_cart_button.php
 *
 * @author 		WP Desk
 * @package 	Flexible Product Fields/Templates
 * @version     1.0.0
 */
?>
<div class="fpf-clear"></div>

<div class="fpf-fields">
    <?php foreach ( $fields as $field ) : ?>
        <?php echo $field; ?>
    <?php endforeach; ?>
</div>
