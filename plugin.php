<?php
/**
 * Plugin Name:       Bestsellers Block
 * Description:       Display the best-selling book for a selected genre using the Biblio API.
 * Version:           1.5.0000000000
 * Author:            Vijay Mishra
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/render-block.php';
require_once plugin_dir_path(__FILE__) . 'includes/api.php';

function register_bestsellers_block() {
    register_block_type_from_metadata(__DIR__, [
        'render_callback' => 'render_bestsellers_block',
    ]);
}
add_action('init', 'register_bestsellers_block');
