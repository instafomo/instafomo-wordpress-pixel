<?php
/**
 * Plugin Name: Instafomo WP Pixel
 * Plugin URI: https://instafomo.com
 * Description: Adds Instafomo pixel tracking to your WordPress site.
 * Version: 1.0
 * Author: Instafomo
 * Author URI: https://instafomo.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: instafomo
 */

// Enqueue admin scripts and styles
function instafomo_enqueue_admin_scripts() {
    wp_enqueue_script( 'instafomo-admin-script', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'instafomo-admin-style', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), '1.0' );
}
add_action( 'admin_enqueue_scripts', 'instafomo_enqueue_admin_scripts' );

// Create menu item
function instafomo_create_menu() {
    add_menu_page(
        __( 'Instafomo Pixel Settings', 'instafomo' ),
        __( 'Instafomo Pixel', 'instafomo' ),
        'manage_options',
        'instafomo-pixel-settings',
        'instafomo_settings_page',
        plugin_dir_url( __FILE__ ) . 'assets/logo.png'
    );
}
add_action( 'admin_menu', 'instafomo_create_menu' );

// Settings page
function instafomo_settings_page() {
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_POST['instafomo_nonce'] ) && wp_verify_nonce( $_POST['instafomo_nonce'], 'instafomo_save_settings' ) ) {
        $api_key = sanitize_text_field( $_POST['instafomo_api_key'] );
        update_option( 'instafomo_api_key', $api_key );
        instafomo_sync_campaigns();
    }

    $api_key = get_option( 'instafomo_api_key' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Instafomo Pixel Settings', 'instafomo' ); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field( 'instafomo_save_settings', 'instafomo_nonce' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'API Key', 'instafomo' ); ?></th>
                    <td>
                        <input type="text" name="instafomo_api_key" value="<?php echo esc_attr( $api_key ); ?>" required />
                        <p class="description"><?php esc_html_e( 'You can find your API key by clicking', 'instafomo' ); ?> <a href="https://instafomo.com/login?redirect=account-api" target="_blank"><?php esc_html_e( 'here', 'instafomo' ); ?></a>.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Save Settings', 'instafomo' ) ); ?>
        </form>
        <h2><?php esc_html_e( 'Campaigns', 'instafomo' ); ?></h2>
        <button id="sync-campaigns" class="button button-primary"><?php esc_html_e( 'Sync Campaigns', 'instafomo' ); ?></button>
        <div id="campaigns-list">
            <?php instafomo_display_campaigns(); ?>
        </div>
    </div>
    <?php
}

// Sync campaigns function
function instafomo_sync_campaigns() {
    $api_key = get_option( 'instafomo_api_key' );

    if ( empty( $api_key ) ) {
        return;
    }

    $response = wp_remote_get( 'https://instafomo.com/api/campaigns/', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . esc_attr( $api_key )
        )
    ) );

    if ( is_wp_error( $response ) ) {
        return;
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( isset( $data['data'] ) ) {
        update_option( 'instafomo_campaigns', $data['data'] );
    }
}

// Display campaigns function
function instafomo_display_campaigns() {
    $campaigns = get_option( 'instafomo_campaigns', array() );

    if ( empty( $campaigns ) ) {
        echo '<p>' . esc_html__( 'No campaigns found.', 'instafomo' ) . '</p>';
        return;
    }

    echo '<ul>';
    foreach ( $campaigns as $campaign ) {
        echo '<li>' . esc_html( $campaign['name'] ) . ' - <code>&lt;script defer src="https://instafomo.com/pixel/' . esc_attr( $campaign['pixel_key'] ) . '"&gt;&lt;/script&gt;</code></li>';
    }
    echo '</ul>';
}

// Enqueue frontend scripts
function instafomo_enqueue_scripts() {
    $campaigns = get_option( 'instafomo_campaigns', array() );

    foreach ( $campaigns as $campaign ) {
        wp_enqueue_script( 'instafomo-pixel-' . esc_attr( $campaign['id'] ), 'https://instafomo.com/pixel/' . esc_attr( $campaign['pixel_key'] ), array(), null, true );
    }
}
add_action( 'wp_enqueue_scripts', 'instafomo_enqueue_scripts' );
