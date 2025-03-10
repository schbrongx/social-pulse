<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// -----------------------------------------------------------------------------
// Leader Endpoint Registration (Leader Mode exposes values at /social-counters/values)
// -----------------------------------------------------------------------------
function sp_register_leader_endpoint() {
    add_rewrite_rule('^social-counters/values/?$', 'index.php?sp_leader_values=1', 'top');
}
add_action('init', 'sp_register_leader_endpoint');

function sp_leader_query_vars( $vars ) {
    $vars[] = 'sp_leader_values';
    return $vars;
}
add_filter( 'query_vars', 'sp_leader_query_vars' );

function sp_leader_template_redirect() {
    if ( get_query_var('sp_leader_values') == 1 ) {
        header('Content-Type: application/json');
        $values = array(
            'youtube'  => sp_youtube_counter_get_value(),
            'steam'    => sp_steam_counter_get_value(),
            'facebook' => sp_facebook_counter_get_value(),
            'x'        => sp_x_counter_get_value(),
        );
        echo json_encode($values);
        exit;
    }
}
add_action( 'template_redirect', 'sp_leader_template_redirect' );

// -----------------------------------------------------------------------------
// Leader Mode - API functions (used in shortcodes when in Leader mode)
// -----------------------------------------------------------------------------
function sp_youtube_counter_get_value() {
    $options = get_option('sp_options');
    $transient_key = 'sp_youtube_counter_value';
    $subscriberCount = get_transient($transient_key);
    if ( false === $subscriberCount ) {
        $api_key    = isset($options['youtube_api_key']) ? trim($options['youtube_api_key']) : '';
        $channel_id = isset($options['youtube_channel_id']) ? trim($options['youtube_channel_id']) : '';
        if ( empty($api_key) || empty($channel_id) ) return 0;
        $api_url = add_query_arg( array(
            'part' => 'statistics',
            'id'   => $channel_id,
            'key'  => $api_key
        ), 'https://www.googleapis.com/youtube/v3/channels' );
        $response = wp_remote_get($api_url);
        if ( is_wp_error($response) ) return 0;
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body,true);
        if ( ! isset($data['items'][0]['statistics']['subscriberCount']) ) return 0;
        $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
        $refresh_hours = isset($options['youtube_refresh_interval']) ? intval($options['youtube_refresh_interval']) : 12;
        set_transient($transient_key, $subscriberCount, $refresh_hours * 3600);
    }
    return number_format_i18n($subscriberCount);
}

function sp_steam_counter_get_value() {
    $options = get_option('sp_options');
    $transient_key = 'sp_steam_counter_value';
    $playerCount = get_transient($transient_key);
    if ( false === $playerCount ) {
        $app_id = isset($options['steam_app_id']) ? trim($options['steam_app_id']) : '';
        if ( empty($app_id) ) return 0;
        $api_url = add_query_arg( array( 'appid' => $app_id ), 'https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/' );
        $response = wp_remote_get($api_url);
        if ( is_wp_error($response) ) return 0;
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body,true);
        if ( ! isset($data['response']['player_count']) ) return 0;
        $playerCount = $data['response']['player_count'];
        $refresh_hours = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
        set_transient($transient_key, $playerCount, $refresh_hours * 3600);
    }
    return number_format_i18n($playerCount);
}

function sp_facebook_counter_get_value() {
    $options = get_option('sp_options');
    $transient_key = 'sp_facebook_counter_value';
    $value = get_transient($transient_key);
    if ( false === $value ) {
        $page_id = isset($options['facebook_page_id']) ? trim($options['facebook_page_id']) : '';
        $access_token = isset($options['facebook_access_token']) ? trim($options['facebook_access_token']) : '';
        $metric = isset($options['facebook_metric']) ? $options['facebook_metric'] : 'fan';
        if ( empty($page_id) || empty($access_token) ) return 0;
        $field = ($metric === 'follower') ? 'followers_count' : 'fan_count';
        $api_url = 'https://graph.facebook.com/v22.0/' . $page_id . '?fields=' . $field . '&access_token=' . $access_token;
        $response = wp_remote_get($api_url);
        if ( is_wp_error($response) ) return 0;
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body,true);
        if ( ! isset($data[$field]) ) return 0;
        $value = $data[$field];
        $refresh_hours = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
        set_transient($transient_key, $value, $refresh_hours * 3600);
    }
    return number_format_i18n($value);
}

function sp_x_counter_get_value() {
    $options = get_option('sp_options');
    $transient_key = 'sp_x_counter_value';
    $followers_count = get_transient($transient_key);
    if ( false === $followers_count ) {
        $username = isset($options['x_username']) ? trim($options['x_username']) : '';
        $bearer_token = isset($options['x_bearer_token']) ? trim($options['x_bearer_token']) : '';
        if ( empty($username) || empty($bearer_token) ) return 0;
        // (Rate limit checks omitted for brevity)
        $api_url = 'https://api.x.com/2/users/by/username/' . $username . '?user.fields=public_metrics';
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $bearer_token,
            ),
        );
        $response = wp_remote_get($api_url, $args);
        if ( is_wp_error($response) ) return 0;
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body,true);
        if ( ! isset($data['data']['public_metrics']['followers_count']) ) return 0;
        $followers_count = $data['data']['public_metrics']['followers_count'];
        $refresh_hours = isset($options['x_refresh_interval']) ? intval($options['x_refresh_interval']) : 12;
        set_transient($transient_key, $followers_count, $refresh_hours * 3600);
    }
    return number_format_i18n($followers_count);
}

?>
