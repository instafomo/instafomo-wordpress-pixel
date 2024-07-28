<?php
/*
Plugin Name: Instafomo WordPress Pixel
Description: Inserts a pixel script in the head tag of the website.
Version: 1.0
Author: Instafomo
Author URI: https://instafomo.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: instafomo-wordpress-pixel
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin URL
define('INSTAFOMO_PIXEL_URL', plugin_dir_url(__FILE__));
define('INSTAFOMO_PIXEL_PATH', plugin_dir_path(__FILE__));

// Include required files
require_once INSTAFOMO_PIXEL_PATH . 'includes/settings.php';
require_once INSTAFOMO_PIXEL_PATH . 'admin/admin-page.php';
require_once INSTAFOMO_PIXEL_PATH . 'sync/sync-campaigns.php';

// Add pixel script to head
add_action('wp_head', 'instafomo_add_pixel_script');

function instafomo_add_pixel_script() {
    $campaigns = get_option('instafomo_campaigns');
    if ($campaigns && is_array($campaigns)) {
        foreach ($campaigns as $campaign) {
            echo '<!-- Pixel Code - https://instafomo.com/ -->';
            echo '<script defer src="https://instafomo.com/pixel/' . esc_attr($campaign['pixel_key']) . '"></script>';
            echo '<!-- END Pixel Code -->';
        }
    }
}

// Sync campaigns on plugin activation
register_activation_hook(__FILE__, 'instafomo_sync_campaigns');
