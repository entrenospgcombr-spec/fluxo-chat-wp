```javascript
jQuery(document).ready(function($) {
    // Get widget elements
    var $widget = $('#mistral-chatbot-widget');
    var $toggle = $('.mistral-chatbot-toggle');
    var $close = $('.mistral-chatbot-close');
    var $messages = $('.mistral-chatbot-messages');
    var $input = $('.mistral-chatbot-input-field');
    var $send = $('.mistral-chatbot-send');

    // Set widget color
    var color = $widget.data('color');
    $widget.css('--mistral-color', color);

    // Toggle widget visibility
    $toggle.on('click', function() {
        $widget.toggleClass('closed');
        $toggle.toggleClass('closed');
    });

    $close.on('click', function() {
        $widget.addClass('closed');
        $toggle.removeClass('closed');
    });

    // Handle message sending
    function sendMessage() {
        var message = $input.val().trim();
        if (message === '') return;

        // Add user message to chat
        $messages.append('<div class="mistral-chatbot-message mistral-user-message">' + message + '</div>');
        $input.val('');
        scrollToBottom();

        // Show typing indicator
        $messages.append('<div class="mistral-chatbot-message mistral-bot-message mistral-typing"><span></span><span></span><span></span></div>');
        scrollToBottom();

        // Send message to server
        $.ajax({
            url: mistralChatbot.ajax_url,
            type: 'POST',
            data: {
                action: 'mistral_chatbot_message',
                message: message,
                nonce: mistralChatbot.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Remove typing indicator
                    $('.mistral-typing').remove();

                    // Add bot response
                    $messages.append('<div class="mistral-chatbot-message mistral-bot-message">' + response.data.message + '</div>');
                    scrollToBottom();
                } else {
                    $('.mistral-typing').remove();
                    $messages.append('<div class="mistral-chatbot-message mistral-bot-message">Desculpe, ocorreu um erro: ' + response.data + '</div>');
                    scrollToBottom();
                }
            },
            error: function() {
                $('.mistral-typing').remove();
                $messages.append('<div class="mistral-chatbot-message mistral-bot-message">Desculpe, ocorreu um erro ao processar sua mensagem.</div>');
                scrollToBottom();
            }
        });
    }

    // Send message on button click
    $send.on('click', sendMessage);

    // Send message on Enter key
    $input.on('keypress', function(e) {
        if (e.which === 13) {
            sendMessage();
        }
    });

    // Scroll to bottom of messages
    function scrollToBottom() {
        $messages.scrollTop($messages[0].scrollHeight);
    }

    // Initial scroll to bottom
    scrollToBottom();
});