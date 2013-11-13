#Social Feeds
Contributors: Joshua Adrian

Tags: Social, Feeds, Twitter, Instagram

Requires at least: 2.5

Tested up to: 3.7

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

###0.2.0

* Moved social feed caches to be stored as an option in the WP database.
* Using new Twitter authentication library
* Using SASS instead of less now.

###0.1.0

* Plugin released

#Usage

##Using the ShortCodes and Their Options</h2>
					
###Twitter
					
This is the basic usage it will return the tweets in an unordered list.

	[twitter_feed]

You can also set the amount of tweets to show, with a max up to 20 and a default of 4.

	[twitter_feed count=10]

###Instagram
					
This is the basic usage it will return the Instagrams in an unordered list.

	[instagram_feed]

You can also set the amount of Instagrams to show, with a max up to 20 and a default of 8.

	[instagram_feed count=10]