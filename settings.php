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
        				<option value="1" <?php echo $tp_twitter_plugin_options['support-us'] == '1' ? 'selected="selected"' : ''; ?>>Yes</option>
        				<option value="0" <?php echo !array_key_exists('support-us', $tp_twitter_plugin_options) || $tp_twitter_plugin_options['support-us'] == '0' ? 'selected="selected"' : ''; ?>>No</option>
        			</select>
        		</td>
        	</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>