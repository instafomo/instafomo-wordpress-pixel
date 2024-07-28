<?php

// Add settings menu and submenu
add_action('admin_menu', 'instafomo_add_admin_menu');

function instafomo_add_admin_menu() {
    add_menu_page(
        'Instafomo Pixel',
        'Instafomo Pixel',
        'manage_options',
        'instafomo_pixel',
        'instafomo_admin_page',
        'dashicons-chart-line'
    );
}

function instafomo_admin_page() {
    ?>
    <div class="wrap">
        <h1>
            <img src="<?php echo INSTAFOMO_PIXEL_URL; ?>assets/instafomo-logo.png" alt="Instafomo Logo" style="max-height: 50px; vertical-align: middle; margin-right: 10px;">
        </h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=instafomo_pixel&tab=settings" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
            <a href="?page=instafomo_pixel&tab=campaigns" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'campaigns' ? 'nav-tab-active' : ''; ?>">Campaigns</a>
        </h2>
        <div class="tab-content">
            <?php
            $tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
            switch ($tab) {
                case 'campaigns':
                    instafomo_campaigns_page();
                    break;
                case 'settings':
                default:
                    instafomo_settings_page();
                    break;
            }
            ?>
        </div>
    </div>
    <?php
}

function instafomo_settings_page() {
    ?>
    <form action='options.php' method='post'>
        <?php
        settings_fields('instafomoSettings');
        do_settings_sections('instafomoSettings');
        submit_button();
        ?>
    </form>
    <button class="button button-primary" onclick="location.href='<?php echo admin_url('admin.php?page=instafomo_pixel&tab=campaigns&sync=1'); ?>'">Sync Campaigns</button>
    <?php
}

function instafomo_campaigns_page() {
    if (isset($_GET['sync']) && $_GET['sync'] == 1) {
        instafomo_sync_campaigns();
        echo '<div class="notice notice-success is-dismissible"><p>Campaigns synchronized successfully.</p></div>';
    }
    ?>
    <div class="wrap">
        <h2>Campaigns</h2>
        <?php
        $campaigns = get_option('instafomo_campaigns');
        if ($campaigns && is_array($campaigns)) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>ID</th><th>Name</th><th>Domain</th><th>Pixel Key</th></tr></thead>';
            echo '<tbody>';
            foreach ($campaigns as $campaign) {
                echo '<tr>';
                echo '<td>' . esc_html($campaign['id']) . '</td>';
                echo '<td>' . esc_html($campaign['name']) . '</td>';
                echo '<td>' . esc_html($campaign['domain']) . '</td>';
                echo '<td>' . esc_html($campaign['pixel_key']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No campaigns found. Please sync campaigns.</p>';
        }
        ?>
    </div>
    <?php
}
