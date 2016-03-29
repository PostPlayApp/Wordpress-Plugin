<?php

class PostPlayUploads {

    public function upload_dir($dirs) {
        $dirs['subdir'] = '/postplay';
        $dirs['path'] = $dirs['basedir'] . '/postplay';
        $dirs['url'] = $dirs['baseurl'] . '/postplay';

        return $dirs;
    }

    private function verifyCallbackKey($post_id, $key) {
        $key_saved = get_post_meta($post_id, '_postplay_callback_key', TRUE);
        if ($key == $key_saved)
            return TRUE;
        return FALSE;
    }
    
    private function deleteCallbackKey($post_id, $key){
        delete_post_meta($post_id, '_postplay_callback_key', $key);
    }

    public function downloadTheFile() {   
        
        
        if (empty($_REQUEST['postplay_callback']) || $_REQUEST['postplay_callback'] != 'run')
            return;

        if (empty($_REQUEST['post_id']) || empty($_REQUEST['key']) || empty($_REQUEST['file_url']))
            return;
        
       

        $post_id = $_REQUEST['post_id'];
        $key = $_REQUEST['key'];
        $file = $_REQUEST['file_url'];
        $file_name = $_REQUEST['file_name'];
        
        //wp_send_json(array('status' => $file));

        if (!$this->verifyCallbackKey($post_id, $key))
            wp_send_json(array('status' => 'error1'));

        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        }

        $url = $file; // Input a .zip URL here
        $tmp = download_url($url);
        $file_array = array(
            'name' => $file_name,
            'tmp_name' => $tmp
        );
        

        // Check for download errors
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);
            wp_send_json(array('status' => 'error2', 'messages' => json_encode($tmp->get_error_message() )     ));
        }

        add_filter('upload_dir', array($this, 'upload_dir'));
        $id = media_handle_sideload($file_array, $post_id);
        remove_filter('upload_dir', array($this, 'upload_dir'));

        // Check for handle sideload errors.
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            wp_send_json(array('status' => 'error3' , 'messages' => json_encode($tmp->get_error_message() )   ));
        }

        $attachment_url = wp_get_attachment_url($id);
        //TODO $this->deleteCallbackKey($post_id, $key);
        // Do whatever you have to here
        wp_send_json(array('status' => 'success', 'attachment_url' => $attachment_url));
        
    }

}
