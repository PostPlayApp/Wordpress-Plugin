<?php

class PostPlayOptions {

    public function __construct() {
        // create custom plugin settings menu
        add_action('admin_menu', array($this, 'create_menu'));
    }

    public function create_menu() {

        //create new top-level menu
        add_submenu_page('options-general.php', 'PostPlay Options', 'PostPlay Options', 'manage_options', 'postplay-options', array($this, 'settings_page'));

        //call register settings function
        add_action('admin_init', array($this, 'register_options'));
    }

    public function register_options() {
        //register our settings
        register_setting('postplay-options-group', '_postplay_api_email');
        register_setting('postplay-options-group', '_postplay_api_key');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h2>PostPlay Options</h2>

            <form method="post" action="options.php">
                <?php settings_fields('postplay-options-group'); ?>
                <?php
                do_settings_sections('postplay-options-group');
                $ppConnector = new PostPlayConnector();
                $api_check = $ppConnector->checkApiStatus();
                if ($api_check) {
                    echo '<div class="updated settings-error notice" style="background: #C7FF94;"><p>Your PostPlay account is verified and active! <b>- You have ' . $api_check->data->credits . ' credit' . (($api_check->data->credits == 1) ? '' : 's') . '.</b>' . (($api_check->data->credits < 2) ? ' <a href="#" style="float:right;"><b>Add credits here</b></a>' : '') . '</p></div>';
                } else {
                    echo '<div class="error settings-error notice postplay-error"><p>Your PostPlay account is not active. Please activate by providing following Email Address and API key!</p></div>';
                }
                ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Email Address</th>
                        <td>
                            <input type="text" class="regular-text" name="_postplay_api_email" placeholder="API Email Address.." value="<?php echo esc_attr(get_option('_postplay_api_email')); ?>" />
                            <p class="description">Email address of your PostPlay account.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" class="regular-text" name="_postplay_api_key" placeholder="API Key" value="<?php echo esc_attr(get_option('_postplay_api_key')); ?>" />
                            <p class="description">Find your API key <a href="#">here</a>.</p>
                        </td>
                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }

}

if (is_admin())
    $my_settings_page = new PostPlayOptions();