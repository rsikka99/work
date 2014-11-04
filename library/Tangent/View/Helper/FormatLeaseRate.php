<?php

/**
 * View Helper to format a lease rate.
 *
 * @package Tangent_View
 */
class Tangent_View_Helper_FormatLeaseRate extends Zend_View_Helper_Abstract
{

    /**
     * Formats a lease rate
     *
     * @param float $leaseRate
     *
     * @return mixed
     */
    public function formatLeaseRate ($leaseRate)
    {
        return number_format($leaseRate, 6);
    }
}