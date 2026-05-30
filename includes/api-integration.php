```php
<?php
/**
 * API integration for Mistral Chatbot Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test API connection
 */
function mistral_chatbot_test_api_connection($api_key) {
    if (empty($api_key)) {
        return array(
            'success' => false,
            'message' => __('Chave de API não fornecida', 'mistral-chatbot-manager')
        );
    }

    $response = wp_remote_get('https://api.mistral.ai/v1/models', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        )
    ));

    if (is_wp_error($response)) {
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['error'])) {
        return array(
            'success' => false,
            'message' => $data['error']['message'] ?? __('Erro desconhecido', 'mistral-chatbot-manager')
        );
    }

    return array(
        'success' => true,
        'message' => __('Conexão bem-sucedida', 'mistral-chatbot-manager'),
        'data' => $data
    );
}

/**
 * Get AI response from Mistral API
 */
function mistral_chatbot_get_ai_response($message) {
    $api_key = get_option('mistral_api_key', '');

    if (empty($api_key)) {
        return array(
            'success' => false,
            'message' => __('Chave de API não configurada', 'mistral-chatbot-manager')
        );
    }

    // Get guidelines to include in the prompt
    $guidelines = get_option('mistral_ai_guidelines', '');
    $flow = get_option('mistral_attendance_flow', '');

    $prompt = "Você é um assistente virtual seguindo estas diretrizes:\n\n" . $guidelines . "\n\n";
    $prompt .= "Fluxo de atendimento sugerido:\n" . strip_tags($flow) . "\n\n";
    $prompt .= "Usuário: " . $message . "\n";
    $prompt .= "Assistente:";

    $response = wp_remote_post('https://api.mistral.ai/v1/chat/completions', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array(
            'model' => 'mistral-tiny',
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'temperature' => 0.7,
            'max_tokens' => 150
        ))
    ));

    if (is_wp_error($response)) {
        return array(
            'success' => false,
            'message' => $response->get_error_message()
        );
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['error'])) {
        return array(
            'success' => false,
            'message' => $data['error']['message'] ?? __('Erro desconhecido', 'mistral-chatbot-manager')
        );
    }

    if (isset($data['choices'][0]['message']['content'])) {
        return array(
            'success' => true,
            'data' => $data['choices'][0]['message']['content']
        );
    }

    return array(
        'success' => false,
        'message' => __('Resposta inesperada da API', 'mistral-chatbot-manager')
    );
}
?>