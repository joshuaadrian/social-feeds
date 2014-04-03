<?php

/************************************************************************/
/* SET CUSTOM EVENT SCHEDULES
/************************************************************************/

add_filter('cron_schedules', 'sf_cron_schedules');

function sf_cron_schedules( $schedules ) {

	if ( !array_key_exists('every_five_minutes', $schedules) ) {
		$schedules['every_five_minutes'] = array(
			'interval' => 60 * 5,
			'display' => __( 'Every five minutes' )
		);
	}

	if ( !array_key_exists('every_fifteen_minutes', $schedules) ) {
		$schedules['every_fifteen_minutes'] = array(
			'interval' => 60 * 15,
			'display' => __( 'Every fifteen minutes' )
		);
	}

	if ( !array_key_exists('every_half_hour', $schedules) ) {
		$schedules['every_half_hour'] = array(
			'interval' => 60 * 30,
			'display' => __( 'Every half hour' )
		);
	}

	return $schedules;

}

/************************************************************************/
/* SET EVENT SCHEDULE
/************************************************************************/

add_action('sf_social_event', 'sf_social_calls');

function sf_social_activation() {
	if ( !wp_next_scheduled('sf_social_event') ) {
		wp_schedule_event(current_time('timestamp'), 'every_fifteen_minutes', 'sf_social_event');
	}
}

add_action('wp', 'sf_social_activation');

/************************************************************************/
/* CLEAN EMOJI FROM INSTAGRAM FEED
/************************************************************************/
function removeInstagramEmoji( $instagrams ) {

	$clean_text = '';

	foreach ($instagrams as $instagram_key => $instagram) {
		
		if ( $instagram['comments']['count'] > 0 ) {

			foreach ( $instagram['comments']['data'] as $instagram_comment_key => $instagram_comment_value ) {

				$instagram_comment_value['text'];

				// Match Emoticons
			    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
			    $clean_text = preg_replace($regexEmoticons, '', $instagram_comment_value['text']);

			    // Match Miscellaneous Symbols and Pictographs
			    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
			    $clean_text = preg_replace($regexSymbols, '', $clean_text);

			    // Match Transport And Map Symbols
			    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
			    $clean_text = preg_replace($regexTransport, '', $clean_text);

			    $instagrams[$instagram_key]['comments']['data'][$instagram_comment_key]['text'] = $clean_text;

			}

		}

	}

    return $instagrams;

}

/************************************************************************/
/* ENABLE SHORTCODES FOR TEXT WIDGETS
/************************************************************************/

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode', 11);

/************************************************************************/
/* TWITTER FUNCTIONS
/************************************************************************/

function linkify_twitter_status( $status_text ) {
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

function twitter_relative_time( $time ) {
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
        return date('F y g:i a', $tweet_time);
    }
};

/************************************************************************/
/* EMBED PINTEREST SCRIPT
/************************************************************************/
$social_feeds_shortcode_pinterest = false;

function social_feeds_print_my_script() {

	global $social_feeds_shortcode_pinterest;

	if ( ! $social_feeds_shortcode_pinterest )
		return;

	wp_print_scripts('social-feeds-pinterest');
}

add_action('wp_footer', 'social_feeds_print_my_script');
