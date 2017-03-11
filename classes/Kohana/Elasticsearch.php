<?php

defined('SYSPATH') or die('No direct script access.');

/*
 * Elasticsearch Kohana Module.
 */

class Kohana_Elasticsearch {

    /** Elasticsearch instance.
     *
     * @var Array<Elasticsearch> 
     */
    protected static $_instances = Array();
    
    /**
     * @var GuzzleHttp\Client
     */
    protected $client;
    
    /**
     * @var Array
     */
    protected $config;

    /**
      * Expected JSON in response
      */
    const ERROR_EXPECTED_JSON = 1;
    
    /**
      * CURL communication error
      */
    const ERROR_CURL_TRANSPORT = 2;
    
    /**
      * Wrong HTTP response (!= 200)
      */
    const ERROR_HTTP_RESPONSE = 3;
    
    
    /**
     * Singleton pattern.
     *
     * @return Elasticsearch
     */
    public static function instance($instance = 'default') {
        if (!isset(self::$_instances[$instance])) {
            // Create a new session instance
            self::$_instances[$instance] = new self($instance);
        }

        return self::$_instances[$instance];
    }

    protected function __construct($instance) {
        
        // Load the configuration for this type
        $this->config = Kohana::$config->load('elasticsearch.'.$instance);
        $this->client = new GuzzleHttp\Client($this->config['client']);
    }

    public function request($path = NULL, $method = 'GET', $content = array()) {
        return $this->client->$method( $path, [ 'json' => $content ])->json();
    }

    public function count($type, $data = FALSE) {
        return $this->request($type . '/_count', 'GET', ($data) ? $data : '{ matchAll:{} }');
    }

    public function stats() {
        return $this->request('_stats');
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

    public function status() {
        $dd = explode("\n",trim($this->client->get('/_cat/health?v')->getBody()));
        $k = preg_split('/\s+/',trim(array_shift($dd)));
        $st = Array();
        
        foreach($dd AS $one){
            $st[] = array_combine($k,preg_split('/\s+/',trim($one)));
        }
        return $st;
    }
}
