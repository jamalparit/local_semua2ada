<?php

$current_lang = '';
//Recive update language request
if (isset($_POST['action']) && $_POST['action']=='update' && isset($_POST['plugins_lang_switch_language'])) {

	if(SYNOUpdateLanguage($_POST['plugins_lang_switch_language'])) {
		$current_lang = $_POST['plugins_lang_switch_language'];
		?>
		<script language="JavaScript">
			function myrefresh()
			{
				window.location.replace("<?php echo get_option('siteurl').'/wp-admin'; ?>");
			}
			setTimeout('myrefresh()',500);
		</script>
		<?php
	}
	else {
		?>
		<script>alert("<?php echo 'fail';?>");</script>
		<?php
	}
	
}

$title=__('Select display language');
$languages = get_plugins_languages();
$current_lang = $current_lang?$current_lang:WPLANG;
$current_lang = $current_lang?$current_lang:'en_En';
?>
<form method="post" action="options-general.php?page=language-selector.php">
<div id="poststuff" class="metabox-holder">
<div class="has-sidebar" >
<div id="post-body-content" class="has-sidebar-content">

<?php
    settings_fields('language-selector-options');
?>

<div id="post-body-content" class="has-sidebar-content">
<div class="postbox" >
<h3 style="cursor:default;"><span><?php echo $title; ?></span></h3>
<div class="inside">

	<select name="plugins_lang_switch_language" id="plugins_lang_switch_language">
<?php
 	 foreach ($languages as $key=>$lang) {
?>
		<option value="<?php echo $key; ?>" <?php echo optionSelected($current_lang, $key); ?> ><?php echo $lang; ?></option>
<?php
  }
?>
	</select>
</div>
</div>
<div class="fli submit" style="padding-top: 0px;">
	<input type="submit" name="submit" value="<?php _e('Update'); ?>" title="<?php _e('Save Changes'); ?>" />
</div></div></div></div></div></form>

