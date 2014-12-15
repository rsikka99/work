<?php

namespace Tangent;

use Tangent\View\Helper\FormatCostPerPage;
use Tangent\View\Helper\FormatPageVolume;

/**
 * Class Tangent_View
 *
 * Used to provide auto complete functionality in controllers
 */
class View extends \Zend_View
{

    /**
     * @see \My_View_Helper_IsAllowed
     *
     * @param $resource
     * @param $privilege
     *
     * @return bool
     */
    public function IsAllowed ($resource, $privilege)
    {
        /* @var $helper \My_View_Helper_IsAllowed */
        $helper = $this->getHelper('IsAllowed');

        return $helper->isAllowed($resource, $privilege);
    }

    /**
     * @see \Zend_View_Helper_Currency
     *
     * @param $value
     * @param $currency
     *
     * @return string
     */
    public function currency ($value = null, $currency = null)
    {
        /* @var $helper \Zend_View_Helper_Currency */
        $helper = $this->getHelper('currency');

        return $helper->currency($value, $currency);
    }

    /**
     * @see \Zend_View_Helper_HeadTitle
     *
     * @param null $title
     * @param null $setType
     *
     * @return string
     */
    public function headTitle ($title = null, $setType = null)
    {
        /* @var $helper \Zend_View_Helper_HeadTitle */
        $helper = $this->getHelper('headTitle');

        return $helper->headTitle($title, $setType);
    }

    /**
     * @see \Zend_View_Helper_Placeholder
     *
     * @param  string $name
     *
     * @return \Zend_View_Helper_Placeholder_Container_Abstract
     */
    public function placeholder ($name)
    {
        $helper = $this->getHelper('placeholder');

        return $helper->placeholder($name);
    }

    /**
     * @see \Zend_View_Helper_Layout
     *
     * Usage: $this->layout()->setLayout('alternate');
     *
     * @return \Zend_Layout
     */
    public function layout ()
    {
        $helper = $this->getHelper('layout');

        return $helper->layout();
    }

    /**
     * @param int $pageVolume
     * @param int $precision
     *
     * @return string
     */
    public function formatPageVolume ($pageVolume, $precision = 0)
    {
        $helper = new FormatPageVolume();

        return $helper->formatPageVolume($pageVolume, $precision);
    }

    /**
     * @param int $costPerPage
     * @param int $precision
     *
     * @return string
     */
    public function formatCostPerPage ($costPerPage, $precision = 4)
    {
        $helper = new FormatCostPerPage();

        return $helper->formatCostPerPage($costPerPage, $precision);
    }
}