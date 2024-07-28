<?php

function instafomo_sync_campaigns() {
    $api_key = get_option('instafomo_api_key');
    if ($api_key) {
        $response = wp_remote_get('https://instafomo.com/api/campaigns/', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
            ),
        ));

        if (is_array($response) && !is_wp_error($response)) {
            $body = json_decode($response['body'], true);
            if (isset($body['data'])) {
                update_option('instafomo_campaigns', $body['data']);
            } else {
                update_option('instafomo_campaigns', []);
            }
        } else {
            update_option('instafomo_campaigns', []);
        }
    } else {
        update_option('instafomo_campaigns', []);
    }
}
