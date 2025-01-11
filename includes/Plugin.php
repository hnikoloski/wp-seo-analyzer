<?php
namespace WPSeoAnalyzer;

class Plugin {
    private static $instance = null;
    private $api = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'init']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        
        // Initialize API
        $this->api = new Api();
        
        // Add shortcode
        add_shortcode('seo_analyzer', [$this, 'render_shortcode']);
    }

    public function init() {
        // Register custom block category
        add_filter('block_categories_all', function($categories) {
            return array_merge(
                $categories,
                [
                    [
                        'slug' => 'seo-tools',
                        'title' => __('SEO Tools', 'wp-seo-analyzer'),
                        'icon'  => 'chart-line' // Using Dashicons
                    ]
                ]
            );
        });

        // Register block
        register_block_type(dirname(__DIR__) . '/src/blocks/seo-analyzer', [
            'render_callback' => [$this, 'render_block']
        ]);
    }

    public function register_assets() {
        $script_asset_path = dirname(__DIR__) . '/build/index.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require($script_asset_path)
            : ['dependencies' => [], 'version' => filemtime(dirname(__DIR__) . '/build/index.js')];

        wp_register_script(
            'wp-seo-analyzer',
            plugins_url('build/index.js', dirname(__FILE__)),
            array_merge(
                ['wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-block-editor'],
                $script_asset['dependencies']
            ),
            $script_asset['version'],
            true
        );

        wp_register_style(
            'wp-seo-analyzer-style',
            plugins_url('build/style-index.css', dirname(__FILE__)),
            [],
            filemtime(dirname(__DIR__) . '/build/style-index.css')
        );

        wp_localize_script('wp-seo-analyzer', 'wpSeoAnalyzer', [
            'apiUrl' => rest_url('wp-seo-analyzer/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }

    public function enqueue_admin_assets($hook) {
        if (!did_action('wp_register_scripts')) {
            $this->register_assets();
        }

        if ($hook === 'toplevel_page_wp-seo-analyzer') {
            wp_enqueue_script('wp-seo-analyzer');
            wp_enqueue_style('wp-seo-analyzer-style');
        }
    }

    public function enqueue_frontend_assets() {
        if (!did_action('wp_register_scripts')) {
            $this->register_assets();
        }

        if (has_block('wp-seo-analyzer/seo-analyzer') || has_shortcode(get_post()->post_content, 'seo_analyzer')) {
            wp_enqueue_script('wp-seo-analyzer');
            wp_enqueue_style('wp-seo-analyzer-style');
        }
    }

    public function render_block($attributes, $content) {
        return sprintf(
            '<div class="wp-block-wp-seo-analyzer-seo-analyzer"><div class="wp-seo-analyzer-content"></div></div>'
        );
    }

    public function add_admin_menu() {
        add_menu_page(
            __('SEO Analyzer', 'wp-seo-analyzer'),
            __('SEO Analyzer', 'wp-seo-analyzer'),
            'edit_posts',
            'wp-seo-analyzer',
            [$this, 'render_admin_page'],
            'dashicons-chart-bar'
        );
    }

    public function render_admin_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('SEO Analyzer', 'wp-seo-analyzer') . '</h1>';
        echo '<div class="wp-seo-analyzer-content"></div>';
        echo '</div>';
    }

    public function render_shortcode($atts) {
        return '<div class="wp-seo-analyzer-shortcode"><div class="wp-seo-analyzer-content"></div></div>';
    }
}
