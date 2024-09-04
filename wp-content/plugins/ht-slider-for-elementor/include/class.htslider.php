<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

final class HTSlider_Addons_Elementor {
    const MINIMUM_ELEMENTOR_VERSION = '2.5.0';
    const MINIMUM_PHP_VERSION = '7.0';
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ], 225 );
        add_action( 'after_setup_theme', [ $this, 'control_image_size' ] );
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );
        add_filter( 'single_template', [ $this,'htslider_canvas_template'] );
        add_action('wp_enqueue_scripts', [ $this,'htslider_theme_assets'] );

        // Elementor Editor Style
        add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'elementor_editor_css' ] );
        // Upgrade Pro Menu
        add_action( 'admin_menu', [$this, 'upgrade_to_pro_menu'], 329 );
        add_action('admin_head', [ $this, 'admin_menu_item_adjust'] );
        add_action('admin_head', [ $this, 'enqueue_admin_head_scripts'], 11 );
    }
    public function i18n() {
        load_plugin_textdomain( 'ht-slider' );

    }
    public function init() {
        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }
        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }

        // Plugins Required File
        $this->includes();

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }
        // Add Plugin actions

        if ( htslider_is_elementor_version( '>=', '3.5.0' ) ) {
            add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
        } else {
            add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
        }


        // Plugins Setting Page
        add_filter('plugin_action_links_'.HTSLIDER_PLUGIN_BASE, [ $this, 'plugins_setting_links' ] );

    }
    /**
     * @since 1.5.0
     * Admin notice.
     * For missing elementor.
     */
    public function add_category( $elements_manager ) {
        $elements_manager->add_category(
            'ht-slider',
            [
                'title'  => esc_html__( 'HT Slider', 'ht-slider' ),
                'icon' => 'font',
            ]
        );
    }
    /*
    * Check Plugins is Installed or not
    */
    public function is_plugins_active( $pl_file_path = NULL ){
        $installed_plugins_list = get_plugins();
        return isset( $installed_plugins_list[$pl_file_path] );
    }
    /**
     * Admin notice.
     * For missing elementor.
     */
    public function admin_notice_missing_main_plugin() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] ); // WPCS: XSS ok.
        $elementor = 'elementor/elementor.php';
        if( $this->is_plugins_active( $elementor ) ) {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
            $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor );
            $message = sprintf(
                /*
                * translators: %1$s: strong tag start
                * translators: %2$s: strong tag end
                */
                __( '%1$sHTSlider Addons for Elementor%2$s requires %1$s"Elementor"%2$s plugin to be active. Please activate Elementor to continue.', 'ht-slider' ), '<strong>', '</strong>'
            );
            $button_text = esc_html__( 'Activate Elementor', 'ht-slider' );
        } else {
            if( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }
            $activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
            $message = sprintf(
                /*
                * translators: %1$s: strong tag start
                * translators: %2$s: strong tag end
                */
                __( '%1$sHTSlider Addons for Elementor%2$s requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', 'ht-slider' ), '<strong>', '</strong>'
            );
            $button_text = esc_html__( 'Install Elementor', 'ht-slider' );
        }
        $button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
        printf( '<div class="error"><p>%1$s</p>%2$s</div>', wp_kses_post($message), wp_kses_post($button) );
    }



    public function admin_notice_minimum_elementor_version() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] ); // WPCS: XSS ok.

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ht-slider' ),
            '<strong>' . esc_html__( 'HTSlider Addons for Elementor', 'ht-slider' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'ht-slider' ) . '</strong>',
             self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message) );

    }

    public function admin_notice_minimum_php_version() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] ); // WPCS: XSS ok.

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ht-slider' ),
            '<strong>' . esc_html__( 'HTSlider Addons', 'ht-slider' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'ht-slider' ) . '</strong>',
             self::MINIMUM_PHP_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message) );

    }
    /*
        *admin menu
    */
    public function admin_menu(){
        $menu = 'add_menu_' . 'page';
        $menu(
            'htslider_panel',
            __( 'HT Slider', 'ht-slider' ),
            'read',
            'htslider_page',
            NULL,
            'dashicons-welcome-view-site',
            90
        );
    }

    //Slider Post template
    public function htslider_canvas_template( $single_template ) {
        global $post;
        if ( 'htslider_slider' == $post->post_type ) {
            $elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
            if ( file_exists( $elementor_2_0_canvas ) ) {
                return $elementor_2_0_canvas;
            } else {
                return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
            }
        }
        return $single_template;
    }

    /* 
    * Add settings link on plugin page.
    */
    public function plugins_setting_links( $links ) {
        $settings_link = '<a href="'.admin_url('edit.php?post_type=htslider_slider').'">'.esc_html__( 'Settings', 'ht-slider' ).'</a>'; 
        array_unshift( $links, $settings_link );

        if( is_plugin_active('ht-slider-for-elementor/ht-slider-for-elementor.php') ){
        $links['htslider_pro'] = sprintf('<a href="https://hasthemes.com/plugins/ht-slider-pro-for-elementor/" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__('Go Pro','ht-slider') . '</a>');
        }
        return $links; 
    }


    public function htslider_theme_assets(){
        self::plugin_css();
        self::plugin_js();
    }

    public function plugin_css(){
        wp_enqueue_style('htslider-widgets', HTSLIDER_PL_ASSETS . 'css/ht-slider-widgets.css', '', HTSLIDER_VERSION );

        // Register Style
        wp_register_style( 'slick', HTSLIDER_PL_ASSETS . 'css/slick.min.css', array(), HTSLIDER_VERSION );
    }
    public function elementor_editor_css(){
        wp_enqueue_style('htslider-elementor-editor', HTSLIDER_PL_ASSETS . 'css/htslider-elementor-editor.css', '', HTSLIDER_VERSION );
    }
    public function plugin_js(){
        // Script register

        wp_register_script( 'slick', HTSLIDER_PL_ASSETS . 'js/slick.min.js', array('jquery'), HTSLIDER_VERSION, TRUE );
        wp_register_script( 'htslider-active', HTSLIDER_PL_ASSETS . 'js/active.js', array('slick'), HTSLIDER_VERSION, TRUE );
        wp_register_script( 'htslider-widget-active', HTSLIDER_PL_ASSETS . 'js/htslider-widgets.js', array('slick'), HTSLIDER_VERSION, TRUE );
    }

    public function init_widgets() {
        // Include Widget files
        //include( HTSLIDER_PL_INCLUDE.'/elementor_widgets.php' );
        include( HTSLIDER_PL_INCLUDE.'/addons/elementor_widgets.php' );
        include( HTSLIDER_PL_INCLUDE.'/addons/htslider_addons.php' );
        include( HTSLIDER_PL_INCLUDE.'/addons/htslider_post_addons.php' );

        if ( get_post_type() !== 'htslider_slider' ) {
        // Register widget
        htslider_widget_register_manager( new \Elementor\Htslider_Elementor_Widget_Sliders() );
        htslider_widget_register_manager( new \Elementor\Htsliderpro_Elementor_Widget_Sliders() );
        htslider_widget_register_manager( new \Elementor\HTSlider_Elementor_Widget_Post_Slider() );
        }

    }


    public function includes() {
        require_once HTSLIDER_PL_INCLUDE. '/admin/Class_Recommended_Plugins.php' ;
        require_once HTSLIDER_PL_INCLUDE. '/admin/recommendation-plugins.php' ;
        require_once HTSLIDER_PL_INCLUDE. '/admin/template-library.php' ;
        require_once HTSLIDER_PL_INCLUDE.'/helpers_function.php';
        require_once HTSLIDER_PL_INCLUDE.'/htslider_icon_manager.php';
        require_once HTSLIDER_PL_INCLUDE.'/custom-post-type.php';
        require_once HTSLIDER_PL_INCLUDE.'/shortcode/htslider-shortcode.php';
    }
    //add image size
    public function control_image_size(){
        add_image_size( 'htliser_size_396x360', 396, 360, true );
        add_image_size( 'htslider_size_1170x536', 1170, 536, true );
    }


    /**
     * [upgrade_to_pro_menu] Admin Menu
     */
    public function upgrade_to_pro_menu(){
        add_submenu_page(
            'htslider_page', 
            esc_html__('Upgrade to Pro', 'ht-slider'),
            esc_html__('Upgrade to Pro', 'ht-slider'), 
            'manage_options', 
            'https://hasthemes.com/plugins/ht-slider-pro-for-elementor/?utm_source=admin&utm_medium=mainmenu&utm_campaign=free'
        );
    }

    // Add Class For pro Menu Item
    public function admin_menu_item_adjust(){
        global $submenu;

		// Check HT Slider Menu page exist or not
		if ( ! isset( $submenu['htslider_page'] ) ) {
			return;
		}

        $position = key(
			array_filter( $submenu['htslider_page'],  
				static function( $item ) {
					return strpos( $item[2], 'https://hasthemes.com/plugins/ht-slider-pro-for-elementor/?utm_source=admin&utm_medium=mainmenu&utm_campaign=free' ) !== false;
				}
			)
		);

        if ( isset( $submenu['htslider_page'][ $position ][4] ) ) {
			$submenu['htslider_page'][ $position ][4] .= ' ht-slider-upgrade-pro';
		} else {
			$submenu['htslider_page'][ $position ][] = 'ht-slider-upgrade-pro';
		}
    }

    // Add Custom scripts for pro menu item
    public function enqueue_admin_head_scripts() {
        $styles = '';
        $scripts = '';

        $styles .= '#adminmenu a.ht-slider-upgrade-pro { font-weight: 600; background-color: #D43A6B; color: #ffffff; display: block; text-align: left;}';
        $scripts .= 'jQuery(document).ready( function($) {
			$("#adminmenu a.ht-slider-upgrade-pro").attr("target","_blank");  
		});';
		
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<style>%s</style>', $styles );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<script>%s</script>', $scripts );
    }

}
HTSlider_Addons_Elementor::instance();
