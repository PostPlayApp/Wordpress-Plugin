<?php

class PostPlay {

    private $__file;

    public function PostPlay($file) {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        new PostPlayMetabox();
        $this->defineProperties($file);
    }

    private function defineProperties($file) {
        define('POSTPLAY_LANG_SLUG', '');
        $this->__file = $file;
    }

    function enqueue_admin_styles() {
        wp_enqueue_style('postplay-admin', plugin_dir_url($this->__file) . 'inc/css/postplay-admin.css', false, '1.0.0');
    }

}
