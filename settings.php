<?php
$tp_twitter_plugin_options = get_option('tp_twitter_plugin_options');
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Recent Tweets</h2>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'tp_twitter_plugin_options' ); ?>
		<table class="form-table">
        	<tr valign="top">
        		<th scope="row"><label for="support-us">Support Us!</label></th>
        		<td>
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
        <h3>Adding the Widget</h3>
        <ol>
                <li><a href="<?php echo admin_url('widgets.php'); ?>">Go to your Widgets menu</a>, add the <code>Recent Tweets</code> widget to a widget area.</li>
                <li>Visit <a href="https://apps.twitter.com/">https://apps.twitter.com/</a>, sign in with your account, click on <code>Create New App</code> and create your own keys if you haven't already.</li>
                <li>Fill all your widget settings.</li>
                <li>Enjoy your new Twitter feed! :)</li>
        </ol>
        <p style="border:1px solid #CCCCCC;background:#FFFFFF;padding:8px;">Check out more sharing tools with our <a href="https://wordpress.org/plugins/sumome" target="_blank">SumoMe WordPress plugin</a></p>
</div>