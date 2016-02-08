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
        include 'view-metabox.php';
    }

    /**
     * Save meta box content.
     *
     * @param int $post_id Post ID
     */
    public function save_meta_box($post_id) {

    }

}
