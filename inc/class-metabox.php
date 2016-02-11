<?php

class PostPlayMetabox {

    public function PostPlayMetabox() {
        add_action('add_meta_boxes', array($this, 'register_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
    }

    /**
     * Register meta box(es).
     */
    public function register_meta_box() {
        add_meta_box('postplay-metabox', __('PostPlay Audio', POSTPLAY_LANG_SLUG), array($this, 'metabox_content'), null, 'side', 'core');
    }

    /**
     * Meta box display callback.
     *
     * @param WP_Post $post Current post object.
     */
    public function metabox_content($post) {
        wp_nonce_field('postplay_nonce_ver', 'postplay_meta_box_nonce');
        include 'views/view-metabox.php';
    }

    /**
     * Save meta box content.
     *
     * @param int $post_id Post ID
     */
    public function save_meta_box($post_id) {

        if (!isset($_POST['postplay_meta_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['postplay_meta_box_nonce'];
        if (!wp_verify_nonce($nonce, 'postplay_nonce_ver')) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (wp_is_post_revision($post_id))
            return;

        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        $the_value = sanitize_text_field($_POST['postplay_send']);

        /*
         * 
         * Publish data to server
         * 
         */

        if ($the_value == '1') {
            $response = wp_remote_post('http://postplay.dev/api/v1/publish', array(
                'method' => 'POST',
                'timeout' => 20,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'body' => array(
                    'api_email' => 'prabodamadushan@gmail.com',
                    'api_key' => 'TfbWGY6EugIghpWHnvXt',
                    'pp_title' => $_POST['post_title'],
                    'pp_content' => $_POST['content'],
                    'pp_url' => get_permalink($post_id),
                )
                    )
            );

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
            } else {
                update_post_meta($post_id, '_postplay_submit', $the_value);
            }
        }
    }

}
