<?php

/**
 * View Helper to Format Numbers.
 *
 * @package Tangent_View
 */
class Tangent_View_Helper_NumberFormat extends Zend_View_Helper_Abstract
{

    private $_numberFormat = null;

    /**
     * Custom Format for Decimal Numbers.
     *
     * Number will be formatted as follows:
     *
     * All right trailing zeros will be trimmed except for the two directly to the
     * right of the decimal.
     * Ie. 64.00000 -> 64.00
     * 72.01000 -> 72.01
     * 6 - > 6.00
     * 55.00001 -> 55.00001
     * 23.10000000 -> 23.10
     *
     * This function is to be used when we what to show all available precision with no rounding,
     * with a minimum of two decimal places.
     *
     * IMPORTANT:  Setting the max precision too small will cause the number to be rounded off.
     *
     *
     * @param  double $number       The decimal number to be formatted
     * @param  int    $maxPrecision The maximum number of precision/digits to be displayed to the right of the decimal point.
     *
     * @return string Formatted number converted to a string for display
     */
    public function numberFormat ($number, $maxPrecision = 5)
    {

        $RemainingPrecision = 0;
        //echo $number;
        //echo '<br/>';
        $trimmedNumber = (string)number_format($number, $maxPrecision);
        //echo $trimmedNumber;
        //echo '<br/>';
        $trimmedNumber = rtrim($trimmedNumber, '0');
        //echo $trimmedNumber;
        //echo '<br/>';
        $RemainingPrecision = strlen($trimmedNumber) - 1 - strrpos($trimmedNumber, '.');
        //echo $RemainingPrecision;
        //echo '<br/>';

        //Add zeros to make sure we are displaying at least two decimal places.
        for ($i = $RemainingPrecision; $i < 2; $i++)
        {
            $trimmedNumber .= '0';
        }

        return $trimmedNumber;
    }

    /**
     * Lazily fetches FlashMessenger Instance.
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function _getNumberFormat ()
    {
        if (null === $this->_numberFormat)
        {
            $this->_numberFormat =
                Zend_Controller_Action_HelperBroker::getStaticHelper(
                                                   'NumberFormat');
        }

        return $this->_numberFormat;
    }
}