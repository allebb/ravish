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
     * Stores custom headers for the request.
     * @var type
     */
    private $request_headers = array();

    /**
     * Stores an optional raw request body.
     * @var string Request body content.
     */
    private $request_body = null;

    /**
     * Allow web requests to redirect automatically. (default is 'false')
     * @var boolean
     */
    private $request_followredirects = false;

    /**
     * Display HTTP error messages in PHP if the file_get_contents() request fails. (defualt is 'true')
     * @var boolean
     */
    private $request_showerrors = true;

    /**
     * The request timeout in seconds.
     * @var int Request timeout in seconds.
     */
    private $request_timeout = 10;

    /**
     * Useragent string to send with each request.
     * @var string The useragent string sent with requests.
     */
    private $user_agent = 'Ravish/1.0';

    /**
     * Array of optional POST/PUT/DELETE variables to send with the request.
     * @var array
     */
    private $request_params = array();

    /**
     * Stores an optional HTTP BASIC Authentication encoded string.
     * @var type
     */
    private $request_basicauth = null;

    /**
     * Object storage for the web service response.
     * @var string Response data.
     */
    private $response_body = null;

    /**
     * Object storage for the web service response headers.
     * @var array Response headers recieved.
     */
    private $response_headers = array();

    /**
     * Use cURL to make the requests instead of file_get_contents() of which may work better in enviroments where the http_response_header function has been disabled or blocked by a framework etc.
     * @var boolean
     */
    private $use_curl = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        // Nothing special here but good practice to add it anyway!
    }

    /**
     * Sends the request to server and returns the raw response using PHP's file_get_contents() function.
     * @param string $uri The URI to send the request to.
     */
    private function requestViaFileGetContents($uri)
    {
        $aContext = array(
            'http' => array(
                'method' => $this->request_httpmethod,
                'user_agent' => $this->user_agent,
                'request_fulluri' => true,
                'follow_location' => $this->request_followredirects,
                'ignore_errors' => $this->request_showerrors,
                'timeout' => $this->request_timeout,
            ),
        );
        $aContext['http']['header'] = array();
        $aContext['http']['header'][] = 'User-Agent: ' . $this->user_agent;
        // If a proxy server has been specified we'll set the proxy header autoamtically.
        if ($this->proxy_host) {
            $aContext['http'] = array_merge($aContext['http'], array('proxy' => $this->proxy_host . ':' . $this->proxy_port));
        }
        // If proxy authentication has been provided, we'll set the header for this now!
        if ($this->proxy_auth) {
            array_push($aContext['http']['header'], "Proxy-Authorization: Basic $this->proxy_auth");
        }
        // If HTTP basic authentication encoded string exists lets add it's header..
        if ($this->request_basicauth != null) {
            array_push($aContext['http']['header'], "Authorization: Basic " . $this->request_basicauth);
        }
        // If a raw request body has been set, we'll send the content with the request.
        if ($this->request_body != null) {
            array_push($aContext['http']['header'], 'Content-Length: ' . strlen($this->request_body));
            $aContext['http']['content'] = $this->request_body;
        }
        // If POST/PUT/DELETE parameters have been sent, we'll send them with the form type x-www-form-urlencoded autoamtically!
        if (count($this->request_params) > 0) {
            $request_content = http_build_query($this->request_params);
            array_push($aContext['http']['header'], 'Content-Type: application/x-www-form-urlencoded');
            array_push($aContext['http']['header'], 'Content-Length: ' . strlen($request_content));
            $aContext['http']['content'] = $request_content;
        }
        // We go through and add all the other custom HTTP headers and then the useragent.
        if (count($this->request_headers) > 0) {
            foreach ($this->request_headers as $custom_header => $custom_value) {
                array_push($aContext['http']['header'], $custom_header . ": " . $custom_value);
            }
        }
        $cxContext = stream_context_create($aContext);
        //var_dump($aContext); // Can be used for debugging your current headers etc.
        $this->response_body = @file_get_contents($uri, false, $cxContext);
        if ($this->response_body === false) {
            $this->response_headers = $http_response_header;
            return false;
        } else {
            $this->response_headers = $http_response_header;
            return $this;
        }
    }

    /**
     * Sends the request to server and returns the raw response using cURL (Server must have PHP compiled with cURL for this to work!).
     * @param string $uri The URI to send the request to.
     */
    private function requestViaCurl($uri)
    {

    }

    /**
     * Sends the request to server and returns the raw response.
     * @param string $uri The full URI to request and get the raw response from.
     * @return \Ballen\Ravish\HTTPClient
     */
    protected function sendRequest($uri)
    {
        $this->resetResponse();
        if ($this->use_curl) {
            return $this->requestViaCurl($uri);
        } else {
            return $this->requestViaFileGetContents($uri);
        }
    }

    /**
     * Enable or Disable server side redirects when sending requests.
     * @param boolean $setting Allow server-side redirects
     * @return \Ballen\Ravish\HTTPClient
     */
    public function serverRedirects($setting)
    {
        $this->request_followredirects = (bool) $setting;
        return $this;
    }

    /**
     * Display page/responses that are generated by failed requested caused by file_get_contents (the web request function), default is 'true'.
     * @param boolean $setting Display errors or not.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function showErrors($setting)
    {
        $this->request_showerrors = (bool) $setting;
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
        $this->proxy_host = (string) $host;
        if ($port) {
            $this->proxy_port = (int) $port;
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
        $this->proxy_auth = (string) base64_encode("$username:$password");
        return $this;
    }

    /**
     * Enables the setting of a custom useragent string.
     * @param string $useragent
     */
    public function setCustomUserAgent($useragent)
    {
        $this->user_agent = (string) $useragent;
        return $this;
    }

    /**
     * Add a custom request header.
     * @param string $name Header name/key eg. 'Content-type'
     * @param string $value Value of the header for example 'application/json'
     * @return \Ballen\Ravish\HTTPClient
     */
    public function addRequestHeader($name, $value)
    {
        $this->request_headers[$name] = $value;
        return $this;
    }

    /**
     * Adds a POST/PUT/DELETE parameter to the request.
     * @param string $key The parameter name/key.
     * @param string $value The parameter value.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function addParameter($key, $value)
    {
        $this->request_params[$key] = $value;
        return $this;
    }

    /**
     * Sets a raw request body string.
     * @param string $content
     * @return \Ballen\Ravish\HTTPClient
     */
    public function setRequestBody($content)
    {
        $this->request_body = $content;
        return $this;
    }

    /**
     * Sets optional basic HTTP authetnication header.
     * @param string $username The username to authenticate with.
     * @param string $password The password belonging to the user account.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function setBasicAuthCredentials($username, $password)
    {
        $this->request_basicauth = base64_encode($username . ":" . $password);
        return $this;
    }

    /**
     * Sets the request type to be 'POST'.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function post($uri)
    {
        $this->request_httpmethod = 'POST';
        return $this->sendRequest($uri);
    }

    /**
     * Sets the request type to be 'GET'.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function get($uri)
    {
        $this->request_httpmethod = 'GET';
        return $this->sendRequest($uri);
    }

    /**
     * Sets the request type to be 'PUT'.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function put($uri)
    {
        $this->request_httpmethod = 'PUT';
        return $this->sendRequest($uri);
    }

    /**
     * Sets the request type to be 'PATCH'.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function patch($uri)
    {
        $this->request_httpmethod = 'PATCH';
        return $this->sendRequest($uri);
    }

    /**
     * Sets the request type to be 'DELETE'.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function delete($uri)
    {
        $this->request_httpmethod = 'DELETE';
        return $this->sendRequest($uri);
    }

    /**
     * Resets the request parameters/settings ready for the next request.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function resetRequest()
    {
        $this->request_headers = array();
        $this->request_httpmethod = 'GET';
        $this->request_params = array();
        $this->request_body = null;
        return $this;
    }

    /**
     * Resets the response header and body content ready.
     * @return \Ballen\Ravish\HTTPClient
     */
    public function resetResponse()
    {
        $this->response_headers = array();
        $this->response_body = null;
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
