<?php

/*
Plugin Name: Social Feeds
Plugin URI: https://github.com/joshuaadrian/social-feeds
Description: Pull in Twitter and Instagram feeds. Place them with shortcodes.
Author: Joshua Adrian
Version: 0.2.0
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
define('SF_PATH', plugin_dir_path(__FILE__));
// DEFINE PLUGIN URL
define( 'SF_URL_PATH', plugins_url() . '/social-feeds');
// DEFINE PLUGIN ID
define('PLUGINOPTIONS_ID', 'social-feeds');
// DEFINE PLUGIN NICK
define('PLUGINOPTIONS_NICK', 'Social Feeds');
// DEFINE PLUGIN NICK
register_activation_hook(__FILE__, 'sf_add_defaults');
// DEFINE PLUGIN NICK
register_uninstall_hook(__FILE__, 'sf_delete_plugin_options');
// ADD LINK TO ADMIN
add_action('admin_init', 'sf_init' );
// ADD LINK TO ADMIN
add_action('admin_menu', 'sf_add_options_page');
// ADD LINK TO ADMIN
add_filter( 'plugin_action_links', 'sf_plugin_action_links', 10, 2 );
// GET OPTION
$social_feeds_options = get_option('sf_options');

if ( !function_exists( 'get_plugins' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
if ( is_admin() ) {
  $sf_data = get_plugin_data( SF_PATH . plugin_basename( dirname( __FILE__ ) ) . '.php', false, false );
}

if ( !function_exists('markdown') ) {
	require_once SF_PATH . 'inc/libs/php-markdown/markdown.php';
}

/************************************************************************/
/* Delete options table entries ONLY when plugin deactivated AND deleted
/************************************************************************/

function sf_delete_plugin_options() {
	delete_option('sf_options');
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
//delete_option( 'sf_options' ); sf_add_defaults();
// Define default option settings
function sf_add_defaults() {

	$tmp = get_option('sf_options');

    if ( ( $tmp['default_settings'] == '1' ) || ( !is_array( $tmp ) ) ) {

		delete_option( 'sf_options' );

		$sf_defaults = array(
			'cron_frequency'                    => 'every_fifteen_minutes',
			'skin'                              => 'none',
			'twitter_cache'                     => '',
			'twitter_error_log'                 => '',
			'twitter_username'                  => 'joshua__adrian',
			'twitter_search_term'               => '#bananas',
			'twitter_include_rts'               => '1',
			'twitter_include_replies'           => '1',
			'twitter_include_entities'          => '1',
			'twitter_oauth_access_token'        => '',
			'twitter_oauth_access_token_secret' => '',
			'twitter_consumer_key'              => '',
			'twitter_consumer_secret'           => '',
			'instagram_cache'                   => '',
			'instagram_error_log'               => '',
			'instagram_access_token'            => '',
			'instagram_user_id'                 => '',
			'default_settings'                  => ''
		);

		update_option( 'sf_options', $sf_defaults );

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
	add_options_page('Social Feeds', '<img class="menu_sf" src="' . plugins_url( 'images/social.gif' , __FILE__ ) . '" alt="" />'.PLUGINOPTIONS_NICK, 'manage_options', PLUGINOPTIONS_ID, 'sf_render_form');
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

	    <?php 
	    	global $sf_data;
	    	screen_icon();
	    ?>

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

		    <form action="options.php" method="post" id="<?php echo PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo PLUGINOPTIONS_ID; ?>-options-form">

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
						    		<label for="twitter_user">Twitter Oauth Access Token</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_oauth_access_token]" value="<?php echo $options['twitter_oauth_access_token']; ?>" />
								</td>
							</tr>
							<tr>
						    	<th>
						    		<label for="twitter_user">Twitter Oauth Access Token Secret</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_oauth_access_token_secret]" value="<?php echo $options['twitter_oauth_access_token_secret']; ?>" />
								</td>
							</tr>
							<tr>
						    	<th>
						    		<label for="twitter_user">Twitter Consumer Key</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_consumer_key]" value="<?php echo $options['twitter_consumer_key']; ?>" />
								</td>
							</tr>
							<tr>
						    	<th>
						    		<label for="twitter_user">Twitter Consumer Secret</label>
						    	</th>
						    	<td> 
						    		<input type="text" size="57" name="sf_options[twitter_consumer_secret]" value="<?php echo $options['twitter_consumer_secret']; ?>" />
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

						</table>

		    	</li>
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

									if ( $handle = opendir( SF_PATH . 'css/skins' ) ) {
									    while (false !== ($entry = readdir($handle))) {
									    	if ($entry != "." && $entry != "..") { ?>
									        	<option value='<?php echo $entry; ?>' <?php selected($entry, $options['skin']); ?>><?php echo ucwords( str_replace( '-', ' ', $entry ) ); ?></option>
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
		    			<?php
		    			$text = file_get_contents(SF_PATH . 'README.md');
							$html = Markdown($text);
							echo $html;
						?>
						<h2>Using the ShortCodes and Their Options</h2>
						
						<h3>Twitter</h3>
						<p>
							This is the basic usage it will return the tweets in an unordered list.
							<pre><code>[twitter_feed]</code></pre>
						</p>

						<p>
							The Twitter shortcode has two options. Count and Date_Format
							<pre><code>[twitter_feed]</code></pre>
						</p>
						
						<h2>Header Level 2</h2>
						
						<ol>
							<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
							<li>Aliquam tincidunt mauris eu risus.</li>
						</ol>
						
						<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>
						
						<h3>Header Level 3</h3>
						
						<ul>
							<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
							<li>Aliquam tincidunt mauris eu risus.</li>
						</ul>
						
						<pre><code>
						#header h1 a { 
							display: block; 
							width: 300px; 
							height: 80px; 
						}
						</code></pre>
					</div>		
		    	</li>
		    </ul>
		    <p class="submit"><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
		</form>
		<div class="credits">
			<p><?php echo $sf_data['Name']; ?> Plugin | Version <?php echo $sf_data['Version']; ?> | <a href="<?php echo $sf_data['PluginURI']; ?>">Plugin Website</a> | Author <a href="<?php echo $sf_data['AuthorURI']; ?>"><?php echo $sf_data['Author']; ?></a> | <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="position:relative; top:3px; margin-left:3px"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a><a href="http://joshuaadrian.com" target="_blank" class="alignright"><img src="<?php echo plugins_url( 'images/ja-logo.gif' , __FILE__ ); ?>" alt="Joshua Adrian" /></a></p>
		</div>
	</div>
<?php
}

/************************************************************************/
/* Sanitize and validate input. Accepts an array, return a sanitized array.
/************************************************************************/

function sf_validate_options( $input ) {

	global $social_feeds_options;

	$input['cron_frequency']           = wp_filter_nohtml_kses($input['cron_frequency']);
	$input['skin']                     = wp_filter_nohtml_kses($input['skin']);
	$input['twitter_cache']            = $social_feeds_options['twitter_cache'];
	$input['twitter_error_log']        = $social_feeds_options['twitter_error_log'];
	$input['twitter_username']         = wp_filter_nohtml_kses($input['twitter_username']);
	//$input['twitter_search_term']      = wp_filter_nohtml_kses($input['twitter_search_term']);
	$input['twitter_post_count']       = wp_filter_nohtml_kses($input['twitter_post_count']);
	$input['twitter_include_rts']      = wp_filter_nohtml_kses($input['twitter_include_rts']);
	$input['twitter_include_replies']  = wp_filter_nohtml_kses($input['twitter_include_replies']);
	$input['twitter_include_entities'] = wp_filter_nohtml_kses($input['twitter_include_entities']);
	$input['instagram_cache']          = $social_feeds_options['instagram_cache'];
	$input['instagram_error_log']      = $social_feeds_options['instagram_error_log'];
	$input['instagram_access_token']   = wp_filter_nohtml_kses($input['instagram_access_token']);
	$input['instagram_user_id']        = wp_filter_nohtml_kses($input['instagram_user_id']);
	$input['instagram_count']          = wp_filter_nohtml_kses($input['instagram_count']);

	return $input;
}

/************************************************************************/
/* Display a Settings link on the main Plugins page
/************************************************************************/

function sf_plugin_action_links( $links, $file ) {
	$tmp_id = PLUGINOPTIONS_ID . '/social-feeds.php';
	if ( $file == $tmp_id ) {
		$sf_links = '<a href="'.get_admin_url().'options-general.php?page='.PLUGINOPTIONS_ID.'">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $sf_links );
	}

	return $links;
}

/************************************************************************/
/* IMPORT CSS AND JAVASCRIPT STYLES
/************************************************************************/

function my_enqueue() {
  wp_register_style('social_feeds_css', plugins_url('/css/social-feeds.css', __FILE__), false, '1.0.0');
  wp_enqueue_style('social_feeds_css');
  wp_enqueue_script('social_feeds_scripts', plugins_url('/js/social-feeds.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'my_enqueue');

function skin_styles() {
	$skin = get_option('sf_options');
	$skin = $skin['skin'];

	if ($skin != 'none') {
		wp_register_style('sf-skin-default', plugins_url('/css/skins/'.$skin.'/style.css', __FILE__), false, '1.0.0');
		wp_enqueue_style('sf-skin-default');
		wp_enqueue_script('sf-skin-default', plugins_url('/css/skins/'.$skin.'/app.min.js', __FILE__), array('jquery'), '1.0.0');
	}
}

add_action('wp_enqueue_scripts', 'skin_styles');

/************************************************************************/
/* INCLUDES
/************************************************************************/

require SF_PATH . 'inc/social-feeds-functions.php';
require SF_PATH . 'inc/social-feeds-events.php';
require SF_PATH . 'inc/social-feeds-shortcodes.php';

?>