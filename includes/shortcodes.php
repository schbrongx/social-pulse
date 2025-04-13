<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// -----------------------------------------------------------------------------
// Shortcode Functions: Leader mode uses local API calls, Follower mode gets values from Leader
// -----------------------------------------------------------------------------
function SOCPUL_youtube_counter_shortcode() {
    $options = get_option('SOCPUL_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = SOCPUL_get_leader_value('youtube');
        return $value;
    } else {
        return SOCPUL_youtube_counter_get_value();
    }
}
add_shortcode('counter_youtube', 'SOCPUL_youtube_counter_shortcode');

function SOCPUL_steam_counter_shortcode() {
    $options = get_option('SOCPUL_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = SOCPUL_get_leader_value('steam');
        return $value;
    } else {
        return SOCPUL_steam_counter_get_value();
    }
}
add_shortcode('counter_steam', 'SOCPUL_steam_counter_shortcode');

function SOCPUL_facebook_counter_shortcode() {
    $options = get_option('SOCPUL_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = SOCPUL_get_leader_value('facebook');
        return $value;
    } else {
        return SOCPUL_facebook_counter_get_value();
    }
}
add_shortcode('counter_facebook', 'SOCPUL_facebook_counter_shortcode');

function SOCPUL_x_counter_shortcode() {
    $options = get_option('SOCPUL_options');
    $mode = isset($options['mode']) ? $options['mode'] : 'leader';
    if ($mode === 'follower') {
        $value = SOCPUL_get_leader_value('x');
        return $value;
    } else {
        return SOCPUL_x_counter_get_value();
    }
}
add_shortcode('counter_x', 'SOCPUL_x_counter_shortcode');
