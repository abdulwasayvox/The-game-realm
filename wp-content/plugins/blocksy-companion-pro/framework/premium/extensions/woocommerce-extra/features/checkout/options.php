<?php

$options = [
	'blocksy_has_image_toggle' => [
		'label' => __( 'Product Image', 'blocksy-companion' ),
		'type' => 'ct-switch',
		'value' => 'no',
		'sync' => blocksy_sync_whole_page([
			'prefix' => 'single_page',
			'loader_selector' => '.ct-order-review'
		]),
	],

	'blocksy_has_quantity_toggle' => [
		'label' => __( 'Quantity Input', 'blocksy-companion' ),
		'type' => 'ct-switch',
		'value' => 'no',
		'divider' => 'bottom:full',
		'sync' => blocksy_sync_whole_page([
			'prefix' => 'single_page',
			'loader_selector' => '.ct-order-review'
		]),
	],
];
