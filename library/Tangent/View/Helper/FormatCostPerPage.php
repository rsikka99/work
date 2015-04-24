<?php

namespace Tangent\View\Helper;

/**
 * Class FormatCostPerPage
 *
 * View helper to format cost per page
 *
 * @package Tangent\View\Helper
 */
class FormatCostPerPage extends \Zend_View_Helper_Abstract
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
        return $this->view->currency((float)$costPerPage, ['precision' => $precision]);
    }
}