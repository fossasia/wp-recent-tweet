<?php
add_thickbox();
$tp_twitter_plugin_options = get_option('tp_twitter_plugin_options');

if (array_key_exists('tp_twitter_global_notification', $_GET) && $_GET['tp_twitter_global_notification'] == 0) {
        update_option('tp_twitter_global_notification', 0);
}
?>
<div class="wrap">
        <?php settings_errors(); ?>
        <style type="text/css">
                #tp_twitter_global_notification a.button:active {vertical-align:baseline;}
        </style>
        <div id="tp_twitter_global_notification" class="updated" style="border:3px solid #317A96;position:relative;background:##3c9cc2;background-color:#3c9cc2;color:#ffffff;height:70px;">
                <p style="font-size:16px;line-height:50px;">
                        <?php _e('Looking for more sharing tools?'); ?> &nbsp;<a style="background-color: #6267BE;border-color: #3C3F76;" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox button button-primary">Get SumoMe WordPress Plugin</a>
                </p>
        </div>
	<?php screen_icon(); ?>
	<h2>Recent Tweets</h2>
        <h3>Adding the Widget</h3>
        <ol>
                <li><a href="<?php echo admin_url('widgets.php'); ?>" target="_blank">Go to your Widgets menu</a>, add the <code>Recent Tweets</code> widget to a widget area.</li>
                <li>Visit <a href="https://apps.twitter.com/">https://apps.twitter.com/</a>, sign in with your account, click on <code>Create New App</code> and create your own keys if you haven't already.</li>
                <li>Fill all your widget settings.</li>
                <li>Enjoy your new Twitter feed! :)</li>
        </ol>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'tp_twitter_plugin_options' ); ?>
		<table class="form-table">
                	<tr valign="top">
                		<td scope="row" colspan="2">
                                        <select name="tp_twitter_plugin_options[support-us]" id="support-us">
                                                <option value="1" <?php echo is_array($tp_twitter_plugin_options) && $tp_twitter_plugin_options['support-us'] == '1' ? 'selected="selected"' : ''; ?>>Yes</option>
                                                <option value="0" <?php echo !is_array($tp_twitter_plugin_options) || $tp_twitter_plugin_options['support-us'] != '1' ? 'selected="selected"' : ''; ?>>No</option>
                                        </select>
                                        <p>Show our link below the widget. Pretty please.</p>
                                </td>
                		
                	</tr>
		</table>
		<?php submit_button(); ?>
	</form>

        <h3>Signup for a free 30 day course to DOUBLE YOUR EMAIL LIST</h3>
        <form method="post" class="af-form-wrapper" action="http://www.aweber.com/scripts/addlead.pl" target="_blank">
                <p>
                        <input placeholder="Type Your Email Address" class="email" name="email" autofocus style="width:200px;" />
                </p>
                <p>
                        <button class="button button-primary">Let me in!</button>
                </p>

                <input type="hidden" name="meta_web_form_id" value="1747290999" />
                <input type="hidden" name="meta_split_id" value="" />
                <input type="hidden" name="listname" value="awlist3626406" />
                <input type="hidden" name="redirect" value="http://email1k.sumome.com/tweet.html" id="redirect_19605a373ab8e7f77fc954424326ab1c" />
                <input type="hidden" name="meta_redirect_onlist" value="http://email1k.sumome.com/tweet.html" />
                <input type="hidden" name="meta_adtracking" value="recent-tweets-widget" />
                <input type="hidden" name="meta_message" value="1" />
                <input type="hidden" name="meta_required" value="email" />
                <input type="hidden" name="meta_tooltip" value="" />
        </form>

        <!--<p style="border:1px solid #CCCCCC;background:#FFFFFF;padding:8px;">Check out more sharing tools with our <a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox">SumoMe WordPress plugin</a></p>-->
        <p><i>If you find this plugin useful please <a href="https://wordpress.org/support/view/plugin-reviews/recent-tweets-widget" target="_blank">leave us a review!</a></i></p>
</div>