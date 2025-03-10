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

function sp_sanitize_options( $input ) {
    $output = array();

    // mode: only allow 'leader' or 'follower', default: 'leader'
    $output['mode'] = ( isset( $input['mode'] ) && in_array( $input['mode'], array( 'leader', 'follower' ) ) )
        ? $input['mode']
        : 'leader';

    // make sure leader_url _is_ an URL
    $output['leader_url'] = isset( $input['leader_url'] ) ? esc_url_raw( $input['leader_url'] ) : '';

    // youtube options
    $output['youtube_active'] = isset( $input['youtube_active'] ) && $input['youtube_active'] == 1 ? 1 : 0;
    $output['youtube_api_key'] = isset( $input['youtube_api_key'] ) ? sanitize_text_field( $input['youtube_api_key'] ) : '';
    $output['youtube_channel_id'] = isset( $input['youtube_channel_id'] ) ? sanitize_text_field( $input['youtube_channel_id'] ) : '';
    $output['youtube_refresh_interval'] = isset( $input['youtube_refresh_interval'] ) ? absint( $input['youtube_refresh_interval'] ) : 12;
    $output['last_fetch_time'] = isset($input['last_fetch_time']) ? sanitize_text_field($input['last_fetch_time']) : '';
    $output['last_fetch_value'] = isset($input['last_fetch_value']) ? sanitize_text_field($input['last_fetch_value']) : '';

    // steam options
    $output['steam_active'] = isset( $input['steam_active'] ) && $input['steam_active'] == 1 ? 1 : 0;
    $output['steam_app_id'] = isset( $input['steam_app_id'] ) ? sanitize_text_field( $input['steam_app_id'] ) : '';
    $output['steam_refresh_interval'] = isset( $input['steam_refresh_interval'] ) ? absint( $input['steam_refresh_interval'] ) : 12;
    $output['steam_last_fetch_time'] = isset($input['steam_last_fetch_time']) ? sanitize_text_field($input['steam_last_fetch_time']) : '';
    $output['steam_last_fetch_value'] = isset($input['steam_last_fetch_value']) ? sanitize_text_field($input['steam_last_fetch_value']) : '';

    // facebook options
    $output['facebook_active'] = isset( $input['facebook_active'] ) && $input['facebook_active'] == 1 ? 1 : 0;
    $output['facebook_page_id'] = isset( $input['facebook_page_id'] ) ? sanitize_text_field( $input['facebook_page_id'] ) : '';
    $output['facebook_access_token'] = isset( $input['facebook_access_token'] ) ? sanitize_text_field( $input['facebook_access_token'] ) : '';
    $output['facebook_refresh_interval'] = isset( $input['facebook_refresh_interval'] ) ? absint( $input['facebook_refresh_interval'] ) : 12;
    $output['facebook_metric'] = ( isset( $input['facebook_metric'] ) && in_array( $input['facebook_metric'], array( 'fan', 'follower' ) ) )
        ? $input['facebook_metric']
        : 'fan';
    $output['facebook_last_fetch_time'] = isset($input['facebook_last_fetch_time']) ? sanitize_text_field($input['facebook_last_fetch_time']) : '';
    $output['facebook_last_fetch_value'] = isset($input['facebook_last_fetch_value']) ? sanitize_text_field($input['facebook_last_fetch_value']) : '';

    // X options
    $output['x_active'] = isset( $input['x_active'] ) && $input['x_active'] == 1 ? 1 : 0;
    $output['x_username'] = isset( $input['x_username'] ) ? sanitize_text_field( $input['x_username'] ) : '';
    $output['x_bearer_token'] = isset( $input['x_bearer_token'] ) ? sanitize_text_field( $input['x_bearer_token'] ) : '';
    $output['x_refresh_interval'] = isset( $input['x_refresh_interval'] ) ? absint( $input['x_refresh_interval'] ) : 12;
    $output['x_last_fetch_time'] = isset($input['x_last_fetch_time']) ? sanitize_text_field($input['x_last_fetch_time']) : '';
    $output['x_last_fetch_value'] = isset($input['x_last_fetch_value']) ? sanitize_text_field($input['x_last_fetch_value']) : '';

    return $output;
}

function sp_register_settings() {
    register_setting( 'sp_settings_group', 'sp_options', 'sp_sanitize_options' );
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
              <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/></svg>				</span>
			  YouTube Settings
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
                    <th scope="row">Youtube Refresh Interval</th>
                    <td>
                        <select name="sp_options[youtube_refresh_interval]" <?php echo ($mode === 'follower' ? 'disabled' : ''); ?>>
                            <?php
                            $intervals = array(1, 2, 3, 6, 12, 24);
                            $current_interval = isset($options['youtube_refresh_interval']) ? intval($options['youtube_refresh_interval']) : 12;
                            foreach($intervals as $interval) {
                                echo '<option value="'.esc_attr($interval).'" '. selected($current_interval, $interval, false) .'>'.esc_attr($interval).'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[youtube_refresh_interval]" value="<?php echo esc_attr($current_interval); ?>" />
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
            <?php submit_button(); ?>

<!-- Steam Section -->
            <h2 class="sp-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 496 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M496 256c0 137-111.2 248-248.4 248-113.8 0-209.6-76.3-239-180.4l95.2 39.3c6.4 32.1 34.9 56.4 68.9 56.4 39.2 0 71.9-32.4 70.2-73.5l84.5-60.2c52.1 1.3 95.8-40.9 95.8-93.5 0-51.6-42-93.5-93.7-93.5s-93.7 42-93.7 93.5v1.2L176.6 279c-15.5-.9-30.7 3.4-43.5 12.1L0 236.1C10.2 108.4 117.1 8 247.6 8 384.8 8 496 119 496 256zM155.7 384.3l-30.5-12.6a52.8 52.8 0 0 0 27.2 25.8c26.9 11.2 57.8-1.6 69-28.4 5.4-13 5.5-27.3 .1-40.3-5.4-13-15.5-23.2-28.5-28.6-12.9-5.4-26.7-5.2-38.9-.6l31.5 13c19.8 8.2 29.2 30.9 20.9 50.7-8.3 19.9-31 29.2-50.8 21zm173.8-129.9c-34.4 0-62.4-28-62.4-62.3s28-62.3 62.4-62.3 62.4 28 62.4 62.3-27.9 62.3-62.4 62.3zm.1-15.6c25.9 0 46.9-21 46.9-46.8 0-25.9-21-46.8-46.9-46.8s-46.9 21-46.9 46.8c.1 25.8 21.1 46.8 46.9 46.8z"/></svg>
				Steam Settings
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
                                echo '<option value="'.esc_attr($interval).'" '. selected($current_interval, $interval, false) .'>'.esc_attr($interval).'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[steam_refresh_interval]" value="<?php echo esc_attr($current_interval); ?>" />
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
            </table>
            <?php submit_button(); ?>
<!-- Facebook Section -->
            <h2 class="sp-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"/></svg>
				Facebook Settings
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
                                echo '<option value="'.esc_attr($interval).'" '. selected($current_interval, $interval, false) .'>'.esc_attr($interval).'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[facebook_refresh_interval]" value="<?php echo esc_attr($current_interval); ?>" />
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
            </table>
            <?php submit_button(); ?>
<!-- X Section -->
            <h2 class="sp-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm297.1 84L257.3 234.6 379.4 396H283.8L209 298.1 123.3 396H75.8l111-126.9L69.7 116h98l67.7 89.5L313.6 116h47.5zM323.3 367.6L153.4 142.9H125.1L296.9 367.6h26.3z"/></svg>
				X Settings
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
                                echo '<option value="'.esc_attr($interval).'" '. selected($current_interval, $interval, false) .'>'.esc_attr($interval).'h</option>';
                            }
                            ?>
                        </select>
                        <?php if ($mode === 'follower'): ?>
                            <input type="hidden" name="sp_options[x_refresh_interval]" value="<?php echo esc_attr($current_interval); ?>" />
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
                    <th scope="row">X API Limit</th>
                    <td>
                      <?php $request_data = sp_get_x_request_data(); ?>
                      Limit: 25 requests per 24 hours. Current: <span id="sp-x-api-limit-count"><?php echo intval($request_data['count']); ?></span> requests.
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
                    if(response.api_limit_count) {
                        $('#sp-x-api-limit-count').html(response.api_limit_count);
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
    $refresh_hours = isset($options['youtube_refresh_interval']) ? intval($options['youtube_refresh_interval']) : 12;
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
	
    // Enforce new rate limit: 25 requests per 24 hours
    $request_data = sp_get_x_request_data();
    if ( $request_data['count'] >= 25 ) {
        $response = array( 'message' => 'Request limit reached (25 per 24 hours). Please wait.' );
        wp_send_json( $response );
    }
	sp_increment_x_request_count();

    $api_url = 'https://api.twitter.com/2/users/by/username/' . $username . '?user.fields=public_metrics';
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

    error_log(print_r($data, true));

    if ( ! isset($data['data']['public_metrics']['followers_count']) ) {
        wp_send_json(array('message'=>'No follower count found.', 'api_limit_count' => sp_get_x_request_data()['count']));
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
        'last_fetch_value'=> number_format_i18n($followers_count),
        'api_limit_count' => sp_get_x_request_data()['count']
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

?>
