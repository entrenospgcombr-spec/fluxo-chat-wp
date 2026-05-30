```php
<?php
/**
 * Admin settings page for Mistral Chatbot Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register admin menu
 */
function mistral_chatbot_manager_admin_menu() {
    add_menu_page(
        __('Mistral Chatbot', 'mistral-chatbot-manager'),
        __('Mistral Chatbot', 'mistral-chatbot-manager'),
        'manage_options',
        'mistral-chatbot-settings',
        'mistral_chatbot_manager_settings_page',
        'dashicons-admin-comments',
        80
    );

    add_submenu_page(
        'mistral-chatbot-settings',
        __('Configurações', 'mistral-chatbot-manager'),
        __('Configurações', 'mistral-chatbot-manager'),
        'manage_options',
        'mistral-chatbot-settings',
        'mistral_chatbot_manager_settings_page'
    );

    add_submenu_page(
        'mistral-chatbot-settings',
        __('Fluxo de Atendimento', 'mistral-chatbot-manager'),
        __('Fluxo de Atendimento', 'mistral-chatbot-manager'),
        'manage_options',
        'mistral-chatbot-flow',
        'mistral_chatbot_manager_flow_page'
    );

    add_submenu_page(
        'mistral-chatbot-settings',
        __('Diretrizes de IA', 'mistral-chatbot-manager'),
        __('Diretrizes de IA', 'mistral-chatbot-manager'),
        'manage_options',
        'mistral-chatbot-guidelines',
        'mistral_chatbot_manager_guidelines_page'
    );
}
add_action('admin_menu', 'mistral_chatbot_manager_admin_menu');

/**
 * Settings page content
 */
function mistral_chatbot_manager_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings if form was submitted
    if (isset($_POST['mistral_chatbot_save_settings'])) {
        check_admin_referer('mistral_chatbot_settings_nonce');

        update_option('mistral_api_key', sanitize_text_field($_POST['mistral_api_key']));
        update_option('mistral_chatbot_enabled', isset($_POST['mistral_chatbot_enabled']) ? '1' : '0');
        update_option('mistral_chatbot_position', sanitize_text_field($_POST['mistral_chatbot_position']));
        update_option('mistral_chatbot_color', sanitize_hex_color($_POST['mistral_chatbot_color']));
        update_option('mistral_chatbot_title', sanitize_text_field($_POST['mistral_chatbot_title']));

        // Test API connection
        $api_key = sanitize_text_field($_POST['mistral_api_key']);
        $test_result = mistral_chatbot_test_api_connection($api_key);

        if ($test_result['success']) {
            add_settings_error(
                'mistral_chatbot_messages',
                'mistral_chatbot_test_success',
                __('Conexão com a API testada com sucesso!', 'mistral-chatbot-manager'),
                'success'
            );
        } else {
            add_settings_error(
                'mistral_chatbot_messages',
                'mistral_chatbot_test_error',
                sprintf(__('Erro ao testar conexão: %s', 'mistral-chatbot-manager'), $test_result['message']),
                'error'
            );
        }
    }

    // Get current settings
    $api_key = get_option('mistral_api_key', '');
    $chatbot_enabled = get_option('mistral_chatbot_enabled', '1');
    $chatbot_position = get_option('mistral_chatbot_position', 'bottom-right');
    $chatbot_color = get_option('mistral_chatbot_color', '#2563eb');
    $chatbot_title = get_option('mistral_chatbot_title', 'Como posso ajudar?');

    ?>
    <div class="wrap mistral-chatbot-settings">
        <h1><?php esc_html_e('Configurações do Mistral Chatbot', 'mistral-chatbot-manager'); ?></h1>

        <?php settings_errors('mistral_chatbot_messages'); ?>

        <form method="post" action="">
            <?php wp_nonce_field('mistral_chatbot_settings_nonce'); ?>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="mistral_api_key"><?php esc_html_e('Chave da API Mistral', 'mistral-chatbot-manager'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="mistral_api_key" name="mistral_api_key"
                                   value="<?php echo esc_attr($api_key); ?>"
                                   class="regular-text" />
                            <p class="description">
                                <?php esc_html_e('Insira sua chave de API da Mistral para habilitar a integração.', 'mistral-chatbot-manager'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="mistral_chatbot_enabled"><?php esc_html_e('Habilitar Chatbot', 'mistral-chatbot-manager'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="mistral_chatbot_enabled" name="mistral_chatbot_enabled"
                                   value="1" <?php checked($chatbot_enabled, '1'); ?> />
                            <label for="mistral_chatbot_enabled"><?php esc_html_e('Ativar chatbot flutuante no site', 'mistral-chatbot-manager'); ?></label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="mistral_chatbot_position"><?php esc_html_e('Posição do Chatbot', 'mistral-chatbot-manager'); ?></label>
                        </th>
                        <td>
                            <select id="mistral_chatbot_position" name="mistral_chatbot_position">
                                <option value="bottom-right" <?php selected($chatbot_position, 'bottom-right'); ?>>
                                    <?php esc_html_e('Inferior Direito', 'mistral-chatbot-manager'); ?>
                                </option>
                                <option value="bottom-left" <?php selected($chatbot_position, 'bottom-left'); ?>>
                                    <?php esc_html_e('Inferior Esquerdo', 'mistral-chatbot-manager'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="mistral_chatbot_color"><?php esc_html_e('Cor do Chatbot', 'mistral-chatbot-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="mistral_chatbot_color" name="mistral_chatbot_color"
                                   value="<?php echo esc_attr($chatbot_color); ?>"
                                   class="mistral-color-picker" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="mistral_chatbot_title"><?php esc_html_e('Título do Chatbot', 'mistral-chatbot-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="mistral_chatbot_title" name="mistral_chatbot_title"
                                   value="<?php echo esc_attr($chatbot_title); ?>"
                                   class="regular-text" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" name="mistral_chatbot_save_settings"
                       class="button button-primary"
                       value="<?php esc_attr_e('Salvar Configurações', 'mistral-chatbot-manager'); ?>" />
                <input type="submit" name="mistral_chatbot_test_connection"
                       class="button button-secondary"
                       value="<?php esc_attr_e('Testar Conexão com API', 'mistral-chatbot-manager'); ?>" />
            </p>
        </form>
    </div>
    <?php
}
?>