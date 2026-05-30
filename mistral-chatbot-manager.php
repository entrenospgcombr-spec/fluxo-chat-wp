```php
<?php
/**
 * Plugin Name: Mistral Chatbot Manager
 * Plugin URI: https://seusite.com/mistral-chatbot-manager
 * Description: Gerencia integração com API Mistral, fluxos de atendimento e chatbot flutuante.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mistral-chatbot-manager
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('MISTRAL_CHATBOT_MANAGER_VERSION', '1.0.0');
define('MISTRAL_CHATBOT_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MISTRAL_CHATBOT_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once MISTRAL_CHATBOT_MANAGER_PLUGIN_DIR . 'includes/admin-settings.php';
require_once MISTRAL_CHATBOT_MANAGER_PLUGIN_DIR . 'includes/attendance-flow.php';
require_once MISTRAL_CHATBOT_MANAGER_PLUGIN_DIR . 'includes/ai-guidelines.php';
require_once MISTRAL_CHATBOT_MANAGER_PLUGIN_DIR . 'includes/chat-widget.php';
require_once MISTRAL_CHATBOT_MANAGER_PLUGIN_DIR . 'includes/api-integration.php';

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'mistral_chatbot_manager_activate');
register_deactivation_hook(__FILE__, 'mistral_chatbot_manager_deactivate');

/**
 * Plugin activation function
 */
function mistral_chatbot_manager_activate() {
    // Create default options
    add_option('mistral_api_key', '');
    add_option('mistral_chatbot_enabled', '1');
    add_option('mistral_chatbot_position', 'bottom-right');
    add_option('mistral_chatbot_color', '#2563eb');
    add_option('mistral_chatbot_title', 'Como posso ajudar?');
}

/**
 * Plugin deactivation function
 */
function mistral_chatbot_manager_deactivate() {
    // Cleanup if needed
}

/**
 * Enqueue admin scripts and styles
 */
function mistral_chatbot_manager_admin_enqueue_scripts($hook) {
    if (strpos($hook, 'mistral-chatbot') === false) {
        return;
    }

    wp_enqueue_style(
        'mistral-chatbot-admin-css',
        MISTRAL_CHATBOT_MANAGER_PLUGIN_URL . 'assets/css/admin.css',
        array(),
        MISTRAL_CHATBOT_MANAGER_VERSION
    );

    wp_enqueue_script(
        'mistral-chatbot-admin-js',
        MISTRAL_CHATBOT_MANAGER_PLUGIN_URL . 'assets/js/admin.js',
        array('jquery'),
        MISTRAL_CHATBOT_MANAGER_VERSION,
        true
    );

    wp_localize_script('mistral-chatbot-admin-js', 'mistralChatbotAdmin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mistral_chatbot_nonce')
    ));

    // Enqueue Mermaid for flow diagrams
    wp_enqueue_script(
        'mermaid-js',
        'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js',
        array(),
        '10.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'mistral_chatbot_manager_admin_enqueue_scripts');

/**
 * Enqueue frontend scripts and styles
 */
function mistral_chatbot_manager_enqueue_scripts() {
    if (get_option('mistral_chatbot_enabled', '1') !== '1') {
        return;
    }

    wp_enqueue_style(
        'mistral-chatbot-css',
        MISTRAL_CHATBOT_MANAGER_PLUGIN_URL . 'assets/css/chat-widget.css',
        array(),
        MISTRAL_CHATBOT_MANAGER_VERSION
    );

    wp_enqueue_script(
        'mistral-chatbot-js',
        MISTRAL_CHATBOT_MANAGER_PLUGIN_URL . 'assets/js/chat-widget.js',
        array('jquery'),
        MISTRAL_CHATBOT_MANAGER_VERSION,
        true
    );

    wp_localize_script('mistral-chatbot-js', 'mistralChatbot', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mistral_chatbot_nonce'),
        'position' => get_option('mistral_chatbot_position', 'bottom-right'),
        'color' => get_option('mistral_chatbot_color', '#2563eb'),
        'title' => get_option('mistral_chatbot_title', 'Como posso ajudar?')
    ));
}
add_action('wp_enqueue_scripts', 'mistral_chatbot_manager_enqueue_scripts');
?>