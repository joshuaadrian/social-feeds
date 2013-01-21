<?php

	/************************************************************************/
	/* Remove the 2 main auto-formatters when wrapped in RAW tags
	/************************************************************************/

	remove_filter('the_content', 'wpautop');
	remove_filter('the_content', 'wptexturize');

	/************************************************************************/
	/* ENABLE SHORTCODES FOR TEXT WIDGETS
	/************************************************************************/

	add_filter('widget_text', 'shortcode_unautop');
	add_filter('widget_text', 'do_shortcode', 11);

	/************************************************************************/
	/* TWITTER FUNCTIONS
	/************************************************************************/

	function linkify_twitter_status($status_text) {
	  // linkify URLs
	  $status_text = preg_replace(
	    '/(https?:\/\/\S+)/',
	    '<a href="\1" target="_blank">\1</a>',
	    $status_text
	  );
	 
	  // linkify twitter users
	  $status_text = preg_replace(
	    '/(^|\s)@(\w+)/',
	    '\1@<a href="http://twitter.com/\2" target="_blank">\2</a>',
	    $status_text
	  );
	 
	  // linkify tags
	  $status_text = preg_replace(
	    '/(^|\s)#(\w+)/',
	    '\1#<a href="http://search.twitter.com/search?q=%23\2" target="_blank">\2</a>',
	    $status_text
	  );
	 
	  return $status_text;
	}

	function twitter_relative_time($time) {
		$tweet_time  = strtotime($time);
	    $delta = time() - $tweet_time;
	    if ( $delta < 60 ) {
	        return 'Less than a minute ago';
	    }
	    elseif ($delta > 60 && $delta < 120){
	        return 'About a minute ago';
	    }
	    elseif ($delta > 120 && $delta < (60*60)){
	        return strval(round(($delta/60),0)) . ' minutes ago';
	    }
	    elseif ($delta > (60*60) && $delta < (120*60)){
	        return 'About an hour ago';
	    }
	    elseif ($delta > (120*60) && $delta < (24*60*60)){
	        return strval(round(($delta/3600),0)) . ' hours ago';
	    }
	    else {
	        return time('F y g:i a', $tweet_time);
	    }
	};  

	/************************************************************************/
	/* TWITTER FEED SHORTCODE
	/************************************************************************/

	function sf_twitter_feed( $atts, $content = null ) {

		// GET SHORTCODE OPTIONS
		extract( shortcode_atts( array(
			'count' => 100
		), $atts ));

		// SET UP OUTPUT AND GET LOG FILE
		$twitter_output = '';
		$tweets         = file_get_contents(SF_PATH . 'cache/twitter.txt');
		$i = 0;

		// CHECK IF LOG FILE IS NOT EMPTY
		if ( $tweets ) {
			$tweets         = json_decode($tweets, true);

			foreach ($tweets as $tweet) {
				if ($i < $count) {
					$twitter_output .= '<li id="tweet-'.$i.'" class="tweet">';
					$twitter_output .= linkify_twitter_status($tweet['text']);
					$twitter_output .= ' <span class="tweet-time">';
					$twitter_output .= date( "F j, Y", strtotime($tweet['created_at']));
					$twitter_output .= '</span></li>';
					$i++;
				}
			}

			return '<ul class="tweets group">' . $twitter_output . '</ul>';
		}
	}

	add_shortcode('twitter_feed', 'sf_twitter_feed');

	/************************************************************************/
	/* INSTAGRAM FEED SHORTCODE
	/************************************************************************/

	function sf_instagram_feed( $atts, $content = null ) {

		// GET SHORTCODE OPTIONS
		extract( shortcode_atts( array(
			'count' => 100
		), $atts ));

		// SET UP OUTPUT AND GET LOG FILE
		$instagram_output = '';
		$instagrams_obj   = file_get_contents(SF_PATH . 'cache/instagram.txt');

		$i = 0;


		// CHECK IF LOG FILE IS NOT EMPTY
		if ($instagrams_obj) {
			$instagrams = json_decode($instagrams_obj, true);
		
			if ($instagrams['meta']['code'] == 200) {

				foreach ($instagrams['data'] as $instagram) {

					if ($i < $count) {
				
						if ($instagram['link']) {
							$instagram_output .= '<li id="instagram-'.$i.'" class="instagram"><a href="' . $instagram['images']['standard_resolution']['url'] . '" class="instagram-link" target="_blank"><img class="instagram-image" src="' . $instagram['images']['low_resolution']['url'] . '" />';
							$instagram_output .= '<span class="instagram-likes-count">' . $instagram['likes']['count'] . '</span></a>';

							if ($instagram['likes']['count'] > 0) {
								$instagram_output .= '<div id="instagram-'.$i.'-details" class="instagram-details"><ul class="instagram-likers">';
								foreach ($instagram['likes']['data'] as $like_user) {
									$instagram_output .= '<li class="instagram-liker"><img src="' . $like_user['profile_picture'] . '" alt="' . $like_user['username'] . '" title="' . $like_user['username'] . '" /></li>';
								}
								$instagram_output .= '</ul>';
								if ($instagram['caption']['text']) {
									$instagram_output .= '<span class="instagram-caption">' . $instagram['caption']['text'] . '</span>';
								}
								$instagram_output .= '<span class="instagram-created-time">' . date( "F j, Y", $instagram['created_time']) . '</span></div>';
							} else {
								$instagram_output .= '<div id="instagram-'.$i.'-details" class="instagram-details">';
								if ($instagram['caption']['text']) {
									$instagram_output .= '<span class="instagram-caption">' . $instagram['caption']['text'] . '</span>';
								}
								$instagram_output .= '<span class="instagram-created-time">' . date( "F j, Y", $instagram['created_time']) . '</span></div>';
							}
							
						}
						$i++;
					}
				}

			}

			return '<ul class="instagrams group">' . $instagram_output . '</ul>';
		}

	}

	add_shortcode('instagram_feed', 'sf_instagram_feed');

?>