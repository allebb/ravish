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

$github_api = new HTTPClient();

$github_api_endpoint = 'https://api.github.com/gists';

$request_body = array(
    'description' => 'My example API test Gist',
    'public' => true,
    'files' => array(
        'file1.txt' => array(
            'content' => 'This is a very quick test using the Ravish HTTPClient from https://github.com/bobsta63/ravish.'
        )
    )
);
$github_api->post()
        ->addRequestHeader('Content-type', 'application/json')
        ->setRequestBody(json_encode($request_body));

if ($github_api->sendRequest($github_api_endpoint)) {
    echo "Gist Created, details as follows:<br />" . $github_api->responseHeadersRaw() . "<br/ >" . $github_api->rawResponse();
} else {
    echo "<strong>Unable to get response from site!</strong><p>Are you sure you have the correct web service address, are connected to the internet or if you are using a proxy server in your network are you correctly authetnicated?</p>";
}

//var_dump($bindhub_service->xmlObjectResponse());
?>
