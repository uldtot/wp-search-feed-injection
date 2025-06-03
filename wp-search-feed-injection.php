<?php
/**
 * Plugin Name: WP Search Feed Injection
 * Description: Injektér eksterne XML-feeds som CPT og vis dem i søgning.
 * Version: 1.0.0
 * Author: Kim Vinberg
 */

if (!defined('ABSPATH')) exit;

class WPSearchFeedInjection {

    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'save_settings']);
        add_action('pre_get_posts', [$this, 'include_feed_items_in_search'],9999);
        add_filter('post_type_link', [$this, 'filter_external_feed_permalink'], 10, 2);
        add_filter('the_title', [$this, 'filter_search_result_title'], 10, 2);

        register_post_meta('wsfi_feed_item', 'external_url', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);

        add_action('add_meta_boxes', [$this, 'add_external_url_meta_box']);
        add_action('save_post', function($post_id) {
            if (array_key_exists('wsfi_external_url', $_POST)) {
                update_post_meta($post_id, 'external_url', sanitize_text_field($_POST['wsfi_external_url']));
            }
        });



    }
    
    public function add_external_url_meta_box() {
        add_meta_box(
            'wsfi_external_url_box',
            'Ekstern URL',
            [$this, 'wsfi_render_external_url_box'],
            'wsfi_feed_item',
            'normal',
            'default'
        );
    }

    public function wsfi_render_external_url_box($post) {
        $value = get_post_meta($post->ID, 'external_url', true);
        echo '<label for="wsfi_external_url">URL:</label>';
        echo '<input type="text" id="wsfi_external_url" name="wsfi_external_url" value="' . esc_attr($value) . '" style="width:100%;" />';
    }

    public function include_feed_items_in_search($query) {
        if ($query->is_search() && $query->is_main_query() && !is_admin()) {
            $post_types = $query->get('post_type');

            if (empty($post_types)) {
                $post_types = ['post'];
            }

            if (is_array($post_types)) {
                $post_types[] = 'wsfi_feed_item';
            }

            $query->set('post_type', array_unique($post_types));
        }
    }

    public function filter_external_feed_permalink($url, $post) {
        if (is_search() && $post->post_type === 'wsfi_feed_item') {
            $external_url = get_post_meta($post->ID, 'external_url', true);
            if (!empty($external_url)) {
                return esc_url($external_url);
            }
        }

        return $url;
    }

    public function filter_search_result_title($title, $post_id) {
        if (is_search()) {
            $post = get_post($post_id);
            if ($post && $post->post_type === 'wsfi_feed_item') {
                $permalink = get_permalink($post_id);
                return $this->open_external_permalinks_in_new_tab($permalink, $post);
            }
        }
        return $title;
    }

    public function open_external_permalinks_in_new_tab($link, $post) {
        if (is_search() && $post->post_type === 'wsfi_feed_item') {
            $site_url = get_site_url();

            if (strpos($link, $site_url) === false) {
                $title = get_post_field('post_title', $post->ID); // ❗ Brug dette i stedet for get_the_title()
                return '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">' . esc_html($title) . '</a>';
            }
        }

        // Return standard-link hvis ikke i søgning eller ikke eksternt
        $title = get_post_field('post_title', $post->ID);
        return '<a href="' . esc_url($link) . '">' . esc_html($title) . '</a>';
    }

    public function register_cpt() {
        register_post_type('wsfi_feed_item', [
            'label' => 'Feed Items',
            'public' => true,
            'exclude_from_search' => false,
            'has_archive' => false,
            'rewrite' => false,
            'supports' => ['title', 'editor', 'excerpt', 'author'],
            'show_in_rest' => false,
        ]);
    }


  
}

new WPSearchFeedInjection();
