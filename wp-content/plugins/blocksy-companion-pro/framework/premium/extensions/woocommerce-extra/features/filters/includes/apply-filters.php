<?php

namespace Blocksy\Extensions\WoocommerceExtra;

class ApplyFilters {
	use QueryManager;

	private $transient_key = 'blocksy:woocommerce-extra:filters:counts:cache';

	public $custom_query_string = [];

	public function mount_entry_point() {
		$this->apply_on_main_query();
		$this->wipe_transient_on_save();
	}

	public function apply_on_main_query() {
		add_action('pre_get_posts', [$this, 'filter_products']);
	}

	public function wipe_transient_on_save() {
		add_action('woocommerce_update_product', [$this, 'wipe_transient']);
		add_action('woocommerce_new_product', [$this, 'wipe_transient']);

		add_action(
			'updated_option',
			function ($option) {
				if ($option !== 'woocommerce_attribute_lookup_enabled') {
					return;
				}

				$this->wipe_transient();
			}
		);
	}

	public function filter_products($query) {
		if (! $this->is_main_query($query)) {
			return;
		}

		$params = FiltersUtils::get_query_params();

		$query_args = [
			'tax_query' => []
		];

		if (is_array($query->get('tax_query'))) {
			$query_args['tax_query'] = $query->get('tax_query');
		}

		$filters = [
			new CategoriesFilter([]),
			new BrandsFilter([])
		];

		foreach ($filters as $filter) {
			if (method_exists($filter, 'wp_query_arg')) {
				$query_args = $filter->wp_query_arg(
					$params['params'],
					$query_args
				);
			}
		}

		foreach ($query_args as $key => $value) {
			if (empty($value)) {
				continue;
			}

			$query->set($key, $value);
		}
	}

	public function get_products_count_for($query_string) {
		$cache_key = $this->get_stable_cache_key_for($query_string);

		$maybe_cache = $this->get_cache_for($cache_key);

		if ($maybe_cache !== null) {
			return $maybe_cache;
		}

		$filters = [
			new CategoriesFilter([]),
			new BrandsFilter([]),
			new AttributesFilter([]),
			new CommonWCFilter([])
		];

		$tax_query = [];

		$is_taxonomy_page = $this->is_taxonomy_page();

		if ($is_taxonomy_page) {
			$queried_object = get_queried_object();

			$tax_query = [
				[
					'taxonomy' => $queried_object->taxonomy,
					'field' => 'id',
					'terms' => $queried_object->term_id,
					'operator' => 'IN'
				]
			];
		}

		$query_args = [
			'paged' => 1,
			'posts_per_page' => -1,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'cache_results' => false,
			'no_found_rows' => true,
			'nopaging' => true, // prevent "offset" issues
			'blocksy-woocommerce-extra-filters' => false,
			'fields' => 'ids',
			'post_type' => 'product',

			'tax_query' => $tax_query
		];

		foreach ($filters as $filter) {
			if (method_exists($filter, 'wp_query_arg')) {
				$query_args = $filter->wp_query_arg(
					$query_string,
					$query_args
				);
			}
		}

		$this->custom_query_string = $query_string;

		add_filter(
			'posts_clauses',
			[$this, 'posts_clauses_for_fresh_query'],
			10, 2
		);

		$query = new \WP_Query($query_args);

		remove_filter(
			'posts_clauses',
			[$this, 'posts_clauses_for_fresh_query'],
			10, 2
		);

		$result = count($query->posts);

		$this->set_cache_for($cache_key, $result);

		return $result;
	}

	public function posts_clauses_for_fresh_query($clauses, $query) {
		// Only these filters have posts_clauses, not worth trying other ones.
		$filters = [
			new AttributesFilter([]),
			new CommonWCFilter([])
		];

		foreach ($filters as $filter) {
			if (method_exists($filter, 'posts_clauses')) {
				$clauses = $filter->posts_clauses(
					$clauses,
					$query,
					$this->custom_query_string
				);
			}
		}

		return $clauses;
	}

	public function wipe_transient() {
		delete_transient($this->transient_key);
	}

	private function get_cache_for($key) {
		$counts_cache = get_transient($this->transient_key);

		if (
			! is_array($counts_cache)
			||
			! isset($counts_cache[$key])
		) {
			return null;
		}

		return $counts_cache[$key];
	}

	private function set_cache_for($key, $value) {
		$counts_cache = get_transient($this->transient_key);

		if (! is_array($counts_cache)) {
			$counts_cache = [];
		}

		$counts_cache[$key] = $value;

		set_transient(
			$this->transient_key,
			$counts_cache,
			12 * MONTH_IN_SECONDS
		);
	}

	private function get_stable_cache_key_for($query_string) {
		$copy = $query_string;

		$copy['blc_current_url'] = blocksy_current_url();

		ksort($copy);

		return md5(http_build_query($copy));
	}
}
