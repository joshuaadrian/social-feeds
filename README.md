#Social Feeds
Contributors: Joshua Adrian

Tags: Social, Feeds, Twitter, Instagram

Requires at least: 2.5

Tested up to: 3.8.1

Stable tag: trunk

##Description

What this plugin does is to easily integrate social feeds via shortcodes into your website.

##Features:

* Shortcodes
* Selectable default styling

##Included languages:

* English

##Frequently Asked Questions

None.

##Installation

1. Download the plugin and unzip it.
2. Upload the folder social-feeds/ to your /wp-content/plugins/ folder.
3. Activate the plugin from your WordPress admin panel.
4. Installation finished.

##Changelog

###0.4.0

* Added widgets for each feed.

###0.3.0

* Added Pinterest embed capabilities.
* Refactored to use the WP settings API

###0.2.0

* Moved social feed caches to be stored as an option in the WP database.
* Using new Twitter authentication library
* Using SASS instead of Less now.

###0.1.0

* Plugin released

#Usage

##Using the ShortCodes and Their Options</h2>
					
###Twitter
					
This is the basic widget will return the tweets in an unordered list.

	[twitter_feed]

You can also set the amount of tweets to show, with a max up to 20 and a default of 4.

	[twitter_feed count=10]

###Instagram
					
This is the basic usage it will return the Instagrams in an unordered list.

	[instagram_feed]

You can also set the amount of Instagrams to show, with a max up to 20 and a default of 8.

	[instagram_feed count=10]

###Pinterest
					
This is the basic usage that will embed the designated Pinterest board.

	[pinterest_feed]

You can also designate the content (you can use the URL of a pin, profile, or board) to use in the Instagram shortcode and override the default pin designated in the Pinterest settings.

Example of a Pin:

	[pinterest_feed content='http://www.pinterest.com/pin/99360735500167749/']

Example of a Profile:

	[pinterest_feed content='http://www.pinterest.com/pinterest/']

Example of a Board:

	[pinterest_feed board='http://www.pinterest.com/pinterest/pin-pets/']

##Using the Widgets and Their Options</h2>
					
###Twitter Widget
					
This is the basic widget will return the tweets in an unordered list.

The only option is 'Count' which determines the number of Tweets to display.

###Instagram
					
This is the basic widget will return the Instagrams in an unordered list.

The only option is 'Count' which determines the number of Instagrams to display.

###Pinterest
					
This is the basic widget will embed the designated Pinterest board.

The only option is 'Content' which determines the Pinterest content to embed.


	