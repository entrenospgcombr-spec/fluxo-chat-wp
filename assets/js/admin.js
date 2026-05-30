```javascript
jQuery(document).ready(function($) {
    // Initialize color picker
    $('.mistral-color-picker').wpColorPicker();

    // Test API connection when button is clicked
    $('input[name="mistral_chatbot_test_connection"]').on('click', function(e) {
        e.preventDefault();

        var apiKey = $('#mistral_api_key').val();
        var nonce = mistralChatbotAdmin.nonce;

        $.ajax({
            url: mistralChatbotAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'mistral_chatbot_test_api',
                api_key: apiKey,
                nonce: nonce
            },
            beforeSend: function() {
                $('input[name="mistral_chatbot_test_connection"]').prop('disabled', true).val('Testando...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Conexão com a API testada com sucesso!');
                } else {
                    alert('Erro: ' + response.data);
                }
            },
            error: function() {
                alert('Ocorreu um erro ao testar a conexão.');
            },
            complete: function() {
                $('input[name="mistral_chatbot_test_connection"]').prop('disabled', false).val('Testar Conexão com API');
            }
        });
    });

    // Initialize Mermaid for flow diagrams
    if (typeof mermaid !== 'undefined') {
        mermaid.initialize({
            startOnLoad: false,
            theme: 'default'
        });

        // Render the main diagram
        try {
            mermaid.init(undefined, $('#mistral-mermaid-diagram'));
        } catch (e) {
            console.error('Erro ao renderizar diagrama Mermaid:', e);
        }

        // Preview on textarea change
        $('#mistral_flow_data').on('input', function() {
            var flowData = $(this).val();
            $('#mistral-flow-preview').html(flowData);

            try {
                mermaid.init(undefined, $('#mistral-flow-preview'));
            } catch (e) {
                console.error('Erro ao renderizar pré-visualização:', e);
            }
        });
    }

    // AJAX handler for testing API
    $(document).on('click', 'input[name="mistral_chatbot_test_connection"]', function(e) {
        e.preventDefault();

        var apiKey = $('#mistral_api_key').val();
        var nonce = mistralChatbotAdmin.nonce;

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mistral_chatbot_test_api',
                api_key: apiKey,
                _wpnonce: nonce
            },
            beforeSend: function() {
                $('input[name="mistral_chatbot_test_connection"]').prop('disabled', true).val('Testando...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                } else {
                    alert('Erro: ' + response.data);
                }
            },
            error: function() {
                alert('Ocorreu um erro ao testar a conexão.');
            },
            complete: function() {
                $('input[name="mistral_chatbot_test_connection"]').prop('disabled', false).val('Testar Conexão com API');
            }
        });
    });
});