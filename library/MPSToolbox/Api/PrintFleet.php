<?php

namespace MPSToolbox\Api;

use cdyweb\http\Exception\RequestException;
use cdyweb\http\guzzle\Guzzle;
use GuzzleHttp\Psr7\Response;

class PrintFleet {

    /** @var string[]  */
    private $uri = null;

    /** @var string */
    private $base_path = '';

    public function __construct($rmsUri) {
        $this->uri = parse_url($rmsUri);
        switch ($this->uri['host']) {
            case 'mps.partsnow.com':
                $this->base_path = 'https://'.$this->uri['host'].'/restapi/3.6.5';
                break;
            default:
                $this->base_path = 'https://'.$this->uri['host'].'/restapi/3.7.1';
                break;
        }
    }

    /** @var \cdyweb\http\BaseAdapter */
    private $http;

    /**
     * @return \cdyweb\http\BaseAdapter
     */
    public function getHttp()
    {
        if (empty($this->http)) {
            $this->http = Guzzle::getAdapter();
        }
        return $this->http;
    }

    /**
     * @param \cdyweb\http\BaseAdapter $http
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }

    private function readUrl($url) {
        /** @var \Zend_Log $log */
        $log = \Zend_Registry::get('Zend_Log');
        $log->log($url, 4);
        $filename='';
        if (file_exists('c:') && !defined('UNIT_TESTING')) {
            $parsed = parse_url($url);
            $filename = 'data/cache/'.basename($parsed['path']) . '.' . md5($url) . '.txt';
            if (file_exists($filename)) return new Response(200, [], file_get_contents($filename));
        }
        $result = $this->getHttp()->get($url);
        if (file_exists('c:') && !defined('UNIT_TESTING')) {
            $body = $result->getBody()->getContents();
            file_put_contents($filename, json_encode(json_decode($body, true), JSON_PRETTY_PRINT));
            $result = new Response(200, [], file_get_contents($filename));
        }
        return $result;
    }

    public function auth() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $api_key = $db->query("select `key` from printfleet_api_key where `host`='".$this->uri['host']."'")->fetchColumn(0);

        $http = $this->getHttp();
        $http->appendRequestHeader('Accept','application/json');
        $http->appendRequestHeader('X-API-KEY', $api_key);

        $http->setBasicAuth($this->uri['user'],$this->uri['pass']);
        try {
            $http->put($this->base_path . '/auth');
        } catch(RequestException $ex) {
            \Tangent\Logger\Logger::logException($ex);
            echo 'Auth failed for uri '.$this->base_path.': '.$ex->getResponse()->getStatusCode().' '.$ex->getResponse()->getBody()->getContents()."<br>\n";
            return false;
        }
        return true;
    }

    public function devices($groupId) {
        $url=$this->base_path.'/devices?'.http_build_query(['groupId'=>$groupId,'includeSubGroups'=>'true']);
        $response = $this->readUrl($url);
        $str = $response->getBody()->getContents();
        return json_decode($str, true);
    }

    public function device($device_id) {
        $url=$this->base_path.'/devices/'.$device_id;
        $response = $this->readUrl($url);
        $str = $response->getBody()->getContents();
        return json_decode($str, true);
    }

    public function meters($device_id, $startDate, $endDate) {
        $url=$this->base_path.'/devices/'.$device_id.'/meters?'.http_build_query(['startDate'=>$startDate.'T00:00:00Z','endDate'=>$endDate.'T00:00:00Z','intervalUnit'=>'yearly']);
        $response = $this->readUrl($url);
        $str = $response->getBody()->getContents();
        return json_decode($str, true);
    }

    public function groups() {
        $url=$this->base_path.'/groups';
        $response = $this->readUrl($url);
        $str = $response->getBody()->getContents();
        return json_decode($str, true);
    }

}
