<div class="wrap">

    <?php Auto_Login_Link::check_notice(TRUE); ?>

    <h1 class="wp-heading-inline"><?php echo $title; ?></h1>

    <form method="post">

        <?php wp_nonce_field('form-action', 'auto-login-link-nonce'); ?>

        <table class="form-table auto-login-link-auto-atc-form">
            <tr valign="top">
                <th scope="row"><?php _e('URL', Auto_Login_Link::LANGUAGE_DOMAIN); ?></th>
                <td>
                    <input type="text" name="url" class="widefat" value="<?php echo isset($form_data) ? $form_data->url : ''; ?>" required />
                    <p class="description"><?php echo __(sprintf('The URL of auto login, for example: %s/{YOUR_URL}', site_url()), Auto_Login_Link::LANGUAGE_DOMAIN); ?></p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Apply for user', Auto_Login_Link::LANGUAGE_DOMAIN); ?></th>
                <td>
                    <select name="user_id" required>
                    <?php 
                    if (count($users) > 0) {
                        foreach ($users as $value) {
                            if ($value->data->ID == $form_data->user_id) {
                                $selected = ' selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="' . $value->data->ID . '" ' . $selected . '>' . $value->data->ID . ' - ' . $value->data->user_login . ' (' . $value->data->user_email . ')' . '</option>';
                        }
                    }
                    ?>
                    </select>
                </td>
            </tr>

        </table>
        
        <?php submit_button('Save'); ?>

    </form>

</div>

