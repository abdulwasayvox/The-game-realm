<?php

namespace Blocksy\Extensions\WoocommerceExtra;

abstract class BaseFilter {
	public $attributes = [];

	// ['items' => [], 'list_attr' => []]
	abstract public function render();
	abstract public function get_filter_name();

	public function __construct($attributes) {
		$this->attributes = $attributes;
	}

	public function additional_query_string_params() {
		return [];
	}

	static public function get_filter_for($attributes) {
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		$initial_attribute = null;

		if (sizeof($attribute_taxonomies)) {
			$initial_attribute = reset($attribute_taxonomies)->attribute_name;
		}

		if (
			isset($attributes['taxonomy'])
			&&
			$attributes['taxonomy'] !== null
		) {
			$maybe_taxonomy_name = str_replace('filter_', '', $attributes['taxonomy']);

			if (taxonomy_exists(wc_attribute_taxonomy_name($maybe_taxonomy_name))) {
				$initial_attribute = $maybe_taxonomy_name;
			}
		}

		$attributes = wp_parse_args($attributes, [
			'type' => 'categories',
			'viewType' => 'list',
			'attribute' => -1,
			'showCounters' => false,
			'attribute' => $initial_attribute,
			'showLabel' => true,
			'multipleFilters' => true,
			'hierarchical' => false,
			'showResetButton' => false,
			'showCheckbox' => true,
			'showSearch' => false,
			'showAttributesCheckbox' => false,
			'showItemsRendered' => true,
			'expandable' => false,
			'defaultExpanded' => true,
			'logoMaxW' => 40,
			'useFrame' => false,
			'aspectRatio' => '16/9',
			'excludeTaxonomy' => false,
			'taxonomy_not_in' => [],
			'limitHeight' => false,
			'limitHeightValue' => 400,
		]);

		if ($attributes['type'] === 'categories') {
			return new CategoriesFilter($attributes);
		}

		$storage = new Storage();
		$settings = $storage->get_settings();

		if (
			$attributes['type'] === 'brands'
			&&
			$settings['features']['product-brands']
		) {
			return new BrandsFilter($attributes);
		}

		if ($attributes['type'] === 'attributes') {
			return new AttributesFilter($attributes);
		}

		return null;
	}

	static public function get_url_without_filters() {
		$cleaned_url = add_query_arg([]);

		$to_try = ['categories', 'brands', 'attributes'];
		$params = FiltersUtils::get_query_params();

		foreach ($params['params'] as $key => $value) {
			foreach ($to_try as $filter_type) {
				$filter = self::get_filter_for([
					'type' => $filter_type
				]);

				if (! $filter) {
					continue;
				}

				$cleaned_url = $filter->remove_my_filters_from_url(
					$cleaned_url,
					$key
				);
			}
		}

		return $cleaned_url;
	}

	public function remove_my_filters_from_url($url, $key) {
		$filter_name = $this->get_filter_name();

		if ($filter_name !== $key) {
			return $url;
		}

		return remove_query_arg($key, $url);
	}

	public function get_applied_filter_descriptor($key, $value) {
		$filter_name = $this->get_filter_name();

		if ($filter_name !== $key) {
			return null;
		}

		$term = get_term($value);

		if ($term) {
			return [
				'name' => $term->name,
				'href' => $this->get_link_url($value)
			];
		}

		return null;
	}

	public function get_link_url($value, $args = []) {
		$args = wp_parse_args($args, [
			'is_multiple' => true,
			'to_add' => []
		]);

		$value = urldecode($value);

		$query_string = array_merge([
			$this->get_filter_name() => $value,
		], $args['to_add']);

		$params = FiltersUtils::get_query_params();

		$url = $params['url'];
		$params = $params['params'];

		if (isset($params[$this->get_filter_name()])) {
			$url = remove_query_arg(
				array_merge([
					$this->get_filter_name()
				], array_keys($args['to_add'])),
				$url
			);

			$all_attrs = explode(',', $params[$this->get_filter_name()]);

			if ($args['is_multiple']) {
				if (in_array($value, $all_attrs)) {
					$all_attrs = array_diff($all_attrs, [$value]);
				} else {
					array_push($all_attrs, $value);
				}
			} else {
				$all_attrs = array_diff([$value], $all_attrs);
			}

			if (! empty($all_attrs)) {
				$query_string = array_merge([
					$this->get_filter_name() => implode(',', $all_attrs)
				], $args['to_add']);
			} else {
				$query_string = [];
			}
		}

		$url = add_query_arg($query_string, $url);

		// if url contains page in url, remove it
		//
		// Need to understand why is that.
		$url = preg_replace('/\/page\/[0-9]+/', '', $url);

		return $url;
	}

	public function is_filter_active($term) {
		$params = FiltersUtils::get_query_params();

		return (
			isset($params['params'][$this->get_filter_name()])
			&&
			in_array(
				urldecode($term),
				explode(',', $params['params'][$this->get_filter_name()])
			)
		);
	}

	public function count_products($args = []) {
		$args = wp_parse_args($args, [
			'term_id' => 0,
			'api_url' => '',

			'with_wrap' => false
		]);

		$count = 0;

		parse_str(
			parse_url($args['api_url'], PHP_URL_QUERY) ?? '',
			$query_string
		);

		$query_string[$this->get_filter_name()] = $args['term_id'];

		$apply_filters = new ApplyFilters();

		$count = $apply_filters->get_products_count_for($query_string);

		if ($count === 0) {
			return '';
		}

		if ($args['with_wrap']) {
			$count = '(' . $count . ')';
		}

		return blocksy_html_tag('span', ['class' => 'ct-filter-count'], $count);
	}
}
