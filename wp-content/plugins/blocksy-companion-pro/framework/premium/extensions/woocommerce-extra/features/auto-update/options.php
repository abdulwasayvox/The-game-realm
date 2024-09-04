<?php

$options = [
	blocksy_rand_md5() => [
		'label' => __('Cart', 'blocksy-companion'),
		'type' => 'ct-panel',
		'setting' => ['transport' => 'postMessage'],
		'inner-options' => [
			blocksy_rand_md5() => [
				'title' => __( 'General', 'blocksy-companion' ),
				'type' => 'tab',
				'options' => [

					'has_cart_auto_update' => [
						'label' => __( 'Cart Auto Update', 'blocksy-companion' ),
						'type' => 'ct-switch',
						'value' => 'no',
					],

				],
			],

			blocksy_rand_md5() => [
				'title' => __( 'Design', 'blocksy-companion' ),
				'type' => 'tab',
				'options' => [
				],
			],
		],
	],
];


