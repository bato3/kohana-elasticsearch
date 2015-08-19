<?php

defined('SYSPATH') or die('No direct script access.');

/*
 * Elasticsearch Kohana Module.
 */

class Kohana_Elasticsearch {

	/** Elasticsearch instance.
	 *
	 * @var Elasticsearch 
	 */
	protected static $_instance;

	/**
	 * @var string 
	 */
	protected $_host;

	/**
	 * @var string 
	 */
	protected $_index;

	/**
	 * @var string 
	 */
	protected $_port;

	/**
	 * Singleton pattern.
	 *
	 * @return Elasticsearch
	 */
	public static function instance($index = NULL) {
		if (!isset(Elasticsearch::$_instance)) {
			// Load the configuration for this type
			$config = Kohana::$config->load('elasticsearch');

			// Create a new session instance
			self::$_instance = new self($config->get('host'), $config->get('port'), isset($index) ? $index : $config->get('index'));
		}

		return self::$_instance;
	}

	protected function __construct($host, $port, $index) {
		$this->_index = $index;
		$this->_host = $host;
		$this->_port = $port;
	}

	protected function request($path, $method = 'GET', $content = array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_host . '/' . $this->_index . '/' . $path);
		curl_setopt($ch, CURLOPT_PORT, $this->_port);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

		if (!empty($content)) {
			$data = $content;
			if (is_array($content))
				$data = json_encode($content);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}

	public function count($type, $data = FALSE) {
		return $this->request($type . '/_count', 'GET', ($data) ? $data : '{ matchAll:{} }');
	}

	public function status() {
		return $this->request('_status');
	}

	public function add($type, $id, $data) {
		return $this->request($type . '/' . $id, 'PUT', $data);
	}

	public function delete($type, $id) {
		$this->request($type . '/' . $id, 'DELETE');
	}

	public function delete_all($type) {
		$this->request($type, 'DELETE');
	}

	public function update($type, $id, $data) {
		$this->request($type . '/' . $id . '/_update', 'POST', $data);
	}

	public function mapping($type, $data) {
		return $this->request($type . '/_mapping', 'PUT', $data);
	}

	public function search($type, $query = array()) {
		return $this->request($type . '/_search', 'POST', $query);
	}

}
