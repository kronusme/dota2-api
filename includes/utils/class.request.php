<?php
/**
 * Class represents basic functionality for sending request to DotA2 API service and receive response
 *
 * @author kronus
 * @package utils
 */
class request {
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
    public function get_url() {
        return $this->_url;
    }

    /**
     * Set url
     * @param string $url
     * @return request
     */
    public function set_url($url) {
        $this->_url = (string)$url;
        return $this;
    }

    /**
     * Get all request parameters
     * @return array
     */
    public function get_all_params() {
        return $this->_params;
    }

    /**
     * Get request parameter by its name
     * @param string $name
     * @return string | null
     */
    public function get_parameter($name) {
        $name = (string)$name;
        if (isset($this->_params[$name])) {
            return $this->_params[$name];
        }
        return null;
    }

    /**
     * Set parameter by its name and value
     * @param string $name
     * @param string $value
     * @return request
     */
    public function set_parameter($name, $value) {
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
    public function set_parameters(array $params) {
        $this->_params = $params + $this->_params;
        return $this;
    }

    /**
     * @param string $url
     * @param array $params
     */
    public function __construct($url, array $params) {
        $this->_url = $url;
        $this->_params = $params;
    }

    /**
     * Send request to Valve's servers
     * @access public
     * @return mixed
     */
    public function send() {
        $ch = curl_init();
        $url = $this->_url;
        $d = '';
        $this->_params['format'] = 'xml';
        $this->_params['key'] = API_KEY;
        $this->_params['language'] = LANGUAGE;
        foreach ($this->_params as $key=>$value) {
            $d .= $key.'='.$value.'&';
        }
        $d = rtrim($d, '&');
        $url .= '?'.$d;
        //echo $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Ignore SSL warnings and questions
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $r = curl_exec($ch);
        curl_close($ch);
        libxml_use_internal_errors(true);
        try {
            $r = new SimpleXMLElement($r);
        }
        catch(Exception $e) {
            return null;
        }
        return $r;
    }
}
