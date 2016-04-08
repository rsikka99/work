<?php
class My_SessionSaveHandlerCache implements Zend_Session_SaveHandler_Interface {
    private $maxlifetime = 3600;
    /** @var Zend_Cache_Core */
    public  $cache = '';
    public function __construct($cacheHandler) {
        $this->cache = $cacheHandler;
    }
    public function open($save_path, $name) {
        return true;
    }
    public function close() {
        return true;
    }
    public function read($id) {
        if(!($data = $this->cache->load($id))) {
            return '';
        }
        else {
            return $data;
        }
    }
    public function write($id, $sessionData) {
        $this->cache->save($sessionData, $id, array(), $this->maxlifetime);
        return true;
    }
    public function destroy($id) {
        $this->cache->remove($id);
        return true;
    }
    public function gc($notusedformemcache) {
        return true;
    }
}