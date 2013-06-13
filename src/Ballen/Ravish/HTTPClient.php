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
     * Object storage for the web service response.
     * @var string Response data.
     */
    private $response_body = null;

    /**
     * Object storage for the web service response headers.
     * @var array Response headers recieved.
     */
    private $response_headers = null;

    /**
     * Sends the request to server and returns the raw response.
     * @param string $uri The full URI to request and get the raw response from.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function sendRequest($uri)
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
        $this->response_body = @file_get_contents($uri, false, $cxContext);
        if ($this->response_body === false) {
            return false;
        } else {
            $this->response_headers = $http_response_header;
            return $this;
        }
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

    /**
     * Returns the RAW response from the server.
     * @return string The RAW response recieved from the server.
     */
    public function rawResponse()
    {
        return $this->response_body;
    }

    /**
     * Returns an object from a JSON formatted HTTP response.
     * @return object Response data object.
     */
    public function jsonObjectResponse()
    {
        return json_decode($this->response_body);
    }

    /**
     * Returns a data array from a JSON formatted HTTP response.
     * @return array Response data array
     */
    public function jsonArrayResponse()
    {
        return json_decode($this->response_body, true);
    }

    /**
     * Returns an object from an XML formatted HTTP response (requires the SimpleXML extension!)
     * @return object Response data object.
     */
    public function xmlObjectResponse()
    {
        return simplexml_load_string($this->response_body);
    }

    /**
     * Returns a data array from an XML formatted HTTP response (requires the SimpleXML extension!)
     * @return array Response data array.
     */
    public function xmlArrayResponse()
    {
        return json_decode(json_encode($this->xmlObjectResponse()), true);
    }

    /**
     * Returns response headers with auto-set lowercase key values.
     * @return object Reponse header elements.
     */
    public function responseHeaders()
    {
        $headers = array();
        foreach ($this->response_headers as $header) {
            $header_split = explode(':', $header);
            if ($header_split[0] == $header) { // The first element is the HTTP header type, such as HTTP/1.1 200 OK,
                $header_top = explode(' ', $header);
                $headers['protocol'] = $header_top[0];
                $headers['status_code'] = $header_top[1];
                $headers['status_text'] = $header_top[2];
            } else {
                $headers[strtolower($header_split[0])] = trim($header_split[1]); // Other header elements will be assigned to their type so they can be accessed using their 'key'.
            }
        }
        return json_decode(json_encode($headers));
    }

    /**
     * Return the raw response headers body as a plain text string (useful for debugging).
     * @return string Raw response headers as a plain string.
     */
    public function responseHeadersRaw()
    {
        return implode("\r\n", $this->response_headers);
    }

}
?>
