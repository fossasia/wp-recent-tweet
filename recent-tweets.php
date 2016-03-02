<?php

/*
Plugin Name: Recent Tweets Widget
Plugin URI: http://wordpress.org/extend/plugins/recent-tweets-widget/
Description: Recent Tweets Widget plugin for Twitter API v1.1 with Cache. It uses the new Twitter API v1.1 and stores tweets in the cache. It means that it will read status messages from your database and it doesn't query Twitter.com for every page load so you won't be rate limited. You can set how often you want to update the cache.
Version: 1.6.5
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
/*
add_action('admin_menu', 'tp_twitter_plugin_menu_item');

function tp_twitter_plugin_menu_item() {
	add_options_page( 'Recent Tweets', 'Recent Tweets', 'manage_options', 'recent-tweets', 'tp_twitter_plugin_settings_page');
}
*/

function tp_twitter_plugin_settings_page() {
	include(plugin_dir_path( __FILE__ ).'/settings.php');
}

function tp_twitter_other_plugins_page() {
	include(plugin_dir_path( __FILE__ ).'/other_plugins.php');
}

function register_tp_twitter_setting() {
	register_setting( 'tp_twitter_plugin_options', 'tp_twitter_plugin_options'); 
} 
add_action( 'admin_init', 'register_tp_twitter_setting' );
//delete_option('tp_twitter_global_notification');
add_option('tp_twitter_global_notification', 1);

function tp_twitter_plugin_top_level_menu() {
	add_menu_page( 'Recent Tweets', 'Recent Tweets', 'manage_options', 'recent-tweets', 'tp_twitter_plugin_settings_page', 'dashicons-twitter');
	add_submenu_page( 'recent-tweets', 'Other Plugins', 'Other Plugins', 'manage_options', 'other-plugins', 'tp_twitter_other_plugins_page');
}

add_action( 'admin_menu', 'tp_twitter_plugin_top_level_menu' );


function tp_twitter_plugin_global_notice() {
	if (in_array(substr(basename($_SERVER['REQUEST_URI']), 0, 11), array('plugins.php', 'index.php')) && get_option('tp_twitter_global_notification') == 1) {
		?>
			<style type="text/css">
				#tp_twitter_global_notification a.button:active {vertical-align:baseline;}
			</style>
			<div class="updated" id="tp_twitter_global_notification" style="border:3px solid #317A96;position:relative;background:##3c9cc2;background-color:#3c9cc2;color:#ffffff;height:70px;">
				<a class="notice-dismiss" href="<?php echo admin_url('admin.php?page=recent-tweets&tp_twitter_global_notification=0'); ?>" style="right:165px;top:0;"></a>
				<a href="<?php echo admin_url('admin.php?page=recent-tweets&tp_twitter_global_notification=0'); ?>" style="position:absolute;top:9px;right:15px;color:#ffffff;">Dismiss and go to settings</a>
				<p style="font-size:16px;line-height:50px;">
					<?php _e('Looking for more sharing tools?'); ?> &nbsp;<a style="background-color: #6267BE;border-color: #3C3F76;" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox button button-primary">Get SumoMe WordPress Plugin</a>
				</p>
	        </div>
		<?php
	}
}
add_action( 'admin_notices', 'tp_twitter_plugin_global_notice' );


function tp_twitter_plugin_deactivate() {
	delete_option('tp_twitter_global_notification');
}
register_deactivation_hook( __FILE__, 'tp_twitter_plugin_deactivate' );
