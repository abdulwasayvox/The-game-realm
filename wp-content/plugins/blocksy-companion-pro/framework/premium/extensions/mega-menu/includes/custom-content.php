<?php

namespace Blocksy\Extensions\MegaMenu;

class CustomContent {
	private $current_topmost_el = null;

	public function __construct() {
		add_filter(
			'walker_nav_menu_start_el',
			function ($item_output, $item, $depth, $args) {
				if (
					! isset($args->blocksy_advanced_item)
					||
					! $args->blocksy_advanced_item
				) {
					return $item_output;
				}

				if ($depth === 0) {
					$this->current_topmost_el = $item;
				}

				$atts = blocksy_get_post_options($item->ID);

				$menu_custom_content_visibility = blocksy_akg(
					'menu_custom_content_visibility',
					$atts,
					[
						'desktop_visible' => true,
						'mobile_visible' => false
					]
				);

				$is_desktop = (
					isset($args->blocksy_mega_menu)
					&&
					$args->blocksy_mega_menu
				);

				// return $item_output;

				if ($is_desktop && $depth === 0) {
					$has_ajax_loading = blocksy_akg('has_ajax_loading', $atts, 'no');
					$has_mega_menu = blocksy_akg('has_mega_menu', $atts, 'no');

					if ($has_ajax_loading === 'yes' && $has_mega_menu === 'yes') {
						add_filter(
							'nav_menu_submenu_css_class',
							[$this, 'append_ajax_loading_class'],
							10, 3
						);
					}
				}

				if ($is_desktop && $depth > 0 && $this->current_topmost_el) {
					$parent_atts = blocksy_get_post_options($this->current_topmost_el->ID);

					$has_ajax_loading = blocksy_akg('has_ajax_loading', $parent_atts, 'no');
					$has_mega_menu = blocksy_akg('has_mega_menu', $parent_atts, 'no');

					if ($has_ajax_loading === 'yes' && $has_mega_menu === 'yes') {
						return $item_output;
					}
				}

				if (
					(
						! $is_desktop
						||
						! $menu_custom_content_visibility['desktop_visible']
					) && (
						$is_desktop
						||
						! $menu_custom_content_visibility['mobile_visible']
					)
				) {
					return $item_output;
				}

				$mega_menu_content_type = blocksy_akg(
					'mega_menu_content_type',
					$atts,
					'default'
				);

				if ($mega_menu_content_type === 'default') {
					return $item_output;
				}

				$text = '';

				if ($mega_menu_content_type === 'text') {
					$text = do_shortcode(
						blocksy_default_akg(
							'mega_menu_text',
							$atts,
							''
						)
					);

					$item_output .= '<div class="entry-content">';
				}

				if ($mega_menu_content_type === 'hook') {
					$hook_to_output = blocksy_default_akg(
						'mega_menu_hook',
						$atts,
						''
					);

					if (
						$hook_to_output
						&&
						\Blocksy\Plugin::instance()
							->premium
							->content_blocks
							->is_hook_eligible_for_display($hook_to_output, [
								'match_conditions' => false
							])
					) {
						$text = \Blocksy\Plugin::instance()
							->premium
							->content_blocks
							->output_hook($hook_to_output, [
								'layout' => false,
								'respect_visibility' => false
							]);
					}
				}

				$item_output .= $text;

				if ($mega_menu_content_type === 'text') {
					$item_output .= '</div>';
				}

				return $item_output;
			},
			60, 4
		);
	}

	public function append_ajax_loading_class($classes, $args, $depth) {
		$classes[] = 'ct-ajax-pending';
		remove_filter(
			'nav_menu_submenu_css_class',
			[$this, 'append_ajax_loading_class'],
			10, 3
		);

		return $classes;
	}
}

