<?php

class CurlClient {

	function __construct() {

		$this->init();

	}

	function init()	{

		$this->ch = curl_init();

		curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->ch, CURLOPT_ENCODING , 'gzip, deflate');
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

	}

	function set_user_agent($useragent) {

		curl_setopt($this->ch, CURLOPT_USERAGENT, $useragent);

	}

	function get_html($url, $ip = null, $timeout = 30) {

		curl_setopt($this->ch, CURLOPT_URL,$url);
		curl_setopt($this->ch, CURLOPT_HTTPGET,true);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,true);

		if($ip) {

			curl_setopt($this->ch,CURLOPT_INTERFACE,$ip);

		}

		curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);

		$result = curl_exec($this->ch);

		if(curl_errno($this->ch)) {

			return false;

		} else {

			return $result;

		}

	}

	function get_http_response_code() {

		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

	}

	function close() {

		curl_close($this->ch);

	}

}

?>
