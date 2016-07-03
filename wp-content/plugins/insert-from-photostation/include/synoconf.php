<?php
function show($content){$fp = fopen('/tmp/log', 'a');fwrite($fp, json_encode($content));fclose($fp);return true;}
$SYNOBLOG_PHOTO_SERVICE_REAL_DIR = @readlink("/var/services/photo");
$SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PATH = $SYNOBLOG_PHOTO_SERVICE_REAL_DIR.'/';
$SYNOBLOG_PHOTO_SERVICE_REAL_DIR_PREFIX = '';

$SYNO_PHOTO_THUMB_WIDTH = 120;
$SYNO_PHOTO_THUMB_HEIGHT = 120;

$SYNOPHOTO_ALLOW_ORIG_NAMES_EXT = array(
    "gif"
);

















?>
