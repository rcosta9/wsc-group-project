	fpf_message_box2 += '<ul>';
		<?php if ( $pl ) : ?>
			<?php $link = 'https://www.wpdesk.pl/docs/flexible-product-fields-woocommerce/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Pierwsze_kroki'; ?>
		<?php else : ?>
			<?php $link = 'https://www.wpdesk.net/docs/flexible-product-fields-woocommerce-docs/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Getting_Started'; ?>
		<?php endif; ?>
	fpf_message_box2 += '<li><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'Getting Started', 'flexible-product-fields' ); ?></a></li>';
		<?php if ( $pl ) : ?>
			<?php $link = 'https://www.wpdesk.pl/docs/flexible-product-fields-woocommerce/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Zestawienie_grup_pol'; ?>
		<?php else : ?>
			<?php $link = 'https://www.wpdesk.net/docs/flexible-product-fields-woocommerce-docs/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Fields_Groups_List'; ?>
		<?php endif; ?>
	fpf_message_box2 += '<li><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'Fields Groups List', 'flexible-product-fields' ); ?></a></li>';
		<?php if ( $pl ) : ?>
			<?php $link = 'https://www.wpdesk.pl/docs/flexible-product-fields-woocommerce/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Konfiguracja_grupy_pol'; ?>
		<?php else : ?>
			<?php $link = 'https://www.wpdesk.net/docs/flexible-product-fields-woocommerce-docs/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Fields_Group_Configuration'; ?>
		<?php endif; ?>
	fpf_message_box2 += '<li><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'Fields Group Configuration', 'flexible-product-fields' ); ?></a></li>';
<?php if ( $pl ) : ?>
			<?php $link = 'https://www.wpdesk.pl/docs/flexible-product-fields-woocommerce/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Opcje_podstawowe'; ?>
		<?php else : ?>
			<?php $link = 'https://www.wpdesk.net/docs/flexible-product-fields-woocommerce-docs/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Basic_options'; ?>
		<?php endif; ?>
	fpf_message_box2 += '<li><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'Fields Basic Options', 'flexible-product-fields' ); ?></a></li>';	
<?php if ( $pl ) : ?>
			<?php $link = 'https://www.wpdesk.pl/docs/flexible-product-fields-woocommerce/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Ceny'; ?>
		<?php else : ?>
			<?php $link = 'https://www.wpdesk.net/docs/flexible-product-fields-woocommerce-docs/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Price'; ?>
		<?php endif; ?>
	fpf_message_box2 += '<li><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'Fields Price', 'flexible-product-fields' ); ?></a></li>';
<?php if ( $pl ) : ?>
			<?php $link = 'https://www.wpdesk.pl/docs/flexible-product-fields-woocommerce/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Logika_warunkowa_dla_pol'; ?>
		<?php else : ?>
			<?php $link = 'https://www.wpdesk.net/docs/flexible-product-fields-woocommerce-docs/?utm_source=flexible-product-fields-settings&utm_medium=link&utm_campaign=settings-docs-link#Conditional_logic_for_fields'; ?>
		<?php endif; ?>
	fpf_message_box2 += '<li><a href="<?php echo $link; ?>" target="_blank"><?php _e( 'Conditional logic for fields', 'flexible-product-fields' ); ?></a></li>';	
	fpf_message_box2 += '</ul>';

