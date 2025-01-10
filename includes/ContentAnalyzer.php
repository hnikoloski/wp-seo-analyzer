<?php
namespace WPSeoAnalyzer;

class ContentAnalyzer {
    public function analyze_posts($keyword = '') {
        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ]);

        $results = [];
        foreach ($posts as $post) {
            $results[] = $this->analyze_single_post($post, $keyword);
        }

        return $results;
    }

    public function analyze_single_post($post, $keyword = '') {
        $content = wp_strip_all_tags($post->post_content);
        $word_count = str_word_count($content);
        
        $keyword_density = 0;
        if (!empty($keyword)) {
            $keyword_count = substr_count(strtolower($content), strtolower($keyword));
            $keyword_density = $word_count > 0 ? ($keyword_count / $word_count) * 100 : 0;
        }

        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'word_count' => $word_count,
            'keyword_density' => round($keyword_density, 2),
            'url' => get_permalink($post->ID),
            'date' => get_the_date('Y-m-d', $post->ID)
        ];
    }
}
