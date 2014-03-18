<?php

/**
 * View Helper to Format Cost Per Page.
 *
 * @package Tangent_View
 */
class Tangent_View_Helper_FormatCostPerPage extends Zend_View_Helper_Abstract
{

    /**
     * Formats a Cost Per Page
     *
     * @param     $costPerPage
     * @param int $precision
     *
     * @return mixed
     */
    public function formatCostPerPage ($costPerPage, $precision = 4)
    {
        return $this->view->currency((float)$costPerPage, array('precision' => $precision));
    }
}