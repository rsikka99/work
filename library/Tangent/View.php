<?php

/**
 * Class Tangent_View
 *
 * Used to provide auto complete functionality in controllers
 */
class Tangent_View extends Zend_View
{

    /**
     * @see My_View_Helper_IsAllowed
     *
     * @param $resource
     * @param $privilege
     *
     * @return bool
     */
    public function IsAllowed ($resource, $privilege)
    {
        /* @var $helper My_View_Helper_IsAllowed */
        $helper = $this->getHelper('IsAllowed');

        return $helper->isAllowed($resource, $privilege);
    }

    /**
     * @see Zend_View_Helper_Currency
     *
     * @param $value
     * @param $currency
     *
     * @return string
     */
    public function currency ($value = null, $currency = null)
    {
        /* @var $helper Zend_View_Helper_Currency */
        $helper = $this->getHelper('currency');

        return $helper->currency($value, $currency);
    }
}