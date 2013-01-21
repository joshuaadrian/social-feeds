<?php

	/************************************************************************/
	/* SET CUSTOM EVENT SCHEDULES
	/************************************************************************/

	add_filter('cron_schedules', 'sf_cron_schedules');

	function sf_cron_schedules() {
		return array(
			'every_minute' => array(
				'interval' => 60 * 1,
				'display' => 'Every minute'
			),
			'every_five_minutes' => array(
				'interval' => 60 * 5,
				'display' => 'Every five minutes'
			),
			'every_fifteen_minutes' => array(
				'interval' => 60 * 15,
				'display' => 'Every fifteen minutes'
			),
			'every_half_hour' => array(
				'interval' => 60 * 30,
				'display' => 'Every half hour'
			)
		);
	}

	/************************************************************************/
	/* SET EVENT SCHEDULE
	/************************************************************************/

	add_action('social_event', 'social_calls');

	function social_activation() {
		if ( !wp_next_scheduled('social_event') ) {
			$options   = get_option('sf_options');
			wp_schedule_event(current_time('timestamp'), $options['cron_frequency'], 'social_event');
		}
	}

	add_action('wp', 'social_activation');

	/************************************************************************/
	/* CURL CALLS
	/************************************************************************/

	function social_calls() {

		// GET PLUGIN OPTIONS
		$options   = get_option('sf_options');
		$error_log = SF_PATH . 'cache/error-log.txt';

		// CHECK FOR TWITTER INFO
		if ($options['twitter_username']) {

			// TWITTER VARIABLES
			$twitter_username    = $options['twitter_username'];
			$twitter_post_count  = $options['twitter_post_count'];
			//$twitter_search_term = $options['twitter_search_term'];
			$twitter_include_rts  = $options['twitter_include_rts'];
			if ($twitter_include_rts) {
				$twitter_include_rts = 'true';
			} else {
				$twitter_include_rts = 'false';
			}
			$twitter_include_replies  = $options['twitter_include_replies'];
			if ($twitter_include_replies) {
				$twitter_include_replies = 'false';
			} else {
				$twitter_include_replies = 'true';
			}
			$twitter_include_entities  = $options['twitter_include_entities'];
			if ($twitter_include_entities) {
				$twitter_include_entities = 'true';
			} else {
				$twitter_include_entities = 'false';
			}
			//_log('twitter_search_term => '. $twitter_search_term .' twitter_include_rts => '. $twitter_include_rts.' twitter_include_replies => '. $twitter_include_replies.' twitter_include_entities => '. $twitter_include_entities);

			// SET LOG FILE
			$twitter_cache = SF_PATH . 'cache/twitter.txt';

			// TWITTER CURL CALL
			$twitter_ch = curl_init();
			curl_setopt ($twitter_ch, CURLOPT_URL, 'http://api.twitter.com/1/statuses/user_timeline/'.$twitter_username.'.json?count='.$twitter_post_count.'&include_rts='.$twitter_include_rts.'&exclude_replies='.$twitter_include_replies.'&include_entities='.$twitter_include_entities);
			curl_setopt ($twitter_ch, CURLOPT_RETURNTRANSFER, 1);
			$twitter_data = curl_exec($twitter_ch);
			curl_close($twitter_ch);
			$twitter_obj = json_decode($twitter_data, true);

			// CHECK FOR ERROR RESPONSE
			if (isset($twitter_obj[0]['error']) || isset($twitter_obj[0]['errors']) || isset($twitter_obj['error']) || isset($twitter_obj['errors'])) {
				$twitter_errorfile = fopen($error_log, 'a');
				$twitter_error_message = date("F j, Y, g:i a") . ' - ' . $twitter_error_code . "\r\n\n";
				fwrite($twitter_errorfile,utf8_encode($twitter_error_message));
				fclose($twitter_errorfile);
			} else {
				$twitter_cachefile = fopen($twitter_cache, 'wb');
				fwrite($twitter_cachefile,utf8_encode($twitter_data));
				fclose($twitter_cachefile);
			}
		}

		// CHECK FOR INSTAGRAM INFO
		if ($options['instagram_user_id']) {

			// INSTAGRAM VARIABLES
			$instagram_access_token  = $options['instagram_access_token'];
			$instagram_user_id       = $options['instagram_user_id'];
			$instagram_count         = $options['instagram_count'];

			// SET LOG FILE
			$instagram_cache = SF_PATH . 'cache/instagram.txt';

			// INSTAGRAM CURL CALL
		    $instagram_url = 'https://api.instagram.com/v1/users/'.$instagram_user_id.'/media/recent/?access_token='. $instagram_access_token.'&count='. $instagram_count;
		    $instagram_ch = curl_init();
		    curl_setopt ($instagram_ch, CURLOPT_URL, $instagram_url);
		    curl_setopt ($instagram_ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($instagram_ch, CURLOPT_TIMEOUT, 20);
		    curl_setopt ($instagram_ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		    curl_setopt ($instagram_ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		    curl_setopt ($instagram_ch, CURLOPT_RETURNTRANSFER, 1); 
		    $instagram_data = curl_exec($instagram_ch);
		    $instagram_error = curl_errno($instagram_ch);
		    curl_close($instagram_ch);
		    $instagrams = json_decode($instagram_data, true);

		    // CHECK FOR ERRORS AND WRITE JSON TO FILE
		    if ($instagrams['meta']['code'] == 200) {
		    	$instagram_cachefile = fopen($instagram_cache, 'wb');
			    fwrite($instagram_cachefile,utf8_encode($instagram_data));
			    fclose($instagram_cachefile);
		    } else {
			    $instagram_errorfile = fopen($error_log, 'a');
				$instagram_error_message = date("F j, Y, g:i a") . ' instagram error json => ' . $instagram_data . ' | rest call url =>  ' . $instagram_url . "\r\n\n";
				fwrite($instagram_errorfile,utf8_encode($instagram_message));
				fclose($instagram_errorfile);
			}
		}

	}

?>