<?php

namespace MPSToolbox\Legacy\Services;

use Bootstrap;
use MPSToolbox\Legacy\Mappers\DealerFeatureMapper;
use Zend_Acl;
use Zend_Cache_Core;
use Zend_Cache_Manager;
use Zend_Config_Xml;
use Zend_Controller_Front;
use Zend_Navigation;
use Zend_Navigation_Page;
use Zend_Registry;

/**
 * Class NavigationService
 *
 * @package MPSToolbox\Legacy\Services
 */
class NavigationService
{
    const USE_CACHE = true;
    const NO_CACHE  = false;

    /**
     * @var int
     */
    public static $userId = 0;

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

        $list = DealerFeatureMapper::getInstance()->fetchFeatureListForDealer();
        foreach ($list as $feature) {
            if ($feature->featureId=='ecommerce') {
                $node = $container->findOneBy('id', 'ecommerce');
                $node->setVisible(true);
            }
        }

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