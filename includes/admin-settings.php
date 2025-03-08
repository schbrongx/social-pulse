<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Registrierung des Admin-Menüs
function sp_add_admin_menu() {
    add_menu_page(
        'Social Counters Einstellungen',  // Page title
        'Social Counters',                 // Menütext
        'manage_options',                  // Capability
        'social-counters',                 // Menü-Slug
        'sp_settings_page_html',           // Callback-Funktion
        'dashicons-chart-bar',             // Icon
        20                                 // Position
    );
}
add_action( 'admin_menu', 'sp_add_admin_menu' );

// Registrierung der Einstellungen
function sp_register_settings() {
    register_setting( 'sp_settings_group', 'sp_options' );
}
add_action( 'admin_init', 'sp_register_settings' );

// Callback-Funktion für die Einstellungsseite
function sp_settings_page_html() {
    // Prüfen, ob der Benutzer die nötigen Rechte hat
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Einstellungen abrufen
    $options = get_option( 'sp_options' );
    ?>
<script>
jQuery(document).ready(function($) {
    // YouTube Test (bereits vorhanden)
    $('#sp-test-youtube').on('click', function() {
         $('#sp-youtube-test-result').html('Testing...');
         $.ajax({
             url: ajaxurl,
             type: 'POST',
             dataType: 'json',
             data: { action: 'sp_test_youtube_api' },
             success: function(response) {
                 $('#sp-youtube-test-result').html(response.message);
                 if(response.last_fetch_time) {
                     $('#sp-last-fetch-time').html(response.last_fetch_time);
                 }
                 if(response.last_fetch_value) {
                     $('#sp-last-fetch-value').html(response.last_fetch_value);
                 }
             },
             error: function(xhr, status, error) {
                 $('#sp-youtube-test-result').html('Error: ' + error);
             }
         });
    });
    
    // Steam Test (bereits vorhanden)
    $('#sp-test-steam').on('click', function() {
         $('#sp-steam-test-result').html('Testing...');
         $.ajax({
             url: ajaxurl,
             type: 'POST',
             dataType: 'json',
             data: { action: 'sp_test_steam_api' },
             success: function(response) {
                 $('#sp-steam-test-result').html(response.message);
                 if(response.last_fetch_time) {
                     $('#sp-steam-last-fetch-time').html(response.last_fetch_time);
                 }
                 if(response.last_fetch_value) {
                     $('#sp-steam-last-fetch-value').html(response.last_fetch_value);
                 }
             },
             error: function(xhr, status, error) {
                 $('#sp-steam-test-result').html('Error: ' + error);
             }
         });
    });

    // Facebook Test
    $('#sp-test-facebook').on('click', function() {
         $('#sp-facebook-test-result').html('Testing...');
         $.ajax({
             url: ajaxurl,
             type: 'POST',
             dataType: 'json',
             data: { action: 'sp_test_facebook_api' },
             success: function(response) {
                 $('#sp-facebook-test-result').html(response.message);
                 if(response.last_fetch_time) {
                     $('#sp-facebook-last-fetch-time').html(response.last_fetch_time);
                 }
                 if(response.last_fetch_value) {
                     $('#sp-facebook-last-fetch-value').html(response.last_fetch_value);
                 }
             },
             error: function(xhr, status, error) {
                 $('#sp-facebook-test-result').html('Error: ' + error);
             }
         });
    });
});
</script>

    <div class="wrap">
        <h1>Social Pulse Einstellungen</h1>
        <p>Diese Seite erklärt, wie Sie Social Pulse nutzen können. Aktivieren Sie die gewünschten Counter und tragen Sie ggf. notwendige API-Schlüssel bzw. IDs ein.</p>
        <p>
            <strong>Verwendung der Shortcodes:</strong><br />
            Nutzen Sie in Ihren Beiträgen oder Seiten die folgenden Tags, um die jeweiligen Social Media Follower-Zahlen einzublenden:<br />
            - <code>[counter_youtube]</code> für YouTube-Abonnenten<br />
            - <code>[counter_facebook]</code> für Facebook-Fans<br />
            - <code>[counter_x]</code> für Twitter/X Follower<br />
            - <code>[counter_steam]</code> für Steam "Im Spiel" Zahlen
        </p>
        <form action="options.php" method="post">
            <?php settings_fields( 'sp_settings_group' ); ?>
            <?php do_settings_sections( 'sp_settings_group' ); ?>
            <table class="form-table">
                <!-- YouTube Counter -->
                <tr valign="top">
                    <th scope="row">YouTube Counter aktivieren</th>
                    <td>
                        <input type="checkbox" name="sp_options[youtube_active]" value="1" <?php checked( isset($options['youtube_active']) ? $options['youtube_active'] : 0, 1 ); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube API Key</th>
                    <td>
                        <input type="text" name="sp_options[youtube_api_key]" value="<?php echo isset($options['youtube_api_key']) ? esc_attr( $options['youtube_api_key'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube Channel ID</th>
                    <td>
                        <input type="text" name="sp_options[youtube_channel_id]" value="<?php echo isset($options['youtube_channel_id']) ? esc_attr( $options['youtube_channel_id'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test YouTube API</th>
                    <td>
                        <button id="sp-test-youtube" type="button" class="button">Test now</button>
                        <span id="sp-youtube-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Aktualisierungsintervall</th>
                    <td>
                        <select name="sp_options[refresh_interval]">
                            <?php
                            // Mögliche Intervalle in Stunden
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            // Standardwert 12 Stunden, falls noch nicht gesetzt
                            $current_interval = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'. $interval .'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Letzter Abruf (Zeit)</th>
                    <td id="sp-last-fetch-time">
                        <?php 
                        echo isset($options['last_fetch_time']) ? esc_html($options['last_fetch_time']) : 'Noch nicht abgerufen';
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Letzter Abruf (Wert)</th>
                    <td id="sp-last-fetch-value">
                        <?php 
                        echo isset($options['last_fetch_value']) ? number_format_i18n($options['last_fetch_value']) : 'Noch nicht abgerufen';
                        ?>
                    </td>
                </tr>
                <!-- Steam Counter Einstellungen -->
                <tr valign="top">
                    <th scope="row">Steam Counter aktivieren</th>
                    <td>
                        <input type="checkbox" name="sp_options[steam_active]" value="1" <?php checked( isset($options['steam_active']) ? $options['steam_active'] : 0, 1 ); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Steam App ID</th>
                    <td>
                        <input type="text" name="sp_options[steam_app_id]" value="<?php echo isset($options['steam_app_id']) ? esc_attr( $options['steam_app_id'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Steam Aktualisierungsintervall</th>
                    <td>
                        <select name="sp_options[steam_refresh_interval]">
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'.$interval.'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Letzter Abruf Steam (Zeit)</th>
                    <td id="sp-steam-last-fetch-time">
                        <?php echo isset($options['steam_last_fetch_time']) ? esc_html($options['steam_last_fetch_time']) : 'Noch nicht abgerufen'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Letzter Abruf Steam (Wert)</th>
                    <td id="sp-steam-last-fetch-value">
                        <?php echo isset($options['steam_last_fetch_value']) ? number_format_i18n($options['steam_last_fetch_value']) : 'Noch nicht abgerufen'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test Steam API</th>
                    <td>
                        <button id="sp-test-steam" type="button" class="button">Test now</button>
                        <span id="sp-steam-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>
                <!-- Facebook Fans Counter Einstellungen -->
                <tr valign="top">
                    <th scope="row">Facebook Counter aktivieren</th>
                    <td>
                        <input type="checkbox" name="sp_options[facebook_active]" value="1" <?php checked( isset($options['facebook_active']) ? $options['facebook_active'] : 0, 1 ); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Page ID</th>
                    <td>
                        <input type="text" name="sp_options[facebook_page_id]" value="<?php echo isset($options['facebook_page_id']) ? esc_attr( $options['facebook_page_id'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Access Token</th>
                    <td>
                        <input type="text" name="sp_options[facebook_access_token]" value="<?php echo isset($options['facebook_access_token']) ? esc_attr( $options['facebook_access_token'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Aktualisierungsintervall</th>
                    <td>
                        <select name="sp_options[facebook_refresh_interval]">
                            <?php
                            // Mögliche Intervalle in Stunden
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'. $interval .'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Letzter Abruf Facebook (Zeit)</th>
                    <td id="sp-facebook-last-fetch-time">
                        <?php 
                        echo isset($options['facebook_last_fetch_time']) ? esc_html($options['facebook_last_fetch_time']) : 'Noch nicht abgerufen';
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Letzter Abruf Facebook (Wert)</th>
                    <td id="sp-facebook-last-fetch-value">
                        <?php 
                        echo isset($options['facebook_last_fetch_value']) ? number_format_i18n($options['facebook_last_fetch_value']) : 'Noch nicht abgerufen';
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test Facebook API</th>
                    <td>
                        <button id="sp-test-facebook" type="button" class="button">Test now</button>
                        <span id="sp-facebook-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>

                <!-- Weitere Counter können hier analog ergänzt werden: Twitter/X, Facebook, Steam -->
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function sp_test_youtube_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Nicht berechtigt.');
    }

    $options = get_option( 'sp_options' );
    $api_key    = isset( $options['youtube_api_key'] ) ? trim( $options['youtube_api_key'] ) : '';
    $channel_id = isset( $options['youtube_channel_id'] ) ? trim( $options['youtube_channel_id'] ) : '';

    if ( empty( $api_key ) || empty( $channel_id ) ) {
        $response = array(
            'message' => 'API Key oder Channel ID fehlen.',
        );
        wp_send_json( $response );
    }

    $api_url = add_query_arg( array(
         'part' => 'statistics',
         'id'   => $channel_id,
         'key'  => $api_key
    ), 'https://www.googleapis.com/youtube/v3/channels' );

    $response_wp = wp_remote_get( $api_url );

    if ( is_wp_error( $response_wp ) ) {
         $response = array(
             'message' => 'Fehler beim Abrufen der Daten.',
         );
         wp_send_json( $response );
    }

    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );

    if ( ! isset( $data['items'][0]['statistics']['subscriberCount'] ) ) {
         $response = array(
             'message' => 'Keine Abonnentendaten gefunden.',
         );
         wp_send_json( $response );
    }

    $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
    
    // Aktualisierung des Cache und der Optionswerte
    $refresh_hours = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_youtube_counter_value', $subscriberCount, $refresh_seconds );
    $options['last_fetch_time'] = current_time('mysql');
    $options['last_fetch_value'] = $subscriberCount;
    update_option( 'sp_options', $options );
    
    // Rückgabe als JSON
    $response = array(
        'message'         => 'YouTube Abonnenten: ' . number_format_i18n( $subscriberCount ),
        'last_fetch_time' => $options['last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $subscriberCount ),
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_youtube_api', 'sp_test_youtube_api_callback' );

function sp_test_steam_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Nicht berechtigt.');
    }
    
    $options = get_option( 'sp_options' );
    $app_id = isset( $options['steam_app_id'] ) ? trim( $options['steam_app_id'] ) : '';
    
    if ( empty( $app_id ) ) {
        $response = array( 'message' => 'Steam App ID fehlt.' );
        wp_send_json($response);
    }
    
    // Steam API URL: GetNumberOfCurrentPlayers
    $api_url = add_query_arg( array( 'appid' => $app_id ), 'https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/' );
    
    $response_wp = wp_remote_get( $api_url );
    if ( is_wp_error( $response_wp ) ) {
         $response = array( 'message' => 'Fehler beim Abrufen der Steam-Daten.' );
         wp_send_json( $response );
    }
    
    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );
    
    if ( ! isset( $data['response']['player_count'] ) ) {
         $response = array( 'message' => 'Keine Spieleranzahl gefunden.' );
         wp_send_json( $response );
    }
    
    $playerCount = $data['response']['player_count'];
    
    // Aktualisierungsintervall aus den Steam-Einstellungen (in Stunden, Standard 12h)
    $refresh_hours = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_steam_counter_value', $playerCount, $refresh_seconds );
    
    // Speichern des letzten Abrufs in den Optionen
    $options['steam_last_fetch_time'] = current_time('mysql');
    $options['steam_last_fetch_value'] = $playerCount;
    update_option( 'sp_options', $options );
    
    $response = array(
        'message'         => 'Steam Spieler: ' . number_format_i18n( $playerCount ),
        'last_fetch_time' => $options['steam_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $playerCount )
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_steam_api', 'sp_test_steam_api_callback' );
