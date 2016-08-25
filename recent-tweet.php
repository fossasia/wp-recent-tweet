<?php

/*
Plugin Name: Recent Tweet
Plugin URI: http://wordpress.org/extend/plugins/recent-tweet/
Description: Recent Tweet plugin for anonymous Loklak API and Twitter API v1.1 with Cache. It supports the new Twitter API v1.1 and stores tweets in the cache. It means that it will read status messages from your database and it doesn't query loklak.org or Twitter.com for every page load so you won't be rate limited. You can set how often you want to update the cache.
Version: 1.0
Author: FOSSASIA
Author URI: http://twitter.com/fossasia
*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('FA_RECENT_TWEET_PATH', plugin_dir_url( __FILE__ ));

//register stylesheet for widget
function fa_twitter_plugin_styles() {
	wp_enqueue_style( 'fa_twitter_plugin_css', FA_RECENT_TWEET_PATH . 'fa_twitter_plugin.css', array(), '1.0', 'screen' );
}
add_action( 'wp_enqueue_scripts', 'fa_twitter_plugin_styles' );

// include widget function
require_once('widget.php');