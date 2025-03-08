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
