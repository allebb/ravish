<?php

/**
 * Ravish
 *
 * Ravish is a composer/PSR-0 compatible basic HTTP Client for PHP.
 *
 * @author bobbyallen.uk@gmail.com (Bobby Allen)
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/bobsta63/ravish
 * @link http://www.bobbyallen.me
 *
 */
require_once '../src/Ballen/Ravish/HTTPClient.php';

use Ballen\Ravish\HTTPClient;

$bindhub_service = new HTTPClient();

$bindhub_endpoint = 'https://www.bindhub.com/api/ip.xml';

if ($bindhub_service->get()->sendRequest($bindhub_endpoint)) {
    echo "Your public IP address is: " . $bindhub_service->xmlObjectResponse()->address->public;
    echo "<br /><br />The response headers was as follows: <strong>" . $bindhub_service->responseHeaders()->status_code . "</strong>";
} else {
    echo "<strong>Unable to get response from site!</strong><p>Are you sure you have the correct web service address, are connected to the internet or if you are using a proxy server in your network are you correctly authetnicated?</p>";
}

//var_dump($bindhub_service->xmlObjectResponse());
?>
