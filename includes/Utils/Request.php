<?php

namespace Dota2Api\Utils;

use SimpleXMLElement;
use Exception;

/**
 * Class represents basic functionality for sending request to DotA2 API service and receive response
 *
 * @author kronus
 */
class Request
{

    /**
     * @var string
     */
    public static $apiKey = '';

    /**
     * @var string
     */
    private $_url;
    /**
     * @var array
     */
    private $_params;

    /**
     * Get url
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set url
     * @param string $url
     * @return request
     */
    public function setUrl($url)
    {
        $this->_url = (string)$url;
        return $this;
    }

    /**
     * Get all request parameters
     * @return array
     */
    public function getAllParams()
    {
        return $this->_params;
    }

    /**
     * Get request parameter by its name
     * @param string $name
     * @return string | null
     */
    public function getParameter($name)
    {
        $name = (string)$name;
        return array_key_exists($name, $this->_params) ? $this->_params[$name] : null;
    }

    /**
     * Set parameter by its name and value
     * @param string $name
     * @param string $value
     * @return request
     */
    public function setParameter($name, $value)
    {
        $name = (string)$name;
        $value = (string)$value;
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * Set array of parameters. New values will rewrite older
     * @param array $params
     * @return request
     */
    public function setParameters(array $params)
    {
        $this->_params = $params + $this->_params;
        return $this;
    }

    /**
     * @param string $url
     * @param array $params
     */
    public function __construct($url, array $params)
    {
        $this->_url = $url;
        $this->_params = $params;
    }

    /**
     * Send request to Valve's servers
     * @access public
     * @return SimpleXMLElement | null
     */
    public function send()
    {
        $ch = curl_init();
        $url = $this->_url;
        $d = '';
        $this->_params['format'] = 'xml';
        $this->_params['key'] = self::$apiKey;
        // The language to retrieve results in (see http://en.wikipedia.org/wiki/ISO_639-1 for the language codes (first
        // two characters) and http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes for the country codes (last two characters))
        $this->_params['language'] = 'en_us';
        foreach ($this->_params as $key => $value) {
            $d .= $key . '=' . $value . '&';
        }
        $d = rtrim($d, '&');
        $url .= '?' . $d;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Ignore SSL warnings and questions
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $r = curl_exec($ch);
        curl_close($ch);
        libxml_use_internal_errors(true);
        try {
            $r = new SimpleXMLElement($r);
        } catch (Exception $e) {
            return null;
        }
        return $r;
    }
}
