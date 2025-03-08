<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Register the admin menu
function sp_add_admin_menu() {
    add_menu_page(
        'Social Pulse Settings',   // Page title
        'Social Pulse',            // Menu text
        'manage_options',          // Capability
        'social-counters',         // Menu slug
        'sp_settings_page_html',   // Callback function
        'dashicons-chart-line',     // Icon
        100                         // Position
    );
}
add_action( 'admin_menu', 'sp_add_admin_menu' );

// Register the settings
function sp_register_settings() {
    register_setting( 'sp_settings_group', 'sp_options' );
}
add_action( 'admin_init', 'sp_register_settings' );

// Callback function for the settings page
function sp_settings_page_html() {
    // Check if the user has sufficient rights
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Retrieve settings
    $options = get_option( 'sp_options' );
    ?>
    <script>
    jQuery(document).ready(function($) {
        // YouTube Test
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
        
        // Steam Test
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
        // X Test
        $('#sp-test-x').on('click', function() {
             $('#sp-x-test-result').html('Testing...');
             $.ajax({
                 url: ajaxurl,
                 type: 'POST',
                 dataType: 'json',
                 data: { action: 'sp_test_x_api' },
                 success: function(response) {
                     $('#sp-x-test-result').html(response.message);
                     if(response.last_fetch_time) {
                         $('#sp-x-last-fetch-time').html(response.last_fetch_time);
                     }
                     if(response.last_fetch_value) {
                         $('#sp-x-last-fetch-value').html(response.last_fetch_value);
                     }
                 },
                 error: function(xhr, status, error) {
                     $('#sp-x-test-result').html('Error: ' + error);
                 }
             });
        });
    });
    </script>
    <style>
        .sp-section-title {
            background-color: #cccccc;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 10px;
        }
    </style>
    <div class="wrap">
        <h1>Social Pulse Settings</h1>
        <p>This page explains how to use Social Pulse. Enable the desired counters and enter the necessary API keys or IDs.</p>
        <p>
            <strong>Usage of Shortcodes:</strong><br />
            In your posts or pages use the following tags to display the corresponding social media follower counts:<br />
            - <code>[counter_youtube]</code> for YouTube subscribers<br />
            - <code>[counter_facebook]</code> for Facebook fans<br />
            - <code>[counter_x]</code> for X followers<br />
            - <code>[counter_steam]</code> for Steam "in-game" numbers
        </p>
        <form action="options.php" method="post">
            <?php settings_fields( 'sp_settings_group' ); ?>
            <?php do_settings_sections( 'sp_settings_group' ); ?>

<!-- YouTube Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-youtube"></span> YouTube Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable YouTube Counter</th>
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
                    <th scope="row">Refresh Interval</th>
                    <td>
                        <select name="sp_options[refresh_interval]">
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'. $interval .'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch (Time)</th>
                    <td id="sp-last-fetch-time">
                        <input type="hidden" name="sp_options[last_fetch_time]" value="<?php echo isset($options['last_fetch_time']) ? esc_attr($options['last_fetch_time']) : ''; ?>" />
                        <?php echo ( !empty($options['last_fetch_time']) ) ? esc_html($options['last_fetch_time']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch (Value)</th>
                    <td id="sp-last-fetch-value">
                        <input type="hidden" name="sp_options[last_fetch_value]" value="<?php echo isset($options['last_fetch_value']) ? esc_attr($options['last_fetch_value']) : ''; ?>" />
                        <?php echo ( isset($options['last_fetch_value']) && is_numeric($options['last_fetch_value']) ) ? number_format_i18n($options['last_fetch_value']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
            </table>

<!-- Steam Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-steam"></span> Steam Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Steam Counter</th>
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
                    <th scope="row">Steam Refresh Interval</th>
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
                    <th scope="row">Last Fetch Steam (Time)</th>
                    <td id="sp-steam-last-fetch-time">
                        <input type="hidden" name="sp_options[steam_last_fetch_time]" value="<?php echo isset($options['steam_last_fetch_time']) ? esc_attr($options['steam_last_fetch_time']) : ''; ?>" />
                        <?php echo ( !empty($options['steam_last_fetch_time']) ) ? esc_html($options['steam_last_fetch_time']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch Steam (Value)</th>
                    <td id="sp-steam-last-fetch-value">
                        <input type="hidden" name="sp_options[steam_last_fetch_value]" value="<?php echo isset($options['steam_last_fetch_value']) ? esc_attr($options['steam_last_fetch_value']) : ''; ?>" />
                        <?php echo ( isset($options['steam_last_fetch_value']) && is_numeric($options['steam_last_fetch_value']) ) ? number_format_i18n($options['steam_last_fetch_value']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test Steam API</th>
                    <td>
                        <button id="sp-test-steam" type="button" class="button">Test now</button>
                        <span id="sp-steam-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>
            </table>

<!-- Facebook Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-facebook"></span> Facebook Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Facebook Counter</th>
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
                    <th scope="row">Facebook Refresh Interval</th>
                    <td>
                        <select name="sp_options[facebook_refresh_interval]">
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'. $interval .'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <!-- Facebook Metric Selection -->
                <tr valign="top">
                   <th scope="row">Facebook Metric</th>
                   <td>
                       <select name="sp_options[facebook_metric]">
                           <?php
                           $metrics = array(
                               'fan'      => 'Fan (fan_count)',
                               'follower' => 'Follower (followers_count)'
                           );
                           $current_metric = isset($options['facebook_metric']) ? $options['facebook_metric'] : 'fan';
                           foreach($metrics as $key => $label) {
                               echo '<option value="'. esc_attr($key) .'" '. selected($current_metric, $key, false) .'>'. esc_html($label) .'</option>';
                           }
                           ?>
                       </select>
                   </td>
               </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch Facebook (Time)</th>
                    <td id="sp-facebook-last-fetch-time">
                        <input type="hidden" name="sp_options[facebook_last_fetch_time]" value="<?php echo isset($options['facebook_last_fetch_time']) ? esc_attr($options['facebook_last_fetch_time']) : ''; ?>" />
                        <?php echo ( !empty($options['facebook_last_fetch_time']) ) ? esc_html($options['facebook_last_fetch_time']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch Facebook (Value)</th>
                    <td id="sp-facebook-last-fetch-value">
                        <input type="hidden" name="sp_options[facebook_last_fetch_value]" value="<?php echo isset($options['facebook_last_fetch_value']) ? esc_attr($options['facebook_last_fetch_value']) : ''; ?>" />
                        <?php echo ( isset($options['facebook_last_fetch_value']) && is_numeric($options['facebook_last_fetch_value']) ) ? number_format_i18n($options['facebook_last_fetch_value']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test Facebook API</th>
                    <td>
                        <button id="sp-test-facebook" type="button" class="button">Test now</button>
                        <span id="sp-facebook-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>
               <!-- Help text for long-lived token -->
               <tr valign="top">
                    <td colspan="2">
                        <p class="sp-help-text">
                            For instructions on obtaining a long-lived token for clients, please visit 
                            <a href="https://developers.facebook.com/docs/facebook-login/guides/access-tokens/get-long-lived/?locale=en_US" target="_blank">
                                Facebook Developers Documentation
                            </a>.
                        </p>
                    </td>
               </tr>
            </table>

<!-- X Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-x-x"></span> X Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable X Counter</th>
                    <td>
                        <input type="checkbox" name="sp_options[x_active]" value="1" <?php checked( isset($options['x_active']) ? $options['x_active'] : 0, 1 ); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X Username</th>
                    <td>
                        <input type="text" name="sp_options[x_username]" value="<?php echo isset($options['x_username']) ? esc_attr( $options['x_username'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X Bearer Token</th>
                    <td>
                        <input type="text" name="sp_options[x_bearer_token]" value="<?php echo isset($options['x_bearer_token']) ? esc_attr( $options['x_bearer_token'] ) : ''; ?>" size="50" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X Refresh Interval</th>
                    <td>
                        <select name="sp_options[x_refresh_interval]">
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['x_refresh_interval']) ? intval($options['x_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'. $interval .'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch X (Time)</th>
                    <td id="sp-x-last-fetch-time">
                        <input type="hidden" name="sp_options[x_last_fetch_time]" value="<?php echo isset($options['x_last_fetch_time']) ? esc_attr($options['x_last_fetch_time']) : ''; ?>" />
                        <?php echo ( !empty($options['x_last_fetch_time']) ) ? esc_html($options['x_last_fetch_time']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Last Fetch X (Value)</th>
                    <td id="sp-x-last-fetch-value">
                        <input type="hidden" name="sp_options[x_last_fetch_value]" value="<?php echo isset($options['x_last_fetch_value']) ? esc_attr($options['x_last_fetch_value']) : ''; ?>" />
                        <?php echo ( isset($options['x_last_fetch_value']) && is_numeric($options['x_last_fetch_value']) ) ? number_format_i18n($options['x_last_fetch_value']) : 'Not fetched yet'; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test X API</th>
                    <td>
                        <button id="sp-test-x" type="button" class="button">Test now</button>
                        <span id="sp-x-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X API Limit</th>
                    <td>
                        <?php 
                        $request_data = sp_get_x_request_data();
                        echo 'Limit: 25 requests per 24 hours. Current: ' . intval($request_data['count']) . ' requests.';
                        ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// AJAX callback for testing the YouTube API
function sp_test_youtube_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }

    $options = get_option( 'sp_options' );
    $api_key    = isset( $options['youtube_api_key'] ) ? trim( $options['youtube_api_key'] ) : '';
    $channel_id = isset( $options['youtube_channel_id'] ) ? trim( $options['youtube_channel_id'] ) : '';

    if ( empty( $api_key ) || empty( $channel_id ) ) {
        $response = array(
            'message' => 'API Key or Channel ID is missing.',
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
             'message' => 'Error fetching data.',
         );
         wp_send_json( $response );
    }

    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );

    if ( ! isset( $data['items'][0]['statistics']['subscriberCount'] ) ) {
         $response = array(
             'message' => 'No subscriber data found.',
         );
         wp_send_json( $response );
    }

    $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
    
    // Update the cache and options values
    $refresh_hours = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_youtube_counter_value', $subscriberCount, $refresh_seconds );
    $options['last_fetch_time'] = current_time('mysql');
    $options['last_fetch_value'] = $subscriberCount;
    update_option( 'sp_options', $options );
    
    // Return as JSON
    $response = array(
        'message'         => 'YouTube Subscribers: ' . number_format_i18n( $subscriberCount ),
        'last_fetch_time' => $options['last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $subscriberCount ),
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_youtube_api', 'sp_test_youtube_api_callback' );

// AJAX callback for testing the Steam API
function sp_test_steam_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    
    $options = get_option( 'sp_options' );
    $app_id = isset( $options['steam_app_id'] ) ? trim( $options['steam_app_id'] ) : '';
    
    if ( empty( $app_id ) ) {
        $response = array( 'message' => 'Steam App ID is missing.' );
        wp_send_json($response);
    }
    
    // Steam API URL: GetNumberOfCurrentPlayers
    $api_url = add_query_arg( array( 'appid' => $app_id ), 'https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/' );
    
    $response_wp = wp_remote_get( $api_url );
    if ( is_wp_error( $response_wp ) ) {
         $response = array( 'message' => 'Error fetching Steam data.' );
         wp_send_json( $response );
    }
    
    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );
    
    if ( ! isset( $data['response']['player_count'] ) ) {
         $response = array( 'message' => 'No player count found.' );
         wp_send_json( $response );
    }
    
    $playerCount = $data['response']['player_count'];
    
    // Refresh interval from Steam settings (in hours, default 12h)
    $refresh_hours = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_steam_counter_value', $playerCount, $refresh_seconds );
    
    // Save the last fetch time and value in options
    $options['steam_last_fetch_time'] = current_time('mysql');
    $options['steam_last_fetch_value'] = $playerCount;
    update_option( 'sp_options', $options );
    
    $response = array(
        'message'         => 'Steam Players: ' . number_format_i18n( $playerCount ),
        'last_fetch_time' => $options['steam_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $playerCount )
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_steam_api', 'sp_test_steam_api_callback' );

// AJAX callback for testing the Facebook API
function sp_test_facebook_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    
    $options = get_option( 'sp_options' );
    $page_id = isset( $options['facebook_page_id'] ) ? trim( $options['facebook_page_id'] ) : '';
    $access_token = isset( $options['facebook_access_token'] ) ? trim( $options['facebook_access_token'] ) : '';
    $metric = isset($options['facebook_metric']) ? $options['facebook_metric'] : 'fan'; // Default: fan
    
    if ( empty( $page_id ) || empty( $access_token ) ) {
        $response = array( 'message' => 'Facebook Page ID or Access Token is missing.' );
        wp_send_json( $response );
    }
    
    // Choose the field based on metric selection
    $field = ($metric === 'follower') ? 'followers_count' : 'fan_count';
    
    // Assemble API URL: Request the selected field
    $api_url = 'https://graph.facebook.com/v22.0/' . $page_id . '?fields=' . $field . '&access_token=' . $access_token;
    $response_wp = wp_remote_get( $api_url );
    
    if ( is_wp_error( $response_wp ) ) {
         $response = array( 'message' => 'Error fetching Facebook data.' );
         wp_send_json( $response );
    }
    
    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );
    
    if ( ! isset( $data[$field] ) ) {
         $response = array( 'message' => 'No fan/follower count found.' );
         wp_send_json( $response );
    }
    
    $value = $data[$field];
    
    // Refresh interval from Facebook settings (in hours, default 12h)
    $refresh_hours = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_facebook_counter_value', $value, $refresh_seconds );
    
    // Save the last fetch time and value in options
    $options['facebook_last_fetch_time'] = current_time('mysql');
    $options['facebook_last_fetch_value'] = $value;
    update_option( 'sp_options', $options );
    
    $response = array(
        'message'         => 'Facebook ' . ucfirst($metric) . ': ' . number_format_i18n( $value ),
        'last_fetch_time' => $options['facebook_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $value )
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_facebook_api', 'sp_test_facebook_api_callback' );

// AJAX callback for testing the X API
function sp_test_x_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    
    $options = get_option( 'sp_options' );
    $username = isset( $options['x_username'] ) ? trim( $options['x_username'] ) : '';
    $bearer_token = isset( $options['x_bearer_token'] ) ? trim( $options['x_bearer_token'] ) : '';
    
    if ( empty( $username ) || empty( $bearer_token ) ) {
        $response = array( 'message' => 'X username or Bearer Token is missing.' );
        wp_send_json( $response );
    }
    
    // New limit: 25 requests per 24 hours
    $request_data = sp_get_x_request_data();
    if ( $request_data['count'] >= 25 ) {
        $response = array( 'message' => 'Request limit reached (25/24 hours). Please wait.' );
        wp_send_json( $response );
    }
    
    sp_increment_x_request_count();
    
    // X API call
    $api_url = 'https://api.x.com/2/users/by/username/' . $username . '?user.fields=public_metrics';
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $bearer_token,
        ),
    );
    $response_wp = wp_remote_get( $api_url, $args );
    
    if ( is_wp_error( $response_wp ) ) {
        $response = array( 'message' => 'Error fetching X data.' );
        wp_send_json( $response );
    }
    
    $body = wp_remote_retrieve_body( $response_wp );
    $data = json_decode( $body, true );
    
    if ( ! isset( $data['data']['public_metrics']['followers_count'] ) ) {
        $response = array( 'message' => 'No follower count found.' );
        wp_send_json( $response );
    }
    
    $followers_count = $data['data']['public_metrics']['followers_count'];
    $refresh_hours = isset($options['x_refresh_interval']) ? intval($options['x_refresh_interval']) : 12;
    $refresh_seconds = $refresh_hours * 3600;
    set_transient( 'sp_x_counter_value', $followers_count, $refresh_seconds );
    
    // Save the last fetch time and value in options
    $options['x_last_fetch_time'] = current_time('mysql');
    $options['x_last_fetch_value'] = $followers_count;
    update_option( 'sp_options', $options );
    
    $response = array(
        'message'         => 'X Followers: ' . number_format_i18n( $followers_count ),
        'last_fetch_time' => $options['x_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n( $followers_count )
    );
    wp_send_json( $response );
}
add_action( 'wp_ajax_sp_test_x_api', 'sp_test_x_api_callback' );

function sp_get_x_request_data() {
    // New time window: 24 hours (86,400 seconds)
    $window = 24 * 3600;
    $data = get_transient('sp_x_api_requests');

    // If no transient exists or the window has expired:
    if ( false === $data || ( time() - $data['start_time'] ) >= $window ) {
        $data = array(
            'count'      => 0,
            'start_time' => time()
        );
        set_transient('sp_x_api_requests', $data, $window);
    }
    return $data;
}

function sp_increment_x_request_count() {
    $data = sp_get_x_request_data();
    $data['count']++;
    // Calculate remaining lifetime of the window
    $window = 24 * 3600;
    $remaining = $window - (time() - $data['start_time']);
    set_transient('sp_x_api_requests', $data, $remaining);
}

function sp_enqueue_fontawesome() {
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css' );
}
add_action( 'admin_enqueue_scripts', 'sp_enqueue_fontawesome' );
