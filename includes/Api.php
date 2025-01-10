<?php

namespace WPSeoAnalyzer;

class Api {
    private $namespace = 'wp-seo-analyzer/v1';
    private $settings;

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
        $this->settings = Settings::get_instance();
    }

    public function register_routes() {
        register_rest_route($this->namespace, '/analyze', [
            'methods' => 'GET',
            'callback' => [$this, 'analyze'],
            'permission_callback' => [$this, 'check_bearer_token'],
            'args' => [
                'keyword' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'post_type' => [
                    'required' => false,
                    'type' => 'string',
                    'default' => 'all',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'show_only_keyword' => [
                    'required' => false,
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ]);

        register_rest_route($this->namespace, '/post-types', [
            'methods' => 'GET',
            'callback' => [$this, 'get_post_types'],
            'permission_callback' => [$this, 'check_bearer_token'],
        ]);
    }

    public function check_bearer_token($request) {
        $auth_header = $request->get_header('Authorization');
        
        if (empty($auth_header) || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            return new \WP_Error(
                'rest_forbidden',
                'Missing or invalid Bearer token. Add "Authorization: Bearer your-token" header.',
                ['status' => 401]
            );
        }

        $token = $matches[1];
        if ($token !== $this->settings->get_api_key()) {
            return new \WP_Error(
                'rest_forbidden',
                'Invalid Bearer token.',
                ['status' => 401]
            );
        }

        return true;
    }

    public function check_permission() {
        return current_user_can('edit_posts');
    }

    private function analyze_content($content, $keyword) {
        $word_count = str_word_count(strip_tags($content));
        $keyword_count = substr_count(strtolower(strip_tags($content)), strtolower($keyword));
        $keyword_density = $word_count > 0 ? ($keyword_count / $word_count) * 100 : 0;
        
        return [
            'word_count' => $word_count,
            'keyword_count' => $keyword_count,
            'keyword_density' => round($keyword_density, 2) // Round to 2 decimal places
        ];
    }

    public function analyze($request) {
        $keyword = sanitize_text_field($request->get_param('keyword'));
        $post_type = sanitize_text_field($request->get_param('post_type'));

        if (empty($keyword)) {
            return new \WP_Error('invalid_keyword', 'Keyword is required', ['status' => 400]);
        }

        $args = [
            'post_type' => $post_type === 'all' ? ['post', 'page'] : $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1
        ];

        $query = new \WP_Query($args);
        $results = [];

        foreach ($query->posts as $post) {
            $content = $post->post_content . ' ' . $post->post_title;
            $analysis = $this->analyze_content($content, $keyword);
            
            $results[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'type' => get_post_type_object($post->post_type)->labels->singular_name,
                'edit_url' => get_edit_post_link($post->ID, 'raw'),
                'word_count' => $analysis['word_count'],
                'keyword_count' => $analysis['keyword_count'],
                'keyword_density' => $analysis['keyword_density']
            ];
        }

        return new \WP_REST_Response($results, 200);
    }

    public function get_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $results = [];

        foreach ($post_types as $post_type) {
            if ($post_type->name === 'attachment') {
                continue;
            }

            $results[] = [
                'name' => $post_type->name,
                'label' => $post_type->labels->singular_name,
            ];
        }

        return rest_ensure_response($results);
    }
}
