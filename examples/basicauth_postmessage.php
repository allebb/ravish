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

use Ballen\Ravish\HTTPClient as BassRocketClient;

$bassrocket_api = new BassRocketClient(); // Using Namespace aliases!!

// Set your post details...
$post_text = 'This is an example post to BassRocket using the HTTP Ravish client found https://github.com/bobsta63/ravish';
$post_soundcloudurl = '';

$endpoint = 'http://beta.bassrocket.com/api/v1/post';
$bassrocket_api
        ->showErrors(true)
        ->setBasicAuthCredentials('apitest', 'password123') // This particular webservice uses HTTP BASIC authentication, you can easily set this infomation!
        ->addParameter('text', $post_text) // We now set the POST variables!
        ->addParameter('scu', $post_soundcloudurl)
        ->post($endpoint); // Now finally we POST to the webservice endpoint to get our response.

// Lets now retrieve the response from the web service/API...
if ($bassrocket_api->responseHeaders()->status_code == 201) {
    echo "You're status have been posted to the API... congratulations! :)";
} else {
    echo "Hmmm something went wrong, the server responded with the following error code " . $bassrocket_api->responseHeaders()->status_code;
    if (isset($bassrocket_api->jsonObjectResponse()->message)) {
        echo "The API responded with the following message: " . $bassrocket_api->jsonObjectResponse()->message;
    }
}

// You can display the raw response like so:-
//echo $bassrocket_api->responseHeadersRaw();
//echo $bassrocket_api->rawResponse();
?>
