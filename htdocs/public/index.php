<?php

use function PHPSTORM_META\type;

$PATH = __DIR__ . '/../private';
$RESOURCE_PATH = $PATH . '/resources';

require_once $PATH  . "/src/links.php";
require_once $PATH  . "/src/config.php";

$query = false;
if (isset($_GET['query']))  {
    $query = true;
}

$verify = verifyLink();
if(!isset($verify['resource'])) {
    if($query) {
        http_response_code(403);
        echo 'invalid';
    } else {
        $contents = file_get_contents($RESOURCE_PATH . '/invalid.html');
        // echo type($verify);
        // echo implode(", ", $verify);
        // echo implode(", ", array_keys($verify));
        $contents = str_replace('{placeholder}', $verify["status"], $contents);
        echo $contents;
    }
} else {
    if($query) {
        http_response_code(200);
        echo 'ok';
    } else {
        include $RESOURCE_PATH . '/' . $verify['resource'];
    }
}

?>

