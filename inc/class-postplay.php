<?php

class PostPlay {

    private $__file;

    public function PostPlay($file) {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        new PostPlayMetabox();
        $this->defineProperties($file);

        add_action('init', array($this, 'downloadTheFile'));
    }

    private function defineProperties($file) {
        define('POSTPLAY_LANG_SLUG', '');
        $this->__file = $file;
    }

    function enqueue_admin_styles() {
        wp_enqueue_style('postplay-admin', plugin_dir_url($this->__file) . 'inc/css/postplay-admin.css', false, '1.0.0');
    }

    function downloadTheFile() {
        if (empty($_REQUEST['test-upload']) || $_REQUEST['test-upload'] != 'ammapa')
            return;

        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        }

        $url = 'http://ananmanan1.com/mp3/201508/m_Edward_Jayakody_Sunila_Yuwathiya[Ananmanan.lk].mp3'; // Input a .zip URL here
        $tmp = download_url($url);
        $file_array = array(
            'name' => basename($url),
            'tmp_name' => $tmp
        );

        // Check for download errors
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);
            var_dump($tmp);
            exit();
        }

        $id = media_handle_sideload($file_array, 0);
        // Check for handle sideload errors.
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            var_dump($id);
            exit();
        }

        $attachment_url = wp_get_attachment_url($id);
        // Do whatever you have to here

        var_dump($attachment_url);

        exit();
    }

}
