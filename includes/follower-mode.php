<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// In Follower mode: fetch leader values from the Leader URL (with 5 minutes caching)
function SOCPUL_get_leader_value($key) {
    $options = get_option('SOCPUL_options');
    if ( empty($options['leader_url']) ) {
        return 'Leader URL not configured.';
    }
    $leader_values = get_transient('SOCPUL_leader_values');
    if ( false === $leader_values ) {
        $response = wp_remote_get( $options['leader_url'] );
        if ( is_wp_error($response) ) {
            return 'Error fetching leader values.';
        }
        $body = wp_remote_retrieve_body($response);
        $leader_values = json_decode($body, true);
        if ( ! is_array($leader_values) ) {
            return 'Invalid leader response.';
        }
        set_transient('SOCPUL_leader_values', $leader_values, 300); // Cache for 5 minutes
    }
    return isset($leader_values[$key]) ? $leader_values[$key] : 'N/A';
}

// AJAX callback for testing the Leader URL (Follower mode)
function SOCPUL_test_leader_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    $options = get_option('SOCPUL_options');
    $leader_url = isset($options['leader_url']) ? trim($options['leader_url']) : '';
    if ( empty($leader_url) ) {
        wp_send_json(array('message' => 'Leader URL is missing.'));
    }
    $response = wp_remote_get($leader_url);
    if ( is_wp_error($response) ) {
        wp_send_json(array('message' => 'Error fetching leader data.'));
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if ( ! is_array($data) ) {
        wp_send_json(array('message' => 'Invalid leader response.'));
    }
    // Build a detailed message with actual values
    $message = 'Leader data: YouTube: ' . $data['youtube'] . ', Steam: ' . $data['steam'] . ', Facebook: ' . $data['facebook'] . ', X: ' . $data['x'];
    wp_send_json(array('message' => $message));
}
add_action('wp_ajax_SOCPUL_test_leader_api', 'SOCPUL_test_leader_api_callback');
