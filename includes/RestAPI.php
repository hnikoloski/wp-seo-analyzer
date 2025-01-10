<?php
namespace WPSeoAnalyzer;

class RestAPI {
    private $analyzer;
    private $namespace = 'wp-seo-analyzer/v1';

    public function __construct(ContentAnalyzer $analyzer) {
        $this->analyzer = $analyzer;
    }

    public function init() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route($this->namespace, '/analyze', [
            'methods' => 'GET',
            'callback' => [$this, 'get_analysis'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'keyword' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);
    }

    public function check_permission() {
        return current_user_can('read');
    }

    public function get_analysis($request) {
        $keyword = $request->get_param('keyword');
        $results = $this->analyzer->analyze_posts($keyword);
        
        return rest_ensure_response($results);
    }
}
