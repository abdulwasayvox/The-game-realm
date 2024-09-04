<?php
/**
 * Get Post List
 * return array
 */

if(!function_exists('htslider_post_name')){
    function htslider_post_name( $post_type = 'post' ){
        $options = array();
        $options['0'] = __('Select','ht-slider');
        $all_post = array( 'posts_per_page' => -1, 'post_type'=> $post_type );
        $post_terms = get_posts( $all_post );
        if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ){
            foreach ( $post_terms as $term ) {
                $options[ $term->ID ] = $term->post_title;
            }
            return $options;
        }
    }
}


/*
 * Get Taxonomy
 * return array
 */
if(!function_exists('htslider_get_taxonomies')){
    function htslider_get_taxonomies( $texonomy = 'category' ){
        $options = array();
        $options['0'] = __('Select','ht-slider');
        $terms = get_terms( array(
            'taxonomy' => $texonomy,
            'hide_empty' => true,
        ));
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
            foreach ( $terms as $term ) {
                $options[ $term->slug ] = $term->name;
            }
            return $options;
        }
    }
}
/*
*add menu slider
*/
if(!function_exists('htslider_post_tabs')){
    function htslider_post_tabs($view) {
        if ( ! is_admin() ) {
            return;
        }
        $admin_tabs = apply_filters(
            'htslider_tabs_info',
            array(

                10 => array(
                    "link" => "edit.php?post_type=htslider_slider",
                    "name" => __( "HTSlider Slider", "ht-slider" ),
                    "id"   => "edit-htslider_slider",
                ),

                20 => array(
                    "link" => "edit-tags.php?taxonomy=htslider_category&post_type=htslider_slider",
                    "name" => __( "Categories", "ht-slider" ),
                    "id"   => "edit-htslider_category",
                ),

            )
        );

        ksort( $admin_tabs );
        $tabs = array();
        foreach ( $admin_tabs as $key => $value ) {
            array_push( $tabs, $key );
        }

        $pages = apply_filters(
            'htslier_admin_tabs_on_pages',
            array( 'edit-htslider_slider', 'edit-htslider_category', 'htslider_slider' )
        );
        $admin_tabs_on_page = array();

        foreach ( $pages as $page ) {
            $admin_tabs_on_page[ $page ] = $tabs;
        }

        $current_page_id = get_current_screen()->id;
        $current_user    = wp_get_current_user();
        if ( ! in_array( 'administrator', $current_user->roles ) ) {
            return;
        }
        if ( ! empty( $admin_tabs_on_page[ $current_page_id ] ) && count( $admin_tabs_on_page[ $current_page_id ] ) ) {
            echo '<h2 class="nav-tab-wrapper lp-nav-tab-wrapper">';
            foreach ( $admin_tabs_on_page[ $current_page_id ] as $admin_tab_id ) {

                $class = ( $admin_tabs[ $admin_tab_id ]["id"] == $current_page_id ) ? "nav-tab nav-tab-active" : "nav-tab";
                echo '<a href="' . esc_url(admin_url( $admin_tabs[ $admin_tab_id ]["link"] )) . '" class="' . esc_attr($class) . ' nav-tab-' . esc_attr($admin_tabs[ $admin_tab_id ]["id"]) . '">' . wp_kses_post($admin_tabs[ $admin_tab_id ]["name"]) . '</a>';
            }
            echo '</h2>';
        }
        return $view;
    }

}
add_filter( 'views_edit-htslider_slider', 'htslider_post_tabs' );
add_action('htslider_slider_cat_pre_add_form','htslider_post_tabs');


/**
* Elementor Version check
* Return boolean value
*/
function htslider_is_elementor_version( $operator = '<', $version = '2.6.0' ) {
    return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, $version, $operator );
}

// Compatibility with elementor version 3.6.1
function htslider_widget_register_manager($widget_class){
    $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
    $widgets_manager->register( $widget_class );

}


/**
 * @since 1.50
 * HTML Tag list
 * return array
 */
function htslider_html_tag_lists() {
    $html_tag_list = [
        'h1'   => __( 'H1', 'ht-slider' ),
        'h2'   => __( 'H2', 'ht-slider' ),
        'h3'   => __( 'H3', 'ht-slider' ),
        'h4'   => __( 'H4', 'ht-slider' ),
        'h5'   => __( 'H5', 'ht-slider' ),
        'h6'   => __( 'H6', 'ht-slider' ),
        'p'    => __( 'p', 'ht-slider' ),
        'div'  => __( 'div', 'ht-slider' ),
        'span' => __( 'span', 'ht-slider' ),
    ];
    return $html_tag_list;
}

/**
 *  @since 1.5.0
 *  Elementor pro feature notice function
 *
 * @param [type] $repeater/ $this
 * @param [type] $condition_key
 * @param [type] $array_value
 * @param [type] $type Controls_Manager::RAW_HTML
 * @return void
 */
function htslider_pro_notice( $element, $condition_key, $array_value, $type ){

    $element->add_control(
        'update_pro'.$condition_key,
        [
            'type' => $type,
            'raw' => sprintf(
                /*
                * translators: %1$s: strong and a tag start
                * translators: %2$s: strong and a tag end
                */
                __('Upgrade to pro version to use this feature %1$s Pro Version %2$s', 'ht-slider'),
                '<strong><a href="https://hasthemes.com/plugins/ht-slider-pro-for-elementor/" target="_blank">',
                '</a></strong>'),
            'content_classes' => 'htslider-addons-notice',
            'condition' => [
                $condition_key => $array_value,
            ]
        ]
    );
}

/**
 * @since 1.5.0
 * Get Post Type
 * return array
 */
if( !function_exists('htslider_get_post_types') ){
    function htslider_get_post_types( $args = [], $pro_badge = [] ) {

        $post_type_args = [
            'show_in_nav_menus' => true,
        ];
        if ( ! empty( $args['post_type'] ) ) {
            $post_type_args['name'] = $args['post_type'];
        }
        $_post_types = get_post_types( $post_type_args , 'objects' );

        $post_types  = [];

        foreach ( $_post_types as $post_type => $object ) {

            $search_id = array_search( $object->label, $pro_badge );
            if( $pro_badge && false === $search_id ) {
                $post_types[ $post_type ] = $object->label . esc_html__( ' (Pro)', 'ht-slider' );
            } else {
                $post_types[ $post_type ] = $object->label;
            }
        }
        return $post_types;
    }
}
/**
 * @since 1.5.0
 * All Taxonomie Category Load
 * return Array
*/
if( ! function_exists( 'htslider_category_list_using_taxonomie' ) ){
    function htslider_category_list_using_taxonomie( $taxonomieName ) {

        $allTaxonomie =  get_object_taxonomies( $taxonomieName );
        if ( isset( $allTaxonomie['0'] ) ) {
            if ( $allTaxonomie['0'] == "product_type" ) {
                $allTaxonomie['0'] = 'product_cat';
            }
            return htslider_get_taxonomies( $allTaxonomie['0'] );
        }
    }
}

/**
 * @since 1.5.0
 * Get all Authors List
 *
 * @return array
 */
if( ! function_exists( 'htslider_get_authors_list' ) ) {
    function htslider_get_authors_list() {
        $args = [
            'capability'          => [ 'edit_posts' ],
            'has_published_posts' => true,
            'fields'              => [
                'ID',
                'display_name',
            ],
        ];

        // Version check 5.9.
        if ( version_compare( $GLOBALS['wp_version'], '5.9-alpha', '<' ) ) {
            $args['who'] = 'authors';
            unset( $args['capability'] );
        }

        $authors = get_users( $args );

        if ( ! empty( $authors ) ) {
            return wp_list_pluck( $authors, 'display_name', 'ID' );
        }

        return [];
    }
}


    /**
     * [render_build_content]
     * @param  [int]  $id
     * @return string
     */

     if( ! function_exists( 'htslider_render_build_content' ) ) {
    function htslider_render_build_content( $id ){

            $output = '';
            $document = class_exists('\Elementor\Plugin') ? Elementor\Plugin::instance()->documents->get( $id ) : false;

            if( $document && $document->is_built_with_elementor() ){
                $output = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id );
            }else{
                $content = get_the_content( null, false, $id );

                if ( has_blocks( $content ) ) {
                    $blocks = parse_blocks( $content );
                    $embed = new WP_Embed();
                    foreach ( $blocks as $block ) {
                        $output .= $embed->autoembed(do_shortcode( render_block( $block ) )); //phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                }else{
                    $content = apply_filters( 'the_content', $content );
                    $content = str_replace(']]>', ']]&gt;', $content );
                    return $content;
                }

            }

            return $output;

        }
}
