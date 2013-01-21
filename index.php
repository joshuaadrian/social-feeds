<?php

/*
Plugin Name: Social Feeds
Plugin URI: http://joshuaadrian.com/social-feeds-plugin/
Description: Pull in and cache Twitter, Instagram feeds. Place them with shortcodes on pages, posts, and/or widgets.
Author: Joshua Adrian
Version: 0.5.0
Author URI: http://joshuaadrian.com
*/

/************************************************************************/
/* ERROR LOGGING
/************************************************************************/

/**
 *  Simple logging function that outputs to debug.log if enabled
 *  _log('Testing the error message logging');
 *	_log(array('it' => 'works'));
 */

if (!function_exists('_log')) {
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

/************************************************************************/
/* DEFINE PLUGIN ID AND NICK
/************************************************************************/

// DEFINE PLUGIN BASE
define( 'SF_PATH', plugin_dir_path(__FILE__) );
// DEFINE PLUGIN ID
define( 'SF_PLUGINOPTIONS_ID', 'social-feeds' );
// DEFINE PLUGIN NICK
define( 'SF_PLUGINOPTIONS_NICK', 'Social Feeds' );
// DEFINE PLUGIN NICK
register_activation_hook( __FILE__, 'sf_add_defaults' );
// DEFINE PLUGIN NICK
register_uninstall_hook( __FILE__, 'sf_delete_plugin_options' );
// ADD LINK TO ADMIN
add_action( 'admin_init', 'sf_init' );
// ADD LINK TO ADMIN
add_action( 'admin_menu', 'sf_add_options_page' );
// ADD LINK TO ADMIN
add_filter( 'plugin_action_links', 'sf_plugin_action_links', 10, 2 );

/************************************************************************/
/* ADD LOCALIZATION FOLDER
/************************************************************************/

function sf_plugin_setup() {
    load_plugin_textdomain( 'social-feeds', false, dirname(plugin_basename(__FILE__)) . '/lang/' );
}

add_action( 'after_setup_theme', 'sf_plugin_setup' );

/************************************************************************/
/* Delete options table entries ONLY when plugin deactivated AND deleted
/************************************************************************/

function sf_delete_plugin_options() {
	delete_option( 'sf_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'posk_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function sf_add_defaults() {
	$tmp = get_option('sf_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('sf_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(
			"cron_frequency"           => "every_fifteen_minutes",
			"skin"                     => "none",
			"twitter_username"         => "CurrentSeinfeld",
			"twitter_search_term"      => "#bananas",
			"twitter_post_count"       => "20",
			"twitter_include_rts"      => "1",
			"twitter_include_replies"  => "1",
			"twitter_include_entities" => "1",
			"chk_default_options_db"   => "",
			"instagram_access_token"   => "",
			"instagram_user_id"        => "10039007",
			"instagram_count"          => "20"
		);
		update_option('sf_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'posk_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function sf_init() {
	register_setting( 'sf_plugin_options', 'sf_options', 'sf_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'posk_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function sf_add_options_page() {
	add_options_page('Social Feeds', '<img class="menu_sf" src="' . plugins_url( 'images/social.gif' , __FILE__ ) . '" alt="" />'.SF_PLUGINOPTIONS_NICK, 'manage_options', SF_PLUGINOPTIONS_ID, 'sf_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function sf_render_form() { ?>
	<div class="wrap">

	    <?php screen_icon(); ?>

		    <h2>Social Feeds Settings</h2>

		    <ul class="sf_pagination group">
		    	<li class="sf-active" id="sf-pagination-twitter">
		    		<a href="#sf-twitter">Twitter</a>
		    	</li>
		    	<li id="sf-pagination-instagram">
		    		<a href="#sf-instagram">Instagram</a>
		    	</li>
		    	<!-- <li id="sf-pagination-facebook">
		    		<a href="#sf-facebook">Facebook</a>
		    	</li> -->
		    	<li id="sf-pagination-settings">
		    		<a href="#sf-settings">Settings</a>
		    	</li>
		    	<li id="sf-pagination-help">
		    		<a href="#sf-help">Help</a>
		    	</li>
		    </ul>
		    <form action="options.php" method="post" id="<?php echo SF_PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo SF_PLUGINOPTIONS_ID; ?>-options-form">
		    <ul class="sf_content">
		    	<li id="sf-twitter" class="sf-active">
			    		
		    			<?php
		    				settings_fields('sf_plugin_options');
							$options = get_option('sf_options');
		    			?>

			    		<table class="form-table">
					    	<tr>
						    	<th>
						    		<label for="twitter_user">Twitter User</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_username]" value="<?php echo $options['twitter_username']; ?>" />
								</td>
							</tr>
							<tr>
						    	<th>
						    		<label for="twitter_user">Twitter Post Count</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_post_count]" value="<?php echo $options['twitter_post_count']; ?>" />
								</td>
							</tr>
							<!-- <tr>
						    	<th>
						    		<label for="twitter_user">Twitter Search Term</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_search_term]" value="<?php //echo $options['twitter_search_term']; ?>" />
								</td>
							</tr> -->
							<tr>
								<th>Twitter Options</th>
						    	<td> 
						    		<label><input name="sf_options[twitter_include_rts]" type="checkbox" value="1" <?php if (isset($options['twitter_include_rts'])) { checked('1', $options['twitter_include_rts']); } ?> /> Include Retweets</label><br />

						    		<label><input name="sf_options[twitter_include_replies]" type="checkbox" value="1" <?php if (isset($options['twitter_include_replies'])) { checked('1', $options['twitter_include_replies']); } ?> /> Include Replies</label><br />

						    		<label><input name="sf_options[twitter_include_entities]" type="checkbox" value="1" <?php if (isset($options['twitter_include_entities'])) { checked('1', $options['twitter_include_entities']); } ?> /> Include Entities</label><br />
								</td>
							</tr>
						</table>

		    	</li>
		    	<li id="sf-instagram">

			    		<table class="form-table">

					    	<tr>
						    	<th>
						    		<label for="instagram_access_token">Instagram Access Token</label>
						    	</th>
						    	<td>
									<input type="text" name="sf_options[instagram_access_token]" value="<?php echo $options['instagram_access_token']; ?>" />
								</td>
							</tr>

							<tr>
						    	<th>
						    		<label for="instagram_user_id">Instagram User ID</label>
						    	</th>
						    	<td>
									<input type="text" name="sf_options[instagram_user_id]" value="<?php echo $options['instagram_user_id']; ?>" />
								</td>
							</tr>

							<tr>
						    	<th>
						    		<label for="instagram_count">Instagram Count</label>
						    	</th>
						    	<td>
									<input type="text" name="sf_options[instagram_count]" value="<?php echo $options['instagram_count']; ?>" />
								</td>
							</tr>
						</table>

		    	</li>
		    	<!-- <li id="sf-facebook">

			    		<table class="form-table">
					    	<tr>
						    	<th>
						    		<label for="twitter_user">Twitter User</label>
						    	</th>
						    	<td>
									<input type="text" name="twitter_user" value="<?php echo get_option('twitter_user'); ?>" />
								</td>
							</tr>

							<tr>
						    	<th>
						    		<label for="twitter_user">Twitter User</label>
						    	</th>
						    	<td>
									<input type="text" name="twitter_user" value="<?php echo get_option('twitter_user'); ?>" />
								</td>
							</tr>
						</table>

		    	</li> -->
		    	<li id="sf-settings">
		    		<div class="sf-copy">
						<h2>Global Plugin Settings</h2>
					</div>

		    		<table class="form-table">
				    	<tr>
							<th>
					    		<label for="sf_skin">Skin</label>
					    	</th>
					    	<td>
								<select name='sf_options[skin]'>
									<option value='none' <?php selected('none', $options['skin']); ?>>&mdash; None &mdash;</option>
									<?php
									if ($handle = opendir(SF_PATH . 'css/skins')) {
									    while (false !== ($entry = readdir($handle))) {
									    	if ($entry != "." && $entry != ".." && substr($entry, 0, 1) != '.') { ?>
									        	<option value='<?php echo $entry; ?>' <?php selected($entry, $options['skin']); ?>><?php echo ucfirst($entry); ?></option>
									    	<?php }
									    }
									    closedir($handle);
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
					    	<th>
					    		<label for="cron_freuquency">Cron Frequency</label>
					    	</th>
					    	<td> 
								<select name='sf_options[cron_frequency]'>
									<option value='every_five_minutes' <?php selected('every_five_minutes', $options['cron_frequency']); ?>>Every 5 minutes</option>
									<option value='every_fifteen_minutes' <?php selected('every_fifteen_minutes', $options['cron_frequency']); ?>>Every 15 minutes</option>
									<option value='every_half_hour' <?php selected('every_half_hour', $options['cron_frequency']); ?>>Every 30 minutes</option>
								</select>
							</td>
						</tr>
					</table>
		    	</li>
		    	<li id="sf-help">
		    		<div class="sf-copy">
						<h2>Using the ShortCodes and Their Options</h2>

						<h3>Usage</h3>
						
						<p>You may place the shortcodes in pages, posts, and/or widgets.</p>

						<h3>Twitter</h3>
						<p>
							This is the basic usage it will return the tweets in an unordered list.
							<pre><code>[twitter_feed]</code></pre>
						</p>

						<p>
							The Twitter shortcode has one option, <strong>count</strong>.
							<pre><code>[twitter_feed count="2"]</code></pre>
							The count number must be less than the global twitter count you have set on the twitter tab.
						</p>

						<h3>Instagram</h3>
						<p>
							This is the basic usage it will return the tweets in an unordered list.
							<pre><code>[instagram_feed]</code></pre>
						</p>

						<p>
							The Twitter shortcode has one option, <strong>count</strong>.
							<pre><code>[instagram_feed count="2"]</code></pre>
							The count number must be less than the global instagram count you have set on the instagram tab.
						</p>

						<h2>Using and Creating Skins</h2>

						<p>The default skin is placed in the plugins/social-feeds/css/skins/ folder. You may create or add a new skin by simply adding your skin folder to this folder.</p>

						<p>I've included a clean, simple skin called 'Fresh' that you are free to modify for your needs but would back it up since new versions of this plugin will overwrite everything in the social feeds folder.</p>
						
					</div>
					<?php
					//echo 'PLUGIN PATH => ' . SF_PATH . '<br />';
					//var_dump($options);
					?>			
		    	</li>
		    </ul>
			
		    <p class="submit"><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
		</form>
		<div class="credits">
			<p>Social Feeds Plugin | Version 0.1.0 | <a href="http://www.joshuaadrian.com/social-feeds-plugin/" target="_blank">Website</a> | Author <a href="http://joshuaadrian.com" target="_blank">Joshua Adrian</a> | <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="position:relative; top:3px; margin-left:3px"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a><a href="http://joshuaadrian.com" target="_blank" class="alignright"><img src="<?php echo plugins_url( 'images/ja-logo.gif' , __FILE__ ); ?>" alt="Joshua Adrian" /></a></p>
		</div>
	</div>
<?php
}

/************************************************************************/
/* Sanitize and validate input. Accepts an array, return a sanitized array.
/************************************************************************/

function sf_validate_options($input) {
	// strip html from textboxes
	//$input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['twitter_username']       =  wp_filter_nohtml_kses($input['twitter_username']);
	$input['twitter_post_count']     =  wp_filter_nohtml_kses($input['twitter_post_count']);
	$input['instagram_access_token'] =  wp_filter_nohtml_kses($input['instagram_access_token']);
	$input['instagram_user_id']      =  wp_filter_nohtml_kses($input['instagram_user_id']);
	$input['instagram_count']        =  wp_filter_nohtml_kses($input['instagram_count']);
	return $input;
}

/************************************************************************/
/* Display a Settings link on the main Plugins page
/************************************************************************/

function sf_plugin_action_links( $links, $file ) {
	$tmp_id = SF_PLUGINOPTIONS_ID . '/index.php';
	if ($file == $tmp_id) {
		$sf_links = '<a href="'.get_admin_url().'options-general.php?page='.SF_PLUGINOPTIONS_ID.'">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $sf_links );
	}
	return $links;
}

/************************************************************************/
/* IMPORT CSS AND JAVASCRIPT STYLES
/************************************************************************/

function sf_plugin_enqueue() {
  wp_register_style('social_feeds_css', plugins_url('/css/social-feeds.css', __FILE__), false, '1.0.0');
  wp_enqueue_style('social_feeds_css');
  wp_enqueue_script('social_feeds_scripts', plugins_url('/js/social-feeds.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'sf_plugin_enqueue');

function sf_plugin_skin_styles() {
	$skin = get_option('sf_options');
	$skin = $skin['skin'];

	if ($skin != 'none') {
		wp_register_style('sf-skin-default', plugins_url('/css/skins/'.$skin.'/style.css', __FILE__), false, '1.0.0');
		wp_enqueue_style('sf-skin-default');
		wp_enqueue_script('sf-skin-default', plugins_url('/css/skins/'.$skin.'/app.min.js', __FILE__), array('jquery'), '1.0.0');
	}
}

add_action('wp_enqueue_scripts', 'sf_plugin_skin_styles');

/************************************************************************/
/* INCLUDES
/************************************************************************/

require SF_PATH . 'inc/social-feeds-events.inc';
require SF_PATH . 'inc/social-feeds-shortcodes.inc';

?>