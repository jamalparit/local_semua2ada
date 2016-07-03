<?php
/*
Plugin Name: Language Selector
Plugin URI: http://www.synology.com/
Description: The plug-in enables you to select a display language on WordPress.
Version: 1.0
Author: Synology Inc.
Author URI: http://www.synology.com
*/

if (! defined("WPLANG")) {
  die;  // Silence is golden, direct call is prohibited
}

require_once('language-lib.php');

function language_options_page() {
	if (!current_user_can('activate_plugins')) {
		die('action is forbidden');
	}
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2><?php _e('Language'); ?></h2>
				<?php require_once('language-options.php'); ?>
	</div>
	<?php
}

function activate() {
	global $wp_version;
	if ( ! version_compare( $wp_version, '3.0', '>=') ) {
		if ( function_exists('deactivate_plugins') )
			deactivate_plugins(__FILE__);
		die(sprintf( __('<strong>Language Selector: </strong>This plugin requires version %s or later.'), '3.1'));
	}
	//add WP_LANG_DIR define 
	if (!SYNOActiveLanguagesPath(true)) {
		die(__('Plugin failed to reactivate due to a fatal error.'));
	}
}

function deactivate(){
	//delete WP_LANG_DIR define 
	if (!SYNOActiveLanguagesPath(false)) {
		die(__('Plugin failed to reactivate due to a fatal error.'));
	}
}

function admin_init() {
	load_plugin_textdomain('language-selector','', $folder.'/langs');
	if (function_exists('register_setting')) {
		register_setting('language-selector-options', 'language_selector');
	}
}


function action_links($links, $file) {
	if ($file == plugin_basename(dirname(__FILE__).'/language-selector.php')){
		$settings_link = "<a href='options-general.php?page=language-selector.php'>".__('Settings')."</a>";
		array_unshift( $links, $settings_link );
	}
	return $links;
}

function admin_css_enqueue() {
	wp_enqueue_style('admin_css',plugins_url('/language-selector/css/language-selector.css'), array(), false, 'screen');
}

function settings_menu() {
	if ( function_exists('add_options_page') ) {
		$page = add_options_page(__('Language'), __('Language'), 'create_users', basename(__FILE__), 'language_options_page');
		add_action( "admin_print_styles-$page", 'admin_css_enqueue' );
	}
}

if (is_admin()) {
	// activation action
	register_activation_hook(__FILE__, 'activate');
	register_deactivation_hook(__FILE__, 'deactivate');
	add_action('admin_init', 'admin_init');

	// add a Settings link in the installed plugins page
	add_filter('plugin_action_links', 'action_links', 10, 2);
	add_action('admin_menu', 'settings_menu'); //obview setting combobox
}
?>
