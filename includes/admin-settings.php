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
        $('#sp-test-youtube').on('click', function() {
             $('#sp-youtube-test-result').html('Testing...');
             $.ajax({
                 url: ajaxurl, // ajaxurl ist in WordPress-Admin bereits definiert
                 type: 'POST',
                 data: {
                     action: 'sp_test_youtube_api'
                 },
                 success: function(response) {
                     $('#sp-youtube-test-result').html(response);
                 },
                 error: function(xhr, status, error) {
                     $('#sp-youtube-test-result').html('Error: ' + error);
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
                <!-- Weitere Counter können hier analog ergänzt werden: Twitter/X, Facebook, Steam -->
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function sp_test_youtube_api_callback() {
    // Sicherheitsprüfung: Nur berechtigte Benutzer dürfen den Test ausführen
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Nicht berechtigt.');
    }

    // Optionen abrufen
    $options = get_option( 'sp_options' );
    $api_key    = isset( $options['youtube_api_key'] ) ? trim( $options['youtube_api_key'] ) : '';
    $channel_id = isset( $options['youtube_channel_id'] ) ? trim( $options['youtube_channel_id'] ) : '';

    if ( empty( $api_key ) || empty( $channel_id ) ) {
        echo 'API Key oder Channel ID fehlen.';
        wp_die();
    }

    // YouTube API URL zusammenbauen
    $api_url = add_query_arg( array(
         'part' => 'statistics',
         'id'   => $channel_id,
         'key'  => $api_key
    ), 'https://www.googleapis.com/youtube/v3/channels' );

    // API-Aufruf (Hinweis: Für den produktiven Betrieb empfiehlt sich ein Caching der Ergebnisse)
    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) ) {
         echo 'Fehler beim Abrufen der Daten.';
         wp_die();
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! isset( $data['items'][0]['statistics']['subscriberCount'] ) ) {
         echo 'Keine Abonnentendaten gefunden.';
         wp_die();
    }

    $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
    echo 'YouTube Abonnenten: ' . number_format_i18n( $subscriberCount );
    wp_die();
}
add_action( 'wp_ajax_sp_test_youtube_api', 'sp_test_youtube_api_callback' );
