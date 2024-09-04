<?php

foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
	$_product = apply_filters(
		'woocommerce_cart_item_product',
		$cart_item['data'],
		$cart_item,
		$cart_item_key
	);

	if (
		! $_product
		||
		! $_product->exists()
		||
		$cart_item['quantity'] === 0
		||
		! apply_filters(
			'woocommerce_checkout_cart_item_visible',
			true,
			$cart_item,
			$cart_item_key
		)
	) {
		continue;
	}

    $thumbnail = '';

	if (blocksy_get_theme_mod('blocksy_has_image_toggle', 'no') === 'yes') {
		$thumbnail = $cart_item['data']->get_image(array( 70, 70));

		if (function_exists('blocksy_media')) {
			$thumbnail = blocksy_media([
				'attachment_id' => $cart_item['data']->get_image_id(),
				'size' => [70, 70],
				'ratio' => '1/1',
				'tag_name' => 'figure',
			]);
		}
	}

	$cart_item_name = wp_kses_post(
		apply_filters(
			'woocommerce_cart_item_name',
			$_product->get_name(),
			$cart_item,
			$cart_item_key
		)
	);

	$quantity = apply_filters(
		'woocommerce_checkout_cart_item_quantity',
		' <strong class="product-quantity">' . sprintf(
			'&times;&nbsp;%s',
			$cart_item['quantity']
		) . '</strong>',
		$cart_item,
		$cart_item_key
	);

	$cart_item_data = wc_get_formatted_cart_item_data($cart_item);

	$content = blocksy_html_tag(
		'span',
		[],
		$cart_item_name . $quantity
	) . $cart_item_data;

	if (blocksy_get_theme_mod('blocksy_has_quantity_toggle', 'no') === 'yes') {
		$content = $cart_item_name . $cart_item_data . $quantity;
	}

?>
	<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
		<td class="product-name">
			<?php
				echo blocksy_html_tag(
					'div',
					[
						'class' => 'ct-checkout-cart-item'
					],
					$thumbnail .
					blocksy_html_tag(
						'div',
						[
							'class' => 'ct-checkout-cart-item-content'
						],
						$content
					)
				)
			?>
		</td>

		<td class="product-total">
			<?php
				echo apply_filters(
					'woocommerce_cart_item_subtotal',
					WC()->cart->get_product_subtotal($_product, $cart_item['quantity']),
					$cart_item,
					$cart_item_key
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</td>
	</tr>
<?php

}
