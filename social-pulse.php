<?php
/*
Plugin Name: Social Pulse
Plugin URI: https://thomaspesendorfer.ch/social-pulse
Description: Shows follower-numbers of social accounts (i.e. YouTube, X, Facebook, Steam).
Version: 1.1.4
Author: Schbrongx
Author URI: https://thomaspesendorfer.ch
License: MIT
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// load settings and add settings page to the WordPress menu
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';

// leader mode functions
require_once plugin_dir_path( __FILE__ ) . 'includes/leader-mode.php';

// follower mode functions
require_once plugin_dir_path( __FILE__ ) . 'includes/follower-mode.php';

// register shortcodes for the social counters
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
