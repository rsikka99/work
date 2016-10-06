<?php

namespace MPSToolbox\Api;

class Cloudinary {

    private static $instance = null;
    private $api = false;

    private function __construct() {
        /** @var \Zend_Config $cfg */
        $cfg = \Zend_Registry::get('config')->cloudinary;
        $arr=$cfg->toArray();
        \Cloudinary::config($arr);
        $this->api = new \Cloudinary\Api();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Cloudinary();
        }
        return self::$instance;
    }

    /**
     * @return \Cloudinary\Api
     */
    public function getApi() {
        return $this->api;
    }

    /**
     * @param $filename
     * @return array
     */
    public function upload($filename) {
        return \Cloudinary\Uploader::upload($filename);
    }

}