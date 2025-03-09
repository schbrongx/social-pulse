<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// -----------------------------------------------------------------------------
// Shortcode Functions: Leader mode uses local API calls, Follower mode gets values from Leader
// -----------------------------------------------------------------------------
function sp_youtube_counter_shortcode() {
    $options = get_option('sp_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = sp_get_leader_value('youtube');
        return $value;
    } else {
        return sp_youtube_counter_get_value();
    }
}
add_shortcode('counter_youtube', 'sp_youtube_counter_shortcode');

function sp_steam_counter_shortcode() {
    $options = get_option('sp_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = sp_get_leader_value('steam');
        return $value;
    } else {
        return sp_steam_counter_get_value();
    }
}
add_shortcode('counter_steam', 'sp_steam_counter_shortcode');

function sp_facebook_counter_shortcode() {
    $options = get_option('sp_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = sp_get_leader_value('facebook');
        return $value;
    } else {
        return sp_facebook_counter_get_value();
    }
}
add_shortcode('counter_facebook', 'sp_facebook_counter_shortcode');

function sp_x_counter_shortcode() {
    $options = get_option('sp_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = sp_get_leader_value('x');
        return $value;
    } else {
        return sp_x_counter_get_value();
    }
}
add_shortcode('counter_x', 'sp_x_counter_shortcode');

?>