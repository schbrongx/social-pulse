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
    
    // YouTube API URL zusammenbauen
    $api_url = add_query_arg( array(
        'part'       => 'statistics',
        'id'         => $channel_id,
        'key'        => $api_key
    ), 'https://www.googleapis.com/youtube/v3/channels' );
    
    // API-Aufruf (Hinweis: Für produktive Systeme sollten Sie Caching einbauen!)
    $response = wp_remote_get( $api_url );
    
    if ( is_wp_error( $response ) ) {
        return 'Fehler beim Abrufen der YouTube-Daten.';
    }
    
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    // Fehlerbehandlung falls API-Daten nicht korrekt sind
    if ( ! isset( $data['items'][0]['statistics']['subscriberCount'] ) ) {
        return 'Keine Abonnentendaten gefunden.';
    }
    
    $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
    
    // Formatierung (optional können Sie hier weitere Formatierungen vornehmen)
    return number_format_i18n( $subscriberCount );
}

// Registrierung des Shortcodes
add_shortcode( 'counter_youtube', 'sp_youtube_counter_shortcode' );
