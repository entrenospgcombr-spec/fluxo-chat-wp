```php
<?php
/**
 * AI Guidelines page for Mistral Chatbot Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Guidelines page content
 */
function mistral_chatbot_manager_guidelines_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save guidelines if form was submitted
    if (isset($_POST['mistral_chatbot_save_guidelines'])) {
        check_admin_referer('mistral_chatbot_guidelines_nonce');

        $guidelines = isset($_POST['mistral_guidelines']) ? wp_unslash($_POST['mistral_guidelines']) : '';
        update_option('mistral_ai_guidelines', $guidelines);

        add_settings_error(
            'mistral_chatbot_messages',
            'mistral_chatbot_guidelines_saved',
            __('Diretrizes salvas com sucesso!', 'mistral-chatbot-manager'),
            'success'
        );
    }

    // Get current guidelines
    $guidelines = get_option('mistral_ai_guidelines', mistral_chatbot_get_default_guidelines());

    ?>
    <div class="wrap mistral-chatbot-guidelines">
        <h1><?php esc_html_e('Diretrizes para Agentes de IA', 'mistral-chatbot-manager'); ?></h1>

        <?php settings_errors('mistral_chatbot_messages'); ?>

        <form method="post" action="">
            <?php wp_nonce_field('mistral_chatbot_guidelines_nonce'); ?>

            <div class="mistral-guidelines-editor">
                <h3><?php esc_html_e('Regras e Comportamentos', 'mistral-chatbot-manager'); ?></h3>
                <p class="description">
                    <?php esc_html_e('Defina as diretrizes que os agentes de IA devem seguir durante as interações com os usuários.', 'mistral-chatbot-manager'); ?>
                </p>

                <textarea id="mistral_guidelines" name="mistral_guidelines" rows="20" class="large-text"><?php echo esc_textarea($guidelines); ?></textarea>

                <div class="mistral-guidelines-examples">
                    <h4><?php esc_html_e('Exemplos de Diretrizes', 'mistral-chatbot-manager'); ?></h4>
                    <ul>
                        <li><?php esc_html_e('Sempre seja educado e profissional', 'mistral-chatbot-manager'); ?></li>
                        <li><?php esc_html_e('Não forneça informações confidenciais', 'mistral-chatbot-manager'); ?></li>
                        <li><?php esc_html_e('Encaminhe para um humano quando não souber responder', 'mistral-chatbot-manager'); ?></li>
                        <li><?php esc_html_e('Mantenha o tom de voz da marca', 'mistral-chatbot-manager'); ?></li>
                    </ul>
                </div>
            </div>

            <p class="submit">
                <input type="submit" name="mistral_chatbot_save_guidelines"
                       class="button button-primary"
                       value="<?php esc_attr_e('Salvar Diretrizes', 'mistral-chatbot-manager'); ?>" />
            </p>
        </form>
    </div>
    <?php
}

/**
 * Get default guidelines
 */
function mistral_chatbot_get_default_guidelines() {
    return <<<GUIDELINES
1. **Tom de Voz**:
   - Seja sempre educado, profissional e amigável
   - Use linguagem clara e simples, evitando jargões técnicos
   - Mantenha o tom consistente com a marca

2. **Limitações**:
   - Não forneça informações confidenciais ou sensíveis
   - Não faça promessas que não possam ser cumpridas
   - Não tente resolver problemas complexos que exijam intervenção humana

3. **Encaminhamento**:
   - Sempre ofereça a opção de falar com um atendente humano
   - Encaminhe para um humano quando:
     * O usuário solicitar explicitamente
     * O problema não puder ser resolvido pelo chatbot
     * O usuário demonstrar frustração

4. **Coleta de Informações**:
   - Sempre peça permissão antes de coletar dados pessoais
   - Explique claramente como as informações serão usadas
   - Forneça opção de não compartilhar informações

5. **Respostas Padrão**:
   - Saudações: "Olá! Como posso te ajudar hoje?"
   - Despedidas: "Foi um prazer ajudar! Estou à disposição se precisar de mais alguma coisa."
   - Não entendeu: "Desculpe, não entendi sua pergunta. Poderia reformular?"
GUIDELINES;
}
?>