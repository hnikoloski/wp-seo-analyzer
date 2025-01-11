<?php
/**
 * Plugin Name: WP SEO Analyzer
 * Plugin URI: https://github.com/hnikoloski/wp-seo-analyzer
 * Description: A high-performance WordPress plugin for programmatic SEO analysis
 * Version: 1.0.0
 * Requires at least: 6.7.1
 * Requires PHP: 8.3.3
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
