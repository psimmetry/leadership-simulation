<?php

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/encrypt.php";



/**
 * Create a link query string with expiration and resource parameters
 * 
 * @param string $resource The requested resource path
 * @param int|null $expiresSeconds Number of seconds until expiration (null for default)
 * @param string|null $secret Secret key for signing
 * 
 * @return string HTML encoded query string
 */
function generateLinkQueryString($resource, $startTime, $timeValid)
{
    // Default to current time if no secret provided (no signature)
    return sprintf('v=%d&e=%d&r=%s', $startTime, $timeValid, urlencode($resource));
}


function generateLink($resource, $startTime, $timeValid)
{
    $plaintext = generateLinkQueryString(
        $resource,
        $startTime, $timeValid
    );
    $encr = FieldEncryptor::encrypt($plaintext, Config::$LINK_ENCRYPT_KEY);
    return "?x=" . urlencode($encr);
}

function verifyLink()
{
    $DATE_FORMAT = 'H:i:s (d M Y)';
    if (isset($_GET['x'])) {
        $encr = $_GET['x'];
        $plaintext = FieldEncryptor::decrypt($encr, Config::$LINK_ENCRYPT_KEY);
        if (!isset($plaintext)) return [
                    "resource" => NULL,
                    "status" => "invalid link"
                ];;
        // $plaintext = substr($plaintext, 1);
        // return $plaintext;
        $output = [];
        parse_str($plaintext, $output);
        // return implode(", ", array_keys($output));
        if (isset($output['r']) && isset($output['v']) && isset($output['e'])) {
            $resource = $output['r'];
            $startTime = $output['v'];
            $exp = $startTime + $output['e'];
            $now = time();
            if ($now < $exp && $now > $startTime) {
                return [
                    "resource" => $resource,
                    "status" => "valid"
                ];
            } else {
                $timeZone = new DateTimeZone('Africa/Johannesburg');
                $start = new DateTime( );
                $start->setTimezone($timeZone);
                $start->setTimestamp($startTime);
                $end = new DateTime();
                $end->setTimezone($timeZone);
                $end->setTimestamp($exp);
                return [
                    "resource" => NULL,
                    "status" => "only valid from " . $start->format($DATE_FORMAT ) . " until " . $end->format($DATE_FORMAT )
                ];
            }
        } else {
            return [
                "resource" => NULL,
                "status" => "invalid link"
            ];
        }
    } else {
        return [
            "resource" => NULL,
            "status" => "no query provided"
        ];
    }
}
