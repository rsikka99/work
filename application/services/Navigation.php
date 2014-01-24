<?php

/**
 * Class Application_Service_Navigation
 */
class Application_Service_Navigation
{
    const USE_CACHE = true;
    const NO_CACHE  = false;

    /**
     * @var int
     */
    public static $userId = -1;

    public function getNavigationContainer ($useCaching = self::USE_CACHE)
    {
        if ($useCaching)
        {
            $cache     = $this->_getCacheObject('navigation_cache');
            $container = $cache->load('siteNavigationForUser_' . self::$userId);
            if (!$container)
            {
                $container = $this->_getNavigationContainer();
                $cache->save($container, 'siteNavigationForUser_' . self::$userId);
            }
        }
        else
        {
            $container = $this->_getNavigationContainer();
        }

        return $container;
    }


    /**
     *
     */
    protected function _getNavigationContainer ()
    {
        $container = new Zend_Navigation();
        $config    = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $container->addPages($config);

        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Zend_Acl)
        {
            $this->_trimPages($container, $container->getPages(), $acl);
        }

        return $container;
    }

    /**
     * @param Zend_Navigation        $container
     * @param Zend_Navigation_Page[] $pages
     * @param Zend_Acl               $acl
     */
    protected function _trimPages ($container, $pages, $acl)
    {
        foreach ($pages as $page)
        {
            $isAllowed = ($page->getResource() !== null) ? $acl->isAllowed(self::$userId, $page->getResource(), $page->getPrivilege()) : true;
            if ($isAllowed)
            {
                /**
                 * Recurse through children
                 */
                if ($page->hasPages())
                {
                    $this->_trimPages($page, $page->getPages(), $acl);
                }
            }
            else
            {
                $this->_removePage($container, $page);
            }
        }
    }

    /**
     * @param Zend_Navigation      $container
     * @param Zend_Navigation_Page $page
     */
    protected function _removePage ($container, $page)
    {

        if ($page->hasPages())
        {
            foreach ($page->getPages() as $childPage)
            {
                $this->_removePage($page, $childPage);
            }
        }

        $test = $container->removePage($page);
    }

    /**
     * Get a cache object from the cache manager
     *
     * @param string $cacheName
     *
     * @return Zend_Cache_Core
     */
    protected function _getCacheObject ($cacheName)
    {
        /* @var $bootstrap Bootstrap */
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');

        /* @var $cacheManager Zend_Cache_Manager */
        $cacheManager = $bootstrap->getResource('cachemanager');

        // get requested cache:
        $cache = $cacheManager->getCache($cacheName);

        return $cache;
    }
}