<?php
$filename = "/etc.defaults/VERSION";
$str = "";
if(file_exists($filename)){
        $file = fopen($filename, "r");
        if($file != NULL){
                while (!feof($file)) {
                        $str = fgets($file);
                        if (preg_match("/majorversion=\"(\d).*\"/", $str, $result)){   
                                $version = $result[1];     
                                $version = floatval($version);                        
                                break;                                                
                        }                                  
                }                                              
                fclose($file);
        }                     
}                                                    
                                                      
$username = ($version > 4 ?'postgres':'admin'); 

//personal photo station
if (!isset($GLOBALS['dbconn_photo']) || $GLOBALS['dbconn_photo'] == null ||
    $GLOBALS['dbconn_photo']->getAttribute(constant("PDO::ATTR_DRIVER_NAME")) != 'pgsql') {
    try{
        $GLOBALS['dbconn_photo'] = new PDO("pgsql:dbname=photo;host=localhost", $username, "" );
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

//sqlite should defind escape char for each LIKE
function BLOG_DB_GetEscape() //used
{
	$db_type = $GLOBALS['dbconn_photo']->getAttribute(constant("PDO::ATTR_DRIVER_NAME"));
	if ('sqlite' == $db_type) {
			return "ESCAPE '\'";
	}
	return '';
}

function BLOG_DB_PHOTO_Query($dbconn, $query, $param=array())
{
	$stmt = $dbconn->prepare($query);
	$rs = $stmt->execute($param);
	return $stmt;
}

function BLOG_DB_PHOTO_FetchRow($stmt)
{
	$row = $stmt->fetch();
	return $row;
}

?>
