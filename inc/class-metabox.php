<?php

class PostPlayMetabox {

    public function PostPlayMetabox() {
        add_action('add_meta_boxes', array($this, 'register_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
        add_action('admin_notices', array($this, 'show_error_messages'));
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
        $api_email = esc_attr(get_option('_postplay_api_email'));
        $api_key = esc_attr(get_option('_postplay_api_key'));

        if (empty($api_email) || empty($api_key))
            return;

        /*
         * 
         * Publish data to server
         * 
         */

        if ($the_value == '1') {
            $response = wp_remote_post('http://postplay.dev/api/v1/publish', array(
                'method' => 'POST',
                'timeout' => 20,
                'httpversion' => '1.0',
                'body' => array(
                    'api_email' => $api_email,
                    'api_key' => $api_key,
                    'pp_title' => $_POST['post_title'],
                    'pp_content' => $_POST['content'],
                    'pp_url' => get_permalink($post_id),
                )
                    )
            );

            $response_obj = json_decode(wp_remote_retrieve_body($response), true);

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
            } elseif ($response_obj['status'] == 'success') {
                update_post_meta($post_id, '_postplay_submit', $the_value);
            } elseif ($response_obj['status'] == 'error') {
                $error_messages = $response_obj['messages'];
                set_transient("_postplay_error_msg", ($error_messages[0]), 60);
            }
        }
    }
    
    /*
     * 
     * Show error messages
     * 
     */
    
    public function show_error_messages() {
        global $post;
        if (false !== ( $msg = get_transient("_postplay_error_msg") ) && $msg) {
            delete_transient("_postplay_error_msg");
            echo "<div id=\"postplay-plugin-message\" class=\"error notice notice-success is-dismissible postplay-error\"><p>PostPlay error: $msg.</p></div>";
        }
    }

}
