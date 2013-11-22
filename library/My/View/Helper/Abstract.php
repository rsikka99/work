<?php

abstract class My_View_Helper_Abstract extends Zend_View_Helper_Abstract
{
    /**
     *
     * @var Zend_Controller_Front
     */
    private $_frontController;

    /**
     * Convience function for getting a request parameter from the request
     * object in a view helper
     *
     * @param string $name
     *            The name of the request parameter
     * @param mixed  $default
     *            The value to return if $name is not defined in the
     *            request
     *
     * @return mixed The value of parameter $name in the request object,
     *         or $default if $name is not defined in the request
     */
    public function getRequestVariable ($name, $default = null)
    {
        return $this->getRequest()->getParam($name, $default);
    }

    /**
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest ()
    {
        return $this->getFrontController()->getRequest();
    }

    /**
     *
     * @return Zend_Controller_Front
     */
    private function getFrontController ()
    {
        if (empty($this->_frontController))
        {
            $this->_frontController = Zend_Controller_Front::getInstance();
        }

        return $this->_frontController;
    }
}