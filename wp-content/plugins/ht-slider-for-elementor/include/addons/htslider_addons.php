<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Htsliderpro_Elementor_Widget_Sliders extends Widget_Base {

    public function get_name() {
        return 'htsliderpro-addons';
    }
    
    public function get_title() {
        return esc_html__( 'HT: Advanced Slider', 'ht-slider' );
    }

    public function get_icon() {
        return 'eicon-slides';
    }

    public function get_categories() {
        return [ 'ht-slider' ];
    }

    public function get_style_depends() {
        return [
            'slick','htslider-widgets'
        ];
    }
    public function get_script_depends() {
        return [
            'slick',
            'htslider-widget-active',
        ];
    }
    public function get_keywords() {
        return [ 'post slider', 'slider','custom post slider','carousel','post','ht-slider','htslider','content slider' ];
    }
    
    public function get_help_url() {
		return 'https://hasthemes.com/plugins/ht-slider-pro-for-elementor/';
	}
    protected function register_controls() {

        $this->start_controls_section(
            'htlider_content',
            [
                'label' => esc_html__( 'Slider', 'ht-slider' ),
            ]
        );

        $this->add_control(
            'content_sourse',
            [
                'label'   => esc_html__( 'Content Sourse', 'ht-slider' ),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1'   => esc_html__( 'Custom Content', 'ht-slider' ),
                    '2'   => esc_html__( 'HT Slider', 'ht-slider' ),
                ],
            ]
        );

        $this->add_control(
            'slider_on',
            [
                'label'         => esc_html__( 'Slider', 'ht-slider' ),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'On', 'ht-slider' ),
                'label_off'     => esc_html__( 'Off', 'ht-slider' ),
                'return_value'  => 'yes',
                'default'       => 'yes',
            ]
        );
        $this->end_controls_section();

        //custom slider option
        $this->start_controls_section(
            'custom_content',
            [
                'label'     => esc_html__( 'Slides Content', 'ht-slider' ),
                'condition' => [
                    'content_sourse' => '1',
                ]
            ]
        );

        $this->add_control(
            'slider_style',
            [
                'label'   => esc_html__( 'Slide Layout', 'ht-slider' ),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1'   => esc_html__( 'Style 1', 'ht-slider' ),
                    '2'   => esc_html__( 'Style 2 (Pro)', 'ht-slider' ),
                ],
            ]
        );
        htslider_pro_notice( $this,'slider_style', '2', Controls_Manager::RAW_HTML );

        $this->add_control(
            'image_hidden_item',
            [
                'label' => esc_html__( 'View', 'ht-slider' ),
                'type' => Controls_Manager::HIDDEN,
                'default' => 'image_hidden_item',
            ]
        );

            $repeater = new Repeater();

            $repeater->start_controls_tabs('slider_area');

                $repeater->start_controls_tab(
                    'slider_tab',
                    [
                        'label' => esc_html__( 'Content', 'ht-slider' ),
                    ]
                );

            $repeater->add_control(
                'title',
                [
                    'label'       => esc_html__( 'Title', 'ht-slider' ),
                    'type'        => Controls_Manager::TEXTAREA,
                    'default'     => '',
                    'label_block' => 'true',
                    'description' => esc_html__( 'HTML tag supported', 'ht-slider' ),
                ]
            );
            $repeater->add_control(
                'subtitle',
                [
                    'label'       => esc_html__( 'Sub title', 'ht-slider' ),
                    'type'        => Controls_Manager::TEXTAREA,
                    'default'     => '',
                    'label_block' => 'true',
                    'description' => esc_html__( 'HTML tag supported', 'ht-slider' ),
                ]
            );
            
            $repeater->add_control(
                'desc',
                [
                    'label'       => esc_html__( 'Excerpt', 'ht-slider' ),
                    'type'        => Controls_Manager::WYSIWYG,
                    'default'     => '',
                    'label_block' => 'true',
                ]
            );

            $repeater->add_control(
                'button_text',
                [
                    'label'         => esc_html__( 'Button Text', 'ht-slider' ),
                    'type'          => Controls_Manager::TEXT,
                    'default'       => '',
                ]
            );

            $repeater->add_control(
                'button_link',
                [
                    'label'             => esc_html__( 'Link', 'ht-slider' ),
                    'type'              => Controls_Manager::URL,
                    'placeholder'       => esc_html__( 'https://example.com', 'ht-slider' ),
                    'show_external'     => true,
                    'default'           => [
                        'url'           => '',
                        'is_external'   => false,
                        'nofollow'      => false,
                    ],
                ]
            );  

            $repeater->add_control(
                'image_position',
                [
                    'label'      => esc_html__( 'Image Position', 'ht-slider' ),
                    'type'       => Controls_Manager::SELECT,
                    'default'    => 'left',
                    'options'    => [
                        'left'   => esc_html__( 'Left', 'ht-slider' ),
                        'right'  => esc_html__( 'Right', 'ht-slider' ),
                    ],
                    'description' => esc_html__( 'Image Position only for Slide Layout Two', 'ht-slider' ),
                ]
            );

            $repeater->add_control(
                'hidden_item_selector',
                [
                    'label' => esc_html__( 'Image Position', 'ht-slider' ),
                    'type' => Controls_Manager::HIDDEN,
                    'default' => 'hidden_item_selector',
                ]
            );

            $repeater->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'      => 'background',
                    'label'     => esc_html__( 'Background', 'ht-slider' ),
                    'types'     => [ 'classic' ],
                    'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}.htslider-item-img',
                ]
            ); 


            $repeater->end_controls_tab(); // Content tab end

                $repeater->start_controls_tab(
                    'slider_rep_style',
                    [
                        'label' => esc_html__( 'Individual Style', 'ht-slider' ),
                    ]
                );

                $repeater->add_control(
                    'update_pro_sytle',
                    [
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => sprintf(
                            /*
                            * translators: %1$s: anchor start tag
                            * translators: %2$s: anchor end tag
                            */
                            __('Upgrade to pro version to use this feature %1$s Pro Version %2$s', 'ht-slider'),
                            '<strong><a href="https://hasthemes.com/plugins/ht-slider-pro-for-elementor/" target="_blank">',
                            '</a></strong>'),
                        'content_classes' => 'htslider-addons-notice',
                    ]
                );

                  
                $repeater->end_controls_tab();// End Style tab

            $repeater->end_controls_tabs();// Repeater Tabs end

            $this->add_control(
                'sliders_list',
                [
                    'label'     => esc_html__( 'Slider Items', 'ht-slider' ),                         
                    'type'      => Controls_Manager::REPEATER,
                    'fields'    =>  $repeater->get_controls(),
                    'default'   => [
                        [
                            'subtitle'    => esc_html__( 'Sub title item ', 'ht-slider' ),
                            'title'       => esc_html__( 'Slider Item 1', 'ht-slider' ),
                            'desc'        => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quod consequatur enim corrupti', 'ht-slider' ),
                            'button_text' => esc_html__( 'Details', 'ht-slider' ),
                            'button_link' => esc_html__( '#', 'ht-slider' ),
                        ],
                        [
                            'subtitle'    => esc_html__( 'Sub title item ', 'ht-slider' ),
                            'title'       => esc_html__( 'Slider Item 2', 'ht-slider' ),
                            'desc'        => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quod consequatur enim corrupti', 'ht-slider' ),
                            'button_text' => esc_html__( 'Details', 'ht-slider' ),
                            'button_link' => esc_html__( '#', 'ht-slider' ),
                        ],
                    ],
                    'slider_title' => esc_html__( 'Single Slide', 'ht-slider' ),
                ]
            ); 

            $this->add_control(
                'title_html_tag',
                [
                    'label'   => esc_html__( 'Title HTML Tag', 'ht-slider' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'h2',
                    'options' => htslider_html_tag_lists(),
                    'separator' => 'before',
                ]
            );
    
            $this->add_control(
                'subtitle_html_tag',
                [
                    'label'   => esc_html__( 'Sub Title HTML Tag', 'ht-slider' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'h4',
                    'options' => htslider_html_tag_lists(),
                ]
            );
        $this->end_controls_section();
        //end custom slider option 

        //custom post type
        $this->start_controls_section(
            'post_content',
            [
                'label'     => esc_html__( 'Post Option', 'ht-slider' ),
                'condition' => [
                    'content_sourse' => '2',
                ]
            ]
        );

        $this->add_control(
                'slider_show_by',
                [
                    'label'     => esc_html__( 'Slider Show By', 'ht-slider' ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'show_bycat',
                    'options'   => [
                        'show_byid'   => esc_html__( 'Show By ID', 'ht-slider' ),
                        'show_bycat'  => esc_html__( 'Show By Category', 'ht-slider' ),
                    ],
                ]
            );

            $this->add_control(
                'slider_id',
                [
                    'label'         => esc_html__( 'Select Slides', 'ht-slider' ),
                    'type'          => Controls_Manager::SELECT2,
                    'label_block'   => true,
                    'multiple'      => true,
                    'options'       => htslider_post_name( 'htslider_slider' ),
                    'condition'     => [
                        'slider_show_by' => 'show_byid',
                    ]
                ]
            );

            $this->add_control(
                'slider_cat',
                [
                    'label'         => esc_html__( 'Select Category', 'ht-slider' ),
                    'type'          => Controls_Manager::SELECT2,
                    'label_block'   => true,
                    'multiple'      => true,
                    'options'       => htslider_get_taxonomies( 'htslider_category' ),
                    'condition'     => [
                        'slider_show_by' => 'show_bycat',
                    ]
                ]
            );
            
            $this->add_control(
                'slider_limit',
                [
                    'label'     => esc_html__( 'Slides Limit', 'ht-slider' ),
                    'type'      => Controls_Manager::NUMBER,
                    'step'      => 1,
                    'default'   => 2,
                ]
            );

        $this->end_controls_section();
        //end custop post type

        // Slider setting
        $this->start_controls_section(
            'slider_option',
            [
                'label'         => esc_html__( 'Slider Option', 'ht-slider' ),
                'condition'     => [
                    'slider_on' => 'yes',
                ]
            ]
        );
            $this->add_control(
                'slides_items_popover',
                [
                    'label' => esc_html__( 'Slides Items', 'ht-slider' ),
                    'type' => Controls_Manager::POPOVER_TOGGLE,
                ]
            );

            $this->start_popover();
                $this->add_control(
                    'slitems',
                    [
                        'label'         => esc_html__( 'Slide Items (Desktop)', 'ht-slider' ),
                        'type'          => Controls_Manager::NUMBER,
                        'min'           => 1,
                        'max'           => 20,
                        'step'          => 1,
                        'default'       => 1,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );

                $this->add_control(
                    'slscroll_columns',
                    [
                        'label'         => esc_html__('Slider Item to Scroll', 'ht-slider'),
                        'type'          => Controls_Manager::NUMBER,
                        'min'           => 1,
                        'max'           => 10,
                        'step'          => 1,
                        'default'       => 1,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );

                $this->add_control(
                    'heading_tablet',
                    [
                        'label'             => esc_html__( 'Tablet', 'ht-slider' ),
                        'type'              => Controls_Manager::HEADING,
                        'separator'         => 'after',
                        'condition'         => [
                            'slider_on'     => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'sltablet_display_columns',
                    [
                        'label'         => esc_html__('Slider Items', 'ht-slider'),
                        'type'          => Controls_Manager::NUMBER,
                        'min'           => 1,
                        'max'           => 8,
                        'step'          => 1,
                        'default'       => 1,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'sltablet_scroll_columns',
                    [
                        'label'         => esc_html__('Slider Item to Scroll', 'ht-slider'),
                        'type'          => Controls_Manager::NUMBER,
                        'min'           => 1,
                        'max'           => 8,
                        'step'          => 1,
                        'default'       => 1,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'sltablet_width',
                    [
                        'label'         => esc_html__('Tablet Resolution', 'ht-slider'),
                        'description'   => esc_html__('The resolution to tablet.', 'ht-slider'),
                        'type'          => Controls_Manager::NUMBER,
                        'default'       => 750,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'heading_mobile',
                    [
                        'label'         => esc_html__( 'Mobile Phone', 'ht-slider' ),
                        'type'          => Controls_Manager::HEADING,
                        'separator'     => 'after',
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'slmobile_display_columns',
                    [
                        'label'         => esc_html__('Slider Items', 'ht-slider'),
                        'type'          => Controls_Manager::NUMBER,
                        'min'           => 1,
                        'max'           => 4,
                        'step'          => 1,
                        'default'       => 1,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'slmobile_scroll_columns',
                    [
                        'label'     => esc_html__('Slider Item To Scroll', 'ht-slider'),
                        'type'      => Controls_Manager::NUMBER,
                        'min'       => 1,
                        'max'       => 4,
                        'step'      => 1,
                        'default'   => 1,
                        'condition' => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
    
                $this->add_control(
                    'slmobile_width',
                    [
                        'label'         => esc_html__('Mobile Resolution', 'ht-slider'),
                        'description'   => esc_html__('The resolution to mobile.', 'ht-slider'),
                        'type'          => Controls_Manager::NUMBER,
                        'default'       => 480,
                        'condition'     => [
                            'slider_on' => 'yes',
                        ]
                    ]
                );
            $this->end_popover();

            $this->add_control(
                'slarrows',
                [
                    'label'         => esc_html__( 'Slider Arrow', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'slarrows_type',
                [
                    'label' => esc_html__( 'Icon Type', 'ht-slider' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'icon',
                    'options' => [
                        'icon' => esc_html__( 'Icon', 'ht-slider' ),
                        'image' => esc_html__( 'Image (Pro)', 'ht-slider' ),
                        'text' => esc_html__( 'Text (Pro)', 'ht-slider' ),
                    ],
                    'condition' => [
                        'slider_on' => 'yes',
                        'slarrows' => 'yes',
                    ]                    
                ]
            );

            htslider_pro_notice( $this,'slarrows_type', ['image','text'], Controls_Manager::RAW_HTML );

            $this->add_control(
                'slprevicon',
                [
                    'label'         => esc_html__( 'Previous Icon', 'ht-slider' ),
                    'type'          => Controls_Manager::ICONS,
                    'default'       => [
                        'value'     => 'fas fa-angle-left',
                        'library'   => 'solid',
                    ],
                    'condition'     => [
                        'slider_on' => 'yes',
                        'slarrows'  => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slnexticon',
                [
                    'label'         => esc_html__( 'Next Icon', 'ht-slider' ),
                    'type'          => Controls_Manager::ICONS,
                    'default'       => [
                        'value'     => 'fas fa-angle-right',
                        'library'   => 'solid',
                    ],
                    'condition'     => [
                        'slider_on' => 'yes',
                        'slarrows'  => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'sldots',
                [
                    'label'         => esc_html__( 'Slider Dots', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slpagination',
                [
                    'label'         => esc_html__( 'Show Dots Index', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'condition'     => [
                        'sldots' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'slide_effect',
                [
                    'type' => Controls_Manager::SELECT,
                    'label' => esc_html__( 'Slide Effect', 'ht-slider' ) . ' <i class="eicon-pro-icon"></i>',
                    'default' => 'slide',
                    'options' => [
                        'slide' => esc_html__( 'Slide', 'ht-slider' ),
                        'fade' => esc_html__( 'Fade', 'ht-slider' ),
                    ],
                    'separator' => 'before',
                    'classes' => 'htslider-disable-control',
                ]
            );
            $this->add_control(
                'variable_width',
                [
                    'label' => esc_html__( 'Variable Width', 'ht-slider' ) . ' <i class="eicon-pro-icon"></i>',
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('Column width according to Content', 'ht-slider'),
                    'return_value' => 'yes',
                    'default' => 'no',
                    'classes' => 'htslider-disable-control',
                    
                ]
            );
            
            $this->add_control(
                'vertical',
                [
                    'label' => esc_html__( 'Vertical Mode', 'ht-slider' ) . ' <i class="eicon-pro-icon"></i>',
                    'type' => Controls_Manager::SWITCHER,
                    'description' => __('On this Switcher to Verticle Slide', 'ht-slider'),
                    'return_value' => 'yes',
                    'default' => 'no',
                    'slide_effect' => 'slide',
                    'classes' => 'htslider-disable-control',
                ]
            );
            $this->add_control(
                'slpause_on_hover',
                [
                    'type'          => Controls_Manager::SWITCHER,
                    'label_off'     => esc_html__('No', 'ht-slider'),
                    'label_on'      => esc_html__('Yes', 'ht-slider'),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'label'         => esc_html__('Pause on Hover?', 'ht-slider'),
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slcentermode',
                [
                    'label'         => esc_html__( 'Center Mode', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slcenterpadding',
                [
                    'label'             => esc_html__( 'Center Padding', 'ht-slider' ),
                    'type'              => Controls_Manager::NUMBER,
                    'min'               => 0,
                    'max'               => 500,
                    'step'              => 1,
                    'default'           => 50,
                    'condition'         => [
                        'slider_on'     => 'yes',
                        'slcentermode'  => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slautolay',
                [
                    'label'             => esc_html__( 'Slide Auto Play', 'ht-slider' ),
                    'type'              => Controls_Manager::SWITCHER,
                    'return_value'      => 'yes',
                    'separator'         => 'before',
                    'default'           => 'no',
                    'condition'         => [
                        'slider_on'     => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slautoplay_speed',
                [
                    'label'         => esc_html__('Autoplay Speed', 'ht-slider'),
                    'type'          => Controls_Manager::NUMBER,
                    'default'       => 3000,
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slanimation_speed',
                [
                    'label'         => __('Slide Animation Speed', 'ht-slider'),
                    'type'          => Controls_Manager::NUMBER,
                    'default'       => 300,
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

        $this->end_controls_section(); // Slider Option end

        // Style Slider arrow style start
        $this->start_controls_section(
            'slider_style_control',
            [
                'label'             => esc_html__( 'Slides Style', 'ht-slider' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         =>[
                    'content_sourse' => '1',
                ],
            ]
        );

            $this->add_control(
                'content_color',
                [
                    'label'     => esc_html__( 'Color', 'ht-slider' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '#333333',
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .htslider-subtitle' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-desc' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'bg_color',
                [
                    'label'     => esc_html__( 'Background', 'ht-slider' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '#ededed',
                    'selectors' => [
                        '{{WRAPPER}} .htslider-item-img' => 'background: {{VALUE}}',
                    ],
                ]
            );
    
            $this->add_responsive_control(
                'slider_seciton_height',
                [
                'label'     => esc_html__( 'Slider Height', 'shieldem' ),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 0,
                'max'       => 2000,
                'step'      => 1,
                'default'   =>650,
                'selectors' => [
                '{{WRAPPER}} .htslider-item-img.single-slide-item.htslider-single-post-slide' => 'height: {{VALUE}}px;',
                ],
                ]
            );
        $this->add_control(
            'text_align',
            [
                'label' => esc_html__( 'Alignment', 'ht-slider' ),
                'type'  => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'  => [
                        'title' => esc_html__( 'Left', 'ht-slider' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'ht-slider' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'ht-slider' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
              
                'selectors' => [
                    '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner' => 'text-align: {{VALUE}};',
                ],
            ]
        );

           $this->start_controls_tabs( 'slider_style_tabs' );

                // title tab Start
                $this->start_controls_tab(
                    'slider_style_title_tab',
                    [
                        'label' => esc_html__( 'Title', 'ht-slider' ),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name'      => 'title_typography',
                        'label'     => esc_html__( 'Typography', 'ht-slider' ),
                        'selector'  => '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title',
                    ]
                );
                $this->add_control(
                    'slide_title_color',
                    [
                        'label'     => esc_html__( 'Color', 'ht-slider' ),
                        'type'      => Controls_Manager::COLOR,
                        'default'   => '',
                        'selectors' => [
                            '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title' => 'color: {{VALUE}}',
                        ],
                    ]
                );
    
                $this->add_control(
                    'title_margin',
                    [
                        'label'         => esc_html__( 'Margin', 'ht-slider' ),
                        'type'          => Controls_Manager::DIMENSIONS,
                        'size_units'    => [ 'px', '%', 'em' ],
                        'selectors'     => [
                            '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->end_controls_tab(); // title tab end

                // Subtitle tab Start
                $this->start_controls_tab(
                    'slider_style_subtitle_tab',
                    [
                        'label' => esc_html__( 'Sub Title', 'ht-slider' ),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name'      => 'subtitle_typography',
                        'label'     => esc_html__( 'Typography', 'ht-slider' ),
                        'selector'  => '{{WRAPPER}} .htslider-single-post-slide .content .htslider-subtitle',
                    ]
                );
                $this->add_control(
                    'slide_subtitle_color',
                    [
                        'label'     => esc_html__( 'Color', 'ht-slider' ),
                        'type'      => Controls_Manager::COLOR,
                        'default'   => '',
                        'selectors' => [
                            '{{WRAPPER}} .htslider-single-post-slide .content .htslider-subtitle' => 'color: {{VALUE}}',
                        ],
                    ]
                );
                $this->add_control(
                    'subtitle_margin',
                    [
                        'label'         => esc_html__( 'Margin', 'ht-slider' ),
                        'type'          => Controls_Manager::DIMENSIONS,
                        'size_units'    => [ 'px', '%', 'em' ],
                        'selectors'     => [
                            '{{WRAPPER}} .htslider-single-post-slide .content .htslider-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->end_controls_tab(); // subtitle tab end

                // excerpt tab Start
                $this->start_controls_tab(
                    'slider_style_excerpt_tab',
                    [
                        'label' => __( 'Excerpt', 'ht-slider' ),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name'      => 'content_typography',
                        'label'     => esc_html__( 'Typography', 'ht-slider' ),
                        'selector'  => '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-desc,.htslider-single-post-slide .content .post-inner .htslider-desc p, .htslider-single-post-slide .content .post-inner .htslider-desc h1,.htslider-single-post-slide .content .post-inner .htslider-desc h2,.htslider-single-post-slide .content .post-inner .htslider-desc h3,.htslider-single-post-slide .content .post-inner .htslider-desc h4,.htslider-single-post-slide .content .post-inner .htslider-desc h5, .htslider-single-post-slide .content .post-inner .htslider-desc h6',
                    ]
                );
                $this->add_control(
                    'slide_content_color',
                    [
                        'label'     => esc_html__( 'Color', 'ht-slider' ),
                        'type'      => Controls_Manager::COLOR,
                        'default'   => '',
                        'selectors' => [
                            '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-desc' => 'color: {{VALUE}}',
                        ],
                    ]
                );
                $this->add_control(
                    'content_margin',
                    [
                        'label'         => esc_html__( 'Margin', 'ht-slider' ),
                        'type'          => Controls_Manager::DIMENSIONS,
                        'size_units'    => [ 'px', '%', 'em' ],
                        'selectors'     => [
                            '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
 
                $this->end_controls_tab(); // excerpt tab end

            $this->end_controls_tabs();

        $this->end_controls_section(); // style Option end
        // Content box style
        $this->start_controls_section(
            'content_box_style_section',
            [
                'label'             => esc_html__( 'Content Box', 'ht-slider' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         =>[
                    'content_sourse' => '1',
                ],
            ]
        );
        $this->add_responsive_control(
            'content_box_width',
            [
                'label' => esc_html__( 'Width', 'ht-slider' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_box_bg_color',
                'label' => __( 'Background', 'ht-slider' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'content_box_border',
                'label'     => esc_html__( 'Border', 'ht-slider' ),
                'selector'  => '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner',
            ]
        );

        $this->add_responsive_control(
            'content_box_radius',
            [
                'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_box_boxshadow',
                'label' => __( 'Box Shadow', 'ht-slider' ),
                'selector' => '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner',
            ]
        );
        $this->add_responsive_control(
            'content_box_margin',
            [
                'label' => __( 'Margin', 'ht-slider' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

                ],
            ]
        );
        $this->add_responsive_control(
            'content_box_padding',
            [
                'label' => __( 'Padding', 'ht-slider' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content .post-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'content_box_position',
            [
                'label' => esc_html__( 'Position', 'ht-slider' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'separator' => 'before',
            ]
        );
        $this->start_popover();
            $this->add_responsive_control(
                'content_vertical_position',
                [
                    'label' => esc_html__( 'Vertical Position', 'ht-slider' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'center',
                    'options' => [
                        'start' => esc_html__( 'Top (Pro)', 'ht-slider' ),
                        'center' => esc_html__( 'Center', 'ht-slider' ),
                        'flex-end' => esc_html__( 'Bottom (Pro)', 'ht-slider' ),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content' => 'align-items: center',
                    ],
                ]
            );
            htslider_pro_notice( $this,'content_vertical_position', ['start','flex-end'], Controls_Manager::RAW_HTML );

            $this->add_responsive_control(
                'content_horizontal_position',
                [
                    'label' => esc_html__( 'Horizontal Position', 'ht-slider' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'left',
                    'options' => [
                        'left' => esc_html__( 'left', 'ht-slider' ),
                        'center' => esc_html__( 'Center (Pro)', 'ht-slider' ),
                        'right' => esc_html__( 'Right (Pro)', 'ht-slider' ),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .single-slide-item.htslider-single-post-slide .content' => 'justify-content: left',
                    ],
                ]
            );
            htslider_pro_notice( $this,'content_horizontal_position', ['center','right'], Controls_Manager::RAW_HTML );
        $this->end_popover();

        $this->end_controls_section(); // content box style end

         $this->start_controls_section(
            'slider_style_button_control',
            [
                'label'             => esc_html__( 'Button', 'ht-slider' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         =>[
                    'content_sourse' => '1',
                ],
            ]
        );

            $this->start_controls_tabs( 'slider_button_style_tabs' );

                // Normal tab Start
                $this->start_controls_tab(
                    'slider_button_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'ht-slider' ),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name'      => 'button_typography',
                        'label'     => esc_html__( 'Typography', 'ht-slider' ),
                        'selector'  => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn',
                    ]
                );
                $this->add_control(
                    'slider_button_color',
                    [
                        'label'     => esc_html__( 'Color', 'ht-slider' ),
                        'type'      => Controls_Manager::COLOR,
                        'default'   => '#18012c',
                        'selectors' => [
                            '{{WRAPPER}} .htslider-area-pro .htslider-single-post-slide .post-btn a.readmore-btn' => 'color: {{VALUE}};',
                        ],
                    ]
                );
                $this->add_control(
                    'slider_button_bg',
                    [
                        'label'     => esc_html__( 'Background  Color', 'ht-slider' ),
                        'type'      => Controls_Manager::COLOR,
                        'default'   => '#ffffff',
                        'selectors' => [
                            '{{WRAPPER}} .htslider-area-pro .htslider-single-post-slide .post-btn a.readmore-btn' => 'background: {{VALUE}};',
                        ],
                    ]
                );


                $this->add_control(
                    'button_padding',
                    [
                        'label'         => esc_html__( 'Padding', 'ht-slider' ),
                        'type'          => Controls_Manager::DIMENSIONS,
                        'size_units'    => [ 'px', '%', 'em' ],
                        'selectors'     => [
                            '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_control(
                    'button_margin',
                    [
                        'label'         => esc_html__( 'Margin', 'ht-slider' ),
                        'type'          => Controls_Manager::DIMENSIONS,
                        'size_units'    => [ 'px', '%', 'em' ],
                        'selectors'     => [
                            '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
                       
                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name'      => 'slider_button_border',
                            'label'     => esc_html__( 'Border', 'ht-slider' ),
                            'selector'  => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn',
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_button_border_radius',
                        [
                            'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type'      => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );


                $this->end_controls_tab(); // Normal tab end

                // Hover tab Start
                $this->start_controls_tab(
                    'slider_button_style_hover_tab',
                    [
                        'label' => esc_html__( 'Hover', 'ht-slider' ),
                    ]
                );

                    $this->add_control(
                        'slider_button_hover_color',
                        [
                            'label'     => esc_html__( 'Color', 'ht-slider' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   => '#00282a',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-area-pro .htslider-single-post-slide .post-btn a.readmore-btn:hover' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                $this->add_control(
                    'slider_button_hover_bg',
                    [
                        'label'     => esc_html__( 'Color', 'ht-slider' ),
                        'type'      => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .htslider-area-pro .htslider-single-post-slide .post-btn a.readmore-btn:hover' => 'background: {{VALUE}};',
                        ],
                    ]
                );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name'      => 'slider_button_hover_border',
                            'label'     => esc_html__( 'Border', 'ht-slider' ),
                            'selector'  => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_button_hover_border_radius',
                        [
                            'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type'      => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Hover tab end

            $this->end_controls_tabs();

        $this->end_controls_section(); // Style Slider arrow style end


        // Style Slider arrow style start
        $this->start_controls_section(
            'slider_arrow_style',
            [
                'label'         => esc_html__( 'Arrow', 'ht-slider' ),
                'tab'           => Controls_Manager::TAB_STYLE,
                'condition'     =>[
                    'slider_on' => 'yes',
                    'slarrows'  => 'yes',
                ],
            ]
        );
            $this->add_control(
                'post_slider_arrow_style',
                [
                    'label'     => esc_html__( 'Arrow Position', 'ht-slider' ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => '1',
                    'options'   => [
                        '1'     => esc_html__( 'Default', 'ht-slider' ),
                        '2'     => esc_html__( 'Right Center', 'ht-slider' ),
                        '3'     => esc_html__( 'Bottom Left', 'ht-slider' ),
                        '4'     => esc_html__( 'Custom Position (Pro)', 'ht-slider' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                'post_slider_arrow_inner_space',
                [
                    'label' => __( 'Inner Gap', 'ht-slider' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-arrow-2.htslider-postslider-area button.htslider-carosul-prev.slick-arrow' => 'margin-top: -{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htslider-arrow-2.htslider-postslider-area button.htslider-carosul-next.slick-arrow' => 'margin-top: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htslider-arrow-3.htslider-postslider-area button.htslider-carosul-prev.slick-arrow' => 'bottom: {{SIZE}}{{UNIT}}!important;',
                    ],
                    'condition'     =>[
                        'post_slider_arrow_style' => ['2','3'],
                    ],
                ]
            );
            $this->add_responsive_control(
                'slider_arrow_position_X',
                [
                    'label' => __( 'Offset X', 'ht-slider' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => -1000,
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -100,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-arrow-2.htslider-postslider-area button.htslider-carosul-prev.slick-arrow,{{WRAPPER}} .htslider-arrow-2.htslider-postslider-area button.htslider-carosul-next.slick-arrow' => 'margin-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htslider-arrow-3.htslider-postslider-area button.htslider-carosul-next.slick-arrow,{{WRAPPER}} .htslider-arrow-3.htslider-postslider-area button.htslider-carosul-prev.slick-arrow' => 'left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htslider-arrow-1.htslider-postslider-area button.slick-arrow' => 'left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htslider-arrow-1.htslider-postslider-area button.htslider-carosul-next.slick-arrow' => 'right: {{SIZE}}{{UNIT}};',
                    ],
                    'condition'     =>[
                        'post_slider_arrow_style!' => '4',
                    ],
                ]
            );

            htslider_pro_notice( $this,'post_slider_arrow_style', '4', Controls_Manager::RAW_HTML );

            $this->add_responsive_control(
                'slider_arrow_height',
                [
                    'label' => esc_html__( 'Height', 'ht-slider' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 46,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-postslider-area button.slick-arrow' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'slider_arrow_width',
                [
                    'label' => esc_html__( 'Width', 'ht-slider' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 46,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-postslider-area button.slick-arrow' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'slider_arrow_padding',
                [
                    'label'      => esc_html__( 'Padding', 'ht-slider' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors'  => [
                        '{{WRAPPER}} .htslider-postslider-area button.slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'slider_arrow_fontsize',
                [
                    'label' => esc_html__( 'Font Size', 'ht-slider' ),
                    'type'  => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min'   => 0,
                            'max'   => 100,
                            'step'  => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default'   => [
                        'unit'  => 'px',
                        'size'  => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-postslider-area button.slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htslider-postslider-area button.slick-arrow svg' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_control(
                'arrow_color_border_heading',
                [
                    'label' => __( 'Colors and Border', 'ht-slider' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $this->start_controls_tabs( 'slider_arrow_style_tabs' );

                // Normal tab Start
                $this->start_controls_tab(
                    'slider_arrow_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'ht-slider' ),
                    ]
                );

                    $this->add_control(
                        'slider_arrow_color',
                        [
                            'label'     => esc_html__( 'Color', 'ht-slider' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   => '#00282a',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-postslider-area button.slick-arrow' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .htslider-postslider-area button.slick-arrow svg path' => 'fill: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name'      => 'slider_arrow_background',
                            'label'     => esc_html__( 'Background', 'ht-slider' ),
                            'types'     => [ 'classic', 'gradient' ],
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area button.slick-arrow',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name'      => 'slider_arrow_border',
                            'label'     => esc_html__( 'Border', 'ht-slider' ),
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area button.slick-arrow',
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_border_radius',
                        [
                            'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type'      => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-postslider-area button.slick-arrow' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );
                    $this->add_group_control(
                        Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => 'slider_arrow_boxshadow',
                            'label' => __( 'Box Shadow', 'ht-slider' ),
                            'selector' => '{{WRAPPER}} .htslider-postslider-area button.slick-arrow',
                        ]
                    );
                $this->end_controls_tab(); // Normal tab end

                // Hover tab Start
                $this->start_controls_tab(
                    'slider_arrow_style_hover_tab',
                    [
                        'label' => esc_html__( 'Hover', 'ht-slider' ),
                    ]
                );

                    $this->add_control(
                        'slider_arrow_hover_color',
                        [
                            'label'     => esc_html__( 'Color', 'ht-slider' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   => '#00282a',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-postslider-area button.slick-arrow:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .htslider-postslider-area button.slick-arrow:hover svg path' => 'fill: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name'      => 'slider_arrow_hover_background',
                            'label'     => esc_html__( 'Background', 'ht-slider' ),
                            'types'     => [ 'classic', 'gradient' ],
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area button.slick-arrow:hover',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name'      => 'slider_arrow_hover_border',
                            'label'     => esc_html__( 'Border', 'ht-slider' ),
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area button.slick-arrow:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'slider_arrow_hover_border_radius',
                        [
                            'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type'      => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-postslider-area button.slick-arrow:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );
                    $this->add_group_control(
                        Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => 'slider_arrow_hover_boxshadow',
                            'label' => __( 'Box Shadow', 'ht-slider' ),
                            'selector' => '{{WRAPPER}} .htslider-postslider-area button.slick-arrow:hover',
                        ]
                    );
        
                $this->end_controls_tab(); // Hover tab end

            $this->end_controls_tabs();

        $this->end_controls_section(); // Style Slider arrow style end

        // Style Pagination button tab section
        $this->start_controls_section(
            'post_slider_pagination_style_section',
            [
                'label'         => esc_html__( 'Pagination', 'ht-slider' ),
                'tab'           => Controls_Manager::TAB_STYLE,
                'condition'     =>[
                    'slider_on' => 'yes',
                    'sldots'    =>'yes',
                ]
            ]
        );
            //pagination postition
            $this->add_control(
                'pagination_position',
                [
                    'label' => esc_html__( 'Pagination Position', 'ht-slider' ),
                    'type' => Controls_Manager::POPOVER_TOGGLE,
                ]
            );
            $this->start_popover();
            $this->add_control(
                'dot_show_position',
                [
                    'label' => esc_html__( 'Dots Show In', 'ht-slider' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'dot_bottom_center',
                    'options' => [
                        'dot_bottom_center' => esc_html__( 'Bottom Center', 'ht-slider' ),
                        'dot_bottom_left' => esc_html__( 'Bottom Left', 'ht-slider' ),
                        'dot_bottom_right' => esc_html__( 'Bottom Right', 'ht-slider' ),
                        'dot_right_center' => esc_html__( 'Right Center', 'ht-slider' ),
                        'dot_left_center' => esc_html__( 'Left Center', 'ht-slider' ),
                        'dot_custom' => esc_html__( 'Custom Position', 'ht-slider' ),
                    ],                
                ]
            );

            $this->add_responsive_control(
                    'pagination_x_position',
                    [
                        'label' => esc_html__( 'Horizontal Position', 'ht-slider' ),
                        'type'  => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'default' => [
                            'size' => 50,
                            'unit' => '%',
                        ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],

                        'selectors' => [
                            '{{WRAPPER}} .htslider-postslider-area ul.slick-dots' => 'left: {{SIZE}}{{UNIT}};',
                        ],
                        'condition'     =>[
                            'dot_show_position' => 'dot_custom',
                        ]
                    ]
                );

                $this->add_responsive_control(
                    'pagination_y_position',
                    [
                        'label' => esc_html__( 'Vertical Position', 'ht-slider' ),
                        'type'  => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'default' => [
                            'size' => 92,
                            'unit' => '%',
                        ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .htslider-postslider-area ul.slick-dots' => 'top: {{SIZE}}{{UNIT}};',
                        ],
                        'condition'     =>[
                            'dot_show_position' => 'dot_custom',
                        ]
                    ]
                );
                $this->add_responsive_control(
                    'carousel_dots_offset',
                    [
                        'label' => __( 'Offset X', 'ht-slider' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'range' => [
                            'px' => [
                                'min' => -1000,
                                'max' => 1000,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => -100,
                                'max' => 100,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => '',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .htslider-carousel-activation.dot_bottom_right .slick-dots,{{WRAPPER}} .htslider-carousel-activation.dot_right_center .slick-dots' => 'right: {{SIZE}}{{UNIT}};left:auto;',
                            '{{WRAPPER}} .htslider-carousel-activation.dot_bottom_left .slick-dots,{{WRAPPER}} .htslider-carousel-activation.dot_left_center .slick-dots' => 'left: {{SIZE}}{{UNIT}};',
                            
                        ],
                        'condition'     =>[
                            'dot_show_position!' => ['dot_custom','dot_bottom_center'],
                        ]
                    ]
                );
                $this->add_responsive_control(
                    'carousel_dots_offset_y',
                    [
                        'label' => __( 'Offset Y', 'ht-slider' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'range' => [
                            'px' => [
                                'min' => -1000,
                                'max' => 1000,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => -100,
                                'max' => 100,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => '',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .htslider-carousel-activation.dot_bottom_left .slick-dots,
                            {{WRAPPER}} .htslider-carousel-activation.dot_bottom_right .slick-dots,
                            {{WRAPPER}} .htslider-carousel-activation.dot_bottom_center .slick-dots
                            ' => 'bottom: {{SIZE}}{{UNIT}};top:auto;',
                        ],
                        'condition'     =>[
                            'dot_show_position' => ['dot_bottom_left','dot_bottom_right','dot_bottom_center'],
                        ]
                    ]
                );
                $this->add_responsive_control(
                    'carousel_dots_pagination_inner_space',
                    [
                        'label' => __( 'Inner Gap', 'ht-slider' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 200,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => '5',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .htslider-carousel-activation.dot_right_center .slick-dots li:not(:last-child), {{WRAPPER}} .htslider-carousel-activation.dot_left_center .slick-dots li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .htslider-carousel-activation.dot_bottom_center .slick-dots li:not(:last-child), {{WRAPPER}} .htslider-carousel-activation.dot_bottom_right .slick-dots li:not(:last-child),{{WRAPPER}} .htslider-carousel-activation.dot_bottom_left .slick-dots li:not(:last-child),{{WRAPPER}} .htslider-carousel-activation.dot_custom .slick-dots li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );
            $this->end_popover();
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'dot_typgrapry',
                    'selector' => '{{WRAPPER}} .htslider-dot-index-yes .slick-dots li button',
                    'condition' =>[
                        'slpagination'=>'yes',
                    ],
                    
                ]
            ); 

            $this->add_responsive_control(
                'slider_pagination_padding',
                [
                    'label'      => esc_html__( 'Padding', 'ht-slider' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors'  => [
                        '{{WRAPPER}} .htslider-postslider-area ul.slick-dots li button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'pagination_margin',
                [
                    'label'         => esc_html__( 'Margin', 'ht-slider' ),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', '%', 'em' ],
                    'selectors'     => [
                        '{{WRAPPER}} .htslider-postslider-area .slick-dots li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'after',
                ]
            );
            $this->start_controls_tabs('pagination_style_tabs',[
                'separator' => 'before',
            ]);

                $this->start_controls_tab(
                    'pagination_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'ht-slider' ),
                    ]
                );
                $this->add_control(
                    'dot_color_index',
                    [
                        'label' => __( 'Index Color', 'ht-slider' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .htslider-dot-index-yes .slick-dots li button' => 'color: {{VALUE}};',
                        ],
                        'condition' =>[
                            'slpagination'=>'yes',
                        ],
                    ]
                );
                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name'      => 'pagination_background',
                            'label'     => esc_html__( 'Background', 'ht-slider' ),
                            'types'     => [ 'classic', 'gradient' ],
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area ul.slick-dots li button',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name'      => 'pagination_border',
                            'label'     => esc_html__( 'Border', 'ht-slider' ),
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area ul.slick-dots li button',
                        ]
                    );

                    $this->add_responsive_control(
                        'pagination_border_radius',
                        [
                            'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type'      => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-postslider-area ul.slick-dots li button' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );
                    $this->add_responsive_control(
                        'htslider_carousel_dots_height',
                        [
                            'label' => __( 'Height', 'ht-slider' ),
                            'type' => Controls_Manager::SLIDER,
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 200,
                                    'step' => 1,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htslider-carousel-activation .slick-dots li button' => 'height: {{SIZE}}px;',
                            ],
                        ]
                    );
        
                    $this->add_responsive_control(
                        'htslider_carousel_dots_width',
                        [
                            'label' => __( 'Width', 'ht-slider' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px'],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 200,
                                    'step' => 1,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htslider-carousel-activation .slick-dots li button' => 'width: {{SIZE}}px !important;',
                            ],
                        ]
                    );
                $this->end_controls_tab(); // Normal Tab end

                $this->start_controls_tab(
                    'pagination_style_active_tab',
                    [
                        'label' => esc_html__( 'Active', 'ht-slider' ),
                    ]
                );
                $this->add_control(
                    'dot_color_hover',
                    [
                        'label' => __( 'Index Color', 'ht-slider' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .htslider-dot-index-yes .slick-dots li.slick-active button' => 'color: {{VALUE}};',
                        ],
                        'condition' =>[
                            'slpagination'=>'yes',
                        ],
                    ]
                );
                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name'      => 'pagination_hover_background',
                            'label'     => esc_html__( 'Background', 'ht-slider' ),
                            'types'     => [ 'classic', 'gradient' ],
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area ul.slick-dots li button:hover, {{WRAPPER}} .htslider-postslider-area .slick-dots li.slick-active button',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name'      => 'pagination_hover_border',
                            'label'     => esc_html__( 'Border', 'ht-slider' ),
                            'selector'  => '{{WRAPPER}} .htslider-postslider-area ul.slick-dots li button:hover, {{WRAPPER}} .htslider-postslider-area .slick-dots li.slick-active button',
                        ]
                    );

                    $this->add_responsive_control(
                        'pagination_hover_border_radius',
                        [
                            'label'     => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type'      => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-postslider-area .slick-dots li.slick-active button' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                                '{{WRAPPER}} .htslider-postslider-area .slick-dots li:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );
                    $this->add_responsive_control(
                        'htslider_carousel_dots_height_active',
                        [
                            'label' => __( 'Height', 'ht-slider' ),
                            'type' => Controls_Manager::SLIDER,
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 200,
                                    'step' => 1,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => '',
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htslider-carousel-activation .slick-dots li.slick-active button' => 'height: {{SIZE}}px;',
                            ],
                        ]
                    );
        
                    $this->add_responsive_control(
                        'htslider_carousel_dots_width_active',
                        [
                            'label' => __( 'Width', 'ht-slider' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px'],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 200,
                                    'step' => 1,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => '',
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htslider-carousel-activation .slick-dots li.slick-active button' => 'width: {{SIZE}}px !important;',
                            ],
                        ]
                    );
                $this->end_controls_tab(); // Hover Tab end

            $this->end_controls_tabs();

        $this->end_controls_section();


    }

    protected function render( $instance = [] ) {

        $settings   = $this->get_settings_for_display(); 

        $id = $this->get_id();
        $args = array(
            'post_type'             => 'htslider_slider',
            'posts_per_page'        => $settings['slider_limit'],
            'post_status'           => 'publish',
            'order'                 => 'ASC',
        );

        // Fetch By id
        if( $settings['slider_show_by'] == 'show_byid' ){
            $args['post__in'] = $settings['slider_id'];
        }

        // Fetch by category
        if( $settings['slider_show_by'] == 'show_bycat' ){
            // By Category
            $get_slider_categories = $settings['slider_cat'];
            $slider_cats = str_replace(' ', '', $get_slider_categories);
            if ( "0" != $get_slider_categories) {
                if( is_array( $slider_cats ) && count( $slider_cats ) > 0 ){
                    $field_name = is_numeric( $slider_cats[0] )?'term_id':'slug';
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'htslider_category',
                            'terms' => $slider_cats,
                            'field' => $field_name,
                            'include_children' => false
                        )
                    );
                }
            }
        }
        $sliders = new \WP_Query( $args );

        if ( $settings['slpagination']=='yes' ) {
            $this->add_render_attribute( 'htslider_post_slider_attr', 'class', 'htslider-postslider-area htslider-area-pro htslider-postslider-style-1 pagination htslider-dot-index-yes' );
        }else{
            $this->add_render_attribute( 'htslider_post_slider_attr', 'class', 'htslider-postslider-area htslider-area-pro htslider-postslider-style-1' );
        }

        $this->add_render_attribute( 'htslider_post_slider_item_attr', 'class', 'htslider-data-title htslider-single-post-slide htslider-postslider-layout-1' );

        // Slider options
        if( $settings['slider_on'] == 'yes' ){

            $this->add_render_attribute( 'htslider_post_slider_attr', 'class', 'htslider-carousel-activation htslider-arrow-'.$settings['post_slider_arrow_style'] .' '.$settings['dot_show_position'] );

            $slider_settings = [
                'arrows' => ('yes' === $settings['slarrows']),
                'arrow_prev_txt' => HTSliders_Icons_managers::render_icon( $settings['slprevicon'], [ 'aria-hidden' => 'true' ] ),
                'arrow_next_txt' => HTSliders_Icons_managers::render_icon( $settings['slnexticon'], [ 'aria-hidden' => 'true' ] ),
                'dots' => ('yes' === $settings['sldots']),
                'autoplay' => ('yes' === $settings['slautolay']),
                'autoplay_speed' => absint($settings['slautoplay_speed']),
                'animation_speed' => absint($settings['slanimation_speed']),
                'pause_on_hover' => ('yes' === $settings['slpause_on_hover']),
                'center_mode' => ( 'yes' === $settings['slcentermode']),
                'center_padding' => absint($settings['slcenterpadding']),
                //'carousel_style_ck' => absint( $settings['post_slider_layout'] ),
            ];

            $slider_responsive_settings = [
                'display_columns' => $settings['slitems'],
                'scroll_columns' => $settings['slscroll_columns'],
                'tablet_width' => $settings['sltablet_width'],
                'tablet_display_columns' => $settings['sltablet_display_columns'],
                'tablet_scroll_columns' => $settings['sltablet_scroll_columns'],
                'mobile_width' => $settings['slmobile_width'],
                'mobile_display_columns' => $settings['slmobile_display_columns'],
                'mobile_scroll_columns' => $settings['slmobile_scroll_columns'],

            ];

            $slider_settings = array_merge( $slider_settings, $slider_responsive_settings );

            $this->add_render_attribute( 'htslider_post_slider_attr', 'data-settings', wp_json_encode( $slider_settings ) );
        }

        $sliderpost_ids = array();
        while( $sliders->have_posts() ):$sliders->the_post();
            $sliderpost_ids[] = get_the_ID();
        endwhile;
        wp_reset_postdata(); wp_reset_query();


        $html_title_tag = $settings['title_html_tag'];
        $html_subtitle_tag = $settings['subtitle_html_tag'];

        $s_display_none = ' style="display:none;"';
    ?>

        <div <?php echo $this->get_render_attribute_string( 'htslider_post_slider_attr' ) . esc_attr($s_display_none); ?>>
            <?php if( $settings['content_sourse']=='1'): 

                foreach ($settings['sliders_list'] as $item ): 

                    $dynamic_button_link = 'unique'.$item['_id'];
                    if ( ! empty( $item['button_link']['url'] ) ) {
                        $this->add_link_attributes( $dynamic_button_link, $item['button_link'] );
                    }
                ?>

                    <div class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> htslider-item-img single-slide-item htslider-single-post-slide">
                        <div class="htb-container">
                            <div class="content">
                                <div class="post-inner">
                                    <?php 
                                        if( $item['subtitle']  ){
                                            printf('<%s class="htslider-subtitle">%s</%s>', tag_escape($html_subtitle_tag), wp_kses_post( $item['subtitle'] ), tag_escape($html_subtitle_tag) );
                                        }
                                        if(  $item['title']  ){
                                            printf('<%s class="htslider-title">%s</%s>', tag_escape($html_title_tag), wp_kses_post( $item['title'] ), tag_escape($html_title_tag) );
                                        }
                                        if(  $item['desc'] ){
                                            printf('<div class="htslider-desc">%s</div>', wp_kses_post( $item['desc'] ) );
                                        }
                                        
                                        if( !empty($item['button_text']) ){
                                        echo '<div class="post-btn"><a ' .$this->get_render_attribute_string( $dynamic_button_link ).' class="readmore-btn" >' . wp_kses_post( $item['button_text'] ) .'</a></div>';
                                        } 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach; ?>
            <?php else: ?>

            <?php foreach( $sliderpost_ids as $slider_item ): ?>
               <div <?php echo $this->get_render_attribute_string( 'htslider_post_slider_item_attr' ); ?> >
                    <?php
                    echo htslider_render_build_content($slider_item);
                    ?>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }

}
