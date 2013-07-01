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
    'description' => 'My example API test Gist with cURL',
    'public' => true,
    'files' => array(
        'file1.txt' => array(
            'content' => 'This is a very quick test using the Ravish HTTPClient from http://www.github.com/bobsta63/ravish, this particular request is using the useCurl() method to force it to use cURL instead of the default file_get_content() method.'
        )
    )
);



$github_api->serverRedirects(false) // GitHub will attempt to redirect you after the request was successful, we need to therefore stop the redirect!
        ->addRequestHeader('Content-Type', 'application/json')
        ->setRequestBody(json_encode($request_body))
        ->useCurl() // To use cURL instead of the default file_get_contents() method we just add this method call!
        ->post($github_api_endpoint);

var_dump($github_api->jsonObjectResponse());
?>
