<?php

// Register settings
add_action('admin_init', 'instafomo_settings_init');

function instafomo_settings_init() {
    register_setting('instafomoSettings', 'instafomo_api_key', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ));

    add_settings_section(
        'instafomo_section',
        __('Instafomo Settings', 'wordpress'),
        'instafomo_settings_section_callback',
        'instafomoSettings'
    );

    add_settings_field(
        'instafomo_api_key',
        __('API Key', 'wordpress'),
        'instafomo_api_key_render',
        'instafomoSettings',
        'instafomo_section'
    );
}

function instafomo_api_key_render() {
    $options = get_option('instafomo_api_key');
    ?>
    <input type='text' name='instafomo_api_key' value='<?php echo esc_attr($options); ?>' required>
    <p>You can find your API key by clicking <a href="https://instafomo.com/login?redirect=account-api" target="_blank">here</a>.</p>
    <p><strong>About Instafomo:</strong></p>
    <p>Instafomo is a powerful tool that helps you increase conversions by displaying real-time social proof notifications on your website.</p>
    <p><strong>Key Benefits:</strong></p>
    <ul>
        <li>Boosts trust and credibility</li>
        <li>Increases conversion rates</li>
        <li>Easy to set up and use</li>
        <li>Customizable notifications</li>
    </ul>
    <?php
}

function instafomo_settings_section_callback() {
    echo __('Enter your Instafomo API key below.', 'wordpress');
}
