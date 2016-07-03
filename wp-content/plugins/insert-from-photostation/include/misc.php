<?php
require_once("../include/synoconf.php");
//require_once($SYNOBLOG_LANGS_LANG);
//require_once($SYNOBLOG_INCLUDE_DB_PHOTO);
//require_once($SYNOBLOG_INCLUDE_CONFIG);


function SYNOBLOG_MISC_RoundFloat($value)
{
	$round = 3;
	$tmp_value = $value * 10 % 10;
	if($tmp_value < $round) {
		return floor($value);
	} else {
		return ceil($value);
	}
}

function SYNOBLOG_MISC_GetWidthHeight($width, $height, $expect_width, $expect_height)
{
	if ($width > $expect_width && $height > $expect_height) {
		if (($height / $expect_height) < ($width / $expect_width)){
			$result['width'] = $expect_width;
			$result['height'] = SYNOBLOG_MISC_RoundFloat($height * ($expect_width / $width));
		} else {
			$result['height'] = $expect_height;
			$result['width'] = SYNOBLOG_MISC_RoundFloat($width * ($expect_height / $height));
		}
	} elseif ($height > $expect_height){
		$result['height'] = $expect_height;
		$result['width'] = SYNOBLOG_MISC_RoundFloat($width * ($expect_height / $height));
	} elseif ($width > $expect_width){
		$result['width'] = $expect_width;
		$result['height'] = SYNOBLOG_MISC_RoundFloat($height*($expect_width / $width));
	} else {
		$result['width'] = $width;
		$result['height'] = $height;
	}
	return $result;
}

function SYNOBLOG_MISC_IsPhotoFileWithThumb($path)
{
	global $SYNOPHOTO_ALLOW_ORIG_NAMES_EXT;

	$path = strtolower($path);
	$path_parts = pathinfo($path);
	$extension = $path_parts['extension'];
	if(!in_array($extension, $SYNOPHOTO_ALLOW_ORIG_NAMES_EXT)) {
		return true;
	} else {
		return false;
	}
}

function SYNOBLOG_MISC_EscapForLike($val)
{
	$val = str_replace('\\', '\\\\', $val);
	$val = str_replace('_', '\\_', $val);
	$val = str_replace('%', '\\%', $val);

	return $val;
}

function SYNOBLOG_MISC_GetUrlPrefix()
{
	if (preg_match('/\/~[^\/]+/', $_SERVER['REQUEST_URI'], $matches)){
		return $matches[0];
	}
	return '';
}

?>
