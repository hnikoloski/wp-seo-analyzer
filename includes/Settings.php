<?php

namespace WPSeoAnalyzer;

class Settings {
    private static $instance = null;
    private $option_name = 'wp_seo_analyzer_settings';

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_submenu_page(
            'wp-seo-analyzer',
            'SEO Analyzer Settings',
            'Settings',
            'manage_options',
            'wp-seo-analyzer-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting($this->option_name, $this->option_name);

        add_settings_section(
            'api_settings',
            'API Settings',
            null,
            'wp-seo-analyzer-settings'
        );

        add_settings_field(
            'api_key',
            'API Key',
            [$this, 'render_api_key_field'],
            'wp-seo-analyzer-settings',
            'api_settings'
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>SEO Analyzer Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->option_name);
                do_settings_sections('wp-seo-analyzer-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_api_key_field() {
        $options = get_option($this->option_name);
        $api_key = isset($options['api_key']) ? $options['api_key'] : '';
        
        if (empty($api_key)) {
            $api_key = wp_generate_password(32, false);
            update_option($this->option_name, array_merge((array)$options, ['api_key' => $api_key]));
        }
        ?>
        <input type="text" 
               id="api_key" 
               name="<?php echo $this->option_name; ?>[api_key]" 
               value="<?php echo esc_attr($api_key); ?>" 
               class="regular-text"
               readonly
        />
        <p class="description">Use this API key to authenticate API requests. Keep it secret!</p>
        <?php
    }

    public function get_api_key() {
        $options = get_option($this->option_name);
        return isset($options['api_key']) ? $options['api_key'] : '';
    }
}
