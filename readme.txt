=== Recent Tweets Widget ===
Contributors: noahkagan
Tags: recent tweets, twitter widget, twitter api v1.1, cache, twitter, tweets, social media
Requires at least: 3.4.1
Tested up to: 4.5
Stable tag: 1.6.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Recent Tweets Widget plugin for new Twitter API v1.1 with CACHE, so you won't be rate limited at Twitter!

== Description ==

Recent Tweets Widget plugin for Twitter API v1.1 with Cache. It uses the new Twitter API v1.1 and stores tweets in the cache. It means that it will read status messages from your database and it doesn't query Twitter.com for every page load so you won't be rate limited. You can set how often you want to update the cache.


== Installation ==

1. Unzip the downloaded zip file.
2. Upload the `recent-tweets` folder and its contents into the `wp-content/plugins/` directory of your WordPress installation
3. Activate Recent Tweets Widget from Plugins page.
4. Go to your Widgets menu, add `* Recent Tweets` widget to a widget area.
5. Visit [https://apps.twitter.com/](https://apps.twitter.com/ "Twitter") in a new tab, sign in with your account, click on `Create a new application` and create your own keys if you haven't already.
6. Fill all your widget settings.
7. Enjoy your new Twitter feed! :)


== Frequently Asked Questions ==

= How can I get Consumer Key, Consumer Secret, Access Token and Access Token Secret?  =

You will need to visit [https://apps.twitter.com/](https://apps.twitter.com/ "Twitter"), sign in with your account and create your own keys.

== Screenshots ==

1. The widget
2. How it looks on frontend page

== Changelog ==

= 1.6.5 =
* Updated twitter logo
* Removed line break before time link and use css instead
* Fixed some undefined array key and undefined notices
* Fix for redeclaring OAuthException class

= 1.6.4 =
* Fix widget for WP 4.3

= 1.6 =
* Fix bug on settings page

= 1.5 =
* Settings page and other updates

= 1.4 =
* PHP 5.5 deprecated preg_replace() function fix
* Added gettext calls for easier translation

= 1.3 =
* Fix for special 4 byte UTF8 characters

= 1.2 =
* Header output issue fix

= 1.1 =
* Some styling issues were fixed
* Now the script checks if a function already exists
* Special function naming are applied so function dupes are also prevented
* Exclude replies option has been added

= 1.0 =
* The first version of this plugin! Enjoy! :)

== Upgrade Notice ==

= 1.0 =
None.

