<?php

/************************************************************************/
/* TWITTER FEED SHORTCODE
/************************************************************************/

function sf_twitter_feed( $atts, $content = null ) {

	global $social_feeds_options;

	// GET SHORTCODE OPTIONS
	extract( shortcode_atts( array(
		'date_format' => 'joshua__adrian',
		'count' => '10'
	), $atts ));

	if ( $count > 20 )
		$count = 20;

	if ( !isset( $social_feeds_options['twitter_cache'] ) )
		return;

	// SET UP OUTPUT AND GET CACHED TWITTER FEED FILE
	$twitter_output = '';
	$tweets         = $social_feeds_options['twitter_cache'];

	for ( $i = 0; $i < $count; ++$i ) {

		$twitter_output .= '<li id="tweet-'.$i.'" class="tweet">';
		$twitter_output .= linkify_twitter_status($tweets[$i]['text']);
		$twitter_output .= ' <span class="tweet-time">';
		$twitter_output .= date( "F j, Y", strtotime($tweets[$i]['created_at']));
		$twitter_output .= '</span></li>';

	}

	return '<h4><a href="http://twitter.com/' . $social_feeds_options['twitter_username'] . '">@' . $social_feeds_options['twitter_username'] . '</a></h4><ul class="tweets group">' . $twitter_output . '</ul>';

}

add_shortcode('twitter_feed', 'sf_twitter_feed');

/************************************************************************/
/* INSTAGRAM FEED SHORTCODE
/************************************************************************/

function sf_instagram_feed( $atts, $content = null ) {

	global $social_feeds_options;

	// GET SHORTCODE OPTIONS
	extract( shortcode_atts( array(
		'user' => 'person',
		'count' => '8'
	), $atts ));

	if ( $count > 20 )
		$count = 20;

	// SET UP OUTPUT AND GET LOG FILE
	$instagram_output = '';
	$instagrams       = $social_feeds_options['instagram_cache'];

	// CHECK IF LOG FILE IS NOT EMPTY
	if ( $instagrams ) {

		for ( $i = 0; $i < $count; ++$i ) {

			if ( $instagrams[$i]['link'] ) {

				_log($instagrams[$i]);

				$instagram_output .= '<li id="instagram-'.($i + 1).'" class="instagram"><a href="' . $instagrams[$i]['images']['standard_resolution']['url'] . '" class="instagram-link" target="_blank"><img class="instagram-image" src="' . $instagrams[$i]['images']['low_resolution']['url'] . '" />';
				$instagram_output .= '<span class="instagram-likes-count">' . $instagrams[$i]['likes']['count'] . '</span></a>';

				if ( $instagrams[$i]['likes']['count'] > 0 ) {
					$instagram_output .= '<div id="instagram-'.($i + 1).'-details" class="instagram-details"><ul class="instagram-likers">';
					foreach ( $instagrams[$i]['likes']['data'] as $like_user ) {
						$instagram_output .= '<li class="instagram-liker"><img src="' . $like_user['profile_picture'] . '" alt="' . $like_user['username'] . '" title="' . $like_user['username'] . '" /></li>';
					}
					$instagram_output .= '</ul>';
					if ($instagrams[$i]['caption']['text']) {
						$instagram_output .= '<span class="instagram-caption">' . $instagrams[$i]['caption']['text'] . '</span>';
					}
					$instagram_output .= '<span class="instagram-created-time">' . date( "F j, Y", $instagrams[$i]['created_time']) . '</span></div>';
				} else {
					$instagram_output .= '<div id="instagram-'.($i + 1).'-details" class="instagram-details">';
					if ($instagrams[$i]['caption']['text']) {
						$instagram_output .= '<span class="instagram-caption">' . $instagrams[$i]['caption']['text'] . '</span>';
					}
					$instagram_output .= '<span class="instagram-created-time">' . date( "F j, Y", $instagrams[$i]['created_time']) . '</span></div>';
				}
				
			}

		}

		return '<ul class="instagrams group">' . $instagram_output . '</ul>';
	}

}

add_shortcode('instagram_feed', 'sf_instagram_feed');

?>