<?php

global $post;
$current_product_id = $post->ID;

$pages = get_posts([
    'numberposts' => -1,
    'post_type'   => 'ct_thank_you_page',
]);

$selected = (int) get_post_meta($current_product_id, '_ct_thank_you_page_id', true);

?>
<div id="ct_custom_thank_you" class="panel woocommerce_options_panel hidden">
	<div class="options_group">
		<p class="form-field">
			<label for="ct_thank_you_page_id">
                <?php
                    _e( 'Choose thank you page', 'blocksy-companion' );
                ?>
            </label>
			
            <select style="width:100%" name="ct_thank_you_page_id">
                <option value="0">
                    <?php _e( 'None', 'blocksy-companion' ); ?>
                </option>
                    
                <?php foreach ( $pages as $page ) { ?>
                    <option <?php echo $page->ID === $selected ? 'selected' : ''; ?> value="<?php echo esc_attr( $page->ID ); ?>">
                        <?php echo esc_html( $page->post_title ); ?>
                    </option>
                <?php } ?>
            </select>
		</p>
	</div>
</div>