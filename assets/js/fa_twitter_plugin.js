jQuery(function() {
	if(jQuery(".rtw-loklak_api").prop('checked')){
		console.log("init");
		rtw_update_twitter_auth(true);
	}

    jQuery(".rtw-loklak_api").live('change', function() {
    	if(jQuery(this).is(':checked')){
	    	rtw_update_twitter_auth(true);
	    }
	    else {
	    	rtw_update_twitter_auth(false);
	    }
	});

	function rtw_update_twitter_auth(arg) {
	    jQuery(".rtw-consumerkey").prop('disabled', arg);
		jQuery(".rtw-consumersecret").prop('disabled', arg);
		jQuery(".rtw-accesstoken").prop('disabled', arg);
		jQuery(".rtw-accesstokensecret").prop('disabled', arg);
	}
});
