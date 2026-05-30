```php
<?php
/**
 * Attendance flow page for Mistral Chatbot Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Flow page content
 */
function mistral_chatbot_manager_flow_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save flow if form was submitted
    if (isset($_POST['mistral_chatbot_save_flow'])) {
        check_admin_referer('mistral_chatbot_flow_nonce');

        $flow_data = isset($_POST['mistral_flow_data']) ? wp_unslash($_POST['mistral_flow_data']) : '';
        update_option('mistral_attendance_flow', $flow_data);

        add_settings_error(
            'mistral_chatbot_messages',
            'mistral_chatbot_flow_saved',
            __('Fluxo de atendimento salvo com sucesso!', 'mistral-chatbot-manager'),
            'success'
        );
    }

    // Get current flow
    $flow_data = get_option('mistral_attendance_flow', mistral_chatbot_get_default_flow());

    ?>
    <div class="wrap mistral-chatbot-flow">
        <h1><?php esc_html_e('Fluxo de Atendimento', 'mistral-chatbot-manager'); ?></h1>

        <?php settings_errors('mistral_chatbot_messages'); ?>

        <div class="mistral-flow-container">
            <div class="mistral-flow-editor">
                <form method="post" action="">
                    <?php wp_nonce_field('mistral_chatbot_flow_nonce'); ?>

                    <div class="mistral-flow-diagram">
                        <div id="mistral-mermaid-diagram" class="mermaid">
                            <?php echo esc_html($flow_data); ?>
                        </div>
                    </div>

                    <div class="mistral-flow-code-editor">
                        <h3><?php esc_html_e('Editar Fluxo (Mermaid Syntax)', 'mistral-chatbot-manager'); ?></h3>
                        <textarea id="mistral_flow_data" name="mistral_flow_data" rows="20" class="large-text"><?php echo esc_textarea($flow_data); ?></textarea>
                        <p class="description">
                            <?php esc_html_e('Edite o fluxo usando a sintaxe Mermaid. Consulte a documentação do Mermaid para mais informações.', 'mistral-chatbot-manager'); ?>
                        </p>
                    </div>

                    <p class="submit">
                        <input type="submit" name="mistral_chatbot_save_flow"
                               class="button button-primary"
                               value="<?php esc_attr_e('Salvar Fluxo', 'mistral-chatbot-manager'); ?>" />
                    </p>
                </form>
            </div>

            <div class="mistral-flow-preview">
                <h3><?php esc_html_e('Visualização', 'mistral-chatbot-manager'); ?></h3>
                <div id="mistral-flow-preview" class="mermaid-preview">
                    <!-- Preview will be rendered here -->
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Get default flow diagram
 */
function mistral_chatbot_get_default_flow() {
    return <<<MERMAID
flowchart TD
    A[Início] --> B{Usuário precisa de ajuda?}
    B -->|Sim| C[Coletar informações básicas]
    B -->|Não| Z[Fim do atendimento]

    C --> D{Usuário é cliente?}
    D -->|Sim| E[Verificar histórico]
    D -->|Não| F[Coletar dados de contato]

    E --> G{Problema identificado?}
    G -->|Sim| H[Oferecer solução]
    G -->|Não| I[Encaminhar para atendente humano]

    F --> J{Interesse em nossos serviços?}
    J -->|Sim| K[Agendar contato com equipe comercial]
    J -->|Não| L[Oferecer materiais informativos]

    H --> M{Usuário satisfeito?}
    M -->|Sim| N[Encerrar atendimento]
    M -->|Não| I

    K --> N
    L --> N
    I --> N
MERMAID;
}
?>