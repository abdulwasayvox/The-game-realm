<?php

namespace Blocksy\Extensions\WoocommerceExtra;

class DisableCartCoupons {
	public function __construct() {
		add_filter('woocommerce_coupons_enabled', function ($enabled) {
			if (! function_exists('blocksy_get_theme_mod')) {
				return $enabled;
			}

			$has_cart_coupons = blocksy_get_theme_mod(
				'has_cart_coupons',
				'yes'
			);

			if ($has_cart_coupons !== 'yes') {
				return false;
			}

			return $enabled;
		});

		add_filter(
			'blocsky:pro:woocommerce-extra:cart-options:general',
			function ($general_options) {
				$general_options['has_cart_coupons'] = [
					'label' => __('Coupon Form', 'blocksy-companion' ),
					'type' => 'ct-switch',
					'value' => 'yes',
					'sync' => blocksy_sync_whole_page([
						'loader_selector' => '.ct-cart-form .woocommerce-cart-form'
					]),
				];

				return $general_options;
			}
		);
	}
}

