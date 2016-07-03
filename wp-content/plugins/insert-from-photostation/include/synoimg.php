<?php
require_once("../include/synoconf.php");
require_once("../include/database.php");
require_once("../include/misc.php");

if (isset($_POST['node'])) {
	echo SYNOBLOG_IMG_GetPublicTreeData($_POST['node']);
	exit;
}

$action = (array_key_exists('action', $_POST)) ? $_POST['action'] : null ;
if ('get_album_photo' == $action) {
	echo SYNOBLOG_IMG_GetAlbumPhoto(hexToBinForPath($_POST['current_dir']));
	exit;
}

function CheckRootPublic()
{
	$query = "SELECT config_key FROM photo_config WHERE config_key=? AND config_value=?";
	$sqlParam = array('allow_root_folder_public', 'on');

	$stmt = BLOG_DB_PHOTO_Query($GLOBALS['dbconn_photo'], $query, $sqlParam);

	return ($stmt->rowCount() === 1);
}

function SYNOBLOG_IMG_GetAlbumPhoto($album)
{
	$album = stripslashes($album);
	global $SYNO_PHOTO_THUMB_WIDTH, $SYNO_PHOTO_THUMB_HEIGHT, $SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PATH;
	global $SYNOBLOG_PHOTO_SERVICE_REAL_DIR, $SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PREFIX;
	$files = array();
	$i = 0;

	if ($album == null || $album == "") {
		if (!CheckRootPublic()) {
			return json_encode($files);
		}

		$path_prefix = $SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PATH;
	} else {
		$path_prefix = $SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PATH.SYNOBLOG_MISC_EscapForLike($album)."/";
	}

	$query = "SELECT * FROM photo_image WHERE path LIKE ? AND path NOT LIKE ? ORDER BY path";
	$sqlParam = array($path_prefix."%", $path_prefix."%/%");

	$db_result = BLOG_DB_PHOTO_Query($GLOBALS['dbconn_photo'], $query, $sqlParam);

	while(($row = BLOG_DB_PHOTO_FetchRow($db_result))) {
		$path = $SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PREFIX.$row[1];
		$file_name = basename($path);
		$dir_name = substr(str_replace("/".$file_name, "", $path), strlen($SYNOBLOG_PHOTO_SERVICE_REAL_DIR."/"));

		$src = $url = SYNOBLOG_MISC_GetUrlPrefix().'/photo/convert.php?dir='.bin2hex($dir_name).'&name='.bin2hex($file_name).'&type=';
		$have_thumb = SYNOBLOG_MISC_IsPhotoFileWithThumb($file_name);

		if($have_thumb) {
			$url .= '0'; // small thumb
			$src .= '1'; // big thumb
		} else {
			$url .= '2'; // orig
			$src .= '2';
		}

		if ($row['version'] % 2 == 0) {
			$tmp = SYNOBLOG_MISC_GetWidthHeight($row[7],$row[8],$SYNO_PHOTO_THUMB_WIDTH,$SYNO_PHOTO_THUMB_HEIGHT);
		} else {
			$tmp = SYNOBLOG_MISC_GetWidthHeight($row[8],$row[7],$SYNO_PHOTO_THUMB_WIDTH,$SYNO_PHOTO_THUMB_HEIGHT);
		}

		$files['images'][$i]['id'] = $i;
		$files['images'][$i]['dir'] = htmlspecialchars($dir_name);
		$files['images'][$i]['name'] = htmlspecialchars($file_name);
		$files['images'][$i]['url'] = $url;
		$files['images'][$i]['src'] = $src;
		$files['images'][$i]['dispaly_info'] = htmlspecialchars($dir_name."/".$file_name);
		$files['images'][$i]['thumb_width'] = $tmp['width'];
		$files['images'][$i]['thumb_height'] = $tmp['height'];

		$i++;
	}

	return json_encode($files);
}

function IsSharePublic($sharename)
{
	$query = "SELECT sharename FROM photo_share WHERE public='t' AND sharename = ?";
	$sqlParam = array($sharename);

	$stmt = BLOG_DB_PHOTO_Query($GLOBALS['dbconn_photo'], $query, $sqlParam);

	return ($stmt->rowCount() === 1);
}

function CheckSharePrivilege($path)
{
	$path_items = explode('/', $path);
	$item_count = count($path_items);

	$sharename = '';
	for ($idx = 0; $idx < $item_count && $idx < 2; $idx++) {
		$sharename .= $path_items[$idx];

		if (!IsSharePublic($sharename)) {
			return false;
		}

		$sharename .= '/';
	}

	return true;
}

function SYNOBLOG_IMG_GetPublicTreeData($path)
{
	if (!strstr($path, 'source')) {
		return json_encode(array());
	}

	if ($path === 'source') {
		$result = array();
		$result[0]['text'] = 'Albums';
		$result[0]['id'] = 'source/';
		$result[0]['cls'] = 'root';

		return json_encode($result);
	}

	$path = hexToBinForPath(stripslashes($path));
	if (!$path) {
		return json_encode(array());
	}

	if ($path === '/') {
		$like_str = "%";
		$not_like_str = "%/%";
		$check_public_condition = "public = 't' AND";
	} else {
		if (!CheckSharePrivilege($path)) {
			return json_encode(array());
		}

		$escaped_path = SYNOBLOG_MISC_EscapForLike($path);
		$like_str = "$escaped_path/%";
		$not_like_str = "$escaped_path/%/%";

		$path_items = explode('/', $path);
		if (count($path_items) == 1) {
			$check_public_condition = "public = 't' AND";
		} else {
			$check_public_condition = "";
		}
	}

	$query = "SELECT sharename FROM photo_share WHERE $check_public_condition sharename LIKE ? AND sharename NOT LIKE ?  ORDER BY sharename";

	$sqlParam = array($like_str, $not_like_str);
	$db_result = BLOG_DB_PHOTO_Query($GLOBALS['dbconn_photo'], $query, $sqlParam);

	$result = array();
	while (($row = BLOG_DB_PHOTO_FetchRow($db_result))) {
		$sharename = $row[0];
		$hex_sharename = bin2hex($sharename);

		$result[] = array(
			'text' => basename($sharename),
			'id'   => "source/$hex_sharename",
			'cls'  => 'root'
		);
	}

	return json_encode($result);
}

function hexToBin($hexdata) {
	for ($i=0;$i<strlen($hexdata);$i+=2) {
		$bindata.=chr(hexdec(substr($hexdata,$i,2)));
	}
	return $bindata;
}

/**
 * input hex path, return sharename
 */
function hexToBinForPath($hex_path) {
	$prefix = 'source/';
	$prefix_len = strlen($prefix);

	if (null == $hex_path) {
		return false;
	}

	if ($hex_path === 'source/') {
		return '/';
	}

	if (0 === strncmp($hex_path, $prefix, $prefix_len)) {
		$hex_path = substr($hex_path, $prefix_len);
	}

	$path = hexToBin($hex_path);

	// check if every part has value
	$path_parts = explode('/', $path);
	foreach ($path_parts as $part) {
		if (!$part) {
			return false;
		}
	}

	return $path;
}
