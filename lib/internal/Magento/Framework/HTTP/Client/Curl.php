<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\HTTP\Client;

/**
 * Class to work with HTTP protocol using curl library
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @api
 */
class Curl implements \Magento\Framework\HTTP\ClientInterface
{
    /**
     * Max supported protocol by curl CURL_SSLVERSION_TLSv1_2
     * @var int
     */
    private $sslVersion;

    /**
     * Hostname
     * @var string
     */
    private $host = 'localhost';

    /**
     * Port
     * @var int
     */
    private $port = 80;

    /**
     * Stream resource
     * @var object
     */
    private $sock = null;

    /**
     * Request headers
     * @var array
     */
    private $headers = [];

    /**
     * Fields for POST method - hash
     * @var array
     */
    private $postFields = [];

    /**
     * Request cookies
     * @var array
     */
    private $cookies = [];

    /**
     * Response headers
     * @var array
     */
    private $responseHeaders = [];

    /**
     * Response body
     * @var string
     */
    private $responseBody = '';

    /**
     * Response status
     * @var int
     */
    private $responseStatus = 0;

    /**
     * Request timeout
     * @var int type
     */
    private $timeout = 300;

    /**
     * @var int
     */
    private $redirectCount = 0;

    /**
     * Curl
     * @var resource
     */
    private $ch;

    /**
     * User overrides options hash
     * Are applied before curl_exec
     *
     * @var array
     */
    private $curlUserOptions = [];

    /**
     * Header count, used while parsing headers
     * in CURL callback function
     * @var int
     */
    private $headerCount = 0;

    /**
     * Set request timeout
     *
     * @param int $value value in seconds
     * @return void
     */
    public function setTimeout($value)
    {
        $this->timeout = (int)$value;
    }

    /**
     * @param int|null $sslVersion
     */
    public function __construct($sslVersion = null)
    {
        $this->sslVersion = $sslVersion;
    }

    /**
     * Set headers from hash
     *
     * @param array $headers
     * @return void
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Add header
     *
     * @param string $name name, ex. "Location"
     * @param string $value value ex. "http://google.com"
     * @return void
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Remove specified header
     *
     * @param string $name
     * @return void
     */
    public function removeHeader($name)
    {
        unset($this->headers[$name]);
    }

    /**
     * Authorization: Basic header
     *
     * Login credentials support
     *
     * @param string $login username
     * @param string $pass password
     * @return void
     */
    public function setCredentials($login, $pass)
    {
        $val = base64_encode("{$login}:{$pass}");
        $this->addHeader("Authorization", "Basic {$val}");
    }

    /**
     * Add cookie
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }

    /**
     * Remove cookie
     *
     * @param string $name
     * @return void
     */
    public function removeCookie($name)
    {
        unset($this->cookies[$name]);
    }

    /**
     * Set cookies array
     *
     * @param array $cookies
     * @return void
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Clear cookies
     *
     * @return void
     */
    public function removeCookies()
    {
        $this->setCookies([]);
    }

    /**
     * Make GET request
     *
     * @param string $uri uri relative to host, ex. "/index.php"
     * @return void
     */
    public function get($uri)
    {
        $this->makeRequest("GET", $uri);
    }

    /**
     * Make POST request
     *
     * String type was added to parameter $param in order to support sending JSON or XML requests.
     * This feature was added base on Community Pull Request https://github.com/magento/magento2/pull/8373
     *
     * @param string $uri
     * @param array|string $params
     * @return void
     *
     * @see \Magento\Framework\HTTP\Client#post($uri, $params)
     */
    public function post($uri, $params)
    {
        $this->makeRequest("POST", $uri, $params);
    }

    /**
     * Get response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->responseBody;
    }

    /**
     * Get cookies response hash
     *
     * @return array
     */
    public function getCookies()
    {
        if (empty($this->responseHeaders['Set-Cookie'])) {
            return [];
        }
        $out = [];
        foreach ($this->responseHeaders['Set-Cookie'] as $row) {
            $values = explode("; ", $row);
            $c = count($values);
            if (!$c) {
                continue;
            }
            list($key, $val) = explode("=", $values[0]);
            if ($val === null) {
                continue;
            }
            $out[trim($key)] = trim($val);
        }
        return $out;
    }

    /**
     * Get cookies array with details
     * (domain, expire time etc)
     *
     * @return array
     */
    public function getCookiesFull()
    {
        if (empty($this->responseHeaders['Set-Cookie'])) {
            return [];
        }
        $out = [];
        foreach ($this->responseHeaders['Set-Cookie'] as $row) {
            $values = explode("; ", $row);
            $c = count($values);
            if (!$c) {
                continue;
            }
            list($key, $val) = explode("=", $values[0]);
            if ($val === null) {
                continue;
            }
            $out[trim($key)] = ['value' => trim($val)];
            array_shift($values);
            $c--;
            if (!$c) {
                continue;
            }
            for ($i = 0; $i < $c; $i++) {
                list($subkey, $val) = explode("=", $values[$i]);
                $out[trim($key)][trim($subkey)] = trim($val);
            }
        }
        return $out;
    }

    /**
     * Get response status code
     *
     * @see lib\Magento\Framework\HTTP\Client#getStatus()
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->responseStatus;
    }

    /**
     * Make request
     *
     * String type was added to parameter $param in order to support sending JSON or XML requests.
     * This feature was added base on Community Pull Request https://github.com/magento/magento2/pull/8373
     *
     * @param string $method
     * @param string $uri
     * @param array|string $params - use $params as a string in case of JSON or XML POST request.
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function makeRequest($method, $uri, $params = [])
    {
        $this->ch = curl_init();
        $this->curlOption(CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP | CURLPROTO_FTPS);
        $this->curlOption(CURLOPT_URL, $uri);
        if ($method == 'POST') {
            $this->curlOption(CURLOPT_POST, 1);
            $this->curlOption(CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
        } elseif ($method == "GET") {
            $this->curlOption(CURLOPT_HTTPGET, 1);
        } else {
            $this->curlOption(CURLOPT_CUSTOMREQUEST, $method);
        }

        if (count($this->headers)) {
            $heads = [];
            foreach ($this->headers as $k => $v) {
                $heads[] = $k . ': ' . $v;
            }
            $this->curlOption(CURLOPT_HTTPHEADER, $heads);
        }

        if (count($this->cookies)) {
            $cookies = [];
            foreach ($this->cookies as $k => $v) {
                $cookies[] = "{$k}={$v}";
            }
            $this->curlOption(CURLOPT_COOKIE, implode(";", $cookies));
        }

        if ($this->timeout) {
            $this->curlOption(CURLOPT_TIMEOUT, $this->timeout);
        }

        if ($this->port != 80) {
            $this->curlOption(CURLOPT_PORT, $this->port);
        }

        $this->curlOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlOption(CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);
        if ($this->sslVersion !== null) {
            $this->curlOption(CURLOPT_SSLVERSION, $this->sslVersion);
        }

        if (count($this->curlUserOptions)) {
            foreach ($this->curlUserOptions as $k => $v) {
                $this->curlOption($k, $v);
            }
        }

        $this->headerCount = 0;
        $this->responseHeaders = [];
        $this->responseBody = curl_exec($this->ch);
        $err = curl_errno($this->ch);
        if ($err) {
            $this->doError(curl_error($this->ch));
        }
        curl_close($this->ch);
    }

    /**
     * Throw error exception
     *
     * @param string $string
     * @return void
     * @throws \Exception
     */
    public function doError($string)
    {
        //  phpcs:ignore Magento2.Exceptions.DirectThrow
        throw new \Exception($string);
    }

    /**
     * Parse headers - CURL callback function
     *
     * @param resource $ch curl handle, not needed
     * @param string $data
     * @return int
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function parseHeaders($ch, $data)
    {
        if ($this->headerCount == 0) {
            $line = explode(" ", trim($data), 3);
            if (count($line) < 2) {
                $this->doError("Invalid response line returned from server: " . $data);
            }
            $this->responseStatus = (int)$line[1];
        } else {
            $name = $value = '';
            $out = explode(": ", trim($data), 2);
            if (count($out) == 2) {
                $name = $out[0];
                $value = $out[1];
            }

            if (strlen($name)) {
                if ('set-cookie' === strtolower($name)) {
                    $this->responseHeaders['Set-Cookie'][] = $value;
                } else {
                    $this->responseHeaders[$name] = $value;
                }
            }
        }
        $this->headerCount++;

        return strlen($data);
    }

    /**
     * Set curl option directly
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    private function curlOption($name, $value)
    {
        curl_setopt($this->ch, $name, $value);
    }

    /**
     * Set curl options array directly
     *
     * @param array $arr
     * @return void
     */
    private function curlOptions($arr)
    {
        curl_setopt_array($this->ch, $arr);
    }

    /**
     * Set CURL options overrides array
     *
     * @param array $arr
     * @return void
     */
    public function setOptions($arr)
    {
        $this->curlUserOptions = $arr;
    }

    /**
     * Set curl option
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setOption($name, $value)
    {
        $this->curlUserOptions[$name] = $value;
    }
}
