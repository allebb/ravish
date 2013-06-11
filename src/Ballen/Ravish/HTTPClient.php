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

namespace Ballen\Ravish;

class HTTPClient
{

    /**
     * Object storage for the web service response.
     * @var string JSON response data.
     */
    private $response = null;

    /**
     * Optional proxy server hostname or IP address.
     * @var string
     */
    private $proxy_host = null;

    /**
     * Optional proxy server TCP port (defaulted to 8080)
     * @var integer
     */
    private $proxy_port = 8080;

    /**
     * Optional proxy server BASIC authentication string (Base64)
     * @var string
     */
    private $proxy_auth = null;

    /**
     * The HTTP method type used for the request.
     * @var string
     */
    private $request_httpmethod = 'GET';

    /**
     * Array of POST variables to send with the request.
     * @var array
     */
    private $post_params = array();

    /**
     * Sends the request to server and returns the raw response.
     * @param string $uri The full URI to request and get the raw response from.
     * @return \Ballen\Ravish\HTTPClient
     */
    protected function sendRequest($uri)
    {
        $aContext = array(
            'http' => array(
                'method' => $this->request_httpmethod,
                'request_fulluri' => true,
            ),
        );
        if ($this->proxy_host) {
            $aContext['http'] = array_merge($aContext['http'], array('proxy' => $this->proxy_host . ':' . $this->proxy_port));
        }
        if (count($this->post_params) > 0) {
            if (!isset($aContext['http']['header'])) {
                $aContext['http']['header'] = array();
            }
            $request_content = http_build_query($this->post_params);
            array_push($aContext['http']['header'], 'Content-Type: application/x-www-form-urlencoded');
            array_push($aContext['http']['header'], 'Content-Length: ' . strlen($request_content));
            $aContext['http']['content'] = $request_content;
        }
        if ($this->proxy_auth) {
            if (!isset($aContext['http']['header'])) {
                $aContext['http']['header'] = array();
            }
            array_push($aContext['http']['header'], "Proxy-Authorization: Basic $this->proxy_auth");
        }
        $cxContext = stream_context_create($aContext);
        $this->response = file_get_contents($uri, false, $cxContext);
        $this->resetRequest();
        return $this;
    }

    /**
     * Sets Proxy server host and port infomation (if required.)
     * @param string $host The hostname or IP address of the proxy server.
     * @param string $port The TCP port to use to connect to the proxy (default is set to 8080)
     * @return \Ballen\Ravish\HTTPClient
     */
    public function setProxyHost($host, $port = null)
    {
        $this->proxy_host = $host;
        if ($port) {
            $this->proxy_port = $port;
        }
        return $this;
    }

    /**
     * Sets Proxy authentication.
     * @param string $username Username to use to authenticate with the proxy.
     * @param string $password Password to use to authenticate with the proxy.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function setProxyAuth($username, $password)
    {
        $this->proxy_auth = base64_encode("$username:$password");
        return $this;
    }

    /**
     * Resets the request parameters/settings ready for the next request.
     * @return \Ballen\Ravish\HTTPClient
     */
    protected function resetRequest()
    {
        $this->post_params = array();
        $this->request_wsmethod = null;
        $this->request_httpmethod = 'GET';
        return $this;
    }

}

?>
