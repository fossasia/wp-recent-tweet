function update_twitter_auth(arg) {
    jQuery("#widget-tp_widget_recent_tweets-2-consumerkey").prop('disabled', arg);
	jQuery("#widget-tp_widget_recent_tweets-2-consumersecret").prop('disabled', arg);
	jQuery("#widget-tp_widget_recent_tweets-2-accesstoken").prop('disabled', arg);
	jQuery("#widget-tp_widget_recent_tweets-2-accesstokensecret").prop('disabled', arg);
}


jQuery(function() {
	if(jQuery("#widget-tp_widget_recent_tweets-2-loklak_api").prop('checked')){
		update_twitter_auth(true);
	}
	
    jQuery("#widget-tp_widget_recent_tweets-2-loklak_api").live('change', function() {
    	if(jQuery(this).is(':checked')){
	    	update_twitter_auth(true);
	    }
	    else {
	    	update_twitter_auth(false);
	    }
	});
});