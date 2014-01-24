<?php

/**
 * Application_View_Helper_MyNavigation
 *
 * @author Lee Robert
 *
 */
class Application_View_Helper_MyNavigation extends Zend_View_Helper_Navigation
{
    /**
     * Helper entry point
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               operate on
     *
     * @return Zend_View_Helper_Navigation           fluent interface, returns
     *                                               self
     */
    public function MyNavigation (Zend_Navigation_Container $container = null)
    {
        return parent::navigation($container);
    }

    /**
     * Returns the navigation container helper operates on by default
     *
     * Implements {@link Zend_View_Helper_Navigation_Interface::getContainer()}.
     *
     * If a helper is not explicitly set in this helper instance by calling
     * {@link setContainer()} or by passing it through the helper entry point,
     * this method will look in {@link Zend_Registry} for a container by using
     * the key 'Zend_Navigation'.
     *
     * If no container is set, and nothing is found in Zend_Registry, a new
     * container will be instantiated and stored in the helper.
     *
     * @return Zend_Navigation_Container  navigation container
     */
    public function getContainer ()
    {
        if (null === $this->_container)
        {
            /**
             * Fetch from the service
             */
            $navigationService = new Application_Service_Navigation();
            $nav               = $navigationService->getNavigationContainer(Application_Service_Navigation::USE_CACHE);
            if ($nav instanceof Zend_Navigation_Container)
            {
                return $this->_container = $nav;
            }

            // If we haven't found it, fall back to the parent option
            return parent::getContainer();
        }

        return $this->_container;
    }
}
