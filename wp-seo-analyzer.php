<?php
/**
 * Plugin Name: WP SEO Analyzer
 * Plugin URI: https://github.com/hnikoloski/wp-seo-analyzer
 * Description: A high-performance WordPress plugin for programmatic SEO analysis
 * Version: 1.0.0
 * Author: hNikoloski
 * Author URI: https://hnikoloski.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-seo-analyzer
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/Api.php';
require_once plugin_dir_path(__FILE__) . 'includes/Plugin.php';

// Initialize the plugin
WPSeoAnalyzer\Plugin::get_instance();

// Register scripts and styles
function wp_seo_analyzer_register_assets() {
    $asset_file = include(plugin_dir_path(__FILE__) . 'build/index.asset.php');
    
    wp_register_script(
        'wp-seo-analyzer',
        plugins_url('build/index.js', __FILE__),
        $asset_file['dependencies'],
        $asset_file['version'],
        true
    );

    wp_register_style(
        'wp-seo-analyzer',
        plugins_url('build/style-index.css', __FILE__),
        [],
        $asset_file['version']
    );
}
add_action('init', 'wp_seo_analyzer_register_assets');

// Enqueue scripts and styles
function wp_seo_analyzer_enqueue_assets() {
    if (!is_admin()) {
        return;
    }

    wp_enqueue_script('wp-seo-analyzer');
    wp_enqueue_style('wp-seo-analyzer');
}
add_action('admin_enqueue_scripts', 'wp_seo_analyzer_enqueue_assets');
