<?php
// tappersia/admin/ajax/class-yab-ajax-content-handler.php

if (!class_exists('Yab_Ajax_Content_Handler')) {
    class Yab_Ajax_Content_Handler {

        public function register_hooks() {
            add_action('wp_ajax_yab_search_content', [$this, 'search_content']);
        }

        public function search_content() {
            check_ajax_referer('yab_nonce', 'nonce');
            if (!current_user_can('manage_options')) { 
                wp_send_json_error(['message' => 'Permission denied.'], 403); 
                return; 
            }

            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
            $content_type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : 'posts';
            
            $results = [];

            switch ($content_type) {
                case 'posts':
                    $query = new WP_Query([
                        'post_type' => 'post', 
                        'posts_per_page' => 50, 
                        's' => $search_term, 
                        'post_status' => 'publish'
                    ]);
                    foreach ($query->posts as $post) { 
                        $results[] = ['ID' => $post->ID, 'post_title' => $post->post_title]; 
                    }
                    break;
                case 'pages':
                    $query = new WP_Query([
                        'post_type' => 'page', 
                        'posts_per_page' => 50, 
                        's' => $search_term, 
                        'post_status' => 'publish'
                    ]);
                    foreach ($query->posts as $page) { 
                        $results[] = ['ID' => $page->ID, 'post_title' => $page->post_title]; 
                    }
                    break;
                case 'categories':
                    $terms = get_terms([
                        'taxonomy' => 'category', 
                        'name__like' => $search_term, 
                        'hide_empty' => false, 
                        'number' => 50
                    ]);
                    foreach ($terms as $term) { 
                        $results[] = ['term_id' => $term->term_id, 'name' => $term->name]; 
                    }
                    break;
            }

            wp_send_json_success($results);
            wp_die();
        }
    }
}