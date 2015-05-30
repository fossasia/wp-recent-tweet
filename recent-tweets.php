<?php

/*
Plugin Name: Recent Tweets Widget
Plugin URI: http://wordpress.org/extend/plugins/recent-tweets-widget/
Description: Recent Tweets Widget plugin for Twitter API v1.1 with Cache. It uses the new Twitter API v1.1 and stores tweets in the cache. It means that it will read status messages from your database and it doesn't query Twitter.com for every page load so you won't be rate limited. You can set how often you want to update the cache.
Version: 1.6.1
Author: Noah Kagan
Author URI: http://sumome.com
*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// make sure we don't expose any info if called directly
	if ( !function_exists( 'add_action' ) ) {
		echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
		exit;
	}

	define('TP_RECENT_TWEETS_PATH', plugin_dir_url( __FILE__ ));

	//register stylesheet for widget
	function tp_twitter_plugin_styles() {
		wp_enqueue_style( 'tp_twitter_plugin_css', TP_RECENT_TWEETS_PATH . 'tp_twitter_plugin.css', array(), '1.0', 'screen' );
	}
	add_action( 'wp_enqueue_scripts', 'tp_twitter_plugin_styles' );

	// include widget function
	require_once('widget.php');

	// Link to settings page from plugins screen
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

	function add_action_links ( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'options-general.php?page=recent-tweets' ) . '">Settings</a>',
		);

		return array_merge( $links, $mylinks );
	}

	// Settings menu
	add_action('admin_menu', 'tp_twitter_plugin_menu_item');

	function tp_twitter_plugin_menu_item() {
		add_options_page( 'Recent Tweets', 'Recent Tweets', 'manage_options', 'recent-tweets', 'tp_twitter_plugin_settings_page');
	}

	function tp_twitter_plugin_settings_page() {
		include(plugin_dir_path( __FILE__ ).'/settings.php');
	}

	//add_option('recent-tweets-options', array('support-us' => 0));


	function register_tp_twitter_setting() {
		register_setting( 'tp_twitter_plugin_options', 'tp_twitter_plugin_options'); 
	} 
add_action( 'admin_init', 'register_tp_twitter_setting' );
