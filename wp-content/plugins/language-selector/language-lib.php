<?php

if (! defined("WPLANG")) {
	die;  // Silence is golden, direct call is prohibited
}

$basename = plugin_basename(__FILE__);
$folder = dirname($basename);

if ( !defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

$tmp = dirname(__FILE__);
if (strpos($tmp, '/', 0)!==false) {
	define('LANGUAGE_SELECTOR_DIR_SLASH', '/');
} else {
	define('LANGUAGE_SELECTOR_DIR_SLASH', '\\');
}
define('DEFINELANGUAGESPATH','define(\'WP_LANG_DIR\', \'' . $tmp . '/language-selector/languages\');');

function optionSelected($value, $etalon) {
	$selected = '';
	if ($value==$etalon) {
		$selected = 'selected="selected"';
	}
	return $selected;
}

function scanPluginsDirectory($path, $all_languages, &$languages) {
	if (substr($path, strlen($path)-1, 1) != LANGUAGE_SELECTOR_DIR_SLASH) {
		$path .= LANGUAGE_SELECTOR_DIR_SLASH;
	}
	$dir = @opendir($path);
	if ($dir) {
		while($fileName = readdir($dir)) {
			if ($fileName == '.' || $fileName == '..') {
				continue;
			}
			$ext = pathinfo($fileName, PATHINFO_EXTENSION);
			if ($ext=='mo') {
				$baseName = pathinfo($fileName, PATHINFO_BASENAME);
				$baseName = substr($baseName, 0, strlen($baseName)-strlen($ext)-1);
				if (isset($all_languages[$baseName])) {
					$languages[$baseName] = $all_languages[$baseName];
				}
			} else {
				$fileName = $path.$fileName;
				if (is_dir($fileName)) {
					scanPluginsDirectory($fileName, $all_languages, $languages);
				}
			}
		}
	closedir($dir);
	}
}


function get_plugins_languages() {

$all_languages =
	array(
	'af'=>'Afrikaans',
	'al'=>'Albanian',
	'ar'=>'Arabic – عربي',
	'bn_BD'=>'Bangla - Bengali',
	'be_BY'=>'Belarusian - Беларуская - Biełaruskaja',
	'bs_BA'=>'Bosnian - Bosanski',
	'eu'=>'Basque - Euskara',
	'bg_BG'=>'Bulgarian - Български',
	'ca'=>'Catalan - Català',
	'zh_CN'=>'Chinese - 中文',
	'hr'=>'Croatian - Hrvatski',
	'cs_CZ'=>'Czech - Čeština',
	'da_DK'=>'Danish - Dansk',
	'nl_NL'=>'Dutch - Nederlands (nl_NL)',
	'eo'=>'Esperanto',
	'et'=>'Estonian - Eesti',
	'fo'=>'Faroese',
	'fi'=>'Finnish - Suomi',
	'fr_FR'=>'French - Français',
	'gl_ES'=>'Galician - Galego',
	'ge_GE'=>'Georgian',
	'zh_HK'=>'Hong Kong (香港)',
	'de_DE'=>'German - Deutsch',
	'el'=>'Greek',
	'en_En'=>'English',
	'fr_FR'=>'French',
	'he_IL'=>'Hebrew',
	'hi_IN'=>'Hindi',
	'hu_HU'=>'Hungarian - Magyar',
	'is_IS'=>'Icelandic',
	'id_ID'=>'Indonesian - Bahasa Indonesia',
	'it_IT'=>'Italian',
	'ja'=>'Japanese - 日本語',
	'km_KH'=>'Khmer(Cambodian Language)',
	'ko_KR'=>'Korean - 한국어',
	'lv'=>'Latvian (Latviešu)',
	'lt_LT'=>'Lithuanian - Lietuviškai',
	'mk_MK'=>'Macedonian - Македонски',
	'mg_MG'=>'Malagasy',
	'ms_MY'=>'Malay – Bahasa Melayu',
	'ni_ID'=>'Nias - Li Niha',
	'nb_NO'=>'Norwegian',
	'nn_NO'=>'Nynorsk',
	'fa_IR'=>'Persian',
	'pl_PL'=>'Polish - Polski',
	'pt_PT'=>'European Portuguese',
	'pt_BR'=>'Brazilian Portuguese',
	'ro_RO'=>'Romanian - Română',
	'ru_RU'=>'Russian - Русский',
	'sr_RS'=>'Serbian — Српски',
	'si_LK'=>'Sinhala',
	'sk_SK'=>'Slovak – Slovenčina',
	'sl_SI'=>'Slovenian - Slovenščina',
	'es_ES'=>'Spanish - Español',
	'su_ID'=>'Sundanese - Basa Sunda',
	'sv_SE'=>'Swedish - Svenska',
	'tg'=>'Tajik',
	'th'=>'Thai',
	'tr_TR'=>'Turkish - Türkçe',
	'zh_TW'=>'Taiwan - 台灣',
	'uk'=>'Ukrainian - Українська',
	'uz_UZ'=>'Uzbek - O‘zbekcha',
	'vi'=>'Vietnamse - Tiếng Việt',
	'cy'=>'Welsh - Cymraeg'
	  );

	$plugins_languages = array('en_En'=>'English');
	scanPluginsDirectory(WP_PLUGIN_DIR, $all_languages, $plugins_languages);
	asort($plugins_languages);
	return $plugins_languages;
}

function SYNOUpdateLanguage ($lang) {
	$err = false;

	if (WPLANG == $lang) {
		return $err;
	}

	$content = '';
	if (!($content .= file_get_contents(ABSPATH.'wp-config.php'))) {
		return $err;
	}
	
	$new_wplang = '\'WPLANG\', \'' . $lang . '\'';
	$old_wplang = '/\'WPLANG\', \'' . WPLANG . '\'/';
	if (!$content = preg_replace($old_wplang, $new_wplang, $content)) {
		return $err;
	}

	$fp = fopen(ABSPATH.'wp-config.php', 'w');
	fwrite($fp, $content);
	fclose($fp);

	return true;
}

function SYNOActiveLanguagesPath ($active) {
	$err = false;
	$find = false;

	if (!($fd = fopen(ABSPATH.'wp-config.php', 'rb'))) {
		return $err;
	}

	while ($oldline = rtrim(fgets($fd), 128)) {
		if (strstr($oldline,'\'WP_LANG_DIR\'')) {
			$newline = DEFINELANGUAGESPATH. "\n";
			$find = true;
			break;
		}
		if (strstr($oldline,'\'WPLANG\'')) {
			$newline_bak = $oldline . DEFINELANGUAGESPATH. "\n";
			$oldline_bak = $oldline;
		}
	}
	fclose($fd);
	if (!$active && !$find) {
		return $err;
	}
	if ($active) {
		$newline = $find?$newline:$newline_bak;
		$oldline = $find?$oldline:$oldline_bak;
	} else {
		$newline = '';
	}

	$content = '';
	if (!($content .= file_get_contents(ABSPATH.'wp-config.php'))) {
		return $err;
	}
	
	if (!($content = str_replace($oldline, $newline, $content))) {
		return $err;
	}

	if (!($fw = fopen(ABSPATH.'wp-config.php','wb'))) {
		return $err;
	}

	if (!(fwrite($fw, $content))) {
		return $err;
	}

	fclose($fw);
	return true;
}

?>
