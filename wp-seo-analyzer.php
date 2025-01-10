<?php
/**
 * Plugin Name: WP SEO Analyzer
 * Description: A WordPress plugin to analyze SEO metrics of your content.
 * Version: 1.0.0
 * Author: hNikoloski
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-seo-analyzer
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/Settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/Api.php';
require_once plugin_dir_path(__FILE__) . 'includes/Plugin.php';

// Initialize the plugin
WPSeoAnalyzer\Plugin::get_instance();
