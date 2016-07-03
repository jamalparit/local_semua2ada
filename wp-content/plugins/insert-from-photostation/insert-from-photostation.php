<?php
/*
Plugin Name: Insert From PhotoStation
Version: 1.1
Plugin URI: http://www.synology.com/
Description: The plug-in enables you to insert photos from Photo Station.
Author: Synology Inc.
Author URI: http://www.synology.com
*/

$GLOBALS['insert-from-photostation'] = new insert_from_photostation();
class insert_from_photostation {

	var $basename = '';
	var $folder = '';
	var $version = '1.1';

	function insert_from_photostation() {
		//Set the directory
		$this->basename = plugin_basename(__FILE__);
		$this->folder = dirname($this->basename);

		if ("yes" != @exec("/usr/syno/bin/synogetkeyvalue /etc/synoinfo.conf runphoto") && !@file_exists ("/var/packages/PhotoStation/enabled")) {
			$current = get_settings('active_plugins');
			if (in_array( $this->basename, $current)) {
				array_splice($current, array_search($this->basename, $current), 1 );
				update_option('active_plugins', $current);
				header("Location: wp-admin/plugins.php?deactivate=true");
			}
		}
		//Register general hooks.
		add_action('admin_init', array(&$this, 'admin_init'));
		register_activation_hook(__FILE__, array(&$this, 'activate'));
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
	}

	function print_language_for_js(){
		$err = false;
		$content = '';
		$content .=	'chooser_align = \'' .__("Align") . "';\n";
		$content .=	'chooser_thumbnail_size = \'' .__("Thumbnail size") . "';\n";
		$content .=	'chooser_align_left= \'' .__("Align Left") . "';\n";
		$content .=	'chooser_align_center= \'' .__("Align Center") . "';\n";
		$content .=	'chooser_align_right= \'' .__("Align Right") . "';\n";
		$content .=	'chooser_insert_an_image = \'' .__("Insert an Image") . "';\n";
		$content .=	'chooser_image_properties = \'' .__("Image properties") . "';\n";
		$content .=	'chooser_cancel = \'' .__("Cancel") . "';\n";
		$content .=	'chooser_requires = \'' .__("Align") . "';\n";
		$content .=	'chooser_error = \'' .__("Error") . "';\n";
		$content .=	'chooser_no_public_folder = \'' .__("Can\'t find a public folder in Photo Station") . "';\n";
		if (!($fw = fopen(ABSPATH.'/wp-content/plugins/'.$this->folder.'/client_string.js','w'))) {
			return $err;
		}

		if (!(fwrite($fw, $content))) {
			return $err;
		}

		fclose($fw);
		return true;

	}

	function admin_init() {
		//Load any translation files needed:
		$this->print_language_for_js();
		//Register our JS & CSS
		wp_register_style ('ext-all', plugins_url( $this->folder . '/scripts/ext-3/resources/css/ext-all.css' ), array());
		wp_register_style ('ext-theme-gray', plugins_url( $this->folder . '/scripts/ext-3/resources/css/xtheme-gray-syno.css' ), array());
		wp_register_style ('chooser.css', plugins_url( $this->folder . '/scripts/ImageChooser.css' ), array());
		wp_register_script('ext-base', plugins_url( $this->folder . '/scripts/ext-3/adapter/ext/ext-base.js' ), array());
		wp_register_script('ext-all', plugins_url( $this->folder . '/scripts/ext-3/ext-all.js' ), array());
		wp_register_script('insert-from-photostation', plugins_url( $this->folder . '/scripts/insert-from-photostation.js' ), array(), $this->version);
		wp_register_script('chooser', plugins_url( $this->folder . '/scripts/ImageChooser.js' ), array(), $this->version);
		wp_register_script('strings',plugins_url( $this->folder . '/client_string.js'), array(), $this->version);

		//Enqueue JS & CSS
		add_action('wp_dashboard_setup', array(&$this, 'add_head_files'));
		add_filter('load-post-new.php', array(&$this, 'add_head_files'));
		add_filter('load-post.php', array(&$this, 'add_head_files'));

		//Add actions/filters
			add_filter('media_buttons_context', array(&$this, 'media_buttons_context'));

	}
	function activate() {
		global $wp_version;
		if ( ! version_compare( $wp_version, '3.0', '>=') ) {
			if ( function_exists('deactivate_plugins') )
				deactivate_plugins(__FILE__);
			die(sprintf( __('<strong>Insert From Photo Station: </strong>This plug-in requires version %s or later.'), '3.1'));
		}

		if (3 < @exec("/usr/syno/bin/synogetkeyvalue /etc.defaults/VERSION majorversion")) {
			if ( false == @file_exists ("/var/packages/PhotoStation/enabled")) {
				if ( function_exists('deactivate_plugins') )
					deactivate_plugins(__FILE__);
				die( __('<strong>Insert From Photo Station: </strong>This plug-in must be used with Photo Station.'));
			}
		} else if ("yes" != @exec("/usr/syno/bin/synogetkeyvalue /etc/synoinfo.conf runphoto")) {
			if ( function_exists('deactivate_plugins') )
				deactivate_plugins(__FILE__);
			die( __('<strong>Insert From Photo Station: </strong>This plug-in must be used with Photo Station.'));
		}
	}

	function deactivate(){
		delete_option('frmsvr_last_folder');
	}

	function add_head_files() {
		//Enqueue support files.
		wp_enqueue_style('ext-all');
		wp_enqueue_style('ext-theme-gray');
		wp_enqueue_style('chooser.css');
		wp_enqueue_script('ext-base');
		wp_enqueue_script('ext-all');
		wp_enqueue_script('insert-from-photostation');
		wp_enqueue_script('chooser');
		wp_enqueue_script('strings');
	}

	function media_buttons_context($context) {
		do_action('insert_from_photostation_admin_header');
		global $post_ID, $temp_ID;
		$dir = dirname(__FILE__);

			$image_btn = plugins_url( $this->folder .'/scripts/synops4.png');
		$image_title = __('Insert image from Photo Station');

		$out = ' <a href="" title="'.$image_title.'"onclick="return false;">';
		$out .= '<img src="'.$image_btn.'" onclick="addExtImage.onClickSelectSynoImage();"/></a>';
		return $context.$out;
	}

}//end class

?>
