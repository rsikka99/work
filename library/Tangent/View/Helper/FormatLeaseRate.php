<?php
namespace Tangent\View\Helper;

use NumberFormatter;
use Zend_View_Helper_Abstract;

/**
 * View Helper to format a lease rate.
 *
 * @package Tangent_View
 */
class FormatLeaseRate extends Zend_View_Helper_Abstract
{
    const MIN_FRACTION_DIGITS = 4;
    const MAX_FRACTION_DIGITS = 10;

    const MIN_SIGNIFICANT_DIGITS = 3;

    /**
     * @var NumberFormatter
     */
    protected $formatter;

    /**
     * Formats a lease rate
     *
     * @param float $leaseRate
     *
     * @return mixed
     */
    public function formatLeaseRate ($leaseRate)
    {
        return $this->getFormatter()->format($leaseRate);
    }

    /**
     * @return NumberFormatter
     */
    protected function getFormatter ()
    {
        if (!isset($this->formatter))
        {
            $this->formatter = NumberFormatter::create($this->view->App()->locale, NumberFormatter::DECIMAL);
            $this->formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, self::MIN_FRACTION_DIGITS);
            $this->formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, self::MAX_FRACTION_DIGITS);
            $this->formatter->setAttribute(NumberFormatter::MIN_SIGNIFICANT_DIGITS, self::MIN_SIGNIFICANT_DIGITS);
        }

        return $this->formatter;
    }
}