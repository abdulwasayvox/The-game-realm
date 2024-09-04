<?php

namespace Blocksy\Extensions\WoocommerceExtra;

use \Automattic\WooCommerce\Internal\ProductAttributesLookup\Filterer;

class AttributesFilter extends BaseFilter {
	public function get_filter_name() {
		return 'filter_' . $this->attributes['attribute'];
	}

	public function wp_query_arg($query_string, $query_args) {
		$filterer = wc_get_container()->get(Filterer::class);

		if ($filterer->filtering_via_lookup_table_is_active()) {
			return $query_args;
		}

		// TODO: implement attributes lookup for case when lookup tables
		// aren't active.

		/*
		if (! isset($query_string['filter_product_category'])) {
			return $query_args;
		}

		$terms = explode(',', $query_string['filter_product_category']);

		if (! isset($query_args['tax_query'])) {
			$query_args['tax_query'] = [];
		}

		$query_args['tax_query'][] = [
			'taxonomy' => 'product_cat',
			'field' => 'id',
			'terms' => $terms,
			'operator' => 'IN'
		];
		 */

		return $query_args;
	}

	public function posts_clauses($clauses, $query, $query_string) {
		$layered_nav_chosen = [];


		$filterer = wc_get_container()->get(Filterer::class);

		if (! $filterer->filtering_via_lookup_table_is_active()) {
			return $clauses;
		}

		$layered_nav_chosen = [];

		foreach ($query_string as $key => $value) {
			if (0 !== strpos($key, 'filter_')) {
				continue;
			}

			$attribute = wc_sanitize_taxonomy_name(
				str_replace('filter_', '', $key)
			);

			$taxonomy = wc_attribute_taxonomy_name($attribute);

			$filter_terms = ! empty($value)
				? explode(',', wc_clean(wp_unslash($value)))
				: array();

			if (
				empty($filter_terms)
				||
				! taxonomy_exists($taxonomy)
				||
				! wc_attribute_taxonomy_id_by_name($attribute)
			) {
				continue;
			}

			$all_terms = [];

			foreach ($filter_terms as $term) {
				$term_obj = get_term_by('id', $term, $taxonomy);

				if (! $term_obj) {
					$term_obj = get_term_by('slug', $term, $taxonomy);
				}

				$all_terms[] = $term_obj->slug;
			}

			if (! isset($layered_nav_chosen[$taxonomy])) {
				$layered_nav_chosen[$taxonomy] = [
					'terms' => [],
					'query_type' => 'or',
				];
			}

			$layered_nav_chosen[$taxonomy]['terms'] = $all_terms;
		}

		global $wp_the_query;
		$prev_wp_query = $wp_the_query;
		$GLOBALS['wp_the_query'] = $query;

		$clauses = $filterer->filter_by_attribute_post_clauses(
			$clauses,
			$query,
			$layered_nav_chosen
		);

		$GLOBALS['wp_the_query'] = $prev_wp_query;

		return $clauses;
	}

	public function render() {
		$items = $this->filter_get_items();

		$params = [
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => true,
			'exclude' => $this->attributes['taxonomy_not_in']
		];

		if (! $this->attributes['excludeTaxonomy']) {
			unset($params['exclude']);
		}

		$taxonomy_terms = get_terms(
			wc_attribute_taxonomy_name($this->attributes['attribute']),
			$params
		);

		if (
			! $taxonomy_terms
			||
			is_wp_error($taxonomy_terms)
		) {
			return [
				'items' => []
			];
		}

		$additional_attrs = [];

		$storage = new Storage();
		$settings = $storage->get_settings();

		if ($settings['features']['variation-swatches']) {
			$swatch_type = 'select';

			if (sizeof($taxonomy_terms)) {
				$first_swatch_id = $taxonomy_terms[0]->term_id;
				$first_swatch = new SwatchesRender($first_swatch_id);

				$swatch_type = $first_swatch->type;
			}

			$swatch_shape = 'round';

			if ($swatch_type === 'color') {
				$swatch_shape = blocksy_get_theme_mod('color_swatch_shape', 'round');
			}

			if ($swatch_type === 'image') {
				$swatch_shape = blocksy_get_theme_mod('image_swatch_shape', 'round');
			}

			if ($swatch_type === 'button') {
				$swatch_shape = blocksy_get_theme_mod('button_swatch_shape', 'round');
			}

			$additional_attrs = [
				'data-swatches-type' => $swatch_type,
				'data-swatches-shape' => $swatch_shape,
			];
		}

		return [
			'items' => $items,
			'list_attr' => $additional_attrs
		];
	}

	private function filter_get_items() {
		$attribute_slug = $this->attributes['attribute'];
		$show_label = $this->attributes['showLabel'];

		if (! taxonomy_exists(wc_attribute_taxonomy_name($attribute_slug))) {
			return [];
		}

		$taxonomy_terms = [];
		$list_items_html = [];

		$params = [
			'hide_empty' => true,
			'exclude' => $this->attributes['taxonomy_not_in']
		];

		if (! $this->attributes['excludeTaxonomy']) {
			unset($params['exclude']);
		}

		$taxonomy_terms = get_terms(
			wc_attribute_taxonomy_name($attribute_slug),
			$params
		);

		foreach ($taxonomy_terms as $key => $value) {
			$api_url = $this->get_link_url(
				$value->slug,
				[
					'is_multiple' => $this->attributes['multipleFilters'],
					'to_add' => [
						'query_type_' . $this->attributes['attribute'] => 'or'
					]
				]
			);

			$products_count = $this->count_products([
				'api_url' => $api_url,
				'term_id' => $value->term_id,
			]);

			if (! $products_count) {
				continue;
			}

			if (! $this->attributes['showCounters']) {
				$products_count = '';
			}

			$swatch_term_html = '';

			$swatch_term = new SwatchesRender($value->term_id);

			if ($this->attributes['showItemsRendered']) {
				$swatch_term_html = $swatch_term->get_output(true);
			}

			$label_html = $show_label
				? blocksy_html_tag(
					'span',
					['class' => 'ct-filter-label'],
					$value->name
				)
				: '';

			$item_classes = ['ct-filter-item'];

			if ($this->is_filter_active($value->slug)) {
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
							'name' => 'product_attribute_' . $value->term_id,
							'aria-label' => $value->name,
						],
						$this->is_filter_active($value->slug)
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
							'data-key' => $attribute_slug,
							'data-value' => $value->term_id,
							'href' => esc_url($api_url),
							'aria-label' => $value->name,
						],
						$checbox_html .
						$swatch_term_html .
							$label_html .
							$products_count
					)
				)
			);
		}

		return $list_items_html;
	}

	public function get_applied_filter_descriptor($key, $value) {
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$maybe_attribute = null;

		foreach ($attribute_taxonomies as $attribute) {
			if ('filter_' . $attribute->attribute_name === $key) {
				$maybe_attribute = $attribute;
				break;
			}
		}

		if (! $maybe_attribute) {
			return null;
		}

		$taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
		$term = get_term_by('slug', $value, $taxonomy);

		if ($term) {
			return [
				'name' => $term->name,
				'href' => $this->get_link_url($value, [
					'to_add' => [
						'query_type_' . $attribute->attribute_name => 'or'
					]
				])
			];
		}

		return null;
	}

	public function additional_query_string_params() {
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$maybe_attribute = null;

		$result = [];
		$params = FiltersUtils::get_query_params();

		foreach ($params['params'] as $key => $value) {
			foreach ($attribute_taxonomies as $attribute) {
				if ('query_type_' . $attribute->attribute_name === $key) {
					$result[] = $key;
				}
			}
		}

		return $result;
	}

	public function remove_my_filters_from_url($url, $key) {
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$maybe_attribute = null;

		foreach ($attribute_taxonomies as $attribute) {
			if (
				'filter_' . $attribute->attribute_name === $key
				||
				'query_type_' . $attribute->attribute_name === $key
			) {
				$maybe_attribute = $attribute;
				break;
			}
		}

		if (! $maybe_attribute) {
			return $url;
		}

		return remove_query_arg($key, $url);
	}
}

