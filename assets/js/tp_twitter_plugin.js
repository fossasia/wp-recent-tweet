function update_twitter_auth(arg) {
    jQuery(".rtw-consumerkey").prop('disabled', arg);
	jQuery(".rtw-consumersecret").prop('disabled', arg);
	jQuery(".rtw-accesstoken").prop('disabled', arg);
	jQuery(".rtw-accesstokensecret").prop('disabled', arg);
}


jQuery(function() {
	console.log("init");
	if(jQuery(".rtw-loklak_api").prop('checked')){
		console.log("init");
		update_twitter_auth(true);
	}

    jQuery(".rtw-loklak_api").live('change', function() {
    	if(jQuery(this).is(':checked')){
	    	update_twitter_auth(true);
	    }
	    else {
	    	update_twitter_auth(false);
	    }
	});
	
});