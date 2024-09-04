<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTSlider_Elementor_Widget_Post_Slider extends Widget_Base {

    public function get_name() {
        return 'htslider-postslider-addons';
    }
    
    public function get_title() {
        return esc_html__( 'HT: Posts Slider', 'ht-slider' );
    }

    public function get_icon() {
        return 'eicon-post-slider';
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
            'post_slider_content',
            [
                'label' => esc_html__( 'Post Slider', 'ht-slider' ),
            ]
        );

            $this->add_control(
                'post_slider_layout',
                [
                    'label'   => esc_html__( 'Layout', 'ht-slider' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => '1',
                    'options' => [
                        '1'   => esc_html__( 'Layout One', 'ht-slider' ),
                        '2'   => esc_html__( 'Layout Two (Pro)', 'ht-slider' ),
                        '3'   => esc_html__( 'Layout Three (Pro)', 'ht-slider' ),
                        '4'   => esc_html__( 'Layout Four (Pro)', 'ht-slider' ),
                        '5'   => esc_html__( 'Layout Five (Pro)', 'ht-slider' ),
                    ],
                ]
            );
            htslider_pro_notice( $this,'post_slider_layout', ['2','3','4','5'], Controls_Manager::RAW_HTML );
            $this->add_control(
                'title_html_tag',
                [
                    'label'   => esc_html__( 'Title HTML Tag', 'ht-slider' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'h2',
                    'options' => htslider_html_tag_lists(),
                ]
            );
            
        $this->end_controls_section();

        // Post Option Start
        $this->start_controls_section(
            'post_content_option',
            [
                'label' => esc_html__( 'Post Option', 'ht-slider' ),
            ]
        );
            
        $this->show_post_source();

            $this->add_control(
                'show_title',
                [
                    'label'         => esc_html__( 'Title', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'separator'     =>'before',
                ]
            );

            $this->add_control(
                'title_length',
                [
                    'label'          => esc_html__( 'Title Length', 'ht-slider' ),
                    'type'           => Controls_Manager::NUMBER,
                    'step'           => 1,
                    'default'        => 5,
                    'condition'      =>[
                        'show_title' =>'yes',
                    ]
                ]
            );

            $this->add_control(
                'show_content',
                [
                    'label'         => esc_html__( 'Content', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );
            $this->add_control(
                'content_type',
                [
                    'label' => esc_html__( 'Content Source', 'ht-slider' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'content',
                    'options' => [
                        'content'          => esc_html__('Content','ht-slider'),
                        'excerpt'            => esc_html__('Excerpt','ht-slider'),
                    ],
                    'condition'=>[
                        'show_content'=>'yes',
                    ]
                ]
            );
            $this->add_control(
                'content_length',
                [
                    'label'             => esc_html__( 'Content Length', 'ht-slider' ),
                    'type'              => Controls_Manager::NUMBER,
                    'step'              => 1,
                    'default'           => 20,
                    'condition'         =>[
                        'show_content'  =>'yes',
                    ]
                ]
            );

            $this->add_control(
                'show_read_more_btn',
                [
                    'label'         => esc_html__( 'Read More Button', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'read_more_txt',
                [
                    'label'                 => esc_html__( 'Button Text', 'ht-slider' ),
                    'type'                  => Controls_Manager::TEXT,
                    'default'               => esc_html__( 'Read More', 'ht-slider' ),
                    'placeholder'           => esc_html__( 'Read More', 'ht-slider' ),
                    'condition'             =>[
                        'show_read_more_btn'=>'yes',
                    ]
                ]
            );

            $this->add_control(
                'show_category',
                [
                    'label'         => esc_html__( 'Category', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'show_author',
                [
                    'label'         => esc_html__( 'Author', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'show_date',
                [
                    'label'         => esc_html__( 'Date', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );
            $this->add_control(
                'hide_empty_thumbnail_post',
                [
                    'label'         => esc_html__( 'Hide Empty Thumbnail', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'separator'     =>'after',
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

        $this->end_controls_section(); // Content Option End

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
                    'label'         => esc_html__( 'Previous icon', 'ht-slider' ),
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
                    'label'         => esc_html__( 'Next icon', 'ht-slider' ),
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
                    'label'         => esc_html__( 'Slider dots', 'ht-slider' ),
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
                        'sldots'    => 'yes',
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
                    'type'              => Controls_Manager::SWITCHER,
                    'label_off'         => esc_html__('No', 'ht-slider'),
                    'label_on'          => esc_html__('Yes', 'ht-slider'),
                    'return_value'      => 'yes',
                    'default'           => 'yes',
                    'label'             => esc_html__('Pause on Hover?', 'ht-slider'),
                    'condition'         => [
                        'slider_on'     => 'yes',
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
                    'label'         => esc_html__( 'Slider Auto Play', 'ht-slider' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'return_value'  => 'yes',
                    'separator'     => 'before',
                    'default'       => 'no',
                    'condition'     => [
                        'slider_on' => 'yes',
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
                    'label'         => esc_html__('Slide Animation Speed', 'ht-slider'),
                    'type'          => Controls_Manager::NUMBER,
                    'default'       => 300,
                    'condition'     => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

        $this->end_controls_section(); // Slider Option end

       // Style Slide Image
       $this->start_controls_section(
        'post_slider_image_style_section',
        [
            'label' => __( 'Thumbnail ', 'ht-slider' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [  'name' => 'htslider_post_image',
                'default' => 'htslider_size_1170x536',
                'exclude' => ['custom'],
                'separator' => 'before',
            ]
        ); 
         

        $this->add_control(
            'post_slider_image_overlay_heading',
            [
                'label' => __( 'Thumbnail Overlay', 'ht-slider' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'post_slider_image_overlay',
                'label' => __( 'Background', 'ht-slider' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .htslider-single-post-slide .thumb a:after',
                'exclude' => ['image'],
                'separator' => 'after',
            ]
        );            
                     
    $this->end_controls_section();
     //Slider Image Style end
    // Style Content Box section
        $this->start_controls_section(
            'post_slider_content_area_style_section',
            [
                'label'     => esc_html__( 'Content Box', 'ht-slider' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'post_slider_layout'=> array( '1', '4'),
                ]
            ]
        );
        $this->add_control(
            'content_area',
            [
                'label'     => esc_html__( 'Background', 'ht-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .htslider-postslider-layout-1 .content .post-inner' => 'background: {{VALUE}}',
                    '{{WRAPPER}} .htslider-postslider-layout-4 .content .post-inner' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'content1_position',
            [
                'label'     => esc_html__( 'Content Position', 'ht-slider' ),
                'type'      => Controls_Manager::SLIDER,
                'condition' => [
                'post_slider_layout'=> '1',
                ],
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'size'   => 0,
                    'unit'   => '%',
                ],
                'range'  => [
                    'px' => [
                        'min' => 0,
                        'max' => 670,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                     '{{WRAPPER}} .htslider-postslider-layout-1 .content' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

          //contetn postition
        $this->add_control(
            'content4_position',
            [
                'label'     => esc_html__( 'Content Position', 'ht-slider' ),
                'type'      => Controls_Manager::POPOVER_TOGGLE,
                'condition' =>[
                    'post_slider_layout'=> '4',
                ]
            ]
        );
        $this->start_popover();
            $this->add_responsive_control(
                'content4_x_position',
                [
                    'label' => esc_html__( 'Horizontal Position', 'ht-slider' ),
                    'type'  => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'default'  => [
                        'size' => 0,
                        'unit' => '%',
                    ],
                    'range'  => [
                        'px' => [
                            'min' => 0,
                            'max' => 450,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 40,
                        ],
                    ],
                    'selectors' => [
                         '{{WRAPPER}} .htslider-postslider-layout-4 .content' => 'left: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'content4_y_position',
                [
                    'label' => esc_html__( 'Vertical Position', 'ht-slider' ),
                    'type'  => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'default'  => [
                        'size' => 0,
                        'unit' => '%',
                    ],
                    'range'   => [
                         'px' => [
                            'min' => 0,
                            'max' => 240,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-postslider-layout-4 .content' => 'bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_popover();
        
        $this->add_responsive_control(
            'post_slider_content_box_padding',
            [
                'label' => __( 'Padding', 'ht-slider' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .htslider-postslider-layout-1 .content .post-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'post_slider_content_box_border',
                'label' => __( 'Border', 'ht-slider' ),
                'selector' => '{{WRAPPER}} .htslider-postslider-layout-1 .content .post-inner',
            ]
        );
        $this->add_responsive_control(
            'post_slider_content_box_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'ht-slider' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .htslider-postslider-layout-1 .content .post-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'slider_content_width',
            [
                'label' => __( 'Box Width', 'ht-slider' ),
                'type' => Controls_Manager::SLIDER,
                'description' =>'Custom Box Max Width(%)',
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .htslider-postslider-layout-1 .content,{{WRAPPER}} .htslider-postslider-layout-4 .content' => 'max-Width: {{SIZE}}%; Width: {{SIZE}}%;',
                ],
                'condition'=>[
                    'post_slider_layout' =>['1','4'], 
                ]
            ]
        );

        $this->add_responsive_control(
            'post_slider_content_box_align',
            [
                'label' => __( 'Alignment', 'ht-slider' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'ht-slider' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'ht-slider' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'ht-slider' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .htslider-single-post-slide .content .post-inner,
                    {{WRAPPER}} .htslider-postslider-layout-5 .content .post-inner' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category,{{WRAPPER}} .htslider-single-post-slide ul.meta' => 'justify-content: {{VALUE}};',
                ],
            ]
        );
    $this->end_controls_section(); //Content Box Option end

        // Style Title tab section
        $this->start_controls_section(
            'post_slider_title_style_section',
            [
                'label'         => esc_html__( 'Title', 'ht-slider' ),
                'tab'           => Controls_Manager::TAB_STYLE,
                'condition'     =>[
                    'show_title'=>'yes',
                ]
            ]
        );
            $this->add_control(
                'title_color',
                [
                    'label'     => esc_html__( 'Color', 'ht-slider' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   =>'#18012c',
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title a' => 'color: {{VALUE}}',
                    ],
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

            $this->add_responsive_control(
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

            $this->add_responsive_control(
                'title_padding',
                [
                    'label'         => esc_html__( 'Padding', 'ht-slider' ),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', '%', 'em' ],
                    'selectors'     => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'title_align',
                [
                    'label'     => esc_html__( 'Alignment', 'ht-slider' ),
                    'type'      => Controls_Manager::CHOOSE,
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
                        'justify' => [
                            'title' => esc_html__( 'Justified', 'ht-slider' ),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner .htslider-title' => 'text-align: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();

        // Style Content tab section
        $this->start_controls_section(
            'post_slider_content_style_section',
            [
                'label' => esc_html__( 'Content', 'ht-slider' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'show_content'=>'yes',
                ]
            ]
        );
            $this->add_control(
                'content_color',
                [
                    'label'         => esc_html__( 'Color', 'ht-slider' ),
                    'type'          => Controls_Manager::COLOR,
                    'default'       =>'#18012c',
                    'selectors'     => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner p' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'      => 'content_typography',
                    'label'     => esc_html__( 'Typography', 'ht-slider' ),
                    'selector'  => '{{WRAPPER}} .htslider-single-post-slide .content .post-inner p',
                ]
            );

            $this->add_responsive_control(
                'content_margin',
                [
                    'label'         => esc_html__( 'Margin', 'ht-slider' ),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', '%', 'em' ],
                    'selectors'     => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'content_padding',
                [
                    'label'         => esc_html__( 'Padding', 'ht-slider' ),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', '%', 'em' ],
                    'selectors'     => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'content_align',
                [
                    'label'     => esc_html__( 'Alignment', 'ht-slider' ),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => [
                        'left'  => [
                            'title' => esc_html__( 'Left', 'ht-slider' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'ht-slider' ),
                            'icon'  => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'ht-slider' ),
                            'icon'  => 'eicon-text-align-right',
                        ],
                        'justify' => [
                            'title' => esc_html__( 'Justified', 'ht-slider' ),
                            'icon'  => 'eicon-text-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner p' => 'text-align: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();

        // Style Category tab section
        $this->start_controls_section(
            'post_slider_category_style_section',
            [
                'label'             => esc_html__( 'Category', 'ht-slider' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         =>[
                    'show_category' =>'yes',
                ]
            ]
        );
            
            $this->start_controls_tabs('category_style_tabs');

                $this->start_controls_tab(
                    'category_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'ht-slider' ),
                    ]
                );

                    $this->add_control(
                        'category_color',
                        [
                            'label'     => esc_html__( 'Color', 'ht-slider' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   =>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li a' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Typography::get_type(),
                        [
                            'name'      => 'category_typography',
                            'label'     => esc_html__( 'Typography', 'ht-slider' ),
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li a',
                        ]
                    );

                    $this->add_responsive_control(
                        'category_margin',
                        [
                            'label' => esc_html__( 'Margin', 'ht-slider' ),
                            'type'  => Controls_Manager::DIMENSIONS,
                            'size_units'    => [ 'px', '%', 'em' ],
                            'selectors'     => [
                                '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'category_padding',
                        [
                            'label'         => __( 'Padding', 'ht-slider' ),
                            'type'          => Controls_Manager::DIMENSIONS,
                            'size_units'    => [ 'px', '%', 'em' ],
                            'selectors'     => [
                                '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name'      => 'category_background',
                            'label'     => __( 'Background', 'ht-slider' ),
                            'types'     => [ 'classic', 'gradient' ],
                            'selector'  => '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li a',
                        ]
                    );

                $this->end_controls_tab(); // Normal Tab end

                $this->start_controls_tab(
                    'category_style_hover_tab',
                    [
                        'label' => esc_html__( 'Hover', 'ht-slider' ),
                    ]
                );
                    $this->add_control(
                        'category_hover_color',
                        [
                            'label' => esc_html__( 'Color', 'ht-slider' ),
                            'type' => Controls_Manager::COLOR,
                            'default'=>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li a:hover' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'category_hover_background',
                            'label' => esc_html__( 'Background', 'ht-slider' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .content ul.post-category li a:hover',
                        ]
                    );

                $this->end_controls_tab(); // Hover Tab end

            $this->end_controls_tabs();

        $this->end_controls_section();

        // Style Meta tab section
        $this->start_controls_section(
            'post_meta_style_section',
            [
                'label' => esc_html__( 'Meta', 'ht-slider' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'meta_color',
                [
                    'label' => esc_html__( 'Color', 'ht-slider' ),
                    'type' => Controls_Manager::COLOR,
                    'default'=>'#18012c',
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide ul.meta' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .htslider-single-post-slide .content .post-inner ul.meta li a' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .htslider-postslider-layout-3 .content .post-inner ul.meta li' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .htslider-postslider-layout-4 .content .post-inner ul.meta li' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'meta_typography',
                    'label' => esc_html__( 'Typography', 'ht-slider' ),
                    'selector' => '{{WRAPPER}} .htslider-single-post-slide ul.meta',
                ]
            );

            $this->add_responsive_control(
                'meta_margin',
                [
                    'label' => esc_html__( 'Margin', 'ht-slider' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide ul.meta li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'meta_padding',
                [
                    'label' => esc_html__( 'Padding', 'ht-slider' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide ul.meta li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'meta_align',
                [
                    'label' => esc_html__( 'Alignment', 'ht-slider' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'start' => [
                            'title' => esc_html__( 'Left', 'ht-slider' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'ht-slider' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'end' => [
                            'title' => esc_html__( 'Right', 'ht-slider' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                        'justify' => [
                            'title' => esc_html__( 'Justified', 'ht-slider' ),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htslider-single-post-slide ul.meta' => 'justify-content: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();

        // Style Read More button tab section
        $this->start_controls_section(
            'post_slider_readmore_style_section',
            [
                'label' => esc_html__( 'Read More', 'ht-slider' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'show_read_more_btn'=>'yes',
                    'read_more_txt!'=>'',
                ]
            ]
        );
            
            $this->start_controls_tabs('readmore_style_tabs');

                $this->start_controls_tab(
                    'readmore_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'ht-slider' ),
                    ]
                );

                    $this->add_control(
                        'readmore_color',
                        [
                            'label' => esc_html__( 'Color', 'ht-slider' ),
                            'type' => Controls_Manager::COLOR,
                            'default'=>'#464545',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Typography::get_type(),
                        [
                            'name' => 'readmore_typography',
                            'label' => esc_html__( 'Typography', 'ht-slider' ),
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn',
                        ]
                    );

                    $this->add_responsive_control(
                        'readmore_margin',
                        [
                            'label' => esc_html__( 'Margin', 'ht-slider' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'readmore_padding',
                        [
                            'label' => esc_html__( 'Padding', 'ht-slider' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'readmore_background',
                            'label' => esc_html__( 'Background', 'ht-slider' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'readmore_border',
                            'label' => esc_html__( 'Border', 'ht-slider' ),
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn',
                        ]
                    );

                    $this->add_responsive_control(
                        'readmore_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Normal Tab end

                $this->start_controls_tab(
                    'readmore_style_hover_tab',
                    [
                        'label' => esc_html__( 'Hover', 'ht-slider' ),
                    ]
                );
                    $this->add_control(
                        'readmore_hover_color',
                        [
                            'label' => esc_html__( 'Color', 'ht-slider' ),
                            'type' => Controls_Manager::COLOR,
                            'default'=>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn:hover' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'readmore_hover_background',
                            'label' => esc_html__( 'Background', 'ht-slider' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn:hover',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'readmore_hover_border',
                            'label' => esc_html__( 'Border', 'ht-slider' ),
                            'selector' => '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'readmore_hover_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'ht-slider' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htslider-single-post-slide .post-btn a.readmore-btn:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Hover Tab end

            $this->end_controls_tabs();

        $this->end_controls_section();

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
                'label' => esc_html__( 'Pagination', 'ht-slider' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'slider_on' => 'yes',
                    'sldots'=>'yes',
                    'post_slider_layout!'=>'4',
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
        $orderby            = $this->get_settings_for_display('orderby');
        $postorder          = $this->get_settings_for_display('postorder');
        $post_type = $settings['slider_post_type'];
        $settings['post_slider_layout'] = '1';
        $htslider_post_image  =  $this->get_settings_for_display('htslider_post_image_size');

        if( 'post'== $post_type ){
            $post_categorys = $settings['slider_categories'];
        } else if( 'htslider_slider'== $post_type ){
            $post_categorys = $settings[ $post_type.'_post_category'];
        }else {
            $post_type = 'post';
            $post_categorys = $settings['slider_categories'];
        }
        $post_author = $settings['post_author'];
        $exclude_posts = $settings['exclude_posts'];


        $s_display_none = ' style="display:none;"';
        if ( $settings['slpagination']=='yes' ) {

        $this->add_render_attribute( 'htslider_post_slider_attr', 'class', 'htslider-postslider-area pagination  htslider-dot-index-yes htslider-postslider-style-1' );
        }else{
            $this->add_render_attribute( 'htslider_post_slider_attr', 'class', 'htslider-postslider-area htslider-postslider-style-1' );
        }

        $this->add_render_attribute( 'htslider_post_slider_item_attr', 'class', 'htslider-data-title htslider-single-post-slide htslider-postslider-layout-1' );

        // Slider options
        if( $settings['slider_on'] == 'yes' ){

            $this->add_render_attribute( 'htslider_post_slider_attr', 'class', 'htslider-carousel-activation htslider-arrow-'.$settings['post_slider_arrow_style'] .' '.$settings['dot_show_position']  );

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
                'carousel_style_ck' => absint( $settings['post_slider_layout'] ),
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

        // Query
        // Post query
        $args = array(
            'post_type'             => $post_type,
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => !empty( $settings['post_limit'] ) ? (int)$settings['post_limit'] : 3,
        );

        if (  !empty( $post_categorys ) ) {

            $category_name =  get_object_taxonomies($post_type);
            if( $category_name['0'] == "product_type" ){
                    $category_name['0'] = 'product_cat';
            }

            if( is_array($post_categorys) && count($post_categorys) > 0 ){

                $field_name = is_numeric( $post_categorys[0] ) ? 'term_id' : 'slug';
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $category_name[0],
                        'terms' => $post_categorys,
                        'field' => $field_name,
                        'include_children' => false
                    )
                );
            }
        }
        // author check
        if (  !empty( $post_author ) ) {
            $args['author__in'] = $post_author;
        }
        // order by  check
        if ( !empty( $orderby ) ) {
            if ( 'date' == $orderby && 'yes'== $settings['custom_order_by_date'] && (!empty( $settings['order_by_date_after'] || $settings['order_by_date_before'] ) ) ) {
            $order_by_date_after = strtotime($settings['order_by_date_after']);
            $order_by_date_before = strtotime($settings['order_by_date_before']);
                $args['date_query'] = array(
                    array(
                        'before'    => array(
                            'year'  => gmdate('Y', $order_by_date_before),
                            'month' =>gmdate('m', $order_by_date_before),
                            'day'   => gmdate('d', $order_by_date_before),
                        ),
                        'after'    => array(
                            'year'  => gmdate('Y', $order_by_date_after),
                            'month' =>gmdate('m', $order_by_date_after),
                            'day'   => gmdate('d', $order_by_date_after),
                        ),
                        'inclusive' => true,
                    ),
                );

            } else {
                $args['orderby'] = $orderby;
            }
        }

        // Exclude posts check
        if (  !empty( $exclude_posts ) ) {
            $exclude_posts = explode(',',$exclude_posts);
            $args['post__not_in'] =  $exclude_posts;
        }

        // Order check
        if (  !empty( $postorder ) ) {
            $args['order'] =  $postorder;
        }

        // empty thumbnail post
        if ( 'yes' === $settings['hide_empty_thumbnail_post'] ) {
            $args['meta_query'] = [
                [
                    'key' => '_thumbnail_id',
                    'compare' => 'EXISTS'
                ],
            ];
        }
        
        $slider_post = new \WP_Query( $args );

        ?>
            <div <?php echo $this->get_render_attribute_string( 'htslider_post_slider_attr' ) . esc_attr($s_display_none); ?>>

                <?php
                if( $slider_post->have_posts() ):
                    while( $slider_post->have_posts() ): $slider_post->the_post();
                ?>
                    <div <?php echo $this->get_render_attribute_string( 'htslider_post_slider_item_attr' ); ?> data-title="<?php echo esc_attr(wp_trim_words( get_the_title(), 10, '' )); ?>">

                        <div class="thumb">
                            <a href="<?php the_permalink();?>"><?php $this->htslider_render_loop_thumbnail( $htslider_post_image ); ?></a>
                        </div>
                        <?php $this->htslider_render_loop_content( $slider_post->ID ); ?>

                    </div>

                <?php endwhile; wp_reset_postdata(); wp_reset_query(); 
            else:
                echo "<div class='htslider-error-notice'>".esc_html__('There are no posts in this query','ht-slider')."</div>";
                    
            endif; ?>

            </div>

        <?php

    }

    // Loop Content
    public function htslider_render_loop_content( $post_id = null ){
        $settings   = $this->get_settings_for_display();
        $category_name =  get_object_taxonomies( $settings['slider_post_type'] );

         $html_title_tag = $settings['title_html_tag'];
        ?>
            <div class="content">
                <div class="post-inner">
                    <?php if( $settings['show_category'] == 'yes' ): ?>
                        <ul class="post-category">
                            <?php

                                if( $category_name ){
                                    $get_terms = get_the_terms($post_id, $category_name[0] );
                                    if( $settings['slider_post_type'] == 'product' ){
                                        $get_terms = get_the_terms($post_id, 'product_cat');
                                    }
                                    if( $get_terms ){
                                        foreach ( $get_terms as $category ) {
                                            $term_link = get_term_link( $category );
                                            ?>
                                            <li><a href="<?php echo esc_url( $term_link ); ?>" class="category <?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
                                            <?php
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    <?php endif;?>
                    <?php if( $settings['show_title'] == 'yes' ): 

                        printf('<%s class="htslider-title"><a href="%s">%s</a></%s>',tag_escape($html_title_tag),esc_url(get_the_permalink()),wp_kses_post(wp_trim_words( get_the_title(), $settings['title_length'], '' )),tag_escape($html_title_tag)); 

                        endif; if( $settings['show_author'] == 'yes' || $settings['show_date'] == 'yes'): ?>

                        <ul class="meta">
                            <?php if( $settings['show_author'] == 'yes' ): ?>
                                <li><i class="fa fa-user-circle"></i><a href="<?php echo esc_url(get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) )); ?>"><?php the_author();?></a></li>
                            <?php endif; if( $settings['show_date'] == 'yes' ):?>
                                <li><i class="fa fa-clock"></i><?php the_time(esc_html__('d F Y','ht-slider'));?></li>
                            <?php endif; ?>
                        </ul>
                    <?php endif;?>
                    <?php
                        if ( $settings['show_content'] == 'yes' ) {

                            if ( $settings['content_type'] == 'excerpt' ) {
                                echo '<p>'. esc_html( wp_trim_words( get_the_excerpt(), floatval( $settings['content_length'] ),'' ) ) .'</p>';
                            } else {
                                echo '<p>'. wp_kses_post( wp_trim_words( strip_shortcodes( get_the_content() ), floatval( $settings['content_length'] ), '' ) ).'</p>'; 
                            }
                        }
                        if( $settings['show_read_more_btn'] == 'yes' ):
                    ?>
                        <div class="post-btn">
                            <a class="readmore-btn" href="<?php the_permalink();?>"><?php echo esc_html( $settings['read_more_txt'] );?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php
    }

    // Loop Thumbnails
    public function htslider_render_loop_thumbnail( $thumbnails_size = 'full' ) {
        if ( has_post_thumbnail() ){
            the_post_thumbnail( $thumbnails_size );
        }else{
            echo '<img src="'.esc_url(HTSLIDER_PL_URL.'/assets/images/image-placeholder.png').'" alt="'.esc_attr(get_the_title()).'" />';
        }

    }
    // post query fields
    public function show_post_source(){

        $this->add_control(
            'slider_post_type',
            [
                'label' => esc_html__( 'Post Type', 'ht-slider' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => htslider_get_post_types( [], ['Posts','Slider'] ),
                'default' =>'post',
                'frontend_available' => true,
                'description' => esc_html__( 'Upgrade to the pro version to  display any custom post type content with WooCommerce Products.', 'ht-slider' ),
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'update_pro_slider_post_type',
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
                'condition' => [
                    'slider_post_type!' => ['post','htslider_slider'],
                ]
            ]
        );

        $this->add_control(
            'include_by',
            [
                'label' => __( 'Include By', 'ht-slider' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'default' =>'in_category',
                'options' => [
                    'in_author'      => __( 'Author', 'ht-slider' ),
                    'in_category'      => __( 'Category', 'ht-slider' ),
                ],
            ]
        );
        $this->add_control(
            'post_author',
            [
                'label' => esc_html__( 'Authors', 'ht-slider' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'options' => htslider_get_authors_list(),
                'condition' =>[
                    'include_by' => 'in_author',
                ]
            ]
        );
        $all_post_type = htslider_get_post_types();
        foreach( $all_post_type as $post_key => $post_item ) {
            
            if( 'post' == $post_key ){
                $this->add_control(
                    'slider_categories',
                    [
                        'label' => esc_html__( 'Categories', 'ht-slider' ),
                        'type' => Controls_Manager::SELECT2,
                        'label_block' => true,
                        'multiple' => true,
                        'options' => htslider_get_taxonomies(),
                        'condition' =>[
                            'slider_post_type' => 'post',
                            'include_by' => 'in_category',
                        ]
                    ]
                );
            } else if ( 'product' == $post_key ) {
                $this->add_control(
                    'slider_prod_categories',
                    [
                        'label' => esc_html__( 'Categories', 'ht-slider' ),
                        'type' => Controls_Manager::SELECT2,
                        'label_block' => true,
                        'multiple' => true,
                        'options' => htslider_get_taxonomies('product_cat'),
                        'condition' =>[
                            'slider_post_type' => 'product',
                            'include_by' => 'in_category',
                        ]
                    ]
                );

            } else {
                $this->add_control(
                    "{$post_key}_post_category",
                    [
                        'label' => esc_html__( 'Select Categories', 'ht-slider' ),
                        'type' => Controls_Manager::SELECT2,
                        'label_block' => true,
                        'multiple' => true,
                        'options' => htslider_category_list_using_taxonomie($post_key),
                        'condition' => [
                            'slider_post_type' => $post_key,
                            'include_by' => 'in_category',
                        ],
                    ]
                );
            }

        }

        $this->add_control(
            "exclude_posts",
            [
                'label' => esc_html__( 'Exclude Posts', 'ht-slider' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => esc_html__( 'Example: 10,11,105', 'ht-slider' ),
                'description' => esc_html__( "To Exclude Post, Enter  the post id separated by ','", 'ht-slider' ),
            ]
        );
        $this->add_control(
            'post_limit',
            [
                'label' => __('Limit', 'ht-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5,
                'separator'=>'before',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__( 'Order By', 'ht-slider' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'ID'            => esc_html__('ID','ht-slider'),
                    'date'          => esc_html__('Date','ht-slider'),
                    'name'          => esc_html__('Name','ht-slider'),
                    'title'         => esc_html__('Title','ht-slider'),
                    'comment_count' => esc_html__('Comment count','ht-slider'),
                    'rand'          => esc_html__('Random','ht-slider'),
                ],
            ]
        );
        $this->add_control(
            'custom_order_by_date',
            [
                'label' => esc_html__( 'Custom Date', 'ht-slider' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'no',
                'condition' =>[
                    'orderby'=>'date'
                ]
            ]
        );
        $this->add_control(
            'order_by_date_before',
            [
                'label' => __( 'Before Date', 'ht-slider' ),
                'type' => Controls_Manager::DATE_TIME,
                'condition' =>[
                    'orderby'=>'date',
                    'custom_order_by_date'=>'yes',
                ]
            ]
        );
        $this->add_control(
            'order_by_date_after',
            [
                'label' => __( 'After Date', 'ht-slider' ),
                'type' => Controls_Manager::DATE_TIME,
                'condition' =>[
                    'orderby'=>'date',
                    'custom_order_by_date'=>'yes',
                ]
            ]
        );
        $this->add_control(
            'postorder',
            [
                'label' => esc_html__( 'Order', 'ht-slider' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC'  => esc_html__('Descending','ht-slider'),
                    'ASC'   => esc_html__('Ascending','ht-slider'),
                ],

            ]
        );
    }
}