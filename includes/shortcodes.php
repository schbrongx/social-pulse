<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sp_youtube_counter_shortcode() {
    $options = get_option( 'sp_options' );

    // Prüfen, ob der YouTube Counter aktiviert ist
    if ( ! isset( $options['youtube_active'] ) || $options['youtube_active'] != 1 ) {
        return 'YouTube Counter ist nicht aktiviert.';
    }
    
    // Überprüfen, ob API-Key und Channel ID gesetzt sind
    $api_key    = isset( $options['youtube_api_key'] ) ? trim( $options['youtube_api_key'] ) : '';
    $channel_id = isset( $options['youtube_channel_id'] ) ? trim( $options['youtube_channel_id'] ) : '';
    
    if ( empty( $api_key ) || empty( $channel_id ) ) {
        return 'API Key oder Channel ID nicht konfiguriert.';
    }
    
    // Prüfen, ob ein gecachter Wert vorliegt
    $transient_key = 'sp_youtube_counter_value';
    $subscriberCount = get_transient( $transient_key );
    
    if ( false === $subscriberCount ) {
        // Kein Cache vorhanden, API-Aufruf durchführen
        $api_url = add_query_arg( array(
            'part'       => 'statistics',
            'id'         => $channel_id,
            'key'        => $api_key
        ), 'https://www.googleapis.com/youtube/v3/channels' );
        
        $response = wp_remote_get( $api_url );
        
        if ( is_wp_error( $response ) ) {
            return 'Fehler beim Abrufen der YouTube-Daten.';
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( ! isset( $data['items'][0]['statistics']['subscriberCount'] ) ) {
            return 'Keine Abonnentendaten gefunden.';
        }
        
        $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
        
        // Holen Sie sich das in Stunden angegebene Refresh-Intervall, Standard: 12h
        $refresh_hours = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
        $refresh_seconds = $refresh_hours * 3600;
        
        // Wert im Transient speichern
        set_transient( $transient_key, $subscriberCount, $refresh_seconds );
        
        // Speichern des letzten Abrufzeitpunkts und Wertes in den Optionen (zur Anzeige in den Settings)
        $options['last_fetch_time'] = current_time('mysql');
        $options['last_fetch_value'] = $subscriberCount;
        update_option( 'sp_options', $options );
    }
    
    // Rückgabe des (gecachedten) Wertes
    return number_format_i18n( $subscriberCount );
}

// Registrierung des Shortcodes
add_shortcode( 'counter_youtube', 'sp_youtube_counter_shortcode' );

function sp_steam_counter_shortcode() {
    $options = get_option( 'sp_options' );
    
    // Prüfen, ob der Steam Counter aktiviert ist
    if ( ! isset( $options['steam_active'] ) || $options['steam_active'] != 1 ) {
        return 'Steam Counter ist nicht aktiviert.';
    }
    
    $app_id = isset( $options['steam_app_id'] ) ? trim( $options['steam_app_id'] ) : '';
    if ( empty( $app_id ) ) {
        return 'Steam App ID nicht konfiguriert.';
    }
    
    $transient_key = 'sp_steam_counter_value';
    $playerCount = get_transient( $transient_key );
    
    if ( false === $playerCount ) {
        // Kein gültiger Cache vorhanden – API-Aufruf
        $api_url = add_query_arg( array( 'appid' => $app_id ), 'https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/' );
        
        $response_wp = wp_remote_get( $api_url );
        if ( is_wp_error( $response_wp ) ) {
            return 'Fehler beim Abrufen der Steam-Daten.';
        }
        
        $body = wp_remote_retrieve_body( $response_wp );
        $data = json_decode( $body, true );
        
        if ( ! isset( $data['response']['player_count'] ) ) {
            return 'Keine Spieleranzahl gefunden.';
        }
        
        $playerCount = $data['response']['player_count'];
        $refresh_hours = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
        $refresh_seconds = $refresh_hours * 3600;
        set_transient( $transient_key, $playerCount, $refresh_seconds );
        
        // Letzten Abruf in den Optionen speichern
        $options['steam_last_fetch_time'] = current_time('mysql');
        $options['steam_last_fetch_value'] = $playerCount;
        update_option( 'sp_options', $options );
    }
    
    return number_format_i18n( $playerCount );
}
add_shortcode( 'counter_steam', 'sp_steam_counter_shortcode' );

function sp_test_facebook_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Nicht berechtigt.');
    }
    
    $options = get_option( 'sp_options' );
    $page_id = isset( $options['facebook_page_id'] ) ? trim( $options['facebook_page_id'] ) : '';
    $access_token = isset( $options['facebook_access_token'] ) ? trim( $options['facebook_access_token'] ) : '';
    
    if ( empty( $page_id ) || empty( $access_token ) ) {
        $response = array( 'message' => 'Facebook Page ID oder Access Token fehlen.' );
        wp_send_json( $response );
    }
    
    // API-URL zusammensetzen: Wir rufen das Feld "fan_count" ab
    $api_url = 'https://graph.facebook.com/v22.0/' . $page_id . '?fields=fan_count&access_token=' . $access_token;
    $response_wp = wp_remote_get( $api_url );
    
    if ( is_wp_error( $response_wp ) ) {
         $response = array( 'message' => 'Fehler beim Abrufen der Facebook-Daten.' );
         wp_send_json( $response );
    }
    
    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );
    
    if ( ! isset( $data['fan_count'] ) ) {
         $response = array( 'message' => 'Keine Fan-Zahl gefunden.' );
         wp_send_json( $response );
    }
    
    $fan_count = $data['fan_count'];
    
    // Aktualisierungsintervall aus den Facebook-Einstellungen (in Stunden, Standard 12h)
    $refresh_hours = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_facebook_counter_value', $fan_count, $refresh_seconds );
    
    // Speichern des letzten Abrufs in den Optionen
    $options['facebook_last_fetch_time'] = current_time('mysql');
    $options['facebook_last_fetch_value'] = $fan_count;
    update_option( 'sp_options', $options );
    
    $response = array(
        'message'         => 'Facebook Fans: ' . number_format_i18n( $fan_count ),
        'last_fetch_time' => $options['facebook_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $fan_count )
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_facebook_api', 'sp_test_facebook_api_callback' );
	