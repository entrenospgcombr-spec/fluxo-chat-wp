```php
<?php
/**
 * Chat widget functionality for Mistral Chatbot Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add chat widget to footer
 */
function mistral_chatbot_add_widget() {
    if (get_option('mistral_chatbot_enabled', '1') !== '1') {
        return;
    }

    $position = get_option('mistral_chatbot_position', 'bottom-right');
    $color = get_option('mistral_chatbot_color', '#2563eb');
    $title = get_option('mistral_chatbot_title', 'Como posso ajudar?');

    ?>
    <div id="mistral-chatbot-widget" class="mistral-chatbot-widget mistral-<?php echo esc_attr($position); ?>" data-color="<?php echo esc_attr($color); ?>">
        <div class="mistral-chatbot-header">
            <span class="mistral-chatbot-title"><?php echo esc_html($title); ?></span>
            <button class="mistral-chatbot-close" aria-label="<?php esc_attr_e('Fechar chat', 'mistral-chatbot-manager'); ?>">&times;</button>
        </div>
        <div class="mistral-chatbot-messages">
            <div class="mistral-chatbot-message mistral-bot-message">
                <?php esc_html_e('Olá! Como posso te ajudar hoje?', 'mistral-chatbot-manager'); ?>
            </div>
        </div>
        <div class="mistral-chatbot-input">
            <input type="text" class="mistral-chatbot-input-field" placeholder="<?php esc_attr_e('Digite sua mensagem...', 'mistral-chatbot-manager'); ?>" />
            <button class="mistral-chatbot-send" aria-label="<?php esc_attr_e('Enviar mensagem', 'mistral-chatbot-manager'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
    <button class="mistral-chatbot-toggle" aria-label="<?php esc_attr_e('Abrir chat', 'mistral-chatbot-manager'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </button>
    <?php
}
add_action('wp_footer', 'mistral_chatbot_add_widget');

/**
 * Handle chat messages via AJAX
 */
function mistral_chatbot_handle_message() {
    check_ajax_referer('mistral_chatbot_nonce', 'nonce');

    if (!isset($_POST['message'])) {
        wp_send_json_error(__('Mensagem não recebida', 'mistral-chatbot-manager'));
    }

    $message = sanitize_text_field($_POST['message']);
    $response = mistral_chatbot_get_ai_response($message);

    if ($response['success']) {
        wp_send_json_success(array(
            'message' => $response['data']
        ));
    } else {
        wp_send_json_error($response['message']);
    }
}
add_action('wp_ajax_mistral_chatbot_message', 'mistral_chatbot_handle_message');
add_action('wp_ajax_nopriv_mistral_chatbot_message', 'mistral_chatbot_handle_message');
?>