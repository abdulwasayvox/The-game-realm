<?php

function blc_get_woo_offcanvas_trigger($has_filter_ajax_reveal) {
	$icons = [
		'type-1' => '<svg width="12" height="12" viewBox="0 0 10 10"><path d="M0 1.8c0-.4.3-.7.7-.7h8.6c.4 0 .7.3.7.7 0 .4-.3.7-.7.7H.7c-.4 0-.7-.3-.7-.7zm9.3 2.5H.7c-.4 0-.7.3-.7.7 0 .4.3.7.7.7h8.6c.4 0 .7-.3.7-.7 0-.4-.3-.7-.7-.7zm0 3.2H.7c-.4 0-.7.3-.7.7 0 .4.3.7.7.7h8.6c.4 0 .7-.3.7-.7 0-.4-.3-.7-.7-.7z"/></svg>',

		'type-2' => '<svg width="12" height="12" viewBox="0 0 10 10"><path d="M.7 1.1c-.4 0-.7.3-.7.7 0 .4.3.7.7.7h8.6c.4 0 .7-.3.7-.7 0-.4-.3-.7-.7-.7H.7zm.9 3.2c-.4 0-.7.3-.7.7 0 .4.3.7.7.7h6.8c.4 0 .7-.3.7-.7 0-.4-.3-.7-.7-.7H1.6zm.9 3.2c-.4 0-.7.3-.7.7 0 .4.3.7.7.7h5c.4 0 .7-.3.7-.7 0-.4-.3-.7-.7-.7h-5z"/></svg>',

		'type-3' => '<svg width="12" height="12" viewBox="0 0 10 10"><path d="M10 1v1H5.4c-.2.6-.7 1-1.4 1s-1.2-.4-1.4-1H0V1h2.6c.2-.6.7-1 1.4-1s1.2.4 1.4 1H10zM6.5 3.5c-.7 0-1.2.4-1.4 1H0v1h5.1c.2.6.8 1 1.4 1 .7 0 1.2-.4 1.4-1H10v-1H7.9c-.2-.6-.7-1-1.4-1zM2.5 7c-.7 0-1.2.4-1.4 1H0v1h1.1c.2.6.8 1 1.4 1 .7 0 1.2-.4 1.4-1H10V8H3.9c-.2-.6-.7-1-1.4-1z"/></svg>',

		'type-4' => '<svg width="12" height="12" viewBox="0 0 10 10"><path d="M5.9 9.5h-.2l-1.8-.9c-.2-.1-.3-.2-.3-.4V5.4L.1 1.2C0 1.1 0 .9 0 .7.1.5.2.4.4.4h9.1c.2 0 .3.1.4.3s0 .3-.1.5L6.4 5.4v3.7c0 .2-.1.3-.2.4h-.3z"/></svg>'
	];

	$type = blocksy_get_theme_mod('woocommerce_filter_icon_type', 'type-1');

	$type_prop = '';

	if (empty($type)) {
		$type = 'type-1';
	}

	$class = '';

	if (blocksy_get_theme_mod('woocommerce_filter_type', 'type-1') === 'type-2') {
		$type_prop = 'data-behaviour="drop-down"';
		$class = 'ct-toggle-filter-panel';
	} else {
		$class = 'ct-toggle-filter-panel ct-offcanvas-trigger';
	}

	$class .= ' ' . blocksy_visibility_classes(blocksy_get_theme_mod(
		'woocommerce_filter_visibility',
		[
			'desktop' => true,
			'tablet' => true,
			'mobile' => true,
		]
	));

	$woocommerce_filter_label = blocksy_get_theme_mod('woocommerce_filter_label', __('Filter', 'blocksy-companion'));

	if (blocksy_get_theme_mod('woocommerce_filter_type', 'type-1') === 'type-2') {
		$ariaExpanded = blocksy_get_theme_mod(
			'filter_panel_behaviour',
			'no'
		) === 'no' ? 'false' : 'true';

		return blocksy_action_button(
			[
				'button_html_attributes' => array_merge(
					[
						'class' => $class . (! $has_filter_ajax_reveal ? ' ct-expandable-trigger' : ''),
						'data-target' => '#woo-filters-panel',
						'aria-expanded' => $ariaExpanded
					],

					$has_filter_ajax_reveal === 'yes' ? [
						'aria-expanded' => 'false',
					] : []
				),
				'icon' => $icons[$type],
				'content' => $woocommerce_filter_label
			]
		);
	}

	return blocksy_action_button(
		[
			'button_html_attributes' => array_merge(
				[
					'class' => $class,
					'data-toggle-panel' => '#woo-filters-panel',
				]
			),
			'icon' => $icons[$type],
			'content' => $woocommerce_filter_label
		]
	);
}
