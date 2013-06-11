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

$bindhub_endpoint = 'https://www.bindhub.com/api/ip.json';
$bindhub_service->sendRequest($bindhub_endpoint);

$response = $bindhub_service->rawResponse();

echo $response;
?>
