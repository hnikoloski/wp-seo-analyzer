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
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
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
                        'icon'  => 'chart-line'
                    ]
                ]
            );
        });

        // Register block
        register_block_type(dirname(__DIR__) . '/build/blocks/seo-analyzer', [
            'render_callback' => [$this, 'render_block']
        ]);

        // Register scripts and styles
        $this->register_assets();
    }

    public function register_assets() {
        $script_asset_path = dirname(__DIR__) . '/build/index.asset.php';
        if (!file_exists($script_asset_path)) {
            throw new \Error(
                'You need to run `npm start` or `npm run build` first.'
            );
        }

        $script_asset = require($script_asset_path);
        
        // Register the main script
        wp_register_script(
            'wp-seo-analyzer-editor',
            plugins_url('build/index.js', dirname(__FILE__)),
            array_merge(['wp-element', 'wp-components', 'wp-blocks', 'wp-i18n'], $script_asset['dependencies']),
            $script_asset['version'],
            true
        );

        // Register styles
        wp_register_style(
            'wp-seo-analyzer-editor',
            plugins_url('build/style-index.css', dirname(__FILE__)),
            [],
            $script_asset['version']
        );

        // Register frontend script
        wp_register_script(
            'wp-seo-analyzer-frontend',
            plugins_url('build/index.js', dirname(__FILE__)),
            array_merge(['wp-element', 'wp-components'], $script_asset['dependencies']),
            $script_asset['version'],
            true
        );

        // Register frontend styles
        wp_register_style(
            'wp-seo-analyzer-frontend',
            plugins_url('build/style-index.css', dirname(__FILE__)),
            [],
            $script_asset['version']
        );

        // Localize both scripts with a public nonce
        $script_data = [
            'nonce' => wp_create_nonce('wp_rest'),
            'apiUrl' => rest_url('wp-seo-analyzer/v1')
        ];
        wp_localize_script('wp-seo-analyzer-editor', 'wpSeoAnalyzer', $script_data);
        wp_localize_script('wp-seo-analyzer-frontend', 'wpSeoAnalyzer', $script_data);
    }

    public function enqueue_block_editor_assets() {
        wp_enqueue_script('wp-seo-analyzer-editor');
        wp_enqueue_style('wp-seo-analyzer-editor');
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_wp-seo-analyzer' !== $hook) {
            return;
        }

        wp_enqueue_script('wp-seo-analyzer-editor');
        wp_enqueue_style('wp-seo-analyzer-editor');
    }

    public function enqueue_frontend_assets() {
        if (!has_block('wp-seo-analyzer/seo-analyzer') && !is_singular()) {
            return;
        }

        wp_enqueue_script('wp-seo-analyzer-frontend');
        wp_enqueue_style('wp-seo-analyzer-frontend');
    }

    public function add_admin_menu() {
        add_menu_page(
            __('SEO Analyzer', 'wp-seo-analyzer'),
            __('SEO Analyzer', 'wp-seo-analyzer'),
            'manage_options',
            'wp-seo-analyzer',
            [$this, 'render_admin_page'],
            'dashicons-chart-area'
        );
    }

    public function render_admin_page() {
        echo '<div id="wp-seo-analyzer-admin"></div>';
    }

    public function render_block($attributes) {
        ob_start();
        ?>
        <div class="wp-block-wp-seo-analyzer-seo-analyzer">
            <div id="wp-seo-analyzer-block"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_shortcode($atts) {
        return $this->render_block($atts);
    }
}
