<?php

namespace Blocksy\Extensions\WoocommerceExtra;

class AutoUpdate {
	public function __construct() {
		add_filter(
			'blocsky:pro:woocommerce-extra:cart-options:general',
			function ($general_options) {
				$general_options['has_cart_auto_update'] = [
					'label' => __( 'Quantity Auto Update', 'blocksy-companion' ),
					'type' => 'ct-switch',
					'value' => 'no',
					'sync' => blocksy_sync_whole_page([
						'loader_selector' => '.ct-cart-form .woocommerce-cart-form'
					]),
				];

				return $general_options;
			}
		);

		add_filter('blocksy:woocommerce:cart:wrapper-class', function ($class) {
			if (blocksy_get_theme_mod('has_cart_auto_update', 'no') === 'yes') {
				$class .= ' ct-cart-auto-update';
			}

			return trim($class);
		});
	}
}
