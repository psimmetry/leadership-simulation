<?php

use function PHPSTORM_META\type;

$PATH = '/home/psicalc1/linkgen';
$RESOURCE_PATH = $PATH . '/resources';

require_once $PATH  . "/src/links.php";
require_once $PATH  . "/src/config.php";

$verify = verifyLink();
if(!isset($verify['resource'])) {
    $contents = file_get_contents($RESOURCE_PATH . '/invalid.html');
    // echo type($verify);
    // echo implode(", ", $verify);
    // echo implode(", ", array_keys($verify));
    $contents = str_replace('{placeholder}', $verify["status"], $contents);
    echo $contents;
} else {
    // $contents = file_get_contents($RESOURCE_PATH . '/' . $verify['resource']);
    // echo $contents;
    include $RESOURCE_PATH . '/' . $verify['resource'];
}

?>

