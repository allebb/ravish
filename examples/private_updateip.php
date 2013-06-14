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

// BindHub.com settings.
$bindhub_username = 'ballen';
$bindhub_apikey = '139d242e93e5f5524de149054d535b34205efc57';
$bindhub_record = 'blahtest.autono.de';

// We'll set the required endpoint URI's here...
$bindhub_publicip_endpoint = 'https://www.bindhub.com/api/ip.xml';
$bindhub_updateip_endpoint = 'https://www.bindhub.com/api/record/update.json'; // As a second example, we'll return the response and handle is using JSON.

if ($bindhub_service->get($bindhub_publicip_endpoint)) {
    echo "Your current public IP address is: " . $bindhub_service->xmlObjectResponse()->address->public . " we'll now update your IP address in DNS for your domain name <strong>" . $bindhub_record . "</strong>.";
    // We now make a new request and send the new IP address...
    $ipa = $bindhub_service->xmlObjectResponse()->address->public;
    $bindhub_service->resetRequest(); // Reset the request parameters etc. so we can reuse the same object for the next request...
    $bindhub_service->addParameter('user', $bindhub_username)
            ->addParameter('key', $bindhub_apikey)
            ->addParameter('record', $bindhub_record)
            ->addParameter('target', trim($ipa));
    $bindhub_service->post($bindhub_updateip_endpoint);
    if ($bindhub_service->responseHeaders()->status_code == 200) {
        echo "New IP address has been updated successfully!";
    } else {
        echo "Oppps an error occured, the error code was <strong>" . $bindhub_service->responseHeaders()->status_code . " (" . $bindhub_service->responseHeaders()->status_text . ")</strong> and web service reported the issue was: <br /><em>" . $bindhub_service->responseHeadersRaw() . " <br />Raw response was: " . $bindhub_service->rawResponse() . "</em>.";
    }
} else {
    echo "<strong>Unable to get response from site!</strong><p>Are you sure you have the correct web service address, are connected to the internet or if you are using a proxy server in your network are you correctly authetnicated?</p>";
}
?>
