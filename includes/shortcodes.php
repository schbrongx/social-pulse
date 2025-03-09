<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sp_youtube_counter_shortcode() {
    $options = get_option( 'sp_options' );

    // Check if YouTube Counter is activated
    if ( ! isset( $options['youtube_active'] ) || $options['youtube_active'] != 1 ) {
        return 'YouTube Counter is not activated.';
    }
    
    // Retrieve API key and channel ID from options
    $api_key    = isset( $options['youtube_api_key'] ) ? trim( $options['youtube_api_key'] ) : '';
    $channel_id = isset( $options['youtube_channel_id'] ) ? trim( $options['youtube_channel_id'] ) : '';
    
    if ( empty( $api_key ) || empty( $channel_id ) ) {
        return 'API Key or Channel ID is not configured.';
    }
    
    // Check for cached subscriber count
    $transient_key = 'sp_youtube_counter_value';
    $subscriberCount = get_transient( $transient_key );
    
    if ( false === $subscriberCount ) {
        // No cache available; perform API request to retrieve YouTube data
        $api_url = add_query_arg( array(
            'part' => 'statistics',
            'id'   => $channel_id,
            'key'  => $api_key
        ), 'https://www.googleapis.com/youtube/v3/channels' );
        
        $response = wp_remote_get( $api_url );
        if ( is_wp_error( $response ) ) {
            return 'Error retrieving YouTube data.';
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( ! isset( $data['items'][0]['statistics']['subscriberCount'] ) ) {
            return 'No subscriber data found.';
        }
        
        $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
        
        // Determine refresh interval (default: 12 hours)
        $refresh_hours = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
        $refresh_seconds = $refresh_hours * 3600;
        
        // Cache the subscriber count
        set_transient( $transient_key, $subscriberCount, $refresh_seconds );
        
        // Update options with last fetch time and value
        $options['last_fetch_time'] = current_time('mysql');
        $options['last_fetch_value'] = $subscriberCount;
        update_option( 'sp_options', $options );
    }
    
    // Format and return the subscriber count
    return number_format_i18n( $subscriberCount );
}
add_shortcode( 'counter_youtube', 'sp_youtube_counter_shortcode' );

function sp_steam_counter_shortcode() {
    $options = get_option( 'sp_options' );
    
    // Check if Steam Counter is activated
    if ( ! isset( $options['steam_active'] ) || $options['steam_active'] != 1 ) {
        return 'Steam Counter is not activated.';
    }
    
    // Retrieve Steam App ID from options
    $app_id = isset( $options['steam_app_id'] ) ? trim( $options['steam_app_id'] ) : '';
    if ( empty( $app_id ) ) {
        return 'Steam App ID is not configured.';
    }
    
    // Check for cached player count
    $transient_key = 'sp_steam_counter_value';
    $playerCount = get_transient( $transient_key );
    
    if ( false === $playerCount ) {
        // No cache available; perform API request to retrieve Steam data
        $api_url = add_query_arg( array( 'appid' => $app_id ), 'https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/' );
        $response_wp = wp_remote_get( $api_url );
        if ( is_wp_error( $response_wp ) ) {
            return 'Error retrieving Steam data.';
        }
        
        $body = wp_remote_retrieve_body( $response_wp );
        $data = json_decode( $body, true );
        
        if ( ! isset( $data['response']['player_count'] ) ) {
            return 'No player count found.';
        }
        
        $playerCount = $data['response']['player_count'];
        $refresh_hours = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
        $refresh_seconds = $refresh_hours * 3600;
        set_transient( $transient_key, $playerCount, $refresh_seconds );
        
        // Update options with last fetch time and value
        $options['steam_last_fetch_time'] = current_time('mysql');
        $options['steam_last_fetch_value'] = $playerCount;
        update_option( 'sp_options', $options );
    }
    
    // Format and return the player count
    return number_format_i18n( $playerCount );
}
add_shortcode( 'counter_steam', 'sp_steam_counter_shortcode' );

function sp_facebook_counter_shortcode() {
    $options = get_option( 'sp_options' );
    
    // Check if Facebook Counter is activated
    if ( ! isset( $options['facebook_active'] ) || $options['facebook_active'] != 1 ) {
        return 'Facebook Counter is not activated.';
    }
    
    // Retrieve Facebook Page ID, Access Token, and metric type from options
    $page_id = isset( $options['facebook_page_id'] ) ? trim( $options['facebook_page_id'] ) : '';
    $access_token = isset( $options['facebook_access_token'] ) ? trim( $options['facebook_access_token'] ) : '';
    $metric = isset($options['facebook_metric']) ? $options['facebook_metric'] : 'fan'; // default: fan
    
    if ( empty( $page_id ) || empty( $access_token ) ) {
        return 'Facebook Page ID or Access Token is not configured.';
    }
    
    // Check for cached fan/follower count
    $transient_key = 'sp_facebook_counter_value';
    $value = get_transient( $transient_key );
    
    if ( false === $value ) {
        // Select the appropriate field based on metric
        $field = ($metric === 'follower') ? 'followers_count' : 'fan_count';
        $api_url = 'https://graph.facebook.com/v10.0/' . $page_id . '?fields=' . $field . '&access_token=' . $access_token;
        $response_wp = wp_remote_get( $api_url );
        if ( is_wp_error( $response_wp ) ) {
            return 'Error retrieving Facebook data.';
        }
        
        $body = wp_remote_retrieve_body( $response_wp );
        $data = json_decode( $body, true );
        
        if ( ! isset( $data[$field] ) ) {
            return 'No fan/follower count found.';
        }
        
        $value = $data[$field];
        $refresh_hours = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
        $refresh_seconds = $refresh_hours * 3600;
        set_transient( $transient_key, $value, $refresh_seconds );
        
        // Update options with last fetch time and value
        $options['facebook_last_fetch_time'] = current_time('mysql');
        $options['facebook_last_fetch_value'] = $value;
        update_option( 'sp_options', $options );
    }
    
    // Format and return the fan/follower count
    return number_format_i18n( $value );
}
add_shortcode( 'counter_facebook', 'sp_facebook_counter_shortcode' );

function sp_x_counter_shortcode() {
    $options = get_option( 'sp_options' );
    
    // Check if X Follower Counter is activated
    if ( ! isset( $options['x_active'] ) || $options['x_active'] != 1 ) {
        return 'X Follower Counter is not activated.';
    }
    
    // Retrieve username and bearer token from options
    $username = isset( $options['x_username'] ) ? trim( $options['x_username'] ) : '';
    $bearer_token = isset( $options['x_bearer_token'] ) ? trim( $options['x_bearer_token'] ) : '';
    
    if ( empty( $username ) || empty( $bearer_token ) ) {
        return 'X username or Bearer Token is not configured.';
    }
    
    // Check for cached follower count
    $transient_key = 'sp_x_counter_value';
    $followers_count = get_transient( $transient_key );
    
    // If no cache exists, proceed with API request (with rate limit check)
    if ( false === $followers_count ) {
        $request_data = sp_get_x_request_data();
        if ( $request_data['count'] >= 25 ) {
            return 'Request limit reached (25 per 24 hours). Please wait.';
        }
        sp_increment_x_request_count();
        
        $api_url = 'https://api.x.com/2/users/by/username/' . $username . '?user.fields=public_metrics';
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $bearer_token,
            ),
        );
        $response_wp = wp_remote_get( $api_url, $args );
        if ( is_wp_error( $response_wp ) ) {
            return 'Error retrieving X data.';
        }
        
        $body = wp_remote_retrieve_body( $response_wp );
        $data = json_decode( $body, true );
        
        if ( ! isset( $data['data']['public_metrics']['followers_count'] ) ) {
            return 'No follower count found.';
        }
        
        $followers_count = $data['data']['public_metrics']['followers_count'];
        $refresh_hours = isset($options['x_refresh_interval']) ? intval($options['x_refresh_interval']) : 12;
        $refresh_seconds = $refresh_hours * 3600;
        set_transient( $transient_key, $followers_count, $refresh_seconds );
        
        // Update options with last fetch time and value
        $options['x_last_fetch_time'] = current_time('mysql');
        $options['x_last_fetch_value'] = $followers_count;
        update_option( 'sp_options', $options );
    }
    
    // Format and return the follower count
    return number_format_i18n( $followers_count );
}
add_shortcode( 'counter_x', 'sp_x_counter_shortcode' );
