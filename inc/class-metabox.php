<?php

class PostPlayMetabox {

    public function PostPlayMetabox() {
        add_action('add_meta_boxes', array($this, 'wpdocs_register_meta_boxes'));
        add_action('save_post', array($this, 'wpdocs_save_meta_box'));
    }

    /**
     * Register meta box(es).
     */
    public function wpdocs_register_meta_boxes() {
        add_meta_box('meta-box-id', __('My Meta Box', 'textdomain'), array($this, 'wpdocs_my_display_callback'), 'post', 'side', 'core');
    }

    /**
     * Meta box display callback.
     *
     * @param WP_Post $post Current post object.
     */
    public function wpdocs_my_display_callback($post) {
        echo 'test';
    }

    /**
     * Save meta box content.
     *
     * @param int $post_id Post ID
     */
    public function wpdocs_save_meta_box($post_id) {

    }

}
