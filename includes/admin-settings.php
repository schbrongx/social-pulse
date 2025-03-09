<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sp_add_admin_menu() {
    add_menu_page(
        'Social Pulse Settings',   // Page title
        'Social Pulse',            // Menu text
        'manage_options',          // Capability required
        'social-counters',         // Menu slug
        'sp_settings_page_html',   // Callback function for settings page
        'dashicons-chart-line',    // Icon
        100                        // Position in menu
    );
}
add_action('admin_menu', 'sp_add_admin_menu');

function sp_register_settings() {
    register_setting( 'sp_settings_group', 'sp_options' );
}
add_action( 'admin_init', 'sp_register_settings' );

function sp_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $options = get_option('sp_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    $leader_url = isset($options['leader_url']) ? $options['leader_url'] : '';
    ?>
	<style>
    .sp-section-title {
        background-color: #cccccc;
        padding: 10px;
        border-radius: 3px;
        margin-bottom: 10px;
        font-weight: bold;
    }
    </style>
    <div class="wrap">
        <h1>Social Pulse Settings</h1>
        <p>Enable the desired counters and enter the necessary API keys or IDs. Always hit "Save Changes" before testing.</p>
		<p>There are two modes: <b>Leader-Mode and Follower-Mode</b>:
		  <ul>
		  <li><b>In Leader-Mode</b> the plugin makes the actual API-requests and
		   makes them available for the selected refresh-interval. The values also get exposed at the "Leader-URL" which is
		   displayed on top of the settings page.</li>
		   <li><b>In Follower-Mode</b> you have to enter the Leader-URL (and hit "Save Changes". In this case the plugin does Not
		   request values from the API but receives them from the "Leader-URL" every 5 minutes. This should greatly reduce the amount
		   of requests to the actual API endpoints and is usefull because some of the API endpoints have <b>very</b>
		   restictive limits.</li>
		   </ul>
		</p>
        <p>
            <strong>Usage of Shortcodes in Wordpress:</strong><br />
            In your posts or pages, use the following tags to display the corresponding social media follower counts:<br />
            - <code>[counter_youtube]</code> for YouTube subscribers<br />
            - <code>[counter_facebook]</code> for Facebook fans<br />
            - <code>[counter_x]</code> for X followers<br />
            - <code>[counter_steam]</code> for Steam in-game player counts
        </p>
        <form action="options.php" method="post">
            <?php settings_fields('sp_settings_group'); ?>
            <?php do_settings_sections('sp_settings_group'); ?>

            <!-- Plugin Mode Section -->
            <h2 class="sp-section-title">Plugin Mode</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Mode</th>
                    <td>
                        <label>
                            <input type="radio" name="sp_options[mode]" value="leader" <?php checked($mode, 'leader'); ?> /> Leader
                        </label>
                        <label style="margin-left:20px;">
                            <input type="radio" name="sp_options[mode]" value="follower" <?php checked($mode, 'follower'); ?> /> Follower
                        </label>
                    </td>
                </tr>
                <?php if ( $mode === 'leader' ) : ?>
                <tr valign="top">
                    <th scope="row">Exposed Values URL</th>
                    <td>
                        <?php 
                            $values_url = site_url('/social-counters/values'); 
                            echo '<input type="text" readonly="readonly" value="'.esc_url($values_url).'" size="60" />';
                        ?>
                    </td>
                </tr>
                <?php else: ?>
                <tr valign="top">
                    <th scope="row">Leader URL</th>
                    <td>
                        <input type="text" name="sp_options[leader_url]" value="<?php echo esc_attr($leader_url); ?>" size="60" />
                        <button id="sp-test-leader" type="button" class="button">Test now</button>
                        <span id="sp-leader-test-result" style="margin-left:10px;"></span>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
            <?php submit_button(); ?>

            <!-- YouTube Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-youtube"></span> YouTube Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable YouTube Counter</th>
                    <td>
                        <input type="checkbox" name="sp_options[youtube_active]" value="1" <?php checked( isset($options['youtube_active']) ? $options['youtube_active'] : 0, 1 ); ?> <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[youtube_active]" value="<?php echo isset($options['youtube_active']) ? esc_attr($options['youtube_active']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube API Key</th>
                    <td>
                        <input type="text" name="sp_options[youtube_api_key]" value="<?php echo isset($options['youtube_api_key']) ? esc_attr($options['youtube_api_key']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[youtube_api_key]" value="<?php echo isset($options['youtube_api_key']) ? esc_attr($options['youtube_api_key']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube Channel ID</th>
                    <td>
                        <input type="text" name="sp_options[youtube_channel_id]" value="<?php echo isset($options['youtube_channel_id']) ? esc_attr($options['youtube_channel_id']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[youtube_channel_id]" value="<?php echo isset($options['youtube_channel_id']) ? esc_attr($options['youtube_channel_id']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test YouTube API</th>
                    <td>
                        <button id="sp-test-youtube" type="button" class="button" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>Test now</button>
                        <span id="sp-youtube-test-result" style="margin-left:10px;"></span>
                        <span class="settings-changed" style="color:red; margin-left:10px; display:none;">Settings changed, save before testing.</span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>

            <!-- Steam Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-steam"></span> Steam Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Steam Counter</th>
                    <td>
                        <input type="checkbox" name="sp_options[steam_active]" value="1" <?php checked( isset($options['steam_active']) ? $options['steam_active'] : 0, 1 ); ?> <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[steam_active]" value="<?php echo isset($options['steam_active']) ? esc_attr($options['steam_active']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Steam App ID</th>
                    <td>
                        <input type="text" name="sp_options[steam_app_id]" value="<?php echo isset($options['steam_app_id']) ? esc_attr($options['steam_app_id']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[steam_app_id]" value="<?php echo isset($options['steam_app_id']) ? esc_attr($options['steam_app_id']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Steam Refresh Interval</th>
                    <td>
                        <select name="sp_options[steam_refresh_interval]" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'.$interval.'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[steam_refresh_interval]" value="<?php echo $current_interval; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test Steam API</th>
                    <td>
                        <button id="sp-test-steam" type="button" class="button" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>Test now</button>
                        <span id="sp-steam-test-result" style="margin-left:10px;"></span>
                        <span class="settings-changed" style="color:red; margin-left:10px; display:none;">Settings changed, save before testing.</span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>

            <!-- Facebook Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-facebook"></span> Facebook Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Facebook Counter</th>
                    <td>
                        <input type="checkbox" name="sp_options[facebook_active]" value="1" <?php checked( isset($options['facebook_active']) ? $options['facebook_active'] : 0, 1 ); ?> <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[facebook_active]" value="<?php echo isset($options['facebook_active']) ? esc_attr($options['facebook_active']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Page ID</th>
                    <td>
                        <input type="text" name="sp_options[facebook_page_id]" value="<?php echo isset($options['facebook_page_id']) ? esc_attr($options['facebook_page_id']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[facebook_page_id]" value="<?php echo isset($options['facebook_page_id']) ? esc_attr($options['facebook_page_id']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Access Token</th>
                    <td>
                        <input type="text" name="sp_options[facebook_access_token]" value="<?php echo isset($options['facebook_access_token']) ? esc_attr($options['facebook_access_token']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[facebook_access_token]" value="<?php echo isset($options['facebook_access_token']) ? esc_attr($options['facebook_access_token']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Refresh Interval</th>
                    <td>
                        <select name="sp_options[facebook_refresh_interval]" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'.$interval.'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[facebook_refresh_interval]" value="<?php echo $current_interval; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Metric</th>
                    <td>
                        <select name="sp_options[facebook_metric]" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>
                            <?php
                            $metrics = array(
                                'fan' => 'Fan (fan_count)',
                                'follower' => 'Follower (followers_count)'
                            );
                            $current_metric = isset($options['facebook_metric']) ? $options['facebook_metric'] : 'fan';
                            foreach($metrics as $key => $label) {
                                echo '<option value="'.esc_attr($key).'" '. selected($current_metric, $key, false) .'>'.esc_html($label).'</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[facebook_metric]" value="<?php echo esc_attr($current_metric); ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test Facebook API</th>
                    <td>
                        <button id="sp-test-facebook" type="button" class="button" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>Test now</button>
                        <span id="sp-facebook-test-result" style="margin-left:10px;"></span>
                        <span class="settings-changed" style="color:red; margin-left:10px; display:none;">Settings changed, save before testing.</span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>

            <!-- X Section -->
            <h2 class="sp-section-title">
                <span class="fa-brands fa-x-twitter"></span> X Settings
            </h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable X Counter</th>
                    <td>
                        <input type="checkbox" name="sp_options[x_active]" value="1" <?php checked( isset($options['x_active']) ? $options['x_active'] : 0, 1 ); ?> <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[x_active]" value="<?php echo isset($options['x_active']) ? esc_attr($options['x_active']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X Username</th>
                    <td>
                        <input type="text" name="sp_options[x_username]" value="<?php echo isset($options['x_username']) ? esc_attr($options['x_username']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[x_username]" value="<?php echo isset($options['x_username']) ? esc_attr($options['x_username']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X Bearer Token</th>
                    <td>
                        <input type="text" name="sp_options[x_bearer_token]" value="<?php echo isset($options['x_bearer_token']) ? esc_attr($options['x_bearer_token']) : ''; ?>" size="50" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?> />
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[x_bearer_token]" value="<?php echo isset($options['x_bearer_token']) ? esc_attr($options['x_bearer_token']) : ''; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">X Refresh Interval</th>
                    <td>
                        <select name="sp_options[x_refresh_interval]" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['x_refresh_interval']) ? intval($options['x_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'.$interval.'" '. selected($current_interval, $interval, false) .'>'.$interval.'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[x_refresh_interval]" value="<?php echo $current_interval; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Test X API</th>
                    <td>
                        <button id="sp-test-x" type="button" class="button" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>Test now</button>
                        <span id="sp-x-test-result" style="margin-left:10px;"></span>
                        <span class="settings-changed" style="color:red; margin-left:10px; display:none;">Settings changed, save before testing.</span>
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
    <script>
    jQuery(document).ready(function($) {
        // Show "Settings changed" message when any field changes.
        $('form').on('change', 'input, select, textarea', function() {
            $('.settings-changed').show();
        });
        // Test Leader URL AJAX (Follower mode)
        $('#sp-test-leader').on('click', function() {
            $('#sp-leader-test-result').html('Testing...');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: { action: 'sp_test_leader_api' },
                success: function(response) {
                    $('#sp-leader-test-result').html(response.message);
                },
                error: function(xhr, status, error) {
                    $('#sp-leader-test-result').html('Error: ' + error);
                }
            });
        });
        // AJAX calls for Test API buttons (YouTube, Steam, Facebook, X)
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
    <?php
}

// -----------------------------------------------------------------------------
// AJAX Callback Functions for Testing APIs (YouTube, Steam, Facebook, X)
// -----------------------------------------------------------------------------
function sp_test_youtube_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    $options = get_option('sp_options');
    $api_key = isset($options['youtube_api_key']) ? trim($options['youtube_api_key']) : '';
    $channel_id = isset($options['youtube_channel_id']) ? trim($options['youtube_channel_id']) : '';
    if ( empty($api_key) || empty($channel_id) ) {
        wp_send_json(array('message'=>'API Key or Channel ID is missing.'));
    }
    $api_url = add_query_arg(array(
        'part'=>'statistics',
        'id'=>$channel_id,
        'key'=>$api_key
    ), 'https://www.googleapis.com/youtube/v3/channels');
    $response = wp_remote_get($api_url);
    if ( is_wp_error($response) ) {
        wp_send_json(array('message'=>'Error fetching data.'));
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body,true);
    if ( ! isset($data['items'][0]['statistics']['subscriberCount']) ) {
        wp_send_json(array('message'=>'No subscriber data found.'));
    }
    $subscriberCount = $data['items'][0]['statistics']['subscriberCount'];
    $refresh_hours = isset($options['refresh_interval']) ? intval($options['refresh_interval']) : 12;
    set_transient('sp_youtube_counter_value', $subscriberCount, $refresh_hours * 3600);
    $options['last_fetch_time'] = current_time('mysql');
    $options['last_fetch_value'] = $subscriberCount;
    update_option('sp_options',$options);
    $message = 'YouTube Subscribers: ' . number_format_i18n($subscriberCount);
    wp_send_json(array(
        'message'         => $message,
        'last_fetch_time' => $options['last_fetch_time'],
        'last_fetch_value'=> number_format_i18n($subscriberCount)
    ));
}
add_action('wp_ajax_sp_test_youtube_api', 'sp_test_youtube_api_callback');

function sp_test_steam_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    $options = get_option('sp_options');
    $app_id = isset($options['steam_app_id']) ? trim($options['steam_app_id']) : '';
    if ( empty($app_id) ) {
        wp_send_json(array('message'=>'Steam App ID is missing.'));
    }
    $api_url = add_query_arg(array('appid'=>$app_id), 'https://api.steampowered.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/');
    $response = wp_remote_get($api_url);
    if ( is_wp_error($response) ) {
        wp_send_json(array('message'=>'Error fetching Steam data.'));
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body,true);
    if ( ! isset($data['response']['player_count']) ) {
        wp_send_json(array('message'=>'No player count found.'));
    }
    $playerCount = $data['response']['player_count'];
    $refresh_hours = isset($options['steam_refresh_interval']) ? intval($options['steam_refresh_interval']) : 12;
    set_transient('sp_steam_counter_value', $playerCount, $refresh_hours * 3600);
    $options['steam_last_fetch_time'] = current_time('mysql');
    $options['steam_last_fetch_value'] = $playerCount;
    update_option('sp_options',$options);
    $message = 'Steam Players: ' . number_format_i18n($playerCount);
    wp_send_json(array(
        'message'         => $message,
        'last_fetch_time' => $options['steam_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n($playerCount)
    ));
}
add_action('wp_ajax_sp_test_steam_api', 'sp_test_steam_api_callback');

function sp_test_facebook_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    $options = get_option('sp_options');
    $page_id = isset($options['facebook_page_id']) ? trim($options['facebook_page_id']) : '';
    $access_token = isset($options['facebook_access_token']) ? trim($options['facebook_access_token']) : '';
    $metric = isset($options['facebook_metric']) ? $options['facebook_metric'] : 'fan';
    if ( empty($page_id) || empty($access_token) ) {
        wp_send_json(array('message'=>'Facebook Page ID or Access Token is missing.'));
    }
    $field = ($metric === 'follower') ? 'followers_count' : 'fan_count';
    $api_url = 'https://graph.facebook.com/v22.0/' . $page_id . '?fields=' . $field . '&access_token=' . $access_token;
    $response = wp_remote_get($api_url);
    if ( is_wp_error($response) ) {
        wp_send_json(array('message'=>'Error fetching Facebook data.'));
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body,true);
    if ( ! isset($data[$field]) ) {
        wp_send_json(array('message'=>'No fan/follower count found.'));
    }
    $value = $data[$field];
    $refresh_hours = isset($options['facebook_refresh_interval']) ? intval($options['facebook_refresh_interval']) : 12;
    set_transient('sp_facebook_counter_value', $value, $refresh_hours * 3600);
    $options['facebook_last_fetch_time'] = current_time('mysql');
    $options['facebook_last_fetch_value'] = $value;
    update_option('sp_options',$options);
    $message = 'Facebook ' . ucfirst($metric) . ': ' . number_format_i18n($value);
    wp_send_json(array(
        'message'         => $message,
        'last_fetch_time' => $options['facebook_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n($value)
    ));
}
add_action('wp_ajax_sp_test_facebook_api', 'sp_test_facebook_api_callback');

function sp_test_x_api_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('Not allowed.');
    }
    $options = get_option('sp_options');
    $username = isset($options['x_username']) ? trim($options['x_username']) : '';
    $bearer_token = isset($options['x_bearer_token']) ? trim($options['x_bearer_token']) : '';
    if ( empty($username) || empty($bearer_token) ) {
        wp_send_json(array('message'=>'X username or Bearer Token is missing.'));
    }
    // (Rate limit logic omitted for brevity)
    $api_url = 'https://api.x.com/2/users/by/username/' . $username . '?user.fields=public_metrics';
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $bearer_token,
        ),
    );
    $response = wp_remote_get($api_url, $args);
    if ( is_wp_error($response) ) {
        wp_send_json(array('message'=>'Error fetching X data.'));
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body,true);
    if ( ! isset($data['data']['public_metrics']['followers_count']) ) {
        wp_send_json(array('message'=>'No follower count found.'));
    }
    $followers_count = $data['data']['public_metrics']['followers_count'];
    $refresh_hours = isset($options['x_refresh_interval']) ? intval($options['x_refresh_interval']) : 12;
    set_transient('sp_x_counter_value', $followers_count, $refresh_hours * 3600);
    $options['x_last_fetch_time'] = current_time('mysql');
    $options['x_last_fetch_value'] = $followers_count;
    update_option('sp_options',$options);
    $message = 'X Followers: ' . number_format_i18n($followers_count);
    wp_send_json(array(
        'message'         => $message,
        'last_fetch_time' => $options['x_last_fetch_time'],
        'last_fetch_value'=> number_format_i18n($followers_count)
    ));
}
add_action('wp_ajax_sp_test_x_api', 'sp_test_x_api_callback');

// Helper functions for X rate limiting
function sp_get_x_request_data() {
    $window = 24 * 3600;
    $data = get_transient('sp_x_api_requests');
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
    $window = 24 * 3600;
    $remaining = $window - (time() - $data['start_time']);
    set_transient('sp_x_api_requests', $data, $remaining);
}

// -----------------------------------------------------------------------------
// Enqueue Font Awesome for the admin area
// -----------------------------------------------------------------------------
function sp_enqueue_fontawesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css');
}
add_action('admin_enqueue_scripts', 'sp_enqueue_fontawesome');
?>
