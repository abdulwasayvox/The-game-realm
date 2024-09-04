<?php
function htslider_slider_shortcode( $atts ) {

    wp_enqueue_style('htslider-widgets');
    wp_enqueue_style('slick');
    wp_enqueue_script('slick');

    // Extract shortcode attributes
    $default_atts = array(
        'limit' => -1, 
        'show_by' => 'show_byid', 
        'ids' => '', 
        'category' => '', 
        'pagination' => 'false', 
        'arrow' => 'true', 
        'slides' => '1', 
    );
    $atts = shortcode_atts( $default_atts, $atts );
    $limit = intval( $atts['limit'] );
    $show_by = sanitize_text_field( $atts['show_by'] );
    $ids = sanitize_text_field( $atts['ids'] );
    $category = sanitize_text_field( $atts['category'] );
    $arrow = sanitize_text_field( $atts['arrow'] );
    $pagination = sanitize_text_field( $atts['pagination'] );
    $slides = intval( $atts['slides'] ); 

    // Set up query arguments based on shortcode attributes
    $args = array(
        'post_type'      => 'htslider_slider',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'order'          => 'ASC',
    );

    if ( $show_by == 'show_byid' && ! empty( $ids ) ) {
        $args['post__in'] = explode( ',', $ids );
    } elseif ( $atts['show_by'] == 'show_bycat' && ! empty( $category ) ) {

        $slider_cats = str_replace(' ', '', $category);
        if ( "0" != $category) {
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

        $args['tax_query'] = array(
            array(
                'taxonomy' => 'htslider_category',
                'terms' => explode( ',', $category ),
                'field' => is_numeric( $category ) ? 'term_id' : 'slug',
                'include_children' => false,
            ),
        );
    }

    // Perform WP_Query
    $sliders = new WP_Query( $args );

    // Start output buffering
    ob_start();
    $unicid = 'htslider_slider_' . uniqid();
    // Output slider HTML
    ?>
    <div class="htslider-slider-area">
        <div class="htslider-carousel-activation htslider-slider <?php esc_attr_e( $unicid ); ?>">
            <?php
            while ( $sliders->have_posts() ) : $sliders->the_post();
                ?>
                
                <div class="slingle-slider">
                    <div class="slider-content"><?php the_content(); ?></div>
                </div>
                <?php
            endwhile;
            ?>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {

            var $slider_elem = $('.htslider-slider.<?php esc_attr_e( $unicid ); ?>');
            $($slider_elem).slick({
                arrows: <?php echo esc_js( $arrow ); ?>,
                prevArrow: '<button class="slick-prev"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M390.627 54.627 189.255 256l201.372 201.373a32 32 0 1 1-45.254 45.254l-224-224a32 32 0 0 1 0-45.254l224-224a32 32 0 0 1 45.254 45.254z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg></button>',
                nextArrow: '<button class="slick-next"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 240.823 240.823" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M183.189 111.816 74.892 3.555c-4.752-4.74-12.451-4.74-17.215 0-4.752 4.74-4.752 12.439 0 17.179l99.707 99.671-99.695 99.671c-4.752 4.74-4.752 12.439 0 17.191 4.752 4.74 12.463 4.74 17.215 0l108.297-108.261c4.68-4.691 4.68-12.511-.012-17.19z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg></button>',
                dots: <?php echo esc_js( $pagination ); ?>,
                infinite: true,
                speed: 300,
                slidesToShow: <?php echo esc_js( absint( $slides ) ); ?>
            });

            if ($slider_elem) {

                $slider_elem.each(function () {
                    var $this = $(this),
                        $singleSlideElem = $this.find('.slick-slide .elementor-widget-wrap .elementor-element');
                    function $slideElemAnimation() {
                        $singleSlideElem.each(function () {
                            var $this = $(this),
                                $thisSetting = $this.data('settings') ? $this.data('settings') : '',
                                $animationName = $thisSetting._animation,
                                $animationDelay = $thisSetting._animation_delay;
                            $this.removeClass('animated ' + $animationName).addClass('animated fadeOut');
                            if($this.closest('.slick-slide').hasClass('slick-current')) {
                                $this.removeClass('animated fadeOut').addClass('animated ' + $animationName).css({
                                    'animation-delay': $animationDelay+'s'
                                });
                            }
                        });
                    }
                    $slideElemAnimation();
                    $this.on('afterChange', function(slick, currentSlide){
                        $slideElemAnimation();
                    });
                    $this.on('beforeChange', function(slick, currentSlide){
                        $slideElemAnimation();
                    });
                    $this.on('init', function(slick){
                        $slideElemAnimation();
                    });
                });
            }
        });

    </script>
    <?php

    // Reset post data and query
    wp_reset_postdata();

    // Return buffered output
    return ob_get_clean();
}
add_shortcode( 'htslider', 'htslider_slider_shortcode' );
