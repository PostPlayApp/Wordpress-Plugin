<?php

class PostPlay {

    private $__file;

    public function PostPlay($file) {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        new PostPlayMetabox();
        $this->defineProperties($file);

        add_action('init', array(new PostPlayUploads(), 'downloadTheFile'));
        add_filter('the_content', array($this, 'post_content_filter'));
    }

    private function defineProperties($file) {
        define('POSTPLAY_LANG_SLUG', '');
        $this->__file = $file;
    }

    function enqueue_admin_styles() {
        wp_enqueue_style('postplay-admin', plugin_dir_url($this->__file) . 'inc/css/postplay-admin.css', false, '1.0.0');
    }

    function enqueue_styles() {
        wp_enqueue_style('postplay', plugin_dir_url($this->__file) . 'inc/css/postplay.css', false, '1.0.0');
    }

    function post_content_filter($content) {
        $current_data_str = get_post_meta(get_the_ID(), '_postplay_attachments', TRUE);
        $current_data = unserialize($current_data_str);
        $attachments_array = $current_data['attachments'];
        if (empty($attachments_array) || !is_array($attachments_array))
            return $content;
        ob_start();
        ?>
        <ul class="postplay-audio-players">
            <?php
            foreach ($attachments_array as $atta):
                $attachment_url = wp_get_attachment_url($atta);
                if ($attachment_url == false)
                    continue;
                echo '<li>';
                $attr = array(
                    'src' => $attachment_url,
                    'loop' => '',
                    'autoplay' => '',
                    'preload' => 'none'
                );
                echo wp_audio_shortcode($attr);
                echo '</li>';
            endforeach;
            ?>
        </ul>
        <?php
        $mods = ob_get_clean();
        return $content . $mods;
    }

}
