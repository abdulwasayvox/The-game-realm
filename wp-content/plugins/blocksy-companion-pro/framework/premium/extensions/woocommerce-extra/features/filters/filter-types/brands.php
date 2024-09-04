<?php

namespace Blocksy\Extensions\WoocommerceExtra;

class BrandsFilter extends BaseFilter {
	public function get_filter_name() {
		return 'filter_product_brands';
	}

	public function wp_query_arg($query_string, $query_args) {
		if (! isset($query_string['filter_product_brands'])) {
			return $query_args;
		}

		$terms = explode(',', $query_string['filter_product_brands']);

		if (! isset($query_args['tax_query'])) {
			$query_args['tax_query'] = [];
		}

		$query_args['tax_query'][] = [
			'taxonomy' => 'product_brands',
			'field' => 'id',
			'terms' => $terms,
			'operator' => 'IN'
		];

		return $query_args;
	}

	public function render() {
		return [
			'items' => $this->get_filter_items_for_brands(),
			'list_attr' => [
				'style' => "--product-brand-logo-size: {$this->attributes['logoMaxW']}px;",
				'data-frame' => $this->attributes['useFrame'] ? 'yes' : 'no',
			]
		];
	}

	private function get_filter_items_for_brands() {
		$params = [
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => true,
			'exclude' => $this->attributes['taxonomy_not_in'],
		];

		if (! $this->attributes['excludeTaxonomy']) {
			unset($params['exclude']);
		}

		$terms = get_terms('product_brands', $params);

		$list_items_html = [];

		foreach ($terms as $key => $brand) {
			$api_url = $this->get_link_url($brand->term_id, [
				'is_multiple' => false
			]);

			$brand_content = '';

			$term_atts = get_term_meta(
				$brand->term_id,
				'blocksy_taxonomy_meta_options'
			);

			if (empty($term_atts)) {
				$term_atts = [[]];
			}

			$term_atts = $term_atts[0];

			$maybe_image = blocksy_akg('image', $term_atts, '');

			if (
				$maybe_image
				&&
				is_array($maybe_image)
				&&
				isset($maybe_image['attachment_id'])
				&&
				function_exists('blocksy_media')
				&&
				$this->attributes['showItemsRendered']
			) {
				$attachment_id = $maybe_image['attachment_id'];

				$brand_content .= blocksy_media([
					'attachment_id' => $maybe_image['attachment_id'],
					'size' => 'full',
					'ratio' => $this->attributes['aspectRatio'],
					'class' => 'ct-product-brand',
				]);
			}

			if ($this->attributes['showLabel']) {
				$brand_content .= blocksy_html_tag('span', [
					'class' => 'ct-filter-label'
				], $brand->name);
			}

			$products_count = $this->count_products([
				'term_id' => $brand->term_id,
				'api_url' => $api_url,
			]);

			if (! $products_count) {
				continue;
			}

			if ($this->attributes['showCounters']) {
				$brand_content .= $products_count;
			}

			$item_classes = ['ct-filter-item'];

			if ($this->is_filter_active($brand->term_id)) {
				$item_classes[] = 'active';
			}

			$checbox_html = $this->attributes['showAttributesCheckbox']
				? blocksy_html_tag(
					'input',
					array_merge(
						[
							'type' => 'checkbox',
							'class' => 'ct-checkbox',
							'tabindex' => '-1',
							'name' => 'product_brand_' . $brand->term_id,
							'aria-label' => $brand->name,
						],
						$this->is_filter_active($brand->term_id)
							? ['checked' => 'checked']
							: []
					)
				)
				: '';

			$list_items_html[] = blocksy_html_tag(
				'li',
				[
					'class' => implode(' ', $item_classes),
				],
				blocksy_html_tag(
					'div',
					[
						'class' => 'ct-filter-item-inner'
					],
					blocksy_html_tag(
						'a',
						[
							'data-key' => 'product_brands',
							'data-value' => $brand->term_id,
							'href' => esc_url($api_url),
							'aria-label' => $brand->name,
						],
						$checbox_html .
						$brand_content
					)
				)
			);
		}

		return $list_items_html;
	}
}

