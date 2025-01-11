<?php

namespace WPSeoAnalyzer;

class Api {
    private $namespace = 'wp-seo-analyzer/v1';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route($this->namespace, '/analyze', [
            'methods' => 'GET',
            'callback' => [$this, 'analyze'],
            'permission_callback' => '__return_true', 
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
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // Add a helper endpoint to get nonce
        register_rest_route($this->namespace, '/get-nonce', [
            'methods' => 'GET',
            'callback' => function() {
                return [
                    'nonce' => wp_create_nonce('wp_rest'),
                    'expires_in' => DAY_IN_SECONDS
                ];
            },
            'permission_callback' => function() {
                return true;
            }
        ]);
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
        // Verify nonce
        // if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
        //     return new \WP_Error('unauthorized', 'Invalid nonce', ['status' => 401]);
        // }

        $keyword = sanitize_text_field($request->get_param('keyword'));
        $post_type = sanitize_text_field($request->get_param('post_type'));

        if (empty($keyword)) {
            return new \WP_Error('invalid_keyword', 'Keyword is required', ['status' => 400]);
        }

        // Get all public post types except attachments when 'all' is selected
        if ($post_type === 'all') {
            $post_types = get_post_types(['public' => true], 'names');
            unset($post_types['attachment']); // Remove attachments
            $post_type = array_values($post_types);
        }

        $args = [
            'post_type' => $post_type,
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
