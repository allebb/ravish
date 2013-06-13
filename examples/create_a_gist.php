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
            'content' => 'This is a very quick test using the Ravish HTTPClient from'
        )
    )
);
$github_api->post()
        ->addRequestHeader('Content-type', 'application/json')
        ->setRequestBody(json_encode($request_body));
$github_api->sendRequest($github_api_endpoint);
var_dump($github_api);

echo $github_api->responseHeadersRaw();
?>
