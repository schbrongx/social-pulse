<?php
/*
Plugin Name: Social Pulse
Plugin URI: https://thomaspesendorfer.ch/social-pulse
Description: Shows follower-numbers of social accounts (i.e. YouTube, X, Facebook, Steam) an.
Version: 1.0
Author: Schbrongx
Author URI: https://thomaspesendorfer.ch
License: MIT
*/

// Verhindern, dass direkt auf die Datei zugegriffen wird
if ( ! defined( 'ABSPATH' ) ) exit;

// Einstellungen laden und Admin-Menü hinzufügen
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';

// Shortcode für YouTube-Counter registrieren
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';

//AIzaSyDYOOHMwzCz28u_mT0J894TcJvIxJk4XHs