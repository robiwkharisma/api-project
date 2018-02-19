<?php

namespace App\Helpers;

class CurlHelper {
	/**
	 * cURL request method
	 *
	 * @var string
	 */
	protected $_method = 'GET';
	/**
	 * Currently accepted cURL request method types
	 *
	 * @var string
	 */
	protected $_methods = array('POST', 'GET');
	/**
	 * cURL URL
	 *
	 * @var string
	 */
	protected $_url = '';
	/**
	 * cURL data params
	 *
	 * @var string
	 */
	protected $_params = array();
	/**
	 * cURL options
	 *
	 * @var array
	 */
	protected $_options = array();
	/**
	 * cURL user agent
	 *
	 * @var array
	 */
	protected $_agent = 'ground(ctrl) engine';
	/**
	 * GET request
	 *
	 * @param   string  $url
	 * @param   array   $params
	 * @param   array   $options
	 * @return  mixed
	 */
	static public function get($url = '', $params = array(), $options = array()) {
		$cSession = curl_init();

		curl_setopt($cSession,CURLOPT_URL,$url);

		// pass header variable in curl method
		curl_setopt($cSession, CURLOPT_HTTPHEADER, $options);

		curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
		$result=curl_exec($cSession);

		curl_close($cSession);

		return $result;

		// return self::make($url, $params, $options, 'GET');
	}

	/**
	 * POST request
	 *
	 * @param   string  $url
	 * @param   array   $params
	 * @param   array   $options
	 * @return  mixed
	 */
	static public function post($url = '', $params = array(), $options = array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$options);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		// if(sizeof($options)>0){
		// 	foreach($options as $key => $value){
		// 		curl_setopt($ch, constant($key), $value);
		// 	}
		// }

		// $response = new \stdClass();
		$response = curl_exec($ch);
		// $response->info = curl_getinfo($ch);
		// $response->error = curl_error($ch);
		// $response->error_code = curl_errno($ch);

		curl_close($ch);
		return $response;
	}

	/**
	 * PUT request
	 *
	 * @param   string  $url
	 * @param   array   $params
	 * @param   array   $options
	 * @return  mixed
	 */
	static public function put($url = '', $params = array(), $options = array()) {
		// return self::make($url, $params, $options, 'PUT');
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
		if(isset($options['apikey'])){
			curl_setopt($curl, CURLOPT_USERPWD, "apikey :" . $options['apikey']);  
		}

		// Make the REST call, returning the result
		$response = curl_exec($curl);
		if (!$response) {
		    die("Connection Failure.n");
		}else{
			return $response;
		}
	}

	/**
	 * DELETE request
	 *
	 * @param   string  $url
	 * @param   array   $params
	 * @param   array   $options
	 * @return  mixed
	 */
	static public function delete($url = '', $options = array()) {
		// return self::make($url, $params, $options, 'PUT');
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		if(isset($options['apikey'])){
			curl_setopt($curl, CURLOPT_USERPWD, "apikey :" . $options['apikey']);  
		}

		// Make the REST call, returning the result
		$response = curl_exec($curl);
		if($response == ""){
			return array("response"=>"delete", 'http_code'=>"200");
		}else{
			return $response;
		}
	}

	/**
	 * Make request
	 *
	 * @param   string  $url
	 * @param   array   $params
	 * @param   array   $options
	 * @param   string  $method
	 * @return  Curl
	 */
	static public function make($url = '', $params = array(), $options = array(), $method = null) {
		return new self($url, $params, $options, $method);
	}

	public function __construct($url = '', $params = array(), $options = array(), $method = null) {
		// Set request method
		if ($method) {
			$this->_method = $method;
		}
		// Explode the URL to get the URL params
		$url_split = explode('?', $url);
		// Request URL is everything before the ? (if it exists)
		$this->_url = $url_split[0];
		// If there were URL params, add it to the params array
		$this->_params = array_merge($this->_params, $this->_queryString($url), (array) $params);
		// Set the passed options
		$this->_options($options);
	}

	/**
	 * Add multiple params
	 *
	 * @param   array   $keys
	 * @return  Curl
	 */
	public function params($keys = array()) {
		$this->_params = array_merge($this->_params, (array) $keys);
		return $this;
	}

	/**
	 * Add a single param
	 *
	 * @param   string  $key
	 * @param   string  $value
	 * @return  Curl
	 */
	public function param($key = '', $value = '') {
		if ( ! empty($key) && is_string($key)) {
			$key = array(
				$key => (string) $value
			);
		}
		return $this->params($key);
	}

	/**
	 * Add multiple options
	 *
	 * @param   array   $options
	 * @return  Curl
	 */
	public function options($options = array()) {
		$this->_options($options);
		return $this;
	}

	/**
	 * Add single option
	 *
	 * @param   string  $key
	 * @param   string  $value
	 * @return  Curl
	 */
	public function option($key = '', $value = '') {
		$this->_option($key, $value);
		return $this;
	}

	/**
	 * Request method
	 *
	 * @param   string  $method
	 * @return  Curl
	 */
	public function method($method = null) {
		$this->_method = $method;
		return $this;
	}

	/**
	 * User agent
	 *
	 * @param   string  $agent
	 * @return  Curl
	 */
	public function agent($agent = '') {
		if ($agent) {
			return $this->option('CURLOPT_USERAGENT', $agent);
		}
		return $this;
	}

	/**
	 * Proxy helper
	 *
	 * @param   string  $url
	 * @param   int     $port
	 * @return  Curl
	 */
	public function proxy($url = '', $port = 80) {
		return $this->options(array(
			'CURLOPT_HTTPPROXYTUNNEL' => true,
			'CURLOPT_PROXY' => $url.':'.$port
		));
	}

	/**
	 * Custom header helper
	 *
	 * @param   string  $header
	 * @param   string  $content
	 * @return  Curl
	 */
	public function httpHeader($header, $content = null) {
		$header = ($content) ? $header.':'.$content : $header;
		return $this->option('CURLOPT_HTTPHEADER', $header);
	}

	/**
	 * HTTP login helper
	 *
	 * @param   string  $username
	 * @param   string  $password
	 * @param   string  $type
	 * @return  Curl
	 */
	public function httpLogin($username = '', $password = '', $type = 'ANY') {
		return $this->options(array(
			'CURLOPT_HTTPAUTH' => constant('CURLAUTH_'.strtoupper($type)),
			'CURLOPT_USERPWD' => $username.':'.$password
		));
	}

	/**
	 * Proxy login helper
	 *
	 * @param   string  $username
	 * @param   string  $password
	 * @return  Curl
	 */
	public function proxyLogin($username = '', $password = '') {
		return $this->option('CURLOPT_PROXYUSERPWD', $username.':'.$password);
	}

	/**
	 * SSL helper
	 *
	 * @param   bool    $verify_peer
	 * @param   int     $verify_host
	 * @param   string  $path_to_cert
	 * @return  Curl
	 */
	public function ssl($verify_peer = true, $verify_host = 2, $path_to_cert = null) {
		if ($verify_peer) {
			$options = array(
				'CURLOPT_SSL_VERIFYPEER' => true,
				'CURLOPT_SSL_VERIFYHOST' => $verify_host,
				'CURLOPT_CAINFO' => $path_to_cert
			);
		}
		else {
			$options = array(
				'CURLOPT_SSL_VERIFYPEER' => false
			);
		}
		return $this->options($options);
	}

	/**
	 * Execute request
	 *
	 * @return  Curl
	 * @throws  Exception
	 */
	public function call() {
		// cURL is not enabled
		if ( ! $this->_isEnabled()) {
			throw new Exception(__CLASS__.': PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
		}
		// Request method
		$method = ($this->_method) ? strtoupper($this->_method) : 'GET';
		// Unrecognized request method?
		if ( ! in_array($method, $this->_methods)) {
			throw new Exception(__CLASS__.': Unrecognized request method of '.$this->_method);
		}
		return $this->_execute($method);
	}

	/**
	 * Alias for call();
	 *
	 * @return  Curl
	 */
	public function execute() {
		return $this->call();
	}

	/**
	 * Execute request
	 *
	 * @param   string  $method
	 * @return  Curl
	 */
	private function _execute($method) {
		// Method specific options
		switch ($method) {
			case 'GET' :
				// Append GET params to URL
				$this->_url .= '?'.http_build_query($this->_params);
				// Set options
				$this->options('CURLOPT_HTTPGET', 1);
				break;
			case 'POST' :
				// Set options
					$this->options(array(
						'CURLOPT_HEADER' => true,
						'CURLOPT_POST' => true,
						'CURLOPT_POSTFIELDS' => $this->_params
					));
				break;
			// Mostly for future use
			case 'PUT' :
				// Set options
				$this->options(array(
					'CURLOPT_PUT' => true,
					'CURLOPT_HTTPHEADER' => array('Content-Type: '.strlen($this->_params)),
					'CURLOPT_POSTFIELDS' => $this->_params
				));
				break;
			// Mostly for future use
			case 'DELETE' :
				// Set options
				$this->option('CURLOPT_CUSTOMREQUEST', 'DELETE');
				$this->option('CURLOPT_POSTFIELDS', $this->_params);
				break;
		}

		// Set timeout option if it isn't already set
		if ( ! array_key_exists('CURLOPT_TIMEOUT', $this->_options)) {
			$this->option('CURLOPT_TIMEOUT', 30);
		}

		// Set returntransfer option if it isn't already set
		if ( ! array_key_exists('CURLOPT_RETURNTRANSFER', $this->_options)) {
			$this->option('CURLOPT_RETURNTRANSFER', true);
		}

		// Set failonerror option if it isn't already set
		if ( ! array_key_exists('CURLOPT_FAILONERROR', $this->_options)) {
			$this->option('CURLOPT_FAILONERROR', true);
		}

		// Set user agent option if it isn't already set
		if ( ! array_key_exists('CURLOPT_USERAGENT', $this->_options)) {
			$this->option('CURLOPT_USERAGENT', $this->_agent);
		}

		// Only set follow location if not running securely
		if ( ! ini_get('safe_mode') && ! ini_get('open_basedir')) {
			// Ok, follow location is not set already so lets set it to true
			if ( ! array_key_exists('CURLOPT_FOLLOWLOCATION', $this->_options)) {
				$this->option('CURLOPT_FOLLOWLOCATION', true);
			}
		}
		// Initialize cURL request
		$ch = curl_init($this->_url);
		// Can't set the options in batch
		if ( ! function_exists('curl_setopt_array')) {
			foreach ($this->_options as $key => $value) {
				curl_setopt($ch, $key, $value);
			}
		}
		// Set options in batch
		else {
			curl_setopt_array($ch, $this->_options);
			if($method == 'POST'){
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			}
		}
		// Execute
		$response = new \stdClass();
		$response->result = curl_exec($ch);
		$response->info = curl_getinfo($ch);
		$response->error = curl_error($ch);
		$response->error_code = curl_errno($ch);
		// Reset the options
		$this->_url = '';
		$this->_params = '';
		$this->_options = array();
		// Close cURL request
		curl_close($ch);
		return $response;
	}
	/**
	 * Add multiple options
	 *
	 * @param   array   $options
	 */
	protected function _options($options = array()) {
		foreach ((array) $options as $key => $value) {
			$this->_option($key, $value);
		}
	}
	/**
	 * Add single option
	 *
	 * @param   string  $key
	 * @param   string  $value
	 * @throws  Exception
	 */
	protected function _option($key = '', $value = '') {
		if (is_string($key) && ! is_numeric($key)) {
			$const = strtoupper($key);
			if (defined($const)) {
				$key = constant(strtoupper($key));
			}
			else {
				throw new \Exception('Curl: Constant ['.$const.'] not defined.');
			}
		}
		// Custom header
		if ($key == CURLOPT_HTTPHEADER) {
			$this->_options[$key][] = $value;
			dd($this->_options);
		}
		// Not a custom header
		else {
			$this->_options[$key] = $value;
		}
	}
	/**
	 * Get query string from URL
	 *
	 * @param   $uri
	 * @return  array
	 */
	protected function _queryString($uri) {
		$query_data = array();
		$query_array = html_entity_decode(parse_url($uri, PHP_URL_QUERY));
		if ( ! empty($query_array)) {
			$query_array = explode('&', $query_array);
			foreach($query_array as $val) {
				$x = explode('=', $val);
				$query_data[$x[0]] = $x[1];
			}
		}
		return $query_data;
	}
	/**
	 * Check if cURL is enabled
	 *
	 * @return  bool
	 */
	protected function _isEnabled() {
		return function_exists('curl_init');
	}
}
