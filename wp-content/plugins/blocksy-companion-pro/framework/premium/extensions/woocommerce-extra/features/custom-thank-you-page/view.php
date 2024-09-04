<?php
/**
 * The template for putting the site in maintenance mode.
 *
 * @package Blocksy
 */
$renderer = new \Blocksy\CustomPostTypeRenderer($blc_mathing_thank_you_page->ID);
$output = $renderer->get_content();

echo $output;